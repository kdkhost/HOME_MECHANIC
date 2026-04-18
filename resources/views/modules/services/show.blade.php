@extends('layouts.admin')

@section('title', $service->title . ' - HomeMechanic')
@section('page-title', $service->title)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Serviços</a></li>
<li class="breadcrumb-item active">{{ $service->title }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if($service->icon)
                        <i class="bi {{ $service->icon }} mr-2"></i>
                    @endif
                    {{ $service->title }}
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                @if($service->cover_image_url)
                    <div class="mb-3">
                        <img src="{{ $service->cover_image_url }}" class="img-fluid rounded" alt="{{ $service->title }}">
                    </div>
                @endif
                
                <div class="mb-3">
                    <h5>Descrição</h5>
                    <p class="text-muted">{{ $service->description }}</p>
                </div>
                
                @if($service->content)
                    <div class="mb-3">
                        <h5>Conteúdo Completo</h5>
                        <div class="content">
                            {!! nl2br(e($service->content)) !!}
                        </div>
                    </div>
                @endif
                
                <div class="mb-3">
                    <h5>Informações Técnicas</h5>
                    <dl class="row">
                        <dt class="col-sm-3">Slug:</dt>
                        <dd class="col-sm-9"><code>{{ $service->slug }}</code></dd>
                        
                        <dt class="col-sm-3">Ordem:</dt>
                        <dd class="col-sm-9">{{ $service->sort_order }}</dd>
                        
                        <dt class="col-sm-3">Criado em:</dt>
                        <dd class="col-sm-9">{{ $service->created_at->format('d/m/Y H:i') }}</dd>
                        
                        <dt class="col-sm-3">Atualizado em:</dt>
                        <dd class="col-sm-9">{{ $service->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Status</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="font-weight-bold">Status Atual:</label>
                    @if($service->active)
                        <span class="badge badge-success ml-2">Ativo</span>
                    @else
                        <span class="badge badge-secondary ml-2">Inativo</span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <label class="font-weight-bold">Destaque:</label>
                    @if($service->featured)
                        <span class="badge badge-warning ml-2">Em Destaque</span>
                    @else
                        <span class="badge badge-light ml-2">Sem Destaque</span>
                    @endif
                </div>
                
                @if($service->icon)
                    <div class="mb-3">
                        <label class="font-weight-bold">Ícone:</label>
                        <div class="mt-1">
                            <i class="bi {{ $service->icon }}" style="font-size: 2rem;"></i>
                            <code class="ml-2">{{ $service->icon }}</code>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ações Rápidas</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-{{ $service->active ? 'warning' : 'success' }} btn-block" 
                            onclick="toggleActive({{ $service->id }})">
                        <i class="bi bi-{{ $service->active ? 'pause' : 'play' }}"></i>
                        {{ $service->active ? 'Desativar' : 'Ativar' }}
                    </button>
                    
                    <button class="btn btn-outline-{{ $service->featured ? 'secondary' : 'warning' }} btn-block" 
                            onclick="toggleFeatured({{ $service->id }})">
                        <i class="bi bi-star{{ $service->featured ? '-fill' : '' }}"></i>
                        {{ $service->featured ? 'Remover Destaque' : 'Destacar' }}
                    </button>
                    
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary btn-block">
                        <i class="bi bi-pencil"></i> Editar Serviço
                    </a>
                    
                    <button class="btn btn-danger btn-block" onclick="deleteService({{ $service->id }})">
                        <i class="bi bi-trash"></i> Excluir Serviço
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function toggleActive(id) {
    try {
        const response = await fetch(`{{ route('admin.services.index') }}/${id}/toggle-active`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            HMToast.success(data.message);
            
            setTimeout(() => location.reload(), 1500);
        } else {
            HMToast.error('Erro ao alterar status');
        }
    } catch (error) {
        console.error('Erro:', error);
        HMToast.error('Erro de conexão');
    }
}

async function toggleFeatured(id) {
    try {
        const response = await fetch(`{{ route('admin.services.index') }}/${id}/toggle-featured`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            HMToast.success(data.message);
            
            setTimeout(() => location.reload(), 1500);
        } else {
            HMToast.error('Erro ao alterar destaque');
        }
    } catch (error) {
        console.error('Erro:', error);
        HMToast.error('Erro de conexão');
    }
}

async function deleteService(id) {
    const result = await Swal.fire({
        title: 'Confirmar Exclusão',
        text: 'Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`{{ route('admin.services.index') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            HMToast.success(data.message);
            
            setTimeout(() => {
                window.location.href = '{{ route("admin.services.index") }}';
            }, 1500);
        } else {
            HMToast.error('Erro ao excluir serviço');
        }
    } catch (error) {
        console.error('Erro:', error);
        HMToast.error('Erro de conexão');
    }
}
</script>
@endsection