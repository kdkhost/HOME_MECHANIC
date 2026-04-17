@extends('layouts.admin')
@section('title', 'Configurações Gerais')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
    <li class="breadcrumb-item active">Geral</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-cog me-2" style="color:var(--hm-primary);"></i>Configurações Gerais</h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'general'])

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-cog"></i> Configurações Gerais do Site</span>
            </div>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="section" value="general">
                <div class="card-body">
                    <div class="form-group">
                        <label>Nome do Site <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="site_name"
                               value="{{ old('site_name', $settings['site_name'] ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição do Site</label>
                        <textarea class="form-control" name="site_description" rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail de Contato</label>
                                <input type="email" class="form-control" name="contact_email"
                                       value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Telefone de Contato</label>
                                <input type="text" class="form-control" name="contact_phone"
                                       value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" class="form-control" name="address"
                               value="{{ old('address', $settings['address'] ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Fuso Horário</label>
                        <select class="form-control" name="timezone" style="max-width:280px;">
                            @foreach(['America/Sao_Paulo'=>'Brasília (UTC-3)','America/Manaus'=>'Manaus (UTC-4)','America/Belem'=>'Belém (UTC-3)','America/Fortaleza'=>'Fortaleza (UTC-3)','America/Recife'=>'Recife (UTC-3)','America/Cuiaba'=>'Cuiabá (UTC-4)','America/Porto_Velho'=>'Porto Velho (UTC-4)','America/Boa_Vista'=>'Boa Vista (UTC-4)','America/Manaus'=>'Manaus (UTC-4)','America/Rio_Branco'=>'Rio Branco (UTC-5)'] as $tz => $label)
                                <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'America/Sao_Paulo') === $tz ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="maintenance_mode"
                                       name="maintenance_mode" value="1"
                                       {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="maintenance_mode">Modo de Manutenção</label>
                            </div>
                            <small class="form-text">Quando ativado, apenas administradores acessam o site.</small>
                        </div>
                        <div class="col-sm-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="analytics_enabled"
                                       name="analytics_enabled" value="1"
                                       {{ ($settings['analytics_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="analytics_enabled">Analytics Habilitado</label>
                            </div>
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
