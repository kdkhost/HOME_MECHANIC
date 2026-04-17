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
                <div class="d-flex flex-wrap gap-2" style="gap:0.5rem;">
                    <button onclick="clearCache('config')" class="btn btn-secondary"><i class="fas fa-cog"></i> Cache de Config</button>
                    <button onclick="clearCache('view')"   class="btn btn-secondary"><i class="fas fa-eye"></i> Cache de Views</button>
                    <button onclick="clearCache('all')"    class="btn btn-danger"><i class="fas fa-trash"></i> Limpar Tudo</button>
                </div>
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
function clearCache(type) {
    Swal.fire({
        title: 'Limpar cache?',
        text: 'Confirma a limpeza do cache?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--hm-primary)',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, limpar!',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        $.post('{{ route("admin.dashboard.clear-cache") }}', { _token: '{{ csrf_token() }}', type })
            .done(() => Swal.fire({ icon:'success', title:'Cache limpo!', timer:1500, showConfirmButton:false }))
            .fail(() => Swal.fire({ icon:'error', title:'Erro ao limpar cache.' }));
    });
}
</script>
@endsection
