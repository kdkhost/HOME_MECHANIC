// HomeMechanic Admin Panel JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Hide preloader after page load
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 1000);
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Sidebar toggle functionality
    const sidebarToggle = document.querySelector('[data-widget="pushmenu"]');
    const body = document.body;
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            body.classList.toggle('sidebar-collapse');
            
            // Save state to localStorage
            if (body.classList.contains('sidebar-collapse')) {
                localStorage.setItem('sidebar-state', 'collapsed');
            } else {
                localStorage.setItem('sidebar-state', 'expanded');
            }
        });
    }

    // Restore sidebar state from localStorage
    const sidebarState = localStorage.getItem('sidebar-state');
    if (sidebarState === 'collapsed') {
        body.classList.add('sidebar-collapse');
    }

    // Form validation enhancement
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Show first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            form.classList.add('was-validated');
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Loading button states
    document.querySelectorAll('.btn-loading').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="loading-spinner me-2"></span>Processando...';
            this.disabled = true;
            
            // Re-enable after form submission or timeout
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 5000);
        });
    });

    // Confirm delete actions
    document.querySelectorAll('.btn-delete, .delete-action').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const itemName = this.getAttribute('data-item-name') || 'este item';
            const form = this.closest('form') || document.querySelector(this.getAttribute('data-form'));
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Tem certeza?',
                    text: `Você está prestes a excluir ${itemName}. Esta ação não pode ser desfeita.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (form) {
                            form.submit();
                        } else if (this.href) {
                            window.location.href = this.href;
                        }
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm(`Tem certeza que deseja excluir ${itemName}?`)) {
                    if (form) {
                        form.submit();
                    } else if (this.href) {
                        window.location.href = this.href;
                    }
                }
            }
        });
    });

    // File upload drag and drop
    const fileUploadAreas = document.querySelectorAll('.file-upload-area');
    fileUploadAreas.forEach(area => {
        const input = area.querySelector('input[type="file"]');
        
        if (input) {
            area.addEventListener('click', () => input.click());
            
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('dragover');
            });
            
            area.addEventListener('dragleave', () => {
                area.classList.remove('dragover');
            });
            
            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    
                    // Trigger change event
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                }
            });
            
            input.addEventListener('change', function() {
                const fileName = this.files[0]?.name;
                if (fileName) {
                    const label = area.querySelector('.file-name');
                    if (label) {
                        label.textContent = fileName;
                    }
                }
            });
        }
    });

    // Data tables enhancement
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        // Add search functionality
        const searchInput = document.querySelector(`[data-table="${table.id}"]`);
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
        
        // Add sorting functionality
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                const column = this.getAttribute('data-sort');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                const isAscending = this.classList.contains('sort-asc');
                
                // Remove sort classes from all headers
                headers.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
                
                // Add appropriate class to current header
                this.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
                
                // Sort rows
                rows.sort((a, b) => {
                    const aVal = a.querySelector(`[data-sort="${column}"]`)?.textContent || '';
                    const bVal = b.querySelector(`[data-sort="${column}"]`)?.textContent || '';
                    
                    if (isAscending) {
                        return bVal.localeCompare(aVal);
                    } else {
                        return aVal.localeCompare(bVal);
                    }
                });
                
                // Reorder DOM
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });

    // Auto-save functionality for forms
    const autoSaveForms = document.querySelectorAll('.auto-save');
    autoSaveForms.forEach(form => {
        let saveTimeout;
        
        form.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            
            // Show saving indicator
            const indicator = form.querySelector('.save-indicator');
            if (indicator) {
                indicator.textContent = 'Salvando...';
                indicator.className = 'save-indicator text-warning';
            }
            
            saveTimeout = setTimeout(() => {
                // Simulate auto-save (implement actual save logic as needed)
                if (indicator) {
                    indicator.textContent = 'Salvo automaticamente';
                    indicator.className = 'save-indicator text-success';
                    
                    setTimeout(() => {
                        indicator.textContent = '';
                    }, 2000);
                }
            }, 2000);
        });
    });

    // CSRF Token setup for AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        // Setup for jQuery if available
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token.getAttribute('content')
                }
            });
        }
        
        // Setup for Axios if available
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
    }

    // Toast notifications helper
    window.showToast = function(message, type = 'success') {
        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: type === 'success' ? "#28a745" : 
                                type === 'error' ? "#dc3545" : 
                                type === 'warning' ? "#ffc107" : "#007bff",
                stopOnFocus: true
            }).showToast();
        } else {
            // Fallback to alert
            alert(message);
        }
    };

    // Quick stats update (for dashboard)
    function updateStats() {
        const statElements = document.querySelectorAll('[data-stat]');
        statElements.forEach(element => {
            const statType = element.getAttribute('data-stat');
            // Implement actual stat fetching logic here
            // This is just a placeholder
        });
    }

    // Update stats every 30 seconds on dashboard
    if (window.location.pathname.includes('dashboard')) {
        setInterval(updateStats, 30000);
    }

    // Initialize rich text editors (if TinyMCE is available)
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '.rich-editor',
            height: 300,
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }'
        });
    }

    console.log('HomeMechanic Admin Panel initialized successfully!');
});

// Global error handler for AJAX requests
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    
    if (typeof showToast === 'function') {
        showToast('Ocorreu um erro inesperado. Tente novamente.', 'error');
    }
});

// Utility functions for admin panel
window.AdminUtils = {
    // Confirm action with SweetAlert or native confirm
    confirmAction: function(title, text, confirmText = 'Confirmar', cancelText = 'Cancelar') {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF6B00',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: cancelText
            });
        } else {
            return Promise.resolve({ isConfirmed: confirm(title + '\n' + text) });
        }
    },
    
    // Show loading state on button
    setButtonLoading: function(button, loading = true) {
        if (loading) {
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<span class="loading-spinner me-2"></span>Carregando...';
            button.disabled = true;
        } else {
            button.innerHTML = button.dataset.originalText || button.innerHTML;
            button.disabled = false;
        }
    },
    
    // Format number with thousands separator
    formatNumber: function(num) {
        return new Intl.NumberFormat('pt-BR').format(num);
    },
    
    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(amount);
    }
};