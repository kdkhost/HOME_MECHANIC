@extends('layouts.admin')

@section('title', 'Configurações Gerais')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configurações Gerais</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
                        <li class="breadcrumb-item active">Geral</li>
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
                                    <a href="{{ route('admin.settings.index') }}" class="nav-link active">
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
                                    <a href="{{ route('admin.settings.backup') }}" class="nav-link">
                                        <i class="fas fa-download mr-2"></i> Backup
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Formulário -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-cog mr-2"></i> Configurações Gerais do Site</h3>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf
                            <input type="hidden" name="section" value="general">
                            <div class="card-body">

                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <div class="form-group">
                                    <label>Nome do Site</label>
                                    <input type="text" class="form-control" name="site_name"
                                           value="{{ $settings['site_name'] ?? 'HomeMechanic' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Descrição do Site</label>
                                    <textarea class="form-control" name="site_description" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>E-mail de Contato</label>
                                            <input type="email" class="form-control" name="contact_email"
                                                   value="{{ $settings['contact_email'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Telefone de Contato</label>
                                            <input type="text" class="form-control" name="contact_phone"
                                                   value="{{ $settings['contact_phone'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Endereço</label>
                                    <input type="text" class="form-control" name="address"
                                           value="{{ $settings['address'] ?? '' }}">
                                </div>

                                <div class="form-group">
                                    <label>Fuso Horário</label>
                                    <select class="form-control" name="timezone">
                                        <option value="America/Sao_Paulo" selected>America/Sao_Paulo (Brasília)</option>
                                        <option value="America/Manaus">America/Manaus</option>
                                        <option value="America/Belem">America/Belem</option>
                                        <option value="America/Fortaleza">America/Fortaleza</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="maintenance_mode"
                                               name="maintenance_mode" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="maintenance_mode">
                                            Modo de Manutenção
                                        </label>
                                    </div>
                                    <small class="text-muted">Quando ativado, apenas administradores acessam o site.</small>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
