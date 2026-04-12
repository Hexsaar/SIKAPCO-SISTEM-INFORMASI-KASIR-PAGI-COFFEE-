<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ config('app.name') }}</title>

    <!-- Tailwind + Fonts + Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Poppins untuk sidebar -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* minimal styles, no more bubble/employee/notification leftovers */
        .material-icons {
            font-size: 1.25rem;
        }
    </style>
</head>

<body class="bg-gray-100">

<!-- ========== LAYOUT FLEX DENGAN SIDEBAR BARU (TANPA EMPLOYEES & NOTIF) ========== -->
<div class="flex min-h-screen">
    <!-- SIDEBAR KIRI (DESKTOP) - TANPA TOMBOL KARYAWAN & NOTIFIKASI -->
    <aside class="hidden md:flex w-64 bg-white text-[#4B332B] flex-col justify-between py-6 px-0 border-r border-[#E5E5E5] sticky top-0 h-screen" style="font-family: 'Poppins', sans-serif;">
        <div>
            <div class="flex justify-center items-center pb-6 pt-10">
                <h1 class="text-xl font-extrabold tracking-wide">PagiCoffee</h1>
            </div>
            <nav class="flex flex-col gap-1 px-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-[#4B332B] text-white' : 'hover:bg-[#F3F3F3]' }}">
                    <span class="material-icons">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('admin.reports.sales') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.reports.sales') ? 'bg-[#4B332B] text-white' : 'hover:bg-[#F3F3F3]' }}">
                    <span class="material-icons">bar_chart</span>
                    Laporan Penjualan
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.products.*') ? 'bg-[#4B332B] text-white' : 'hover:bg-[#F3F3F3]' }}">
                    <span class="material-icons">restaurant_menu</span>
                    Kelola Menu
                </a>
                <a href="{{ route('admin.finance.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.finance.*') ? 'bg-[#4B332B] text-white' : 'hover:bg-[#F3F3F3]' }}">
                    <span class="material-icons">account_balance_wallet</span>
                    Keuangan
                </a>
                <a href="{{ route('admin.orders.history') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.orders.history') ? 'bg-[#4B332B] text-white' : 'hover:bg-[#F3F3F3]' }}">
                    <span class="material-icons">history</span>
                    History
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold {{ request()->routeIs('profile.*') ? 'bg-[#4B332B] text-white' : 'hover:bg-[#F3F3F3]' }}">
                    <span class="material-icons">settings</span>
                    Pengaturan
                </a>
                <!-- TIDAK ADA TOMBOL KARYAWAN / EMPLOYEES -->
            </nav>
        </div>
        <div class="mt-8 border-t border-[#E5E5E5] px-2 pt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold text-[#4B332B] hover:bg-[#F3F3F3] w-full">
                    <span class="material-icons">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- MOBILE NAVBAR (tanpa notifikasi, tanpa karyawan) -->
        <nav class="md:hidden fixed top-0 left-0 right-0 z-50 bg-[#4F2E22] text-white h-14 flex items-center justify-between px-4 shadow-lg">
            <div class="flex items-center gap-2">
                <span class="material-icons text-xl">restaurant</span>
                <span class="font-bold text-sm">{{ config('app.name', 'Dashboard') }}</span>
            </div>
            <!-- Hanya hamburger menu, tidak ada notifikasi bell -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-full hover:bg-white/10 transition">
                <span class="material-icons text-xl" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
            </button>
        </nav>

        <!-- MOBILE DRAWER (menu sederhana tanpa karyawan/notifikasi) -->
        <div x-data="{ mobileMenuOpen: false }" x-init="() => { window.mobileMenuToggle = () => { mobileMenuOpen = !mobileMenuOpen }; }" 
             x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden fixed top-14 left-0 right-0 z-[55] bg-white shadow-xl overflow-y-auto border-b border-gray-200"
             style="max-height: calc(100vh - 3.5rem)"
             @click.away="mobileMenuOpen = false">
            <div class="flex flex-col divide-y divide-gray-100 py-2">
                <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-5 py-3.5 transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#4B332B] text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                    <span class="material-icons text-lg">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.reports.sales') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-5 py-3.5 transition {{ request()->routeIs('admin.reports.sales') ? 'bg-[#4B332B] text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                    <span class="material-icons text-lg">bar_chart</span>
                    <span>Laporan Penjualan</span>
                </a>
                <a href="{{ route('admin.products.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-5 py-3.5 transition {{ request()->routeIs('admin.products.*') ? 'bg-[#4B332B] text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                    <span class="material-icons text-lg">restaurant_menu</span>
                    <span>Kelola Menu</span>
                </a>
                <a href="{{ route('admin.finance.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-5 py-3.5 transition {{ request()->routeIs('admin.finance.*') ? 'bg-[#4B332B] text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                    <span class="material-icons text-lg">account_balance_wallet</span>
                    <span>Keuangan</span>
                </a>
                <a href="{{ route('admin.orders.history') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-5 py-3.5 transition {{ request()->routeIs('admin.orders.history') ? 'bg-[#4B332B] text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                    <span class="material-icons text-lg">history</span>
                    <span>Riwayat Pesanan</span>
                </a>
                <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-5 py-3.5 transition {{ request()->routeIs('profile.*') ? 'bg-[#4B332B] text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                    <span class="material-icons text-lg">settings</span>
                    <span>Pengaturan</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-5 py-3.5 hover:bg-red-50 transition w-full text-left text-red-600">
                        <span class="material-icons text-lg">logout</span>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- CONTENT UTAMA -->
        <div class="px-4 pb-6 pt-16 md:pt-8 md:px-8 flex-1">
            <h1 class="text-2xl font-semibold mb-6">@yield('title')</h1>
            @yield('content')
        </div>
    </div>
</div>

<!-- AlpineJS sederhana hanya untuk mobile menu, tanpa employees/notifications -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>