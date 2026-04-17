@extends('layouts.admin')
@section('title', 'Templates de E-mail')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email') }}">E-mail</a></li>
    <li class="breadcrumb-item active">Templates</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-envelope-open-text me-2" style="color:var(--hm-primary);"></i>Templates de E-mail</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.settings.email') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row g-4">
    @foreach($templates as $slug => $tpl)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex gap-3 align-items-start">
                <div style="width:48px;height:48px;background:var(--hm-primary-light);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-envelope" style="color:var(--hm-primary);font-size:1.2rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div style="font-weight:700;font-size:0.95rem;color:var(--hm-text);margin-bottom:0.3rem;">{{ $tpl['name'] }}</div>
                    <p style="font-size:0.84rem;color:var(--hm-text-muted);margin-bottom:0.75rem;">{{ $tpl['desc'] }}</p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @foreach($tpl['vars'] as $var)
                            <code style="background:#f1f5f9;color:var(--hm-primary);padding:0.15rem 0.5rem;border-radius:4px;font-size:0.72rem;">{{ $var }}</code>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.settings.email.templates.edit', $slug) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-pencil-alt"></i> Editar Template
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
