<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenancePayment;
use App\Models\MaintenancePeriod;
use App\Models\SocietyFlat;
use App\Models\SocietyWing;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $userId = (int) $request->user()->id;
        $periodQuery = MaintenancePeriod::query()
            ->where('user_id', $userId)
            ->withCount([
                'items as total_flats',
                'items as paid_flats' => fn ($query) => $query->where('status', MaintenancePayment::STATUS_PAID),
                'items as unpaid_flats' => fn ($query) => $query->where('status', MaintenancePayment::STATUS_UNPAID),
            ]);

        $periods = (clone $periodQuery)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(10);

        $allPeriods = (clone $periodQuery)->get();
        $summary = [
            'cycles' => $allPeriods->count(),
            'total_flats' => (int) $allPeriods->sum('total_flats'),
            'paid' => (int) $allPeriods->sum('paid_flats'),
            'unpaid' => (int) $allPeriods->sum('unpaid_flats'),
        ];
        $summary['collection_rate'] = $summary['total_flats'] > 0
            ? round(($summary['paid'] / $summary['total_flats']) * 100)
            : 0;

        $latestPeriod = (clone $periodQuery)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        $months = $this->months();
        $years = $this->years();

        return view('Admin.Maintenance.index', compact('periods', 'months', 'years', 'summary', 'latestPeriod'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = (int) $request->user()->id;
        $data = $request->validate([
            'month' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('maintenance_periods')->where(fn ($query) => $query
                    ->where('user_id', $userId)
                    ->where('year', (int) $request->input('year'))),
            ],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'month.unique' => 'Maintenance for this month and year already exists.',
        ]);

        $flatIds = SocietyFlat::query()
            ->where('user_id', $userId)
            ->pluck('id');

        if ($flatIds->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['month' => 'Please generate flats first from Society Setup before creating maintenance.']);
        }

        $period = DB::transaction(function () use ($data, $flatIds, $userId) {
            $period = MaintenancePeriod::create([
                'user_id' => $userId,
                'month' => (int) $data['month'],
                'year' => (int) $data['year'],
                'amount' => $data['amount'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $now = now();
            $rows = $flatIds->map(fn ($flatId) => [
                'maintenance_period_id' => $period->id,
                'society_flat_id' => $flatId,
                'status' => MaintenancePayment::STATUS_UNPAID,
                'paid_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            MaintenancePayment::insert($rows);

            return $period;
        });

        return redirect()
            ->route('admin.maintenance.show', $period)
            ->with('success', 'Maintenance created for '.$period->label.'.');
    }

    public function show(Request $request, MaintenancePeriod $maintenance): View
    {
        $this->authorizePeriod($request, $maintenance);

        $status = $request->query('status', 'all');
        if (! in_array($status, ['all', MaintenancePayment::STATUS_PAID, MaintenancePayment::STATUS_UNPAID], true)) {
            $status = 'all';
        }

        $payments = $this->maintenancePaymentsForPeriod($maintenance, $status)->get();
        $stats = $this->periodStats($maintenance);
        $wings = SocietyWing::query()
            ->where('user_id', $maintenance->user_id)
            ->orderBy('sort_order')
            ->get();

        $paymentsByWingFloor = $payments
            ->groupBy(fn ($payment) => optional($payment->flat)->society_wing_id)
            ->map(fn ($wingPayments) => $wingPayments->groupBy(fn ($payment) => (int) optional($payment->flat)->floor_number)->sortKeysDesc());

        return view('Admin.Maintenance.show', compact(
            'maintenance',
            'payments',
            'paymentsByWingFloor',
            'stats',
            'status',
            'wings'
        ));
    }

    public function downloadListPdf(Request $request, MaintenancePeriod $maintenance, string $list): Response
    {
        $this->authorizePeriod($request, $maintenance);
        if (! in_array($list, [MaintenancePayment::STATUS_PAID, MaintenancePayment::STATUS_UNPAID], true)) {
            abort(404);
        }

        $payments = $this->maintenancePaymentsForPeriod($maintenance, $list)->get();
        $listLabel = $list === MaintenancePayment::STATUS_PAID ? 'Paid' : 'Unpaid';

        $maintenance->loadMissing('user');
        $societyName = trim((string) ($maintenance->user?->society_name ?? $request->user()->society_name));

        $html = view('Admin.Maintenance.pdf-list', [
            'maintenance' => $maintenance,
            'payments' => $payments,
            'listLabel' => $listLabel,
            'societyName' => $societyName,
            'brandName' => 'Society Management System',
        ])->render();

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = sprintf('maintenance-%d-%02d-%s.pdf', $maintenance->year, $maintenance->month, $list);

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function updateStatus(Request $request, MaintenancePeriod $maintenance, MaintenancePayment $payment): RedirectResponse
    {
        $this->authorizePeriod($request, $maintenance);

        if ((int) $payment->maintenance_period_id !== (int) $maintenance->id) {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['required', Rule::in([MaintenancePayment::STATUS_PAID, MaintenancePayment::STATUS_UNPAID])],
            'transaction_id' => ['nullable', 'string', 'max:120'],
            'payment_note' => ['nullable', 'string', 'max:1000'],
            'receipt_file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ]);

        if ($data['status'] === MaintenancePayment::STATUS_UNPAID) {
            $this->deleteReceiptFile($payment);
            $payment->update([
                'status' => MaintenancePayment::STATUS_UNPAID,
                'paid_at' => null,
                'transaction_id' => null,
                'payment_note' => null,
                'receipt_path' => null,
                'receipt_original_name' => null,
                'receipt_mime_type' => null,
                'receipt_size' => null,
            ]);

            return back()->with('success', 'Flat '.$payment->flat?->unit_code.' marked as unpaid.');
        }

        $updates = [
            'status' => MaintenancePayment::STATUS_PAID,
            'paid_at' => $payment->paid_at ?: now(),
            'transaction_id' => $this->nullableTrim($data['transaction_id'] ?? null),
            'payment_note' => $this->nullableTrim($data['payment_note'] ?? null),
        ];

        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');
            $this->deleteReceiptFile($payment);
            $path = $file->store('maintenance-receipts/'.$maintenance->user_id.'/'.$maintenance->id, 'public');
            $updates['receipt_path'] = $path;
            $updates['receipt_original_name'] = $file->getClientOriginalName();
            $updates['receipt_mime_type'] = $file->getMimeType();
            $updates['receipt_size'] = (int) $file->getSize();
        }

        $payment->update($updates);

        return back()->with('success', 'Flat '.$payment->flat?->unit_code.' marked as paid.');
    }

    public function destroy(Request $request, MaintenancePeriod $maintenance): RedirectResponse
    {
        $this->authorizePeriod($request, $maintenance);
        $label = $maintenance->label;

        DB::transaction(function () use ($maintenance) {
            $payments = MaintenancePayment::query()
                ->where('maintenance_period_id', $maintenance->id)
                ->get();

            foreach ($payments as $payment) {
                $this->deleteReceiptFile($payment);
            }

            $maintenance->delete();
        });

        return redirect()
            ->route('admin.maintenance.index')
            ->with('success', 'Maintenance data for '.$label.' deleted.');
    }

    private function maintenancePaymentsForPeriod(MaintenancePeriod $maintenance, string $status): Builder
    {
        $this->syncMissingFlatRows($maintenance);

        $query = MaintenancePayment::query()
            ->where('maintenance_period_id', $maintenance->id)
            ->join('society_flats', 'society_flats.id', '=', 'maintenance_payments.society_flat_id')
            ->join('society_wings', 'society_wings.id', '=', 'society_flats.society_wing_id')
            ->with('flat.wing:id,code,label')
            ->orderBy('society_wings.sort_order')
            ->orderBy('society_flats.floor_number')
            ->orderBy('society_flats.flat_index')
            ->select('maintenance_payments.*');

        if ($status !== 'all') {
            $query->where('maintenance_payments.status', $status);
        }

        return $query;
    }

    private function syncMissingFlatRows(MaintenancePeriod $period): void
    {
        $existingFlatIds = MaintenancePayment::query()
            ->where('maintenance_period_id', $period->id)
            ->pluck('society_flat_id')
            ->all();

        $missingFlatIds = SocietyFlat::query()
            ->where('user_id', $period->user_id)
            ->whereNotIn('id', $existingFlatIds)
            ->pluck('id');

        if ($missingFlatIds->isEmpty()) {
            return;
        }

        $now = now();
        MaintenancePayment::insert($missingFlatIds->map(fn ($flatId) => [
            'maintenance_period_id' => $period->id,
            'society_flat_id' => $flatId,
            'status' => MaintenancePayment::STATUS_UNPAID,
            'paid_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());
    }

    private function periodStats(MaintenancePeriod $period): array
    {
        $base = MaintenancePayment::query()->where('maintenance_period_id', $period->id);

        return [
            'total' => (clone $base)->count(),
            'paid' => (clone $base)->where('status', MaintenancePayment::STATUS_PAID)->count(),
            'unpaid' => (clone $base)->where('status', MaintenancePayment::STATUS_UNPAID)->count(),
        ];
    }

    private function authorizePeriod(Request $request, MaintenancePeriod $period): void
    {
        abort_unless((int) $period->user_id === (int) $request->user()->id, 404);
    }

    private function deleteReceiptFile(MaintenancePayment $payment): void
    {
        if ($payment->receipt_path) {
            Storage::disk('public')->delete($payment->receipt_path);
        }
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value !== '' ? $value : null;
    }

    private function months(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn ($month) => [$month => date('F', mktime(0, 0, 0, $month, 1))])
            ->all();
    }

    private function years(): array
    {
        $current = (int) date('Y');
        return range($current - 1, $current + 5);
    }
}
