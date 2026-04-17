@extends('layouts.admin')
@section('title', 'Usuários')
@section('page-title', 'Usuários')
@section('breadcrumb')
    <li class="breadcrumb-item active">Usuários</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-users mr-2" style="color:var(--hm-primary);"></i>Gerenciar Usuários</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Usuário</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list"></i> Lista de Usuários</span>
    </div>
    <div class="card-body p-0">
        @if(count($users) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Função</th>
                            <th>Status</th>
                            <th>Cadastro</th>
                            <th style="width:120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @php
                            $uid   = $user['id']   ?? $user->id;
                            $uname = $user['name']  ?? $user->name;
                            $uemail= $user['email'] ?? $user->email;
                            $urole = $user['role']  ?? $user->role;
                            $uver  = $user['email_verified_at'] ?? $user->email_verified_at ?? null;
                            $udate = $user['created_at'] ?? $user->created_at;
                            $udate = is_string($udate) ? \Carbon\Carbon::parse($udate) : $udate;
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $uid }}</td>
                            <td><strong>{{ $uname }}</strong></td>
                            <td>{{ $uemail }}</td>
                            <td>
                                @if($urole === 'admin')
                                    <span class="badge badge-danger">Administrador</span>
                                @else
                                    <span class="badge badge-info">Usuário</span>
                                @endif
                            </td>
                            <td>
                                @if($uver)
                                    <span class="badge badge-success">Verificado</span>
                                @else
                                    <span class="badge badge-warning">Pendente</span>
                                @endif
                            </td>
                            <td>{{ $udate->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $uid) }}" class="btn btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.users.edit', $uid) }}" class="btn btn-warning" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                                    @if($uid !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $uid) }}" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-delete" title="Excluir"><i class="fas fa-trash"></i></button>
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
