<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HomeMechanic Admin</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome (compatibilidade) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE 4 -->
    <link rel="stylesheet" href="{{ asset('css/adminlte4.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Custom -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @yield('styles')
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

<!--begin::App Wrapper-->
<div class="app-wrapper">

    <!--begin::Header-->
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="sidebarToggle" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <a href="{{ route('admin.dashboard.index') }}" class="nav-link">
                        <i class="bi bi-house me-1"></i> Dashboard
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <!-- Usuário -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:220px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold" style="font-size:0.88rem;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email ?? '' }}</div>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                                <i class="bi bi-gear me-2"></i> Configurações
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!--end::Header-->

    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">

        <!--begin::Brand-->
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard.index') }}" class="brand-link">
                <i class="fas fa-tools" style="color:var(--hm-primary);font-size:1.3rem;margin-right:0.5rem;"></i>
                <span class="brand-text fw-bold" style="color:var(--hm-primary);">HomeMechanic</span>
            </a>
        </div>
        <!--end::Brand-->

        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                    <!-- User panel -->
                    <li class="nav-item px-3 py-2 mb-1" style="border-bottom:1px solid rgba(255,255,255,0.07);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-circle fs-3" style="color:var(--hm-primary);"></i>
                            <div>
                                <div class="text-white fw-semibold" style="font-size:0.88rem;">{{ Auth::user()->name ?? 'Administrador' }}</div>
                                <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard.index') }}" class="nav-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer2"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">CONTEÚDO</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>Serviços</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.gallery.index') }}" class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-images"></i>
                            <p>Galeria</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.blog.index') }}" class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-newspaper"></i>
                            <p>Blog</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.contact.index') }}" class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-envelope"></i>
                            <p>Mensagens</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.upload.index') }}" class="nav-link {{ request()->routeIs('admin.upload.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-cloud-upload"></i>
                            <p>Upload de Arquivos</p>
                        </a>
                    </li>

                    <li class="nav-header">MARKETING</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.seo.index') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-search"></i>
                            <p>SEO</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-graph-up"></i>
                            <p>Analytics</p>
                        </a>
                    </li>

                    <li class="nav-header">SISTEMA</li>

                    <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-gear"></i>
                            <p>Configurações <i class="nav-arrow bi bi-chevron-right ms-auto"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i><p>Geral</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.seo') }}" class="nav-link {{ request()->routeIs('admin.settings.seo') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i><p>SEO</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.email') }}" class="nav-link {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i><p>E-mail (SMTP)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.backup') }}" class="nav-link {{ request()->routeIs('admin.settings.backup') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i><p>Backup / Manutenção</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Usuários</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.documentation.index') }}" class="nav-link {{ request()->routeIs('admin.documentation.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-book"></i>
                            <p>Documentação</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
        <!--end::Sidebar Wrapper-->
    </aside>
    <!--end::Sidebar-->

    <!--begin::App Main-->
    <main class="app-main">

        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">@yield('page-title', 'Dashboard')</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="bi bi-house"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                            </li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content Header-->

        <!--begin::App Content-->
        <div class="app-content">
            <div class="container-fluid">

                @if(session('success'))
                    {{-- toast disparado via JS --}}
                @endif
                @if(session('error'))
                    {{-- toast disparado via JS --}}
                @endif
                @if(session('warning'))
                    {{-- toast disparado via JS --}}
                @endif
                @if(session('info'))
                    {{-- toast disparado via JS --}}
                @endif

                @yield('content')

            </div>
        </div>
        <!--end::App Content-->

    </main>
    <!--end::App Main-->

    <!--begin::Footer-->
    <footer class="app-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="https://homemechanic.com.br">HomeMechanic</a>.</strong>
        Todos os direitos reservados.
        <div class="float-end d-none d-sm-inline-block">
            Laravel {{ app()->version() }} | PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}
        </div>
    </footer>
    <!--end::Footer-->

</div>
<!--end::App Wrapper-->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/adminlte4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>

<script>
    // ── Toastify helper global ────────────────────────────────
    function toast(message, type = 'success') {
        const colors = {
            success: 'linear-gradient(135deg, #28a745, #20c997)',
            error:   'linear-gradient(135deg, #dc3545, #c82333)',
            warning: 'linear-gradient(135deg, #ffc107, #e0a800)',
            info:    'linear-gradient(135deg, #17a2b8, #138496)',
        };
        const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
        Toastify({
            text: (icons[type] || '') + '  ' + message,
            duration: 4000,
            gravity: 'top',
            position: 'right',
            stopOnFocus: true,
            style: {
                background: colors[type] || colors.success,
                borderRadius: '8px',
                padding: '0.85rem 1.25rem',
                fontSize: '0.88rem',
                fontWeight: '500',
                boxShadow: '0 4px 16px rgba(0,0,0,0.2)',
                minWidth: '280px',
            },
            onClick: function() {}
        }).showToast();
    }

    // ── Flash messages → Toastify ─────────────────────────────
    @if(session('success'))
        toast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        toast(@json(session('error')), 'error');
    @endif
    @if(session('warning'))
        toast(@json(session('warning')), 'warning');
    @endif
    @if(session('info'))
        toast(@json(session('info')), 'info');
    @endif

<script>
    // OverlayScrollbars (requerido pelo AdminLTE 4)
    if (typeof OverlayScrollbarsGlobal !== 'undefined') {
        const { OverlayScrollbars } = OverlayScrollbarsGlobal;
        document.querySelectorAll('.sidebar-wrapper, body').forEach(el => {
            OverlayScrollbars(el, { scrollbars: { autoHide: 'leave' } });
        });
    }

    // CSRF para AJAX
    if (typeof $ !== 'undefined') {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
    }

    // ── Sidebar toggle ──────────────────────────────────────
    document.getElementById('sidebarToggle').addEventListener('click', function(e) {
        e.preventDefault();
        const body = document.body;
        if (window.innerWidth >= 992) {
            body.classList.toggle('sidebar-collapse');
            localStorage.setItem('hm_sidebar', body.classList.contains('sidebar-collapse') ? 'closed' : 'open');
        } else {
            body.classList.toggle('sidebar-open');
        }
    });

    // Estado padrão: ABERTO. Só fecha se o usuário explicitamente fechou.
    (function() {
        // Limpar chave antiga que pode ter ficado como "fechado"
        localStorage.removeItem('sidebarCollapsed');
        // Só colapsa se o usuário salvou explicitamente como fechado
        if (window.innerWidth >= 992 && localStorage.getItem('hm_sidebar') === 'closed') {
            document.body.classList.add('sidebar-collapse');
        }
    })();

    // Auto-hide alerts
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 5000);

    // Confirm delete
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete')) {
            e.preventDefault();
            const form = e.target.closest('form');
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
        }
    });
</script>

@yield('scripts')
</body>
</html>
