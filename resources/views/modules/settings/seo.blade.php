@extends('layouts.admin')
@section('title', 'SEO')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
    <li class="breadcrumb-item active">SEO</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-search mr-2" style="color:var(--hm-primary);"></i>Configurações de SEO</h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'seo'])

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-search"></i> Meta Tags e Integrações</span>
            </div>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="section" value="seo">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    @endif
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" value="{{ $settings['meta_title'] ?? '' }}" placeholder="HomeMechanic - Oficina Mecânica">
                        <small class="form-text">Recomendado: 50–60 caracteres</small>
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="3" placeholder="Descrição para mecanismos de busca...">{{ $settings['meta_description'] ?? '' }}</textarea>
                        <small class="form-text">Recomendado: 150–160 caracteres</small>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" value="{{ $settings['meta_keywords'] ?? '' }}" placeholder="oficina, mecânica, carros, manutenção">
                    </div>
                    <hr>
                    <h6 class="mb-3" style="font-weight:700;color:#4a5568;">Integrações</h6>
                    <div class="form-group">
                        <label>Google Analytics ID</label>
                        <input type="text" class="form-control" name="google_analytics" value="{{ $settings['google_analytics'] ?? '' }}" placeholder="G-XXXXXXXXXX">
                    </div>
                    <div class="form-group">
                        <label>Google Tag Manager ID</label>
                        <input type="text" class="form-control" name="google_tag_manager" value="{{ $settings['google_tag_manager'] ?? '' }}" placeholder="GTM-XXXXXXX">
                    </div>
                    <div class="form-group">
                        <label>Facebook Pixel ID</label>
                        <input type="text" class="form-control" name="facebook_pixel" value="{{ $settings['facebook_pixel'] ?? '' }}" placeholder="XXXXXXXXXXXXXXXX">
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
