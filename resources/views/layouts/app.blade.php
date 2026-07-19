<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Keuangan Me')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark">K</span>
            <span><strong>Keuangan Me</strong><small>Kesehatan finansial</small></span>
        </a>
        <nav class="nav-list">
            <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>⌂</span>Dashboard</a>
            <a class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.create') }}"><span>＋</span>Transaksi</a>
            <a class="{{ request()->routeIs('reports.monthly') ? 'active' : '' }}" href="{{ route('reports.monthly') }}"><span>▤</span>Laporan Bulanan</a>
            <a class="{{ request()->routeIs('reports.yearly') ? 'active' : '' }}" href="{{ route('reports.yearly') }}"><span>⌁</span>Laporan Tahunan</a>
        </nav>
        <a class="primary-button sidebar-button" href="{{ route('transactions.create') }}">＋ Tambah Transaksi</a>
        <div class="profile"><span class="avatar">BM</span><span><strong>Banx Me</strong><small>Pengelola keuangan</small></span></div>
    </aside>
    <header class="mobile-header">
        <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">K</span><strong>Keuangan Me</strong></a>
        <button class="menu-button" data-menu aria-label="Buka menu">☰</button>
    </header>
    <main class="main-content">
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
