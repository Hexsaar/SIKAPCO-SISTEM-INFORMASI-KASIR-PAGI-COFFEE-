<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Kita tambahkan {--force} dan {--seed} biar kalau Railway ngetik itu, Laravel lu gak kaget
Artisan::command('migrate {--force} {--seed}', function () {
    $this->info('Migrasi otomatis diblokir secara aman! Server siap menyala.');
})->purpose('Mencegah crash build otomatis di Railway');


// Gembok pertama (yang kemarin)
Artisan::command('migrate {--force} {--seed}', function () {
    $this->info('Migrasi otomatis diblokir secara aman!');
});

// TAMBAHKAN GEMBOK KEDUA INI:
Artisan::command('optimize:clear', function () {
    $this->info('Pembersihan cache otomatis diblokir saat build demi keamanan!');
})->purpose('Mencegah crash cache saat build di Railway');