@extends('layouts.admin')

@section('title') Flat Profile @endsection

@php
    use Illuminate\Support\Facades\Storage;
    use App\Models\MaintenancePayment;

    $flat = $flat ?? null;
    $unitCode = $unit_code ?? optional($flat)->unit_code ?? '-';
    $ownerName = old('owner_name', $owner_name ?? optional($flat)->owner_name ?? '');
    $ownerMobile = old('owner_mobile', $owner_mobile ?? optional($flat)->owner_mobile ?? '');
    $ownerEmail = old('owner_email', $owner_email ?? optional($flat)->owner_email ?? '');
    $vehicleCount = (int) ($vehicle_count ?? optional($flat)->vehicles_count ?? 0);
    $documents = $documents ?? optional($flat)->documents ?? collect();
    $maintenanceHistory = $maintenanceHistory ?? collect();
    $maintenanceStats = $maintenanceStats ?? ['total' => 0, 'paid' => 0, 'unpaid' => 0];
    $lastMaintenancePaid = $last_maintenance_paid ?? 'No paid maintenance yet';
    $lastUpdated = $last_updated ?? optional($flat)->updated_at?->format('d M Y, h:i A') ?? '-';
    $backUrl = $back_url ?? route('admin.dashboard.index');
    $formAction = $form_action ?? '#';
    $isOccupied = trim((string) $ownerName) !== '';
@endphp

@section('content')
<style>
    .flat-profile { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
    .profile-hero { border-radius: .9rem; overflow: hidden; background: linear-gradient(135deg, #0f6bff, #0b4bb3); color: #fff; box-shadow: 0 16px 36px rgba(15, 107, 255, .2); }
    .profile-hero-inner { padding: 1.25rem; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 1rem; align-items: flex-end; }
    .profile-back { color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: .35rem; font-weight: 750; opacity: .86; margin-bottom: .75rem; }
    .profile-back:hover { color: #fff; opacity: 1; }
    .profile-title { font-size: clamp(1.55rem, 3vw, 2.35rem); font-weight: 900; line-height: 1.05; margin: 0; letter-spacing: 0; }
    .profile-sub { color: rgba(255,255,255,.78); margin: .45rem 0 0; }
    .profile-status { display: inline-flex; align-items: center; gap: .35rem; border-radius: 999px; padding: .45rem .75rem; font-size: .78rem; font-weight: 900; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.2); }
    .profile-card { border: 0; border-radius: .85rem; box-shadow: 0 10px 28px rgba(15, 23, 42, .08); overflow: hidden; }
    .profile-card .card-header { background: linear-gradient(180deg, #fbfdff, #fff); border-bottom: 1px solid #e8eef7; padding: 1rem 1.15rem; }
    .profile-card-title { margin: 0; font-size: .98rem; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: .45rem; }
    .profile-card .card-body { padding: 1.15rem; }
    .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem; }
    .info-item { padding: .85rem; border: 1px solid #e8eef7; border-radius: .65rem; background: #f8fbff; min-width: 0; }
    .info-label { display: block; color: #64748b; font-size: .72rem; font-weight: 850; text-transform: uppercase; letter-spacing: .04em; margin-bottom: .25rem; }
    .info-value { color: #0f172a; font-size: .95rem; font-weight: 800; word-break: break-word; }
    .metric-row { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }
    .metric { padding: .9rem; border-radius: .75rem; background: #f8fafc; border: 1px solid #e8eef7; }
    .metric span { display: block; color: #64748b; font-size: .72rem; font-weight: 850; text-transform: uppercase; letter-spacing: .04em; }
    .metric strong { display: block; margin-top: .2rem; color: #0f172a; font-size: 1.3rem; line-height: 1; }
    .form-label { font-size: .82rem; font-weight: 850; color: #334155; margin-bottom: .35rem; }
    .form-control { border-color: #dbe4f0; border-radius: .6rem; min-height: 42px; }
    .form-control:focus { border-color: #93c5fd; box-shadow: 0 0 0 .2rem rgba(37,99,235,.12); }
    .form-control[readonly] { background: #f8fafc; color: #475569; }
    .vehicle-pill { display: inline-flex; align-items: center; gap: .35rem; padding: .42rem .65rem; border-radius: 999px; background: #eff6ff; color: #1d4ed8; font-weight: 850; font-size: .78rem; margin: .2rem .25rem .2rem 0; }
    .doc-row, .maintenance-row { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .75rem 0; border-bottom: 1px solid #edf2f7; }
    .doc-row:last-child, .maintenance-row:last-child { border-bottom: 0; }
    .doc-title, .maint-title { font-weight: 850; color: #0f172a; }
    .doc-sub, .maint-sub { color: #64748b; font-size: .76rem; margin-top: .15rem; }
    .paid-badge, .unpaid-badge { border-radius: 999px; padding: .28rem .55rem; font-size: .72rem; font-weight: 900; white-space: nowrap; }
    .paid-badge { background: #dcfce7; color: #047857; }
    .unpaid-badge { background: #ffedd5; color: #c2410c; }
    .empty-box { border: 1px dashed #cbd5e1; background: #f8fafc; color: #64748b; border-radius: .75rem; padding: 1rem; text-align: center; }
    @media (max-width: 767.98px) { .info-grid, .metric-row { grid-template-columns: 1fr; } .profile-hero-inner { align-items: flex-start; } }
</style>

<section class="flat-profile">
    <div class="profile-hero mb-3">
        <div class="profile-hero-inner">
            <div>
                <a href="{{ $backUrl }}" class="profile-back"><i class="ti ti-arrow-left"></i> Back to dashboard</a>
                <h4 class="profile-title">Flat {{ $unitCode }}</h4>
                <p class="profile-sub">
                    {{ optional($flat?->wing)->label ?? ('Wing '.optional($flat?->wing)->code) }}
                    @if($flat)
                        · {{ (int) $flat->floor_number === 0 ? 'Ground floor' : 'Floor '.$flat->floor_number }}
                    @endif
                </p>
            </div>
            <span class="profile-status">
                <i class="ti {{ $isOccupied ? 'ti-user-check' : 'ti-user-off' }}"></i>
                {{ $isOccupied ? 'Occupied' : 'Vacant' }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-2">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-2">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card profile-card mb-3">
                <div class="card-header">
                    <h5 class="profile-card-title"><i class="ti ti-user text-primary"></i> Owner Profile</h5>
                </div>
                <div class="card-body">
                    <form id="flat-owner-form" method="post" action="{{ $formAction }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="owner_name">Owner name</label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" value="{{ $ownerName }}" placeholder="Owner full name" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="owner_mobile">Mobile number</label>
                                <input type="tel" class="form-control" id="owner_mobile" name="owner_mobile" value="{{ $ownerMobile }}" placeholder="Mobile number" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="owner_email">Email address</label>
                                <input type="email" class="form-control" id="owner_email" name="owner_email" value="{{ $ownerEmail }}" placeholder="name@example.com" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="vehicles_2w">2W</label>
                                <input type="number" min="0" max="99" class="form-control" id="vehicles_2w" name="vehicles_2w" value="{{ old('vehicles_2w', optional($flat)->vehicles_2w ?? 0) }}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="vehicles_3w">3W</label>
                                <input type="number" min="0" max="99" class="form-control" id="vehicles_3w" name="vehicles_3w" value="{{ old('vehicles_3w', optional($flat)->vehicles_3w ?? 0) }}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="vehicles_4w">4W</label>
                                <input type="number" min="0" max="99" class="form-control" id="vehicles_4w" name="vehicles_4w" value="{{ old('vehicles_4w', optional($flat)->vehicles_4w ?? 0) }}" readonly>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-primary fw-semibold order-2 order-sm-1" id="btn-save" disabled>
                                <i class="ti ti-device-floppy me-1"></i> Save Profile
                            </button>
                            <button type="button" class="btn btn-outline-primary fw-semibold order-1 order-sm-2" id="btn-edit">
                                <i class="ti ti-pencil me-1"></i> Edit Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card profile-card">
                <div class="card-header">
                    <h5 class="profile-card-title"><i class="ti ti-receipt text-primary"></i> Maintenance History</h5>
                </div>
                <div class="card-body">
                    @if($maintenanceHistory->isEmpty())
                        <div class="empty-box">No maintenance records found for this flat.</div>
                    @else
                        @foreach($maintenanceHistory as $payment)
                            <div class="maintenance-row">
                                <div>
                                    <div class="maint-title">{{ optional($payment->period)->label ?? 'Maintenance' }}</div>
                                    <div class="maint-sub">
                                        @if(optional($payment->period)->amount !== null)
                                            Rs. {{ number_format((float) $payment->period->amount, 2) }}
                                        @else
                                            Amount not set
                                        @endif
                                        @if($payment->paid_at)
                                            · Paid {{ $payment->paid_at->format('d M Y') }}
                                        @endif
                                        @if($payment->transaction_id)
                                            · Txn: {{ $payment->transaction_id }}
                                        @endif
                                    </div>
                                    @if($payment->payment_note)
                                        <div class="maint-sub">Note: {{ $payment->payment_note }}</div>
                                    @endif
                                    @if($payment->receipt_path)
                                        <a href="{{ Storage::disk('public')->url($payment->receipt_path) }}" target="_blank" class="small fw-semibold text-decoration-none">
                                            <i class="ti ti-paperclip me-1"></i>{{ $payment->receipt_original_name ?: 'View receipt' }}
                                        </a>
                                    @endif
                                </div>
                                <span class="{{ $payment->status === MaintenancePayment::STATUS_PAID ? 'paid-badge' : 'unpaid-badge' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card profile-card mb-3">
                <div class="card-header">
                    <h5 class="profile-card-title"><i class="ti ti-info-circle text-primary"></i> Flat Details</h5>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">Flat</span><span class="info-value">{{ $unitCode }}</span></div>
                        <div class="info-item"><span class="info-label">Wing</span><span class="info-value">{{ optional($flat?->wing)->code ?? '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Floor</span><span class="info-value">{{ $flat ? ((int) $flat->floor_number === 0 ? 'Ground' : $flat->floor_number) : '-' }}</span></div>
                        <div class="info-item"><span class="info-label">Flat no.</span><span class="info-value">{{ optional($flat)->flat_index ?? '-' }}</span></div>
                    </div>
                </div>
            </div>

            <div class="card profile-card mb-3">
                <div class="card-header">
                    <h5 class="profile-card-title"><i class="ti ti-car text-primary"></i> Vehicles</h5>
                </div>
                <div class="card-body">
                    <div class="metric-row mb-3">
                        <div class="metric"><span>2 Wheeler</span><strong>{{ (int) optional($flat)->vehicles_2w }}</strong></div>
                        <div class="metric"><span>3 Wheeler</span><strong>{{ (int) optional($flat)->vehicles_3w }}</strong></div>
                        <div class="metric"><span>4 Wheeler</span><strong>{{ (int) optional($flat)->vehicles_4w }}</strong></div>
                    </div>
                    <span class="vehicle-pill"><i class="ti ti-car"></i> Total {{ $vehicleCount }} vehicle(s)</span>
                </div>
            </div>

            <div class="card profile-card mb-3">
                <div class="card-header">
                    <h5 class="profile-card-title"><i class="ti ti-chart-pie text-primary"></i> Maintenance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="metric-row">
                        <div class="metric"><span>Total</span><strong>{{ $maintenanceStats['total'] ?? 0 }}</strong></div>
                        <div class="metric"><span>Paid</span><strong class="text-success">{{ $maintenanceStats['paid'] ?? 0 }}</strong></div>
                        <div class="metric"><span>Unpaid</span><strong class="text-warning">{{ $maintenanceStats['unpaid'] ?? 0 }}</strong></div>
                    </div>
                    <div class="info-item mt-3"><span class="info-label">Last paid</span><span class="info-value">{{ $lastMaintenancePaid }}</span></div>
                </div>
            </div>

            <div class="card profile-card">
                <div class="card-header">
                    <h5 class="profile-card-title"><i class="ti ti-files text-primary"></i> Documents</h5>
                </div>
                <div class="card-body">
                    @if($documents->isEmpty())
                        <div class="empty-box">No documents uploaded.</div>
                    @else
                        @foreach($documents as $doc)
                            <div class="doc-row">
                                <div class="min-w-0">
                                    <div class="doc-title">{{ $doc->name }}</div>
                                    <div class="doc-sub">{{ $doc->file_original_name }}</div>
                                </div>
                                @if($doc->file_path)
                                    <a href="{{ Storage::disk('public')->url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary flex-shrink-0">View</a>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    var editBtn = document.getElementById('btn-edit');
    var saveBtn = document.getElementById('btn-save');
    var inputs = ['owner_name', 'owner_mobile', 'owner_email', 'vehicles_2w', 'vehicles_3w', 'vehicles_4w'].map(function (id) {
        return document.getElementById(id);
    }).filter(Boolean);
    var editing = false;
    var snapshot = [];

    function setEditing(on) {
        editing = on;
        inputs.forEach(function (el) {
            el.readOnly = !on;
            el.toggleAttribute('readonly', !on);
        });
        saveBtn.disabled = !on;
        editBtn.classList.toggle('active', on);
        editBtn.innerHTML = on
            ? '<i class="ti ti-x me-1"></i> Cancel'
            : '<i class="ti ti-pencil me-1"></i> Edit Profile';
    }

    editBtn.addEventListener('click', function () {
        if (editing) {
            inputs.forEach(function (el, i) {
                if (snapshot[i] !== undefined) { el.value = snapshot[i]; }
            });
            setEditing(false);
        } else {
            snapshot = inputs.map(function (el) { return el.value; });
            setEditing(true);
            if (inputs[0]) { inputs[0].focus(); }
        }
    });
})();
</script>
@endsection
