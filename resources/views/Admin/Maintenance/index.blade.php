@extends('layouts.admin')
@section('title') Maintenance @endsection

@section('content')
<style>
    .maint-page {
        --maint-primary: #0f6bff;
        --maint-primary-dark: #0b4bb3;
        --maint-ink: #0f172a;
        --maint-muted: #64748b;
        --maint-border: #e2e8f0;
        --maint-soft: #f8fbff;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
    .maint-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #0f6bff 0%, #0b4bb3 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 1.35rem;
        box-shadow: 0 18px 42px rgba(15, 107, 255, 0.22);
    }
    .maint-hero::after {
        content: "";
        position: absolute;
        right: -4rem;
        bottom: -5rem;
        width: 16rem;
        height: 16rem;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
    }
    .maint-hero-content { position: relative; z-index: 1; }
    .maint-eyebrow { color: rgba(255,255,255,.68) !important; font-size: .72rem; font-weight: 850; text-transform: uppercase; letter-spacing: .08em; margin-bottom: .3rem; }
    .maint-hero h4 { color: #fff !important; font-size: clamp(1.45rem, 2.5vw, 2.25rem); font-weight: 900 !important; letter-spacing: 0; }
    .maint-hero p { color: rgba(255,255,255,.78) !important; max-width: 44rem; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
    .quick-stat {
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: .85rem;
        padding: .8rem .9rem;
        min-width: 8.8rem;
    }
    .quick-stat span { display: block; color: rgba(255,255,255,.68); font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .quick-stat strong { display: block; color: #fff; font-size: 1.45rem; line-height: 1; margin-top: .25rem; }
    .maint-card { border: 0; border-radius: 1rem; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08); overflow: hidden; }
    .maint-card h5 { color: #0f172a !important; font-size: 1rem; font-weight: 900 !important; }
    .maint-card .text-muted { color: #64748b !important; }
    .create-card { margin-top: -1.25rem; position: sticky; top: 84px; z-index: 1; }
    .create-head { padding: 1rem 1.1rem; background: linear-gradient(180deg, #f8fbff, #fff); border-bottom: 1px solid var(--maint-border); }
    .create-icon { width: 2.5rem; height: 2.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: .75rem; background: #dbeafe; color: #1d4ed8; font-size: 1.25rem; }
    .maint-label { font-size: .75rem; font-weight: 850; color: #334155; margin-bottom: .35rem; }
    .maint-control { border-color: #dbe4f0; border-radius: .65rem; min-height: 44px; }
    .maint-control:focus { border-color: #93c5fd; box-shadow: 0 0 0 .2rem rgba(37,99,235,.12); }
    .helper-box { border: 1px dashed #cbd5e1; border-radius: .75rem; padding: .8rem; background: #f8fafc; color: var(--maint-muted); font-size: .78rem; line-height: 1.45; }
    .history-card .card-body { padding: 1.1rem; }
    .history-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; margin-bottom: 1rem; }
    .period-list { display: grid; gap: .85rem; }
    .period-card {
        border: 1px solid #e8eef7;
        border-radius: .9rem;
        padding: 1rem;
        background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
        transition: border-color .15s ease, box-shadow .15s ease, transform .12s ease;
    }
    .period-card:hover { border-color: #bfdbfe; box-shadow: 0 10px 26px rgba(37, 99, 235, .09); transform: translateY(-1px); }
    .period-main { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 1rem; align-items: start; }
    .period-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
    .period-title { font-weight: 900; color: var(--maint-ink); font-size: 1.05rem; line-height: 1.15; }
    .period-badge { border-radius: 999px; padding: .28rem .55rem; background: #eff6ff; color: #1d4ed8; font-size: .72rem; font-weight: 850; }
    .period-sub { font-size: .78rem; color: var(--maint-muted); margin-top: .35rem; }
    .period-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .5rem; margin-top: .85rem; }
    .period-stat { border-radius: .7rem; padding: .65rem; background: #f1f5f9; color: #334155; }
    .period-stat span { display: block; font-size: .68rem; font-weight: 850; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
    .period-stat strong { display: block; margin-top: .15rem; font-size: 1.05rem; line-height: 1; color: #0f172a; }
    .period-stat--paid { background: #ecfdf3; }
    .period-stat--paid strong { color: #047857; }
    .period-stat--unpaid { background: #fff7ed; }
    .period-stat--unpaid strong { color: #c2410c; }
    .period-progress { height: .55rem; border-radius: 999px; background: #e2e8f0; overflow: hidden; margin-top: .85rem; }
    .period-progress span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #16a34a, #22c55e); }
    .period-actions { display: flex; flex-direction: column; gap: .45rem; min-width: 7.5rem; }
    .period-actions .btn { font-weight: 800; border-radius: .65rem; }
    .empty-state {
        min-height: 24rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #cbd5e1;
        background: radial-gradient(circle at 50% 10%, rgba(37,99,235,.08), transparent 32%), #f8fafc;
        border-radius: .9rem;
        padding: 2rem 1rem;
        text-align: center;
        color: var(--maint-muted);
    }
    .empty-icon { width: 4rem; height: 4rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 1rem; background: #dbeafe; color: #1d4ed8; font-size: 2rem; margin-bottom: .9rem; }
    @media (max-width: 991.98px) { .create-card { margin-top: 0; position: static; } }
    @media (max-width: 767.98px) {
        .period-main { grid-template-columns: 1fr; }
        .period-actions { flex-direction: row; min-width: 0; }
        .period-actions .btn, .period-actions form { flex: 1 1 auto; }
        .period-stats { grid-template-columns: 1fr; }
        .quick-stat { flex: 1 1 9rem; }
    }
</style>

<section class="maint-page">
    <div class="maint-hero mb-3">
        <div class="maint-hero-content">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="maint-eyebrow">Society collections</div>
                    <h4 class="fw-bold mb-2">Maintenance Dashboard</h4>
                    <p class="mb-0">Create monthly collection cycles, watch paid/unpaid progress, and keep transaction proof organized for every flat.</p>
                </div>
                <div class="hero-actions">
                    <a href="{{ route('admin.dashboard.index') }}" class="btn btn-light btn-sm fw-semibold">
                        <i class="ti ti-layout-dashboard me-1"></i> Dashboard
                    </a>
                    @if($latestPeriod)
                        <a href="{{ route('admin.maintenance.show', $latestPeriod) }}" class="btn btn-warning btn-sm fw-semibold">
                            <i class="ti ti-arrow-right me-1"></i> Latest Cycle
                        </a>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <div class="quick-stat"><span>Cycles</span><strong>{{ $summary['cycles'] ?? 0 }}</strong></div>
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
        <div class="alert alert-danger border-0 rounded-3 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row g-3 align-items-start">
        <div class="col-lg-4 col-xl-3">
            <div class="card maint-card create-card">
                <div class="create-head">
                    <div class="d-flex align-items-center gap-3">
                        <span class="create-icon"><i class="ti ti-calendar-plus"></i></span>
                        <div>
                            <h5 class="fw-bold mb-1">New Cycle</h5>
                            <p class="text-muted small mb-0">Month-wise billing for all flats.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-xl-4">
                    <form method="POST" action="{{ route('admin.maintenance.store') }}" autocomplete="off">
                        @csrf
                        <div class="row g-2">
                            <div class="col-7 col-lg-12">
                                <label for="month" class="maint-label">Month</label>
                                <select id="month" name="month" class="form-select maint-control @error('month') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('month') ? '' : 'selected' }}>Select month</option>
                                    @foreach($months as $number => $name)
                                        <option value="{{ $number }}" @selected((int) old('month', now()->month) === (int) $number)>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-5 col-lg-12">
                                <label for="year" class="maint-label">Year</label>
                                <select id="year" name="year" class="form-select maint-control @error('year') is-invalid @enderror" required>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" @selected((int) old('year', now()->year) === (int) $year)>{{ $year }}</option>
                                    @endforeach
                                </select>
                                @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="amount" class="maint-label">Amount per flat <span class="text-muted fw-normal">(optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rs.</span>
                                <input type="number" step="0.01" min="0" id="amount" name="amount" value="{{ old('amount') }}" class="form-control maint-control border-start-0 @error('amount') is-invalid @enderror" placeholder="2500">
                            </div>
                            @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mt-3">
                            <label for="notes" class="maint-label">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="3" class="form-control maint-control @error('notes') is-invalid @enderror" placeholder="Example: May maintenance with water charges">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- <div class="helper-box mt-3">
                            <i class="ti ti-info-circle me-1"></i>
                            Saving creates unpaid entries for every current flat. You can upload proof while marking each flat as paid.
                        </div> --}}

                        <button type="submit" class="btn btn-primary w-100 fw-bold mt-3 py-2">
                            <i class="ti ti-device-floppy me-1"></i> Create Cycle
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9">
            <div class="card maint-card history-card">
                <div class="card-body">
                    <div class="history-toolbar">
                        <div>
                            <h5 class="fw-bold mb-1">Maintenance History</h5>
                            <p class="text-muted small mb-0">Open a cycle to collect payments, upload proof, or view unpaid flats.</p>
                        </div>
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                            {{ $periods->total() }} cycle(s)
                        </span>
                    </div>

                    @if($periods->isEmpty())
                        <div class="empty-state">
                            <div>
                                <span class="empty-icon"><i class="ti ti-receipt"></i></span>
                                <h5 class="fw-bold text-dark mb-2">No maintenance cycle yet</h5>
                                <p class="mb-0">Create your first monthly cycle from the panel on the left. It will appear here with paid and unpaid tracking.</p>
                            </div>
                        </div>
                    @else
                        <div class="period-list">
                            @foreach($periods as $period)
                                @php
                                    $total = max((int) $period->total_flats, 0);
                                    $paid = (int) $period->paid_flats;
                                    $unpaid = (int) $period->unpaid_flats;
                                    $rate = $total > 0 ? round(($paid / $total) * 100) : 0;
                                @endphp
                                <article class="period-card">
                                    <div class="period-main">
                                        <div>
                                            <div class="period-title-row">
                                                <div class="period-title">{{ $period->label }}</div>
                                                <span class="period-badge">{{ $rate }}% collected</span>
                                            </div>
                                            <div class="period-sub">
                                                @if($period->amount !== null)
                                                    Rs. {{ number_format((float) $period->amount, 2) }} per flat
                                                @else
                                                    Amount not set
                                                @endif
                                                @if($period->notes)
                                                    · {{ \Illuminate\Support\Str::limit($period->notes, 90) }}
                                                @endif
                                            </div>

                                            <div class="period-stats">
                                                <div class="period-stat"><span>Total flats</span><strong>{{ $total }}</strong></div>
                                                <div class="period-stat period-stat--paid"><span>Paid</span><strong>{{ $paid }}</strong></div>
                                                <div class="period-stat period-stat--unpaid"><span>Unpaid</span><strong>{{ $unpaid }}</strong></div>
                                            </div>
                                            <div class="period-progress" aria-label="{{ $rate }} percent collected">
                                                <span style="width: {{ $rate }}%;"></span>
                                            </div>
                                        </div>

                                        <div class="period-actions">
                                            <a href="{{ route('admin.maintenance.show', $period) }}" class="btn btn-primary btn-sm text-nowrap">
                                                Open <i class="ti ti-arrow-right ms-1"></i>
                                            </a>
                                            <a href="{{ route('admin.maintenance.show', [$period, 'status' => 'unpaid']) }}" class="btn btn-outline-warning btn-sm text-nowrap">
                                                Unpaid
                                            </a>
                                            <a href="{{ route('admin.maintenance.pdf-list', [$period, 'paid']) }}" class="btn btn-outline-success btn-sm text-nowrap">
                                                <i class="ti ti-file-download me-1"></i> Paid PDF
                                            </a>
                                            <a href="{{ route('admin.maintenance.pdf-list', [$period, 'unpaid']) }}" class="btn btn-outline-secondary btn-sm text-nowrap">
                                                <i class="ti ti-file-download me-1"></i> Unpaid PDF
                                            </a>
                                            <form method="POST" action="{{ route('admin.maintenance.destroy', $period) }}" onsubmit="return confirm('Delete all maintenance data for {{ $period->label }}? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 text-nowrap">
                                                    <i class="ti ti-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $periods->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
