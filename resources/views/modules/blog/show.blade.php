@extends('layouts.admin')

@section('title', 'Visualizar Post')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Visualizar Post</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
                        <li class="breadcrumb-item active">Visualizar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $post['title'] }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.blog.edit', $post['id']) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        @if($post['status'] === 'published')
                            <span class="badge badge-success">Publicado</span>
                        @else
                            <span class="badge badge-warning">Rascunho</span>
                        @endif
                        <small class="text-muted ml-2">por {{ $post['author'] }}</small>
                    </div>
                    <div>{!! nl2br(e($post['content'])) !!}</div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
