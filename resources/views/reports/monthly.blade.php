@extends('layouts.app')

@section('title', 'Laporan Bulanan — Keuangan Me')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp '.number_format($value, 0, ',', '.');
    $income = (float) $transactions->where('type', 'income')->sum('amount');
    $expense = (float) $transactions->where('type', 'expense')->sum('amount');
    $maxDailyCashFlow = max(1, $dailyCashFlow->max(fn ($day) => max($day['income'], $day['expense'])));
@endphp

<div class="page-heading">
    <div><p class="eyebrow">LAPORAN BULANAN</p><h1>{{ $month->translatedFormat('F Y') }}</h1><p>Ringkasan lengkap aktivitas keuangan dalam satu bulan.</p></div>
    <form class="period-form"><input type="month" name="bulan" value="{{ $month->format('Y-m') }}"><button class="secondary-button">Tampilkan</button></form>
</div>

<section class="stats-grid">
    <article class="stat-card featured"><p>Arus kas bersih</p><h2>{{ $rupiah($income-$expense) }}</h2><small>Selisih pemasukan dan pengeluaran</small></article>
    <article class="stat-card"><p>Pemasukan</p><h2>{{ $rupiah($income) }}</h2><small class="positive">{{ $transactions->where('type','income')->count() }} transaksi</small></article>
    <article class="stat-card"><p>Pengeluaran</p><h2>{{ $rupiah($expense) }}</h2><small class="negative">{{ $transactions->where('type','expense')->count() }} transaksi</small></article>
</section>

<section class="panel monthly-cash-flow-panel" data-interactive-chart>
    <div class="panel-heading">
        <div><h3>Arus kas bulanan</h3><p>Klik batang untuk melihat nilai pemasukan dan pengeluaran harian.</p></div>
        <span class="legend"><i></i>Pemasukan <i></i>Pengeluaran</span>
    </div>
    <div class="daily-chart-scroll">
        <div class="bar-chart daily-bar-chart">
            @foreach ($dailyCashFlow as $day)
                <button type="button" class="bar-group interactive-bar-group" data-chart-item data-chart-label="Tanggal {{ $day['day'] }}" data-income="{{ $day['income'] }}" data-expense="{{ $day['expense'] }}" aria-label="Lihat arus kas tanggal {{ $day['day'] }}">
                    <span class="bar-tooltip" role="tooltip"><strong>Tanggal {{ $day['day'] }}</strong><span class="tooltip-income">Pemasukan: {{ $rupiah($day['income']) }}</span><span class="tooltip-expense">Pengeluaran: {{ $rupiah($day['expense']) }}</span></span>
                    <span class="bars">
                        <span class="bar income-bar" style="height: {{ $day['income'] > 0 ? max(3, ($day['income'] / $maxDailyCashFlow) * 100) : 0 }}%"></span>
                        <span class="bar expense-bar" style="height: {{ $day['expense'] > 0 ? max(3, ($day['expense'] / $maxDailyCashFlow) * 100) : 0 }}%"></span>
                    </span>
                    <small>{{ $day['day'] }}</small>
                </button>
            @endforeach
        </div>
    </div>
</section>

<section class="panel table-panel">
    <div class="panel-heading"><div><h3>Rincian transaksi</h3><p>{{ $transactions->count() }} aktivitas tercatat</p></div></div>
    <div class="table-wrap"><table><thead><tr><th>Transaksi</th><th>Kategori</th><th>Tanggal</th><th class="text-right">Nominal</th></tr></thead><tbody>
    @forelse($transactions as $transaction)
        <tr><td><strong>{{ $transaction->description }}</strong><small>{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</small></td><td><span class="category">{{ $transaction->category }}</span></td><td>{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td><td class="amount {{ $transaction->type }}">{{ $transaction->type === 'income' ? '+' : '-' }}{{ $rupiah($transaction->amount) }}</td></tr>
    @empty
        <tr><td colspan="4" class="empty">Belum ada transaksi pada bulan ini.</td></tr>
    @endforelse
    </tbody></table></div>
</section>
@endsection
