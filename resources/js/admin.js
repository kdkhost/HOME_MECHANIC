// Import jQuery and Bootstrap first
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Import AdminLTE 4 JavaScript
import 'admin-lte/dist/js/adminlte.min.js';

// Import required dependencies
import 'toastify-js';
import Swal from 'sweetalert2';
import { Dropzone } from 'dropzone';

// Make libraries globally available
window.Swal = Swal;
window.Dropzone = Dropzone;

// Configure CSRF token for AJAX requests
document.addEventListener('DOMContentLoaded', function() {
    // Set up CSRF token for all AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        
        // For jQuery if available
        if (window.$) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token.getAttribute('content')
                }
            });
        }
    }

    // Hide preloader when page is loaded
    const preloader = document.getElementById('preloader');
    if (preloader) {
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 500);
    }
});

// Configure Dropzone defaults
if (window.Dropzone) {
    Dropzone.autoDiscover = false;
    
    // Default Dropzone configuration
    Dropzone.prototype.defaultOptions = {
        ...Dropzone.prototype.defaultOptions,
        dictDefaultMessage: "Arraste arquivos aqui ou clique para selecionar",
        dictFallbackMessage: "Seu navegador não suporta drag and drop de arquivos.",
        dictFileTooBig: "Arquivo muito grande ({{filesize}}MB). Tamanho máximo: {{maxFilesize}}MB.",
        dictInvalidFileType: "Tipo de arquivo não permitido.",
        dictResponseError: "Servidor respondeu com código {{statusCode}}.",
        dictCancelUpload: "Cancelar upload",
        dictUploadCanceled: "Upload cancelado.",
        dictCancelUploadConfirmation: "Tem certeza que deseja cancelar este upload?",
        dictRemoveFile: "Remover arquivo",
        dictMaxFilesExceeded: "Você não pode enviar mais arquivos.",
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    };
}

// Utility functions for admin panel
window.AdminUtils = {
    // Show success toast
    showSuccess: function(message) {
        if (window.Toastify) {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(135deg, #28a745, #20c997)"
                }
            }).showToast();
        }
    },

    // Show error toast
    showError: function(message) {
        if (window.Toastify) {
            Toastify({
                text: message,
                duration: 5000,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(135deg, #dc3545, #e74c3c)"
                }
            }).showToast();
        }
    },

    // Show warning toast
    showWarning: function(message) {
        if (window.Toastify) {
            Toastify({
                text: message,
                duration: 4000,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(135deg, #ffc107, #fd7e14)"
                }
            }).showToast();
        }
    },

    // Confirm deletion with SweetAlert2
    confirmDelete: function(title = 'Tem certeza?', text = 'Esta ação não pode ser desfeita!') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        });
    },

    // Show loading state
    showLoading: function(element) {
        if (element) {
            element.disabled = true;
            const originalText = element.innerHTML;
            element.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Carregando...';
            element.dataset.originalText = originalText;
        }
    },

    // Hide loading state
    hideLoading: function(element) {
        if (element && element.dataset.originalText) {
            element.disabled = false;
            element.innerHTML = element.dataset.originalText;
            delete element.dataset.originalText;
        }
    }
};

// Add CSS for spinner animation
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);