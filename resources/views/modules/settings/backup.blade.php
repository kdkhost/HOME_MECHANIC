@extends('layouts.admin')
@section('title', 'Backup e Manutenção')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
    <li class="breadcrumb-item active">Backup</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-tools mr-2" style="color:var(--hm-primary);"></i>Backup e Manutenção</h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'backup'])

    <div class="col-md-9">

        <!-- Modo de Manutenção -->
        <div class="card" style="border: 1px solid rgba(255,107,0,0.1);">
            <div class="card-header" style="background: rgba(255,107,0,0.02);">
                <span class="card-title font-weight-bold" style="color: #ff6b00;"><i class="fas fa-hard-hat"></i> Parâmetros de Manutenção</span>
            </div>
            <div class="card-body py-4">
                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="section" value="maintenance">
                    <div class="row">
                        <div class="col-md-12 mb-4 border-bottom pb-3">
                            <div class="custom-control custom-switch custom-switch-lg">
                                <input type="checkbox" class="custom-control-input" id="maintenance_mode"
                                       name="maintenance_mode" value="1"
                                       {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" style="font-size: 1.1rem; padding-top: 2px;" for="maintenance_mode">Ativar Modo de Manutenção</label>
                            </div>
                            <small class="form-text mt-2" style="font-size: 0.95rem; color: #6c757d;">
                                Quando ativado, o site exibe uma página de manutenção isolada. Administradores e IPs autorizados continuam com acesso normal.
                            </small>
                        </div>
                        
                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label>Título da Página de Manutenção</label>
                                <input type="text" class="form-control" name="maintenance_title"
                                       value="{{ old('maintenance_title', $settings['maintenance_title'] ?? 'Site em Manutenção') }}"
                                       placeholder="Ex: Site em Manutenção">
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label>Mensagem para Visitantes</label>
                                <input type="text" class="form-control" name="maintenance_message"
                                       value="{{ old('maintenance_message', $settings['maintenance_message'] ?? 'Voltaremos em breve. Estamos realizando atualizações.') }}"
                                       placeholder="Ex: Voltaremos em breve.">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label>Temporizador de Retorno (Opcional)</label>
                                <input type="datetime-local" class="form-control" name="maintenance_timer"
                                       value="{{ old('maintenance_timer', $settings['maintenance_timer'] ?? '') }}">
                                <small class="form-text text-muted mt-1">Defina quando a página voltará para exibir um <strong>Cronômetro</strong>.</small>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label>Imagem de Fundo (Opcional)</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="flex-grow:1;">
                                        <input type="file" class="form-control" name="maintenance_bg_image" accept="image/jpeg,image/png,image/webp">
                                        <small class="form-text text-muted mt-1">Recomendado: 1920x1080px. Deixe em branco para usar o tema padrão escuro.</small>
                                    </div>
                                    @if(!empty($settings['maintenance_bg_image']))
                                        <div style="flex-shrink:0;">
                                            <img src="{{ asset('storage/' . $settings['maintenance_bg_image']) }}" alt="Bg Atual" style="height:60px;width:100px;object-fit:cover;border-radius:4px;border:1px solid var(--hm-border);">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group mb-4">
                                <label>IPs com Acesso Liberado (separados por vírgula)</label>
                                <textarea class="form-control" name="maintenance_ips" rows="2" placeholder="Ex: 192.168.1.1, 201.55.10.2">{{ old('maintenance_ips', $settings['maintenance_ips'] ?? '') }}</textarea>
                                <small class="form-text text-muted mt-2"><i class="fas fa-info-circle text-orange"></i> Esses IPs ignoram a manutenção e veem o site normalmente. <strong>Seu IP atual: {{ request()->ip() }}</strong></small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Salvar Manutenção</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Limpeza de Cache -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-broom"></i> Limpeza de Cache</span>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Limpe os caches do sistema para forçar a atualização de configurações e views.</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button onclick="clearCacheType('all')"    class="btn btn-primary"><i class="fas fa-broom"></i> Limpar Tudo</button>
                    <button onclick="clearCacheType('config')" class="btn btn-secondary"><i class="fas fa-cog"></i> Config</button>
                    <button onclick="clearCacheType('view')"   class="btn btn-secondary"><i class="fas fa-eye"></i> Views</button>
                    <button onclick="clearCacheType('route')"  class="btn btn-secondary"><i class="fas fa-route"></i> Rotas</button>
                    <button onclick="clearCacheType('app')"    class="btn btn-secondary"><i class="fas fa-database"></i> App Cache</button>
                </div>
                <div id="cacheResult" style="display:none;"></div>
            </div>
        </div>

        <!-- Banco de Dados -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-database"></i> Banco de Dados</span>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Execute as migrations pendentes para atualizar a estrutura do banco de dados.</p>
                <button type="button" class="btn btn-primary" id="btnMigrate" onclick="runMigrations()">
                    <i class="fas fa-play-circle"></i> Rodar Migrations Pendentes
                </button>
                <div id="migrateResult" class="mt-3" style="display:none;"></div>
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-info-circle"></i> Informações do Sistema</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" style="font-size:0.87rem;">
                    <tr><td class="font-weight-bold" width="35%">Versão PHP</td><td>{{ PHP_VERSION }}</td></tr>
                    <tr><td class="font-weight-bold">Versão Laravel</td><td>{{ app()->version() }}</td></tr>
                    <tr>
                        <td class="font-weight-bold">Ambiente</td>
                        <td>
                            @if(app()->environment('production'))
                                <span class="badge badge-success">Produção</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst(app()->environment()) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Debug</td>
                        <td>
                            @if(config('app.debug'))
                                <span class="badge badge-warning">Ativado</span>
                            @else
                                <span class="badge badge-success">Desativado</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td class="font-weight-bold">Fuso Horário</td><td>{{ config('app.timezone') }}</td></tr>
                    <tr><td class="font-weight-bold">Data/Hora do Servidor</td><td>{{ now()->format('d/m/Y H:i:s') }}</td></tr>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
// Rodar migrations
function runMigrations() {
    Swal.fire({
        title: 'Rodar Migrations?',
        html: '<div style="font-size:0.88rem;color:#64748b;">Isso executará todas as migrations pendentes no banco de dados.<br><strong>Recomendado após atualizar o sistema.</strong></div>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--hm-primary)',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-play-circle me-1"></i> Executar',
        cancelButtonText: 'Cancelar',
    }).then(r => {
        if (!r.isConfirmed) return;

        const btn       = document.getElementById('btnMigrate');
        const resultDiv = document.getElementById('migrateResult');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Executando...';
        resultDiv.style.display = 'none';

        $.ajax({
            url: '{{ route("admin.system.migrate") }}',
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: JSON.stringify({}),
            success(data) {
                const output = data.output ? `<pre style="font-size:0.78rem;margin-top:0.5rem;background:#f8fafc;padding:0.75rem;border-radius:6px;max-height:200px;overflow-y:auto;">${data.output}</pre>` : '';
                resultDiv.innerHTML = data.success
                    ? `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><strong>${data.message}</strong>${output}</div>`
                    : `<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>${data.message}</div>`;
                resultDiv.style.display = 'block';
                if (data.success) HMToast.success(data.message);
                else HMToast.error(data.message);
            },
            error(xhr) {
                const msg = xhr.responseJSON?.message || 'Erro ao executar migrations.';
                resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>${msg}</div>`;
                resultDiv.style.display = 'block';
                HMToast.error(msg);
            },
            complete() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-play-circle me-1"></i> Rodar Migrations Pendentes';
            }
        });
    });
}

// Sobrescrever clearCacheType para mostrar resultado inline nesta página
window.clearCacheType = function(type) {
    const labels = { all:'Limpar TODOS os caches', config:'Configuração', view:'Views', route:'Rotas', app:'App Cache' };
    Swal.fire({
        title: labels[type] || 'Limpar cache?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--hm-primary)',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-broom me-1"></i> Limpar',
        cancelButtonText: 'Cancelar',
    }).then(r => {
        if (!r.isConfirmed) return;

        const resultDiv = document.getElementById('cacheResult');
        resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Limpando cache...</div>';
        resultDiv.style.display = 'block';

        $.ajax({
            url: '{{ route("admin.system.clear-cache") }}',
            method: 'POST',
            data: JSON.stringify({ type }),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success(data) {
                const details = (data.details || []).join('<br>');
                resultDiv.innerHTML = data.success
                    ? `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><strong>Concluído!</strong><br>${details}</div>`
                    : `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>${data.message}<br>${details}</div>`;
                if (data.success) HMToast.success(data.message);
                else HMToast.warning(data.message);
            },
            error(xhr) {
                const msg = xhr.responseJSON?.message || 'Erro ao limpar cache.';
                resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>${msg}</div>`;
                HMToast.error(msg);
            }
        });
    });
};
</script>
@endsection
