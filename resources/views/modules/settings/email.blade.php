@extends('layouts.admin')
@section('title', 'E-mail (SMTP)')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
    <li class="breadcrumb-item active">E-mail</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-envelope mr-2" style="color:var(--hm-primary);"></i>Configurações de E-mail</h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'email'])

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-envelope"></i> Configurações SMTP</span>
            </div>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="section" value="email">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    @endif
                    <div class="form-group">
                        <label>Driver de E-mail</label>
                        <select class="form-control" name="mail_driver" style="max-width:200px;">
                            <option value="smtp"     {{ ($settings['mail_driver'] ?? '') === 'smtp'     ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ ($settings['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="log"      {{ ($settings['mail_driver'] ?? '') === 'log'      ? 'selected' : '' }}>Log (Dev)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Servidor SMTP</label>
                                <input type="text" class="form-control" name="mail_host" value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}" placeholder="smtp.gmail.com">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Porta</label>
                                <input type="number" class="form-control" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" placeholder="587">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Usuário SMTP</label>
                                <input type="text" class="form-control" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" placeholder="seu@email.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Senha SMTP</label>
                                <input type="password" class="form-control" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}" placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="max-width:200px;">
                        <label>Criptografia</label>
                        <select class="form-control" name="mail_encryption">
                            <option value="tls" {{ ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value=""    {{ ($settings['mail_encryption'] ?? '') === ''    ? 'selected' : '' }}>Nenhuma</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail Remetente</label>
                                <input type="email" class="form-control" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="noreply@seusite.com.br">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nome Remetente</label>
                                <input type="text" class="form-control" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}" placeholder="HomeMechanic">
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
