@extends('layouts.admin')
@section('title', 'Blog')
@section('page-title', 'Blog')
@section('breadcrumb')
    <li class="breadcrumb-item active">Blog</li>
@endsection

@section('styles')
<style>
.post-preview img { max-width:100%; height:auto; border-radius:6px; margin:1rem 0; }
.post-preview h2, .post-preview h3 { margin-top:1rem; margin-bottom:0.5rem; font-size:1.1rem; font-weight:600; }
.post-preview ul, .post-preview ol { padding-left:1.5rem; margin-bottom:0.75rem; }
.post-preview p { margin-bottom:0.75rem; line-height:1.6; }
</style>
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
                                <button type="button" class="btn btn-info" title="Ver" onclick="showPostModal({{ $id }}, '{{ addslashes($title) }}', '{{ addslashes($author) }}', '{{ $date->format('d/m/Y') }}', '{{ $status }}')">
                                    <i class="fas fa-eye"></i>
                                </button>
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

{{-- Modal de Visualização --}}
<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Visualizar Post</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-2 d-flex align-items-center gap-2" style="gap:0.5rem;">
                    <span id="modalStatus"></span>
                    <small class="text-muted ml-2">por <span id="modalAuthor"></span> em <span id="modalDate"></span></small>
                </div>
                <div id="modalCoverImage" class="mb-3 text-center"></div>
                <div id="modalContent" class="post-preview p-3" style="background:#f8f9fa;border-radius:8px;"></div>
            </div>
            <div class="modal-footer">
                <a href="#" id="modalEditLink" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> Editar</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const postsData = @json($posts->map(fn($p) => [
    'id' => is_object($p) ? $p->id : $p['id'],
    'content' => is_object($p) ? $p->content : $p['content'],
    'cover_image' => is_object($p) ? $p->cover_image : ($p['cover_image'] ?? null)
])->keyBy('id'));

function showPostModal(id, title, author, date, status) {
    const post = postsData[id] || {};

    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalAuthor').textContent = author;
    document.getElementById('modalDate').textContent = date;
    document.getElementById('modalEditLink').href = '{{ url("admin/blog") }}/' + id + '/edit';

    const statusBadge = status === 'published'
        ? '<span class="badge badge-success">Publicado</span>'
        : (status === 'archived' ? '<span class="badge badge-secondary">Arquivado</span>' : '<span class="badge badge-warning">Rascunho</span>');
    document.getElementById('modalStatus').innerHTML = statusBadge;

    const coverDiv = document.getElementById('modalCoverImage');
    if (post.cover_image) {
        coverDiv.innerHTML = '<img src="' + post.cover_image + '" class="img-fluid rounded" style="max-height:200px; object-fit:cover;">';
    } else {
        coverDiv.innerHTML = '';
    }

    document.getElementById('modalContent').innerHTML = post.content || '<em class="text-muted">Sem conteúdo</em>';

    $('#postModal').modal('show');
}
</script>
@endsection
