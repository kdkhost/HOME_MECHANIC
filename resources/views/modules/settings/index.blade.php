@extends('layouts.admin')
@section('title', 'Configurações')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item active">Configurações</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-cog mr-2" style="color:var(--hm-primary);"></i>Configurações do Sistema</h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'general'])

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-cog"></i> Configurações Gerais</span>
            </div>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="section" value="general">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    @endif
                    <div class="form-group">
                        <label>Nome do Site <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="site_name" value="{{ $settings['site_name'] }}" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição do Site</label>
                        <textarea class="form-control" name="site_description" rows="3">{{ $settings['site_description'] }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail de Contato</label>
                                <input type="email" class="form-control" name="contact_email" value="{{ $settings['contact_email'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Telefone de Contato</label>
                                <input type="text" class="form-control" name="contact_phone" value="{{ $settings['contact_phone'] }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" class="form-control" name="address" value="{{ $settings['address'] }}">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode" {{ $settings['maintenance_mode'] ? 'checked' : '' }}>
                            <label class="custom-control-label" for="maintenance_mode">Modo de Manutenção</label>
                        </div>
                        <small class="form-text">Quando ativado, apenas administradores acessam o site.</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="analytics_enabled" name="analytics_enabled" {{ $settings['analytics_enabled'] ? 'checked' : '' }}>
                            <label class="custom-control-label" for="analytics_enabled">Analytics Habilitado</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Configurações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
