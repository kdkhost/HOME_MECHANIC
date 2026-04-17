<div class="col-md-3">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-list"></i> Menu</span>
        </div>
        <div class="card-body p-2">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ ($active ?? '') === 'general' ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Geral
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.seo') }}" class="nav-link {{ ($active ?? '') === 'seo' ? 'active' : '' }}">
                        <i class="fas fa-search"></i> SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.email') }}" class="nav-link {{ ($active ?? '') === 'email' ? 'active' : '' }}">
                        <i class="fas fa-envelope"></i> E-mail (SMTP)
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.backup') }}" class="nav-link {{ ($active ?? '') === 'backup' ? 'active' : '' }}">
                        <i class="fas fa-tools"></i> Backup / Manutenção
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
