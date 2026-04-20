@extends('layouts.admin')
@section('title', 'Visualizar Post')
@section('page-title', 'Blog')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
    <li class="breadcrumb-item active">Visualizar</li>
@endsection

@section('styles')
<style>
.post-content img { max-width:100%; height:auto; border-radius:6px; margin:1rem 0; }
.post-content h2, .post-content h3 { margin-top:1.5rem; margin-bottom:0.75rem; font-weight:600; }
.post-content ul, .post-content ol { padding-left:1.5rem; margin-bottom:1rem; }
.post-content p { margin-bottom:1rem; line-height:1.7; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-newspaper mr-2" style="color:var(--hm-primary);"></i>{{ $post->title }}</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.blog.edit', $post->id) }}" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> Editar</a>
        <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-file-alt"></i> Detalhes do Post</span>
    </div>
    <div class="card-body">
        <div class="mb-3 d-flex align-items-center gap-2" style="gap:0.5rem;">
            @if($post->status === 'published')
                <span class="badge badge-success">Publicado</span>
            @else
                <span class="badge badge-warning">Rascunho</span>
            @endif
            <small class="text-muted ml-2">por {{ $post->author?->name ?? 'Admin' }}</small>
        </div>

        @if($post->cover_image)
        <div class="mb-3">
            <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="img-fluid rounded" style="max-height:300px; object-fit:cover;">
        </div>
        @endif

        <div class="post-content p-3" style="background:#f8f9fa;border-radius:8px;">
            {!! $post->content !!}
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>
@endsection
