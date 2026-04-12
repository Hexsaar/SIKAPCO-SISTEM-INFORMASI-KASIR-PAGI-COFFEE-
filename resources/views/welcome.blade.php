<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagi Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('assets/Logo.png') }}" type="image/png">
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeUp {
            animation: fadeUp 0.8s ease-out both;
        }

        .gallery-img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 768px) {
            .gallery-scroll {
                display: flex;
                overflow-x: auto;
                gap: 12px;
                scroll-snap-type: x mandatory;
                padding-bottom: 6px;
            }

            .gallery-scroll .gallery-img {
                min-width: 240px;
                flex-shrink: 0;
                scroll-snap-align: start;
            }

            .gallery-scroll::-webkit-scrollbar {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-white overflow-x-hidden text-slate-900">
    <header class="w-full bg-white/90 backdrop-blur sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-4 flex items-center justify-between">
            <a href="#home" class="flex items-center gap-2">
                <img src="{{ asset('assets/Logo.png') }}" alt="Pagi Coffee" class="w-20 h-10 object-cover">
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#home" class="hover:text-orange-500">Home</a>
                <a href="#menu" class="hover:text-orange-500">Menu</a>
                <a href="#about" class="hover:text-orange-500">About</a>
                <a href="#gallery" class="hover:text-orange-500">Gallery</a>
            </nav>

            <button id="menuBtn" class="md:hidden text-3xl leading-none" aria-label="Buka menu">
                &#9776;
            </button>
        </div>
    </header>

    <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition duration-300 z-40"></div>

    <aside id="sidebar" class="fixed top-0 right-0 h-full w-72 max-w-[85vw] bg-white shadow-xl translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center justify-between p-4 border-b">
            <h2 class="font-bold text-lg">Menu</h2>
            <button id="closeBtn" class="text-2xl leading-none" aria-label="Tutup menu">&times;</button>
        </div>

        <nav class="flex flex-col p-6 gap-6 text-sm font-medium">
            <a href="#home" class="hover:text-orange-500 mobile-link">Home</a>
            <a href="#menu" class="hover:text-orange-500 mobile-link">Menu</a>
            <a href="#about" class="hover:text-orange-500 mobile-link">About</a>
            <a href="#gallery" class="hover:text-orange-500 mobile-link">Gallery</a>
        </nav>
    </aside>

    <section id="home" class="flex justify-center px-4 md:px-0 py-10">
        <div class="w-full md:w-[1200px] aspect-[16/9] md:aspect-[21/9] rounded-[24px] md:rounded-[50px] overflow-hidden relative shadow-xl">
            <img src="https://i.pinimg.com/1200x/e0/09/08/e00908e9d7c4919287edda4d863e23b4.jpg" alt="Hero" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="absolute inset-6 md:inset-12 flex flex-col justify-center text-white">
                <h1 class="text-2xl md:text-6xl font-bold mb-4 leading-tight">
                    Coffee enak,<br>lezat bergizi
                </h1>
                <p class="text-xs md:text-sm max-w-md mb-6">
                    Kopi terbaik pilihan barista.
                </p>
                <a href="#menu" class="bg-white text-black px-6 py-2 rounded-full w-fit inline-block hover:scale-105 transition">
                    Let's see!
                </a>
            </div>
        </div>
    </section>

    <div class="w-full flex justify-center my-10">
        <div class="w-[90%] border-t border-black/20"></div>
    </div>

    <section id="menu" class="px-4 md:px-12 py-10">
        <h2 class="text-2xl font-bold mb-6">MENU</h2>

        <div class="flex flex-wrap gap-3 mb-8">
            <button class="filter-btn px-4 py-2 border rounded-full text-xs bg-[#5A2E1A] text-white" data-filter="all">ALL</button>
            <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="coffee">COFFEE</button>
            <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="coffee-milk">COFFEE MILK</button>
            <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="non-coffee">NON COFFEE</button>
            <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="bottle">BOTTLE</button>
            <button class="filter-btn px-4 py-2 border rounded-full text-xs" data-filter="snack">SNACK</button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee" data-name="Americano" data-price="Rp12.000" data-image="{{ asset('assets/menu/Americano.jpeg') }}" data-desc="Espresso dengan air panas, rasa bold dan clean.">
                <img src="{{ asset('assets/menu/Americano.jpeg') }}" alt="Americano" class="w-full h-full object-cover group-hover:scale-110 transition">
                <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
                    <h3 class="font-semibold">Americano</h3>
                    <p class="text-sm">Rp12.000</p>
                    <p class="text-xs mt-2">Lihat Detail</p>
                </div>
            </div>

            <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="coffee-milk" data-name="Pagi Coffee" data-price="Rp14.000" data-image="{{ asset('assets/menu/capucino.jpeg') }}" data-desc="Kopi susu signature dengan gula aren dan robusta Indonesia.">
                <img src="{{ asset('assets/menu/capucino.jpeg') }}" alt="Pagi Coffee" class="w-full h-full object-cover group-hover:scale-110 transition">
                <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
                    <h3 class="font-semibold">Pagi Coffee</h3>
                    <p class="text-sm">Rp14.000</p>
                    <p class="text-xs mt-2">Lihat Detail</p>
                </div>
            </div>

            <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="non-coffee" data-name="Matcha" data-price="Rp14.000" data-image="{{ asset('assets/menu/matcha.jpeg') }}" data-desc="Matcha premium dengan tekstur creamy dan segar.">
                <img src="{{ asset('assets/menu/matcha.jpeg') }}" alt="Matcha" class="w-full h-full object-cover group-hover:scale-110 transition">
                <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
                    <h3 class="font-semibold">Matcha</h3>
                    <p class="text-sm">Rp14.000</p>
                    <p class="text-xs mt-2">Lihat Detail</p>
                </div>
            </div>

            <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="bottle" data-name="Pagi Coffee 250ml" data-price="Rp14.000" data-image="{{ asset('assets/menu/pagicoffee250ml.jpeg') }}" data-desc="Botol praktis untuk dibawa pulang.">
                <img src="{{ asset('assets/menu/pagicoffee250ml.jpeg') }}" alt="Pagi Coffee 250ml" class="w-full h-full object-cover group-hover:scale-110 transition">
                <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
                    <h3 class="font-semibold">Pagi Coffee 250ml</h3>
                    <p class="text-sm">Rp14.000</p>
                    <p class="text-xs mt-2">Lihat Detail</p>
                </div>
            </div>

            <div class="menu-card h-56 md:h-72 rounded-xl overflow-hidden relative group shadow-lg cursor-pointer" data-category="snack" data-name="Singkong Goreng" data-price="Rp10.000" data-image="{{ asset('assets/menu/singkonggoreng.jpeg') }}" data-desc="Snack hangat teman terbaik minum kopi.">
                <img src="{{ asset('assets/menu/singkonggoreng.jpeg') }}" alt="Singkong Goreng" class="w-full h-full object-cover group-hover:scale-110 transition">
                <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition p-4 text-white flex flex-col justify-end">
                    <h3 class="font-semibold">Singkong Goreng</h3>
                    <p class="text-sm">Rp10.000</p>
                    <p class="text-xs mt-2">Lihat Detail</p>
                </div>
            </div>
        </div>
    </section>

    <div id="detailModal" class="hidden fixed inset-0 bg-black/50 z-[100] items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full relative animate-fadeUp overflow-hidden">
            <button id="closeModal" class="absolute top-4 right-4 text-2xl z-10 bg-white/90 w-8 h-8 rounded-full flex items-center justify-center hover:bg-white" aria-label="Tutup">&times;</button>
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2 h-64 md:h-auto bg-gray-100">
                    <img id="modalImg" src="" alt="Menu" class="w-full h-full object-cover">
                </div>
                <div class="md:w-1/2 p-6 flex flex-col">
                    <h3 id="modalName" class="text-3xl font-bold text-gray-800 mb-3"></h3>
                    <p id="modalDesc" class="text-gray-600 text-sm leading-relaxed mb-6"></p>
                    <p id="modalPrice" class="text-3xl font-bold text-[#5A3A2E] mb-6"></p>
                    <div class="space-y-3 mt-auto">
                        <a href="#" target="_blank" class="flex items-center justify-between w-full bg-[#00AA13] text-white py-3 px-4 rounded-xl hover:bg-[#00880f] transition">
                            <span class="font-semibold">GoFood</span><span>&rarr;</span>
                        </a>
                        <a href="#" target="_blank" class="flex items-center justify-between w-full bg-[#EE4D2D] text-white py-3 px-4 rounded-xl hover:bg-[#d13d1f] transition">
                            <span class="font-semibold">GrabFood</span><span>&rarr;</span>
                        </a>
                        <a href="#" target="_blank" class="flex items-center justify-between w-full bg-[#2D7AEE] text-white py-3 px-4 rounded-xl hover:bg-[#1e5ab3] transition">
                            <span class="font-semibold">ShopeeFood</span><span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="about" class="w-full px-4 md:px-12 py-12">
        <div class="flex flex-col md:flex-row gap-8 md:gap-12 items-center">
            <div class="flex-shrink-0 w-full md:w-auto">
                <img src="{{ asset('assets/gallery/about.jpeg') }}" alt="Pagi Coffee" class="rounded-xl w-full md:w-[400px] h-[260px] md:h-[400px] object-cover shadow-lg">
            </div>
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">About Pagi Coffee</h2>
                <p class="text-gray-700 text-sm leading-relaxed">
                    Pagi Coffee adalah kafe modern yang menyajikan kopi pilihan dengan kualitas premium.
                    Kami fokus pada rasa konsisten, suasana nyaman, dan pelayanan yang ramah untuk setiap pelanggan.
                </p>
            </div>
        </div>
    </section>

    <section id="gallery" class="w-full px-4 md:px-12 py-6 relative">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">GALLERY</h2>
        <button id="galleryPrev" class="absolute left-1 top-1/2 -translate-y-1/2 z-10 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full md:hidden" aria-label="Geser kiri">&lsaquo;</button>
        <div class="gallery-scroll w-full md:grid md:grid-cols-4 gap-3">
            <img src="{{ asset('assets/gallery/about.jpeg') }}" alt="Gallery 1" class="gallery-img">
            <img src="{{ asset('assets/gallery/about2.jpeg') }}" alt="Gallery 2" class="gallery-img">
            <img src="{{ asset('assets/gallery/foto.jpeg') }}" alt="Gallery 3" class="gallery-img">
            <img src="{{ asset('assets/gallery/foto1.jpeg') }}" alt="Gallery 4" class="gallery-img">
        </div>
        <button id="galleryNext" class="absolute right-1 top-1/2 -translate-y-1/2 z-10 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full md:hidden" aria-label="Geser kanan">&rsaquo;</button>
    </section>

    <footer class="bg-[#5a2e1a] text-white py-12 mt-8">
        <div class="max-w-6xl mx-auto px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <img src="{{ asset('assets/Logo.png') }}" alt="Pagi Coffee" class="w-20 h-10 object-cover mb-3">
                    <p class="text-xs text-gray-300 mb-2">Kedai kopi rumahan dengan suasana nyaman buat ngobrol santai.</p>
                    <p class="text-xs text-gray-300">IG: @pagicoffee.id</p>
                    <p class="text-xs text-gray-300">WA: 0812-3456-7890</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-sm">Lokasi</h4>
                    <ul class="text-xs text-gray-300 space-y-2">
                        <li>Perum Griya Asri Blok A5 No.12</li>
                        <li>RT 03 RW 08, Sukasenang</li>
                        <li>Depan Masjid Al-Ikhlas</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-sm">Jam Buka</h4>
                    <ul class="text-xs text-gray-300 space-y-2">
                        <li>Senin - Jumat: 08.00 - 18.00</li>
                        <li>Sabtu/Minggu/libur: Tutup</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-sm">Menu Andalan</h4>
                    <ul class="text-xs text-gray-300 space-y-2">
                        <li>Pagi Coffee</li>
                        <li>Vietnam Drip</li>
                        <li>Matcha Latte</li>
                        <li>Singkong Goreng</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-6">
                <p class="text-xs text-gray-400 text-center">&copy; 2026 pagicoffee.id</p>
            </div>
        </div>
    </footer>

    <script>
        const menuBtn = document.getElementById('menuBtn');
        const closeBtn = document.getElementById('closeBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const mobileLinks = document.querySelectorAll('.mobile-link');
        const buttons = document.querySelectorAll('.filter-btn');
        const cards = document.querySelectorAll('.menu-card');

        function closeMenu() {
            sidebar.classList.add('translate-x-full');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('overflow-hidden');
        }

        function openMenu() {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            document.body.classList.add('overflow-hidden');
        }

        menuBtn.addEventListener('click', openMenu);
        closeBtn.addEventListener('click', closeMenu);
        overlay.addEventListener('click', closeMenu);
        mobileLinks.forEach(link => link.addEventListener('click', closeMenu));

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                buttons.forEach(b => b.classList.remove('bg-[#5A2E1A]', 'text-white'));
                btn.classList.add('bg-[#5A2E1A]', 'text-white');

                cards.forEach(card => {
                    card.classList.toggle('hidden', !(filter === 'all' || card.dataset.category === filter));
                });
            });
        });

        const detailModal = document.getElementById('detailModal');
        const closeModalBtn = document.getElementById('closeModal');
        const modalName = document.getElementById('modalName');
        const modalPrice = document.getElementById('modalPrice');
        const modalDesc = document.getElementById('modalDesc');
        const modalImg = document.getElementById('modalImg');

        cards.forEach(card => {
            card.addEventListener('click', () => {
                modalName.textContent = card.dataset.name;
                modalPrice.textContent = card.dataset.price;
                modalDesc.textContent = card.dataset.desc;
                modalImg.src = card.dataset.image;
                detailModal.classList.remove('hidden');
                detailModal.classList.add('flex');
            });
        });

        closeModalBtn.addEventListener('click', () => {
            detailModal.classList.add('hidden');
            detailModal.classList.remove('flex');
        });
        detailModal.addEventListener('click', e => {
            if (e.target === detailModal) {
                detailModal.classList.add('hidden');
                detailModal.classList.remove('flex');
            }
        });

        const galleryScroll = document.querySelector('.gallery-scroll');
        const galleryPrev = document.getElementById('galleryPrev');
        const galleryNext = document.getElementById('galleryNext');

        if (galleryPrev && galleryNext && galleryScroll) {
            const scrollAmount = 260;
            galleryPrev.addEventListener('click', () => galleryScroll.scrollBy({ left: -scrollAmount, behavior: 'smooth' }));
            galleryNext.addEventListener('click', () => galleryScroll.scrollBy({ left: scrollAmount, behavior: 'smooth' }));
        }
    </script>
</body>
</html>
