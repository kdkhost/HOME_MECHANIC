@extends('layouts.admin')
@section('title', 'Blog')
@section('page-title', 'Blog')
@section('breadcrumb')
    <li class="breadcrumb-item active">Blog</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-newspaper me-2" style="color:var(--hm-primary);"></i>Posts do Blog</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Post
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Buscar posts..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Todos os status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicados</option>
                    <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Rascunhos</option>
                    <option value="archived"  {{ request('status') === 'archived'  ? 'selected' : '' }}>Arquivados</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search"></i> Filtrar</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary btn-sm w-100"><i class="fas fa-times"></i> Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list"></i> Posts</span>
        <div class="card-tools">
            <span style="font-size:0.78rem;color:rgba(255,255,255,0.7);">
                {{ method_exists($posts, 'total') ? $posts->total() : count($posts) }} post(s)
            </span>
        </div>
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
                        <th>Publicado em</th>
                        <th style="width:110px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    @php
                        $isObj = is_object($post);
                        $id     = $isObj ? $post->id     : $post['id'];
                        $title  = $isObj ? $post->title  : $post['title'];
                        $status = $isObj ? $post->status : $post['status'];
                        $author = $isObj ? ($post->author->name ?? 'Admin') : ($post['author'] ?? 'Admin');
                        $date   = $isObj ? $post->created_at : $post['created_at'];
                        $date   = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $id }}</td>
                        <td>
                            <strong>{{ $title }}</strong>
                            @if($isObj && $post->excerpt)
                                <div class="text-muted" style="font-size:0.78rem;">{{ Str::limit($post->excerpt, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($status === 'published')
                                <span class="badge badge-success">Publicado</span>
                            @elseif($status === 'archived')
                                <span class="badge badge-secondary">Arquivado</span>
                            @else
                                <span class="badge badge-warning">Rascunho</span>
                            @endif
                        </td>
                        <td>{{ $author }}</td>
                        <td>{{ $date->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.blog.show', $id) }}" class="btn btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.blog.edit', $id) }}" class="btn btn-warning" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                                <form method="POST" action="{{ route('admin.blog.destroy', $id) }}" style="display:inline;">
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
        @if(method_exists($posts, 'links'))
        <div class="px-3 py-2">{{ $posts->withQueryString()->links() }}</div>
        @endif
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
