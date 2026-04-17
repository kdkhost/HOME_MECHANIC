@extends('layouts.admin')

@section('title', 'Configurações')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configurações</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Configurações</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Menu de configurações -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Menu de Configurações</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.general') }}" class="nav-link">
                                        <i class="bi bi-gear"></i> Geral
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.seo') }}" class="nav-link">
                                        <i class="bi bi-search"></i> SEO
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.email') }}" class="nav-link">
                                        <i class="bi bi-envelope"></i> Email
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.settings.backup') }}" class="nav-link">
                                        <i class="bi bi-download"></i> Backup
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Configurações Gerais</h3>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="site_name">Nome do Site</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" 
                                           value="{{ $settings['site_name'] }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="site_description">Descrição do Site</label>
                                    <textarea class="form-control" id="site_description" name="site_description" 
                                              rows="3">{{ $settings['site_description'] }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_email">Email de Contato</label>
                                            <input type="email" class="form-control" id="contact_email" 
                                                   name="contact_email" value="{{ $settings['contact_email'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_phone">Telefone de Contato</label>
                                            <input type="text" class="form-control" id="contact_phone" 
                                                   name="contact_phone" value="{{ $settings['contact_phone'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address">Endereço</label>
                                    <input type="text" class="form-control" id="address" name="address" 
                                           value="{{ $settings['address'] }}">
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="maintenance_mode" 
                                               name="maintenance_mode" {{ $settings['maintenance_mode'] ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="maintenance_mode">
                                            Modo de Manutenção
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Quando ativado, apenas administradores podem acessar o site.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="analytics_enabled" 
                                               name="analytics_enabled" {{ $settings['analytics_enabled'] ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="analytics_enabled">
                                            Analytics Habilitado
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Salvar Configurações
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