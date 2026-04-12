<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Register Page</title>
    
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
                    to register<br>
                    page
                </h1>
            </div>
        </div>

        <!-- RIGHT SIDE - Register Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-8 bg-gray-50">
            <div class="w-full max-w-sm">
                
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
                
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Register.</h2>
                    
                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-4">
                    @csrf
                    
                    <!-- Username Input -->
                    <div>
                        <label for="name" class="block text-gray-700 font-semibold mb-1 text-sm">
                            Username
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name"
                            placeholder="Masukan Username...."
                            value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-900 focus:border-transparent transition"
                            required
                            autofocus
                            autocomplete="name"
                            pattern="[a-zA-Z0-9\s]{3,50}"
                            title="Username must be 3-50 characters and contain only letters, numbers, and spaces"
                        >
                        @error('name')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-gray-700 font-semibold mb-1 text-sm">
                            Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            placeholder="example@email.com"
                            value="{{ old('email') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-900 focus:border-transparent transition"
                            required
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
                        <label for="password" class="block text-gray-700 font-semibold mb-1 text-sm">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            placeholder="12xxxxxxx"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-900 focus:border-transparent transition"
                            required
                            autocomplete="new-password"
                            pattern="(?=.*\d).{8,}"
                            title="Password must be at least 8 characters and contain at least one number"
                        >
                        @error('password')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password Input -->
                    <div>
                        <label for="password_confirmation" class="block text-gray-700 font-semibold mb-1 text-sm">
                            Confirm Password
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation"
                            placeholder="12xxxxxxx"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-900 focus:border-transparent transition"
                            required
                            autocomplete="new-password"
                        >
                        @error('password_confirmation')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Terms and Privacy Link -->
                    <div class="flex items-center justify-between">
                        <!-- Terms Checkbox -->
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="terms"
                                name="terms"
                                class="w-4 h-4 text-amber-900 border-gray-300 rounded focus:ring-amber-900 cursor-pointer"
                                required
                            >
                            <label for="terms" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                I agree to terms
                            </label>
                        </div>
                        
                        <!-- Privacy Policy Link -->
                        <a href="#" id="privacyPolicyLink" class="text-[#4F2E22] font-semibold text-sm hover:underline">
                            Privacy Policy
                        </a>
                    </div>

                    <!-- Register Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-[#4F2E22] text-white py-2 rounded-lg font-semibold hover:bg-[#3A2119] transition duration-300 shadow-md"
                    >
                        Register
                    </button>
                </form>

                <!-- Sign In Link -->
                <p class="text-center text-sm text-gray-600 mt-4">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-[#4F2E22] font-semibold hover:underline">
                        Sign In
                    </a>
                </p>

            </div>
        </div>

    </div>

    <!-- Privacy Policy Modal -->
    <div id="privacyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full max-h-[80vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Privacy Policy</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                </div>
                <div class="text-gray-600 space-y-3">
                    <p>
                        <strong>Information Collection:</strong><br>
                        We collect information you provide directly to us, such as when you create an account, register for our services, or contact us.
                    </p>
                    <p>
                        <strong>How We Use Your Information:</strong><br>
                        We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.
                    </p>
                    <p>
                        <strong>Information Sharing:</strong><br>
                        We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.
                    </p>
                    <p>
                        <strong>Data Security:</strong><br>
                        We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
                    </p>
                    <p>
                        <strong>Your Rights:</strong><br>
                        You have the right to access, update, or delete your personal information at any time through your account settings or by contacting us.
                    </p>
                    <p>
                        <strong>Contact Us:</strong><br>
                        If you have any questions about this Privacy Policy, please contact us at privacy@coffeeshop.com
                    </p>
                </div>
                <div class="mt-6 flex justify-end">
                    <button id="acceptPrivacy" class="bg-[#4F2E22] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#3A2119] transition">
                        I Understand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Security: Prevent XSS attacks
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
            
            // Privacy Policy Modal
            const privacyModal = document.getElementById('privacyModal');
            const privacyPolicyLink = document.getElementById('privacyPolicyLink');
            const closeModal = document.getElementById('closeModal');
            const acceptPrivacy = document.getElementById('acceptPrivacy');
            
            // Open privacy modal
            privacyPolicyLink.addEventListener('click', function(e) {
                e.preventDefault();
                privacyModal.classList.remove('hidden');
                privacyModal.classList.add('flex');
            });
            
            // Close privacy modal
            function closePrivacyModal() {
                privacyModal.classList.add('hidden');
                privacyModal.classList.remove('flex');
            }
            
            closeModal.addEventListener('click', closePrivacyModal);
            acceptPrivacy.addEventListener('click', closePrivacyModal);
            
            // Close modal when clicking outside
            privacyModal.addEventListener('click', function(e) {
                if (e.target === privacyModal) {
                    closePrivacyModal();
                }
            });
            
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
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirmation').value;
                
                // Check for suspicious patterns
                const suspiciousPatterns = [
                    /<script/i,
                    /javascript:/i,
                    /on\w+=/i,
                    /eval\(/i,
                    /alert\(/i
                ];
                
                const isSuspicious = suspiciousPatterns.some(pattern => 
                    pattern.test(name) || pattern.test(email) || pattern.test(password) || pattern.test(passwordConfirm)
                );
                
                if (isSuspicious) {
                    e.preventDefault();
                    alert('Invalid input detected. Please use only valid characters.');
                    return false;
                }
                
                // Password confirmation check
                if (password !== passwordConfirm) {
                    e.preventDefault();
                    alert('Passwords do not match. Please confirm your password.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
