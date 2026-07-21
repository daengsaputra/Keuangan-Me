@extends('layouts.app')

@section('title', 'Dashboard — Keuangan Me')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp '.number_format($value, 0, ',', '.');
    $balance = $income - $expense;
    $maxChart = max(1, $months->max(fn ($m) => max($m['income'], $m['expense'])));
@endphp
<div class="page-heading">
    <div><p class="eyebrow">RINGKASAN KEUANGAN</p><h1>Selamat datang kembali.</h1><p>Pantau arus kas dan jaga kesehatan finansial Anda.</p></div>
    <a class="primary-button" href="{{ route('transactions.create') }}">＋ Tambah transaksi</a>
</div>

<section class="stats-grid">
    <article class="stat-card featured"><span class="stat-icon">◉</span><p>Saldo saat ini</p><h2>{{ $rupiah($balance) }}</h2><small>Akumulasi seluruh transaksi</small></article>
    <article class="stat-card"><span class="stat-icon income">↗</span><p>Total pemasukan</p><h2>{{ $rupiah($income) }}</h2><small class="positive">Arus dana masuk</small></article>
    <article class="stat-card"><span class="stat-icon expense">↘</span><p>Total pengeluaran</p><h2>{{ $rupiah($expense) }}</h2><small class="negative">{{ $rupiah($monthlyExpense) }} bulan ini</small></article>
</section>

<section class="content-grid">
    <article class="panel chart-panel" data-interactive-chart>
        <div class="panel-heading"><div><h3>Arus kas 6 bulan</h3><p>Klik batang untuk melihat nominalnya</p></div><span class="legend"><i></i>Pemasukan <i></i>Pengeluaran</span></div>
        <div class="bar-chart">
            @foreach ($months as $month)
                <button type="button" class="bar-group interactive-bar-group" data-chart-item data-chart-label="{{ $month['label'] }}" data-income="{{ $month['income'] }}" data-expense="{{ $month['expense'] }}" aria-label="Lihat arus kas bulan {{ $month['label'] }}">
                    <span class="bar-tooltip" role="tooltip"><strong>{{ $month['label'] }}</strong><span class="tooltip-income">Pemasukan: {{ $rupiah($month['income']) }}</span><span class="tooltip-expense">Pengeluaran: {{ $rupiah($month['expense']) }}</span></span>
                    <span class="bars"><span class="bar income-bar" style="height: {{ max(3, ($month['income'] / $maxChart) * 100) }}%"></span><span class="bar expense-bar" style="height: {{ max(3, ($month['expense'] / $maxChart) * 100) }}%"></span></span>
                    <small>{{ $month['label'] }}</small>
                </button>
            @endforeach
        </div>
    </article>
    <article class="panel budget-panel">
        <div class="panel-heading"><div><h3>Kontrol pengeluaran</h3><p>Bulan {{ now()->translatedFormat('F') }}</p></div></div>
        @php $budget = 8000000; $budgetPercent = min(100, ($monthlyExpense / $budget) * 100); @endphp
        <div class="budget-value"><strong>{{ $rupiah($monthlyExpense) }}</strong><span>dari {{ $rupiah($budget) }}</span></div>
        <div class="progress"><span style="width: {{ $budgetPercent }}%"></span></div>
        <p class="budget-note">Tersisa <strong>{{ $rupiah(max(0, $budget - $monthlyExpense)) }}</strong> dari batas bulan ini.</p>
    </article>
</section>

<section class="panel meal-monitor" data-meal-monitor data-month="{{ now()->format('Y-m') }}" data-food-spent="{{ $monthlyFoodSpent }}">
    <div class="panel-heading"><div><h3>Kalkulator uang makan</h3><p>Hitung jatah harian dan pantau sisa uang makan bulan ini.</p></div></div>
    <div class="meal-calculator">
        <div class="meal-input meal-input-grid">
            <label>Total uang makan
                <input type="number" min="0" step="1000" inputmode="numeric" data-meal-budget placeholder="Contoh: 1.500.000">
            </label>
            <label>Jumlah hari
                <input type="number" min="1" max="366" step="1" inputmode="numeric" data-meal-divisor value="{{ now()->daysInMonth }}">
            </label>
            <small>Rumus: total uang makan ÷ jumlah hari. Pengeluaran aktual diambil dari transaksi kategori Makanan bulan ini.</small>
        </div>
        <div class="meal-results">
            <div class="meal-result"><span>Uang makan per hari</span><strong data-daily-budget>Rp 0</strong></div>
            <div class="meal-result"><span>Sudah dipakai bulan ini</span><strong data-food-spent>{{ $rupiah($monthlyFoodSpent) }}</strong></div>
            <div class="meal-result"><span>Sisa uang makan</span><strong data-meal-left>Rp 0</strong></div>
            <div class="meal-result"><span>Sisa untuk tabungan</span><strong class="positive" data-meal-saving>Rp 0</strong></div>
            <div class="meal-result meal-progress"><span>Pemakaian anggaran</span><div class="progress"><span data-meal-progress style="width:0%"></span></div><small data-meal-status>Masukkan total uang makan dan jumlah hari.</small></div>
        </div>
    </div>
</section>

<section class="panel table-panel">
    <div class="panel-heading"><div><h3>Transaksi terbaru</h3><p>Aktivitas keuangan terakhir Anda</p></div><a href="{{ route('reports.monthly') }}">Lihat laporan →</a></div>
    <div class="table-wrap"><table><thead><tr><th>Transaksi</th><th>Kategori</th><th>Tanggal</th><th class="text-right">Nominal</th><th></th></tr></thead><tbody>
    @forelse ($transactions->take(8) as $transaction)
        <tr><td><strong>{{ $transaction->description }}</strong><small>{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</small></td><td><span class="category">{{ $transaction->category }}</span></td><td>{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td><td class="amount {{ $transaction->type }}">{{ $transaction->type === 'income' ? '+' : '-' }}{{ $rupiah($transaction->amount) }}</td><td><form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini?')">@csrf @method('DELETE')<button class="delete-button" aria-label="Hapus">×</button></form></td></tr>
    @empty
        <tr><td colspan="5" class="empty">Belum ada transaksi.</td></tr>
    @endforelse
    </tbody></table></div>
</section>
@endsection
