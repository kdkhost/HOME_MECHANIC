<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    // ── Helpers estáticos ──────────────────────────────────

    /**
     * Obter valor de uma configuração (com cache de 10 min)
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('settings_all', 600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Definir/atualizar uma configuração e limpar cache
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
        Cache::forget('settings_all');
    }

    /**
     * Obter todas as configurações de um grupo como array key=>value
     */
    public static function group(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Salvar múltiplas configurações de uma vez
     */
    public static function setMany(array $data, string $group = 'general'): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }
        Cache::forget('settings_all');
    }
}
