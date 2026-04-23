<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HomeMechanic Admin</title>

    @php
        $favicon = \App\Models\Setting::get('site_favicon', '');
        if ($favicon && !str_starts_with($favicon, 'http') && !str_contains($favicon, '/')) {
            $upload = \App\Modules\Upload\Models\Upload::where('uuid', $favicon)->first();
            $favicon = $upload?->path ?? '';
        }
    @endphp
    @if($favicon)
    <link rel="icon" href="{{ str_starts_with($favicon, 'http') ? $favicon : asset(ltrim($favicon, '/')) }}" type="image/x-icon">
    @endif

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

    <!-- FilePond -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
    <style>
        .filepond--root { font-family: inherit; }
        .filepond--panel-root { background-color: #f8f9fa; border: 2px dashed #dee2e6; }
        .filepond--item-panel { background-color: var(--hm-primary); }
        
        /* Navbar fixo ao rolar */
        .app-header {
            position: sticky;
            top: 0;
            z-index: 1040;
            transition: all 0.3s ease;
        }
        .app-header.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
    </style>

    @yield('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

<!--begin::App Wrapper-->
<div class="app-wrapper">

    <!--begin::Header-->
    <nav class="app-header navbar navbar-expand bg-body" id="mainNavbar">
        <div class="container-fluid">

            <!-- ── Esquerda ──────────────────────────────── -->
            <ul class="navbar-nav">
                <!-- Hambúrguer — usa o toggle nativo do AdminLTE 4 -->
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <a href="{{ route('admin.dashboard.index') }}" class="nav-link">
                        <i class="bi bi-house-fill me-1"></i> Dashboard
                    </a>
                </li>
            </ul>

            <!-- ── Direita ────────────────────────────────── -->
            <ul class="navbar-nav ms-auto">

                <!-- Busca rápida -->
                <li class="nav-item d-none d-md-flex align-items-center">
                    <div class="input-group input-group-sm" style="width:220px;" id="searchWrap">
                        <input type="text" id="navSearch" class="form-control"
                               placeholder="Buscar no painel..." autocomplete="off"
                               style="border-radius:20px 0 0 20px; font-size:0.82rem;">
                        <span class="input-group-text" style="border-radius:0 20px 20px 0; background:var(--hm-primary); border-color:var(--hm-primary); color:#fff;">
                            <i class="bi bi-search"></i>
                        </span>
                        <div class="navbar-search-results" id="searchResults"></div>
                    </div>
                </li>

                <!-- Dark mode -->
                <li class="nav-item">
                    <a class="nav-link" href="#" id="darkModeToggle" title="Alternar modo escuro" role="button">
                        <i class="bi bi-moon-fill" id="darkIcon"></i>
                    </a>
                </li>

                <!-- Notificações -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" title="Notificações">
                        <i class="bi bi-bell-fill"></i>
                        <span class="navbar-badge badge text-bg-danger" id="notifBadge" style="display:none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end" style="width:340px; padding:0; border-radius:12px; overflow:hidden;">
                        <div class="d-flex align-items-center justify-content-between px-3 py-2" style="background:#f8fafc; border-bottom:1px solid var(--hm-border);">
                            <span style="font-weight:700; font-size:0.85rem;">Notificações</span>
                            <button class="btn btn-link btn-sm p-0 text-decoration-none" id="clearNotifs"
                                    style="font-size:0.75rem; color:var(--hm-primary);">Limpar tudo</button>
                        </div>
                        <div id="notifList" style="max-height:320px; overflow-y:auto;">
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-bell-slash d-block mb-2" style="font-size:1.8rem; opacity:0.4;"></i>
                                <small>Nenhuma notificação</small>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Atalhos -->
                <li class="nav-item dropdown d-none d-md-block">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" title="Atalhos rápidos">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="width:260px; border-radius:12px; overflow:hidden;">
                        <div class="px-3 py-2" style="background:#f8fafc; border-bottom:1px solid var(--hm-border);">
                            <span style="font-weight:700; font-size:0.85rem;">Atalhos Rápidos</span>
                        </div>
                        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:4px; padding:0.5rem;">
                            <a href="{{ route('admin.services.index') }}" class="shortcut-item">
                                <i class="fas fa-tools"></i><span>Serviços</span>
                            </a>
                            <a href="{{ route('admin.gallery.index') }}" class="shortcut-item">
                                <i class="fas fa-images"></i><span>Galeria</span>
                            </a>
                            <a href="{{ route('admin.blog.index') }}" class="shortcut-item">
                                <i class="fas fa-newspaper"></i><span>Blog</span>
                            </a>
                            <a href="{{ route('admin.contact.index') }}" class="shortcut-item">
                                <i class="fas fa-envelope"></i><span>Mensagens</span>
                            </a>
                            <a href="{{ route('admin.seo.index') }}" class="shortcut-item">
                                <i class="fas fa-search"></i><span>SEO</span>
                            </a>
                            <a href="{{ route('admin.analytics.index') }}" class="shortcut-item">
                                <i class="fas fa-chart-line"></i><span>Analytics</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="shortcut-item">
                                <i class="fas fa-users"></i><span>Usuários</span>
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="shortcut-item">
                                <i class="fas fa-cog"></i><span>Config.</span>
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Limpar Cache -->
                <li class="nav-item">
                    <a class="nav-link" href="#" id="btnClearCache" title="Limpar todos os caches do Laravel">
                        <i class="fas fa-broom" id="cacheIcon"></i>
                    </a>
                </li>

                <!-- Usuário -->
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="nav-avatar">
                            @if(Auth::user()->avatar)
                                <img src="{{ '/' . ltrim(Auth::user()->avatar, '/') }}"
                                     style="width:32px;height:32px;border-radius:8px;object-fit:cover;"
                                     onerror="this.style.display='none'"
                                     alt="">
                            @else
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            @endif
                        </div>
                        <div class="d-none d-md-block text-start lh-sm">
                            <div style="font-size:0.84rem; font-weight:600; color:var(--hm-text);">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div style="font-size:0.72rem; color:var(--hm-text-muted);">{{ Auth::user()->role === 'admin' ? 'Administrador' : 'Usuário' }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:220px; border-radius:12px; overflow:hidden; padding:0;">
                        <li class="px-3 py-2" style="border-bottom:1px solid var(--hm-border); background:#f8fafc;">
                            <div style="font-weight:700; font-size:0.88rem;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div style="font-size:0.75rem; color:var(--hm-text-muted);">{{ Auth::user()->email ?? '' }}</div>
                        </li>
                        <li>
                            <a href="{{ Auth::id() ? route('admin.users.edit', Auth::id()) : '#' }}" class="dropdown-item">
                                <i class="fas fa-user-edit me-2"></i> Meu Perfil
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                                <i class="fas fa-cog me-2"></i> Configurações
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}" class="dropdown-item" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i> Ver Site
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
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
    <!--end::Header-->

    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">

        <!--begin::Brand-->
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard.index') }}" class="brand-link">
                @php
                    $adminLogo = \App\Models\Setting::get('site_logo', '');
                    if ($adminLogo && !str_starts_with($adminLogo, 'http') && !str_contains($adminLogo, '/')) {
                        $uploadLogo = \App\Modules\Upload\Models\Upload::where('uuid', $adminLogo)->first();
                        $adminLogo = $uploadLogo?->path ?? '';
                    }
                @endphp
                @if($adminLogo)
                    <img src="{{ str_starts_with($adminLogo, 'http') ? $adminLogo : asset(ltrim($adminLogo, '/')) }}" alt="HomeMechanic" style="height:40px;width:auto;object-fit:contain;">
                @else
                    <i class="fas fa-tools" style="color:var(--hm-primary);font-size:1.3rem;margin-right:0.5rem;"></i>
                    <span class="brand-text fw-bold" style="color:var(--hm-primary);">HomeMechanic</span>
                @endif
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
                            @if(Auth::user()->avatar)
                                <img src="{{ '/' . ltrim(Auth::user()->avatar, '/') }}"
                                     style="width:36px;height:36px;border-radius:10px;object-fit:cover;flex-shrink:0;"
                                     onerror="this.outerHTML='<i class=\'bi bi-person-circle fs-3\' style=\'color:var(--hm-primary);\'></i>'"
                                     alt="">
                            @else
                                <i class="bi bi-person-circle fs-3" style="color:var(--hm-primary);"></i>
                            @endif
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
                        <a href="{{ route('admin.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-star"></i>
                            <p>Depoimentos</p>
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

                    @if(auth()->user()->permission_level >= 50)
                    <li class="nav-item">
                        <a href="{{ route('admin.upload.index') }}" class="nav-link {{ request()->routeIs('admin.upload.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-cloud-upload"></i>
                            <p>Upload de Arquivos</p>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->permission_level >= 50)
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

                    <li class="nav-item">
                        <a href="{{ route('admin.sponsors.index') }}" class="nav-link {{ request()->routeIs('admin.sponsors.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>Patrocinadores</p>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->permission_level >= 50)
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
                        <a href="{{ route('admin.settings.frontend') }}" class="nav-link {{ request()->routeIs('admin.settings.frontend') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i><p>Conteúdo do Site</p>
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
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.recaptcha') }}" class="nav-link {{ request()->routeIs('admin.settings.recaptcha') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i><p>reCAPTCHA / Segurança</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Usuários</p>
                        </a>
                    </li>

                    @if(auth()->user()->permission_level >= 50)
                    <li class="nav-item">
                        <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-shield-check"></i>
                            <p>Permissões</p>
                        </a>
                    </li>
                    @endif

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
          <a href="https://kdkhost.com.br">KdkHost</a> - Laravel {{ app()->version() }} | PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}
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
<script src="{{ asset('js/hm-masks.js') }}"></script>

<!-- FilePond -->
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script>
    // Configurações globais FilePond
    if (typeof FilePond !== 'undefined') {
        FilePond.registerPlugin(
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize,
            FilePondPluginImagePreview
        );
        
        FilePond.setOptions({
            labelIdle: 'Arraste arquivos ou <span class="filepond--label-action">Procure</span>',
            labelFileProcessing: 'Enviando',
            labelFileProcessingComplete: 'Upload concluído',
            labelTapToCancel: 'clique para cancelar',
            labelTapToRetry: 'clique para tentar novamente',
            labelTapToUndo: 'clique para desfazer',
            credits: false,
            server: {
                process: {
                    url: '{{ route("admin.upload.store") }}',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    onload: (response) => {
                        const res = JSON.parse(response);
                        return res.success ? res.data.uuid : null;
                    }
                },
                load: '{{ route("admin.upload.load") }}?load=',
                revert: (uniqueFileId, load, error) => {
                    $.ajax({
                        url: '{{ route("admin.upload.destroy", "") }}/' + uniqueFileId,
                        method: 'DELETE',
                        success: () => load(),
                        error: () => error('Erro ao excluir')
                    });
                }
            }
        });
    }
</script>

<script>
    // ── Flash messages → HMToast ─────────────────────────────
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => HMToast.success(@json(session('success'))));
    @endif
    @if(session('error'))
        document.addEventListener('DOMContentLoaded', () => HMToast.error(@json(session('error'))));
    @endif
    @if(session('warning'))
        document.addEventListener('DOMContentLoaded', () => HMToast.warning(@json(session('warning'))));
    @endif
    @if(session('info'))
        document.addEventListener('DOMContentLoaded', () => HMToast.info(@json(session('info'))));
    @endif

    // ── OverlayScrollbars ─────────────────────────────────────
    if (typeof OverlayScrollbarsGlobal !== 'undefined') {
        const { OverlayScrollbars } = OverlayScrollbarsGlobal;
        document.querySelectorAll('.sidebar-wrapper').forEach(el => {
            OverlayScrollbars(el, { scrollbars: { autoHide: 'leave' } });
        });
    }

    // ── CSRF para AJAX ────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        }
    });

    // ── Auto-hide alerts ──────────────────────────────────────
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 5000);

    // ── Limpar Cache ──────────────────────────────────────────
    const CLEAR_CACHE_URL = '{{ route("admin.system.clear-cache") }}';

    function runClearCache(type) {
        type = type || 'all';
        const icon = document.getElementById('cacheIcon');
        const btn  = document.getElementById('btnClearCache');
        if (icon) icon.className = 'fas fa-spinner fa-spin';
        if (btn)  btn.style.pointerEvents = 'none';

        // Aguardar jQuery estar disponível
        var doRequest = function() {
            $.ajax({
                url:         CLEAR_CACHE_URL,
                method:      'POST',
                data:        JSON.stringify({ type: type }),
                contentType: 'application/json',
                headers:     { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data) {
                    if (icon) icon.className = 'fas fa-broom';
                    if (btn)  btn.style.pointerEvents = '';
                    var details = (data.details || []).join('<br>');
                    Swal.fire({
                        title: data.success ? '✅ Cache limpo!' : '⚠️ Concluído com erros',
                        html:  details || data.message,
                        icon:  data.success ? 'success' : 'warning',
                        confirmButtonColor: '#FF6B00',
                    });
                    if (data.success) HMToast.success(data.message);
                    else HMToast.warning(data.message);
                },
                error: function(xhr) {
                    if (icon) icon.className = 'fas fa-broom';
                    if (btn)  btn.style.pointerEvents = '';
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao limpar cache.';
                    HMToast.error(msg);
                }
            });
        };

        if (typeof $ !== 'undefined') {
            doRequest();
        } else {
            // jQuery ainda não carregou — aguardar
            var t = setInterval(function() {
                if (typeof $ !== 'undefined') { clearInterval(t); doRequest(); }
            }, 100);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('btnClearCache');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Limpar todos os caches?',
                    html:  '<div style="font-size:0.88rem;color:#64748b;margin-top:0.5rem;">Serão limpos:<br>✅ Cache de aplicação<br>✅ Views compiladas<br>✅ Configurações<br>✅ Rotas</div>',
                    icon:  'question',
                    showCancelButton:   true,
                    confirmButtonColor: '#FF6B00',
                    cancelButtonColor:  '#64748b',
                    confirmButtonText:  '<i class="fas fa-broom"></i> Limpar tudo',
                    cancelButtonText:   'Cancelar',
                }).then(function(r) { if (r.isConfirmed) runClearCache('all'); });
            });
        }
    });

    // Expor globalmente para a página de backup
    window.clearCacheType = function(type) {
        var labels = { all:'Limpar TODOS os caches', config:'Configuração', view:'Views', route:'Rotas', app:'App Cache' };
        Swal.fire({
            title: labels[type] || 'Limpar cache?',
            icon:  'question',
            showCancelButton:   true,
            confirmButtonColor: '#FF6B00',
            cancelButtonColor:  '#64748b',
            confirmButtonText:  '<i class="fas fa-broom"></i> Limpar',
            cancelButtonText:   'Cancelar',
        }).then(function(r) { if (r.isConfirmed) runClearCache(type); });
    };

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

<script>
// Navbar sticky effect ao rolar
(function() {
    const navbar = document.getElementById('mainNavbar');
    if (!navbar) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
})();
</script>

</body>
</html>
