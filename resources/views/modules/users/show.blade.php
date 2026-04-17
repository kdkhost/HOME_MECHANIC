@extends('layouts.admin')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Detalhes do Usuário</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
                        <li class="breadcrumb-item active">Detalhes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user mr-2"></i> {{ $user->name ?? $user['name'] }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.edit', $user->id ?? $user['id']) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td class="font-weight-bold" width="30%">Nome</td>
                            <td>{{ $user->name ?? $user['name'] }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">E-mail</td>
                            <td>{{ $user->email ?? $user['email'] }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Função</td>
                            <td>
                                @php $role = $user->role ?? $user['role']; @endphp
                                @if($role === 'admin')
                                    <span class="badge badge-danger">Administrador</span>
                                @else
                                    <span class="badge badge-info">Usuário</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Cadastrado em</td>
                            <td>
                                @php
                                    $date = $user->created_at ?? $user['created_at'];
                                    echo is_string($date) ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i') : $date->format('d/m/Y H:i');
                                @endphp
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
