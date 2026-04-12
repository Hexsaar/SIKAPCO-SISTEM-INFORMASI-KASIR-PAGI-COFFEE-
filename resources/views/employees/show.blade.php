@extends('layouts.admin')

@section('title', 'Detail Karyawan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Detail Karyawan</h2>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                <!-- Profile Photo -->
                <div class="flex justify-center mb-6">
                    <div class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center">
                        <i class="fas fa-user text-gray-600 text-5xl"></i>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama Lengkap</p>
                        <p class="font-medium">{{ $employee->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $employee->email }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Posisi</p>
                        <p class="font-medium">{{ $employee->position }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="font-medium">
                            <span class="px-2 py-1 text-xs rounded-full {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Akun User</p>
                        <p class="font-medium">{{ $employee->user->name ?? '-' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Bergabung</p>
                        <p class="font-medium">{{ $employee->created_at->format('d F Y') }}</p>
                    </div>
                </div>

                <!-- Statistics -->
                @if($employee->orders->count() > 0)
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-lg font-semibold mb-4">Statistik Karyawan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $employee->orders->count() }}</p>
                            <p class="text-sm text-gray-600">Total Transaksi</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <p class="text-2xl font-bold text-green-600">
                                Rp {{ number_format($employee->orders->sum('total'), 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-600">Total Penjualan</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg text-center">
                            <p class="text-2xl font-bold text-purple-600">
                                {{ $employee->orders->where('status', 'done')->count() }}
                            </p>
                            <p class="text-sm text-gray-600">Order Selesai</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('employees.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Kembali
                </a>
                <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection