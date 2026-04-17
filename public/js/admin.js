/**
 * HomeMechanic Admin Panel JavaScript
 */

$(document).ready(function() {
    console.log('HomeMechanic Admin Panel - Loaded');
    
    // Remove preloader after 500ms
    setTimeout(function() {
        $('#preloader').fadeOut('slow');
    }, 500);
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-toggle="popover"]').popover();
    
    // Confirm delete actions
    $('.btn-delete, .delete-btn').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const itemName = $(this).data('name') || 'este item';
        
        Swal.fire({
            title: 'Tem certeza?',
            text: `Deseja realmente excluir ${itemName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert:not(.alert-permanent)').fadeOut('slow');
    }, 5000);
    
    // Handle AJAX form submissions
    $('.ajax-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method') || 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message || 'Operação realizada com sucesso!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.message || 'Ocorreu um erro ao processar a solicitação.'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Ocorreu um erro ao processar a solicitação.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    html: errorMessage
                });
            },
            complete: function() {
                // Re-enable button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Character counter for textareas
    $('textarea[maxlength]').each(function() {
        const textarea = $(this);
        const maxLength = textarea.attr('maxlength');
        const counter = $('<small class="form-text text-muted char-counter"></small>');
        textarea.after(counter);
        
        function updateCounter() {
            const remaining = maxLength - textarea.val().length;
            counter.text(`${remaining} caracteres restantes`);
        }
        
        updateCounter();
        textarea.on('input', updateCounter);
    });
    
    // Image preview for file inputs
    $('input[type="file"][accept*="image"]').on('change', function() {
        const input = this;
        const preview = $(input).data('preview');
        
        if (input.files && input.files[0] && preview) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $(preview).attr('src', e.target.result).show();
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    // Sortable lists (if jQuery UI is loaded)
    if ($.fn.sortable) {
        $('.sortable-list').sortable({
            handle: '.drag-handle',
            update: function(event, ui) {
                const order = $(this).sortable('toArray', { attribute: 'data-id' });
                const url = $(this).data('sort-url');
                
                if (url) {
                    $.post(url, { order: order }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ordem atualizada!',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            }
        });
    }
    
    // Toggle switches
    $('.toggle-switch').on('change', function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const field = checkbox.data('field');
        const value = checkbox.is(':checked') ? 1 : 0;
        
        if (url) {
            $.post(url, { [field]: value }, function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Atualizado!',
                        text: response.message || 'Status atualizado com sucesso!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Revert checkbox
                    checkbox.prop('checked', !checkbox.is(':checked'));
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.message || 'Erro ao atualizar status.'
                    });
                }
            }).fail(function() {
                // Revert checkbox
                checkbox.prop('checked', !checkbox.is(':checked'));
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao atualizar status.'
                });
            });
        }
    });
    
    // Copy to clipboard
    $('.copy-to-clipboard').on('click', function() {
        const text = $(this).data('text') || $(this).text();
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        Swal.fire({
            icon: 'success',
            title: 'Copiado!',
            text: 'Texto copiado para a área de transferência.',
            timer: 1500,
            showConfirmButton: false
        });
    });
});

// Helper function to show toast notifications
function showToast(message, type = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: type,
        title: message
    });
}

// Helper function to confirm action
function confirmAction(message, callback) {
    Swal.fire({
        title: 'Tem certeza?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, confirmar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}
