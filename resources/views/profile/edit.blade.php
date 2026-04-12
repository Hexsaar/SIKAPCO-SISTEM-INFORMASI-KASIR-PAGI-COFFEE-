@extends('layouts.admin')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Profile Settings</h2>
            <p class="text-sm text-gray-600 mt-1">Update informasi profile dan akun Anda</p>
        </div>

        <div class="p-6">
            <!-- Success Message -->
            @if(session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('status') == 'photo-updated' ? 'Foto profile berhasil diupdate!' : session('status') }}
                </div>
            @endif

            <!-- Profile Photo -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="text-lg font-medium mb-4">Foto Profile</h3>
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center overflow-hidden ring-4 ring-gray-100">
                        @if(Auth::user()->profile_photo)
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="Profile" class="w-full h-full object-cover">
                        @endif
                    </div>
                    
                    <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="flex-1">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <input type="file" name="photo" id="photo" accept="image/*" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                       required>
                            </div>
                            @error('photo')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                            <div>
                                <button type="submit" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                    Upload Foto Baru
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Profile Information -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="text-lg font-medium mb-4">Informasi Profile</h3>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" 
                               class="w-full border rounded-lg px-3 py-2 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', Auth::user()->username) }}" 
                               class="w-full border rounded-lg px-3 py-2 @error('username') border-red-500 @enderror">
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" 
                               class="w-full border rounded-lg px-3 py-2 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Update Password -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="text-lg font-medium mb-4">Ubah Password</h3>
                
                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="w-full border rounded-lg px-3 py-2 @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="password" id="password" 
                               class="w-full border rounded-lg px-3 py-2 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Delete Account -->
            <div>
                <h3 class="text-lg font-medium text-red-600 mb-4">Hapus Akun</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Setelah akun Anda dihapus, semua data akan dihapus permanen. Harap backup data penting Anda sebelum menghapus akun.
                </p>
                
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Masukkan Password untuk Konfirmasi</label>
                        <input type="password" name="password" id="password" 
                               class="w-full border rounded-lg px-3 py-2 @error('password', 'userDeletion') border-red-500 @enderror" 
                               placeholder="Password Anda">
                        @error('password', 'userDeletion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Hapus Akun Permanen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection