@extends('layouts.admin')
@section('title', 'Novo Post')
@section('page-title', 'Blog')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
    <li class="breadcrumb-item active">Novo Post</li>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
.note-editor { background:#fff; }
.note-editor .note-toolbar { background:#f8f9fa; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-plus-circle mr-2" style="color:var(--hm-primary);"></i>Novo Post</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-edit"></i> Criar Post</span>
    </div>
    <form method="POST" action="{{ route('admin.blog.store') }}" id="postForm">
        @csrf
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>
                    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="form-group">
                <label>Título <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" value="{{ old('title') }}" required placeholder="Título do post">
            </div>
            <div class="form-group mb-4">
                <label class="form-label font-weight-bold">Imagem de Capa</label>
                <x-filepond name="cover_image" />
                <small class="text-muted">Recomendado: 1200x600px. Arraste e solte para enviar.</small>
            </div>
            <div class="form-group">
                <label>Conteúdo <span class="text-danger">*</span></label>
                <textarea id="summernote" name="content">{{ old('content') }}</textarea>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status" style="max-width:200px;">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Post</button>
            <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    $('#summernote').summernote({
        height: 300,
        lang: 'pt-BR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        styleTags: ['p', 'h2', 'h3', 'h4'],
        callbacks: {
            onImageUpload: function(files) {
                alert('Use o botão Inserir > Imagem e cole a URL da imagem. Upload direto não está disponível.');
            }
        }
    });
});
</script>
@endsection
