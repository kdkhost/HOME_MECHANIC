<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    protected string $disk = 'local';
    protected string $backupPath = 'backups';
    protected int $maxBackups = 30;

    public function __construct()
    {
        if (!Storage::disk($this->disk)->exists($this->backupPath)) {
            Storage::disk($this->disk)->makeDirectory($this->backupPath);
        }
    }

    /**
     * Criar um novo backup (Banco + Arquivos)
     */
    public function create(Request $request)
    {
        $disk = Storage::disk($this->disk);

        try {
            $type = $request->input('type', 'all');
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_{$type}_{$timestamp}.zip";
            $relativePath = $this->backupPath . '/' . $filename;

            // Caminho absoluto para ZipArchive (requer filesystem real)
            $zipPath = $disk->path($relativePath);
            $errors = [];

            $zip = new ZipArchive();
            $res = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($res !== true) {
                throw new \Exception("Não foi possível criar o arquivo ZIP. Código ZipArchive: {$res}");
            }

            // 1. Backup do Banco de Dados
            if ($type === 'all' || $type === 'db') {
                try {
                    $sqlDump = $this->generateSqlDump();
                    $zip->addFromString("database_dump.sql", $sqlDump);
                } catch (\Exception $e) {
                    $errors[] = "Banco de Dados: " . $e->getMessage();
                    Log::warning('Falha parcial no dump SQL: ' . $e->getMessage());
                    $zip->addFromString("_ERRO_DUMP_SQL.txt", "Erro ao gerar dump SQL:\n" . $e->getMessage());
                }
            }

            // 2. Backup de Arquivos (Uploads)
            if ($type === 'all' || $type === 'files') {
                try {
                    $fileCount = $this->addFilesToZip($zip);
                    if ($fileCount === 0) {
                        $zip->addFromString("_ARQUIVOS.txt", "Nenhum arquivo de upload encontrado para backup.");
                    }
                } catch (\Exception $e) {
                    $errors[] = "Arquivos: " . $e->getMessage();
                    Log::warning('Falha parcial no backup de arquivos: ' . $e->getMessage());
                }
            }

            $zip->close();

            // Verificar se o arquivo realmente foi criado e tem conteudo
            if (!$disk->exists($relativePath)) {
                throw new \Exception("Arquivo de backup não foi criado: {$relativePath}");
            }
            $fileSize = $disk->size($relativePath);
            if ($fileSize === 0) {
                $disk->delete($relativePath);
                throw new \Exception("Arquivo de backup ficou vazio (0 bytes). Possível falha na escrita.");
            }

            // Limpar backups antigos
            $this->cleanupOldBackups();

            $message = 'Backup concluído com sucesso!';
            if (!empty($errors)) {
                $message .= ' Avisos: ' . implode('; ', $errors);
            }

            Log::info("Backup gerado: {$filename} (" . $this->formatSize($fileSize) . ")");

            \App\Models\AuditLog::record('backup_manual_run', null, [], ['filename' => $filename, 'size' => $this->formatSize($fileSize)]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'filename' => $filename,
                    'size' => $this->formatSize($fileSize),
                    'date' => date('d/m/Y H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar backup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar backups realizados
     */
    public function list()
    {
        $disk = Storage::disk($this->disk);
        $backups = [];

        foreach ($disk->files($this->backupPath) as $file) {
            if (!str_ends_with($file, '.zip')) continue;

            $backups[] = [
                'name'     => basename($file),
                'url'      => route('admin.settings.backup.download', ['file' => basename($file)]),
                'size'     => $this->formatSize($disk->size($file)),
                'date'     => date('d/m/Y H:i', $disk->lastModified($file)),
                'raw_date' => $disk->lastModified($file),
            ];
        }

        usort($backups, fn($a, $b) => $b['raw_date'] <=> $a['raw_date']);

        return response()->json(['success' => true, 'data' => $backups]);
    }

    /**
     * Download de um backup
     */
    public function download(Request $request)
    {
        $filename = basename($request->query('file'));
        $relativePath = $this->backupPath . '/' . $filename;

        if (!Storage::disk($this->disk)->exists($relativePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk($this->disk)->download($relativePath, $filename);
    }

    /**
     * Excluir um backup
     */
    public function destroy(Request $request)
    {
        $filename = basename($request->input('file'));
        $relativePath = $this->backupPath . '/' . $filename;
        $disk = Storage::disk($this->disk);

        if ($disk->exists($relativePath)) {
            $disk->delete($relativePath);
            \App\Models\AuditLog::record('backup_manual_delete', null, [], ['filename' => $filename]);
            return response()->json(['success' => true, 'message' => 'Backup excluído.']);
        }

        return response()->json(['success' => false, 'message' => 'Arquivo não encontrado.'], 404);
    }

    /**
     * Limpar backups antigos, mantendo apenas os $maxBackups mais recentes
     */
    private function cleanupOldBackups(): void
    {
        $disk = Storage::disk($this->disk);

        $files = collect($disk->files($this->backupPath))
            ->filter(fn($f) => str_ends_with($f, '.zip'))
            ->sortByDesc(fn($f) => $disk->lastModified($f));

        if ($files->count() > $this->maxBackups) {
            foreach ($files->skip($this->maxBackups) as $file) {
                $disk->delete($file);
            }
        }
    }

    /**
     * Gerar dump SQL nativo via PHP/PDO
     */
    private function generateSqlDump(): string
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');

        // Detectar dinamicamente o nome da coluna retornado por SHOW TABLES
        $tablesCol = null;
        if (!empty($tables)) {
            $firstRow = (array) $tables[0];
            $tablesCol = array_key_first($firstRow);
        }
        if (!$tablesCol) {
            $tablesCol = "Tables_in_{$dbName}";
        }

        $output = "-- HomeMechanic SQL Dump\n";
        $output .= "-- Gerado em: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tablesCol;

            // Pular views e tabelas do sistema
            if (str_starts_with($tableName, 'v_') || str_starts_with($tableName, 'sys_')) {
                continue;
            }

            try {
                $showCreate = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createCol = 'Create Table';
                if (!property_exists($showCreate[0], $createCol)) {
                    $createCol = 'Create View';
                }
                if (!property_exists($showCreate[0], $createCol)) {
                    continue;
                }

                $output .= "\n-- Estrutura da tabela `{$tableName}`\n";
                $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $output .= $showCreate[0]->$createCol . ";\n\n";

                if ($createCol === 'Create Table') {
                    $rows = DB::table($tableName)->get();
                    if ($rows->count() > 0) {
                        $output .= "-- Dados da tabela `{$tableName}`\n";
                        foreach ($rows as $row) {
                            $rowArray = (array) $row;
                            $keys = array_keys($rowArray);
                            $values = array_values($rowArray);

                            $escapedValues = array_map(function ($v) {
                                if (is_null($v)) return "NULL";
                                return "'" . addslashes($v) . "'";
                            }, $values);

                            $output .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
                        }
                        $output .= "\n";
                    }
                }
            } catch (\Exception $e) {
                $output .= "-- ERRO ao exportar tabela `{$tableName}`: {$e->getMessage()}\n\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $output;
    }

    /**
     * Adicionar pastas de upload ao ZIP
     */
    private function addFilesToZip($zip): int
    {
        $folders = [
            public_path('uploads'),
            storage_path('app/public'),
        ];

        $count = 0;
        foreach ($folders as $folder) {
            if (!File::isDirectory($folder)) continue;

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'files/' . substr($filePath, strlen(base_path()) + 1);
                    if ($zip->addFile($filePath, $relativePath)) {
                        $count++;
                    }
                }
            }
        }
        return $count;
    }

    /**
     * Formatar tamanho de arquivo
     */
    private function formatSize(int $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
