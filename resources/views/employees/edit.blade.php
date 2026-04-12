@extends('layouts.admin')

@section('title', 'Edit Karyawan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Edit Karyawan</h2>
        </div>
        
        <form action="{{ route('employees.update', $employee) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" 
                           class="w-full border rounded-lg px-3 py-2 @error('name') border-red-500 @enderror" 
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" 
                           class="w-full border rounded-lg px-3 py-2 @error('email') border-red-500 @enderror" 
                           required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Posisi</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $employee->position) }}" 
                           class="w-full border rounded-lg px-3 py-2 @error('position') border-red-500 @enderror" 
                           required>
                    @error('position')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Account -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Akun User</label>
                    <select name="user_id" id="user_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (old('user_id', $employee->user_id) == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} class="mr-2">
                            <span>Aktif</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="is_active" value="0" {{ old('is_active', $employee->is_active) ? '' : 'checked' }} class="mr-2">
                            <span>Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('employees.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection