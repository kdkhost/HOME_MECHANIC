<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Helper de upload compatível com CloudLinux/CageFS/LiteSpeed
 * Salva diretamente em public/uploads/ — sem symlink
 */
class FileUploadHelper
{
    /** Regex para UUID padrao (v4) */
    private const UUID_REGEX = '/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i';

    /**
     * Resolve upload a partir do request: arquivo direto OU UUID do FilePond.
     * Retorna o path relativo salvo, ou null se nada foi enviado.
     *
     * @param  Request     $request   Instancia do request
     * @param  string      $field     Nome do campo no formulario
     * @param  string      $subdir    Subdiretorio de destino (ex: 'uploads/services')
     * @param  string|null $oldPath   Path atual para deletar se houver substituicao
     * @return string|null            Path relativo salvo ou null
     */
    public static function resolveFromRequest(
        Request $request,
        string  $field,
        string  $subdir = 'uploads',
        ?string $oldPath = null
    ): ?string {
        // 1) Arquivo enviado diretamente no formulario
        $file = $request->file($field);
        if ($file && $file->isValid()) {
            if ($oldPath) static::delete($oldPath);
            return static::save($file, $subdir);
        }

        // 2) UUID retornado pelo FilePond (upload assincrono)
        $val = $request->input($field);
        
        // Se for array, pega o primeiro elemento (FilePond as vezes envia como array)
        if (is_array($val)) {
            $val = reset($val);
        }

        if (is_string($val) && !empty($val)) {
            $uuid = null;
            
            // Tenta validar como UUID direto
            if (preg_match(self::UUID_REGEX, $val)) {
                $uuid = $val;
            } 
            // Tenta validar se é um JSON contendo o UUID (comum no FilePond)
            else if (str_starts_with($val, '{')) {
                $json = json_decode($val, true);
                if (isset($json['data']['uuid'])) {
                    $uuid = $json['data']['uuid'];
                } else if (isset($json['uuid'])) {
                    $uuid = $json['uuid'];
                }
            }

            if ($uuid) {
                $upload = \App\Modules\Upload\Models\Upload::where('uuid', $uuid)->first();
                if ($upload) {
                    if ($oldPath) static::delete($oldPath);
                    return $upload->path;
                }
            }
        }

        // 3) Flag de remocao (_clear) vinda do componente FilePond
        if ($request->input($field . '_clear') === '1') {
            if ($oldPath) static::delete($oldPath);
            return '';
        }

        // Nenhuma alteracao
        return null;
    }

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
