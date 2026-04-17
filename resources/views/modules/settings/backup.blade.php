@extends('layouts.admin')

@section('title', 'Backup e Manutenção')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Backup e Manutenção</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
                        <li class="breadcrumb-item active">Backup</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Menu lateral -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Menu</h3></div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.index') }}" class="nav-link">
                                        <i class="fas fa-cog mr-2"></i> Geral
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.seo') }}" class="nav-link">
                                        <i class="fas fa-search mr-2"></i> SEO
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.email') }}" class="nav-link">
                                        <i class="fas fa-envelope mr-2"></i> E-mail
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.backup') }}" class="nav-link active">
                                        <i class="fas fa-download mr-2"></i> Backup
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo -->
                <div class="col-md-9">

                    <!-- Modo de Manutenção -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tools mr-2"></i> Modo de Manutenção</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Quando ativado, o site exibe uma página de manutenção para visitantes. Administradores continuam com acesso normal.</p>
                            <form method="POST" action="{{ route('admin.settings.update') }}">
                                @csrf
                                <input type="hidden" name="section" value="maintenance">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode">
                                    <label class="custom-control-label" for="maintenance_mode">Ativar Modo de Manutenção</label>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save mr-1"></i> Salvar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Limpeza de Cache -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-broom mr-2"></i> Limpeza de Cache</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Limpe os caches do sistema para forçar a atualização de configurações e views.</p>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <a href="#" onclick="clearCache('config')" class="btn btn-outline-secondary btn-block">
                                        <i class="fas fa-cog mr-1"></i> Cache de Config
                                    </a>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <a href="#" onclick="clearCache('view')" class="btn btn-outline-secondary btn-block">
                                        <i class="fas fa-eye mr-1"></i> Cache de Views
                                    </a>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <a href="#" onclick="clearCache('all')" class="btn btn-outline-danger btn-block">
                                        <i class="fas fa-trash mr-1"></i> Limpar Tudo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Sistema -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i> Informações do Sistema</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="font-weight-bold" width="40%">Versão PHP</td>
                                        <td>{{ PHP_VERSION }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Versão Laravel</td>
                                        <td>{{ app()->version() }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Ambiente</td>
                                        <td>{{ app()->environment() }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Fuso Horário</td>
                                        <td>{{ config('app.timezone') }}</td>
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
                                    <tr>
                                        <td class="font-weight-bold">Data/Hora do Servidor</td>
                                        <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
function clearCache(type) {
    if (!confirm('Tem certeza que deseja limpar o cache?')) return;

    $.post('{{ route("admin.dashboard.clear-cache") }}', {
        _token: '{{ csrf_token() }}',
        type: type
    })
    .done(function(res) {
        toastr.success('Cache limpo com sucesso!');
    })
    .fail(function() {
        toastr.error('Erro ao limpar cache.');
    });
}
</script>
@endsection
