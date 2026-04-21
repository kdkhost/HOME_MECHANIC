@extends('layouts.admin')

@section('title', 'Permissoes')
@section('page-title', 'Permissoes do Sistema')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Permissoes</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-shield-alt me-2" style="color:var(--hm-primary);"></i>
        Gerenciamento de Permissoes
    </h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nova Permissao
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @forelse($permissions as $module => $modulePermissions)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title">
                    <i class="fas fa-folder me-2 text-muted"></i>
                    {{ $modules[$module] ?? ucfirst($module) }}
                    <span class="badge bg-secondary ms-2">{{ $modulePermissions->count() }}</span>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">Ordem</th>
                                <th>Permissao</th>
                                <th>Slug</th>
                                <th>Descricao</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 150px;" class="text-end">Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modulePermissions as $permission)
                            <tr class="{{ !$permission->is_active ? 'table-secondary text-muted' : '' }}">
                                <td>{{ $permission->sort_order }}</td>
                                <td>
                                    <strong>{{ $permission->name }}</strong>
                                    @if(!$permission->is_active)
                                        <span class="badge bg-warning text-dark ms-2">Inativa</span>
                                    @endif
                                </td>
                                <td><code>{{ $permission->slug }}</code></td>
                                <td>{{ $permission->description ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.permissions.toggle', $permission) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $permission->is_active ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $permission->is_active ? 'Ativa' : 'Inativa' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta permissao?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                <h5>Nenhuma permissao cadastrada</h5>
                <p class="text-muted">Clique em "Nova Permissao" para comecar.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
