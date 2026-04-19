<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    protected $backupPath = 'backups';

    public function __construct()
    {
        if (!Storage::exists($this->backupPath)) {
            Storage::makeDirectory($this->backupPath);
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

            $zip = new ZipArchive();
            $res = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($res !== true) {
                throw new \Exception("Não foi possível criar o arquivo ZIP. Código de Erro ZipArchive: {$res} no caminho: {$zipPath}");
            }

            // 1. Backup do Banco de Dados
            if ($type === 'all' || $type === 'db') {
                $sqlDump = $this->generateSqlDump();
                $zip->addFromString("database_dump.sql", $sqlDump);
            }

            // 2. Backup de Arquivos (Uploads)
            if ($type === 'all' || $type === 'files') {
                $this->addFilesToZip($zip);
            }

            $zip->close();

            return response()->json([
                'success' => true,
                'message' => 'Backup concluído com sucesso!',
                'data' => [
                    'filename' => $filename,
                    'size' => size_format(filesize($zipPath)),
                    'date' => date('d/m/Y H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
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
        $files = Storage::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'url'  => route('admin.settings.backup.download', ['file' => basename($file)]),
                'size' => size_format(Storage::size($file)),
                'date' => date('d/m/Y H:i', Storage::lastModified($file)),
                'raw_date' => Storage::lastModified($file)
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
        $filename = $request->query('file');
        $path = "{$this->backupPath}/{$filename}";

        if (!Storage::exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::download($path);
    }

    /**
     * Excluir um backup
     */
    public function destroy(Request $request)
    {
        $filename = $request->input('file');
        $path = "{$this->backupPath}/{$filename}";

        if (Storage::exists($path)) {
            Storage::delete($path);
            return response()->json(['success' => true, 'message' => 'Backup excluído.']);
        }

        return response()->json(['success' => false, 'message' => 'Arquivo não encontrado.'], 404);
    }

    /**
     * Gerar dump SQL nativo via PHP/PDO
     */
    private function generateSqlDump()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tablesCol = "Tables_in_{$dbName}";
        
        $output = "-- HomeMechanic SQL Dump\n";
        $output .= "-- Gerado em: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tablesCol;
            
            // Estrutura
            $showCreate = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $output .= "\n-- Estrutura da tabela `{$tableName}`\n";
            $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $output .= $showCreate[0]->{'Create Table'} . ";\n\n";

            // Dados
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

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $output;
    }

    /**
     * Adicionar pastas de upload ao ZIP
     */
    private function addFilesToZip($zip)
    {
        $folders = [
            public_path('uploads'),
            storage_path('app/public')
        ];

        foreach ($folders as $folder) {
            if (!File::isDirectory($folder)) continue;

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folder),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'files/' . substr($filePath, strlen(base_path()) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
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
