<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HomeMechanic Admin</title>

    <!-- AdminLTE 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Custom -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @yield('styles')
</head>
<body class="layout-fixed sidebar-expand-lg">

<div class="app-wrapper">

    <!-- ── Navbar ─────────────────────────────────────────── -->
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <a href="{{ route('admin.dashboard.index') }}" class="nav-link">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <!-- Usuário -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="far fa-user-circle me-1"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">
                            <strong>{{ Auth::user()->name ?? 'Admin' }}</strong><br>
                            <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                                <i class="fas fa-cog me-2"></i> Configurações
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ── Sidebar ────────────────────────────────────────── -->
    <aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">

        <!-- Brand -->
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard.index') }}" class="brand-link">
                <i class="fas fa-tools brand-image" style="color:var(--hm-primary);font-size:1.4rem;margin-right:0.5rem;"></i>
                <span class="brand-text fw-bold" style="color:var(--hm-primary);">HomeMechanic</span>
            </a>
        </div>

        <!-- Sidebar content -->
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                    <!-- User panel -->
                    <li class="nav-item px-3 py-2 mb-1" style="border-bottom:1px solid rgba(255,255,255,0.07);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-user-circle fa-2x" style="color:var(--hm-primary);"></i>
                            <div>
                                <div class="text-white fw-semibold" style="font-size:0.88rem;">{{ Auth::user()->name ?? 'Administrador' }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email ?? '' }}</div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard.index') }}" class="nav-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
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
                            <i class="nav-icon fas fa-images"></i>
                            <p>Galeria</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.blog.index') }}" class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-newspaper"></i>
                            <p>Blog</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.contact.index') }}" class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Mensagens</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.upload.index') }}" class="nav-link {{ request()->routeIs('admin.upload.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cloud-upload-alt"></i>
                            <p>Upload de Arquivos</p>
                        </a>
                    </li>

                    <li class="nav-header">MARKETING</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.seo.index') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-search"></i>
                            <p>SEO</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Analytics</p>
                        </a>
                    </li>

                    <li class="nav-header">SISTEMA</li>

                    <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Configurações <i class="nav-arrow fas fa-angle-right ms-auto"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                                    <i class="nav-icon far fa-circle"></i><p>Geral</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.seo') }}" class="nav-link {{ request()->routeIs('admin.settings.seo') ? 'active' : '' }}">
                                    <i class="nav-icon far fa-circle"></i><p>SEO</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.email') }}" class="nav-link {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                                    <i class="nav-icon far fa-circle"></i><p>E-mail (SMTP)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.backup') }}" class="nav-link {{ request()->routeIs('admin.settings.backup') ? 'active' : '' }}">
                                    <i class="nav-icon far fa-circle"></i><p>Backup / Manutenção</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuários</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.documentation.index') }}" class="nav-link {{ request()->routeIs('admin.documentation.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Documentação</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- ── Content ────────────────────────────────────────── -->
    <main class="app-main">

        <!-- Page header -->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">@yield('page-title', 'Dashboard')</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="fas fa-home"></i></a>
                            </li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="app-content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="app-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="https://homemechanic.com.br">HomeMechanic</a>.</strong>
        Todos os direitos reservados.
        <div class="float-end d-none d-sm-inline-block">
            Laravel {{ app()->version() }} | PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}
        </div>
    </footer>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>

<script>
    // CSRF
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Auto-hide alerts
    setTimeout(function() { $('.alert').fadeOut('slow'); }, 5000);

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
