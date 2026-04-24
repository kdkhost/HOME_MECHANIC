<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['title', 'message', 'type', 'link', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Helper para criar notificação facilmente
     */
    public static function push($title, $message = null, $link = null, $type = 'info')
    {
        return self::create([
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'type' => $type
        ]);
    }
}
