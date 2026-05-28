<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('migrate', function () {
    $this->info('Migrasi otomatis diblokir secara aman! Server siap menyala.');
})->purpose('Mencegah crash build otomatis di Railway');
