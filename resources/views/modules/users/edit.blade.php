@extends('layouts.admin')
@section('title', 'Editar Usuário')
@section('page-title', 'Usuários')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-user-edit mr-2" style="color:var(--hm-primary);"></i>Editar: {{ $user->name }}</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-user-edit"></i> Editar Usuário</span>
    </div>
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf @method('PUT')
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>
                    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nova Senha <small class="text-muted font-weight-normal">(deixe em branco para manter)</small></label>
                        <input type="password" class="form-control" name="password" placeholder="Nova senha">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Confirmar Nova Senha</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Repita a nova senha">
                    </div>
                </div>
            </div>
            <div class="form-group" style="max-width:250px;">
                <label>Função</label>
                <select class="form-control" name="role">
                    <option value="user"  {{ $user->role === 'user'  ? 'selected' : '' }}>Usuário</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
