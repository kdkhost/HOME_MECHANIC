

<?php $__env->startSection('title', 'Dashboard — HomeMechanic'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item active">Dashboard</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div class="dash-welcome">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="fas fa-tachometer-alt me-2"></i>Bem-vindo, <?php echo e($user->name); ?>!</h4>
            <p><?php echo e(now()->format('l, d \d\e F \d\e Y')); ?> — Painel HomeMechanic</p>
        </div>
        <button class="btn btn-light btn-sm" onclick="refreshDashboard()" id="btnRefresh">
            <i class="fas fa-sync-alt me-1" id="refreshIcon"></i> Atualizar
        </button>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card kpi-card" style="background:linear-gradient(135deg,#FF6B00,#E55A00) !important;color:#fff !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-num" id="kpi-services" style="color:#fff!important;font-size:2rem;font-weight:700;"><?php echo e($data['counters']['services']); ?></div>
                    <div class="kpi-lbl" style="color:rgba(255,255,255,0.9)!important;font-size:0.78rem;">Serviços Ativos</div>
                </div>
                <i class="fas fa-tools" style="font-size:2rem;opacity:0.7;color:#fff!important;"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card kpi-card" style="background:linear-gradient(135deg,#16a34a,#15803d) !important;color:#fff !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-num" id="kpi-posts" style="color:#fff!important;font-size:2rem;font-weight:700;"><?php echo e($data['counters']['posts_published']); ?></div>
                    <div class="kpi-lbl" style="color:rgba(255,255,255,0.9)!important;font-size:0.78rem;">Posts Publicados</div>
                </div>
                <i class="fas fa-newspaper" style="font-size:2rem;opacity:0.7;color:#fff!important;"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card kpi-card" style="background:linear-gradient(135deg,#0891b2,#0e7490) !important;color:#fff !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-num" id="kpi-photos" style="color:#fff!important;font-size:2rem;font-weight:700;"><?php echo e($data['counters']['gallery_photos']); ?></div>
                    <div class="kpi-lbl" style="color:rgba(255,255,255,0.9)!important;font-size:0.78rem;">Fotos na Galeria</div>
                </div>
                <i class="fas fa-images" style="font-size:2rem;opacity:0.7;color:#fff!important;"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card kpi-card" style="background:linear-gradient(135deg,#d97706,#b45309) !important;color:#fff !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="kpi-num" id="kpi-messages" style="color:#fff!important;font-size:2rem;font-weight:700;"><?php echo e($data['counters']['unread_messages']); ?></div>
                    <div class="kpi-lbl" style="color:rgba(255,255,255,0.9)!important;font-size:0.78rem;">Mensagens Não Lidas</div>
                </div>
                <i class="fas fa-envelope" style="font-size:2rem;opacity:0.7;color:#fff!important;"></i>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    
    <div class="col-6 col-md-3">
        <div class="card mini-stat">
            <div class="mini-stat-num" id="kpi-visits-today"><?php echo e($data['visits']['today'] ?? 0); ?></div>
            <div class="mini-stat-lbl"><i class="fas fa-eye me-1" style="color:var(--hm-primary);"></i>Visitas Hoje</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mini-stat">
            <div class="mini-stat-num" id="kpi-visits-month"><?php echo e($data['visits']['month'] ?? 0); ?></div>
            <div class="mini-stat-lbl"><i class="fas fa-calendar me-1" style="color:var(--hm-primary);"></i>Visitas no Mês</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mini-stat">
            <div class="mini-stat-num" id="kpi-online"><?php echo e($data['visits']['online'] ?? 0); ?></div>
            <div class="mini-stat-lbl"><i class="fas fa-circle me-1" style="color:#16a34a;font-size:0.6rem;"></i>Online Agora</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mini-stat">
            <div class="mini-stat-num"><?php echo e($data['stats']['total_messages']); ?></div>
            <div class="mini-stat-lbl"><i class="fas fa-inbox me-1" style="color:var(--hm-primary);"></i>Total Mensagens</div>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-chart-area"></i> Visitas — Últimos 7 dias</span>
                <div class="card-tools">
                    <span style="font-size:0.72rem;color:rgba(255,255,255,0.75);">Visitantes únicos e totais</span>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrap">
                    <canvas id="visitsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-history"></i> Atividade Recente</span>
            </div>
            <div class="card-body" style="max-height:280px;overflow-y:auto;padding-top:0.5rem!important;">
                <?php $__empty_1 = true; $__currentLoopData = $data['recent_activity']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div>
                        <div class="activity-action"><?php echo e($act['action']); ?> <span style="font-weight:400;color:var(--hm-text-muted);">em <?php echo e($act['model']); ?></span></div>
                        <div class="activity-meta"><?php echo e($act['user']); ?> · <?php echo e($act['formatted_time']); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state" style="padding:1.5rem 0;">
                    <i class="fas fa-history"></i>
                    <p>Nenhuma atividade registrada</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-newspaper"></i> Posts Recentes</span>
                <div class="card-tools">
                    <a href="<?php echo e(route('admin.blog.index')); ?>" class="btn btn-sm">Ver todos</a>
                </div>
            </div>
            <div class="card-body" style="padding-top:0.5rem!important;">
                <?php $__empty_1 = true; $__currentLoopData = $data['recent_posts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="msg-row">
                    <div class="msg-avatar"><?php echo e(strtoupper(substr($post->title ?? 'P', 0, 1))); ?></div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="msg-subject"><?php echo e(\Illuminate\Support\Str::limit($post->title ?? '', 38)); ?></div>
                        <div class="msg-from"><?php echo e($post->author_name ?? 'Autor'); ?> <?php if($post->category_name): ?>· <?php echo e($post->category_name); ?><?php endif; ?></div>
                    </div>
                    <span class="badge <?php echo e($post->status === 'published' ? 'badge-success' : 'badge-warning'); ?>">
                        <?php echo e($post->status === 'published' ? 'Publicado' : 'Rascunho'); ?>

                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state" style="padding:1.5rem 0;">
                    <i class="fas fa-file-alt"></i>
                    <p>Nenhum post encontrado</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-envelope"></i> Mensagens Recentes</span>
                <div class="card-tools">
                    <a href="<?php echo e(route('admin.contact.index')); ?>" class="btn btn-sm">Ver todas</a>
                </div>
            </div>
            <div class="card-body" style="padding-top:0.5rem!important;">
                <?php $__empty_1 = true; $__currentLoopData = $data['recent_messages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="msg-row">
                    <div class="msg-avatar"><?php echo e(strtoupper(substr($msg->name ?? 'M', 0, 1))); ?></div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="msg-subject <?php echo e(!$msg->read ? 'fw-bold' : ''); ?>">
                            <?php echo e(\Illuminate\Support\Str::limit($msg->subject ?? '', 32)); ?>

                            <?php if(!$msg->read): ?><span class="badge badge-danger ms-1" style="font-size:0.6rem;">Nova</span><?php endif; ?>
                        </div>
                        <div class="msg-from"><?php echo e($msg->name); ?> · <?php echo e(\Carbon\Carbon::parse($msg->created_at)->diffForHumans()); ?></div>
                    </div>
                    <a href="<?php echo e(route('admin.contact.show', $msg->id)); ?>" class="btn btn-warning btn-sm"><i class="fas fa-eye"></i></a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state" style="padding:1.5rem 0;">
                    <i class="fas fa-inbox"></i>
                    <p>Nenhuma mensagem</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="row g-3">
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-bolt"></i> Ações Rápidas</span>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.services.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-tools"></i> Serviços
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.blog.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-newspaper"></i> Blog
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.gallery.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-images"></i> Galeria
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.contact.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-envelope"></i> Mensagens
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-users"></i> Usuários
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.settings.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-cog"></i> Config.
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.analytics.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-chart-line"></i> Analytics
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('home')); ?>" target="_blank" class="qa-btn w-100">
                            <i class="fas fa-external-link-alt"></i> Ver Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-server"></i> Sistema</span>
            </div>
            <div class="card-body" style="padding-top:0.5rem!important;">
                <div class="sys-row">
                    <span class="sys-key">PHP</span>
                    <span style="font-weight:600;"><?php echo e($data['system_info']['php_version']); ?></span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Laravel</span>
                    <span style="font-weight:600;"><?php echo e($data['system_info']['laravel_version']); ?></span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Ambiente</span>
                    <span class="badge <?php echo e($data['system_info']['environment'] === 'production' ? 'badge-success' : 'badge-warning'); ?>">
                        <?php echo e(ucfirst($data['system_info']['environment'])); ?>

                    </span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Debug</span>
                    <span class="badge <?php echo e($data['system_info']['debug_mode'] ? 'badge-warning' : 'badge-success'); ?>">
                        <?php echo e($data['system_info']['debug_mode'] ? 'Ativo' : 'Desativado'); ?>

                    </span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Manutenção</span>
                    <span class="badge <?php echo e($data['system_info']['maintenance_mode'] ? 'badge-danger' : 'badge-success'); ?>">
                        <?php echo e($data['system_info']['maintenance_mode'] ? 'Ativo' : 'Inativo'); ?>

                    </span>
                </div>
                <div class="sys-row">
                    <span class="sys-key">Servidor</span>
                    <span style="font-size:0.75rem;color:var(--hm-text-muted);" id="serverTime"><?php echo e($data['system_info']['server_time']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>


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
                        <a href="<?php echo e(route('admin.settings.backup')); ?>" class="qa-btn w-100">
                            <i class="fas fa-shield-alt"></i> Backup
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.settings.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-cogs"></i> Configurações
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="<?php echo e(route('admin.documentation.index')); ?>" class="qa-btn w-100">
                            <i class="fas fa-book"></i> Documentação
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
            labels: <?php echo json_encode($data['charts']['visits_by_day']['labels'] ?? [], 15, 512) ?>,
            datasets: [
                {
                    label: 'Visitas Totais',
                    data: <?php echo json_encode($data['charts']['visits_by_day']['total'] ?? [], 15, 512) ?>,
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
                    data: <?php echo json_encode($data['charts']['visits_by_day']['unique'] ?? [], 15, 512) ?>,
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
        url: '<?php echo e(route("admin.dashboard.quick-stats")); ?>',
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
        url: '<?php echo e(route("admin.dashboard.quick-stats")); ?>',
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
        url: '<?php echo e(route("admin.system.clear-cache")); ?>',
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
            url: '<?php echo e(route("admin.system.migrate")); ?>',
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Seção de Boas-vindas -->
<div class="welcome-section">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="h4 mb-2">Bem-vindo de volta, <?php echo e($user->name); ?>!</h2>
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
                        <h3 class="h2 mb-1" id="services-count"><?php echo e($data['counters']['services']); ?></h3>
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
                        <h3 class="h2 mb-1" id="posts-count"><?php echo e($data['counters']['posts_published']); ?></h3>
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
                        <h3 class="h2 mb-1" id="photos-count"><?php echo e($data['counters']['gallery_photos']); ?></h3>
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
                        <h3 class="h2 mb-1" id="messages-count"><?php echo e($data['counters']['unread_messages']); ?></h3>
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
                <h4 class="text-muted"><?php echo e($data['stats']['posts_draft']); ?></h4>
                <p class="mb-0">Posts em Rascunho</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h4 class="text-muted"><?php echo e($data['stats']['gallery_categories']); ?></h4>
                <p class="mb-0">Categorias da Galeria</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h4 class="text-muted"><?php echo e($data['stats']['total_messages']); ?></h4>
                <p class="mb-0">Total de Mensagens</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h4 class="text-muted"><?php echo e($data['stats']['active_services']); ?></h4>
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
                    <?php $__empty_1 = true; $__currentLoopData = $data['recent_activity']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong><?php echo e($activity['action']); ?></strong> em <?php echo e($activity['model']); ?>

                                    <br>
                                    <small class="text-muted">por <?php echo e($activity['user']); ?></small>
                                </div>
                                <small class="text-muted"><?php echo e($activity['formatted_time']); ?></small>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Nenhuma atividade recente</p>
                        </div>
                    <?php endif; ?>
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
                <?php $__empty_1 = true; $__currentLoopData = $data['recent_posts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1"><?php echo e(Str::limit($post->title, 40)); ?></h6>
                            <small class="text-muted">
                                por <?php echo e($post->author_name ?? 'Desconhecido'); ?> 
                                <?php if($post->category_name): ?>
                                    em <?php echo e($post->category_name); ?>

                                <?php endif; ?>
                            </small>
                        </div>
                        <span class="badge bg-<?php echo e($post->status === 'published' ? 'success' : 'warning'); ?>">
                            <?php echo e($post->status === 'published' ? 'Publicado' : 'Rascunho'); ?>

                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-file-text display-4"></i>
                        <p class="mt-2">Nenhum post encontrado</p>
                    </div>
                <?php endif; ?>
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
                <?php $__empty_1 = true; $__currentLoopData = $data['recent_messages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1 <?php echo e(!$message->read ? 'fw-bold' : ''); ?>">
                                <?php echo e(Str::limit($message->subject, 30)); ?>

                            </h6>
                            <small class="text-muted">
                                de <?php echo e($message->name); ?> (<?php echo e($message->email); ?>)
                            </small>
                        </div>
                        <div class="text-end">
                            <?php if(!$message->read): ?>
                                <span class="badge bg-danger">Nova</span>
                            <?php endif; ?>
                            <br>
                            <small class="text-muted">
                                <?php echo e(\Carbon\Carbon::parse($message->created_at)->diffForHumans()); ?>

                            </small>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-mailbox display-4"></i>
                        <p class="mt-2">Nenhuma mensagem encontrada</p>
                    </div>
                <?php endif; ?>
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
                        <a href="<?php echo e(route('admin.documentation.index')); ?>" class="quick-action-btn w-100 text-center">
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
                            <strong>PHP:</strong> <?php echo e($data['system_info']['php_version']); ?>

                        </div>
                        <div class="col-12 mb-2">
                            <strong>Laravel:</strong> <?php echo e($data['system_info']['laravel_version']); ?>

                        </div>
                        <div class="col-12 mb-2">
                            <strong>Ambiente:</strong> 
                            <span class="badge bg-<?php echo e($data['system_info']['environment'] === 'production' ? 'success' : 'warning'); ?>">
                                <?php echo e(ucfirst($data['system_info']['environment'])); ?>

                            </span>
                        </div>
                        <div class="col-12 mb-2">
                            <strong>Manutenção:</strong>
                            <span class="badge bg-<?php echo e($data['system_info']['maintenance_mode'] ? 'danger' : 'success'); ?>">
                                <?php echo e($data['system_info']['maintenance_mode'] ? 'Ativo' : 'Inativo'); ?>

                            </span>
                        </div>
                        <div class="col-12">
                            <strong>Última Atualização:</strong><br>
                            <small class="text-muted" id="last-update"><?php echo e($data['system_info']['server_time']); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
            labels: <?php echo json_encode($data['charts']['posts_by_month']['labels'], 15, 512) ?>,
            datasets: [{
                label: 'Posts Publicados',
                data: <?php echo json_encode($data['charts']['posts_by_month']['data'], 15, 512) ?>,
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
        url: '<?php echo e(route("admin.dashboard.data")); ?>',
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
        url: '<?php echo e(route("admin.dashboard.quick-stats")); ?>',
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
        url: '<?php echo e(route("admin.dashboard.clear-cache")); ?>',
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
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\dashboard\index.blade.php ENDPATH**/ ?>