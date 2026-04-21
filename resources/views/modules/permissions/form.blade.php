@extends('layouts.admin')

@section('title', $permission ? 'Editar Permissao' : 'Nova Permissao')
@section('page-title', $permission ? 'Editar Permissao' : 'Nova Permissao')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissoes</a></li>
    <li class="breadcrumb-item active">{{ $permission ? 'Editar' : 'Nova' }}</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas {{ $permission ? 'fa-edit' : 'fa-plus' }} me-2" style="color:var(--hm-primary);"></i>
        {{ $permission ? 'Editar Permissao' : 'Nova Permissao' }}
    </h2>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ $permission ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}">
                    @csrf
                    @if($permission)
                        @method('PUT')
                    @endif

                    @if(!$permission)
                    {{-- Campos editaveis apenas na criacao --}}
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="ex: users.create" required>
                        <div class="form-text">Identificador unico. Formato recomendado: modulo.acao (ex: users.view)</div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module" class="form-label">Modulo <span class="text-danger">*</span></label>
                                <select class="form-select @error('module') is-invalid @enderror" id="module" name="module" required>
                                    <option value="">Selecione...</option>
                                    @foreach($modules as $key => $name)
                                        <option value="{{ $key }}" {{ old('module') == $key ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('module')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="action" class="form-label">Acao <span class="text-danger">*</span></label>
                                <select class="form-select @error('action') is-invalid @enderror" id="action" name="action" required>
                                    <option value="">Selecione...</option>
                                    @foreach($actions as $key => $name)
                                        <option value="{{ $key }}" {{ old('action') == $key ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('action')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- Campos bloqueados na edicao --}}
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" value="{{ $permission->slug }}" disabled>
                        <input type="hidden" name="slug" value="{{ $permission->slug }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Modulo</label>
                                <input type="text" class="form-control" value="{{ $modules[$permission->module] ?? $permission->module }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Acao</label>
                                <input type="text" class="form-control" value="{{ $actions[$permission->action] ?? $permission->action }}" disabled>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name ?? '') }}" placeholder="ex: Criar Usuario" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descricao</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Descricao detalhada da permissao">{{ old('description', $permission->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($permission)
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Ordem de Exibicao</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $permission->sort_order) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $permission->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Permissao ativa
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ $permission ? 'Salvar Alteracoes' : 'Criar Permissao' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
