@extends('layouts.admin')

@section('title', 'Configurações de E-mail')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configurações de E-mail</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
                        <li class="breadcrumb-item active">E-mail</li>
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
                                    <a href="{{ route('admin.settings.email') }}" class="nav-link active">
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
                            <h3 class="card-title"><i class="fas fa-envelope mr-2"></i> Configurações de E-mail (SMTP)</h3>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf
                            <input type="hidden" name="section" value="email">
                            <div class="card-body">

                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <div class="form-group">
                                    <label>Driver de E-mail</label>
                                    <select class="form-control" name="mail_driver">
                                        <option value="smtp" {{ ($settings['mail_driver'] ?? '') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ ($settings['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        <option value="log" {{ ($settings['mail_driver'] ?? '') === 'log' ? 'selected' : '' }}>Log (Desenvolvimento)</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Servidor SMTP</label>
                                            <input type="text" class="form-control" name="mail_host"
                                                   value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}"
                                                   placeholder="smtp.gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Porta</label>
                                            <input type="number" class="form-control" name="mail_port"
                                                   value="{{ $settings['mail_port'] ?? '587' }}"
                                                   placeholder="587">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Usuário SMTP</label>
                                            <input type="text" class="form-control" name="mail_username"
                                                   value="{{ $settings['mail_username'] ?? '' }}"
                                                   placeholder="seu@email.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Senha SMTP</label>
                                            <input type="password" class="form-control" name="mail_password"
                                                   value="{{ $settings['mail_password'] ?? '' }}"
                                                   placeholder="••••••••">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Criptografia</label>
                                    <select class="form-control" name="mail_encryption">
                                        <option value="tls" {{ ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ ($settings['mail_encryption'] ?? '') === '' ? 'selected' : '' }}>Nenhuma</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>E-mail Remetente</label>
                                            <input type="email" class="form-control" name="mail_from_address"
                                                   value="{{ $settings['mail_from_address'] ?? '' }}"
                                                   placeholder="noreply@seusite.com.br">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nome Remetente</label>
                                            <input type="text" class="form-control" name="mail_from_name"
                                                   value="{{ $settings['mail_from_name'] ?? '' }}"
                                                   placeholder="HomeMechanic">
                                        </div>
                                    </div>
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
