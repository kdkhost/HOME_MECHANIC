@extends('layouts.admin')
@section('title', 'Usuários')
@section('page-title', 'Usuários')
@section('breadcrumb')
    <li class="breadcrumb-item active">Usuários</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-users me-2" style="color:var(--hm-primary);"></i>Gerenciar Usuários</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Novo Usuário
        </a>
    </div>
</div>

{{-- Stats --}}
@php
    $total  = count($users);
    $admins = collect($users)->filter(fn($u) => (is_object($u) ? $u->role : $u['role']) === 'admin')->count();
    $active = collect($users)->filter(fn($u) => (is_object($u) ? $u->email_verified_at : $u['email_verified_at']) !== null)->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#FF6B00,#E55A00);">
            <i class="fas fa-users stat-icon"></i>
            <div>
                <div class="stat-number">{{ $total }}</div>
                <div class="stat-label">Total de Usuários</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
            <i class="fas fa-shield-alt stat-icon"></i>
            <div>
                <div class="stat-number">{{ $admins }}</div>
                <div class="stat-label">Administradores</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#16a34a,#15803d);">
            <i class="fas fa-user-check stat-icon"></i>
            <div>
                <div class="stat-number">{{ $active }}</div>
                <div class="stat-label">Verificados</div>
            </div>
        </div>
    </div>
</div>

{{-- Filtro --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Buscar por nome ou e-mail..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-control form-control-sm">
                    <option value="">Todas as funções</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                    <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>Usuário</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search"></i> Filtrar</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm w-100"><i class="fas fa-times"></i> Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list"></i> Lista de Usuários</span>
        <div class="card-tools">
            <span style="font-size:0.78rem;color:rgba(255,255,255,0.75);">{{ $total }} usuário(s)</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if(count($users) > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;"></th>
                        <th>Usuário</th>
                        <th>Função</th>
                        <th>Status</th>
                        <th>Último acesso</th>
                        <th>Cadastro</th>
                        <th style="width:110px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    @php
                        $uid   = is_object($u) ? $u->id    : $u['id'];
                        $uname = is_object($u) ? $u->name  : $u['name'];
                        $uemail= is_object($u) ? $u->email : $u['email'];
                        $urole = is_object($u) ? $u->role  : $u['role'];
                        $uver  = is_object($u) ? ($u->email_verified_at ?? null) : ($u['email_verified_at'] ?? null);
                        $udate = is_object($u) ? $u->created_at : $u['created_at'];
                        $udate = is_string($udate) ? \Carbon\Carbon::parse($udate) : $udate;
                        $initials = strtoupper(substr($uname, 0, 1));
                        $isMe = $uid === auth()->id();
                    @endphp
                    <tr>
                        <td>
                            @php $avatarUrl = is_object($u) ? ($u->avatar_url ?? null) : null; @endphp
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $uname }}"
                                     style="width:38px;height:38px;border-radius:10px;object-fit:cover;box-shadow:0 2px 6px rgba(0,0,0,0.12);">
                            @else
                                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,var(--hm-primary),var(--hm-primary-dark));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                                    {{ $initials }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:0.88rem;">
                                {{ $uname }}
                                @if($isMe)
                                    <span class="badge badge-primary ms-1" style="font-size:0.65rem;">Você</span>
                                @endif
                            </div>
                            <div style="font-size:0.78rem;color:var(--hm-text-muted);">{{ $uemail }}</div>
                        </td>
                        <td>
                            @if($urole === 'admin')
                                <span class="badge badge-danger"><i class="fas fa-shield-alt me-1"></i>Admin</span>
                            @else
                                <span class="badge badge-info"><i class="fas fa-user me-1"></i>Usuário</span>
                            @endif
                        </td>
                        <td>
                            @if($uver)
                                <span class="badge badge-success"><i class="fas fa-check me-1"></i>Verificado</span>
                            @else
                                <span class="badge badge-warning"><i class="fas fa-clock me-1"></i>Pendente</span>
                            @endif
                        </td>
                        <td style="font-size:0.82rem;color:var(--hm-text-muted);">—</td>
                        <td style="font-size:0.82rem;color:var(--hm-text-muted);">{{ $udate->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.edit', $uid) }}" class="btn btn-warning" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @if(!$isMe)
                                <form method="POST" action="{{ route('admin.users.destroy', $uid) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-delete"
                                            data-name="{{ $uname }}" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h5>Nenhum usuário encontrado</h5>
            <p>Crie o primeiro usuário do sistema.</p>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Criar Usuário</a>
        </div>
        @endif
    </div>
</div>
@endsection
