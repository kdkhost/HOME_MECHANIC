@extends('layouts.admin')

@section('title', 'Usuários')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Usuários</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Usuários</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Header com botão de criar -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-people"></i>
                                Gerenciar Usuários
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus"></i> Novo Usuário
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de usuários -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if(count($users) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Email</th>
                                                <th>Função</th>
                                                <th>Status</th>
                                                <th>Cadastro</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                            <tr>
                                                <td>{{ $user['id'] ?? $user->id }}</td>
                                                <td>
                                                    <strong>{{ $user['name'] ?? $user->name }}</strong>
                                                </td>
                                                <td>{{ $user['email'] ?? $user->email }}</td>
                                                <td>
                                                    @php
                                                        $role = $user['role'] ?? $user->role;
                                                    @endphp
                                                    @if($role === 'admin')
                                                        <span class="badge badge-danger">Administrador</span>
                                                    @else
                                                        <span class="badge badge-info">Usuário</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $verified = $user['email_verified_at'] ?? $user->email_verified_at ?? null;
                                                    @endphp
                                                    @if($verified)
                                                        <span class="badge badge-success">Verificado</span>
                                                    @else
                                                        <span class="badge badge-warning">Pendente</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $createdAt = $user['created_at'] ?? $user->created_at;
                                                        $date = is_string($createdAt) ? \Carbon\Carbon::parse($createdAt) : $createdAt;
                                                    @endphp
                                                    {{ $date->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.users.show', $user['id'] ?? $user->id) }}" 
                                                           class="btn btn-info" title="Visualizar">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.users.edit', $user['id'] ?? $user->id) }}" 
                                                           class="btn btn-warning" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        @if(($user['id'] ?? $user->id) !== auth()->id())
                                                        <form method="POST" action="{{ route('admin.users.destroy', $user['id'] ?? $user->id) }}" 
                                                              style="display: inline;" 
                                                              onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" title="Excluir">
                                                                <i class="bi bi-trash"></i>
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
                                <div class="text-center py-5">
                                    <i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
                                    <h4 class="mt-3 text-muted">Nenhum usuário encontrado</h4>
                                    <p class="text-muted">Comece criando o primeiro usuário.</p>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus"></i> Criar Primeiro Usuário
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar DataTable se houver usuários
    @if(count($users) > 0)
    $('.table').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        order: [[0, 'desc']]
    });
    @endif
});
</script>
@endsection