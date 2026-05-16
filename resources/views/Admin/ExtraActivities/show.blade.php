@extends('layouts.admin')
@section('title') Extra Activity Details @endsection
@php
    use Illuminate\Support\Facades\Storage;
    use App\Models\ExtraActivityPayment;

    $filterLabels = [
        'all' => 'All flats',
        'paid' => 'Paid list',
        'unpaid' => 'Unpaid list',
    ];
@endphp

@section('content')
<style>
    .extra-detail { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
    .extra-top { background: linear-gradient(135deg, #2563eb, #0f766e); color: #fff; border-radius: .85rem; padding: 1rem 1.15rem; box-shadow: 0 12px 30px rgba(37, 99, 235, .18); }
    .extra-top p { color: rgba(255,255,255,.78); }
    .stat-card { border: 0; border-radius: .72rem; box-shadow: 0 8px 22px rgba(15,23,42,.07); }
    .stat-card .card-body { padding: .9rem 1rem; }
    .stat-label { color: #64748b; font-size: .7rem; font-weight: 850; text-transform: uppercase; letter-spacing: .04em; }
    .stat-value { color: #0f172a; font-weight: 900; font-size: 1.35rem; line-height: 1; }
    .status-tabs .btn { border-radius: 999px; font-weight: 800; }
    .extra-panel { border: 0; border-radius: .9rem; box-shadow: 0 10px 28px rgba(15,23,42,.08); overflow: hidden; }
    .wing-accordion { display: grid; gap: .75rem; }
    .wing-item { border: 1px solid #e2e8f0; border-radius: .85rem; overflow: hidden; background: #fff; }
    .wing-toggle { width: 100%; border: 0; background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%); padding: .75rem .9rem; display: flex; align-items: center; justify-content: space-between; gap: .75rem; text-align: left; }
    .wing-title { display: inline-flex; align-items: center; gap: .5rem; margin: 0; font-size: .92rem; font-weight: 900; color: #0f172a; }
    .wing-code { width: 1.8rem; height: 1.8rem; border-radius: .48rem; display: inline-flex; align-items: center; justify-content: center; background: #ccfbf1; color: #0f766e; font-weight: 900; }
    .wing-meta { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: .4rem; }
    .wing-meta .badge { font-weight: 850; }
    .chevron { color: #64748b; transition: transform .15s ease; }
    .wing-toggle[aria-expanded="true"] .chevron { transform: rotate(180deg); }
    .wing-body { padding: .8rem; border-top: 1px solid #e2e8f0; background: #fff; }
    .floor-row { display: flex; gap: .45rem; align-items: stretch; margin-bottom: .55rem; }
    .floor-row:last-child { margin-bottom: 0; }
    .floor-label { flex: 0 0 4.35rem; display: flex; align-items: center; justify-content: flex-end; padding: .45rem .55rem; background: #f1f5f9; border-radius: .45rem; font-weight: 900; font-size: .66rem; color: #475569; text-transform: uppercase; letter-spacing: .04em; }
    .floor-label--ground { background: #e0f2fe; color: #0369a1; }
    .flat-grid { flex: 1 1 auto; min-width: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(10.5rem, 1fr)); gap: .45rem; padding: .45rem; border: 1px dashed #dbe4f0; border-radius: .55rem; background: #fbfcfe; }
    .flat-card { min-width: 0; display: flex; flex-direction: column; gap: .32rem; border: 1px solid #dbe4f0; border-radius: .65rem; background: #fff; padding: .55rem; }
    .flat-card--paid { border-color: #86efac; background: linear-gradient(180deg, #f0fdf4 0%, #fff 72%); }
    .flat-card--unpaid { border-color: #fdba74; background: linear-gradient(180deg, #fff7ed 0%, #fff 72%); }
    .flat-topline { display: flex; align-items: flex-start; justify-content: space-between; gap: .4rem; }
    .flat-code { font-size: .8rem; font-weight: 950; color: #0f172a; line-height: 1.1; }
    .flat-owner { font-size: .68rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .flat-phone { font-size: .66rem; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .status-pill { flex: 0 0 auto; border-radius: 999px; padding: .16rem .42rem; font-size: .62rem; font-weight: 900; }
    .status-pill--paid { background: #dcfce7; color: #047857; }
    .status-pill--unpaid { background: #ffedd5; color: #c2410c; }
    .proof-summary { min-height: 1.2rem; font-size: .64rem; color: #64748b; line-height: 1.25; }
    .proof-link { display: inline-flex; align-items: center; gap: .2rem; color: #1d4ed8; font-weight: 850; font-size: .66rem; text-decoration: none; }
    .proof-link:hover { text-decoration: underline; }
    .flat-actions { display: flex; gap: .35rem; margin-top: auto; }
    .flat-actions .btn { flex: 1 1 auto; border-radius: .5rem; font-size: .7rem; font-weight: 850; padding: .25rem .4rem; }
    .proof-panel { margin-top: .45rem; padding: .55rem; border-radius: .55rem; background: #f8fafc; border: 1px solid #e8eef7; }
    .proof-label { display: block; margin-bottom: .2rem; color: #475569; font-size: .62rem; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; }
    .proof-control { min-height: 32px; border-radius: .45rem; border-color: #dbe4f0; font-size: .76rem; padding: .32rem .48rem; }
    textarea.proof-control { min-height: 48px; resize: vertical; }
    .activity-note { border: 1px dashed #cbd5e1; background: #f8fafc; border-radius: .75rem; padding: .8rem; color: #64748b; font-size: .82rem; }
    .empty-state { border: 1px dashed #cbd5e1; background: #f8fafc; border-radius: .75rem; padding: 1.25rem; text-align: center; color: #64748b; }
    @media (max-width: 575.98px) { .floor-row { flex-direction: column; } .floor-label { justify-content: flex-start; flex-basis: auto; } .flat-grid { grid-template-columns: 1fr; } .wing-toggle { align-items: flex-start; } }
</style>

<section class="extra-detail">
    <div class="extra-top mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="small text-white-50 fw-bold text-uppercase">Extra Activity Details</div>
                <h4 class="fw-bold mb-1">{{ $extraActivity->title }}</h4>
                <p class="mb-0">
                    {{ $extraActivity->type_label }} · Rs. {{ number_format((float) $extraActivity->amount_per_flat, 2) }} per flat
                    @if($extraActivity->due_date)
                        · Due {{ $extraActivity->due_date->format('d M Y') }}
                    @endif
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.extra-activities.index') }}" class="btn btn-light btn-sm fw-semibold">
                    <i class="ti ti-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('admin.extra-activities.show', [$extraActivity, 'status' => 'unpaid']) }}" class="btn btn-warning btn-sm fw-semibold">
                    <i class="ti ti-alert-circle me-1"></i> Unpaid List
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-2">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-2">{{ $errors->first() }}</div>
    @endif

    @if($extraActivity->notes)
        <div class="activity-note mb-3">
            <i class="ti ti-note me-1"></i> {{ $extraActivity->notes }}
        </div>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6"><div class="card stat-card"><div class="card-body"><div class="stat-label">Total flats</div><div class="stat-value">{{ $stats['total'] }}</div></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card"><div class="card-body"><div class="stat-label">Paid</div><div class="stat-value text-success">{{ $stats['paid'] }}</div></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card"><div class="card-body"><div class="stat-label">Unpaid</div><div class="stat-value text-warning">{{ $stats['unpaid'] }}</div></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card"><div class="card-body"><div class="stat-label">Collected</div><div class="stat-value text-primary">Rs. {{ number_format($stats['collected'], 0) }}</div></div></div></div>
    </div>

    <div class="card extra-panel">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">{{ $filterLabels[$status] ?? 'All flats' }}</h5>
                    <p class="text-muted small mb-0">Wings are collapsible. Open details only when you need transaction ID, note, or receipt upload.</p>
                </div>
                <div class="status-tabs d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.extra-activities.show', [$extraActivity, 'status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                    <a href="{{ route('admin.extra-activities.show', [$extraActivity, 'status' => 'paid']) }}" class="btn btn-sm {{ $status === 'paid' ? 'btn-success' : 'btn-outline-success' }}">Paid</a>
                    <a href="{{ route('admin.extra-activities.show', [$extraActivity, 'status' => 'unpaid']) }}" class="btn btn-sm {{ $status === 'unpaid' ? 'btn-warning' : 'btn-outline-warning' }}">Unpaid</a>
                </div>
            </div>

            @if($payments->isEmpty())
                <div class="empty-state">No flats found for this filter.</div>
            @else
                <div class="wing-accordion" id="extraActivityWingAccordion">
                    @foreach($wings as $wing)
                        @php
                            $floors = $paymentsByWingFloor->get($wing->id, collect());
                            $wingPayments = $floors->flatten(1);
                            $wingCount = $wingPayments->count();
                            $wingPaid = $wingPayments->where('status', ExtraActivityPayment::STATUS_PAID)->count();
                            $wingUnpaid = $wingPayments->where('status', ExtraActivityPayment::STATUS_UNPAID)->count();
                            $collapseId = 'wing-extra-activity-'.$wing->id;
                        @endphp
                        @if($wingCount > 0)
                            <section class="wing-item" aria-label="Wing {{ $wing->code }}">
                                <button class="wing-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                    <span class="wing-title"><span class="wing-code">{{ $wing->code }}</span> Wing {{ $wing->code }}</span>
                                    <span class="wing-meta">
                                        <span class="badge bg-light text-dark border">{{ $wingCount }} flats</span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $wingPaid }} paid</span>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">{{ $wingUnpaid }} unpaid</span>
                                        <i class="ti ti-chevron-down chevron"></i>
                                    </span>
                                </button>

                                <div id="{{ $collapseId }}" class="collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#extraActivityWingAccordion">
                                    <div class="wing-body">
                                        @foreach($floors as $floorNumber => $floorPayments)
                                            @php $isGround = (int) $floorNumber === 0; @endphp
                                            <div class="floor-row">
                                                <div class="floor-label {{ $isGround ? 'floor-label--ground' : '' }}">{{ $isGround ? 'Ground' : 'Floor '.$floorNumber }}</div>
                                                <div class="flat-grid">
                                                    @foreach($floorPayments as $payment)
                                                        @php
                                                            $flat = $payment->flat;
                                                            $isPaid = $payment->status === ExtraActivityPayment::STATUS_PAID;
                                                            $nextStatus = $isPaid ? ExtraActivityPayment::STATUS_UNPAID : ExtraActivityPayment::STATUS_PAID;
                                                            $detailsId = 'extra-payment-details-'.$payment->id;
                                                        @endphp
                                                        <div class="flat-card {{ $isPaid ? 'flat-card--paid' : 'flat-card--unpaid' }}">
                                                            <div class="flat-topline">
                                                                <div class="min-w-0">
                                                                    <div class="flat-code">{{ $flat?->unit_code }}</div>
                                                                    <div class="flat-owner">{{ trim((string) $flat?->owner_name) !== '' ? $flat->owner_name : 'Vacant' }}</div>
                                                                </div>
                                                                <span class="status-pill {{ $isPaid ? 'status-pill--paid' : 'status-pill--unpaid' }}">{{ $isPaid ? 'Paid' : 'Unpaid' }}</span>
                                                            </div>
                                                            <div class="flat-phone">{{ $flat?->owner_mobile ?: 'No mobile number' }}</div>
                                                            @if($isPaid && $payment->paid_at)
                                                                <div class="flat-phone">Paid: {{ $payment->paid_at->format('d M Y, h:i A') }}</div>
                                                            @endif
                                                            <div class="proof-summary">
                                                                @if($payment->transaction_id)
                                                                    Txn: {{ $payment->transaction_id }}
                                                                @elseif($payment->payment_note)
                                                                    {{ \Illuminate\Support\Str::limit($payment->payment_note, 42) }}
                                                                @elseif($payment->receipt_path)
                                                                    <a href="{{ Storage::disk('public')->url($payment->receipt_path) }}" target="_blank" class="proof-link"><i class="ti ti-paperclip"></i> Receipt</a>
                                                                @else
                                                                    No proof added
                                                                @endif
                                                            </div>

                                                            <div class="flat-actions">
                                                                <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $detailsId }}" aria-expanded="false" aria-controls="{{ $detailsId }}">Details</button>
                                                                @if($isPaid)
                                                                    <form method="POST" action="{{ route('admin.extra-activities.payments.update', [$extraActivity, $payment]) }}">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                                        <button type="submit" class="btn btn-outline-warning btn-sm w-100">Unpaid</button>
                                                                    </form>
                                                                @else
                                                                    <form method="POST" action="{{ route('admin.extra-activities.payments.update', [$extraActivity, $payment]) }}">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="paid">
                                                                        <button type="submit" class="btn btn-success btn-sm w-100">Paid</button>
                                                                    </form>
                                                                @endif
                                                            </div>

                                                            <div class="collapse" id="{{ $detailsId }}">
                                                                <form method="POST" action="{{ route('admin.extra-activities.payments.update', [$extraActivity, $payment]) }}" enctype="multipart/form-data" class="proof-panel">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="paid">
                                                                    <label class="proof-label" for="transaction_id_{{ $payment->id }}">Transaction ID</label>
                                                                    <input type="text" id="transaction_id_{{ $payment->id }}" name="transaction_id" value="{{ $payment->transaction_id }}" class="form-control proof-control mb-2" placeholder="UPI / cheque / receipt no.">
                                                                    <label class="proof-label" for="payment_note_{{ $payment->id }}">Payment note</label>
                                                                    <textarea id="payment_note_{{ $payment->id }}" name="payment_note" class="form-control proof-control mb-2" rows="2" placeholder="Any payment note">{{ $payment->payment_note }}</textarea>
                                                                    <label class="proof-label" for="receipt_file_{{ $payment->id }}">Receipt / proof</label>
                                                                    @if($payment->receipt_path)
                                                                        <div class="mb-2">
                                                                            <a href="{{ Storage::disk('public')->url($payment->receipt_path) }}" target="_blank" class="proof-link">
                                                                                <i class="ti ti-paperclip"></i> {{ $payment->receipt_original_name ?: 'View receipt' }}
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" id="receipt_file_{{ $payment->id }}" name="receipt_file" class="form-control proof-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                                                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold mt-2">{{ $isPaid ? 'Update Proof' : 'Mark Paid With Proof' }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
