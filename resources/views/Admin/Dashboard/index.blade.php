@extends('layouts.admin')
@section('title') Dashboard @endsection
@php
    $showSociety = auth()->user()->can('society-setup-view');
    $canMaintenance = auth()->user()->can('maintenance-view');
    $canExpenses = auth()->user()->can('expense-view');

    $flatsByWingFloor = [];
    $wingStats = [];
    if ($showSociety) {
        foreach ($societyWings as $w) {
            $wingFlats = $societyFlatsByWing->get($w->id, collect());
            $flatsByWingFloor[$w->id] = $wingFlats->groupBy(function ($f) {
                return (int) $f->floor_number;
            })->sortKeysDesc();
            $occ = $wingFlats->filter(fn ($f) => trim((string) $f->owner_name) !== '')->count();
            $wingStats[$w->id] = [
                'total' => $wingFlats->count(),
                'occupied' => $occ,
                'vacant' => $wingFlats->count() - $occ,
                'vehicles' => (int) $wingFlats->sum('vehicles_count'),
            ];
        }
    }

    $maintenanceRate = $latestMaintenance && (int) $latestMaintenance->total_flats > 0
        ? round(((int) $latestMaintenance->paid_flats / (int) $latestMaintenance->total_flats) * 100)
        : 0;
@endphp
@section('content')
<style>
    .dash-wrap { --dash-primary:#0f6bff; --dash-dark:#0b4bb3; --dash-ink:#0f172a; --dash-muted:#64748b; --dash-border:#e2e8f0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
    .dash-hero { position:relative; overflow:hidden; border-radius:1rem; padding:1.25rem; color:#fff; background:linear-gradient(135deg,#0f6bff,#0b4bb3); box-shadow:0 18px 42px rgba(15,107,255,.22); }
    .dash-hero::after { content:""; position:absolute; right:-4rem; bottom:-5rem; width:16rem; height:16rem; border-radius:50%; background:rgba(255,255,255,.1); }
    .dash-hero-content { position:relative; z-index:1; }
    .dash-eyebrow { color:rgba(255,255,255,.68) !important; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.25rem; }
    .dash-title { color:#fff !important; font-size:clamp(1.45rem,2.5vw,2.25rem); font-weight:900 !important; letter-spacing:0; margin:0; }
    .dash-sub { color:rgba(255,255,255,.78) !important; max-width:44rem; margin:.45rem 0 0; }
    .dash-actions { display:flex; flex-wrap:wrap; gap:.5rem; }
    .dash-kpis { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.85rem; margin-top:1rem; }
    .dash-kpi { background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.18); border-radius:.85rem; padding:.85rem; }
    .dash-kpi span { display:block; color:rgba(255,255,255,.68); font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.04em; }
    .dash-kpi strong { display:block; color:#fff; font-size:1.55rem; line-height:1; margin-top:.25rem; }
    .dash-card { border:0; border-radius:1rem; box-shadow:0 12px 30px rgba(15,23,42,.08); overflow:hidden; background:#fff; }
    .dash-card-header { padding:1rem 1.1rem; border-bottom:1px solid var(--dash-border); background:linear-gradient(180deg,#fbfdff,#fff); display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem; }
    .dash-card-title { margin:0; font-size:1rem; font-weight:900; color:var(--dash-ink); display:flex; align-items:center; gap:.45rem; }
    .overview-card { height:100%; padding:1rem; border:1px solid #e8eef7; border-radius:1rem; background:linear-gradient(180deg,#fff,#fbfdff); }
    .overview-icon { width:2.65rem; height:2.65rem; display:flex; align-items:center; justify-content:center; border-radius:.8rem; font-size:1.35rem; margin-bottom:.85rem; }
    .overview-icon.green { background:#dcfce7; color:#047857; }
    .overview-icon.purple { background:#f3e8ff; color:#7c3aed; }
    .overview-icon.blue { background:#dbeafe; color:#1d4ed8; }
    .overview-label { color:#64748b; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.04em; }
    .overview-value { color:#0f172a; font-weight:950; font-size:1.35rem; line-height:1.1; margin-top:.2rem; }
    .overview-copy { color:#64748b; font-size:.78rem; line-height:1.45; margin:.55rem 0 0; }
    .progress-soft { height:.55rem; background:#e2e8f0; border-radius:999px; overflow:hidden; margin-top:.75rem; }
    .progress-soft span { display:block; height:100%; border-radius:inherit; background:linear-gradient(90deg,#16a34a,#22c55e); }
    .quick-actions { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.75rem; }
    .quick-action { display:flex; align-items:center; gap:.75rem; padding:.9rem; border:1px solid #e8eef7; border-radius:.85rem; color:#0f172a; text-decoration:none; background:#fff; transition:transform .12s ease, border-color .12s ease, box-shadow .12s ease; }
    .quick-action:hover { transform:translateY(-1px); border-color:#bfdbfe; box-shadow:0 10px 24px rgba(37,99,235,.1); color:#0f172a; }
    .quick-action i { width:2.35rem; height:2.35rem; display:flex; align-items:center; justify-content:center; border-radius:.72rem; background:#eff6ff; color:#1d4ed8; font-size:1.2rem; }
    .quick-action strong { display:block; font-size:.86rem; }
    .quick-action span { display:block; color:#64748b; font-size:.72rem; margin-top:.1rem; }
    .dash-legend { display:flex; flex-wrap:wrap; align-items:center; gap:.85rem; font-size:.74rem; color:#64748b; margin:.25rem 0 .85rem; }
    .dash-legend-item { display:inline-flex; align-items:center; gap:.35rem; }
    .dash-legend-swatch { width:.9rem; height:.9rem; border-radius:3px; border:1px solid #cbd5e1; display:inline-block; }
    .dash-legend-swatch--occupied { background:linear-gradient(180deg,#dcfce7,#f0fdf4); border-color:#86efac; }
    .dash-legend-swatch--vacant { background:#f8fafc; }
    .dash-wing-section + .dash-wing-section { margin-top:1.25rem; }
    .dash-wing-head { display:flex; flex-wrap:wrap; align-items:center; gap:.75rem; padding-bottom:.5rem; margin-bottom:.65rem; border-bottom:1px solid #e2e8f0; }
    .dash-wing-title { margin:0; font-size:.95rem; font-weight:800; color:#0f172a; display:inline-flex; align-items:center; gap:.4rem; }
    .dash-wing-letter { width:1.75rem; height:1.75rem; border-radius:.4rem; display:inline-flex; align-items:center; justify-content:center; background:#e0f2fe; color:#0369a1; font-weight:800; font-size:.85rem; }
    .dash-wing-stats { display:inline-flex; flex-wrap:wrap; gap:.4rem; margin-left:auto; }
    .dash-wing-stat { display:inline-flex; align-items:center; gap:.25rem; padding:.18rem .5rem; border-radius:999px; background:#f1f5f9; color:#334155; font-size:.7rem; font-weight:700; }
    .dash-wing-stat--occ { background:#ecfdf5; color:#047857; }
    .dash-wing-stat--vac { background:#fef3c7; color:#92400e; }
    .dash-wing-stat--veh { background:#eff6ff; color:#1d4ed8; }
    .dash-building { display:flex; flex-direction:column; gap:.45rem; }
    .dash-floor-row { display:flex; align-items:stretch; gap:.5rem; }
    .dash-floor-label { flex:0 0 5.25rem; display:flex; align-items:center; justify-content:flex-end; padding:.5rem .7rem; background:#f1f5f9; border-radius:.4rem; font-weight:800; font-size:.72rem; color:#475569; text-transform:uppercase; letter-spacing:.04em; }
    .dash-floor-label--ground { background:#e0f2fe; color:#0369a1; }
    .dash-floor-flats { flex:1 1 auto; min-width:0; display:flex; flex-wrap:wrap; gap:.4rem; padding:.4rem; border:1px dashed #e2e8f0; border-radius:.4rem; background:#fbfcfe; }
    .dash-floor-empty { flex:1; align-self:center; padding:.25rem .5rem; color:#94a3b8; font-size:.75rem; font-style:italic; }
    .dash-flat { flex:0 0 auto; min-width:7.5rem; max-width:11rem; display:flex; flex-direction:column; gap:.1rem; padding:.5rem .6rem; border:1px solid #cbd5e1; border-radius:.45rem; background:#fff; position:relative; transition:transform .12s ease,border-color .12s ease,box-shadow .12s ease; text-decoration:none; color:inherit; }
    .dash-flat--occupied { border-color:#86efac; background:linear-gradient(180deg,#f0fdf4 0%,#fff 60%); }
    .dash-flat--vacant { background:#f8fafc; }
    .dash-flat:hover { transform:translateY(-1px); border-color:#93c5fd; box-shadow:0 4px 12px rgba(37,99,235,.12); color:inherit; }
    .dash-flat-code { font-size:.8rem; font-weight:800; color:#0f172a; line-height:1.15; }
    .dash-flat-owner { font-size:.7rem; line-height:1.25; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .dash-flat--occupied .dash-flat-owner { color:#047857; font-weight:600; }
    .dash-flat--vacant .dash-flat-owner { color:#94a3b8; font-style:italic; }
    .dash-flat-veh { position:absolute; top:4px; right:4px; display:inline-flex; align-items:center; gap:.1rem; padding:.05rem .3rem; border-radius:999px; background:#1d4ed8; color:#fff; font-size:.6rem; font-weight:800; line-height:1; }
    .dash-flat-veh i { font-size:.7rem; }
    .dash-empty { min-height:46vh; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; text-align:center; background:radial-gradient(circle at 50% 18%,rgba(37,99,235,.08),transparent 32%),linear-gradient(180deg,#fff 0%,#f8fbff 100%); border:1px dashed #cbd5e1; border-radius:.85rem; }
    .dash-empty-inner { width:min(520px,100%); margin:0 auto; }
    .dash-empty-icon { width:4.5rem; height:4.5rem; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; border-radius:1.1rem; background:linear-gradient(135deg,#dbeafe,#eff6ff); color:#1d4ed8; box-shadow:0 14px 34px rgba(37,99,235,.16); }
    .dash-empty-icon i { font-size:2.25rem; }
    .dash-empty-title { font-size:1.25rem; font-weight:850; color:#0f172a; margin-bottom:.45rem; }
    .dash-empty-copy { color:#64748b; line-height:1.65; margin:0 auto 1.15rem; }
    .dash-empty-steps { display:flex; flex-wrap:wrap; justify-content:center; gap:.5rem; margin-bottom:1.25rem; }
    .dash-empty-step { display:inline-flex; align-items:center; gap:.35rem; padding:.38rem .7rem; border-radius:999px; background:#f1f5f9; color:#475569; font-size:.78rem; font-weight:750; }
    #homesWingTabs.nav-pills .nav-link { border:1px solid transparent; }
    #homesWingTabs.nav-pills .nav-link:not(.active) { border-color:var(--dash-border); }
    @media (max-width:991.98px) { .dash-kpis,.quick-actions { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:575.98px) { .dash-kpis,.quick-actions { grid-template-columns:1fr; } .dash-floor-row { flex-direction:column; } .dash-floor-label { flex-basis:auto; justify-content:flex-start; font-size:.68rem; padding:.4rem .5rem; } .dash-flat { min-width:6.5rem; } .dash-flat-code { font-size:.75rem; } }
</style>

<section class="dash-wrap">
    @unless ($showSociety)
        <div class="alert alert-warning border-0 rounded-2 mb-0">You do not have permission to view society data. Contact an administrator if you need access.</div>
    @else
        <div class="dash-hero mb-3">
            <div class="dash-hero-content">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                    <div>
                        <div class="dash-eyebrow">Society overview</div>
                        <h4 class="dash-title">Dashboard</h4>
                        <p class="dash-sub">A quick view of homes, occupancy, vehicles, maintenance collection, and society expenses.</p>
                    </div>
                    <div class="dash-actions">
                        <a href="{{ route('admin.society-setup.wings') }}" class="btn btn-light btn-sm fw-semibold"><i class="ti ti-building me-1"></i> Society Setup</a>
                        @if($canMaintenance)<a href="{{ route('admin.maintenance.index') }}" class="btn btn-warning btn-sm fw-semibold"><i class="ti ti-currency-rupee me-1"></i> Maintenance</a>@endif
                        @if($canExpenses)<a href="{{ route('admin.expenses.index') }}" class="btn btn-info btn-sm fw-semibold text-white"><i class="ti ti-report-money me-1"></i> Expenses</a>@endif
                    </div>
                </div>
                <div class="dash-kpis">
                    <div class="dash-kpi"><span>Wings</span><strong>{{ $dashboardStats['wings'] }}</strong></div>
                    <div class="dash-kpi"><span>Total flats</span><strong>{{ $dashboardStats['flats'] }}</strong></div>
                    <div class="dash-kpi"><span>Occupied</span><strong>{{ $dashboardStats['occupied'] }}</strong></div>
                    <div class="dash-kpi"><span>Occupancy</span><strong>{{ $dashboardStats['occupancy_rate'] }}%</strong></div>
                </div>
            </div>
        </div>

        @if ($societyFlatsList->isEmpty())
            <div class="dash-card p-4">
                <div class="dash-empty" role="status" aria-live="polite">
                    <div class="dash-empty-inner">
                        <div class="dash-empty-icon" aria-hidden="true"><i class="ti ti-building-community"></i></div>
                        <h5 class="dash-empty-title">No society data added yet</h5>
                        <p class="dash-empty-copy">Add your society wings, floors, and flats to start seeing dashboard insights, resident details, vehicles, maintenance, and expenses here.</p>
                        <div class="dash-empty-steps" aria-hidden="true">
                            <span class="dash-empty-step"><i class="ti ti-building"></i> Add wings</span>
                            <span class="dash-empty-step"><i class="ti ti-stairs"></i> Set floors</span>
                            <span class="dash-empty-step"><i class="ti ti-home-plus"></i> Generate flats</span>
                        </div>
                        <a href="{{ route('admin.society-setup.wings') }}" class="btn btn-primary fw-bold"><i class="ti ti-plus me-1"></i> Society Setup</a>
                    </div>
                </div>
            </div>
        @else
            @php
                $totalFlats = $dashboardStats['flats'];
                $totalOccupied = $dashboardStats['occupied'];
                $totalVacant = $dashboardStats['vacant'];
                $totalVehicles = $dashboardStats['vehicles'];
            @endphp

            <div class="row g-3 mb-3">
                <div class="col-lg-4">
                    <div class="overview-card">
                        <div class="overview-icon green"><i class="ti ti-home-check"></i></div>
                        <div class="overview-label">Occupancy</div>
                        <div class="overview-value">{{ $totalOccupied }} / {{ $totalFlats }} flats</div>
                        <p class="overview-copy">{{ $totalVacant }} vacant flat(s), {{ $totalVehicles }} vehicle(s) registered.</p>
                        <div class="progress-soft"><span style="width: {{ $dashboardStats['occupancy_rate'] }}%;"></span></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="overview-card">
                        <div class="overview-icon blue"><i class="ti ti-currency-rupee"></i></div>
                        <div class="overview-label">Latest maintenance</div>
                        @if($latestMaintenance)
                            <div class="overview-value">{{ $latestMaintenance->label }}</div>
                            <p class="overview-copy">{{ (int) $latestMaintenance->paid_flats }} paid, {{ (int) $latestMaintenance->unpaid_flats }} unpaid.</p>
                            <div class="progress-soft"><span style="width: {{ $maintenanceRate }}%;"></span></div>
                        @else
                            <div class="overview-value">No cycle</div>
                            <p class="overview-copy">Create monthly maintenance to track paid and unpaid flats.</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="overview-card">
                        <div class="overview-icon purple"><i class="ti ti-report-money"></i></div>
                        <div class="overview-label">Latest expenses</div>
                        @if($latestExpense)
                            <div class="overview-value">Rs. {{ number_format((float) ($latestExpense->total_amount ?? 0), 2) }}</div>
                            <p class="overview-copy">{{ $latestExpense->label }} · {{ $latestExpense->items_count }} expense item(s).</p>
                        @else
                            <div class="overview-value">No expenses</div>
                            <p class="overview-copy">Create a monthly expense book for salary, bills, repairs, and events.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h5 class="dash-card-title"><i class="ti ti-bolt text-primary"></i> Quick Actions</h5>
                </div>
                <div class="card-body p-3">
                    <div class="quick-actions">
                        <a href="{{ route('admin.society-setup.flats') }}" class="quick-action"><i class="ti ti-home-edit"></i><span><strong>Manage flats</strong><span>Owners and vehicles</span></span></a>
                        @if($canMaintenance)<a href="{{ route('admin.maintenance.index') }}" class="quick-action"><i class="ti ti-receipt-rupee"></i><span><strong>Maintenance</strong><span>Paid and unpaid</span></span></a>@endif
                        @if($canExpenses)<a href="{{ route('admin.expenses.index') }}" class="quick-action"><i class="ti ti-report-money"></i><span><strong>Expenses</strong><span>Bills and salaries</span></span></a>@endif
                        <a href="{{ route('admin.dashboard.index') }}" class="quick-action"><i class="ti ti-refresh"></i><span><strong>Refresh</strong><span>Reload dashboard</span></span></a>
                    </div>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h5 class="dash-card-title"><i class="ti ti-building-community text-primary"></i> Homes Overview</h5>
                        <div class="text-muted small mt-1">Click any flat to open the full resident profile.</div>
                    </div>
                    <a href="{{ route('admin.society-setup.flats') }}" class="btn btn-primary btn-sm flex-shrink-0 text-nowrap"><i class="ti ti-pencil me-1"></i> Edit Flats</a>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-nowrap align-items-center gap-2 pb-3 mb-3 border-bottom overflow-auto">
                        {{-- <span class="fw-semibold text-nowrap flex-shrink-0"><i class="ti ti-home me-1"></i><span class="text-muted fw-normal fs-3">All homes</span> {{ $totalFlats }}</span> --}}
                        <ul class="nav nav-pills flex-nowrap gap-1 mb-0 flex-grow-1 min-w-0" style="flex:1 1 auto" id="homesWingTabs" role="tablist">
                            <li class="nav-item flex-shrink-0" role="presentation"><button class="nav-link py-1 px-2 fw-semibold active" id="homes-wing-tab-all" data-bs-toggle="tab" data-bs-target="#homes-wing-pane-all" type="button" role="tab" aria-controls="homes-wing-pane-all" aria-selected="true">All <span class="badge bg-light text-dark border ms-1">{{ $totalFlats }}</span></button></li>
                            @foreach ($societyWings as $wing)
                                <li class="nav-item flex-shrink-0" role="presentation"><button class="nav-link py-1 px-2 fw-semibold" id="homes-wing-tab-{{ $wing->id }}" data-bs-toggle="tab" data-bs-target="#homes-wing-pane-{{ $wing->id }}" type="button" role="tab" aria-controls="homes-wing-pane-{{ $wing->id }}" aria-selected="false">Wing {{ $wing->code }} <span class="badge bg-light text-dark border ms-1">{{ $wingStats[$wing->id]['total'] ?? 0 }}</span></button></li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- <div class="dash-legend" aria-hidden="true">
                        <span class="dash-legend-item"><span class="dash-legend-swatch dash-legend-swatch--occupied"></span> Occupied ({{ $totalOccupied }})</span>
                        <span class="dash-legend-item"><span class="dash-legend-swatch dash-legend-swatch--vacant"></span> Vacant ({{ $totalVacant }})</span>
                        <span class="dash-legend-item"><i class="ti ti-car"></i> Vehicles: <strong class="text-dark">{{ $totalVehicles }}</strong></span>
                    </div> --}}

                    <div class="tab-content" id="homesWingTabContent">
                        <div class="tab-pane fade show active" id="homes-wing-pane-all" role="tabpanel" aria-labelledby="homes-wing-tab-all" tabindex="0">
                            @foreach ($societyWings as $wing)
                                @include('Admin.Dashboard._wing_grid', ['wing' => $wing, 'floors' => $flatsByWingFloor[$wing->id] ?? collect(), 'stats' => $wingStats[$wing->id] ?? ['total' => 0, 'occupied' => 0, 'vacant' => 0, 'vehicles' => 0]])
                            @endforeach
                        </div>
                        @foreach ($societyWings as $wing)
                            <div class="tab-pane fade" id="homes-wing-pane-{{ $wing->id }}" role="tabpanel" aria-labelledby="homes-wing-tab-{{ $wing->id }}" tabindex="0">
                                @if (($wingStats[$wing->id]['total'] ?? 0) === 0)
                                    <div class="alert alert-light border mb-0 rounded-2">No flats for wing <strong>{{ $wing->code }}</strong>. <a href="{{ route('admin.society-setup.flats') }}" class="alert-link">Flats setup</a></div>
                                @else
                                    @include('Admin.Dashboard._wing_grid', ['wing' => $wing, 'floors' => $flatsByWingFloor[$wing->id] ?? collect(), 'stats' => $wingStats[$wing->id] ?? ['total' => 0, 'occupied' => 0, 'vacant' => 0, 'vehicles' => 0]])
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endunless
</section>
@endsection
