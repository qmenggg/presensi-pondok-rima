<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Auto-finalize Rekap Scheduler
 * 
 * Mode 1: after-activity - Runs every 15 minutes to finalize activities that have ended
 * Mode 2: end-of-day - Runs at 23:59 to finalize all remaining activities for the day
 */

// Run every 15 minutes: finalize activities that have ended
Schedule::command('rekap:auto-finalize --mode=after-activity')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Run at 23:59: finalize all remaining activities for the day  
Schedule::command('rekap:auto-finalize --mode=end-of-day')
    ->dailyAt('23:59')
    ->withoutOverlapping()
    ->runInBackground();

// Also auto-save pending approval from previous day at 00:05
Schedule::command('rekap:auto-save')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground();
