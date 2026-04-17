<?php

namespace App\Modules\Contact\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message',
        'read', 'email_sent', 'ip_address',
    ];

    protected $casts = [
        'read'       => 'boolean',
        'email_sent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeUnread($q) { return $q->where('read', false); }
    public function scopeSearch($q, string $s) {
        return $q->where(fn($x) => $x->where('name', 'like', "%$s%")
            ->orWhere('email', 'like', "%$s%")
            ->orWhere('subject', 'like', "%$s%")
            ->orWhere('message', 'like', "%$s%"));
    }

    public function markAsRead(): void { $this->update(['read' => true]); }
}
