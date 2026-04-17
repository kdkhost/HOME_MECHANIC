@extends('layouts.admin')
@section('title', 'Blog')
@section('page-title', 'Blog')
@section('breadcrumb')
    <li class="breadcrumb-item active">Blog</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-newspaper mr-2" style="color:var(--hm-primary);"></i>Posts do Blog</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Post
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list"></i> Lista de Posts</span>
    </div>
    <div class="card-body p-0">
        @if(count($posts) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th>Status</th>
                            <th>Autor</th>
                            <th>Data</th>
                            <th style="width:120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $post)
                        <tr>
                            <td class="text-muted">{{ $post['id'] }}</td>
                            <td>
                                <strong>{{ $post['title'] }}</strong>
                                <div class="text-muted" style="font-size:0.78rem;">{{ $post['excerpt'] }}</div>
                            </td>
                            <td>
                                @if($post['status'] === 'published')
                                    <span class="badge badge-success">Publicado</span>
                                @else
                                    <span class="badge badge-warning">Rascunho</span>
                                @endif
                            </td>
                            <td>{{ $post['author'] }}</td>
                            <td>{{ $post['created_at']->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.blog.show', $post['id']) }}" class="btn btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.blog.edit', $post['id']) }}" class="btn btn-warning" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                                    <form method="POST" action="{{ route('admin.blog.destroy', $post['id']) }}" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-delete" title="Excluir"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h5>Nenhum post encontrado</h5>
                <p>Crie o primeiro post do blog.</p>
                <a href="{{ route('admin.blog.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Criar Post</a>
            </div>
        @endif
    </div>
</div>
@endsection
