<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Backup automatico diario (banco + arquivos) as 03:00
Schedule::command('backup:run --type=all')->dailyAt('03:00')->timezone('America/Sao_Paulo')
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Backup automatico falhou');
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Backup automatico concluido com sucesso');
    });

// Sync Google Reviews semanalmente aos sabados as 06:00
Schedule::command('google:sync-reviews')->weeklyOn(6, '06:00')->timezone('America/Sao_Paulo');
