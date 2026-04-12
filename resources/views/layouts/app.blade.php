{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Product Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <nav class="bg-[#4F2E22] text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Admin Panel</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm">Welcome, Admin</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm hover:text-gray-200">Logout</button>
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