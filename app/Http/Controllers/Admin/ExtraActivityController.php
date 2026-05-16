<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtraActivity;
use App\Models\ExtraActivityPayment;
use App\Models\SocietyFlat;
use App\Models\SocietyWing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExtraActivityController extends Controller
{
    public function index(Request $request): View
    {
        $userId = (int) $request->user()->id;
        $activityQuery = ExtraActivity::query()
            ->where('user_id', $userId)
            ->withCount([
                'payments as total_flats',
                'payments as paid_flats' => fn ($query) => $query->where('status', ExtraActivityPayment::STATUS_PAID),
                'payments as unpaid_flats' => fn ($query) => $query->where('status', ExtraActivityPayment::STATUS_UNPAID),
            ]);

        $activities = (clone $activityQuery)
            ->latest()
            ->paginate(10);

        $allActivities = (clone $activityQuery)->get();
        $summary = [
            'activities' => $allActivities->count(),
            'total_flats' => (int) $allActivities->sum('total_flats'),
            'paid' => (int) $allActivities->sum('paid_flats'),
            'unpaid' => (int) $allActivities->sum('unpaid_flats'),
            'target' => (float) $allActivities->sum('target_amount'),
        ];
        $summary['collection_rate'] = $summary['total_flats'] > 0 ? round(($summary['paid'] / $summary['total_flats']) * 100) : 0;

        $latestActivity = (clone $activityQuery)->latest()->first();
        $types = ExtraActivity::types();

        return view('Admin.ExtraActivities.index', compact('activities', 'summary', 'latestActivity', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = (int) $request->user()->id;
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'activity_type' => ['required', Rule::in(array_keys(ExtraActivity::types()))],
            'amount_per_flat' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'target_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $flatIds = SocietyFlat::query()
            ->where('user_id', $userId)
            ->pluck('id');

        if ($flatIds->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['title' => 'Please generate flats first from Society Setup before creating an extra activity.']);
        }

        $activity = DB::transaction(function () use ($data, $flatIds, $userId) {
            $activity = ExtraActivity::create([
                'user_id' => $userId,
                'title' => trim($data['title']),
                'activity_type' => $data['activity_type'],
                'amount_per_flat' => $data['amount_per_flat'],
                'target_amount' => $data['target_amount'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $now = now();
            ExtraActivityPayment::insert($flatIds->map(fn ($flatId) => [
                'extra_activity_id' => $activity->id,
                'society_flat_id' => $flatId,
                'status' => ExtraActivityPayment::STATUS_UNPAID,
                'paid_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all());

            return $activity;
        });

        return redirect()
            ->route('admin.extra-activities.show', $activity)
            ->with('success', 'Extra activity created.');
    }

    public function show(Request $request, ExtraActivity $extraActivity): View
    {
        $this->authorizeActivity($request, $extraActivity);

        $status = $request->query('status', 'all');
        if (! in_array($status, ['all', ExtraActivityPayment::STATUS_PAID, ExtraActivityPayment::STATUS_UNPAID], true)) {
            $status = 'all';
        }

        $this->syncMissingFlatRows($extraActivity);

        $query = ExtraActivityPayment::query()
            ->where('extra_activity_id', $extraActivity->id)
            ->join('society_flats', 'society_flats.id', '=', 'extra_activity_payments.society_flat_id')
            ->join('society_wings', 'society_wings.id', '=', 'society_flats.society_wing_id')
            ->with('flat.wing:id,code,label')
            ->orderBy('society_wings.sort_order')
            ->orderBy('society_flats.floor_number')
            ->orderBy('society_flats.flat_index')
            ->select('extra_activity_payments.*');

        if ($status !== 'all') {
            $query->where('extra_activity_payments.status', $status);
        }

        $payments = $query->get();
        $stats = $this->activityStats($extraActivity);
        $wings = SocietyWing::query()
            ->where('user_id', $extraActivity->user_id)
            ->orderBy('sort_order')
            ->get();

        $paymentsByWingFloor = $payments
            ->groupBy(fn ($payment) => optional($payment->flat)->society_wing_id)
            ->map(fn ($wingPayments) => $wingPayments->groupBy(fn ($payment) => (int) optional($payment->flat)->floor_number)->sortKeysDesc());

        return view('Admin.ExtraActivities.show', compact('extraActivity', 'payments', 'paymentsByWingFloor', 'stats', 'status', 'wings'));
    }

    public function updateStatus(Request $request, ExtraActivity $extraActivity, ExtraActivityPayment $payment): RedirectResponse
    {
        $this->authorizeActivity($request, $extraActivity);
        abort_unless((int) $payment->extra_activity_id === (int) $extraActivity->id, 404);

        $data = $request->validate([
            'status' => ['required', Rule::in([ExtraActivityPayment::STATUS_PAID, ExtraActivityPayment::STATUS_UNPAID])],
            'transaction_id' => ['nullable', 'string', 'max:120'],
            'payment_note' => ['nullable', 'string', 'max:1000'],
            'receipt_file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ]);

        if ($data['status'] === ExtraActivityPayment::STATUS_UNPAID) {
            $this->deleteReceiptFile($payment);
            $payment->update([
                'status' => ExtraActivityPayment::STATUS_UNPAID,
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
            'status' => ExtraActivityPayment::STATUS_PAID,
            'paid_at' => $payment->paid_at ?: now(),
            'transaction_id' => $this->nullableTrim($data['transaction_id'] ?? null),
            'payment_note' => $this->nullableTrim($data['payment_note'] ?? null),
        ];

        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');
            $this->deleteReceiptFile($payment);
            $path = $file->store('extra-activity-receipts/'.$extraActivity->user_id.'/'.$extraActivity->id, 'public');
            $updates['receipt_path'] = $path;
            $updates['receipt_original_name'] = $file->getClientOriginalName();
            $updates['receipt_mime_type'] = $file->getMimeType();
            $updates['receipt_size'] = (int) $file->getSize();
        }

        $payment->update($updates);
        return back()->with('success', 'Flat '.$payment->flat?->unit_code.' marked as paid.');
    }

    public function destroy(Request $request, ExtraActivity $extraActivity): RedirectResponse
    {
        $this->authorizeActivity($request, $extraActivity);
        $title = $extraActivity->title;

        DB::transaction(function () use ($extraActivity) {
            $payments = ExtraActivityPayment::query()
                ->where('extra_activity_id', $extraActivity->id)
                ->get();
            foreach ($payments as $payment) {
                $this->deleteReceiptFile($payment);
            }
            $extraActivity->delete();
        });

        return redirect()->route('admin.extra-activities.index')->with('success', 'Extra activity '.$title.' deleted.');
    }

    private function syncMissingFlatRows(ExtraActivity $activity): void
    {
        $existingFlatIds = ExtraActivityPayment::query()
            ->where('extra_activity_id', $activity->id)
            ->pluck('society_flat_id')
            ->all();

        $missingFlatIds = SocietyFlat::query()
            ->where('user_id', $activity->user_id)
            ->whereNotIn('id', $existingFlatIds)
            ->pluck('id');

        if ($missingFlatIds->isEmpty()) {
            return;
        }

        $now = now();
        ExtraActivityPayment::insert($missingFlatIds->map(fn ($flatId) => [
            'extra_activity_id' => $activity->id,
            'society_flat_id' => $flatId,
            'status' => ExtraActivityPayment::STATUS_UNPAID,
            'paid_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());
    }

    private function activityStats(ExtraActivity $activity): array
    {
        $base = ExtraActivityPayment::query()->where('extra_activity_id', $activity->id);
        $total = (clone $base)->count();
        $paid = (clone $base)->where('status', ExtraActivityPayment::STATUS_PAID)->count();
        $unpaid = (clone $base)->where('status', ExtraActivityPayment::STATUS_UNPAID)->count();

        return [
            'total' => $total,
            'paid' => $paid,
            'unpaid' => $unpaid,
            'expected' => $total * (float) $activity->amount_per_flat,
            'collected' => $paid * (float) $activity->amount_per_flat,
        ];
    }

    private function authorizeActivity(Request $request, ExtraActivity $activity): void
    {
        abort_unless((int) $activity->user_id === (int) $request->user()->id, 404);
    }

    private function deleteReceiptFile(ExtraActivityPayment $payment): void
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
}
