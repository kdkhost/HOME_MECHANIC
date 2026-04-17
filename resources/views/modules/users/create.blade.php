@extends('layouts.admin')
@section('title', 'Novo Usuário')
@section('page-title', 'Usuários')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-user-plus mr-2" style="color:var(--hm-primary);"></i>Novo Usuário</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-user-plus"></i> Criar Usuário</span>
    </div>
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
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
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="Nome do usuário">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="email@exemplo.com">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Senha <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required placeholder="Mínimo 8 caracteres">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Confirmar Senha <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirmation" required placeholder="Repita a senha">
                    </div>
                </div>
            </div>
            <div class="form-group" style="max-width:250px;">
                <label>Função</label>
                <select class="form-control" name="role">
                    <option value="user">Usuário</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Criar Usuário</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
