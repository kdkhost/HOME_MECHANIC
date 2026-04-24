<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime'
    ];

    // Não usar updated_at para audit logs
    public $timestamps = false;

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registrar ação no audit log
     */
    public static function record(string $action, ?Model $model = null, array $oldValues = [], array $newValues = []): self
    {
        return static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : 'System',
            'model_id' => $model ? $model->getKey() : 0,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }

    /**
     * Scope para filtrar por usuário
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por ação
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope para filtrar por modelo
     */
    public function scopeByModel($query, string $modelType, ?int $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Scope para período
     */
    public function scopeInPeriod($query, \Carbon\Carbon $start, \Carbon\Carbon $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Obter nome amigável da ação
     */
    public function getActionNameAttribute(): string
    {
        return match ($this->action) {
            'service_created' => 'Serviço Criado',
            'service_updated' => 'Serviço Atualizado',
            'service_deleted' => 'Serviço Excluído',
            'service_toggled' => 'Status do Serviço Alterado',
            'service_featured_toggled' => 'Destaque do Serviço Alterado',
            'gallery_category_created' => 'Categoria da Galeria Criada',
            'gallery_category_updated' => 'Categoria da Galeria Atualizada',
            'gallery_category_deleted' => 'Categoria da Galeria Excluída',
            'gallery_photo_created' => 'Foto da Galeria Criada',
            'gallery_photo_updated' => 'Foto da Galeria Atualizada',
            'gallery_photo_deleted' => 'Foto da Galeria Excluída',
            'post_created' => 'Post Criado',
            'post_updated' => 'Post Atualizado',
            'post_deleted' => 'Post Excluído',
            'post_published' => 'Post Publicado',
            'testimonial_created' => 'Depoimento Criado',
            'testimonial_updated' => 'Depoimento Atualizado',
            'testimonial_deleted' => 'Depoimento Excluído',
            'contact_message_read' => 'Mensagem de Contato Lida',
            'contact_message_deleted' => 'Mensagem de Contato Excluída',
            'settings_updated' => 'Configurações Atualizadas',
            'maintenance_toggled' => 'Modo de Manutenção Alterado',
            'maintenance_ip_added' => 'IP de Manutenção Adicionado',
            'maintenance_ip_removed' => 'IP de Manutenção Removido',
            'cron_manual_run' => 'Tarefa Agendada Executada (Manual)',
            'backup_manual_run' => 'Backup Gerado (Manual)',
            'backup_manual_delete' => 'Backup Excluído',
            'schedule_run' => 'Agendamentos Executados (Manual)',
            'cache_cleared' => 'Cache do Sistema Limpo',
            'migrations_run' => 'Estrutura de Banco Atualizada (Migrations)',
            'smtp_test_sent' => 'Teste de E-mail Enviado',
            'user_login' => 'Login no Sistema',
            'user_logout' => 'Logout do Sistema',
            'password_reset_requested' => 'Recuperação de Senha Solicitada',
            'password_reset_success' => 'Senha Redefinida com Sucesso',
            default => ucfirst(str_replace('_', ' ', $this->action))
        };
    }

    /**
     * Obter nome amigável do modelo
     */
    public function getModelNameAttribute(): string
    {
        return match ($this->model_type) {
            'App\Modules\Services\Models\Service' => 'Serviço',
            'App\Modules\Gallery\Models\GalleryCategory' => 'Categoria da Galeria',
            'App\Modules\Gallery\Models\GalleryPhoto' => 'Foto da Galeria',
            'App\Modules\Blog\Models\Post' => 'Post',
            'App\Modules\Blog\Models\BlogCategory' => 'Categoria do Blog',
            'App\Modules\Testimonials\Models\Testimonial' => 'Depoimento',
            'App\Modules\Contact\Models\ContactMessage' => 'Mensagem de Contato',
            'App\Modules\Settings\Models\Setting' => 'Configuração',
            'App\Modules\Maintenance\Models\MaintenanceIp' => 'IP de Manutenção',
            'System' => 'Sistema',
            default => class_basename($this->model_type)
        };
    }

    /**
     * Serialização para JSON
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        
        $array['action_name'] = $this->action_name;
        $array['model_name'] = $this->model_name;
        $array['user_name'] = $this->user?->name;
        $array['formatted_date'] = $this->created_at?->format('d/m/Y H:i:s');
        
        return $array;
    }
}