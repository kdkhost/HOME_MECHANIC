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
            
            <form id="serviceForm" method="POST" action="{{ route('admin.services.update', $service->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
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
<script>
$(document).ready(function() {
    
    // Valores já preenchidos pelo Blade no HTML
    const service = @json($service);
    $('#active').prop('checked', service.active);
    
    if (service.icon) {
        $('#iconPreview').attr('class', `bi ${service.icon}`);
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
                HMToast.success(data.message);
                
                setTimeout(() => {
                    window.location.href = '{{ route("admin.services.index") }}';
                }, 1500);
            } else {
                HMToast.error(data.message || 'Erro ao atualizar serviço');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            HMToast.error('Erro de conexão');
        });
    });
});
</script>
@endsection