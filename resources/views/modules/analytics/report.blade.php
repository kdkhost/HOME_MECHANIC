@extends('layouts.admin')
@section('title', 'Relatorio Analytics')
@section('page-title', 'Relatorio Analytics')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></li>
    <li class="breadcrumb-item active">Relatorio</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-file-alt"></i> Relatorio de {{ $data['period']['start'] }} a {{ $data['period']['end'] }}</span>
        <div class="card-tools">
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-center p-3 border rounded">
                    <div style="font-size:1.8rem;font-weight:700;color:var(--hm-primary);">{{ number_format($data['summary']['total_visits']) }}</div>
                    <div class="text-muted">Visitas Totais</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 border rounded">
                    <div style="font-size:1.8rem;font-weight:700;color:#28a745;">{{ number_format($data['summary']['unique_visits']) }}</div>
                    <div class="text-muted">Visitas Unicas</div>
                </div>
            </div>
        </div>

        @if(count($data['summary']['top_pages']))
        <h6 class="fw-bold mt-4 mb-2">Paginas Mais Visitadas</h6>
        <table class="table table-sm table-bordered">
            <thead><tr><th>URL</th><th class="text-end" style="width:100px;">Visitas</th></tr></thead>
            <tbody>
                @foreach($data['summary']['top_pages'] as $page)
                <tr><td>{{ $page->url }}</td><td class="text-end fw-bold">{{ number_format($page->visits) }}</td></tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(count($data['summary']['top_countries']))
        <h6 class="fw-bold mt-4 mb-2">Paises</h6>
        <table class="table table-sm table-bordered">
            <thead><tr><th>Pais</th><th class="text-end" style="width:100px;">Visitas</th></tr></thead>
            <tbody>
                @foreach($data['summary']['top_countries'] as $country)
                <tr><td>{{ $country->country }}</td><td class="text-end fw-bold">{{ number_format($country->visits) }}</td></tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
