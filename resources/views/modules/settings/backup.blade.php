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
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-hard-hat"></i> Modo de Manutenção</span>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Quando ativado, o site exibe uma página de manutenção para visitantes. Administradores continuam com acesso normal.</p>
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <input type="hidden" name="section" value="maintenance">
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode">
                        <label class="custom-control-label" for="maintenance_mode">Ativar Modo de Manutenção</label>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Salvar</button>
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
