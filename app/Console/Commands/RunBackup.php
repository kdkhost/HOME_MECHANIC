<?php

namespace App\Console\Commands;

use App\Modules\Settings\Controllers\BackupController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class RunBackup extends Command
{
    protected $signature = 'backup:run {--type=all : Tipo de backup (all, db, files)}';

    protected $description = 'Executar backup automatico do sistema (banco + arquivos)';

    public function handle(): int
    {
        $type = $this->option('type');

        if (!in_array($type, ['all', 'db', 'files'])) {
            $this->error('Tipo invalido. Use: all, db ou files.');
            return self::FAILURE;
        }

        $this->info("Iniciando backup tipo: {$type}...");

        try {
            $controller = new BackupController();
            $request = new Request(['type' => $type]);
            $response = $controller->create($request);
            $data = $response->getData(true);

            if ($data['success']) {
                $this->info("Backup concluido: {$data['data']['filename']} ({$data['data']['size']})");
                return self::SUCCESS;
            }

            $this->error("Erro: {$data['message']}");
            return self::FAILURE;

        } catch (\Exception $e) {
            $this->error("Erro ao executar backup: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
