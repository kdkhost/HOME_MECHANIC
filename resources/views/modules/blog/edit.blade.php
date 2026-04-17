@extends('layouts.admin')

@section('title', 'Editar Post')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Editar Post</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit mr-2"></i> Editar: {{ $post['title'] }}</h3>
                </div>
                <form method="POST" action="{{ route('admin.blog.update', $post['id']) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" class="form-control" name="title" value="{{ old('title', $post['title']) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Conteúdo</label>
                            <textarea class="form-control" name="content" rows="10" required>{{ old('content', $post['content']) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="draft" {{ $post['status'] === 'draft' ? 'selected' : '' }}>Rascunho</option>
                                <option value="published" {{ $post['status'] === 'published' ? 'selected' : '' }}>Publicado</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar Alterações</button>
                        <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
