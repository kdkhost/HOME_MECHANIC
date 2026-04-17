@extends('layouts.admin')
@section('title', 'SEO')
@section('page-title', 'SEO')
@section('breadcrumb')
    <li class="breadcrumb-item active">SEO</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-search me-2" style="color:var(--hm-primary);"></i>
        Configurações de SEO
    </h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.seo.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Configuração
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.seo.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="mb-1" style="font-size:0.78rem;font-weight:600;color:#718096;text-transform:uppercase;letter-spacing:1px;">Tipo de Página</label>
                <select name="page_type" class="form-control form-control-sm">
                    <option value="">Todos os tipos</option>
                    @foreach($pageTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('page_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="mb-1" style="font-size:0.78rem;font-weight:600;color:#718096;text-transform:uppercase;letter-spacing:1px;">Buscar</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Título, descrição ou identificador..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Atalhos rápidos --}}
<div class="row g-3 mb-4">
    @foreach($pageTypes as $key => $label)
    <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('admin.seo.create', ['page_type' => $key]) }}"
           class="card text-center p-3 h-100 text-decoration-none"
           style="border:1px solid {{ request('page_type') == $key ? 'var(--hm-primary)' : 'var(--hm-border)' }} !important; transition:all 0.2s;">
            <i class="fas fa-{{ match($key) {
                'home' => 'home',
                'services' => 'tools',
                'gallery' => 'images',
                'blog' => 'newspaper',
                'contact' => 'envelope',
                'about' => 'info-circle',
                default => 'file'
            } }} mb-2" style="font-size:1.5rem; color:var(--hm-primary);"></i>
            <div style="font-size:0.78rem; font-weight:600; color:#4a5568;">{{ $label }}</div>
            @php
                $exists = $seoSettings->where('page_type', $key)->count();
            @endphp
            <div class="mt-1">
                @if($exists)
                    <span class="badge badge-success" style="font-size:0.65rem;">Configurado</span>
                @else
                    <span class="badge badge-warning" style="font-size:0.65rem;">Pendente</span>
                @endif
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- Tabela --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list"></i> Configurações Cadastradas</span>
        <div class="card-tools">
            <span style="font-size:0.8rem; color:rgba(255,255,255,0.7);">
                {{ $seoSettings->total() }} registro(s)
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($seoSettings->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Página</th>
                        <th>Meta Title</th>
                        <th>Meta Description</th>
                        <th>Robots</th>
                        <th>OG</th>
                        <th>Schema</th>
                        <th style="width:100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seoSettings as $seo)
                    <tr>
                        <td>
                            <span class="badge badge-primary">{{ $pageTypes[$seo->page_type] ?? $seo->page_type }}</span>
                            @if($seo->page_identifier)
                                <br><small class="text-muted">{{ $seo->page_identifier }}</small>
                            @endif
                        </td>
                        <td>
                            @if($seo->meta_title)
                                <div style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:0.85rem;">
                                    {{ $seo->meta_title }}
                                </div>
                                @php $len = strlen($seo->meta_title); @endphp
                                <small class="{{ $len < 30 || $len > 60 ? 'text-warning' : 'text-success' }}">
                                    {{ $len }} chars
                                </small>
                            @else
                                <span class="text-muted" style="font-size:0.82rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($seo->meta_description)
                                <div style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:0.82rem; color:#718096;">
                                    {{ $seo->meta_description }}
                                </div>
                                @php $len = strlen($seo->meta_description); @endphp
                                <small class="{{ $len < 120 || $len > 160 ? 'text-warning' : 'text-success' }}">
                                    {{ $len }} chars
                                </small>
                            @else
                                <span class="text-muted" style="font-size:0.82rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $seo->index ? 'badge-success' : 'badge-danger' }}">
                                {{ $seo->index ? 'index' : 'noindex' }}
                            </span>
                            <span class="badge {{ $seo->follow ? 'badge-success' : 'badge-danger' }}">
                                {{ $seo->follow ? 'follow' : 'nofollow' }}
                            </span>
                        </td>
                        <td>
                            @if($seo->og_title || $seo->og_image)
                                <i class="fas fa-check-circle text-success"></i>
                            @else
                                <i class="fas fa-times-circle text-muted"></i>
                            @endif
                        </td>
                        <td>
                            @if($seo->schema_markup)
                                <i class="fas fa-check-circle text-success"></i>
                            @else
                                <i class="fas fa-times-circle text-muted"></i>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.seo.create', ['page_type' => $seo->page_type, 'page_identifier' => $seo->page_identifier]) }}"
                                   class="btn btn-warning" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.seo.destroy', $seo->id) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-delete" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2">
            {{ $seoSettings->withQueryString()->links() }}
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h5>Nenhuma configuração SEO encontrada</h5>
            <p>Clique em um dos atalhos acima para configurar o SEO de cada página.</p>
            <a href="{{ route('admin.seo.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Criar Configuração
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
