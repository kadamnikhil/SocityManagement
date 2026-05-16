<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseItem;
use App\Models\ExpensePeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $userId = (int) $request->user()->id;
        $periodQuery = ExpensePeriod::query()
            ->where('user_id', $userId)
            ->withCount('items')
            ->withSum('items as total_amount', 'amount');

        $periods = (clone $periodQuery)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(10);

        $allPeriods = (clone $periodQuery)->get();
        $summary = [
            'cycles' => $allPeriods->count(),
            'items' => (int) $allPeriods->sum('items_count'),
            'total' => (float) $allPeriods->sum('total_amount'),
        ];

        $latestPeriod = (clone $periodQuery)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        $months = $this->months();
        $years = $this->years();

        return view('Admin.Expenses.index', compact('periods', 'months', 'years', 'summary', 'latestPeriod'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = (int) $request->user()->id;
        $data = $request->validate([
            'month' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('expense_periods')->where(fn ($query) => $query
                    ->where('user_id', $userId)
                    ->where('year', (int) $request->input('year'))),
            ],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'month.unique' => 'Expenses for this month and year already exist.',
        ]);

        $period = ExpensePeriod::create([
            'user_id' => $userId,
            'month' => (int) $data['month'],
            'year' => (int) $data['year'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.expenses.show', $period)
            ->with('success', 'Expense month created for '.$period->label.'.');
    }

    public function show(Request $request, ExpensePeriod $expense): View
    {
        $this->authorizePeriod($request, $expense);

        $items = ExpenseItem::query()
            ->where('expense_period_id', $expense->id)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        $categoryTotals = $items
            ->groupBy('category')
            ->map(fn ($rows, $category) => [
                'label' => ExpenseItem::categories()[$category] ?? 'Other expense',
                'count' => $rows->count(),
                'total' => (float) $rows->sum('amount'),
            ])
            ->sortByDesc('total');

        $summary = [
            'items' => $items->count(),
            'total' => (float) $items->sum('amount'),
            'highest' => (float) $items->max('amount'),
        ];

        $categories = ExpenseItem::categories();
        $paymentModes = $this->paymentModes();

        return view('Admin.Expenses.show', compact('expense', 'items', 'categoryTotals', 'summary', 'categories', 'paymentModes'));
    }

    public function storeItem(Request $request, ExpensePeriod $expense): RedirectResponse
    {
        $this->authorizePeriod($request, $expense);

        $data = $request->validate([
            'category' => ['required', Rule::in(array_keys(ExpenseItem::categories()))],
            'title' => ['required', 'string', 'max:160'],
            'payee_name' => ['nullable', 'string', 'max:160'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'expense_date' => ['nullable', 'date'],
            'payment_mode' => ['nullable', 'string', 'max:40'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:1000'],
            'bill_file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ]);

        $item = new ExpenseItem([
            'expense_period_id' => $expense->id,
            'category' => $data['category'],
            'title' => trim($data['title']),
            'payee_name' => $this->nullableTrim($data['payee_name'] ?? null),
            'amount' => $data['amount'],
            'expense_date' => $data['expense_date'] ?? null,
            'payment_mode' => $this->nullableTrim($data['payment_mode'] ?? null),
            'reference_no' => $this->nullableTrim($data['reference_no'] ?? null),
            'note' => $this->nullableTrim($data['note'] ?? null),
        ]);

        if ($request->hasFile('bill_file')) {
            $file = $request->file('bill_file');
            $path = $file->store('expense-bills/'.$expense->user_id.'/'.$expense->id, 'public');
            $item->bill_path = $path;
            $item->bill_original_name = $file->getClientOriginalName();
            $item->bill_mime_type = $file->getMimeType();
            $item->bill_size = (int) $file->getSize();
        }

        $item->save();

        return back()->with('success', 'Expense added.');
    }

    public function destroyItem(Request $request, ExpensePeriod $expense, ExpenseItem $item): RedirectResponse
    {
        $this->authorizePeriod($request, $expense);
        abort_unless((int) $item->expense_period_id === (int) $expense->id, 404);

        $this->deleteBillFile($item);
        $item->delete();

        return back()->with('success', 'Expense deleted.');
    }

    public function destroy(Request $request, ExpensePeriod $expense): RedirectResponse
    {
        $this->authorizePeriod($request, $expense);
        $label = $expense->label;

        DB::transaction(function () use ($expense) {
            $items = ExpenseItem::query()
                ->where('expense_period_id', $expense->id)
                ->get();

            foreach ($items as $item) {
                $this->deleteBillFile($item);
            }

            $expense->delete();
        });

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expenses for '.$label.' deleted.');
    }

    private function authorizePeriod(Request $request, ExpensePeriod $period): void
    {
        abort_unless((int) $period->user_id === (int) $request->user()->id, 404);
    }

    private function deleteBillFile(ExpenseItem $item): void
    {
        if ($item->bill_path) {
            Storage::disk('public')->delete($item->bill_path);
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

    private function paymentModes(): array
    {
        return [
            'cash' => 'Cash',
            'upi' => 'UPI',
            'bank_transfer' => 'Bank transfer',
            'cheque' => 'Cheque',
            'card' => 'Card',
            'other' => 'Other',
        ];
    }
}
