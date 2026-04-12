@extends('layouts.admin')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Tambah Karyawan Baru</h2>
        </div>
        
        <form action="{{ route('employees.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="w-full border rounded-lg px-3 py-2 @error('name') border-red-500 @enderror" 
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                           class="w-full border rounded-lg px-3 py-2 @error('email') border-red-500 @enderror" 
                           required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Posisi</label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}" 
                           class="w-full border rounded-lg px-3 py-2 @error('position') border-red-500 @enderror" 
                           required>
                    @error('position')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Account -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Akun User (Opsional)</label>
                    <select name="user_id" id="user_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hubungkan dengan akun user yang sudah ada</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('employees.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection