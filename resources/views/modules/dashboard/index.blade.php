@extends('layouts.admin')

@section('title', 'Dashboard — HomeMechanic')
@section('page-title', 'Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('styles')
<style>
/* ── Welcome ──────────────────────────────────────────── */
.dash-welcome {
    background: linear-gradient(135deg, var(--hm-primary), var(--hm-primary-dark));
    border-radius: var(--hm-radius-lg);
    padding: 1.75rem 2rem;
    color: #fff;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.dash-welcome::after {
    content: '';
    position: absolute; right: -40px; top: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.07);
    border-radius: 50%;
}
.dash-welcome h4 { font-size: 1.2rem; font-weight: 700; margin: 0 0 0.25rem; }
.dash-welcome p  { font-size: 0.85rem; opacity: 0.85; margin: 0; }

/* ── KPI cards ────────────────────────────────────────── */
.kpi-card {
    border-radius: var(--hm-radius-lg) !important;
    border: none !important;
    padding: 1.25rem !important;
    position: relative; overflow: hidden;
    transition: var(--hm-transition);
    box-shadow: var(--hm-shadow-md) !important;
    background: var(--bs-primary); /* fallback */
}
.kpi-card * { color: #fff !important; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: var(--hm-shadow-hover) !important; }
.kpi-card::after {
    content: '';
    position: absolute; right: -20px; bottom: -20px;
    width: 90px; height: 90px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    pointer-events: none;
}
.kpi-num  { font-size: 2rem; font-weight: 700; line-height: 1; color: #fff !important; }
.kpi-lbl  { font-size: 0.78rem; opacity: 0.9; margin-top: 0.2rem; color: #fff !important; }
.kpi-icon { font-size: 2rem; opacity: 0.7; color: #fff !important; }
.kpi-trend {
    font-size: 0.72rem; margin-top: 0.5rem;
    display: flex; align-items: center; gap: 0.3rem;
    opacity: 0.9;
}

/* ── Mini stat ────────────────────────────────────────── */
.mini-stat {
    background: var(--hm-card);
    border: 1px solid var(--hm-border) !important;
    border-radius: var(--hm-radius) !important;
    padding: 1rem !important;
    text-align: center;
    transition: var(--hm-transition);
}
.mini-stat:hover { border-color: var(--hm-primary) !important; }
.mini-stat-num { font-size: 1.5rem; font-weight: 700; color: var(--hm-primary); line-height: 1; }
.mini-stat-lbl { font-size: 0.75rem; color: var(--hm-text-muted); margin-top: 0.2rem; }

/* ── Activity ─────────────────────────────────────────── */
.activity-item {
    display: flex; align-items: flex-start; gap: 0.75rem;
    padding: 0.7rem 0;
    border-bottom: 1px solid var(--hm-border);
    font-size: 0.84rem;
}
.activity-item:last-child { border-bottom: none; }
.activity-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--hm-primary); flex-shrink: 0; margin-top: 5px;
}
.activity-action { font-weight: 600; color: var(--hm-text); }
.activity-meta   { font-size: 0.75rem; color: var(--hm-text-muted); }

/* ── Quick actions ────────────────────────────────────── */
.qa-btn {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.5rem; padding: 1rem 0.5rem;
    border-radius: var(--hm-radius) !important;
    background: var(--hm-primary-light) !important;
    border: 1px solid rgba(255,107,0,0.2) !important;
    color: var(--hm-primary) !important;
    font-size: 0.78rem; font-weight: 600;
    text-decoration: none !important;
    transition: var(--hm-transition);
}
.qa-btn i { font-size: 1.4rem; }
.qa-btn:hover {
    background: var(--hm-primary) !important;
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255,107,0,0.3);
}

/* ── Chart container ──────────────────────────────────── */
.chart-wrap { position: relative; height: 220px; }

/* ── Message row ──────────────────────────────────────── */
.msg-row {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.65rem 0; border-bottom: 1px solid var(--hm-border);
    font-size: 0.84rem;
}
.msg-row:last-child { border-bottom: none; }
.msg-avatar {
    width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0;
    background: var(--hm-primary-light);
    color: var(--hm-primary); font-weight: 700; font-size: 0.9rem;
    display: flex; align-items: center; justify-content: center;
}
.msg-subject { font-weight: 600; color: var(--hm-text); }
.msg-from    { font-size: 0.75rem; color: var(--hm-text-muted); }

/* ── System info ──────────────────────────────────────── */
.sys-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.5rem 0; border-bottom: 1px solid var(--hm-border);
    font-size: 0.84rem;
}
.sys-row:last-child { border-bottom: none; }
.sys-key { color: var(--hm-text-muted); font-size: 0.78rem; }
</style>
@endsection

@section('content')

{{-- Welcome --}}
<div class="dash-welcome">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4><i class="fas fa-tachometer-alt me-2"></i>Bem-vindo, {{ auth()->user()->name }}!</h4>
            <p>{{ now()->locale('pt_BR')->isoFormat('dddd, D [de] MMMM [de] YYYY') }} — Painel Home Mechanic</p>
        </div>
        <span class="badge badge-light" style="font-size:0.75rem;"><i class="fas fa-sync-alt fa-spin me-1"></i>Atualização automática</span>
    </div>
</div>

{{-- Visitas + Mini stats --}}
<div class="row g-3 mb-4">
    {{-- Visitas hoje --}}
    <div class="col-6 col-md-3">
        <div class="card mini-stat h-100">
            <div class="mini-stat-num" id="kpi-visits-today">{{ $data['visits']['today'] ?? 0 }}</div>
            <div class="mini-stat-lbl"><i class="fas fa-eye me-1" style="color:var(--hm-primary);"></i>Visitas Hoje</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mini-stat h-100">
            <div class="mini-stat-num" id="kpi-visits-month">{{ $data['visits']['month'] ?? 0 }}</div>
            <div class="mini-stat-lbl"><i class="fas fa-calendar me-1" style="color:var(--hm-primary);"></i>Visitas no Mês</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mini-stat h-100">
            <div class="mini-stat-num" id="kpi-online">{{ $data['visits']['online'] ?? 0 }}</div>
            <div class="mini-stat-lbl"><i class="fas fa-circle me-1" style="color:#16a34a;font-size:0.6rem;"></i>Online Agora</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mini-stat h-100">
            <div class="mini-stat-num">{{ $data['stats']['total_messages'] }}</div>
            <div class="mini-stat-lbl"><i class="fas fa-inbox me-1" style="color:var(--hm-primary);"></i>Total Mensagens</div>
        </div>
    </div>
</div>

{{-- Gráficos + Atividade --}}
<div class="row g-3 mb-4">
    {{-- Gráfico visitas --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-chart-area"></i> Visitas — Últimos 7 dias</span>
                <div class="card-tools">
                    <span style="font-size:0.72rem;color:rgba(255,255,255,0.75);">Visitantes únicos e totais</span>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrap">
                    <div class="activity-dot"></div>
                    <div>
                        <div class="activity-action">{{ $act['action'] }} <span style="font-weight:400;color:var(--hm-text-muted);">em {{ $act['model'] }}</span></div>
                        <div class="activity-meta">{{ $act['user'] }} · {{ $act['formatted_time'] }}</div>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:1.5rem 0;">
                    <i class="fas fa-history"></i>
                    <p>Nenhuma atividade registrada</p>
                </div>
                @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Posts + Mensagens --}}
<div class="row g-3 mb-4">
    {{-- Posts recentes --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-newspaper"></i> Posts Recentes</span>
                <div class="card-tools">
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-sm">Ver todos</a>
                </div>
            </div>
            <div class="card-body" style="padding-top:0.5rem!important;">
                @forelse($data['recent_posts'] as $post)
                <div class="msg-row">
                    <div class="msg-avatar">{{ strtoupper(substr($post->title ?? 'P', 0, 1)) }}</div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="msg-subject">{{ \Illuminate\Support\Str::limit($post->title ?? '', 38) }}</div>
                        <div class="msg-from">{{ $post->author_name ?? 'Autor' }} @if($post->category_name)· {{ $post->category_name }}@endif</div>
                    </div>
                    <span class="badge {{ $post->status === 'published' ? 'badge-success' : 'badge-warning' }}">
                        {{ $post->status === 'published' ? 'Publicado' : 'Rascunho' }}
                    </span>
                </div>
                @empty
                <div class="empty-state" style="padding:1.5rem 0;">
                    <i class="fas fa-file-alt"></i>
                    <p>Nenhum post encontrado</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Mensagens recentes --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-envelope"></i> Mensagens Recentes</span>
                <div class="card-tools">
                    <a href="{{ route('admin.contact.index') }}" class="btn btn-sm">Ver todas</a>
                </div>
            </div>
            <div class="card-body" style="padding-top:0.5rem!important;">
                @forelse($data['recent_messages'] as $msg)
                <div class="msg-row">
                    <div class="msg-avatar">{{ strtoupper(substr($msg->name ?? 'M', 0, 1)) }}</div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="msg-subject {{ !$msg->read ? 'fw-bold' : '' }}">
                            {{ \Illuminate\Support\Str::limit($msg->subject ?? '', 32) }}
                            @if(!$msg->read)<span class="badge badge-danger ms-1" style="font-size:0.6rem;">Nova</span>@endif
                        </div>
                        <div class="msg-from">{{ $msg->name }} · {{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}</div>
                    </div>
                    <a href="{{ route('admin.contact.show', $msg->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-eye"></i></a>
                </div>
                @empty
                <div class="empty-state" style="padding:1.5rem 0;">
                    <i class="fas fa-inbox"></i>
                    <p>Nenhuma mensagem</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Ações rápidas + Sistema --}}
<div class="row g-3">
    {{-- Ações rápidas --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-bolt"></i> Ações Rápidas</span>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.services.index') }}" class="qa-btn w-100">
                            <i class="fas fa-tools"></i> Serviços
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.blog.index') }}" class="qa-btn w-100">
                            <i class="fas fa-newspaper"></i> Blog
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.gallery.index') }}" class="qa-btn w-100">
                            <i class="fas fa-images"></i> Galeria
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.contact.index') }}" class="qa-btn w-100">
                            <i class="fas fa-envelope"></i> Mensagens
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.users.index') }}" class="qa-btn w-100">
                            <i class="fas fa-users"></i> Usuários
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.settings.index') }}" class="qa-btn w-100">
                            <i class="fas fa-cog"></i> Config.
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.analytics.index') }}" class="qa-btn w-100">
                            <i class="fas fa-chart-line"></i> Analytics
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('home') }}" target="_blank" class="qa-btn w-100">
                            <i class="fas fa-external-link-alt"></i> Ver Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sistema --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-server"></i> Sistema</span>
            </div>
            <div class="card-body" style="padding-top:0.5rem!important;">
                <div class="sys-row">
                    <span class="sys-key">PHP</span>
                    <span style="font-weight:600;">{{ $data['system_info']['php_version'] }}</span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Laravel</span>
                    <span style="font-weight:600;">{{ $data['system_info']['laravel_version'] }}</span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Ambiente</span>
                    <span class="badge {{ $data['system_info']['environment'] === 'production' ? 'badge-success' : 'badge-warning' }}">
                        {{ ucfirst($data['system_info']['environment']) }}
                    </span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Debug</span>
                    <span class="badge {{ $data['system_info']['debug_mode'] ? 'badge-warning' : 'badge-success' }}">
                        {{ $data['system_info']['debug_mode'] ? 'Ativo' : 'Desativado' }}
                    </span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Manutenção</span>
                    <span class="badge {{ $data['system_info']['maintenance_mode'] ? 'badge-danger' : 'badge-success' }}">
                        {{ $data['system_info']['maintenance_mode'] ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Servidor</span>
                    <span style="font-size:0.75rem;color:var(--hm-text-muted);" id="serverTime">{{ $data['system_info']['server_time'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Ferramentas de Manutenção --}}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="card-title"><i class="fas fa-wrench"></i> Ferramentas de Manutenção</span>
                <button class="btn btn-sm btn-outline-danger" onclick="dashClearAll()" id="btnClearAll">
                    <i class="fas fa-broom me-1"></i> Limpar Tudo
                </button>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <button class="qa-btn w-100 border-0" onclick="dashClearType('config')" id="btnClearConfig">
                            <i class="fas fa-sliders-h"></i> Config Cache
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button class="qa-btn w-100 border-0" onclick="dashClearType('view')" id="btnClearViews">
                            <i class="fas fa-eye"></i> Views Cache
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button class="qa-btn w-100 border-0" onclick="dashClearType('route')" id="btnClearRoutes">
                            <i class="fas fa-route"></i> Route Cache
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button class="qa-btn w-100 border-0" onclick="dashClearType('app')" id="btnClearApp">
                            <i class="fas fa-database"></i> App Cache
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button class="qa-btn w-100 border-0" onclick="dashRunMigrations()" id="btnMigrate">
                            <i class="fas fa-database"></i> Migrations
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.settings.backup') }}" class="qa-btn w-100">
                            <i class="fas fa-shield-alt"></i> Backup
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.settings.index') }}" class="qa-btn w-100">
                            <i class="fas fa-cogs"></i> Configurações
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.documentation.index') }}" class="qa-btn w-100">
                            <i class="fas fa-book"></i> Documentação
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
$(function() {
    initVisitsChart();
    // Auto-refresh contadores a cada 2 min
    setInterval(refreshQuickStats, 120000);
});

// ── Gráfico de visitas ────────────────────────────────────
function initVisitsChart() {
    var ctx = document.getElementById('visitsChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($data['charts']['visits_by_day']['labels'] ?? []),
            datasets: [
                {
                    label: 'Visitas Totais',
                    data: @json($data['charts']['visits_by_day']['total'] ?? []),
                    borderColor: '#FF6B00',
                    backgroundColor: 'rgba(255,107,0,0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#FF6B00',
                    pointRadius: 4,
                },
                {
                    label: 'Visitas Únicas',
                    data: @json($data['charts']['visits_by_day']['unique'] ?? []),
                    borderColor: '#0891b2',
                    backgroundColor: 'rgba(8,145,178,0.06)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0891b2',
                    pointRadius: 3,
                    borderDash: [4, 3],
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });
}

// ── Refresh dashboard ─────────────────────────────────────
function refreshDashboard() {
    var icon = document.getElementById('refreshIcon');
    var btn  = document.getElementById('btnRefresh');
    if (icon) icon.className = 'fas fa-spinner fa-spin me-1';
    if (btn)  btn.disabled = true;

    $.ajax({
        url: '{{ route("admin.dashboard.quick-stats") }}',
        method: 'GET',
        success: function(res) {
            // KPI cards
            $('#kpi-services').text(res.services_count  ?? 0);
            $('#kpi-posts').text(res.posts_published     ?? 0);
            $('#kpi-photos').text(res.gallery_photos     ?? 0);
            $('#kpi-messages').text(res.unread_messages  ?? 0);
            // Mini stats
            if (res.visits_today  !== undefined) $('#kpi-visits-today').text(res.visits_today);
            if (res.visits_month  !== undefined) $('#kpi-visits-month').text(res.visits_month);
            if (res.online_now    !== undefined) $('#kpi-online').text(res.online_now);
            if (res.total_messages !== undefined) {
                // total messages mini stat (4th card)
                document.querySelectorAll('.mini-stat .mini-stat-num')[3] &&
                (document.querySelectorAll('.mini-stat .mini-stat-num')[3].textContent = res.total_messages);
            }
            HMToast.success('Dashboard atualizado!', 2500);
        },
        error: function() { HMToast.error('Erro ao atualizar dashboard.'); },
        complete: function() {
            if (icon) icon.className = 'fas fa-sync-alt me-1';
            if (btn)  btn.disabled = false;
        }
    });
}

// ── Refresh rápido (silencioso) ───────────────────────────
function refreshQuickStats() {
    $.ajax({
        url: '{{ route("admin.dashboard.quick-stats") }}',
        method: 'GET',
        success: function(res) {
            $('#kpi-services').text(res.services_count || 0);
            $('#kpi-posts').text(res.posts_published || 0);
            $('#kpi-photos').text(res.gallery_photos || 0);
            $('#kpi-messages').text(res.unread_messages || 0);
            if (res.visits_today !== undefined) $('#kpi-visits-today').text(res.visits_today);
            if (res.online_now   !== undefined) $('#kpi-online').text(res.online_now);
        }
    });
}

// ── Refresh Atividade Recente ───────────────────────────────
function refreshActivity() {
    var icon = document.getElementById('activityRefreshIcon');
    if (icon) icon.className = 'fas fa-spinner fa-spin';
    
    $.ajax({
        url: '{{ route("admin.dashboard.data") }}',
        method: 'GET',
        success: function(res) {
            if (res.success && res.data && res.data.recent_activity) {
                // Re-render activity list
                var html = '';
                if (res.data.recent_activity.length === 0) {
                    html = '<div class="empty-state" style="padding:1.5rem 0;"><i class="fas fa-history"></i><p>Nenhuma atividade registrada</p></div>';
                } else {
                    res.data.recent_activity.forEach(function(act) {
                        html += '<div class="activity-item"><div class="activity-dot"></div><div><div class="activity-action">' + act.action + ' <span style="font-weight:400;color:var(--hm-text-muted);">em ' + act.model + '</span></div><div class="activity-meta">' + act.user + ' · ' + act.formatted_time + '</div></div></div>';
                    });
                }
                $('#activityList').html(html);
                HMToast.success('Atividade atualizada!', 2000);
            }
        },
        error: function() { HMToast.error('Erro ao atualizar atividade.'); },
        complete: function() {
            if (icon) icon.className = 'fas fa-sync-alt';
        }
    });
}

// ── Atualização automática em 2º plano (a cada 60 segundos) ──
(function autoRefresh() {
    console.log('Iniciando atualização automática em 2º plano...');
    setInterval(function() {
        console.log('Atualizando dashboard em 2º plano...');
        refreshQuickStats(); // Atualiza sem mostrar notificação
    }, 60000); // 60 segundos
})();

// ── Ferramentas de Manutenção (Dashboard) ─────────────────
function dashClearType(type) {
    var labels = { config: 'Configuração', view: 'Views Compiladas', route: 'Rotas', app: 'Cache de Aplicação' };
    Swal.fire({
        title: 'Limpar ' + (labels[type] || type) + '?',
        text: 'O cache selecionado será limpo.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-broom"></i> Limpar',
        cancelButtonText: 'Cancelar',
    }).then(function(r) {
        if (r.isConfirmed) _execCacheClear(type);
    });
}

function dashClearAll() {
    Swal.fire({
        title: 'Limpar TODOS os caches?',
        html: '<div style="font-size:0.88rem;color:#64748b;margin-top:0.5rem;">Serão limpos:<br>✅ Cache de aplicação<br>✅ Views compiladas<br>✅ Configurações<br>✅ Rotas</div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-broom"></i> Limpar tudo',
        cancelButtonText: 'Cancelar',
    }).then(function(r) {
        if (r.isConfirmed) _execCacheClear('all');
    });
}

function _execCacheClear(type) {
    Swal.fire({ title: 'Limpando...', text: 'Aguarde enquanto os caches são limpos.', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

    $.ajax({
        url: '{{ route("admin.system.clear-cache") }}',
        method: 'POST',
        data: JSON.stringify({ type: type }),
        contentType: 'application/json',
        success: function(data) {
            var details = (data.details || []).join('<br>');
            Swal.fire({
                title: data.success ? '✅ Cache limpo!' : '⚠️ Concluído com erros',
                html: details || data.message,
                icon: data.success ? 'success' : 'warning',
                confirmButtonColor: '#FF6B00',
            });
            if (data.success) HMToast.success(data.message);
            else HMToast.warning(data.message);
        },
        error: function(xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao limpar cache.';
            Swal.fire({ title: 'Erro', text: msg, icon: 'error', confirmButtonColor: '#FF6B00' });
            HMToast.error(msg);
        }
    });
}

function dashRunMigrations() {
    Swal.fire({
        title: 'Rodar Migrations?',
        text: 'Isso aplicará todas as migrations pendentes no banco de dados.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-database"></i> Executar',
        cancelButtonText: 'Cancelar',
    }).then(function(r) {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Executando...', text: 'Aguarde enquanto as migrations são aplicadas.', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

        $.ajax({
            url: '{{ route("admin.system.migrate") }}',
            method: 'POST',
            success: function(data) {
                Swal.fire({
                    title: data.success ? '✅ Migrations executadas!' : '❌ Erro nas Migrations',
                    html: '<pre style="text-align:left;font-size:0.8rem;max-height:300px;overflow:auto;background:#f8f9fa;padding:1rem;border-radius:8px;">' + (data.output || data.message) + '</pre>',
                    icon: data.success ? 'success' : 'error',
                    confirmButtonColor: '#FF6B00',
                });
                if (data.success) HMToast.success(data.message);
                else HMToast.error(data.message);
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao executar migrations.';
                Swal.fire({ title: 'Erro', text: msg, icon: 'error', confirmButtonColor: '#FF6B00' });
                HMToast.error(msg);
            }
        });
    });
}
</script>
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
// Função de atualizar dashboard - definida no escopo global
    }, 300000);

    // Atividades em tempo real — polling a cada 30s
    setInterval(function() {
        refreshActivity();
    }, 30000);
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
                
                HMToast.success("Dashboard atualizado com sucesso!");
            }
        },
        error: function() {
            HMToast.error("Erro ao atualizar dashboard");
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

// Atualizar atividade em tempo real
function refreshActivity() {
    const icon = document.getElementById('activityRefreshIcon');
    if (icon) icon.classList.add('fa-spin');

    $.ajax({
        url: '{{ route("admin.dashboard.recent-activity") }}',
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        success: function(response) {
            if (response.success && response.data) {
                const list = document.getElementById('activityList');
                if (!list) return;

                if (response.data.length === 0) {
                    list.innerHTML = '<div class="empty-state" style="padding:1.5rem 0;"><i class="fas fa-history"></i><p>Nenhuma atividade registrada</p></div>';
                    return;
                }

                let html = '';
                response.data.forEach(function(act) {
                    html += '<div class="activity-item">' +
                        '<div class="activity-dot"></div>' +
                        '<div>' +
                            '<div class="activity-action">' + act.action + ' <span style="font-weight:400;color:var(--hm-text-muted);">em ' + act.model + '</span></div>' +
                            '<div class="activity-meta">' + act.user + ' · ' + act.formatted_time + '</div>' +
                        '</div>' +
                    '</div>';
                });
                list.innerHTML = html;
            }
        },
        error: function() {
            // Silencioso — polling não deve incomodar o usuário
        },
        complete: function() {
            if (icon) icon.classList.remove('fa-spin');
        }
    });
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
                HMToast.success(response.message);
                
                setTimeout(() => location.reload(), 1000);
            }
        }
    });
}

// Atualizar dashboard sem refresh
function loadDashboardData() {
    console.log('Função loadDashboardData() iniciada');
    
    const btn = document.getElementById('btnRefresh');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1" id="refreshIcon"></i> Atualizando...';
    }

    console.log('Fazendo requisição AJAX para:', '{{ route("admin.dashboard.data") }}');

    $.ajax({
        url: '{{ route("admin.dashboard.data") }}',
        method: 'GET',
        success: function(response) {
            console.log('Resposta recebida:', response);
            
            if (response.success && response.data) {
                const data = response.data;
                console.log('Dados recebidos:', data);

                // Atualizar contadores principais
                if (data.counters) {
                    console.log('Atualizando contadores:', data.counters);
                    $('#services-count').text(data.counters.services || 0);
                    $('#posts-count').text(data.counters.posts_published || 0);
                    $('#photos-count').text(data.counters.gallery_photos || 0);
                    $('#messages-count').text(data.counters.unread_messages || 0);
                    
                    // Atualizar mini stats também
                    $('#kpi-services').text(data.counters.services || 0);
                    $('#kpi-posts').text(data.counters.posts_published || 0);
                    $('#kpi-photos').text(data.counters.gallery_photos || 0);
                    $('#kpi-messages').text(data.counters.unread_messages || 0);
                }

                // Atualizar timestamp
                if (response.last_updated) {
                    $('#last-update').text('Atualizado em: ' + response.last_updated);
                }

                // Notificação de sucesso
                HMToast.success('Dashboard atualizado com sucesso!');
                console.log('Dashboard atualizado com sucesso!');
            } else {
                console.error('Resposta inválida:', response);
                HMToast.error('Erro ao atualizar dashboard - resposta inválida.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro AJAX:', status, error);
            console.error('Resposta do servidor:', xhr.responseText);
            
            let message = 'Erro ao atualizar dashboard.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            HMToast.error(message);
        },
        complete: function() {
            console.log('Requisição completada');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Atualizar';
            }
        }
    });
}
</script>
@endsection