<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Modules\Upload\Models\Upload;

class UploadController extends Controller
{
    /**
     * Upload endpoint para FilePond
     * Recebe arquivo e retorna UUID para uso posterior
     */
    public function filepond(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'Nenhum arquivo enviado'], 400);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json(['error' => 'Arquivo inválido'], 400);
        }

        try {
            $uuid = (string) Str::uuid();
            $subdir = 'uploads/temp';
            $dir = public_path($subdir);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $ext = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
            $filename = $uuid . '.' . $ext;
            $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

            $file->move($dir, $filename);

            // Salvar referência no banco
            $upload = Upload::create([
                'uuid' => $uuid,
                'original_name' => $file->getClientOriginalName(),
                'path' => $subdir . '/' . $filename,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            return response($uuid, 200)->header('Content-Type', 'text/plain');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar upload: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Revert/Delete upload temporário do FilePond
     */
    public function revert(Request $request)
    {
        $uuid = $request->getContent();

        if (!$uuid) {
            return response()->json(['error' => 'UUID não fornecido'], 400);
        }

        try {
            $upload = Upload::where('uuid', $uuid)->first();

            if ($upload) {
                $path = public_path($upload->path);
                if (file_exists($path)) {
                    unlink($path);
                }
                $upload->delete();
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao remover arquivo'], 500);
        }
    }
}
