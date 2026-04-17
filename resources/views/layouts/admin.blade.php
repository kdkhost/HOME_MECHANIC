<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HomeMechanic Admin</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @yield('styles')
</head>
<body>

<!-- Preloader -->
<div id="preloader">
    <div class="spinner-border" role="status"></div>
    <p>HomeMechanic</p>
</div>

<div id="hm-wrapper">

    <!-- ══ SIDEBAR ══════════════════════════════════════════ -->
    <aside id="hm-sidebar">

        <!-- Logo -->
        <a href="{{ route('admin.dashboard.index') }}" class="hm-brand">
            <i class="fas fa-tools"></i>
            <span>HomeMechanic</span>
        </a>

        <!-- User -->
        <div class="hm-user">
            <i class="fas fa-user-circle fa-2x" style="color:var(--hm-primary);flex-shrink:0;"></i>
            <div class="hm-user-info">
                <strong>{{ Auth::user()->name ?? 'Administrador' }}</strong>
                <small>{{ Auth::user()->email ?? '' }}</small>
            </div>
        </div>

        <!-- Nav -->
        <nav class="hm-nav">

            <a href="{{ route('admin.dashboard.index') }}" class="hm-nav-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
            </a>

            <div class="hm-nav-header">CONTEÚDO</div>

            <a href="{{ route('admin.services.index') }}" class="hm-nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i><span>Serviços</span>
            </a>

            <a href="{{ route('admin.gallery.index') }}" class="hm-nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                <i class="fas fa-images"></i><span>Galeria</span>
            </a>

            <a href="{{ route('admin.blog.index') }}" class="hm-nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i><span>Blog</span>
            </a>

            <a href="{{ route('admin.contact.index') }}" class="hm-nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i><span>Mensagens</span>
            </a>

            <a href="{{ route('admin.upload.index') }}" class="hm-nav-link {{ request()->routeIs('admin.upload.*') ? 'active' : '' }}">
                <i class="fas fa-cloud-upload-alt"></i><span>Upload de Arquivos</span>
            </a>

            <div class="hm-nav-header">MARKETING</div>

            <a href="{{ route('admin.seo.index') }}" class="hm-nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                <i class="fas fa-search"></i><span>SEO</span>
            </a>

            <a href="{{ route('admin.analytics.index') }}" class="hm-nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i><span>Analytics</span>
            </a>

            <div class="hm-nav-header">SISTEMA</div>

            <!-- Configurações (accordion) -->
            <div class="hm-nav-group {{ request()->routeIs('admin.settings.*') ? 'open' : '' }}">
                <button class="hm-nav-link hm-nav-toggle" onclick="toggleGroup(this)">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                    <i class="fas fa-angle-down hm-arrow ml-auto"></i>
                </button>
                <div class="hm-nav-sub">
                    <a href="{{ route('admin.settings.index') }}" class="hm-nav-link hm-nav-sub-link {{ request()->routeIs('admin.settings.index') || request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                        <i class="far fa-circle" style="font-size:0.5rem;"></i><span>Geral</span>
                    </a>
                    <a href="{{ route('admin.settings.seo') }}" class="hm-nav-link hm-nav-sub-link {{ request()->routeIs('admin.settings.seo') ? 'active' : '' }}">
                        <i class="far fa-circle" style="font-size:0.5rem;"></i><span>SEO</span>
                    </a>
                    <a href="{{ route('admin.settings.email') }}" class="hm-nav-link hm-nav-sub-link {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                        <i class="far fa-circle" style="font-size:0.5rem;"></i><span>E-mail (SMTP)</span>
                    </a>
                    <a href="{{ route('admin.settings.backup') }}" class="hm-nav-link hm-nav-sub-link {{ request()->routeIs('admin.settings.backup') ? 'active' : '' }}">
                        <i class="far fa-circle" style="font-size:0.5rem;"></i><span>Backup / Manutenção</span>
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.users.index') }}" class="hm-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i><span>Usuários</span>
            </a>

            <a href="{{ route('admin.documentation.index') }}" class="hm-nav-link {{ request()->routeIs('admin.documentation.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i><span>Documentação</span>
            </a>

        </nav>
    </aside>

    <!-- ══ MAIN ══════════════════════════════════════════════ -->
    <div id="hm-main">

        <!-- Topbar -->
        <header id="hm-topbar">
            <div class="hm-topbar-left">
                <button id="hm-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('admin.dashboard.index') }}" class="hm-topbar-home">
                    <i class="fas fa-home"></i>
                </a>
            </div>
            <div class="hm-topbar-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="hm-topbar-btn" data-toggle="dropdown">
                        <i class="far fa-bell"></i>
                        <span class="hm-badge">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <span class="dropdown-item text-muted" style="font-size:0.82rem;">Nenhuma notificação</span>
                    </div>
                </div>

                <!-- Usuário -->
                <div class="dropdown">
                    <button class="hm-topbar-btn hm-topbar-user" data-toggle="dropdown">
                        <i class="far fa-user-circle"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <i class="fas fa-angle-down" style="font-size:0.7rem;"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" style="min-width:200px;">
                        <div class="px-3 py-2 border-bottom">
                            <div style="font-weight:700;font-size:0.88rem;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div style="font-size:0.75rem;color:#718096;">{{ Auth::user()->email ?? '' }}</div>
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                            <i class="fas fa-cog"></i> Configurações
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger" style="background:none;border:none;width:100%;text-align:left;cursor:pointer;">
                                <i class="fas fa-sign-out-alt"></i> Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page header -->
        <div id="hm-page-header">
            <div>
                <h1 id="hm-page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}"><i class="fas fa-home"></i></a>
                </li>
                @yield('breadcrumb')
            </ol>
        </div>

        <!-- Content -->
        <main id="hm-content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @yield('content')

        </main>

        <!-- Footer -->
        <footer id="hm-footer">
            <span>Copyright &copy; {{ date('Y') }} <a href="https://homemechanic.com.br">HomeMechanic</a>. Todos os direitos reservados.</span>
            <span>Laravel {{ app()->version() }} | PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</span>
        </footer>

    </div><!-- /#hm-main -->

</div><!-- /#hm-wrapper -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/admin.js') }}"></script>

<script>
// Preloader
$(window).on('load', function() {
    $('#preloader').fadeOut(400);
});

// CSRF
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

// Auto-hide alerts
setTimeout(function() { $('.alert').fadeOut('slow'); }, 5000);

// Toggle sidebar
function toggleSidebar() {
    document.getElementById('hm-wrapper').classList.toggle('sidebar-closed');
    localStorage.setItem('sidebar', document.getElementById('hm-wrapper').classList.contains('sidebar-closed') ? 'closed' : 'open');
}

// Restaurar estado do sidebar
(function() {
    if (localStorage.getItem('sidebar') === 'closed') {
        document.getElementById('hm-wrapper').classList.add('sidebar-closed');
    }
})();

// Accordion do menu
function toggleGroup(btn) {
    const group = btn.closest('.hm-nav-group');
    group.classList.toggle('open');
}

// Confirm delete
$(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    const form = $(this).closest('form');
    Swal.fire({
        title: 'Confirmar exclusão',
        text: 'Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then(r => { if (r.isConfirmed) form.submit(); });
});
</script>

@yield('scripts')
</body>
</html>
