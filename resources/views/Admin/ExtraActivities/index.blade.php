@extends('layouts.admin')
@section('title') Extra Activity @endsection

@section('content')
<style>
    .extra-page {
        --extra-primary: #0f6bff;
        --extra-dark: #0b4bb3;
        --extra-ink: #0f172a;
        --extra-muted: #64748b;
        --extra-border: #e2e8f0;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
    .extra-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #0f6bff 0%, #0b4bb3 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 1.35rem;
        box-shadow: 0 18px 42px rgba(15, 107, 255, .22);
    }
    .extra-hero::after {
        content: "";
        position: absolute;
        right: -4rem;
        bottom: -5rem;
        width: 16rem;
        height: 16rem;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
    }
    .extra-hero-content { position: relative; z-index: 1; }
    .extra-eyebrow { color: rgba(255,255,255,.68) !important; font-size: .72rem; font-weight: 850; text-transform: uppercase; letter-spacing: .08em; margin-bottom: .3rem; }
    .extra-hero h4 { color: #fff !important; font-size: clamp(1.45rem, 2.5vw, 2.25rem); font-weight: 900 !important; letter-spacing: 0; }
    .extra-hero p { color: rgba(255,255,255,.78) !important; max-width: 44rem; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
    .quick-stat { background: rgba(255,255,255,.13); border: 1px solid rgba(255,255,255,.18); border-radius: .85rem; padding: .8rem .9rem; min-width: 8.8rem; }
    .quick-stat span { display: block; color: rgba(255,255,255,.68); font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .quick-stat strong { display: block; color: #fff; font-size: 1.45rem; line-height: 1; margin-top: .25rem; }
    .extra-card { border: 0; border-radius: 1rem; box-shadow: 0 12px 30px rgba(15, 23, 42, .08); overflow: hidden; }
    .extra-card h5 { color: #0f172a !important; font-size: 1rem; font-weight: 900 !important; }
    .extra-card .text-muted { color: #64748b !important; }
    .create-card { margin-top: -1.25rem; position: sticky; top: 84px; z-index: 1; }
    .create-head { padding: 1rem 1.1rem; background: linear-gradient(180deg, #f8fbff, #fff); border-bottom: 1px solid var(--extra-border); }
    .create-icon { width: 2.5rem; height: 2.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: .75rem; background: #dbeafe; color: #1d4ed8; font-size: 1.25rem; }
    .extra-label { font-size: .75rem; font-weight: 850; color: #334155; margin-bottom: .35rem; }
    .extra-control { border-color: #dbe4f0; border-radius: .65rem; min-height: 44px; }
    .extra-control:focus { border-color: #93c5fd; box-shadow: 0 0 0 .2rem rgba(37,99,235,.12); }
    .helper-box { border: 1px dashed #cbd5e1; border-radius: .75rem; padding: .8rem; background: #f8fafc; color: var(--extra-muted); font-size: .78rem; line-height: 1.45; }
    .history-card .card-body { padding: 1.1rem; }
    .history-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; margin-bottom: 1rem; }
    .activity-list { display: grid; gap: .85rem; }
    .activity-card { border: 1px solid #e8eef7; border-radius: .9rem; padding: 1rem; background: linear-gradient(180deg, #fff 0%, #fbfdff 100%); transition: border-color .15s ease, box-shadow .15s ease, transform .12s ease; }
    .activity-card:hover { border-color: #bfdbfe; box-shadow: 0 10px 26px rgba(37,99,235,.09); transform: translateY(-1px); }
    .activity-main { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 1rem; align-items: start; }
    .activity-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
    .activity-title { font-weight: 900; color: var(--extra-ink); font-size: 1.05rem; line-height: 1.15; }
    .activity-badge { border-radius: 999px; padding: .28rem .55rem; background: #eff6ff; color: #1d4ed8; font-size: .72rem; font-weight: 850; }
    .activity-sub { font-size: .78rem; color: var(--extra-muted); margin-top: .35rem; }
    .activity-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .5rem; margin-top: .85rem; }
    .activity-stat { border-radius: .7rem; padding: .65rem; background: #f1f5f9; color: #334155; }
    .activity-stat span { display: block; font-size: .68rem; font-weight: 850; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
    .activity-stat strong { display: block; margin-top: .15rem; font-size: 1.05rem; line-height: 1; color: #0f172a; }
    .activity-stat--paid { background: #ecfdf3; }
    .activity-stat--paid strong { color: #047857; }
    .activity-stat--unpaid { background: #fff7ed; }
    .activity-stat--unpaid strong { color: #c2410c; }
    .activity-progress { height: .55rem; border-radius: 999px; background: #e2e8f0; overflow: hidden; margin-top: .85rem; }
    .activity-progress span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #16a34a, #22c55e); }
    .activity-actions { display: flex; flex-direction: column; gap: .45rem; min-width: 7.5rem; }
    .activity-actions .btn { font-weight: 800; border-radius: .65rem; }
    .empty-state { min-height: 24rem; display: flex; align-items: center; justify-content: center; border: 1px dashed #cbd5e1; background: radial-gradient(circle at 50% 10%, rgba(37,99,235,.08), transparent 32%), #f8fafc; border-radius: .9rem; padding: 2rem 1rem; text-align: center; color: var(--extra-muted); }
    .empty-icon { width: 4rem; height: 4rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 1rem; background: #dbeafe; color: #1d4ed8; font-size: 2rem; margin-bottom: .9rem; }
    @media (max-width: 991.98px) { .create-card { margin-top: 0; position: static; } }
    @media (max-width: 767.98px) {
        .activity-main { grid-template-columns: 1fr; }
        .activity-actions { flex-direction: row; min-width: 0; }
        .activity-actions .btn, .activity-actions form { flex: 1 1 auto; }
        .activity-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .quick-stat { flex: 1 1 9rem; }
    }
</style>

<section class="extra-page">
    <div class="extra-hero mb-3">
        <div class="extra-hero-content">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="extra-eyebrow">One-time society collections</div>
                    <h4 class="fw-bold mb-2">Extra Activity Collections</h4>
                    <p class="mb-0">Create special collections for repairs, functions, painting, upgrades, or any one-time amount you need to collect from every flat.</p>
                </div>
                <div class="hero-actions">
                    <a href="{{ route('admin.dashboard.index') }}" class="btn btn-light btn-sm fw-semibold">
                        <i class="ti ti-layout-dashboard me-1"></i> Dashboard
                    </a>
                    @if($latestActivity)
                        <a href="{{ route('admin.extra-activities.show', $latestActivity) }}" class="btn btn-warning btn-sm fw-semibold">
                            <i class="ti ti-arrow-right me-1"></i> Latest Activity
                        </a>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <div class="quick-stat"><span>Activities</span><strong>{{ $summary['activities'] ?? 0 }}</strong></div>
                <div class="quick-stat"><span>Paid entries</span><strong>{{ $summary['paid'] ?? 0 }}</strong></div>
                <div class="quick-stat"><span>Unpaid entries</span><strong>{{ $summary['unpaid'] ?? 0 }}</strong></div>
                <div class="quick-stat"><span>Collection</span><strong>{{ $summary['collection_rate'] ?? 0 }}%</strong></div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3 shadow-sm">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3 align-items-start">
        <div class="col-lg-4 col-xl-3">
            <div class="card extra-card create-card">
                <div class="create-head">
                    <div class="d-flex align-items-center gap-3">
                        <span class="create-icon"><i class="ti ti-calendar-event"></i></span>
                        <div>
                            <h5 class="fw-bold mb-1">New Activity</h5>
                            <p class="text-muted small mb-0">One-time billing for all flats.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-xl-4">
                    <form method="POST" action="{{ route('admin.extra-activities.store') }}" autocomplete="off">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="extra-label">Activity title</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control extra-control @error('title') is-invalid @enderror" placeholder="Building repair collection" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="activity_type" class="extra-label">Activity type</label>
                            <select id="activity_type" name="activity_type" class="form-select extra-control @error('activity_type') is-invalid @enderror" required>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" @selected(old('activity_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('activity_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount_per_flat" class="extra-label">Amount per flat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rs.</span>
                                <input type="number" step="0.01" min="1" id="amount_per_flat" name="amount_per_flat" value="{{ old('amount_per_flat') }}" class="form-control extra-control border-start-0 @error('amount_per_flat') is-invalid @enderror" placeholder="10000" required>
                            </div>
                            @error('amount_per_flat')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-2">
                            <div class="col-sm-6 col-lg-12">
                                <label for="target_amount" class="extra-label">Target amount <span class="text-muted fw-normal">(optional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rs.</span>
                                    <input type="number" step="0.01" min="0" id="target_amount" name="target_amount" value="{{ old('target_amount') }}" class="form-control extra-control border-start-0 @error('target_amount') is-invalid @enderror" placeholder="500000">
                                </div>
                                @error('target_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6 col-lg-12">
                                <label for="due_date" class="extra-label">Due date <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" class="form-control extra-control @error('due_date') is-invalid @enderror">
                                @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="notes" class="extra-label">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="3" class="form-control extra-control @error('notes') is-invalid @enderror" placeholder="Example: Repairing work for parking area">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- <div class="helper-box mt-3">
                            <i class="ti ti-info-circle me-1"></i>
                            Saving creates unpaid entries for every flat. Open the activity to mark paid, add transaction ID, notes, and receipt proof.
                        </div> --}}

                        <button type="submit" class="btn btn-primary w-100 fw-bold mt-3 py-2">
                            <i class="ti ti-device-floppy me-1"></i> Create Activity
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9">
            <div class="card extra-card history-card">
                <div class="card-body">
                    <div class="history-toolbar">
                        <div>
                            <h5 class="fw-bold mb-1">Activity History</h5>
                            <p class="text-muted small mb-0">Open an activity to collect payments, upload proof, or view unpaid flats.</p>
                        </div>
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">{{ $activities->total() }} activity(s)</span>
                    </div>

                    @if($activities->isEmpty())
                        <div class="empty-state">
                            <div>
                                <span class="empty-icon"><i class="ti ti-calendar-event"></i></span>
                                <h5 class="fw-bold text-dark mb-2">No extra activity yet</h5>
                                <p class="mb-0">Create your first repair, function, or upgrade collection from the panel on the left.</p>
                            </div>
                        </div>
                    @else
                        <div class="activity-list">
                            @foreach($activities as $activity)
                                @php
                                    $total = max((int) $activity->total_flats, 0);
                                    $paid = (int) $activity->paid_flats;
                                    $unpaid = (int) $activity->unpaid_flats;
                                    $rate = $total > 0 ? round(($paid / $total) * 100) : 0;
                                    $expected = $total * (float) $activity->amount_per_flat;
                                    $collected = $paid * (float) $activity->amount_per_flat;
                                @endphp
                                <article class="activity-card">
                                    <div class="activity-main">
                                        <div>
                                            <div class="activity-title-row">
                                                <div class="activity-title">{{ $activity->title }}</div>
                                                <span class="activity-badge">{{ $activity->type_label }}</span>
                                                <span class="activity-badge">{{ $rate }}% collected</span>
                                            </div>
                                            <div class="activity-sub">
                                                Rs. {{ number_format((float) $activity->amount_per_flat, 2) }} per flat
                                                @if($activity->due_date)
                                                    · Due {{ $activity->due_date->format('d M Y') }}
                                                @endif
                                                @if($activity->notes)
                                                    · {{ \Illuminate\Support\Str::limit($activity->notes, 90) }}
                                                @endif
                                            </div>

                                            <div class="activity-stats">
                                                <div class="activity-stat"><span>Total</span><strong>{{ $total }}</strong></div>
                                                <div class="activity-stat activity-stat--paid"><span>Paid</span><strong>{{ $paid }}</strong></div>
                                                <div class="activity-stat activity-stat--unpaid"><span>Unpaid</span><strong>{{ $unpaid }}</strong></div>
                                                <div class="activity-stat"><span>Collected</span><strong>Rs. {{ number_format($collected, 0) }}</strong></div>
                                            </div>

                                            <div class="activity-progress" aria-label="{{ $rate }} percent collected"><span style="width: {{ $rate }}%"></span></div>
                                            <div class="activity-sub mt-2">Expected: Rs. {{ number_format($expected, 2) }}</div>
                                        </div>

                                        <div class="activity-actions">
                                            <a href="{{ route('admin.extra-activities.show', $activity) }}" class="btn btn-primary btn-sm">
                                                <i class="ti ti-eye me-1"></i> Open
                                            </a>
                                            <a href="{{ route('admin.extra-activities.show', [$activity, 'status' => 'unpaid']) }}" class="btn btn-outline-warning btn-sm">
                                                <i class="ti ti-alert-circle me-1"></i> Unpaid
                                            </a>
                                            <form method="POST" action="{{ route('admin.extra-activities.destroy', $activity) }}" onsubmit="return confirm('Delete this extra activity and all payment entries?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="ti ti-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-3">{{ $activities->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
