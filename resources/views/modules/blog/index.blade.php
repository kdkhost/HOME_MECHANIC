@extends('layouts.admin')

@section('title', 'Blog')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Blog</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Blog</li>
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
                                <i class="bi bi-newspaper"></i>
                                Posts do Blog
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.blog.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus"></i> Novo Post
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de posts -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if(count($posts) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Título</th>
                                                <th>Status</th>
                                                <th>Autor</th>
                                                <th>Data</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($posts as $post)
                                            <tr>
                                                <td>{{ $post['id'] }}</td>
                                                <td>
                                                    <strong>{{ $post['title'] }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $post['excerpt'] }}</small>
                                                </td>
                                                <td>
                                                    @if($post['status'] === 'published')
                                                        <span class="badge badge-success">Publicado</span>
                                                    @else
                                                        <span class="badge badge-warning">Rascunho</span>
                                                    @endif
                                                </td>
                                                <td>{{ $post['author'] }}</td>
                                                <td>{{ $post['created_at']->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.blog.show', $post['id']) }}" 
                                                           class="btn btn-info" title="Visualizar">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.blog.edit', $post['id']) }}" 
                                                           class="btn btn-warning" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.blog.destroy', $post['id']) }}" 
                                                              style="display: inline;" 
                                                              onsubmit="return confirm('Tem certeza que deseja excluir este post?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" title="Excluir">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-newspaper" style="font-size: 4rem; color: #ccc;"></i>
                                    <h4 class="mt-3 text-muted">Nenhum post encontrado</h4>
                                    <p class="text-muted">Comece criando seu primeiro post do blog.</p>
                                    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus"></i> Criar Primeiro Post
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
    // Inicializar DataTable se houver posts
    @if(count($posts) > 0)
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