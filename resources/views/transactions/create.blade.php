@extends('layouts.app')

@section('title', 'Tambah Transaksi - Keuangan Me')

@section('content')
<section class="transaction-stage">
    <div class="transaction-card">
        <div class="transaction-heading">
            <p class="eyebrow">TRANSAKSI BARU</p>
            <h1>Catat transaksi.</h1>
            <p>Simpan pemasukan dan pengeluaran Anda dengan rapi.</p>
        </div>

        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf

            <div class="type-switch">
                <label>
                    <input type="radio" name="type" value="expense" {{ old('type', 'expense') === 'expense' ? 'checked' : '' }}>
                    <span>Pengeluaran</span>
                </label>
                <label>
                    <input type="radio" name="type" value="income" {{ old('type') === 'income' ? 'checked' : '' }}>
                    <span>Pemasukan</span>
                </label>
            </div>

            <div class="form-grid">
                <label class="field full">
                    <span>Deskripsi</span>
                    <input name="description" value="{{ old('description') }}" placeholder="Contoh: Belanja kebutuhan bulanan" required>
                    @error('description')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field">
                    <span>Kategori</span>
                    <select name="category" required>
                        <option value="">Pilih kategori</option>
                        @foreach (['Gaji','Freelance','Makanan','Hunian','Transportasi','Kesehatan','Pendidikan','Hiburan','Langganan','Lainnya'] as $category)
                            <option {{ old('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                    @error('category')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field">
                    <span>Tanggal</span>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                    @error('transaction_date')<small>{{ $message }}</small>@enderror
                </label>

                <label class="field full">
                    <span>Nominal (Rp)</span>
                    <input type="number" min="1" step="1" name="amount" value="{{ old('amount') }}" placeholder="0" required>
                    @error('amount')<small>{{ $message }}</small>@enderror
                </label>
            </div>

            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="secondary-button">Batal</a>
                <button class="primary-button">Simpan transaksi</button>
            </div>
        </form>
    </div>
</section>
@endsection
