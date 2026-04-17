@extends('layouts.admin')
@section('title', 'Analytics')
@section('page-title', 'Analytics')
@section('breadcrumb')
    <li class="breadcrumb-item active">Analytics</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-chart-line mr-2" style="color:var(--hm-primary);"></i>Estatísticas de Acesso</h2>
    <div class="page-header-actions">
        <button onclick="location.reload()" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Atualizar</button>
    </div>
</div>

<!-- Cards de estatísticas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#FF6B00,#E55A00);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number">{{ number_format($stats['total_visits']) }}</div>
                    <div class="stat-label">Total de Visitas</div>
                </div>
                <i class="fas fa-eye stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#28a745,#20c997);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number">{{ number_format($stats['unique_visits']) }}</div>
                    <div class="stat-label">Visitas Únicas</div>
                </div>
                <i class="fas fa-user stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#17a2b8,#6f42c1);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number">{{ number_format($stats['online_now']) }}</div>
                    <div class="stat-label">Online Agora</div>
                </div>
                <i class="fas fa-circle stat-icon" style="font-size:1.5rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#ffc107,#fd7e14);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number">{{ number_format($stats['avg_time']) }}s</div>
                    <div class="stat-label">Tempo Médio</div>
                </div>
                <i class="fas fa-clock stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Aviso de configuração -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-chart-bar"></i> Relatório de Acessos</span>
    </div>
    <div class="card-body">
        @if($stats['total_visits'] === 0)
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <h5>Nenhum dado de analytics ainda</h5>
                <p>Os dados de acesso serão coletados automaticamente conforme os visitantes acessam o site.</p>
                <small class="text-muted">O módulo de analytics registra visitas, dispositivos, navegadores e páginas mais acessadas.</small>
            </div>
        @else
            <div class="row">
                <div class="col-md-8">
                    <canvas id="visitsChart" height="120"></canvas>
                </div>
                <div class="col-md-4">
                    <h6 class="font-weight-bold mb-3">Resumo</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td>Total de Visitas</td><td class="font-weight-bold">{{ number_format($stats['total_visits']) }}</td></tr>
                        <tr><td>Visitas Únicas</td><td class="font-weight-bold">{{ number_format($stats['unique_visits']) }}</td></tr>
                        <tr><td>Online Agora</td><td class="font-weight-bold">{{ $stats['online_now'] }}</td></tr>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
