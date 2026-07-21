<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function dashboard(): View
    {
        $transactions = Transaction::latest('transaction_date')->latest()->get();
        $income = (float) $transactions->where('type', 'income')->sum('amount');
        $expense = (float) $transactions->where('type', 'expense')->sum('amount');
        $monthlyExpense = (float) $transactions->where('type', 'expense')
            ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');
        $monthlyFoodSpent = (float) $transactions->where('type', 'expense')
            ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->filter(fn ($item) => mb_strtolower($item->category) === 'makanan')
            ->sum('amount');

        $months = collect(range(5, 0))->map(function (int $offset) use ($transactions) {
            $date = now()->subMonths($offset);
            $items = $transactions->filter(fn ($item) => $item->transaction_date->isSameMonth($date));

            return [
                'label' => $date->translatedFormat('M'),
                'income' => (float) $items->where('type', 'income')->sum('amount'),
                'expense' => (float) $items->where('type', 'expense')->sum('amount'),
            ];
        });

        return view('dashboard', compact('transactions', 'income', 'expense', 'monthlyExpense', 'monthlyFoodSpent', 'months'));
    }

    public function create(): View
    {
        return view('transactions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1', 'max:9999999999999'],
            'transaction_date' => ['required', 'date'],
        ]);

        Transaction::create($validated);

        return to_route('dashboard')->with('success', 'Transaksi berhasil disimpan.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function monthly(Request $request): View
    {
        $requestedMonth = (string) $request->input('bulan', now()->format('Y-m'));
        $month = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $requestedMonth)
            ? Carbon::createFromFormat('!Y-m', $requestedMonth)->startOfMonth()
            : now()->startOfMonth();
        $transactions = Transaction::whereBetween('transaction_date', [$month, $month->copy()->endOfMonth()])
            ->latest('transaction_date')->get();

        $dailyCashFlow = collect(range(1, $month->daysInMonth))->map(function (int $day) use ($month, $transactions) {
            $date = $month->copy()->day($day);
            $items = $transactions->filter(fn ($item) => $item->transaction_date->isSameDay($date));

            return [
                'day' => $day,
                'income' => (float) $items->where('type', 'income')->sum('amount'),
                'expense' => (float) $items->where('type', 'expense')->sum('amount'),
            ];
        });

        return view('reports.monthly', compact('month', 'transactions', 'dailyCashFlow'));
    }

    public function yearly(Request $request): View
    {
        $year = max(2000, min(2100, $request->integer('tahun', (int) now()->year)));
        $transactions = Transaction::whereYear('transaction_date', $year)->get();
        $summary = collect(range(1, 12))->map(function (int $month) use ($transactions) {
            $items = $transactions->filter(fn ($item) => $item->transaction_date->month === $month);

            return [
                'month' => Carbon::create(null, $month)->translatedFormat('F'),
                'income' => (float) $items->where('type', 'income')->sum('amount'),
                'expense' => (float) $items->where('type', 'expense')->sum('amount'),
            ];
        });

        return view('reports.yearly', compact('year', 'summary'));
    }
}
