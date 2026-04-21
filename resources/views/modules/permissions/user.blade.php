@extends('layouts.admin')

@section('title', 'Permissoes do Usuario')
@section('page-title', 'Gerenciar Permissoes')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-user-shield me-2" style="color:var(--hm-primary);"></i>
        Permissoes: {{ $user->name }}
    </h2>
    <div class="page-header-actions">
        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->permission_level >= 50 ? 'warning text-dark' : 'secondary') }}">
            @if($user->role === 'admin')
                Superadmin (Nivel {{ $user->permission_level }})
            @elseif($user->permission_level >= 50)
                Gerente (Nivel {{ $user->permission_level }})
            @else
                Usuario (Nivel {{ $user->permission_level }})
            @endif
        </span>
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

        @if($user->role === 'admin')
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Superadmin:</strong> Este usuario tem acesso total a todas as funcoes do sistema. As permissoes individuais nao se aplicam a superadmins.
        </div>
        @endif

        <div class="alert alert-secondary d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-layer-group me-2"></i>
                <strong>Sua Hierarquia:</strong> Nivel {{ auth()->user()->permission_level }}
                @if(auth()->user()->isSuperAdmin())
                    (Superadmin - pode gerenciar todos)
                @elseif(auth()->user()->permission_level >= 50)
                    (Gerente - pode gerenciar usuarios nivel 10-49)
                @else
                    (Usuario - sem permissao para gerenciar outros)
                @endif
            </div>
            <span class="badge bg-dark">Maximo nivel de permissao atribuivel: {{ auth()->user()->getMaxPermissionLevel() }}</span>
        </div>

        <form method="POST" action="{{ route('admin.permissions.user.update', $user) }}">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAll()">
                        <i class="fas fa-check-square me-1"></i>Selecionar Todos
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                        <i class="fas fa-square me-1"></i>Limpar
                    </button>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Salvar Permissoes
                </button>
            </div>

            @forelse($permissions as $module => $modulePermissions)
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <div class="form-check">
                        <input class="form-check-input module-check" type="checkbox" id="module_{{ $module }}" data-module="{{ $module }}">
                        <label class="form-check-label fw-bold" for="module_{{ $module }}">
                            <i class="fas fa-folder me-2 text-muted"></i>
                            {{ $modules[$module] ?? ucfirst($module) }}
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($modulePermissions as $permission)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input permission-check" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}" data-module="{{ $module }}" {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                    {{ $permission->name }}
                                    <span class="badge bg-{{ $permission->level >= 100 ? 'danger' : ($permission->level >= 50 ? 'warning text-dark' : ($permission->level >= 30 ? 'info' : 'success')) }} ms-1" style="font-size: 0.7rem;" title="Nivel {{ $permission->level }}">
                                        {{ $permission->level }}
                                    </span>
                                    @if(!$permission->is_active)
                                        <span class="badge bg-secondary ms-1" title="Permissao inativa no sistema">!</span>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                    <h5>Nenhuma permissao disponivel</h5>
                    <p class="text-muted">Execute o seeder de permissoes primeiro.</p>
                </div>
            </div>
            @endforelse

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar para Usuarios
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Salvar Permissoes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectAll() {
    document.querySelectorAll('.permission-check').forEach(cb => cb.checked = true);
    document.querySelectorAll('.module-check').forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll('.permission-check').forEach(cb => cb.checked = false);
    document.querySelectorAll('.module-check').forEach(cb => cb.checked = false);
}

// Sincronizar checkbox de modulo com permissoes
document.querySelectorAll('.module-check').forEach(moduleCheck => {
    moduleCheck.addEventListener('change', function() {
        const module = this.dataset.module;
        const checked = this.checked;
        document.querySelectorAll('.permission-check[data-module="' + module + '"]').forEach(permCheck => {
            permCheck.checked = checked;
        });
    });
});

// Atualizar checkbox de modulo quando permissoes mudam
document.querySelectorAll('.permission-check').forEach(permCheck => {
    permCheck.addEventListener('change', function() {
        const module = this.dataset.module;
        const moduleCheck = document.getElementById('module_' + module);
        if (moduleCheck) {
            const allChecked = document.querySelectorAll('.permission-check[data-module="' + module + '"]:checked').length;
            const total = document.querySelectorAll('.permission-check[data-module="' + module + '"]').length;
            moduleCheck.checked = allChecked === total;
            moduleCheck.indeterminate = allChecked > 0 && allChecked < total;
        }
    });
});

// Inicializar estado dos modulos
document.querySelectorAll('.module-check').forEach(moduleCheck => {
    const module = moduleCheck.dataset.module;
    const allChecked = document.querySelectorAll('.permission-check[data-module="' + module + '"]:checked').length;
    const total = document.querySelectorAll('.permission-check[data-module="' + module + '"]').length;
    moduleCheck.checked = allChecked === total;
    moduleCheck.indeterminate = allChecked > 0 && allChecked < total;
});
</script>
@endpush
