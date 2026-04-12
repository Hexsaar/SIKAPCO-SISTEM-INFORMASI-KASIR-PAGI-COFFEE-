<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->role == $role) {
                return $next($request);
            }
        }

        // Redirect berdasarkan role yang dimiliki
        if ($user->role == 'admin') {
            return redirect('/admin/dashboard')->with('error', 'Akses ditolak. Halaman ini hanya untuk role tertentu.');
        } elseif ($user->role == 'kasir') {
            return redirect('/kasir')->with('error', 'Akses ditolak. Halaman ini hanya untuk role tertentu.');
        } elseif ($user->role == 'barista') {
            return redirect('/barista')->with('error', 'Akses ditolak. Halaman ini hanya untuk role tertentu.');
        } elseif ($user->role == 'pending') {
            // Redirect pending users to kasir instead of pending page
            return redirect('/kasir')->with('error', 'Akun Anda belum dikonfirmasi, tapi tetap bisa mengakses kasir.');
        }
        
        return redirect('/')->with('error', 'Akses ditolak');
    }
}