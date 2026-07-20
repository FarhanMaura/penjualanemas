<?php

use App\Console\Commands\FetchGoldPrice;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fetch harga emas setiap hari jam 09:00 WIB (UTC+7 = 02:00 UTC)
Schedule::command(FetchGoldPrice::class)->dailyAt('02:00');
