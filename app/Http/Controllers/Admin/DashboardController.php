<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpensePeriod;
use App\Models\MaintenancePayment;
use App\Models\MaintenancePeriod;
use App\Models\SocietyFlat;
use App\Models\SocietyWing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $userId = (int) $user->id;
        $societyWings = collect();
        $societyFlatsList = collect();
        $societyFlatsByWing = collect();
        $dashboardStats = [
            'wings' => 0,
            'flats' => 0,
            'occupied' => 0,
            'vacant' => 0,
            'vehicles' => 0,
            'occupancy_rate' => 0,
        ];
        $latestMaintenance = null;
        $latestExpense = null;

        if ($user->can('society-setup-view')) {
            $societyWings = SocietyWing::query()
                ->where('user_id', $userId)
                ->orderBy('sort_order')
                ->get();

            $societyFlatsList = SocietyFlat::query()
                ->where('society_flats.user_id', $userId)
                ->join('society_wings', 'society_wings.id', '=', 'society_flats.society_wing_id')
                ->orderBy('society_wings.sort_order')
                ->orderBy('society_flats.floor_number')
                ->orderBy('society_flats.flat_index')
                ->select('society_flats.*')
                ->with('wing:id,code,label')
                ->get();

            $societyFlatsByWing = $societyFlatsList->groupBy('society_wing_id');

            $occupied = $societyFlatsList->filter(fn ($flat) => trim((string) $flat->owner_name) !== '')->count();
            $dashboardStats = [
                'wings' => $societyWings->count(),
                'flats' => $societyFlatsList->count(),
                'occupied' => $occupied,
                'vacant' => $societyFlatsList->count() - $occupied,
                'vehicles' => (int) $societyFlatsList->sum('vehicles_count'),
                'occupancy_rate' => $societyFlatsList->count() > 0 ? round(($occupied / $societyFlatsList->count()) * 100) : 0,
            ];
        }

        if ($user->can('maintenance-view')) {
            $latestMaintenance = MaintenancePeriod::query()
                ->where('user_id', $userId)
                ->withCount([
                    'items as total_flats',
                    'items as paid_flats' => fn ($query) => $query->where('status', MaintenancePayment::STATUS_PAID),
                    'items as unpaid_flats' => fn ($query) => $query->where('status', MaintenancePayment::STATUS_UNPAID),
                ])
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->first();
        }

        if ($user->can('expense-view')) {
            $latestExpense = ExpensePeriod::query()
                ->where('user_id', $userId)
                ->withCount('items')
                ->withSum('items as total_amount', 'amount')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->first();
        }

        return view('Admin.Dashboard.index', compact(
            'societyWings',
            'societyFlatsList',
            'societyFlatsByWing',
            'dashboardStats',
            'latestMaintenance',
            'latestExpense',
        ));
    }

    public function flatProfile(Request $request, SocietyFlat $flat): View
    {
        abort_unless((int) $flat->user_id === (int) $request->user()->id, 404);

        $flat->load([
            'wing:id,code,label',
            'documents',
            'maintenancePayments.period',
        ]);

        $maintenanceHistory = $flat->maintenancePayments
            ->sortByDesc(fn ($payment) => optional($payment->period)->year * 100 + optional($payment->period)->month)
            ->values();

        $lastPaid = $maintenanceHistory
            ->first(fn ($payment) => $payment->status === MaintenancePayment::STATUS_PAID);

        $maintenanceStats = [
            'total' => $maintenanceHistory->count(),
            'paid' => $maintenanceHistory->where('status', MaintenancePayment::STATUS_PAID)->count(),
            'unpaid' => $maintenanceHistory->where('status', MaintenancePayment::STATUS_UNPAID)->count(),
        ];

        return view('Admin.Flats.flat-detail', [
            'flat' => $flat,
            'unit_code' => $flat->unit_code,
            'owner_name' => $flat->owner_name,
            'owner_mobile' => $flat->owner_mobile,
            'owner_email' => $flat->owner_email,
            'vehicle_count' => (int) $flat->vehicles_count,
            'last_maintenance_paid' => $lastPaid && $lastPaid->period
                ? $lastPaid->period->label
                : 'No paid maintenance yet',
            'last_updated' => optional($flat->updated_at)->format('d M Y, h:i A'),
            'back_url' => route('admin.dashboard.index'),
            'form_action' => route('admin.society-setup.flat-unit.update', $flat),
            'maintenance_url' => route('admin.maintenance.index'),
            'documents' => $flat->documents,
            'maintenanceHistory' => $maintenanceHistory,
            'maintenanceStats' => $maintenanceStats,
        ]);
    }

}
