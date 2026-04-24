<?php

use Illuminate\Support\Facades\Schedule;

// Helper: verificar se a tarefa esta ativada no painel admin
if (!function_exists('cronEnabled')) {
    function cronEnabled(string $command): bool
    {
        try {
            $disabled = json_decode(\App\Models\Setting::get('cron_disabled', '[]'), true) ?: [];
            return !in_array(md5($command), $disabled);
        } catch (\Exception $e) {
            return true; // Se nao conseguir ler, permitir execucao
        }
    }
}

// Backup automatico diario (banco + arquivos) as 03:00
Schedule::command('backup:run --type=all')->dailyAt('03:00')->timezone('America/Sao_Paulo')
    ->skip(fn() => !cronEnabled('backup:run --type=all'))
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Backup automatico falhou');
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Backup automatico concluido com sucesso');
    });

// Sync Google Reviews semanalmente aos sabados as 06:00
Schedule::command('google:sync-reviews')->weeklyOn(6, '06:00')->timezone('America/Sao_Paulo')
    ->skip(fn() => !cronEnabled('google:sync-reviews'));
