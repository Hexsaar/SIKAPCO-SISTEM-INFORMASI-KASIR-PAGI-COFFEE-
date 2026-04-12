@extends('layouts.app')

@section('title', 'Pending Approval')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Akun Menunggu Konfirmasi</h1>
                <p class="text-gray-600 mb-6">
                    Hai {{ auth()->user()->name }}, akun Anda sedang menunggu persetujuan dari admin.
                </p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-left">
                        <h3 class="font-medium text-amber-800 mb-1">Apa yang terjadi sekarang?</h3>
                        <ul class="text-sm text-amber-700 space-y-1">
                            <li>• Admin akan memeriksa permintaan pendaftaran Anda</li>
                            <li>• Anda akan ditetapkan sebagai Kasir atau Barista</li>
                            <li>• Anda akan menerima notifikasi setelah disetujui</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button onclick="location.reload()" class="w-full bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Status
                </button>
                
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Hubungi admin jika Anda menunggu lebih dari 24 jam.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
