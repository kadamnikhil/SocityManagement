@extends('layouts.admin')
@section('title') Expense Details @endsection
@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
    .exp-detail { --exp-primary:#7c3aed; --exp-ink:#0f172a; --exp-muted:#64748b; --exp-border:#e2e8f0; font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif; }
    .exp-top { background:linear-gradient(135deg,#7c3aed,#0f6bff); color:#fff; border-radius:.9rem; padding:1.1rem 1.25rem; box-shadow:0 14px 32px rgba(91,33,182,.2); }
    .exp-top p { color:rgba(255,255,255,.78); }
    .stat-card { border:0; border-radius:.75rem; box-shadow:0 8px 22px rgba(15,23,42,.07); }
    .stat-label { color:#64748b; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.04em; }
    .stat-value { color:#0f172a; font-weight:900; font-size:1.35rem; line-height:1; }
    .exp-card { border:0; border-radius:.9rem; box-shadow:0 10px 28px rgba(15,23,42,.08); overflow:hidden; }
    .exp-card .card-header { background:linear-gradient(180deg,#faf7ff,#fff); border-bottom:1px solid var(--exp-border); padding:1rem 1.1rem; }
    .exp-label { font-size:.74rem; font-weight:850; color:#334155; margin-bottom:.32rem; }
    .exp-control { border-color:#dbe4f0; border-radius:.6rem; min-height:42px; }
    .exp-control:focus { border-color:#c4b5fd; box-shadow:0 0 0 .2rem rgba(124,58,237,.12); }
    .category-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(12rem,1fr)); gap:.65rem; }
    .category-card { border:1px solid #e8eef7; border-radius:.75rem; padding:.85rem; background:#fbfdff; }
    .category-card span { display:block; color:#64748b; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.04em; }
    .category-card strong { display:block; color:#0f172a; font-size:1.08rem; margin-top:.2rem; }
    .expense-list { display:grid; gap:.75rem; }
    .expense-row { border:1px solid #e8eef7; border-radius:.9rem; background:linear-gradient(180deg,#fff,#fbfdff); overflow:hidden; }
    .expense-row-main { display:grid; grid-template-columns:2.65rem minmax(0,1fr) auto; gap:.75rem; align-items:center; padding:.85rem; }
    .expense-icon { width:2.45rem; height:2.45rem; display:flex; align-items:center; justify-content:center; border-radius:.75rem; background:#f3e8ff; color:#6d28d9; font-size:1.25rem; }
    .expense-title { font-weight:950; color:#0f172a; line-height:1.2; }
    .expense-category { display:inline-flex; align-items:center; gap:.25rem; border-radius:999px; padding:.2rem .5rem; background:#f8fafc; border:1px solid #e2e8f0; color:#475569; font-size:.7rem; font-weight:850; margin-top:.3rem; }
    .expense-chips { display:flex; flex-wrap:wrap; gap:.35rem; margin-top:.45rem; }
    .expense-chip { display:inline-flex; align-items:center; gap:.25rem; border-radius:999px; padding:.22rem .48rem; background:#f1f5f9; color:#64748b; font-size:.7rem; font-weight:750; max-width:100%; }
    .expense-chip span { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .expense-money { text-align:right; min-width:8.5rem; }
    .amount-pill { display:inline-flex; align-items:center; border-radius:999px; background:#f3e8ff; color:#6d28d9; padding:.42rem .7rem; font-weight:950; white-space:nowrap; }
    .expense-date-mini { display:block; color:#94a3b8; font-size:.72rem; font-weight:800; margin-top:.35rem; }
    .expense-detail-panel { border-top:1px solid #eef2f7; background:#f8fafc; padding:.75rem .85rem; }
    .expense-note { color:#475569; font-size:.78rem; line-height:1.5; margin:0 0 .5rem; }
    .bill-link { display:inline-flex; align-items:center; gap:.25rem; color:#1d4ed8; font-size:.76rem; font-weight:850; text-decoration:none; }
    .bill-link:hover { text-decoration:underline; }
    .expense-actions { display:flex; flex-wrap:wrap; gap:.45rem; align-items:center; justify-content:flex-end; margin-top:.55rem; }
    .expense-actions .btn { border-radius:.55rem; font-size:.74rem; font-weight:850; }
    .empty-state { border:1px dashed #cbd5e1; background:#f8fafc; border-radius:.8rem; padding:1.25rem; text-align:center; color:#64748b; }
    @media (max-width:767.98px) { .expense-row-main { grid-template-columns:2.65rem minmax(0,1fr); } .expense-money { grid-column:1 / -1; text-align:left; min-width:0; padding-left:3.4rem; } .expense-actions { justify-content:stretch; } .expense-actions .btn, .expense-actions form { flex:1 1 auto; } }
</style>

<section class="exp-detail">
    <div class="exp-top mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="small text-white-50 fw-bold text-uppercase">Expense Details</div>
                <h4 class="fw-bold mb-1">{{ $expense->label }}</h4>
                <p class="mb-0">{{ $expense->notes ?: 'Add every society expense for this month with bills or proof.' }}</p>
            </div>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-light btn-sm fw-semibold"><i class="ti ti-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success border-0 rounded-2">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger border-0 rounded-2">{{ $errors->first() }}</div>@endif

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card stat-card"><div class="card-body p-3"><div class="stat-label">Entries</div><div class="stat-value">{{ $summary['items'] }}</div></div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body p-3"><div class="stat-label">Total spent</div><div class="stat-value text-primary">Rs. {{ number_format((float) $summary['total'], 2) }}</div></div></div></div>
        <div class="col-md-4"><div class="card stat-card"><div class="card-body p-3"><div class="stat-label">Highest expense</div><div class="stat-value text-warning">Rs. {{ number_format((float) $summary['highest'], 2) }}</div></div></div></div>
    </div>

    <div class="row g-3 align-items-start">
        <div class="col-xl-4">
            <div class="card exp-card">
                <div class="card-header"><h5 class="fw-bold mb-0"><i class="ti ti-plus me-1 text-primary"></i>Add Expense</h5></div>
                <div class="card-body p-3 p-xl-4">
                    <form method="POST" action="{{ route('admin.expenses.items.store', $expense) }}" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="mb-3">
                            <label class="exp-label" for="category">Expense type</label>
                            <select id="category" name="category" class="form-select exp-control" required>
                                @foreach($categories as $value => $label)
                                    <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="exp-label" for="title">Title</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control exp-control" placeholder="Example: Watchman salary" required>
                        </div>
                        <div class="row g-2">
                            <div class="col-7">
                                <label class="exp-label" for="amount">Amount</label>
                                <input type="number" step="0.01" min="0.01" id="amount" name="amount" value="{{ old('amount') }}" class="form-control exp-control" placeholder="0.00" required>
                            </div>
                            <div class="col-5">
                                <label class="exp-label" for="expense_date">Date</label>
                                <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" class="form-control exp-control">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="exp-label" for="payee_name">Payee / vendor</label>
                            <input type="text" id="payee_name" name="payee_name" value="{{ old('payee_name') }}" class="form-control exp-control" placeholder="Staff name, vendor, biller">
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <label class="exp-label" for="payment_mode">Payment mode</label>
                                <select id="payment_mode" name="payment_mode" class="form-select exp-control">
                                    <option value="">Select</option>
                                    @foreach($paymentModes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('payment_mode') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="exp-label" for="reference_no">Reference no.</label>
                                <input type="text" id="reference_no" name="reference_no" value="{{ old('reference_no') }}" class="form-control exp-control" placeholder="Txn / bill no.">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="exp-label" for="note">Note</label>
                            <textarea id="note" name="note" rows="3" class="form-control exp-control" placeholder="Add useful details">{{ old('note') }}</textarea>
                        </div>
                        <div class="mt-3">
                            <label class="exp-label" for="bill_file">Upload bill / proof</label>
                            <input type="file" id="bill_file" name="bill_file" class="form-control exp-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold mt-3"><i class="ti ti-device-floppy me-1"></i> Save Expense</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card exp-card mb-3">
                <div class="card-header"><h5 class="fw-bold mb-0"><i class="ti ti-chart-pie me-1 text-primary"></i>Category Summary</h5></div>
                <div class="card-body p-3">
                    @if($categoryTotals->isEmpty())
                        <div class="empty-state">No category totals yet.</div>
                    @else
                        <div class="category-grid">
                            @foreach($categoryTotals as $row)
                                <div class="category-card"><span>{{ $row['label'] }} · {{ $row['count'] }} item(s)</span><strong>Rs. {{ number_format((float) $row['total'], 2) }}</strong></div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card exp-card">
                <div class="card-header"><h5 class="fw-bold mb-0"><i class="ti ti-list-details me-1 text-primary"></i>Expense Entries</h5></div>
                <div class="card-body p-3">
                    @if($items->isEmpty())
                        <div class="empty-state">No expenses added yet. Add salary, bill, repair, programme, or other expense from the form.</div>
                    @else
                        <div class="expense-list">
                            @foreach($items as $item)
                                @php
                                    $detailId = 'expense-entry-'.$item->id;
                                    $categoryIcons = [
                                        'staff_salary' => 'ti-users',
                                        'programme' => 'ti-confetti',
                                        'light_bill' => 'ti-bulb',
                                        'repair' => 'ti-tools',
                                        'cleaning' => 'ti-spray',
                                        'security' => 'ti-shield-lock',
                                        'water' => 'ti-droplet',
                                        'other' => 'ti-receipt',
                                    ];
                                    $icon = $categoryIcons[$item->category] ?? 'ti-receipt';
                                    $hasDetails = $item->note || $item->bill_path || $item->reference_no;
                                @endphp
                                <article class="expense-row">
                                    <div class="expense-row-main">
                                        <div class="expense-icon"><i class="ti {{ $icon }}"></i></div>
                                        <div class="min-w-0">
                                            <div class="expense-title">{{ $item->title }}</div>
                                            <div class="expense-category"><i class="ti ti-tag"></i> {{ $item->category_label }}</div>
                                            <div class="expense-chips">
                                                @if($item->payee_name)<span class="expense-chip"><i class="ti ti-user-dollar"></i><span>{{ $item->payee_name }}</span></span>@endif
                                                @if($item->payment_mode)<span class="expense-chip"><i class="ti ti-wallet"></i><span>{{ $paymentModes[$item->payment_mode] ?? $item->payment_mode }}</span></span>@endif
                                                @if($item->reference_no)<span class="expense-chip"><i class="ti ti-hash"></i><span>{{ $item->reference_no }}</span></span>@endif
                                                @if($item->bill_path)<span class="expense-chip"><i class="ti ti-paperclip"></i><span>Bill attached</span></span>@endif
                                            </div>
                                        </div>
                                        <div class="expense-money">
                                            <span class="amount-pill">Rs. {{ number_format((float) $item->amount, 2) }}</span>
                                            @if($item->expense_date)<span class="expense-date-mini">{{ $item->expense_date->format('d M Y') }}</span>@endif
                                        </div>
                                    </div>
                                    <div class="expense-detail-panel">
                                        @if($item->note)
                                            <p class="expense-note">{{ $item->note }}</p>
                                        @endif
                                        @if($item->bill_path)
                                            <a href="{{ Storage::disk('public')->url($item->bill_path) }}" target="_blank" class="bill-link"><i class="ti ti-paperclip"></i>{{ $item->bill_original_name ?: 'View bill' }}</a>
                                        @elseif(!$item->note)
                                            <span class="text-muted small">No note or bill attached.</span>
                                        @endif
                                        <div class="expense-actions">
                                            <form method="POST" action="{{ route('admin.expenses.items.destroy', [$expense, $item]) }}" onsubmit="return confirm('Delete this expense entry?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="ti ti-trash me-1"></i>Delete entry</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
