@extends('layouts.admin')

@section('title', 'Configurações de SEO')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configurações de SEO</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
                        <li class="breadcrumb-item active">SEO</li>
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
                                    <a href="{{ route('admin.settings.seo') }}" class="nav-link active">
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
                            <h3 class="card-title"><i class="fas fa-search mr-2"></i> Configurações de SEO</h3>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf
                            <input type="hidden" name="section" value="seo">
                            <div class="card-body">

                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <div class="form-group">
                                    <label>Título Meta (Meta Title)</label>
                                    <input type="text" class="form-control" name="meta_title"
                                           value="{{ $settings['meta_title'] ?? '' }}"
                                           placeholder="HomeMechanic - Oficina Mecânica">
                                    <small class="text-muted">Recomendado: 50-60 caracteres</small>
                                </div>

                                <div class="form-group">
                                    <label>Descrição Meta (Meta Description)</label>
                                    <textarea class="form-control" name="meta_description" rows="3"
                                              placeholder="Descrição do site para mecanismos de busca...">{{ $settings['meta_description'] ?? '' }}</textarea>
                                    <small class="text-muted">Recomendado: 150-160 caracteres</small>
                                </div>

                                <div class="form-group">
                                    <label>Palavras-chave (Meta Keywords)</label>
                                    <input type="text" class="form-control" name="meta_keywords"
                                           value="{{ $settings['meta_keywords'] ?? '' }}"
                                           placeholder="oficina, mecânica, carros, manutenção">
                                </div>

                                <hr>
                                <h5>Integrações</h5>

                                <div class="form-group">
                                    <label>Google Analytics ID</label>
                                    <input type="text" class="form-control" name="google_analytics"
                                           value="{{ $settings['google_analytics'] ?? '' }}"
                                           placeholder="G-XXXXXXXXXX ou UA-XXXXXXXX-X">
                                </div>

                                <div class="form-group">
                                    <label>Google Tag Manager ID</label>
                                    <input type="text" class="form-control" name="google_tag_manager"
                                           value="{{ $settings['google_tag_manager'] ?? '' }}"
                                           placeholder="GTM-XXXXXXX">
                                </div>

                                <div class="form-group">
                                    <label>Facebook Pixel ID</label>
                                    <input type="text" class="form-control" name="facebook_pixel"
                                           value="{{ $settings['facebook_pixel'] ?? '' }}"
                                           placeholder="XXXXXXXXXXXXXXXX">
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
