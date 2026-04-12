@extends('layouts.admin')

@section('title', 'Manajemen Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Daftar Karyawan</h2>
            <a href="{{ route('employees.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Karyawan
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <!-- Employees Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[540px]">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="p-3">Nama</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Posisi</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Akun User</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $employee->name }}</td>
                        <td class="p-3">{{ $employee->email }}</td>
                        <td class="p-3">{{ $employee->position }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-3">
                            @if($employee->user)
                                <span class="text-sm">{{ $employee->user->name }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <div class="flex space-x-2">
                                <a href="{{ route('employees.show', $employee) }}" class="text-blue-600 hover:text-blue-800" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('employees.toggle', $employee) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-{{ $employee->is_active ? 'red' : 'green' }}-600 hover:text-{{ $employee->is_active ? 'red' : 'green' }}-800" 
                                            title="{{ $employee->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-{{ $employee->is_active ? 'ban' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-3 text-center text-gray-500">
                            Belum ada data karyawan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
@endsection