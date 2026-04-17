<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

/**
 * Helper de upload compatível com CloudLinux/CageFS/LiteSpeed
 * Salva diretamente em public/uploads/ — sem symlink
 */
class FileUploadHelper
{
    /**
     * Salva um arquivo diretamente em public/uploads/{subdir}/
     * Retorna o path relativo a public/ (ex: "uploads/avatars/avatar_xxx.jpg")
     */
    public static function save(UploadedFile $file, string $subdir = 'uploads'): string
    {
        $dir = public_path($subdir);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext      = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $filename = uniqid(str_replace('/', '_', $subdir) . '_') . '.' . $ext;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        // Mover o arquivo para o destino
        $file->move($dir, $filename);

        // Retorna path relativo a public/ para usar em asset()
        return $subdir . '/' . $filename;
    }

    /**
     * Remove um arquivo salvo via save()
     * $relativePath = "uploads/avatars/avatar_xxx.jpg"
     */
    public static function delete(?string $relativePath): void
    {
        if (!$relativePath) return;

        $fullPath = public_path($relativePath);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Retorna a URL pública do arquivo
     */
    public static function url(?string $relativePath): ?string
    {
        if (!$relativePath) return null;
        return asset($relativePath);
    }
}
