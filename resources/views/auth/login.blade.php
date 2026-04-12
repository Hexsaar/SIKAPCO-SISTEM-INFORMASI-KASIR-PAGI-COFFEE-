<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Login Page</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .coffee-bg {
            background-image: url('https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="flex w-full max-w-4xl h-[600px] bg-white rounded-3xl shadow-2xl overflow-hidden">
        
        <!-- LEFT SIDE - Coffee Background -->
        <div class="hidden md:flex md:w-1/2 coffee-bg relative">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <div class="relative z-10 flex items-start justify-start w-full p-12">
                <h1 class="text-white text-5xl font-bold leading-tight">
                    Welcome<br>
                    to login<br>
                    page
                </h1>
            </div>
        </div>

        <!-- RIGHT SIDE - Login Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-8 bg-gray-50">
            <div class="w-full max-w-sm">
                
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif
                
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <h2 class="text-4xl font-bold text-gray-800 mb-8">Login.</h2>
                
                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-5">
                    @csrf
                    
                    <!-- Username Input -->
                    <div>
                        <label for="email" class="block text-gray-700 font-semibold mb-2">
                            Username
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            placeholder="Masukan Username...."
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-900 focus:border-transparent transition"
                            required
                            autofocus
                            autocomplete="username"
                            pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                            title="Please enter a valid email address"
                        >
                        @error('email')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-gray-700 font-semibold mb-2">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            placeholder="12xxxxxxx"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-900 focus:border-transparent transition"
                            required
                            autocomplete="current-password"
                            pattern="(?=.*\d).{8,}"
                            title="Password must be at least 8 characters and contain at least one number"
                        >
                        @error('password')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Forget Password Link and Remember Me Checkbox -->
                    <div class="flex items-center justify-between">
                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="remember_me"
                                name="remember"
                                class="w-4 h-4 text-amber-900 border-gray-300 rounded focus:ring-amber-900 cursor-pointer"
                            >
                            <label for="remember_me" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                Remember me
                            </label>
                        </div>
                        
                        <!-- Forget Password Link -->
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[#4F2E22] font-semibold text-sm hover:underline">
                                Forget Password?
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-[#4F2E22] text-white py-3 rounded-lg font-semibold hover:bg-[#3A2119] transition duration-300 shadow-md"
                    >
                        Login
                    </button>
                </form>

                <!-- Sign Up Link -->
                <p class="text-center text-sm text-gray-600 mt-5">
                    You don't have an account? 
                    <a href="{{ route('register') }}" class="text-[#4F2E22] font-semibold hover:underline">
                        Sign Up
                    </a>
                </p>

            </div>
        </div>

    </div>

    <script>
        // Security: Prevent XSS attacks
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const inputs = form.querySelectorAll('input[type="email"], input[type="password"]');
            
            // Sanitize input on paste
            inputs.forEach(input => {
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const text = e.clipboardData.getData('text');
                    // Remove potentially dangerous characters
                    const sanitized = text.replace(/[<>'"&]/g, '');
                    document.execCommand('insertText', false, sanitized);
                });
                
                // Sanitize input on keyup for certain characters
                input.addEventListener('keyup', function(e) {
                    if (e.key === '<' || e.key === '>' || e.key === '\'' || e.key === '"' || e.key === '&') {
                        e.preventDefault();
                        this.value = this.value.replace(/[<>'"&]/g, '');
                    }
                });
            });
            
            // Form submission validation
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                
                // Check for suspicious patterns
                const suspiciousPatterns = [
                    /<script/i,
                    /javascript:/i,
                    /on\w+=/i,
                    /eval\(/i,
                    /alert\(/i
                ];
                
                const isSuspicious = suspiciousPatterns.some(pattern => 
                    pattern.test(email) || pattern.test(password)
                );
                
                if (isSuspicious) {
                    e.preventDefault();
                    alert('Invalid input detected. Please use only valid characters.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
