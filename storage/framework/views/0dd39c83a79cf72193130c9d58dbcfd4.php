

<?php $__env->startSection('title', 'Documentação do Sistema'); ?>
<?php $__env->startSection('page-title', 'Documentação'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item active">Documentação</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.documentation-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.documentation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.documentation-icon {
    font-size: 2rem;
    color: var(--bs-primary);
    margin-bottom: 1rem;
}

.doc-link {
    color: #333;
    text-decoration: none;
    display: block;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
    transition: color 0.2s ease;
}

.doc-link:hover {
    color: var(--bs-primary);
    text-decoration: none;
}

.doc-link:last-child {
    border-bottom: none;
}

.search-box {
    background: linear-gradient(135deg, #FF6B00, #E55A00);
    color: white;
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.search-input {
    border: none;
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

.search-results {
    display: none;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    max-height: 300px;
    overflow-y: auto;
    position: absolute;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.search-result-item {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

.quick-links {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <!-- Caixa de Busca -->
        <div class="search-box text-center">
            <h2 class="mb-3">
                <i class="bi bi-book"></i>
                Documentação do HomeMechanic System
            </h2>
            <p class="mb-4">Encontre rapidamente as informações que você precisa</p>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control search-input" 
                               id="searchInput"
                               placeholder="Buscar na documentação... (ex: instalação, configuração, upload)"
                               autocomplete="off">
                        <div id="searchResults" class="search-results"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links Rápidos -->
        <div class="quick-links">
            <h5 class="mb-3">
                <i class="bi bi-lightning-charge text-warning"></i>
                Acesso Rápido
            </h5>
            <div class="row">
                <div class="col-md-3">
                    <a href="<?php echo e(route('admin.documentation.show', 'instalacao')); ?>" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-download"></i> Instalação
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo e(route('admin.documentation.show', 'manual-usuario')); ?>" class="btn btn-outline-success btn-sm w-100 mb-2">
                        <i class="bi bi-person-check"></i> Manual do Usuário
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo e(route('admin.documentation.show', 'configuracao-smtp')); ?>" class="btn btn-outline-info btn-sm w-100 mb-2">
                        <i class="bi bi-envelope-gear"></i> Config. SMTP
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?php echo e(route('admin.documentation.show', 'faq')); ?>" class="btn btn-outline-warning btn-sm w-100 mb-2">
                        <i class="bi bi-question-circle"></i> FAQ
                    </a>
                </div>
            </div>
        </div>

        <!-- Estrutura da Documentação -->
        <div class="row">
            <?php $__currentLoopData = $structure; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionName => $documents): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card documentation-card h-100">
                    <div class="card-body">
                        <div class="text-center">
                            <?php switch($sectionName):
                                case ('Primeiros Passos'): ?>
                                    <i class="bi bi-rocket-takeoff documentation-icon"></i>
                                    <?php break; ?>
                                <?php case ('Manual do Usuário'): ?>
                                    <i class="bi bi-person-gear documentation-icon"></i>
                                    <?php break; ?>
                                <?php case ('Configurações'): ?>
                                    <i class="bi bi-gear-fill documentation-icon"></i>
                                    <?php break; ?>
                                <?php case ('Personalização'): ?>
                                    <i class="bi bi-palette documentation-icon"></i>
                                    <?php break; ?>
                                <?php case ('Desenvolvimento'): ?>
                                    <i class="bi bi-code-slash documentation-icon"></i>
                                    <?php break; ?>
                                <?php case ('Segurança'): ?>
                                    <i class="bi bi-shield-check documentation-icon"></i>
                                    <?php break; ?>
                                <?php case ('Solução de Problemas'): ?>
                                    <i class="bi bi-tools documentation-icon"></i>
                                    <?php break; ?>
                                <?php default: ?>
                                    <i class="bi bi-file-text documentation-icon"></i>
                            <?php endswitch; ?>
                        </div>
                        
                        <h5 class="card-title text-center mb-3"><?php echo e($sectionName); ?></h5>
                        
                        <div class="doc-links">
                            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('admin.documentation.show', $slug)); ?>" class="doc-link">
                                    <i class="bi bi-arrow-right-circle me-2"></i>
                                    <?php echo e($title); ?>

                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Informações Adicionais -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle"></i>
                            Informações do Sistema
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><strong>Versão:</strong> 1.0.0</li>
                            <li><strong>Laravel:</strong> <?php echo e(app()->version()); ?></li>
                            <li><strong>PHP:</strong> <?php echo e(PHP_VERSION); ?></li>
                            <li><strong>Última Atualização:</strong> <?php echo e(date('d/m/Y')); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-headset"></i>
                            Suporte Técnico
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><i class="bi bi-envelope me-2"></i> contato@kdkhost.com.br</li>
                            <li><i class="bi bi-github me-2"></i> GitHub kdkhost/li>
                            <li><i class="bi bi-chat-dots me-2"></i> Chat Online</li>
                            <li><i class="bi bi-telephone me-2"></i> (21) 98132-5441</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    let searchTimeout;
    const searchInput = $('#searchInput');
    const searchResults = $('#searchResults');
    
    // Busca em tempo real
    searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.hide().empty();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, 300);
    });
    
    // Ocultar resultados ao clicar fora
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            searchResults.hide();
        }
    });
    
    function performSearch(query) {
        $.ajax({
            url: '<?php echo e(route("admin.documentation.search")); ?>',
            method: 'GET',
            data: { q: query },
            success: function(response) {
                displaySearchResults(response.results);
            },
            error: function() {
                searchResults.html('<div class="search-result-item text-danger">Erro na busca. Tente novamente.</div>').show();
            }
        });
    }
    
    function displaySearchResults(results) {
        if (results.length === 0) {
            searchResults.html('<div class="search-result-item text-muted">Nenhum resultado encontrado.</div>').show();
            return;
        }
        
        let html = '';
        results.forEach(function(result) {
            html += `
                <div class="search-result-item" onclick="window.location.href='${result.url}'">
                    <div class="fw-bold">${result.title}</div>
                    <div class="text-muted small">${result.context}</div>
                </div>
            `;
        });
        
        searchResults.html(html).show();
    }
    
    // Atalhos de teclado
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + K para focar na busca
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
        
        // Escape para limpar busca
        if (e.key === 'Escape') {
            searchInput.val('');
            searchResults.hide();
        }
    });
    
    // Animação dos cards
    $('.documentation-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\documentation\index.blade.php ENDPATH**/ ?>