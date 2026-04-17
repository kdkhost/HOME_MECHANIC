@extends('layouts.admin')

@section('title', 'Analytics - HomeMechanic')
@section('page-title', 'Analytics')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Analytics</li>
@endsection

@section('content')
<div class="row">
    <!-- Estatísticas Principais -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($stats['total_visits'] ?? 0) }}</h3>
                <p>Total de Visitas</p>
            </div>
            <div class="icon">
                <i class="fas fa-eye"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($stats['unique_visits'] ?? 0) }}</h3>
                <p>Visitas Únicas</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['online_now'] ?? 0 }}</h3>
                <p>Online Agora</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($stats['avg_time'] ?? 0, 0) }}s</h3>
                <p>Tempo Médio</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Visão Geral do Analytics
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Módulo em Desenvolvimento</strong><br>
                    O módulo de Analytics está sendo desenvolvido. Em breve você terá acesso a:
                    <ul class="mb-0 mt-2">
                        <li>Gráficos detalhados de visitas</li>
                        <li>Análise de dispositivos e navegadores</li>
                        <li>Mapa de visitantes por país</li>
                        <li>Páginas mais visitadas</li>
                        <li>Fontes de tráfego</li>
                        <li>Relatórios personalizados</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
