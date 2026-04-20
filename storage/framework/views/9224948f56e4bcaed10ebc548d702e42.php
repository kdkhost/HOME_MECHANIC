

<?php $__env->startSection('title', $title . ' - Documentação'); ?>
<?php $__env->startSection('page-title', $title); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item">
    <a href="<?php echo e(route('admin.documentation.index')); ?>">Documentação</a>
</li>
<li class="breadcrumb-item active"><?php echo e($title); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css">
<style>
.documentation-content {
    font-size: 1.1rem;
    line-height: 1.7;
}

.documentation-content h1,
.documentation-content h2,
.documentation-content h3,
.documentation-content h4,
.documentation-content h5,
.documentation-content h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #333;
}

.documentation-content h1 {
    border-bottom: 3px solid #FF6B00;
    padding-bottom: 0.5rem;
}

.documentation-content h2 {
    border-bottom: 2px solid #eee;
    padding-bottom: 0.3rem;
}

.documentation-content h3 {
    color: #FF6B00;
}

.documentation-content pre {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    padding: 1rem;
    overflow-x: auto;
}

.documentation-content code {
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-size: 0.9em;
}

.documentation-content pre code {
    background: none;
    padding: 0;
}

.documentation-content blockquote {
    border-left: 4px solid #FF6B00;
    background: #fff8f0;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 5px 5px 0;
}

.documentation-content table {
    width: 100%;
    margin: 1rem 0;
    border-collapse: collapse;
}

.documentation-content table th,
.documentation-content table td {
    border: 1px solid #ddd;
    padding: 0.75rem;
    text-align: left;
}

.documentation-content table th {
    background: #f8f9fa;
    font-weight: 600;
}

.documentation-content img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin: 1rem 0;
}

.toc {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    padding: 1rem;
    margin-bottom: 2rem;
}

.toc ul {
    list-style: none;
    padding-left: 0;
}

.toc ul ul {
    padding-left: 1.5rem;
}

.toc a {
    color: #333;
    text-decoration: none;
    display: block;
    padding: 0.25rem 0;
    transition: color 0.2s ease;
}

.toc a:hover {
    color: #FF6B00;
}

.doc-navigation {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin-top: 3rem;
}

.doc-nav-link {
    display: block;
    padding: 1rem;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease;
}

.doc-nav-link:hover {
    background: #FF6B00;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.doc-meta {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 5px;
    padding: 1rem;
    margin-bottom: 2rem;
    font-size: 0.9rem;
}

.floating-toc {
    position: sticky;
    top: 100px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}

.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #FF6B00;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
    cursor: pointer;
    display: none;
    z-index: 1000;
    transition: all 0.3s ease;
}

.back-to-top:hover {
    background: #E55A00;
    transform: translateY(-2px);
}

.print-button {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s ease;
}

.print-button:hover {
    background: #5a6268;
}

@media print {
    .no-print {
        display: none !important;
    }
    
    .documentation-content {
        font-size: 12pt;
        line-height: 1.5;
    }
}

/* Responsividade */
@media (max-width: 768px) {
    .floating-toc {
        position: static;
        margin-bottom: 2rem;
    }
    
    .doc-navigation .row > div {
        margin-bottom: 1rem;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- Índice Lateral (Desktop) -->
    <div class="col-lg-3 d-none d-lg-block no-print">
        <?php if(count($tableOfContents) > 0): ?>
        <div class="floating-toc">
            <div class="toc">
                <h6 class="mb-3">
                    <i class="bi bi-list-ul"></i>
                    Índice
                </h6>
                <ul>
                    <?php $__currentLoopData = $tableOfContents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li style="margin-left: <?php echo e(($item['level'] - 2) * 1); ?>rem;">
                            <a href="#<?php echo e($item['anchor']); ?>"><?php echo e($item['title']); ?></a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Conteúdo Principal -->
    <div class="col-lg-9">
        <!-- Meta Informações -->
        <div class="doc-meta no-print">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Documento:</strong> <?php echo e($title); ?> |
                    <strong>Última atualização:</strong> <?php echo e(date('d/m/Y H:i', $lastModified)); ?>

                </div>
                <div class="col-md-4 text-end">
                    <button onclick="window.print()" class="print-button me-2">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                    <a href="<?php echo e(route('admin.documentation.index')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Índice Mobile -->
        <?php if(count($tableOfContents) > 0): ?>
        <div class="d-lg-none no-print mb-4">
            <div class="toc">
                <h6 class="mb-3">
                    <i class="bi bi-list-ul"></i>
                    Índice
                </h6>
                <ul>
                    <?php $__currentLoopData = $tableOfContents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li style="margin-left: <?php echo e(($item['level'] - 2) * 1); ?>rem;">
                            <a href="#<?php echo e($item['anchor']); ?>"><?php echo e($item['title']); ?></a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Conteúdo do Documento -->
        <div class="card">
            <div class="card-body">
                <div class="documentation-content">
                    <?php echo $content; ?>

                </div>
            </div>
        </div>

        <!-- Navegação Entre Documentos -->
        <?php if($navigation['previous'] || $navigation['next']): ?>
        <div class="doc-navigation no-print">
            <h6 class="mb-3">
                <i class="bi bi-arrow-left-right"></i>
                Navegação
            </h6>
            <div class="row">
                <?php if($navigation['previous']): ?>
                <div class="col-md-6">
                    <a href="<?php echo e(route('admin.documentation.show', $navigation['previous']['slug'])); ?>" class="doc-nav-link">
                        <div class="text-muted small">← Anterior</div>
                        <div class="fw-bold"><?php echo e($navigation['previous']['title']); ?></div>
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if($navigation['next']): ?>
                <div class="col-md-6">
                    <a href="<?php echo e(route('admin.documentation.show', $navigation['next']['slug'])); ?>" class="doc-nav-link text-end">
                        <div class="text-muted small">Próximo →</div>
                        <div class="fw-bold"><?php echo e($navigation['next']['title']); ?></div>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Feedback -->
        <div class="card mt-4 no-print">
            <div class="card-body text-center">
                <h6>Esta documentação foi útil?</h6>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-success" onclick="sendFeedback('positive')">
                        <i class="bi bi-hand-thumbs-up"></i> Sim
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="sendFeedback('negative')">
                        <i class="bi bi-hand-thumbs-down"></i> Não
                    </button>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        Encontrou um erro? 
                        <a href="mailto:suporte@homemechanic.com.br?subject=Erro na Documentação: <?php echo e($title); ?>">
                            Reporte aqui
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botão Voltar ao Topo -->
<button class="back-to-top no-print" onclick="scrollToTop()">
    <i class="bi bi-arrow-up"></i>
</button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
<script>
$(document).ready(function() {
    // Adicionar IDs aos cabeçalhos para navegação
    $('.documentation-content h2, .documentation-content h3, .documentation-content h4, .documentation-content h5, .documentation-content h6').each(function() {
        const text = $(this).text();
        const id = text.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .trim();
        $(this).attr('id', id);
    });
    
    // Smooth scroll para links internos
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // Mostrar/ocultar botão voltar ao topo
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    });
    
    // Destacar seção atual no índice
    $(window).scroll(function() {
        let current = '';
        $('.documentation-content h2, .documentation-content h3, .documentation-content h4').each(function() {
            const sectionTop = $(this).offset().top;
            if (sectionTop <= $(window).scrollTop() + 150) {
                current = $(this).attr('id');
            }
        });
        
        $('.toc a').removeClass('active');
        if (current) {
            $('.toc a[href="#' + current + '"]').addClass('active');
        }
    });
});

function scrollToTop() {
    $('html, body').animate({scrollTop: 0}, 500);
}

function sendFeedback(type) {
    // Aqui você pode implementar o envio de feedback
    const message = type === 'positive' ? 'Obrigado pelo feedback positivo!' : 'Obrigado pelo feedback. Vamos melhorar!';
    
    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        style: {
            background: type === 'positive' ? "#28a745" : "#ffc107",
        }
    }).showToast();
    
    // Desabilitar botões após feedback
    $('.btn-group button').prop('disabled', true);
}

// Atalhos de teclado
$(document).keydown(function(e) {
    // Seta esquerda - documento anterior
    if (e.altKey && e.keyCode === 37) {
        <?php if($navigation['previous']): ?>
        window.location.href = '<?php echo e(route("admin.documentation.show", $navigation["previous"]["slug"])); ?>';
        <?php endif; ?>
    }
    
    // Seta direita - próximo documento
    if (e.altKey && e.keyCode === 39) {
        <?php if($navigation['next']): ?>
        window.location.href = '<?php echo e(route("admin.documentation.show", $navigation["next"]["slug"])); ?>';
        <?php endif; ?>
    }
    
    // Ctrl/Cmd + P - imprimir
    if ((e.ctrlKey || e.metaKey) && e.keyCode === 80) {
        e.preventDefault();
        window.print();
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\documentation\show.blade.php ENDPATH**/ ?>