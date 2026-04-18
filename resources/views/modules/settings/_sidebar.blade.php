<div class="col-md-3">
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-list"></i> Menu</span></div>
        <div class="card-body p-2">
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}"
                       class="nav-link {{ in_array($active ?? '', ['general','']) ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Geral
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.frontend') }}"
                       class="nav-link {{ ($active ?? '') === 'frontend' ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Conteúdo do Site
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.seo') }}"
                       class="nav-link {{ ($active ?? '') === 'seo' ? 'active' : '' }}">
                        <i class="fas fa-search"></i> SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.email') }}"
                       class="nav-link {{ ($active ?? '') === 'email' ? 'active' : '' }}">
                        <i class="fas fa-envelope"></i> E-mail (SMTP)
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.email.templates') }}"
                       class="nav-link {{ ($active ?? '') === 'templates' ? 'active' : '' }}">
                        <i class="fas fa-envelope-open-text"></i> Templates de E-mail
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.backup') }}"
                       class="nav-link {{ ($active ?? '') === 'backup' ? 'active' : '' }}">
                        <i class="fas fa-tools"></i> Backup / Manutenção
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.recaptcha') }}"
                       class="nav-link {{ ($active ?? '') === 'recaptcha' ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i> reCAPTCHA / Segurança
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
