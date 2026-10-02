<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Chaque soir, les habitants sont prévenus des collectes du lendemain dans leur quartier
Schedule::command('collectes:rappeler')->dailyAt('18:00');
