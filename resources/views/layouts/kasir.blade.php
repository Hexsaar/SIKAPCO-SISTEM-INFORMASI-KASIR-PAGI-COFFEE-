{{-- resources/views/layouts/kasir.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir - POS System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @stack('styles')
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #4F2E22;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3e251b;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Simple Navbar untuk Kasir -->
    <nav class="bg-[#4F2E22] text-white p-3 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h1 class="text-xl font-bold">Kasir - POS System</h1>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Profile Section -->
                <div class="hidden md:flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-300">{{ auth()->user()->username }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-center w-9 h-9 rounded-full overflow-hidden ring-2 ring-white/30 hover:ring-white transition-all">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="w-full h-full object-cover">
                        @endif
                    </a>
                </div>
                
                <!-- Mobile Profile -->
                <a href="{{ route('profile.edit') }}" class="md:hidden flex items-center justify-center w-8 h-8 rounded-full overflow-hidden ring-2 ring-white/30">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="w-full h-full object-cover">
                    @endif
                </a>
                
                <span class="bg-white text-[#4F2E22] text-xs px-2 py-1 rounded-full">Kasir</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm hover:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>