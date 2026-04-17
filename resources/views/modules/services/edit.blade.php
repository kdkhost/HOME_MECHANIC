@extends('layouts.admin')

@section('title', 'Editar Serviço - HomeMechanic')
@section('page-title', 'Editar Serviço')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Serviços</a></li>
<li class="breadcrumb-item active">Editar: {{ $service->title }}</li>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@6/dist/dropzone.css" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-pencil mr-2"></i>
                    Editar Serviço: {{ $service->title }}
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <form id="serviceForm" action="{{ route('admin.services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @include('modules.services._form')
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Atualizar Serviço
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary ml-2">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://unpkg.com/dropzone@6/dist/dropzone-min.js"></script>
<script>
$(document).ready(function() {
    // Configurar Dropzone
    Dropzone.autoDiscover = false;
    
    // Preencher formulário com dados do serviço
    const service = @json($service);
    
    $('#title').val(service.title);
    $('#slug').val(service.slug);
    $('#description').val(service.description);
    $('#content').val(service.content);
    $('#icon').val(service.icon);
    $('#cover_image').val(service.cover_image);
    $('#featured').prop('checked', service.featured);
    $('#sort_order').val(service.sort_order);
    $('#active').prop('checked', service.active);
    
    // Atualizar preview do ícone
    if (service.icon) {
        $('#iconPreview').attr('class', `bi ${service.icon}`);
    }
    
    // Mostrar preview da imagem se existir
    if (service.cover_thumbnail_url) {
        $('#imagePreview').html(`<img src="${service.cover_thumbnail_url}" class="img-thumbnail" style="max-height: 100px;">`);
        $('#uploadPlaceholder').hide();
    }
    
    $('#serviceForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toastify({
                    text: data.message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745"
                }).showToast();
                
                setTimeout(() => {
                    window.location.href = '{{ route("admin.services.index") }}';
                }, 1500);
            } else {
                Toastify({
                    text: data.message || 'Erro ao atualizar serviço',
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Toastify({
                text: 'Erro de conexão',
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545"
            }).showToast();
        });
    });
});
</script>
@endsection