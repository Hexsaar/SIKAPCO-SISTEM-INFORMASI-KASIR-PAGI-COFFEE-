<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pagi Coffee</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="{{ asset('assets/Logo.png') }}" type="image/png">

  <style>
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeUp {
      animation: fadeUp .8s ease-out both;
    }
  </style>
</head>

<body class="bg-white overflow-x-hidden">

<!-- ================= HEADER ================= -->
<header class="w-full bg-white sticky top-0 z-50 shadow-sm">

  <div class="max-w-7xl mx-auto px-4 md:px-6 py-4 flex items-center justify-between">

    <!-- LOGO -->
    <img src="{{ asset('assets/Logo.png') }}"
         alt="Pagi Coffee"
         class="w-20 h-10 object-cover">

    <!-- DESKTOP MENU -->
    <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
      <a href="#Home" class="hover:text-orange-500">Home</a>
      <a href="#menu" class="hover:text-orange-500">Menu</a>
      <a href="#about" class="hover:text-orange-500">About</a>
      <a href="#gallery" class="hover:text-orange-500">Gallery</a>
    </nav>

    <!-- HAMBURGER -->
    <button id="menuBtn" class="md:hidden text-3xl">
      ☰
    </button>

  </div>
</header>

<!-- ================= OVERLAY ================= -->
<div id="overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition duration-300 z-40">
</div>

<!-- ================= SIDEBAR MENU ================= -->
<aside id="sidebar"
       class="fixed top-0 right-0 h-full w-64 bg-white shadow-xl
              translate-x-full transition-transform duration-300 z-50">

  <!-- HEADER SIDEBAR -->
  <div class="flex items-center justify-between p-4 border-b">
    <h2 class="font-bold text-lg">Menu</h2>
    <button id="closeBtn" class="text-2xl">✕</button>
  </div>

  <!-- MENU LIST -->
  <nav class="flex flex-col p-6 gap-6 text-sm font-medium">
    <a href="/Home" class="hover:text-orange-500">Home</a>
    <a href="/menu" class="hover:text-orange-500">Menu</a>
    <a href="/about" class="hover:text-orange-500">About</a>
    <a href="/gallery" class="hover:text-orange-500">Gallery</a>
  </nav>

</aside>

<!-- ================= HERO ================= -->
<div id="Home" class="flex justify-center px-4 md:px-0 py-10">

  <div class="
      w-full md:w-[1200px]
      aspect-[16/9] md:aspect-[21/9]
      rounded-[24px] md:rounded-[50px]
      overflow-hidden
      relative
      shadow-xl
  ">

    <!-- IMAGE -->
    <img
      src="https://i.pinimg.com/1200x/e0/09/08/e00908e9d7c4919287edda4d863e23b4.jpg"
      class="w-full h-full object-cover"
    >

    <!-- OVERLAY -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- CONTENT -->
    <div class="absolute inset-6 md:inset-12 flex flex-col justify-center text-white">

      <h1 class="text-2xl md:text-6xl font-bold mb-4 leading-tight">
        Coffee enak,<br>lezat bergizi
      </h1>

      <p class="text-xs md:text-sm max-w-md mb-6">
        Kopi terbaik pilihan barista.
      </p>

      <a href="#menu"
         class="bg-white text-black px-6 py-2 rounded-full w-fit inline-block
                hover:scale-105 transition">
        Let's see!
      </a>

    </div>
  </div>
</div>


  <div class="w-full flex justify-center my-10">
    <div class="w-[90%] border-t border-black/40" style="height:0.5px"></div>
  </div>

  <!-- ================= MENU ================= -->
<div id="menu" class="px-4 md:px-12 py-12">
  <h2 class="text-2xl font-bold mb-6">MENU</h2>

  <!-- FILTER BUTTON -->
  <div class="flex flex-wrap gap-3 mb-8">
    <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="all">ALL</button>
    <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="best-seller">BEST SELLER</button>
    <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="coffee">COFFEE</button>
    <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="coffee-milk">COFFEE MILK</button>
    <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="non-coffee">NON COFFEE</button>
    <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="bottle">BOTTLE</button>
    <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="snack">SNACK</button>
  </div>

  <!-- MENU GRID -->
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">

    <!-- COFFEE -->
    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee" data-name="Americano" data-price="12.000" data-image="{{ asset('assets/menu/Americano.jpeg') }}" data-desc="Espresso dengan air panas, rasa bold & clean." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/Americano.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Americano</h3>
        <p class="text-sm">Rp12.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee" data-name="Americano Lemon" data-price="15.000" data-image="{{ asset('assets/menu/Americano.jpeg') }}" data-desc="Kopi americano segar dengan sentuhan lemon yang refreshing.." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/Americano.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Americano Lemon</h3>
        <p class="text-sm">Rp15.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee" data-name="V60" data-price="18.000" data-image="{{ asset('assets/menu/v60.jpeg') }}" data-desc="Kopi V60 yang khas dengan rasa yang tajam dan kompleks." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/v60.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">V60</h3>
        <p class="text-sm">Rp18.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee" data-name="Vietnam Drip" data-price="15.000" data-image="{{ asset('assets/menu/vietnamdrip.jpeg') }}" data-desc="Kopi vietnam drip yang creamy dan lembut dengan kombinasi sempurna antara espresso dan steamed milk." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/vietnamdrip.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Vietnam Drip</h3>
        <p class="text-sm">Rp15.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <!-- COFFEE MILK -->
    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee-milk" data-name="Pagi Coffee" data-price="14.000" data-image="{{ asset('assets/menu/capucino.jpeg') }}" data-desc="Kopi susu signature dari Pagi Coffee dengan rasa yang berkarakter, menggunakan gula aren asli, dan kopi robusta asli Indonesia." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/capucino.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Pagi Coffee</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee-milk" data-name="Latte" data-price="14.000" data-image="{{ asset('assets/menu/capucino.jpeg') }}" data-desc="Minuman latte premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/capucino.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Latte</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee-milk" data-name="Rum" data-price="14.000" data-image="{{ asset('assets/menu/capucino.jpeg') }}" data-desc="Minuman rum premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/capucino.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Rum</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee-milk" data-name="Baileys" data-price="14.000" data-image="{{ asset('assets/menu/capucino.jpeg') }}" data-desc="Minuman baileys premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/capucino.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Baileys</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee-milk" data-name="Butterscotch" data-price="14.000" data-image="{{ asset('assets/menu/capucino.jpeg') }}" data-desc="Minuman butterscotch premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/capucino.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Butterscotch</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee-milk" data-name="Cappucino" data-price="14.000" data-image="{{ asset('assets/menu/capucino.jpeg') }}" data-desc="Minuman cappucino premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/capucino.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Cappucino</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <!-- NON COFFEE -->
    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="non-coffee" data-name="Matcha" data-price="14.000" data-image="{{ asset('assets/menu/matcha.jpeg') }}" data-desc="Minuman matcha premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/matcha.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Matcha</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="non-coffee" data-name="Chocolate" data-price="14.000" data-image="{{ asset('assets/menu/chocolate.jpeg') }}" data-desc="Minuman chocolate premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/chocolate.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Chocolate</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <!-- BOTTLE -->
    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="bottle" data-name="Pagi Coffee 250ml" data-price="14.000" data-image="{{ asset('assets/menu/pagicoffee250ml.jpeg') }}" data-desc="Minuman pagi coffee 250ml premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/pagicoffee250ml.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Pagi Coffee 250ml</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="bottle" data-name="Pagi Coffee 500ml" data-price="14.000" data-image="{{ asset('assets/menu/pagicoffee500ml.jpeg') }}" data-desc="Minuman pagi coffee 500ml premium yang menyegarkan dengan rasa yang kaya dan tekstur yang halus." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/pagicoffee500ml.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Pagi Coffee 500ml</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <!-- SNACK -->
    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="snack" data-name="Singkong Goreng" data-price="10.000" data-image="{{ asset('assets/menu/singkonggoreng.jpeg') }}" data-desc="Singkong goreng yang lezat dan gurih, sempurna menemani kopi Anda." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/singkonggoreng.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Singkong Goreng</h3>
        <p class="text-sm">Rp10.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="snack" data-name="Tahu Baso Ikan" data-price="10.000" data-image="{{ asset('assets/menu/tahubasoikan.jpeg') }}" data-desc="Tahu baso ikan yang lezat dan gurih, sempurna menemani kopi Anda." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/tahubasoikan.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Tahu Baso Ikan</h3>
        <p class="text-sm">Rp10.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="snack" data-name="Cireng Isi Ayam Pedas" data-price="10.000" data-image="{{ asset('assets/menu/cirengisi.jpeg') }}" data-desc="Cireng isi ayam pedas yang renyah dan lezat, sempurna menemani kopi Anda." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/cirengisi.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Cireng Isi Ayam Pedas</h3>
        <p class="text-sm">Rp10.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="snack" data-name="Pastel" data-price="10.000" data-image="{{ asset('assets/menu/Pastel.jpeg') }}" data-desc="Pastel yang renyah dan lezat, sempurna menemani kopi Anda." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/Pastel.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Pastel</h3>
        <p class="text-sm">Rp10.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

    <!-- BEST SELLER -->
    <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="best-seller" data-name="Pagi Coffee" data-price="14.000" data-image="{{ asset('assets/menu/capucino.jpeg') }}" data-desc="Kopi susu signature dari Pagi Coffee dengan rasa yang berkarakter, menggunakan gula aren asli, dan kopi robusta asli Indonesia." onclick="showDetail(this)">
      <img src="{{ asset('assets/menu/capucino.jpeg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
      <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
        <h3 class="font-semibold">Pagi Coffee</h3>
        <p class="text-sm">Rp14.000</p>
        <div class="text-yellow-400">★★★★★</div>
        <p class="text-xs mt-2">➜ Lihat Detail</p>
        <button class="absolute top-2 right-2 text-white" onclick="showDetail(this.parentElement.parentElement)">➜</button>
      </div>
    </div>

  </div>
</div>

<!-- MODAL DETAIL - LAYOUT SEPERTI SCREENSHOT -->
<div id="detailModal" class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-2xl w-full relative animate-fadeUp overflow-hidden">

    <!-- CLOSE BUTTON -->
    <button id="closeModal" class="absolute top-4 right-4 text-2xl z-10 bg-white/80 w-8 h-8 rounded-full flex items-center justify-center hover:bg-white">✕</button>

    <!-- CONTENT WRAPPER - FLEX UNTUK MOBILE & DESKTOP -->
    <div class="flex flex-col md:flex-row">

      <!-- LEFT: IMAGE SECTION -->
      <div class="md:w-1/2 h-64 md:h-auto bg-gray-100">
        <img id="modalImg" src="" alt="" class="w-full h-full object-cover">
      </div>

      <!-- RIGHT: DETAIL SECTION -->
      <div class="md:w-1/2 p-6 flex flex-col">

        <!-- PRODUCT NAME -->
        <h3 id="modalName" class="text-3xl font-bold text-gray-800 mb-3"></h3>

        <!-- RATING -->
        <div class="flex items-center gap-2 mb-4">
          <div class="flex text-yellow-400 text-lg">
            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
          </div>
          <span class="text-sm text-gray-500">5.0 (120+ reviews)</span>
        </div>

        <!-- DESCRIPTION -->
        <div class="mb-6">
          <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi</h4>
          <p id="modalDesc" class="text-gray-600 text-sm leading-relaxed"></p>
        </div>

        <!-- PRICE -->
        <div class="mb-6">
          <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Harga</h4>
          <p id="modalPrice" class="text-3xl font-bold text-[#5A3A2E]"></p>
        </div>

        <!-- ORDER BUTTONS -->
        <div class="space-y-3 mt-auto">
          <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Pesan via</h4>

          <a href="#" target="_blank" class="flex items-center justify-between w-full bg-[#00AA13] text-white py-3 px-4 rounded-xl hover:bg-[#00880f] transition group">
            <span class="flex items-center gap-2">
              <span class="text-xl">🛵</span>
              <span class="font-semibold">GoFood</span>
            </span>
            <span class="text-sm opacity-80 group-hover:opacity-100">→</span>
          </a>

          <a href="#" target="_blank" class="flex items-center justify-between w-full bg-[#EE4D2D] text-white py-3 px-4 rounded-xl hover:bg-[#d13d1f] transition group">
            <span class="flex items-center gap-2">
              <span class="text-xl">🍔</span>
              <span class="font-semibold">GrabFood</span>
            </span>
            <span class="text-sm opacity-80 group-hover:opacity-100">→</span>
          </a>

          <a href="#" target="_blank" class="flex items-center justify-between w-full bg-[#6B21A5] text-white py-3 px-4 rounded-xl hover:bg-[#551a84] transition group">
            <span class="flex items-center gap-2">
              <span class="text-xl">🚴</span>
              <span class="font-semibold">ShopeeFood</span>
            </span>
            <span class="text-sm opacity-80 group-hover:opacity-100">→</span>
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<div class="w-full flex justify-center my-10">
    <div class="w-[90%] border-t border-black/40" style="height:0.5px"></div>
  </div>


  <!-- ABOUT SECTION 1 -->
<div id="about" class="w-full px-4 md:px-12 py-12">
  <div class="flex flex-col md:flex-row gap-8 md:gap-12 items-center">

    <!-- IMAGE -->
    <div class="flex-shrink-0 w-full md:w-auto">
      <img
        src="{{ asset('assets/gallery/about.jpeg') }}"
        alt="Pagi Coffee"
        class="rounded-[11px] w-full md:w-[400px] h-[260px] md:h-[400px] object-cover shadow-lg">
    </div>

    <!-- TEXT -->
    <div class="flex-1 text-center md:text-left">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
        About Pagi Coffee
      </h2>
      <p class="text-gray-700 text-sm leading-relaxed">
        Pagi Coffee adalah kafe modern yang menyajikan kopi terbaik dengan kualitas premium.
        Kami berkomitmen untuk memberikan pengalaman minum kopi yang tak terlupakan kepada
        setiap pelanggan kami.
      </p>
    </div>

  </div>
</div>

<!-- ABOUT SECTION 2 (REVERSED) -->
<div class="w-full px-4 md:px-12 py-12">
  <div class="flex flex-col-reverse md:flex-row gap-8 md:gap-12 items-center">

    <!-- TEXT -->
    <div class="flex-1 text-center md:text-left">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
        Why Choose Pagi Coffee?
      </h2>
      <p class="text-gray-700 text-sm leading-relaxed mb-4">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor
        incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.
      </p>

      <!-- RATING -->
      <div class="flex justify-center md:justify-start items-center gap-2">
        <div class="text-yellow-400 text-xl">★★★★★</div>
        <span class="text-gray-600 text-sm font-semibold">
          5.0/5 - Recommended
        </span>
      </div>
    </div>

    <!-- IMAGE -->
    <div class="flex-shrink-0 w-full md:w-auto">
      <img
        src="{{ asset('assets/gallery/about2.jpeg') }}"
        alt="Pagi Coffee Experience"
        class="rounded-[11px] w-full md:w-[400px] h-[260px] md:h-[400px] object-cover shadow-lg">
    </div>

  </div>
</div>


<div class="w-full flex justify-center my-10">
    <div class="w-[90%] border-t border-black/40" style="height:0.5px"></div>
  </div>

<!-- ===== GALLERY SECTION ===== -->
<div id="gallery" class="w-full flex flex-col items-start px-4 md:px-12 py-4">
  <h2 class="text-2xl font-bold text-gray-800 mb-6">GALLERY</h2>
</div>

<div class="w-full flex justify-start px-4 md:px-12 py-6 relative">

  <!-- LEFT ARROW -->
  <button id="galleryPrev"
    class="absolute left-0 top-1/2 -translate-y-1/2 z-10
           bg-black/50 hover:bg-black/70 text-white
           p-2 rounded-full md:hidden">
    ‹
  </button>

  <!-- WRAPPER -->
  <div class="gallery-scroll w-full md:grid md:grid-cols-4 gap-3">

    <!-- ITEM -->
    <img src="{{ asset('assets/gallery/about.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/about2.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto1.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto2.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto3.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto4.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto5.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto6.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto7.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/foto8.jpeg') }}"
      class="img gallery-img" />

    <img src="{{ asset('assets/gallery/about1.jpeg') }}"
      class="img gallery-img" />

  </div>

  <!-- RIGHT ARROW -->
  <button id="galleryNext"
    class="absolute right-0 top-1/2 -translate-y-1/2 z-10
           bg-black/50 hover:bg-black/70 text-white
           p-2 rounded-full md:hidden">
    ›
  </button>
</div>

<!-- ===== STYLE ===== -->
<style>

/* === DESKTOP GRID IMAGE === */
.gallery-img {
  width: 100%;
  aspect-ratio: 1 / 1;     /* ← bikin kotak sempurna */
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 10px 20px rgba(0,0,0,.15);
}

/* === MOBILE SCROLL === */
@media (max-width: 768px) {

  .gallery-scroll {
    display: flex;
    overflow-x: auto;
    gap: 12px;
    scroll-snap-type: x mandatory;
  }

  .gallery-scroll .gallery-img {
    min-width: 240px;      /* ukuran kotak mobile */
    aspect-ratio: 1 / 1;   /* tetap kotak */
    flex-shrink: 0;
    scroll-snap-align: start;
  }

  .gallery-scroll::-webkit-scrollbar {
    display: none;
  }
}
</style>

<!-- ===== ARROW SCRIPT ===== -->
<script>

</script>





<div class="w-full flex justify-center my-10">
    <div class="w-[90%] border-t border-black/40" style="height:0.5px"></div>
  </div>

    <section class="max-w-7xl mx-auto px-6 py-28">
    <h2 class="text-5xl font-bold text-center leading-tight">
        Coba <i>aplikasi</i> kami untuk menikmati <i>kenangan</i><br>
        menjadi lebih <i>berwarna</i> & menyenangkan.
    </h2>

    <p class="mt-10 max-w-lg text-gray-600 text-center mx-auto">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        Donec fermentum nisl malesuada interdum.
    </p>
</section>

<!-- FOOTER SECTION -->
<footer class="bg-[#5a2e1a] text-white py-12 mt-8">
  <div class="max-w-6xl mx-auto px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

      <!-- LOGO & INFO -->
      <div>
        <div class="flex items-center gap-2">
          <img src="{{ asset('assets/Logo.png') }}" alt="Pagi Coffee" class="w-20 h-10 object-cover shadow-md">
        </div>
        <p class="text-xs text-gray-300 mb-4">
          Kedai kopi rumahan di komplek perumahan. Nyaman buat ngobrol santai sama teman atau tetangga.
        </p>
        <p class="text-xs text-gray-300 mb-2">IG: @pagicoffee.id</p>
        <p class="text-xs text-gray-300">WA: 0812-3456-7890</p>
      </div>

      <!-- GET IN TOUCH -->
      <div>
        <h4 class="font-bold mb-4 text-sm">Lokasi</h4>
        <ul class="text-xs text-gray-300 space-y-2">
          <li>📍 Perum Griya Asri Blok A5 No.12</li>
          <li>🗺️ Rt 03 Rw 08, Kel. Sukasenang</li>
        </ul>
      </div>

      <!-- SERVICES -->
      <div>
        <h4 class="font-bold mb-4 text-sm">Jam Buka</h4>
        <ul class="text-xs text-gray-300 space-y-2">
          <li>🕒 Senin - Jumat: 08.00 - 18.00</li>
          <li>🕒 Sabtu, Minggu, dan Tgl merah TUTUP</li>
        </ul>
      </div>

      <!-- COMPANY -->
      <div>
        <div class="flex justify-between items-start">
          <div>
            <h4 class="font-bold mb-4 text-sm">Menu Andalan</h4>
            <ul class="text-xs text-gray-300 space-y-2">
              <li>☕ Pagi Coffee</li>
              <li>🥤 Vietnam Drip</li>
              <li>🍵 Matcha Latte</li>
              <li>🍟 Singkong Goreng</li>
            </ul>
          </div>
        </div>
      </div>

    </div>

    <!-- DIVIDER -->
    <div class="border-t border-white/20 mt-8 pt-6">
      <p class="text-xs text-white text-center">
        © 2026 pagicoffee.id
      </p>
      <p class="text-xs text-white/50 text-center mt-2">
        <a href="{{ route('login') }}" class="hover:text-white/80 transition">Admin Access</a>
      </p>
    </div>
  </div>
</footer>


<!-- ================= JS ================= -->
<script>

const menuBtn = document.getElementById("menuBtn");
const closeBtn = document.getElementById("closeBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const buttons = document.querySelectorAll(".filter-btn");
const cards = document.querySelectorAll(".menu-card");

  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      const filter = btn.dataset.filter;

      buttons.forEach(b => b.classList.remove("bg-[#5A2E1A]", "text-white"));
      btn.classList.add("bg-[#5A2E1A]", "text-white");

      cards.forEach(card => {
        if (filter === "all" || card.dataset.category === filter) {
          card.classList.remove("hidden");
        } else {
          card.classList.add("hidden");
        }
      });
    });
  });

// OPEN
menuBtn.addEventListener("click", () => {
  sidebar.classList.remove("translate-x-full");
  overlay.classList.remove("opacity-0", "pointer-events-none");
});

// CLOSE BTN
closeBtn.addEventListener("click", closeMenu);

// CLOSE OVERLAY
overlay.addEventListener("click", closeMenu);

function closeMenu() {
  sidebar.classList.add("translate-x-full");
  overlay.classList.add("opacity-0", "pointer-events-none");
}

  function showDetail(card) {
  const name = card.getAttribute('data-name');
  const price = card.getAttribute('data-price');
  const desc = card.getAttribute('data-desc');
  const image = card.getAttribute('data-image');

  document.getElementById('modalName').innerText = name;
  document.getElementById('modalPrice').innerText = price;
  document.getElementById('modalDesc').innerText = desc;
  document.getElementById('modalImg').src = image;

  document.getElementById('detailModal').classList.remove('hidden');
}

document.getElementById('closeModal').onclick = function() {
  document.getElementById('detailModal').classList.add('hidden');
};

const scrollContainer = document.querySelector(".gallery-scroll");

document.getElementById("galleryNext")
  .onclick = () => scrollContainer.scrollBy({ left: 260, behavior: "smooth" });

document.getElementById("galleryPrev")
  .onclick = () => scrollContainer.scrollBy({ left: -260, behavior: "smooth" });

// Gallery Navigation
const galleryScroll = document.querySelector('.gallery-scroll');
const galleryPrev = document.getElementById('galleryPrev');
const galleryNext = document.getElementById('galleryNext');

if (galleryPrev && galleryNext && galleryScroll) {
  const scrollAmount = 280; // 260px card + 12px gap

  galleryPrev.addEventListener('click', () => {
    galleryScroll.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
  });

  galleryNext.addEventListener('click', () => {
    galleryScroll.scrollBy({ left: scrollAmount, behavior: 'smooth' });
  });
}
</script>

</body>
</html>
