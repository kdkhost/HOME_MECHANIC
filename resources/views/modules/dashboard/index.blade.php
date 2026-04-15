@extends('layouts.admin')

@section('title', 'Dashboard - HomeMechanic')
@section('page-title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('styles')
<style>
.dashboard-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

.stat-card {
    background: linear-gradient(135deg, var(--bs-primary), #E55A00);
    color: white;
    border: none;
}

.stat-card .card-icon {
    color: rgba(255,255,255,0.8);
}

.activity-item {
    padding: 0.75rem;
    border-left: 3px solid var(--bs-primary);
    margin-bottom: 0.5rem;
    background: #f8f9fa;
    border-radius: 0 5px 5px 0;
}

.activity-item:hover {
    background: #e9ecef;
}

.chart-container {
    position: relative;
    height: 300px;
}

.quick-action-btn {
    background: linear-gradient(135deg, #FF6B00, #E55A00);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);
    color: white;
}

.system-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.welcome-section {
    background: linear-gradient(135deg, #FF6B00, #E55A00);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.refresh-btn {
    background: none;
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.375rem 0.75rem;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.refresh-btn:hover {
    background: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border-radius: 10px;
}

.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>
@endsection

@section('content')
<!-- Seção de Boas-vindas -->
<div class="welcome-section">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="h4 mb-2">Bem-vindo de volta, {{ $user->name }}!</h2>
            <p class="mb-0">Aqui está um resumo das atividades do seu sistema HomeMechanic.</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-light" onclick="refreshDashboard()">
                <i class="bi bi-arrow-clockwise me-2"></i>
                Atualizar Dados
            </button>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas Principais -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card stat-card">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-1" id="services-count">{{ $data['counters']['services'] }}</h3>
                        <p class="mb-0">Serviços Ativos</p>
                    </div>
                    <i class="bi bi-tools card-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card" style="background: linear-gradient(135deg, #28a745, #20c997);">
            <div class="card-body text-center text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-1" id="posts-count">{{ $data['counters']['posts_published'] }}</h3>
                        <p class="mb-0">Posts Publicados</p>
                    </div>
                    <i class="bi bi-newspaper card-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card" style="background: linear-gradient(135deg, #17a2b8, #6f42c1);">
            <div class="card-body text-center text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-1" id="photos-count">{{ $data['counters']['gallery_photos'] }}</h3>
                        <p class="mb-0">Fotos na Galeria</p>
                    </div>
                    <i class="bi bi-images card-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
            <div class="card-body text-center text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-1" id="messages-count">{{ $data['counters']['unread_messages'] }}</h3>
                        <p class="mb-0">Mensagens Não Lidas</p>
                    </div>
                    <i class="bi bi-envelope card-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Detalhadas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h4 class="text-muted">{{ $data['stats']['posts_draft'] }}</h4>
                <p class="mb-0">Posts em Rascunho</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h4 class="text-muted">{{ $data['stats']['gallery_categories'] }}</h4>
                <p class="mb-0">Categorias da Galeria</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h4 class="text-muted">{{ $data['stats']['total_messages'] }}</h4>
                <p class="mb-0">Total de Mensagens</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h4 class="text-muted">{{ $data['stats']['active_services'] }}</h4>
                <p class="mb-0">Serviços Disponíveis</p>
            </div>
        </div>
    </div>
</div>

<!-- Conteúdo Principal -->
<div class="row">
    <!-- Atividade Recente -->
    <div class="col-lg-6 mb-4">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-activity text-primary me-2"></i>
                    Atividade Recente
                </h5>
                <button class="refresh-btn" onclick="refreshActivity()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <div id="activity-list">
                    @forelse($data['recent_activity'] as $activity)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $activity['action'] }}</strong> em {{ $activity['model'] }}
                                    <br>
                                    <small class="text-muted">por {{ $activity['user'] }}</small>
                                </div>
                                <small class="text-muted">{{ $activity['formatted_time'] }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Nenhuma atividade recente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Recentes -->
    <div class="col-lg-6 mb-4">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-newspaper text-success me-2"></i>
                    Posts Recentes
                </h5>
                <a href="#" class="btn btn-sm btn-outline-success">Ver Todos</a>
            </div>
            <div class="card-body">
                @forelse($data['recent_posts'] as $post)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ Str::limit($post->title, 40) }}</h6>
                            <small class="text-muted">
                                por {{ $post->author_name ?? 'Desconhecido' }} 
                                @if($post->category_name)
                                    em {{ $post->category_name }}
                                @endif
                            </small>
                        </div>
                        <span class="badge bg-{{ $post->status === 'published' ? 'success' : 'warning' }}">
                            {{ $post->status === 'published' ? 'Publicado' : 'Rascunho' }}
                        </span>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-file-text display-4"></i>
                        <p class="mt-2">Nenhum post encontrado</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Gráficos e Mensagens -->
<div class="row">
    <!-- Gráfico de Posts -->
    <div class="col-lg-6 mb-4">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart text-info me-2"></i>
                    Posts por Mês
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="postsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagens Recentes -->
    <div class="col-lg-6 mb-4">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-envelope text-warning me-2"></i>
                    Mensagens Recentes
                </h5>
                <a href="#" class="btn btn-sm btn-outline-warning">Ver Todas</a>
            </div>
            <div class="card-body">
                @forelse($data['recent_messages'] as $message)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1 {{ !$message->read ? 'fw-bold' : '' }}">
                                {{ Str::limit($message->subject, 30) }}
                            </h6>
                            <small class="text-muted">
                                de {{ $message->name }} ({{ $message->email }})
                            </small>
                        </div>
                        <div class="text-end">
                            @if(!$message->read)
                                <span class="badge bg-danger">Nova</span>
                            @endif
                            <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-mailbox display-4"></i>
                        <p class="mt-2">Nenhuma mensagem encontrada</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas e Informações do Sistema -->
<div class="row">
    <!-- Ações Rápidas -->
    <div class="col-lg-8 mb-4">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning text-warning me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="#" class="quick-action-btn w-100 text-center">
                            <i class="bi bi-plus-circle d-block mb-2"></i>
                            Novo Serviço
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="#" class="quick-action-btn w-100 text-center">
                            <i class="bi bi-file-plus d-block mb-2"></i>
                            Novo Post
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="#" class="quick-action-btn w-100 text-center">
                            <i class="bi bi-image d-block mb-2"></i>
                            Adicionar Foto
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.documentation.index') }}" class="quick-action-btn w-100 text-center">
                            <i class="bi bi-book d-block mb-2"></i>
                            Documentação
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Sistema -->
    <div class="col-lg-4 mb-4">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="system-info">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <strong>PHP:</strong> {{ $data['system_info']['php_version'] }}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>Laravel:</strong> {{ $data['system_info']['laravel_version'] }}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>Ambiente:</strong> 
                            <span class="badge bg-{{ $data['system_info']['environment'] === 'production' ? 'success' : 'warning' }}">
                                {{ ucfirst($data['system_info']['environment']) }}
                            </span>
                        </div>
                        <div class="col-12 mb-2">
                            <strong>Manutenção:</strong>
                            <span class="badge bg-{{ $data['system_info']['maintenance_mode'] ? 'danger' : 'success' }}">
                                {{ $data['system_info']['maintenance_mode'] ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        <div class="col-12">
                            <strong>Última Atualização:</strong><br>
                            <small class="text-muted" id="last-update">{{ $data['system_info']['server_time'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Inicializar gráficos
    initializeCharts();
    
    // Auto-refresh a cada 5 minutos
    setInterval(function() {
        refreshQuickStats();
    }, 300000);
});

// Inicializar gráficos
function initializeCharts() {
    // Gráfico de Posts por Mês
    const postsCtx = document.getElementById('postsChart').getContext('2d');
    new Chart(postsCtx, {
        type: 'line',
        data: {
            labels: @json($data['charts']['posts_by_month']['labels']),
            datasets: [{
                label: 'Posts Publicados',
                data: @json($data['charts']['posts_by_month']['data']),
                borderColor: '#FF6B00',
                backgroundColor: 'rgba(255, 107, 0, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Atualizar dashboard completo
function refreshDashboard() {
    showLoading();
    
    $.ajax({
        url: '{{ route("admin.dashboard.data") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateCounters(response.data.counters);
                updateLastUpdate(response.last_updated);
                
                Toastify({
                    text: "Dashboard atualizado com sucesso!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745"
                }).showToast();
            }
        },
        error: function() {
            Toastify({
                text: "Erro ao atualizar dashboard",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545"
            }).showToast();
        },
        complete: function() {
            hideLoading();
        }
    });
}

// Atualizar estatísticas rápidas
function refreshQuickStats() {
    $.ajax({
        url: '{{ route("admin.dashboard.quick-stats") }}',
        method: 'GET',
        success: function(response) {
            updateCounters({
                services: response.services_count,
                posts_published: response.posts_published,
                gallery_photos: response.gallery_photos,
                unread_messages: response.unread_messages
            });
        }
    });
}

// Atualizar atividade
function refreshActivity() {
    // Implementar refresh da atividade via AJAX
    location.reload();
}

// Atualizar contadores
function updateCounters(counters) {
    $('#services-count').text(counters.services);
    $('#posts-count').text(counters.posts_published);
    $('#photos-count').text(counters.gallery_photos);
    $('#messages-count').text(counters.unread_messages);
    
    // Animação de pulse nos números
    $('.h2').addClass('pulse');
    setTimeout(() => $('.h2').removeClass('pulse'), 1000);
}

// Atualizar timestamp
function updateLastUpdate(timestamp) {
    $('#last-update').text(timestamp);
}

// Mostrar loading
function showLoading() {
    $('body').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"></div></div>');
}

// Ocultar loading
function hideLoading() {
    $('.loading-overlay').remove();
}

// Limpar cache
function clearCache() {
    $.ajax({
        url: '{{ route("admin.dashboard.clear-cache") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                Toastify({
                    text: response.message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745"
                }).showToast();
                
                setTimeout(() => location.reload(), 1000);
            }
        }
    });
}
</script>
@endsection