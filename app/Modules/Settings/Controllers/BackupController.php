<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BackupController extends Controller
{
    protected $backupPath = 'backups';
    protected $maxBackups = 30; // Manter no maximo 30 backups

    public function __construct()
    {
        $backupDir = storage_path("app/" . $this->backupPath);
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0775, true);
        }
    }

    /**
     * Criar um novo backup (Banco + Arquivos)
     */
    public function create(Request $request)
    {
        try {
            $type = $request->input('type', 'all'); // all, db, files
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_{$type}_{$timestamp}.zip";

            // Garantir que a pasta física exista
            $backupDir = storage_path("app/" . $this->backupPath);
            if (!is_dir($backupDir)) {
                if (!@mkdir($backupDir, 0775, true) && !is_dir($backupDir)) {
                    throw new \Exception("Falha ao criar diretório de backups em: {$backupDir}. Verifique as permissões do servidor.");
                }
            }

            $zipPath = $backupDir . '/' . $filename;
            $errors = [];

            $zip = new ZipArchive();
            $res = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($res !== true) {
                throw new \Exception("Não foi possível criar o arquivo ZIP. Código de Erro ZipArchive: {$res} no caminho: {$zipPath}");
            }

            // 1. Backup do Banco de Dados
            if ($type === 'all' || $type === 'db') {
                try {
                    $sqlDump = $this->generateSqlDump();
                    $zip->addFromString("database_dump.sql", $sqlDump);
                } catch (\Exception $e) {
                    $errors[] = "Banco de Dados: " . $e->getMessage();
                    Log::warning('Falha parcial no dump SQL do backup: ' . $e->getMessage());
                    // Adicionar arquivo de erro no ZIP para o admin saber
                    $zip->addFromString("_ERRO_DUMP_SQL.txt", "Erro ao gerar dump SQL:\n" . $e->getMessage() . "\n\nStack:\n" . $e->getTraceAsString());
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
            if (!file_exists($zipPath)) {
                throw new \Exception("Arquivo de backup não foi criado em: {$zipPath}");
            }
            $fileSize = filesize($zipPath);
            if ($fileSize === 0) {
                @unlink($zipPath);
                throw new \Exception("Arquivo de backup ficou vazio (0 bytes). Possível falha na escrita.");
            }

            // Limpar backups antigos (manter apenas os $maxBackups mais recentes)
            $this->cleanupOldBackups();

            $message = 'Backup concluído com sucesso!';
            if (!empty($errors)) {
                $message .= ' Avisos: ' . implode('; ', $errors);
            }

            Log::info("Backup gerado: {$filename} (" . size_format($fileSize) . ")" . (empty($errors) ? '' : " com avisos: " . implode('; ', $errors)));

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'filename' => $filename,
                    'size' => size_format($fileSize),
                    'date' => date('d/m/Y H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar backup: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar backups realizados (para AJAX ou View)
     */
    public function list()
    {
        $backupDir = storage_path("app/" . $this->backupPath);

        // Verificar se o diretorio existe fisicamente
        if (!is_dir($backupDir)) {
            return response()->json(['success' => true, 'data' => []]);
        }

        // Ler arquivos diretamente do filesystem (mais confiavel que Storage)
        $files = glob($backupDir . '/*.zip');
        $backups = [];

        foreach ($files as $filePath) {
            $backups[] = [
                'name' => basename($filePath),
                'url'  => route('admin.settings.backup.download', ['file' => basename($filePath)]),
                'size' => size_format(filesize($filePath)),
                'date' => date('d/m/Y H:i', filemtime($filePath)),
                'raw_date' => filemtime($filePath)
            ];
        }

        // Ordenar por data decrescente
        usort($backups, fn($a, $b) => $b['raw_date'] <=> $a['raw_date']);

        return response()->json(['success' => true, 'data' => $backups]);
    }

    /**
     * Download de um backup
     */
    public function download(Request $request)
    {
        $filename = basename($request->query('file'));
        $filePath = storage_path("app/{$this->backupPath}/{$filename}");

        if (!file_exists($filePath) || !is_file($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return response()->download($filePath, $filename);
    }

    /**
     * Excluir um backup
     */
    public function destroy(Request $request)
    {
        $filename = basename($request->input('file'));
        $filePath = storage_path("app/{$this->backupPath}/{$filename}");

        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
            return response()->json(['success' => true, 'message' => 'Backup excluído.']);
        }

        return response()->json(['success' => false, 'message' => 'Arquivo não encontrado.'], 404);
    }

    /**
     * Limpar backups antigos, mantendo apenas os $maxBackups mais recentes
     */
    private function cleanupOldBackups(): void
    {
        $backupDir = storage_path("app/" . $this->backupPath);
        if (!is_dir($backupDir)) return;

        $files = collect(glob($backupDir . '/*.zip'))
            ->sortByDesc(fn($f) => filemtime($f));

        if ($files->count() > $this->maxBackups) {
            $toDelete = $files->skip($this->maxBackups);
            foreach ($toDelete as $file) {
                @unlink($file);
            }
        }
    }

    /**
     * Gerar dump SQL nativo via PHP/PDO
     */
    private function generateSqlDump()
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
                // Estrutura
                $showCreate = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createCol = 'Create Table';
                // Se for view, o nome da coluna e diferente
                if (!property_exists($showCreate[0], $createCol)) {
                    $createCol = 'Create View';
                }
                if (!property_exists($showCreate[0], $createCol)) {
                    continue; // Pular se nao conseguir obter estrutura
                }

                $output .= "\n-- Estrutura da tabela `{$tableName}`\n";
                $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $output .= $showCreate[0]->$createCol . ";\n\n";

                // Dados (apenas para tabelas, nao views)
                if ($createCol === 'Create Table') {
                    $rows = DB::table($tableName)->get();
                    if ($rows->count() > 0) {
                        $output .= "-- Dados da tabela `{$tableName}`\n";
                        foreach ($rows as $row) {
                            $rowArray = (array)$row;
                            $keys = array_keys($rowArray);
                            $values = array_values($rowArray);

                            $escapedValues = array_map(function($v) {
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
            storage_path('app/public')
        ];

        $count = 0;
        foreach ($folders as $folder) {
            if (!File::isDirectory($folder)) continue;

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
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
}

/**
 * Helper para formatar tamanho de arquivo
 */
function size_format($bytes, $precision = 2) {
    if ($bytes <= 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
