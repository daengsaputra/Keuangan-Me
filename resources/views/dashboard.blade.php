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
    <article class="panel chart-panel">
        <div class="panel-heading"><div><h3>Arus kas 6 bulan</h3><p>Perbandingan pemasukan dan pengeluaran</p></div><span class="legend"><i></i>Pemasukan <i></i>Pengeluaran</span></div>
        <div class="bar-chart">
            @foreach ($months as $month)
                <div class="bar-group">
                    <div class="bars"><span class="bar income-bar" style="height: {{ max(3, ($month['income'] / $maxChart) * 100) }}%"></span><span class="bar expense-bar" style="height: {{ max(3, ($month['expense'] / $maxChart) * 100) }}%"></span></div>
                    <small>{{ $month['label'] }}</small>
                </div>
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
