@extends('layouts.admin')
@section('title', 'Conteúdo do Site')
@section('page-title', 'Conteúdo do Site')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
    <li class="breadcrumb-item active">Conteúdo do Site</li>
@endsection

@section('content')
<div class="row">
    @include('modules.settings._sidebar', ['active' => 'frontend'])

    <div class="col-md-9">
        <form method="POST" action="{{ route('admin.settings.frontend.update') }}" class="ajax-form">
            @csrf

            <!-- Hero Section -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white"><h5 class="mb-0 border-0"><i class="fas fa-home"></i> Seção Principal (Hero)</h5></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Badge de Destaque</label>
                            <input type="text" name="hero_badge_text" class="form-control" value="{{ $settings['hero_badge_text'] ?? 'Bem-vindo à' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Título Principal</label>
                        <input type="text" name="hero_title" class="form-control" value="{{ $settings['hero_title'] ?? 'HOME MECHANIC' }}">
                    </div>
                    <div class="mb-3">
                        <label>Subtítulo</label>
                        <textarea name="hero_subtitle" class="form-control" rows="2">{{ $settings['hero_subtitle'] ?? 'Eleve a performance e o estilo do seu veículo...' }}</textarea>
                    </div>
                    <hr>
                    <h6>Estatísticas do Hero</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Estatística 1 (Valor)</label>
                            <input type="text" name="hero_stat1_value" class="form-control" value="{{ $settings['hero_stat1_value'] ?? '15+' }}">
                            <label class="mt-1">Rótulo</label>
                            <input type="text" name="hero_stat1_label" class="form-control" value="{{ $settings['hero_stat1_label'] ?? 'Anos Mercado' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Estatística 2 (Valor)</label>
                            <input type="text" name="hero_stat2_value" class="form-control" value="{{ $settings['hero_stat2_value'] ?? '5K+' }}">
                            <label class="mt-1">Rótulo</label>
                            <input type="text" name="hero_stat2_label" class="form-control" value="{{ $settings['hero_stat2_label'] ?? 'Projetos' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Estatística 3 (Valor)</label>
                            <input type="text" name="hero_stat3_value" class="form-control" value="{{ $settings['hero_stat3_value'] ?? '100%' }}">
                            <label class="mt-1">Rótulo</label>
                            <input type="text" name="hero_stat3_label" class="form-control" value="{{ $settings['hero_stat3_label'] ?? 'Satisfação' }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- About Section -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white"><h5 class="mb-0 border-0"><i class="fas fa-info-circle"></i> Sobre Nós</h5></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Anos Destacados</label>
                            <input type="text" name="about_years" class="form-control" value="{{ $settings['about_years'] ?? '15' }}">
                        </div>
                        <div class="col-md-8">
                            <label>Subtítulo / Rótulo</label>
                            <input type="text" name="about_subtitle" class="form-control" value="{{ $settings['about_subtitle'] ?? 'Excelência Automotiva' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Título Principal</label>
                        <input type="text" name="about_title" class="form-control" value="{{ $settings['about_title'] ?? 'Nossa Missão é Superar Suas Expectativas' }}">
                    </div>
                    <div class="mb-3">
                        <label>Texto Descritivo</label>
                        <textarea name="about_text" class="form-control" rows="4">{{ $settings['about_text'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white"><h5 class="mb-0 border-0"><i class="fas fa-bullhorn"></i> Chamada para Ação (CTA)</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Título CTA</label>
                        <input type="text" name="cta_title" class="form-control" value="{{ $settings['cta_title'] ?? 'Pronto para Transformar seu Veículo?' }}">
                    </div>
                    <div class="mb-3">
                        <label>Texto CTA</label>
                        <textarea name="cta_text" class="form-control" rows="2">{{ $settings['cta_text'] ?? 'Traga seu projeto para a equipe mais qualificada...' }}</textarea>
                    </div>
                </div>
                <div class="card-footer px-4 py-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Conteúdo do Site</button>
                </div>
            </div>
            
        </form>
    </div>
</div>
@endsection
