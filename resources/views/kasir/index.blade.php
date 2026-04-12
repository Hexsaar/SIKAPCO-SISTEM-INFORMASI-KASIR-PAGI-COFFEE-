<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Menu Kopi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
      /* Kategori style dengan hidden radio dan border coklat saat dipilih */
      .category-radio {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
      }
      
      /* Active state untuk sidebar */
      .sidebar-active {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-left: 4px solid #fff;
        font-weight: 700;
      }
      
      .sidebar-active svg {
        filter: drop-shadow(0 0 2px rgba(255, 255, 255, 0.5));
      }
      
      .category-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: flex-start;
        align-items: center;
      }
      
      .category-item input:checked + label {
        border: 2px solid #4F2E22 !important;
        box-shadow: 0 0 0 1px rgba(79, 46, 34, 0.2);
      }
      
      .category-item label {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: white;
        border-radius: 1rem;
        padding: 0.75rem 1.25rem;
        border: 2px solid transparent;
        transition: all 0.15s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        cursor: pointer;
      }
      
      .category-item label:hover {
        border-color: #e5d6cc;
      }
      
      /* Scroll order untuk checkout */
      .scroll-order {
        max-height: 240px;
        overflow-y: auto;
        scrollbar-width: thin;
      }
      @media (min-width: 768px) {
        .scroll-order {
          max-height: 280px;
        }
      }
      .scroll-order::-webkit-scrollbar {
        width: 5px;
      }
      .scroll-order::-webkit-scrollbar-track {
        background: #f1f1f1;
      }
      .scroll-order::-webkit-scrollbar-thumb {
        background: #d4c4b6;
        border-radius: 20px;
      }
      
      /* Badge untuk notifikasi cart */
      .cart-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: bold;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        border: 2px solid #4F2E22;
      }
      
      /* Style untuk tombol nominal */
      .nominal-btn {
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        color: #4F2E22;
        transition: all 0.2s;
      }
      .nominal-btn:hover {
        background-color: #4F2E22;
        color: white;
        border-color: #4F2E22;
      }
      
      /* Loading Animation */
      .stock-update-loading {
        position: relative;
        overflow: hidden;
      }
      
      .stock-update-loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(79, 46, 34, 0.2), transparent);
        animation: loading-shimmer 1.5s infinite;
      }
      
      @keyframes loading-shimmer {
        0% {
          left: -100%;
        }
        100% {
          left: 100%;
        }
      }
      
      /* Sync indicator */
      .sync-indicator {
        position: fixed;
        top: 80px;
        right: 20px;
        background: #4F2E22;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        z-index: 1000;
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease;
      }
      
      .sync-indicator.show {
        opacity: 1;
        transform: translateY(0);
      }
      
      .sync-indicator.updating {
        background: #f59e0b;
      }
      
      .sync-indicator.success {
        background: #10b981;
      }
      
      .sync-spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
      }
      
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
      
      /* Print styles - only show receipt when printing */
      @media print {
        body * {
          visibility: hidden;
        }
        .print-receipt, .print-receipt * {
          visibility: visible;
        }
        .print-receipt {
          position: absolute;
          left: 0;
          top: 0;
          width: 100%;
          background: white;
          box-shadow: none;
          border: none;
          padding: 20px;
        }
        .print-receipt > div:last-child {
          display: none !important;
        }
      }
    </style>
</head>
<body class="overflow-x-hidden">
      
<!-- Sync Indicator -->
<div id="syncIndicator" class="sync-indicator">
  <div class="sync-spinner"></div>
  <span id="syncText">Updating stock...</span>
</div>

<nav class="bg-neutral-secondary-soft fixed w-full z-20 top-0 start-0 border-b border-default" style="background-color: #4F2E22;">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto px-3 md:px-4 py-2 md:py-2.5">
        <div class="flex items-center gap-2 md:gap-3">
            <button id="hamburger-btn" data-collapse-toggle="navbar-hamburger" type="button" class="inline-flex items-center p-1.5 md:p-2 w-9 h-9 md:w-10 md:h-10 justify-center text-sm text-white rounded-base hover:bg-neutral-tertiary hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary" aria-controls="navbar-hamburger" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5 md:w-6 md:h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14"/></svg>
            </button>
            <span class="text-white font-bold text-base md:text-lg">pagicoffee.id</span>
            <svg class="w-7 h-7 md:w-8 md:h-8 text-yellow-300 ml-1 md:ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <a href="#" class="flex items-center space-x-2 md:space-x-3 rtl:space-x-reverse">
            <span class="self-center text-base md:text-xl text-white font-semibold whitespace-nowrap">{{ auth()->user()->name }}</span>
            <img src="https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=40&h=40&fit=crop" class="h-7 w-7 md:h-8 md:w-8 rounded-full" alt="Barista" />
        </a>
    </div>
</nav>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 w-60 md:w-64 h-screen bg-neutral-secondary-soft transform -translate-x-full transition-transform duration-300 z-30" style="background-color: #4F2E22;">
    <div class="p-5 md:p-6">
        <!-- Logo & Close Button -->
        <div class="flex items-center justify-between mb-6 md:mb-8">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="text-white font-bold">Menu</span>
            </div>
            <button id="close-sidebar" class="text-white hover:bg-neutral-tertiary p-2 rounded">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Menu Items -->
        <ul class="space-y-3 md:space-y-4">
            <li>
                <a href="#" class="flex items-center gap-3 text-white sidebar-active p-2.5 md:p-3 rounded transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m2-2l6-5 6 5m2 2l2 3m-2-3v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7"></path></svg>
                    <span class="font-bold text-sm md:text-base">Menu</span>
                </a>
            </li>
            <li>
                <a href="#" id="history-sidebar-link" class="flex items-center gap-3 text-white hover:bg-neutral-tertiary p-2.5 md:p-3 rounded transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold text-sm md:text-base">History</span>
                </a>
            </li>
            <li>
                <a href="#" id="settings-sidebar-link" class="flex items-center gap-3 text-white hover:bg-neutral-tertiary p-2.5 md:p-3 rounded transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    <span class="font-bold text-sm md:text-base">Settings</span>
                </a>
            </li>
            <li>
                <a href="#" id="cart-sidebar-link" class="flex items-center gap-3 text-white hover:bg-neutral-tertiary p-2.5 md:p-3 rounded transition relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-bold text-sm md:text-base">Cart</span>
                    <!-- Badge notifikasi akan muncul di sini -->
                    <span id="cart-badge" class="cart-badge hidden">0</span>
                </a>
            </li>
        </ul>

        <!-- Logout Button -->
        <div class="absolute bottom-5 md:bottom-6 left-5 md:left-6 right-5 md:right-6">
            <div class="border-t border-white mb-3 md:mb-4"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-white font-semibold py-2 px-3 md:px-4 rounded transition flex items-center justify-center gap-2 hover:opacity-80 text-sm md:text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>

<section class="bg-slate-50 text-slate-900 p-4 md:p-6 lg:p-8 mt-16">
  <!-- Main container 2 kolom -->
  <div class="mx-auto max-w-7xl grid grid-cols-1 md:grid-cols-12 gap-5 md:gap-6 lg:gap-8">
    
    <!-- KIRI: Area Menu -->
    <div class="md:col-span-7 lg:col-span-8">
      <!-- Header All menu + Search -->
      <div class="mb-4 md:mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <button class="text-lg md:text-[22px] font-bold px-4 py-2 rounded-lg" id="show-all-menu">All menu</button>
      <div class="relative w-full sm:w-64 md:w-72">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        </div>
        <input type="text" id="searchInput" placeholder="Cari disini...." 
          class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 bg-white/90 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#4F2E22]/20">
      </div>
      </div>

      <!-- GRID MENU - DYNAMIC FROM DATABASE -->
      <section class="grid gap-3 md:gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 mb-6 md:mb-8">
        @foreach($products as $product)
        <button class="menu-card group flex flex-col items-center rounded-xl md:rounded-2xl border border-slate-200 bg-white p-2.5 md:p-3 text-center shadow-sm transition-all duration-300 hover:shadow-lg hover:border-[#4F2E22] hover:-translate-y-1 active:scale-95 active:shadow-lg active:border-[#4F2E22] 
                @if(!$product->is_available || $product->stock <= 0) opacity-50 pointer-events-none grayscale @endif" 
                data-product-id="{{ $product->id }}" 
                data-product-name="{{ $product->name }}" 
                data-product-price="{{ $product->price }}"
                data-product-stock="{{ $product->stock }}"
                data-product-available="{{ $product->is_available ? 'true' : 'false' }}"
                data-category-id="{{ $product->category_id }}"
                data-product="{{ json_encode(['id' => $product->id, 'name' => $product->name, 'price' => $product->price, 'stock' => $product->stock, 'is_available' => $product->is_available]) }}">
          <img src="{{ $product->image ? asset('uploads/' . $product->image) : 'https://via.placeholder.com/150' }}" 
               alt="{{ $product->name }}" 
               class="h-[90px] md:h-[110px] w-[75px] md:w-[90px] rounded-lg md:rounded-[10px] border border-slate-200 object-cover transition-transform duration-300 group-hover:scale-110" />
          <div class="mt-1.5 md:mt-2 text-xs md:text-[13px] font-semibold">{{ $product->name }}</div>
          <div class="mb-1 text-[11px] md:text-[12px] font-bold text-amber-700">{{ number_format($product->price/1000, 0) }}K</div>
          <div class="rounded-full border 
                @if(!$product->is_available || $product->stock <= 0) 
                    border-red-200 bg-red-50 text-red-600 font-semibold 
                @elseif($product->stock <= 5) 
                    border-orange-200 bg-orange-50 text-orange-600 
                @else 
                    border-dashed border-slate-200 text-slate-500 
                @endif 
                px-2 py-0.5 md:py-1 text-[10px] md:text-[11px]">
            @if(!$product->is_available)
                Tidak Tersedia
            @elseif($product->stock <= 0)
                Stok Habis
            @else
                Stok tersisa {{ $product->stock }}
            @endif
          </div>
        </button>
        @endforeach
      </section>

      <!-- KATEGORI - DILETAKKAN DI BAWAH GRID MENU -->
      <div class="border-t border-slate-200 pt-4 md:pt-6 mt-2">
      <div class="category-container">
        <!-- Coffee -->
        <div class="category-item">
        <input type="radio" name="kategori" id="cat-coffee" class="category-radio" data-category="1" checked>
        <label for="cat-coffee">
          <div class="w-12 h-12 md:w-14 md:h-14 mb-2 flex items-center justify-center bg-[#F3E9DE] rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4F2E22" stroke-width="1.5">
            <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
          </svg>
          </div>
          <span class="font-medium text-stone-800 text-xs md:text-sm">Coffee</span>
        </label>
        </div>
        <!-- Non Coffee -->
        <div class="category-item">
        <input type="radio" name="kategori" id="cat-noncoffee" class="category-radio" data-category="2">
        <label for="cat-noncoffee">
          <div class="w-12 h-12 md:w-14 md:h-14 mb-2 flex items-center justify-center bg-[#F3E9DE] rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4F2E22" stroke-width="1.5">
            <path d="M12 2a10 10 0 0 1 10 10c0 5-4 8-10 8-6 0-10-3-10-8 0-5 4-10 10-10z"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/>
          </svg>
          </div>
          <span class="font-medium text-stone-800 text-xs md:text-sm">Non Coffee</span>
        </label>
        </div>
        <!-- Coffee Milk -->
        <div class="category-item">
        <input type="radio" name="kategori" id="cat-milk" class="category-radio" data-category="6">
        <label for="cat-milk">
          <div class="w-12 h-12 md:w-14 md:h-14 mb-2 flex items-center justify-center bg-[#F3E9DE] rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4F2E22" stroke-width="1.5">
            <path d="M8 2h8M9 22h6M12 2v4M8 8h8"/><rect x="6" y="8" width="12" height="12" rx="2"/><path d="M18 12h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2"/>
          </svg>
          </div>
          <span class="font-medium text-stone-800 text-xs md:text-sm">Coffee Milk</span>
        </label>
        </div>
        <!-- Snack -->
        <div class="category-item">
        <input type="radio" name="kategori" id="cat-snack" class="category-radio" data-category="4">
        <label for="cat-snack">
          <div class="w-12 h-12 md:w-14 md:h-14 mb-2 flex items-center justify-center bg-[#F3E9DE] rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4F2E22" stroke-width="1.5">
            <path d="M12 2a3 3 0 0 0-3 3c0 2 3 5 3 5s3-3 3-5a3 3 0 0 0-3-3Z"/><path d="M5 12a7 7 0 0 1 14 0v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-6Z"/><path d="M9 16v2"/><path d="M15 16v2"/>
          </svg>
          </div>
          <span class="font-medium text-stone-800 text-xs md:text-sm">Snack</span>
        </label>
        </div>
        <!-- Bottle -->
        <div class="category-item">
        <input type="radio" name="kategori" id="cat-bottle" class="category-radio" data-category="7">
        <label for="cat-bottle">
          <div class="w-12 h-12 md:w-14 md:h-14 mb-2 flex items-center justify-center bg-[#F3E9DE] rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4F2E22" stroke-width="1.5">
            <path d="M10 5v3c0 1.5-.5 3-2 4"/><path d="M14 5v3c0 1.5.5 3 2 4"/><path d="M5 11c0-1.5.5-3 2-4h10c1.5 1 2 2.5 2 4v8c0 1.5-1 2-2 2H7c-1 0-2-.5-2-2v-8Z"/><path d="M9 13v4"/><path d="M15 13v4"/>
          </svg>
          </div>
          <span class="font-medium text-stone-800 text-xs md:text-sm">Bottle</span>
        </label>
        </div>
      </div>
      </div>
    </div>

  <!-- ================= WRAPPER ================= -->
  <div class="md:col-span-5 lg:col-span-4 bg-white rounded-2xl shadow-xl border border-slate-300 flex flex-col p-4 h-screen max-h-[800px] sticky top-24">

    <!-- ================= HEADER ================= -->
    <h1 class="text-2xl font-bold text-center pb-2 border-b border-slate-200">
      Checkout
    </h1>

    <!-- ================= LIST ITEM ================= -->
    <!-- Ini akan diisi oleh JavaScript, kosong di awal -->
    <div class="mt-3 flex-1 overflow-y-auto space-y-3 pr-1 scroll-order" id="order-list-container">
      <!-- Konten akan dirender oleh JavaScript -->
    </div>

    <!-- ================= TOTAL ================= -->
    <div class="mt-4 space-y-1.5 bg-slate-50 p-3 rounded-xl border">

      <div class="flex justify-between text-sm">
        <span class="text-slate-600">Sub total</span>
        <span id="subtotal-val">Rp 0</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-slate-600">Service</span>
        <span>Rp 0</span>
      </div>

      <div class="flex justify-between text-lg font-bold border-t pt-2 mt-1">
        <span>Total</span>
        <span class="text-[#4F2E22]" id="total-val">Rp 0</span>
      </div>

    </div>

    <!-- ================= BUTTON PAY ================= -->
    <button class="mt-3 w-full bg-[#4F2E22] hover:bg-[#3f251b] text-white font-bold py-3 rounded-xl text-lg shadow-md transition active:scale-95" id="pay-btn">
      Bayar
    </button>

    <!-- ================= BUTTON ================= -->
    <div class="mt-4 flex gap-3">
      <button id="cancel-btn" class="flex-1 border-2 border-red-500 text-red-500 font-bold py-3 rounded-full text-lg transition hover:bg-red-50 active:scale-95">
        CANCEL ORDER
      </button>
      <button id="hold-btn" class="flex-1 border-2 border-green-500 text-green-500 font-bold py-3 rounded-full text-lg transition hover:bg-green-50 active:scale-95">
        HOLD ORDER
      </button>
    </div>

  </div>
  <!-- ================= END WRAPPER ================= -->

</div>

<script>
  // ==================== FUNGSIONALITAS JAVASCRIPT ====================
  
  // Data struktur order
  let orders = [];
  const orderListEl = document.getElementById('order-list-container');
  
  // Get total elements
  const subtotalTextEl = document.getElementById('subtotal-val');
  const totalTextEl = document.getElementById('total-val');
  
  // Cart badge element
  const cartBadge = document.getElementById('cart-badge');
  
  // Fungsi untuk update badge cart
  function updateCartBadge() {
    const savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];
    const count = savedOrders.length;
    
    if (count > 0) {
      cartBadge.textContent = count;
      cartBadge.classList.remove('hidden');
    } else {
      cartBadge.classList.add('hidden');
    }
  }
  
  // Menu item click handler
  document.querySelectorAll('.menu-card').forEach(card => {
    card.addEventListener('click', (e) => {
      const productData = JSON.parse(card.dataset.product);
      const name = productData.name;
      const price = parseInt(productData.price);
      const stock = parseInt(productData.stock);
      const productId = parseInt(productData.id);
      const isAvailable = productData.is_available === true;
      
      // Cek availability dan stok
      if (!isAvailable || stock <= 0) {
        alert('Maaf, menu ini tidak tersedia!');
        return;
      }
      
      // Cek if item sudah ada
      const existingOrder = orders.find(o => o.name === name);
      
      if (existingOrder) {
        // Cek stok tersisa
        const totalQty = orders.reduce((sum, o) => o.name === name ? sum + o.qty : sum, 0);
        if (totalQty < stock) {
          existingOrder.qty++;
        } else {
          alert('Stok tidak mencukupi!');
          return;
        }
      } else {
        orders.push({ 
          productId: productId,
          name: name, 
          price: price, 
          qty: 1, 
          stock: stock 
        });
      }
      
      renderOrderList();
    });
  });

  // Real-time stock update function with enhanced loading
  async function updateProductStock(productId, newStock, isAvailable, action = 'unknown') {
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (!productCard) return;

    // Add loading animation immediately
    productCard.classList.add('stock-update-loading');
    
    // Show appropriate sync indicator based on action
    let syncMessage = 'Updating stock...';
    if (action === 'admin_update') {
      syncMessage = 'Admin updating stock...';
    } else if (action === 'transaction_update') {
      syncMessage = 'Processing transaction...';
    } else if (action === 'poll_update') {
      syncMessage = 'Syncing stock...';
    } else if (action === 'manual_update') {
      syncMessage = 'Manual refresh...';
    }
    
    showSyncIndicator(syncMessage, 'updating');

    // Update data attributes
    productCard.dataset.productStock = newStock;
    productCard.dataset.productAvailable = isAvailable ? 'true' : 'false';
    
    // Update product data in dataset
    const productData = JSON.parse(productCard.dataset.product);
    productData.stock = newStock;
    productData.is_available = isAvailable;
    productCard.dataset.product = JSON.stringify(productData);

    // Faster loading delay (300ms for better visual feedback)
    await new Promise(resolve => setTimeout(resolve, 300));

    // Update stock display
    const stockDiv = productCard.querySelector('.rounded-full.border');
    if (stockDiv) {
      if (!isAvailable || newStock <= 0) {
        stockDiv.className = 'rounded-full border border-red-200 bg-red-50 text-red-600 font-semibold px-2 py-0.5 md:py-1 text-[10px] md:text-[11px]';
        stockDiv.textContent = !isAvailable ? 'Tidak Tersedia' : 'Stok Habis';
        
        // Disable the card
        productCard.style.opacity = '0.5';
        productCard.style.pointerEvents = 'none';
        productCard.style.filter = 'grayscale(100%)';
      } else if (newStock <= 5) {
        stockDiv.className = 'rounded-full border border-orange-200 bg-orange-50 text-orange-600 px-2 py-0.5 md:py-1 text-[10px] md:text-[11px]';
        stockDiv.textContent = `Stok tersisa ${newStock}`;
        
        // Enable the card
        productCard.style.opacity = '1';
        productCard.style.pointerEvents = 'auto';
        productCard.style.filter = 'none';
      } else {
        stockDiv.className = 'rounded-full border border-dashed border-slate-200 text-slate-500 px-2 py-0.5 md:py-1 text-[10px] md:text-[11px]';
        stockDiv.textContent = `Stok tersisa ${newStock}`;
        
        // Enable the card
        productCard.style.opacity = '1';
        productCard.style.pointerEvents = 'auto';
        productCard.style.filter = 'none';
      }
    }

    // Remove loading animation
    productCard.classList.remove('stock-update-loading');
    
    // Show success message based on action
    let successMessage = 'Stock updated!';
    if (action === 'admin_update') {
      successMessage = 'Admin update completed!';
    } else if (action === 'transaction_update') {
      successMessage = 'Transaction processed!';
    } else if (action === 'poll_update') {
      successMessage = 'Stock synced!';
    } else if (action === 'manual_update') {
      successMessage = 'Manual refresh completed!';
    }
    
    showSyncIndicator(successMessage, 'success');
    
    // Hide sync indicator faster (1.5 seconds for better visibility)
    setTimeout(() => {
      hideSyncIndicator();
    }, 1500);
  }

  // Version-based fast polling (no persistent connections — compatible with php artisan serve)
  let lastStockVersion = 0;
  let pollTimerId = null;
  let idleCycles = 0;
  const POLL_FAST_MS   = 400;   // right after a change
  const POLL_NORMAL_MS = 800;   // default
  const POLL_SLOW_MS   = 2000;  // after several idle cycles
  const IDLE_SLOW_THRESHOLD = 4;

  async function pollStockVersion() {
    try {
      const res = await fetch('/api/products/stock-version', { cache: 'no-store' });
      const json = await res.json();
      const version = json.version || 0;

      if (lastStockVersion === 0) {
        // First run — baseline, no fetch
        lastStockVersion = version;
        idleCycles = 0;
      } else if (version !== lastStockVersion) {
        lastStockVersion = version;
        idleCycles = 0;
        // Fetch full product list only when something changed
        const prodRes = await fetch('/api/products', { cache: 'no-store' });
        const prodJson = await prodRes.json();
        if (prodJson.success) {
          prodJson.data.forEach(p => {
            updateProductStock(p.id, p.stock, p.is_available, 'poll_update');
          });
        }
      } else {
        idleCycles++;
      }
    } catch (e) {
      // swallow errors silently — will retry
    }

    // Adaptive interval: go slow when nothing is happening
    const delay = idleCycles >= IDLE_SLOW_THRESHOLD ? POLL_SLOW_MS : POLL_NORMAL_MS;
    pollTimerId = setTimeout(pollStockVersion, delay);
  }

  function listenForStockUpdates() {
    lastStockVersion = 0;
    idleCycles = 0;
    pollStockVersion();
  }

  function stopStockUpdates() {
    if (pollTimerId !== null) {
      clearTimeout(pollTimerId);
      pollTimerId = null;
    }
  }

  // After a successful transaction, immediately update the kasir UI without waiting for the next poll
  function applyLocalStockUpdateAfterTransaction(items) {
    items.forEach(item => {
      const card = document.querySelector(`[data-product-id="${item.product_id}"]`);
      if (!card) return;
      const currentStock = parseInt(card.dataset.productStock) || 0;
      const newStock = Math.max(0, currentStock - item.quantity);
      updateProductStock(item.product_id, newStock, newStock > 0, 'transaction_update');
    });
  }

  // Sync indicator functions
  function showSyncIndicator(text, type = 'updating') {
    const indicator = document.getElementById('syncIndicator');
    const syncText = document.getElementById('syncText');
    
    syncText.textContent = text;
    indicator.className = `sync-indicator show ${type}`;
  }

  function hideSyncIndicator() {
    const indicator = document.getElementById('syncIndicator');
    indicator.classList.remove('show');
  }

  // Manual refresh function (hanya untuk testing - real-time does this automatically)
  async function refreshStockNow() {
    try {
      showSyncIndicator('Manual refresh...', 'updating');
      
      // Force refresh from API
      const currentHost = window.location.hostname;
      const currentPort = window.location.port || '80';
      const serverUrl = `${window.location.protocol}//${currentHost}:${currentPort}`;
      
      const response = await fetch(`${serverUrl}/api/products`);
      const result = await response.json();
      
      if (result.success) {
        result.data.forEach(product => {
          updateProductStock(product.id, product.stock, product.is_available_for_sale, 'manual_update');
        });
        console.log('🔄 Manual refresh completed');
        
        showSyncIndicator('Refresh completed!', 'success');
        setTimeout(() => {
          hideSyncIndicator();
        }, 1500);
      }
    } catch (error) {
      console.error('Error refreshing stock:', error);
      hideSyncIndicator();
    }
  }

  // Add keyboard shortcut untuk manual refresh (Ctrl+R)
  document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key === 'r') {
      e.preventDefault();
      refreshStockNow();
    }
  });

  // Start listening for stock updates when page loads
  listenForStockUpdates();

  // Stop listening when page unloads
  window.addEventListener('beforeunload', stopStockUpdates);
  
  function renderOrderList() {
    // Kosongkan container
    orderListEl.innerHTML = '';
    
    if (orders.length === 0) {
      // Tampilkan pesan kosong
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'text-center text-slate-400 py-8';
      emptyDiv.textContent = 'Belum ada pesanan';
      orderListEl.appendChild(emptyDiv);
      updateTotal();
      return;
    }
    
    // Render setiap order
    orders.forEach((order, index) => {
      const rowDiv = document.createElement('div');
      rowDiv.className = 'flex items-center justify-between text-sm';
      
      const itemTotal = order.price * order.qty;
      
      // Buat struktur HTML seperti contoh asli
      rowDiv.innerHTML = `
        <div class="flex items-center gap-2 flex-1">
          <svg class="w-5 h-5 text-red-500 cursor-pointer delete-order" data-index="${index}" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-9l-1 1H5v2h14V4z"/>
          </svg>
          <span class="font-medium">${order.name}</span>
        </div>
        <div class="flex items-center gap-2 w-[90px] justify-center">
          <button class="w-7 h-7 rounded-full border flex items-center justify-center active:scale-95 transition-transform qty-dec" data-index="${index}">−</button>
          <span class="qty-display">${order.qty}</span>
          <button class="w-7 h-7 rounded-full border flex items-center justify-center active:scale-95 transition-transform qty-inc" data-index="${index}">+</button>
        </div>
        <div class="w-[90px] text-right font-medium">Rp ${itemTotal.toLocaleString('id-ID')}</div>
      `;
      
      orderListEl.appendChild(rowDiv);
    });
    
    // Attach event listeners untuk delete
    document.querySelectorAll('.delete-order').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && index >= 0 && index < orders.length) {
          orders.splice(index, 1);
          renderOrderList();
        }
      });
    });
    
    // Attach event listeners untuk increment
    document.querySelectorAll('.qty-inc').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && index >= 0 && index < orders.length) {
          const order = orders[index];
          const totalQty = orders.reduce((sum, o) => o.name === order.name ? sum + o.qty : sum, 0);
          if (totalQty < order.stock) {
            orders[index].qty++;
          } else {
            alert('Stok tidak mencukupi!');
          }
          renderOrderList();
        }
      });
    });
    
    // Attach event listeners untuk decrement
    document.querySelectorAll('.qty-dec').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && index >= 0 && index < orders.length) {
          if (orders[index].qty > 1) {
            orders[index].qty--;
          } else {
            orders.splice(index, 1);
          }
          renderOrderList();
        }
      });
    });
    
    updateTotal();
  }
  
  function updateTotal() {
    if (orders.length === 0) {
      if (subtotalTextEl) subtotalTextEl.textContent = 'Rp 0';
      if (totalTextEl) totalTextEl.textContent = 'Rp 0';
      return 0;
    }
    
    const subtotal = orders.reduce((sum, o) => sum + (o.price * o.qty), 0);
    const total = subtotal;
    
    if (subtotalTextEl) subtotalTextEl.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
    if (totalTextEl) totalTextEl.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    
    return total; // Return total untuk digunakan di fungsi lain
  }
  
  // Payment button handler
  const payBtn = document.getElementById('pay-btn');
  
  if (payBtn) {
    payBtn.addEventListener('click', () => {
      if (orders.length === 0) {
        alert('Tambahkan pesanan terlebih dahulu!');
        return;
      }
      showPaymentMethodModal();
    });
  }
  
  function showPaymentMethodModal() {
    // Check existing modal
    if (document.getElementById('payment-method-modal')) return;

    const modal = document.createElement('div');
    modal.id = 'payment-method-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    const total = updateTotal();

    modal.innerHTML = `
      <div class="bg-white rounded-2xl p-6 w-96 shadow-2xl transform transition-all scale-100">
        <h2 class="text-2xl font-bold text-center mb-2">Total: Rp ${total.toLocaleString('id-ID')}</h2>
        <p class="text-center text-gray-500 mb-6">Pilih Metode Pembayaran</p>
        <div class="flex gap-4">
          <button class="flex-1 flex flex-col items-center gap-2 bg-amber-50 border-2 border-[#4F2E22] text-[#4F2E22] hover:bg-[#4F2E22] hover:text-white p-4 rounded-xl transition-colors" id="cash-method-btn">
            <span class="material-symbols-outlined text-3xl">payments</span>
            <span class="font-bold">CASH</span>
          </button>
          <button class="flex-1 flex flex-col items-center gap-2 bg-amber-50 border-2 border-[#4F2E22] text-[#4F2E22] hover:bg-[#4F2E22] hover:text-white p-4 rounded-xl transition-colors" id="qris-method-btn">
            <span class="material-symbols-outlined text-3xl">qr_code_scanner</span>
            <span class="font-bold">QRIS</span>
          </button>
        </div>
        <button class="mt-6 w-full py-2 text-gray-400 hover:text-gray-600" id="close-payment-method-modal">Batal</button>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('cash-method-btn').addEventListener('click', () => {
      modal.remove();
      showCashPaymentModal();
    });
    
    document.getElementById('qris-method-btn').addEventListener('click', () => {
      modal.remove();
      // Untuk QRIS langsung ke struk (tanpa input uang)
      printReceipt('QRIS');
    });

    document.getElementById('close-payment-method-modal').addEventListener('click', () => {
      modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.remove();
    });
  }
  
  // Fungsi untuk format Rupiah
  function formatRupiah(angka) {
    if (!angka) return 'Rp 0';
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }
  
  // Fungsi untuk parse Rupiah ke number
  function parseRupiah(rupiah) {
    if (!rupiah) return 0;
    // Hapus "Rp " dan titik, lalu konversi ke number
    return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
  }
  
  function showCashPaymentModal() {
    // Check existing modal
    if (document.getElementById('cash-payment-modal')) return;

    const modal = document.createElement('div');
    modal.id = 'cash-payment-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    const total = updateTotal();

    modal.innerHTML = `
      <div class="bg-white rounded-2xl p-6 w-96 shadow-2xl transform transition-all scale-100">
        <h2 class="text-xl font-bold text-center mb-4">Pembayaran Tunai</h2>
        
        <div class="mb-4 p-4 bg-slate-50 rounded-xl">
          <div class="flex justify-between mb-2">
            <span class="text-slate-600">Total</span>
            <span class="font-bold text-lg">${formatRupiah(total)}</span>
          </div>
          
          <div class="mb-3">
            <label class="block text-sm text-slate-600 mb-1">Uang diterima</label>
            <input type="text" id="cash-amount" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-lg font-bold focus:outline-none focus:ring-2 focus:ring-[#4F2E22]/20" placeholder="Rp 0" value="">
          </div>
          
          <div class="flex justify-between text-lg border-t pt-3">
            <span class="text-slate-600">Uang kembali</span>
            <span id="change-amount" class="font-bold text-green-600">Rp 0</span>
          </div>
        </div>
        
        <!-- Tombol nominal cepat -->
        <div class="grid grid-cols-5 gap-2 mb-4">
          <button class="nominal-btn" data-nominal="10000">10K</button>
          <button class="nominal-btn" data-nominal="20000">20K</button>
          <button class="nominal-btn" data-nominal="30000">30K</button>
          <button class="nominal-btn" data-nominal="40000">40K</button>
          <button class="nominal-btn" data-nominal="50000">50K</button>
          <button class="nominal-btn" data-nominal="60000">60K</button>
          <button class="nominal-btn" data-nominal="70000">70K</button>
          <button class="nominal-btn" data-nominal="80000">80K</button>
          <button class="nominal-btn" data-nominal="90000">90K</button>
          <button class="nominal-btn" data-nominal="100000">100K</button>
        </div>
        
        <div class="flex gap-3">
          <button class="flex-1 bg-[#4F2E22] hover:bg-[#3f251b] text-white font-bold py-3 rounded-xl transition active:scale-95" id="continue-cash-btn" disabled>
            Lanjut
          </button>
          <button class="flex-1 border-2 border-gray-300 text-gray-600 font-bold py-3 rounded-xl transition hover:bg-gray-100 active:scale-95" id="cancel-cash-btn">
            Batal
          </button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    const cashInput = document.getElementById('cash-amount');
    const changeSpan = document.getElementById('change-amount');
    const continueBtn = document.getElementById('continue-cash-btn');
    
    // Fungsi untuk update tampilan
    function updateCashDisplay() {
      const rawValue = cashInput.value.replace(/[^0-9]/g, '');
      const cashValue = rawValue ? parseInt(rawValue) : 0;
      
      // Format ulang input
      if (rawValue) {
        cashInput.value = formatRupiah(parseInt(rawValue)).replace('Rp ', '');
      } else {
        cashInput.value = '';
      }
      
      // Hitung kembalian
      if (cashValue >= total) {
        const change = cashValue - total;
        changeSpan.textContent = formatRupiah(change);
        continueBtn.disabled = false;
        continueBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      } else {
        if (cashValue > 0) {
          const kurang = total - cashValue;
          changeSpan.textContent = `${formatRupiah(0)} (Kurang ${formatRupiah(kurang)})`;
        } else {
          changeSpan.textContent = 'Rp 0';
        }
        continueBtn.disabled = true;
        continueBtn.classList.add('opacity-50', 'cursor-not-allowed');
      }
    }
    
    // Event listener untuk input
    cashInput.addEventListener('input', updateCashDisplay);
    
    // Event listener untuk tombol nominal
    document.querySelectorAll('.nominal-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const nominal = parseInt(btn.dataset.nominal);
        cashInput.value = formatRupiah(nominal).replace('Rp ', '');
        updateCashDisplay();
      });
    });
    
    cashInput.focus(); // Langsung fokus ke input
    
    document.getElementById('continue-cash-btn').addEventListener('click', () => {
      const cashValue = parseRupiah(cashInput.value);
      if (cashValue >= total) {
        modal.remove();
        printReceipt('CASH', cashValue);
      }
    });
    
    document.getElementById('cancel-cash-btn').addEventListener('click', () => {
      modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.remove();
    });
  }
  
  function printReceipt(method, cashReceived = null) {
    const total = updateTotal();
    const subtotal = orders.reduce((sum, o) => sum + (o.price * o.qty), 0);
    
    // Buat struk dalam modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    const orderId = 'ORD-IDPGICFFEE' + now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0');
    
    let receiptHTML = `
      <div class="print-receipt bg-white rounded-2xl p-6 w-96 shadow-2xl" style="font-family: 'Courier New', monospace; font-size: 13px;">
        <!-- Title -->
        <div class="text-center mb-3 font-bold text-lg">
          Cetak struk
        </div>
        
        <!-- Shop Info -->
        <div class="text-center mb-3 leading-tight">
          <div class="font-bold">Pagi Coffee</div>
          <div class="text-xs">Taman Pagelaran</div>
          <div class="text-xs">085353712877</div>
        </div>
        
        <!-- Separator 1 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Order ID -->
        <div class="text-xs mb-2 text-center">
          Order ID: ${orderId}
        </div>
        
        <!-- Separator 2 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Kasir Info -->
        <div class="text-xs mb-2">
          Kasir: {{ auth()->user()->name }}
        </div>
        
        <!-- Separator 3 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Items List -->
        <div class="mb-2">
    `;
    
    // Daftar item dengan format monospace
    orders.forEach(order => {
      const itemTotal = order.price * order.qty;
      const name = order.name.padEnd(20);
      const qtyStr = `x${order.qty}`.padStart(4);
      const priceStr = formatRupiah(itemTotal).padStart(12);
      receiptHTML += `
        <div class="flex justify-between text-xs">
          <div>${order.name}</div>
          <div>${qtyStr}</div>
          <div class="text-right">${priceStr}</div>
        </div>
      `;
    });
    
    receiptHTML += `
        </div>
        
        <!-- Separator 4 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Summary Section -->
        <div class="text-xs mb-2 space-y-1">
          <div class="flex justify-between">
            <span>Discount (%)</span>
            <span class="text-right">Rp 0</span>
          </div>
          <div class="flex justify-between">
            <span>Sub total</span>
            <span class="text-right">${formatRupiah(subtotal)}</span>
          </div>
          <div class="flex justify-between">
            <span>Tax 5%</span>
            <span class="text-right">Rp 0</span>
          </div>
        </div>
        
        <!-- Separator 5 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Total -->
        <div class="text-center mb-3 font-bold text-lg">
          <div>TOTAL</div>
          <div class="text-2xl text-[#4F2E22]">${formatRupiah(total)}</div>
        </div>
        
        <!-- Separator 6 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Payment Info -->
        <div class="text-xs mb-3 space-y-1">
    `;
    
    if (method === 'CASH' && cashReceived) {
      const change = cashReceived - total;
      receiptHTML += `
          <div class="flex justify-between">
            <span>Tunai</span>
            <span class="text-right">${formatRupiah(cashReceived)}</span>
          </div>
          <div class="flex justify-between font-bold">
            <span>Kembalian</span>
            <span class="text-right">${formatRupiah(change)}</span>
          </div>
      `;
    } else {
      receiptHTML += `
          <div class="flex justify-between">
            <span>Metode Pembayaran</span>
            <span class="text-right font-bold">QRIS</span>
          </div>
      `;
    }
    
    receiptHTML += `
        </div>
        
        <!-- Separator 7 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Footer -->
        <div class="text-center text-xs text-gray-600 mb-4">
          <p>Terima kasih telah berbelanja</p>
          <p>Silakan datang kembali</p>
        </div>
        
        <!-- Button -->
        <button class="w-full bg-[#4F2E22] hover:bg-[#3f251b] text-white font-bold py-2 rounded-lg transition active:scale-95" id="print-close-btn">
          Cetak
        </button>
      </div>
    `;
    
    modal.innerHTML = receiptHTML;
    document.body.appendChild(modal);
    
    document.getElementById('print-close-btn').addEventListener('click', () => {
      // Save transaction to database
      saveTransactionToDatabase(method, cashReceived);
      
      orders = [];
      renderOrderList();
      updateTotal();
      
      // Create print-friendly receipt
      printReceiptContent(receiptHTML);
      modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.remove();
        
        // Save transaction to database
        saveTransactionToDatabase(method, cashReceived);
        
        orders = [];
        renderOrderList();
        updateTotal();
      }
    });
  }
  
  // Function to print receipt content in new window
  function printReceiptContent(receiptHTML) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Extract only the receipt content (remove the print button)
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = receiptHTML;
    const receiptContent = tempDiv.querySelector('.print-receipt').innerHTML;
    
    // Create print-friendly HTML
    const printHTML = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Struk Pagi Coffee</title>
        <style>
          body {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 0;
            padding: 20px;
            background: white;
          }
          .text-center { text-align: center; }
          .mb-3 { margin-bottom: 12px; }
          .mb-2 { margin-bottom: 8px; }
          .mb-4 { margin-bottom: 16px; }
          .font-bold { font-weight: bold; }
          .text-lg { font-size: 18px; }
          .text-xs { font-size: 12px; }
          .leading-tight { line-height: 1.25; }
          .border-t { border-top: 1px solid #000; }
          .border-dashed { border-style: dashed; }
          .border-gray-400 { border-color: #9ca3af; }
          .my-2 { margin-top: 8px; margin-bottom: 8px; }
          .space-y-1 > * + * { margin-top: 4px; }
          .flex { display: flex; }
          .justify-between { justify-content: space-between; }
          .text-right { text-align: right; }
          .text-gray-600 { color: #4b5563; }
          @media print {
            body { margin: 0; padding: 10px; }
          }
        </style>
      </head>
      <body>
        ${receiptContent}
      </body>
      </html>
    `;
    
    printWindow.document.write(printHTML);
    printWindow.document.close();
    
    // Wait for content to load, then print
    printWindow.onload = function() {
      printWindow.print();
      printWindow.close();
    };
  }
  
  // Function to save transaction to database
  async function saveTransactionToDatabase(paymentMethod, cashReceived = null) {
    try {
      showSyncIndicator('Processing transaction...', 'updating');
      
      // Prepare items data
      const items = orders.map(order => ({
        product_id: order.productId,
        quantity: order.qty,
        price: order.price
      }));
      
      const payload = {
        items: items,
        payment_method: paymentMethod,
        cash_received: cashReceived
      };
      
      const response = await fetch('/api/transactions', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(payload)
      });
      
      const result = await response.json();
      
      if (!result.success) {
        console.error('Failed to save transaction:', result.message);
        alert('Gagal menyimpan transaksi: ' + result.message);
        hideSyncIndicator();
      } else {
        console.log('Transaction saved successfully:', result.data);
        showSyncIndicator('Transaction completed!', 'success');
        
        // Immediately update stock in UI without waiting for next poll
        applyLocalStockUpdateAfterTransaction(items);

        // Hide indicator quickly after transaction
        setTimeout(() => {
          hideSyncIndicator();
        }, 800);

        // Speed up next poll so admin-side changes also reflect quickly
        idleCycles = 0;
      }
      
    } catch (error) {
      console.error('Error saving transaction:', error);
      alert('Terjadi kesalahan saat menyimpan transaksi');
      hideSyncIndicator();
    }
  }
  
  // Cancel button
  const cancelBtn = document.getElementById('cancel-btn');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      if (orders.length === 0) {
        alert('Tidak ada pesanan untuk dibatalkan!');
        return;
      }
      if (confirm('Batalkan seluruh pesanan?')) {
        orders = [];
        renderOrderList();
        updateTotal();
      }
    });
  }
  
  // Hold button logic
  const holdBtn = document.getElementById('hold-btn');
  if (holdBtn) {
    holdBtn.addEventListener('click', () => {
      if (orders.length === 0) {
        alert('Tidak ada pesanan untuk di-hold!');
        return;
      }
      
      const savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];
      const holdId = Date.now();
      
      savedOrders.push({
        id: holdId,
        timestamp: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
        items: orders.map(o => ({ ...o })), // copy array
        totalRaw: orders.reduce((sum, o) => sum + (o.price * o.qty), 0)
      });
      
      localStorage.setItem('heldOrders', JSON.stringify(savedOrders));
      
      alert('Pesanan masuk ke Cart!');
      orders = [];
      renderOrderList();
      updateTotal();
      updateCartBadge(); // Update badge setelah hold
    });
  }

  // Cart Button Logic (Sidebar)
  const cartLink = document.getElementById('cart-sidebar-link');
  if (cartLink) {
    cartLink.addEventListener('click', (e) => {
      e.preventDefault();
      showCartModal();
    });
  }

  // History Button Logic (Sidebar)
  const historyLink = document.getElementById('history-sidebar-link');
  if (historyLink) {
    historyLink.addEventListener('click', (e) => {
      e.preventDefault();
      showHistoryModal();
    });
  }

  // Settings Button Logic (Sidebar)
  const settingsLink = document.getElementById('settings-sidebar-link');
  if (settingsLink) {
    settingsLink.addEventListener('click', (e) => {
      e.preventDefault();
      showSettingsModal();
    });
  }

  function showCartModal() {
    // Check existing modal
    if (document.getElementById('cart-modal')) return;

    const savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];

    const modal = document.createElement('div');
    modal.id = 'cart-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    let contentHtml = '';
    
    if (savedOrders.length === 0) {
      contentHtml = `
        <div class="text-center py-8">
          <p class="text-gray-500 text-lg">Cart Kosong</p>
          <p class="text-gray-400 text-sm">Belum ada pesanan yang di-hold</p>
        </div>
      `;
    } else {
      contentHtml = `<div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2">`;
      savedOrders.forEach((order, idx) => {
        const itemCount = order.items.reduce((acc, curr) => acc + curr.qty, 0);
        contentHtml += `
          <div class="border border-slate-200 rounded-xl p-3 hover:border-[#4F2E22] cursor-pointer transition flex justify-between items-center group bg-slate-50 hover:bg-white" onclick="restoreOrder(${idx})">
            <div>
              <div class="font-bold text-[#4F2E22]">Order #${idx + 1}</div>
              <div class="text-xs text-gray-500">${order.timestamp} • ${itemCount} Items</div>
            </div>
            <div class="text-right">
              <div class="font-bold">${formatRupiah(order.totalRaw)}</div>
              <div class="text-[10px] text-[#4F2E22] group-hover:underline">Klik untuk restore</div>
            </div>
          </div>
        `;
      });
      contentHtml += `</div>`;
    }

    modal.innerHTML = `
      <div class="bg-white rounded-2xl p-6 w-[400px] shadow-2xl relative">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined">shopping_cart</span>
          Daftar Hold / Cart
        </h2>
        <button id="close-cart-btn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        ${contentHtml}
      </div>
    `;

    document.body.appendChild(modal);

    document.getElementById('close-cart-btn').addEventListener('click', () => {
      modal.remove();
    });

    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.remove();
    });
  }

  function showHistoryModal() {
    // Check existing modal
    if (document.getElementById('history-modal')) return;

    // Fetch transaction history via AJAX
    fetch('/kasir/api/transactions/history')
      .then(response => response.json())
      .then(data => {
        const modal = document.createElement('div');
        modal.id = 'history-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        
        let tableRows = '';
        if (data.transactions && data.transactions.length > 0) {
          tableRows = data.transactions.map(transaction => `
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-3 text-sm font-medium">${transaction.transaction_number}</td>
              <td class="px-4 py-3 text-sm">
                <span class="px-2 py-1 text-xs rounded-full font-medium
                  ${transaction.payment_method === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                  ${transaction.payment_method.toUpperCase()}
                </span>
              </td>
              <td class="px-4 py-3 text-sm font-bold">Rp ${Number(transaction.total_amount).toLocaleString('id-ID')}</td>
              <td class="px-4 py-3 text-sm text-gray-600">${new Date(transaction.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})}</td>
              <td class="px-4 py-3 text-center">
                <button onclick="printReceiptFromHistory(${transaction.id})" class="bg-[#4F2E22] text-white px-3 py-1 rounded text-sm hover:bg-[#3f251b] transition-colors">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                  </svg>
                  Cetak
                </button>
              </td>
            </tr>
          `).join('');
        } else {
          tableRows = `
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Belum ada transaksi hari ini
              </td>
            </tr>
          `;
        }

        modal.innerHTML = `
          <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[80vh] overflow-hidden">
            <div class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] p-6 relative">
              <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                History Transaksi Hari Ini
              </h2>
              <button id="close-history-modal-header" class="absolute top-6 right-6 text-white hover:text-gray-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            
            <div class="p-6 overflow-x-auto max-h-[60vh]">
              <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full">
                  <thead class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] border-b border-gray-200 sticky top-0">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">ORDER ID</th>
                      <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">PEMBAYARAN</th>
                      <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">TOTAL</th>
                      <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">WAKTU</th>
                      <th class="px-4 py-3 text-center text-xs font-bold text-white uppercase">CETAK</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    ${tableRows}
                  </tbody>
                </table>
              </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t">
              <button class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors" id="close-history-modal">
                Tutup
              </button>
            </div>
          </div>
        `;

        document.body.appendChild(modal);

        // Event listeners
        document.getElementById('close-history-modal').addEventListener('click', () => {
          modal.remove();
        });

        document.getElementById('close-history-modal-header').addEventListener('click', () => {
          modal.remove();
        });

        modal.addEventListener('click', (e) => {
          if (e.target === modal) modal.remove();
        });
      })
      .catch(error => {
        console.error('Error fetching history:', error);
        alert('Gagal memuat history transaksi');
      });
  }

  function printReceiptFromHistory(transactionId) {
    // Open receipt in new tab for printing
    const printWindow = window.open(`/admin/orders/${transactionId}/print`, '_blank');
    if (printWindow) {
      printWindow.focus();
    }
  }

  function showSettingsModal() {
    // Check existing modal
    if (document.getElementById('settings-modal')) return;

    // Fetch current user data
    fetch('/api/user/profile')
      .then(response => response.json())
      .then(user => {
        const modal = document.createElement('div');
        modal.id = 'settings-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        
        modal.innerHTML = `
          <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <div class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] p-6 relative">
              <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                </svg>
                Pengaturan Profile
              </h2>
              <button id="close-settings-modal-header" class="absolute top-6 right-6 text-white hover:text-gray-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[70vh]">
              <form id="settings-form" class="space-y-6">
                <!-- Profile Photo Section -->
                <div class="text-center">
                  <div class="relative inline-block">
                    <img id="profile-preview" src="${user.profile_photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&color=7F9CF5&background=EBF4FF'}" 
                         alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                    <div class="absolute bottom-0 right-0 flex gap-1">
                      <label for="profile-photo" class="bg-[#4F2E22] text-white p-2 rounded-full cursor-pointer hover:bg-[#3f251b] transition-colors" title="Upload Foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <input type="file" id="profile-photo" name="profile_photo" accept="image/*" class="hidden">
                      </label>
                      <button type="button" id="camera-btn" class="bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition-colors" title="Ambil Foto Kamera">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                  <p class="text-sm text-gray-500 mt-2">Upload atau ambil foto kamera</p>
                </div>

                <!-- Hidden camera input -->
                <input type="file" id="camera-input" accept="image/*" capture="camera" class="hidden">

                <!-- User Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="${user.name}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" value="${user.username}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                  <input type="email" name="email" value="${user.email}" required
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                </div>

                <!-- Password Section -->
                <div class="border-t pt-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Ganti Password</h3>
                  <div class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                      <input type="password" name="password" 
                             class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                      <input type="password" name="password_confirmation" 
                             class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                    </div>
                  </div>
                </div>

                <!-- Current Password Required -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini *</label>
                  <input type="password" name="current_password" required
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                  <p class="text-xs text-gray-500 mt-1">Required untuk konfirmasi perubahan</p>
                </div>
              </form>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t flex gap-3">
              <button type="button" class="flex-1 bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors" id="close-settings-modal">
                Batal
              </button>
              <button type="submit" form="settings-form" class="flex-1 bg-[#4F2E22] text-white px-4 py-2 rounded-lg hover:bg-[#3f251b] transition-colors">
                Simpan Perubahan
              </button>
            </div>
          </div>
        `;

        document.body.appendChild(modal);

        // Event listeners
        document.getElementById('close-settings-modal').addEventListener('click', () => {
          modal.remove();
        });

        document.getElementById('close-settings-modal-header').addEventListener('click', () => {
          modal.remove();
        });

        // Profile photo preview
        document.getElementById('profile-photo').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
              document.getElementById('profile-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
          }
        });

        // Camera button functionality
        document.getElementById('camera-btn').addEventListener('click', function() {
          // Check if device supports camera
          if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Try to access camera directly
            navigator.mediaDevices.getUserMedia({ 
              video: { facingMode: 'user' } // Front camera by default
            })
            .then(function(stream) {
              // Create video element to show camera feed
              const video = document.createElement('video');
              video.srcObject = stream;
              video.autoplay = true;
              
              // Create camera modal
              const cameraModal = document.createElement('div');
              cameraModal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
              cameraModal.innerHTML = `
                <div class="bg-white rounded-xl p-4 max-w-sm w-full mx-4">
                  <h3 class="text-lg font-semibold mb-4 text-center">Ambil Foto Profile</h3>
                  <div class="relative">
                    <video id="camera-video" class="w-full rounded-lg" autoplay></video>
                    <div class="absolute inset-0 flex items-center justify-center">
                      <div class="w-32 h-32 border-4 border-white rounded-full"></div>
                    </div>
                  </div>
                  <div class="flex gap-2 mt-4">
                    <button id="capture-btn" class="flex-1 bg-[#4F2E22] text-white py-2 rounded-lg hover:bg-[#3f251b] transition-colors">
                      📸 Ambil Foto
                    </button>
                    <button id="cancel-camera-btn" class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition-colors">
                      Batal
                    </button>
                  </div>
                </div>
              `;
              
              document.body.appendChild(cameraModal);
              
              // Set video stream
              const videoElement = cameraModal.querySelector('#camera-video');
              videoElement.srcObject = stream;
              
              // Capture photo
              document.getElementById('capture-btn').addEventListener('click', function() {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);
                
                // Convert to blob and update preview
                canvas.toBlob(function(blob) {
                  const url = URL.createObjectURL(blob);
                  document.getElementById('profile-preview').src = url;
                  
                  // Update file input for form submission
                  const file = new File([blob], 'camera-photo.jpg', { type: 'image/jpeg' });
                  const dataTransfer = new DataTransfer();
                  dataTransfer.items.add(file);
                  document.getElementById('profile-photo').files = dataTransfer.files;
                  
                  // Stop camera stream
                  stream.getTracks().forEach(track => track.stop());
                  
                  // Remove camera modal
                  cameraModal.remove();
                }, 'image/jpeg');
              });
              
              // Cancel camera
              document.getElementById('cancel-camera-btn').addEventListener('click', function() {
                stream.getTracks().forEach(track => track.stop());
                cameraModal.remove();
              });
              
            })
            .catch(function(error) {
              console.error('Camera access denied:', error);
              // Fallback to file input if camera access denied
              document.getElementById('camera-input').click();
            });
          } else {
            // Fallback to file input if getUserMedia not supported
            document.getElementById('camera-input').click();
          }
        });

        // Camera input preview
        document.getElementById('camera-input').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
              document.getElementById('profile-preview').src = e.target.result;
              // Also update the file input for form submission
              const dataTransfer = new DataTransfer();
              dataTransfer.items.add(file);
              document.getElementById('profile-photo').files = dataTransfer.files;
            };
            reader.readAsDataURL(file);
          }
        });

        // Form submission
        document.getElementById('settings-form').addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.textContent;
          
          submitBtn.textContent = 'Menyimpan...';
          submitBtn.disabled = true;

          // Add method override for PUT/PATCH if needed
          formData.append('_method', 'POST');

          fetch('/api/user/profile', {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json'
            },
            credentials: 'same-origin'
          })
          .then(response => {
            console.log('Response status:', response.status);
            return response.json().then(data => {
              if (!response.ok) {
                throw new Error(data.message || 'Server error');
              }
              return data;
            });
          })
          .then(data => {
            console.log('Success response:', data);
            if (data.success) {
              alert('Profile berhasil diperbarui!');
              modal.remove();
              // Refresh page to show updated profile
              setTimeout(() => location.reload(), 500);
            } else {
              alert(data.message || 'Gagal memperbarui profile');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
          })
          .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
          });
        });

        modal.addEventListener('click', (e) => {
          if (e.target === modal) modal.remove();
        });
      })
      .catch(error => {
        console.error('Error fetching profile:', error);
        alert('Gagal memuat data profile');
      });
  }

  // Fungsi global agar bisa dipanggil dari HTML string onclick
  window.restoreOrder = function(index) {
    const savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];
    if (index >= 0 && index < savedOrders.length) {
      if (orders.length > 0) {
        if (!confirm('Meja kasir sedang terisi. Timpa pesanan saat ini dengan pesanan dari Cart?')) {
          return;
        }
      }
      
      const selectedOrder = savedOrders[index];
      orders = selectedOrder.items.map(item => ({ ...item })); // copy deep
      
      // Hapus dari saved
      savedOrders.splice(index, 1);
      localStorage.setItem('heldOrders', JSON.stringify(savedOrders));
      
      renderOrderList();
      updateTotal();
      updateCartBadge(); // Update badge setelah restore
      
      // Tutup modal cart
      const modal = document.getElementById('cart-modal');
      if (modal) modal.remove();
    }
  };
  
  // Initialize - tampilkan pesan kosong dan update badge
  renderOrderList();
  updateCartBadge(); // Cek localStorage saat load
  
  // All menu button - show all products
  const showAllMenuBtn = document.getElementById('show-all-menu');
  if (showAllMenuBtn) {
    showAllMenuBtn.addEventListener('click', () => {
      // Reset all category filters
      document.querySelectorAll('.category-radio').forEach(radio => {
        radio.checked = false;
      });
      
      // Show all products
      document.querySelectorAll('.menu-card').forEach(card => {
        card.style.display = 'flex';
      });
      
      // Clear search
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.value = '';
      }
    });
  }
  
  // Search functionality
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const search = this.value.toLowerCase();
      document.querySelectorAll('.menu-card').forEach(card => {
        const name = card.dataset.productName.toLowerCase();
        if (name.includes(search)) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }
  
  // Category filter functionality
  document.querySelectorAll('.category-radio').forEach(radio => {
    radio.addEventListener('change', function() {
      const selectedCategory = this.dataset.category;
      
      document.querySelectorAll('.menu-card').forEach(card => {
        const productCategoryId = card.dataset.categoryId;
        
        if (!selectedCategory || selectedCategory === 'all') {
          card.style.display = 'flex';
        } else {
          // Filter berdasarkan category_id dari database
          if (selectedCategory === productCategoryId) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        }
      });
    });
  });
</script>
</section>
</section>

<!-- Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-20"></div>

<script>
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const sidebar = document.getElementById('sidebar');
    const closeSidebarBtn = document.getElementById('close-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    hamburgerBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    closeSidebarBtn.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
</body>
</html>
