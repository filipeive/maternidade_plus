<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes & Task Scheduling
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands and scheduled background jobs.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Avaliação contínua de alertas clínicos precoces a cada 15 minutos
Schedule::command('alertas:avaliar')->everyFifteenMinutes()->runInBackground();

