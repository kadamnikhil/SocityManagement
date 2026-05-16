@extends('layouts.admin')
@section('title') Expenses @endsection

@section('content')
<style>
    .exp-page { --exp-primary:#0f6bff; --exp-dark:#0b4bb3; --exp-ink:#0f172a; --exp-muted:#64748b; --exp-border:#e2e8f0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
    .exp-hero { position:relative; overflow:hidden; border-radius:1rem; padding:1.35rem; color:#fff; background:linear-gradient(135deg,#0f6bff,#0b4bb3); box-shadow:0 18px 42px rgba(15,107,255,.22); }
    .exp-hero::after { content:""; position:absolute; right:-4rem; bottom:-5rem; width:16rem; height:16rem; border-radius:50%; background:rgba(255,255,255,.1); }
    .exp-hero-content { position:relative; z-index:1; }
    .exp-eyebrow { color:rgba(255,255,255,.68) !important; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.3rem; }
    .exp-hero h4 { color:#fff !important; font-size:clamp(1.45rem,2.5vw,2.25rem); font-weight:900 !important; letter-spacing:0; }
    .exp-hero p { color:rgba(255,255,255,.78) !important; max-width:44rem; }
    .quick-stat { background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.18); border-radius:.85rem; padding:.8rem .9rem; min-width:8.8rem; }
    .quick-stat span { display:block; color:rgba(255,255,255,.68); font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
    .quick-stat strong { display:block; color:#fff; font-size:1.45rem; line-height:1; margin-top:.25rem; }
    .exp-card { border:0; border-radius:1rem; box-shadow:0 12px 30px rgba(15,23,42,.08); overflow:hidden; }
    .exp-card h5 { color:#0f172a !important; font-size:1rem; font-weight:900 !important; }
    .exp-card .text-muted { color:#64748b !important; }
    .create-card { margin-top:-1.25rem; position:sticky; top:84px; z-index:1; }
    .create-head { padding:1rem 1.1rem; background:linear-gradient(180deg,#f8fbff,#fff); border-bottom:1px solid var(--exp-border); }
    .create-icon { width:2.5rem; height:2.5rem; display:inline-flex; align-items:center; justify-content:center; border-radius:.75rem; background:#dbeafe; color:#1d4ed8; font-size:1.25rem; }
    .exp-label { font-size:.75rem; font-weight:850; color:#334155; margin-bottom:.35rem; }
    .exp-control { border-color:#dbe4f0; border-radius:.65rem; min-height:44px; }
    .exp-control:focus { border-color:#93c5fd; box-shadow:0 0 0 .2rem rgba(37,99,235,.12); }
    .helper-box { border:1px dashed #cbd5e1; border-radius:.75rem; padding:.8rem; background:#f8fafc; color:var(--exp-muted); font-size:.78rem; line-height:1.45; }
    .period-list { display:grid; gap:.85rem; }
    .period-card { border:1px solid #e8eef7; border-radius:.9rem; padding:1rem; background:linear-gradient(180deg,#fff 0%,#fbfdff 100%); transition:border-color .15s ease, box-shadow .15s ease, transform .12s ease; }
    .period-card:hover { border-color:#bfdbfe; box-shadow:0 10px 26px rgba(37,99,235,.09); transform:translateY(-1px); }
    .period-main { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:1rem; align-items:start; }
    .period-title-row { display:flex; flex-wrap:wrap; align-items:center; gap:.5rem; }
    .period-title { font-weight:900; color:var(--exp-ink); font-size:1.05rem; line-height:1.15; }
    .period-badge { border-radius:999px; padding:.28rem .55rem; background:#eff6ff; color:#1d4ed8; font-size:.72rem; font-weight:850; }
    .period-sub { font-size:.78rem; color:var(--exp-muted); margin-top:.35rem; }
    .period-stats { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.5rem; margin-top:.85rem; }
    .period-stat { border-radius:.7rem; padding:.65rem; background:#f1f5f9; color:#334155; }
    .period-stat span { display:block; font-size:.68rem; font-weight:850; text-transform:uppercase; letter-spacing:.04em; color:#64748b; }
    .period-stat strong { display:block; margin-top:.15rem; font-size:1.05rem; line-height:1; color:#0f172a; }
    .period-actions { display:flex; flex-direction:column; gap:.45rem; min-width:7.5rem; }
    .period-actions .btn { font-weight:800; border-radius:.65rem; }
    .empty-state { min-height:24rem; display:flex; align-items:center; justify-content:center; border:1px dashed #cbd5e1; background:radial-gradient(circle at 50% 10%,rgba(37,99,235,.08),transparent 32%),#f8fafc; border-radius:.9rem; padding:2rem 1rem; text-align:center; color:var(--exp-muted); }
    .empty-icon { width:4rem; height:4rem; display:inline-flex; align-items:center; justify-content:center; border-radius:1rem; background:#dbeafe; color:#1d4ed8; font-size:2rem; margin-bottom:.9rem; }
    @media (max-width:991.98px) { .create-card { margin-top:0; position:static; } }
    @media (max-width:767.98px) { .period-main { grid-template-columns:1fr; } .period-actions { flex-direction:row; min-width:0; } .period-actions .btn, .period-actions form { flex:1 1 auto; } .quick-stat { flex:1 1 9rem; } }
</style>

<section class="exp-page">
    <div class="exp-hero mb-3">
        <div class="exp-hero-content">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="exp-eyebrow">Society spending</div>
                    <h4 class="fw-bold mb-2">Expenses Dashboard</h4>
                    <p class="mb-0">Track staff salaries, light bills, programmes, repairs, cleaning, security, and every society expense month by month.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.dashboard.index') }}" class="btn btn-light btn-sm fw-semibold"><i class="ti ti-layout-dashboard me-1"></i> Dashboard</a>
                    @if($latestPeriod)
                        <a href="{{ route('admin.expenses.show', $latestPeriod) }}" class="btn btn-warning btn-sm fw-semibold"><i class="ti ti-arrow-right me-1"></i> Latest Month</a>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <div class="quick-stat"><span>Months</span><strong>{{ $summary['cycles'] ?? 0 }}</strong></div>
                <div class="quick-stat"><span>Entries</span><strong>{{ $summary['items'] ?? 0 }}</strong></div>
                <div class="quick-stat"><span>Total spent</span><strong>Rs. {{ number_format((float) ($summary['total'] ?? 0), 0) }}</strong></div>
            </div>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success border-0 rounded-3 shadow-sm">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger border-0 rounded-3 shadow-sm">{{ $errors->first() }}</div>@endif

    <div class="row g-3 align-items-start">
        <div class="col-lg-4 col-xl-3">
            <div class="card exp-card create-card">
                <div class="create-head">
                    <div class="d-flex align-items-center gap-3">
                        <span class="create-icon"><i class="ti ti-calendar-dollar"></i></span>
                        <div>
                            <h5 class="fw-bold mb-1">New Month</h5>
                            <p class="text-muted small mb-0">Create monthly expense book.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-xl-4">
                    <form method="POST" action="{{ route('admin.expenses.store') }}" autocomplete="off">
                        @csrf
                        <div class="row g-2">
                            <div class="col-7 col-lg-12">
                                <label for="month" class="exp-label">Month</label>
                                <select id="month" name="month" class="form-select exp-control @error('month') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('month') ? '' : 'selected' }}>Select month</option>
                                    @foreach($months as $number => $name)
                                        <option value="{{ $number }}" @selected((int) old('month', now()->month) === (int) $number)>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-5 col-lg-12">
                                <label for="year" class="exp-label">Year</label>
                                <select id="year" name="year" class="form-select exp-control @error('year') is-invalid @enderror" required>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" @selected((int) old('year', now()->year) === (int) $year)>{{ $year }}</option>
                                    @endforeach
                                </select>
                                @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="notes" class="exp-label">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="3" class="form-control exp-control @error('notes') is-invalid @enderror" placeholder="Example: May monthly society expenses">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- <div class="helper-box mt-3"><i class="ti ti-info-circle me-1"></i>After creating the month, add salary, light bill, repairs, event/programme cost, and other expenses inside it.</div> --}}
                        <button type="submit" class="btn btn-primary w-100 fw-bold mt-3 py-2"><i class="ti ti-device-floppy me-1"></i> Create Expense Month</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9">
            <div class="card exp-card">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Expense History</h5>
                            <p class="text-muted small mb-0">Open any month to add and review expense entries.</p>
                        </div>
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">{{ $periods->total() }} month(s)</span>
                    </div>

                    @if($periods->isEmpty())
                        <div class="empty-state"><div><span class="empty-icon"><i class="ti ti-report-money"></i></span><h5 class="fw-bold text-dark mb-2">No expense month yet</h5><p class="mb-0">Create a month from the panel on the left, then add your society expenses.</p></div></div>
                    @else
                        <div class="period-list">
                            @foreach($periods as $period)
                                <article class="period-card">
                                    <div class="period-main">
                                        <div>
                                            <div class="period-title-row">
                                                <div class="period-title">{{ $period->label }}</div>
                                                <span class="period-badge">{{ $period->items_count }} entries</span>
                                            </div>
                                            <div class="period-sub">{{ $period->notes ? \Illuminate\Support\Str::limit($period->notes, 110) : 'No note added' }}</div>
                                            <div class="period-stats">
                                                <div class="period-stat"><span>Expenses</span><strong>{{ $period->items_count }}</strong></div>
                                                <div class="period-stat"><span>Total spent</span><strong>Rs. {{ number_format((float) ($period->total_amount ?? 0), 2) }}</strong></div>
                                            </div>
                                        </div>
                                        <div class="period-actions">
                                            <a href="{{ route('admin.expenses.show', $period) }}" class="btn btn-primary btn-sm text-nowrap">Open <i class="ti ti-arrow-right ms-1"></i></a>
                                            <form method="POST" action="{{ route('admin.expenses.destroy', $period) }}" onsubmit="return confirm('Delete all expenses for {{ $period->label }}? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 text-nowrap"><i class="ti ti-trash me-1"></i> Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div class="mt-3">{{ $periods->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
