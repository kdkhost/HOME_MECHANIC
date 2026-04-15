/**
 * HomeMechanic Upload System
 * Sistema de upload com drag & drop, progresso e validação
 */

class HomeMechanicUploader {
    constructor(options = {}) {
        this.options = {
            url: '/admin/upload',
            maxFilesize: 100, // MB
            maxFiles: 10,
            parallelUploads: 3,
            acceptedFiles: 'image/*,video/*,.pdf',
            addRemoveLinks: true,
            dictDefaultMessage: 'Arraste arquivos aqui ou clique para selecionar',
            dictFallbackMessage: 'Seu navegador não suporta drag & drop.',
            dictFileTooBig: 'Arquivo muito grande ({{filesize}}MB). Máximo: {{maxFilesize}}MB.',
            dictInvalidFileType: 'Tipo de arquivo não permitido.',
            dictResponseError: 'Erro no servidor ({{statusCode}})',
            dictCancelUpload: 'Cancelar upload',
            dictRemoveFile: 'Remover arquivo',
            dictMaxFilesExceeded: 'Máximo de {{maxFiles}} arquivos permitido.',
            ...options
        };

        this.dropzone = null;
        this.config = null;
        this.uploadedFiles = [];
        this.callbacks = {
            onSuccess: null,
            onError: null,
            onProgress: null,
            onComplete: null
        };

        this.init();
    }

    /**
     * Inicializar uploader
     */
    async init() {
        try {
            // Carregar configuração do servidor
            await this.loadConfig();
            
            // Configurar Dropzone
            this.setupDropzone();
            
            // Configurar eventos
            this.setupEvents();
            
            console.log('HomeMechanic Uploader inicializado com sucesso');
        } catch (error) {
            console.error('Erro ao inicializar uploader:', error);
            this.showError('Erro ao inicializar sistema de upload');
        }
    }

    /**
     * Carregar configuração do servidor
     */
    async loadConfig() {
        try {
            const response = await fetch('/admin/upload/config', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Erro ao carregar configuração');
            }

            const data = await response.json();
            this.config = data.config;
            
            // Atualizar opções com configuração do servidor
            this.updateOptionsFromConfig();
            
        } catch (error) {
            console.error('Erro ao carregar configuração:', error);
            // Usar configuração padrão se falhar
        }
    }

    /**
     * Atualizar opções com configuração do servidor
     */
    updateOptionsFromConfig() {
        if (!this.config) return;

        // Atualizar tipos aceitos
        const allowedMimes = this.config.allowed_types
            .map(type => type.mime_type)
            .join(',');
        
        this.options.acceptedFiles = allowedMimes;
        this.options.maxFiles = this.config.max_files || 10;
        this.options.parallelUploads = this.config.parallel_uploads || 3;
        
        // Calcular tamanho máximo baseado nos tipos permitidos
        const maxSize = Math.max(
            ...this.config.allowed_types.map(type => type.max_size_mb)
        );
        this.options.maxFilesize = maxSize;
    }

    /**
     * Configurar Dropzone
     */
    setupDropzone() {
        const element = document.getElementById('upload-dropzone');
        if (!element) {
            console.error('Elemento #upload-dropzone não encontrado');
            return;
        }

        // Configuração do Dropzone
        const dropzoneConfig = {
            ...this.options,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            init: () => {
                this.dropzone = Dropzone.instances[Dropzone.instances.length - 1];
                this.setupDropzoneEvents();
            }
        };

        // Inicializar Dropzone
        new Dropzone(element, dropzoneConfig);
    }

    /**
     * Configurar eventos do Dropzone
     */
    setupDropzoneEvents() {
        if (!this.dropzone) return;

        // Arquivo adicionado
        this.dropzone.on('addedfile', (file) => {
            this.onFileAdded(file);
        });

        // Progresso do upload
        this.dropzone.on('uploadprogress', (file, progress, bytesSent) => {
            this.onUploadProgress(file, progress, bytesSent);
        });

        // Upload bem-sucedido
        this.dropzone.on('success', (file, response) => {
            this.onUploadSuccess(file, response);
        });

        // Erro no upload
        this.dropzone.on('error', (file, errorMessage, xhr) => {
            this.onUploadError(file, errorMessage, xhr);
        });

        // Upload completo
        this.dropzone.on('complete', (file) => {
            this.onUploadComplete(file);
        });

        // Arquivo removido
        this.dropzone.on('removedfile', (file) => {
            this.onFileRemoved(file);
        });

        // Todos os uploads completos
        this.dropzone.on('queuecomplete', () => {
            this.onQueueComplete();
        });
    }

    /**
     * Configurar eventos personalizados
     */
    setupEvents() {
        // Eventos de callback
        document.addEventListener('uploader:success', (e) => {
            if (this.callbacks.onSuccess) {
                this.callbacks.onSuccess(e.detail);
            }
        });

        document.addEventListener('uploader:error', (e) => {
            if (this.callbacks.onError) {
                this.callbacks.onError(e.detail);
            }
        });

        document.addEventListener('uploader:progress', (e) => {
            if (this.callbacks.onProgress) {
                this.callbacks.onProgress(e.detail);
            }
        });

        document.addEventListener('uploader:complete', (e) => {
            if (this.callbacks.onComplete) {
                this.callbacks.onComplete(e.detail);
            }
        });
    }

    /**
     * Arquivo adicionado
     */
    onFileAdded(file) {
        console.log('Arquivo adicionado:', file.name);
        
        // Adicionar elementos de progresso personalizados
        this.addProgressElements(file);
        
        // Validação adicional
        if (!this.validateFile(file)) {
            this.dropzone.removeFile(file);
            return;
        }
    }

    /**
     * Progresso do upload
     */
    onUploadProgress(file, progress, bytesSent) {
        // Atualizar barra de progresso
        this.updateProgressBar(file, progress);
        
        // Calcular tempo restante
        const timeRemaining = this.calculateTimeRemaining(file, progress, bytesSent);
        this.updateTimeRemaining(file, timeRemaining);
        
        // Disparar evento personalizado
        this.dispatchEvent('uploader:progress', {
            file: file,
            progress: progress,
            bytesSent: bytesSent,
            timeRemaining: timeRemaining
        });
    }

    /**
     * Upload bem-sucedido
     */
    onUploadSuccess(file, response) {
        console.log('Upload bem-sucedido:', file.name, response);
        
        if (response.success) {
            // Armazenar dados do arquivo
            file.uploadData = response.data;
            this.uploadedFiles.push(response.data);
            
            // Mostrar miniatura se for imagem
            if (response.data.is_image && response.data.thumbnail_url) {
                this.showThumbnail(file, response.data.thumbnail_url);
            }
            
            // Notificação de sucesso
            this.showSuccess(`${file.name} enviado com sucesso!`);
            
            // Disparar evento personalizado
            this.dispatchEvent('uploader:success', {
                file: file,
                data: response.data
            });
        } else {
            this.onUploadError(file, response.message || 'Erro desconhecido');
        }
    }

    /**
     * Erro no upload
     */
    onUploadError(file, errorMessage, xhr) {
        console.error('Erro no upload:', file.name, errorMessage);
        
        // Mostrar erro
        this.showError(`Erro ao enviar ${file.name}: ${errorMessage}`);
        
        // Disparar evento personalizado
        this.dispatchEvent('uploader:error', {
            file: file,
            error: errorMessage,
            xhr: xhr
        });
    }

    /**
     * Upload completo
     */
    onUploadComplete(file) {
        // Remover elementos de progresso após delay
        setTimeout(() => {
            this.removeProgressElements(file);
        }, 2000);
    }

    /**
     * Arquivo removido
     */
    onFileRemoved(file) {
        console.log('Arquivo removido:', file.name);
        
        // Remover dos arquivos enviados
        if (file.uploadData) {
            this.uploadedFiles = this.uploadedFiles.filter(
                uploaded => uploaded.uuid !== file.uploadData.uuid
            );
        }
    }

    /**
     * Todos os uploads completos
     */
    onQueueComplete() {
        console.log('Todos os uploads completos');
        
        // Disparar evento personalizado
        this.dispatchEvent('uploader:complete', {
            uploadedFiles: this.uploadedFiles
        });
    }

    /**
     * Validar arquivo
     */
    validateFile(file) {
        if (!this.config) return true;
        
        // Verificar tipo MIME
        const allowedType = this.config.allowed_types.find(
            type => type.mime_type === file.type
        );
        
        if (!allowedType) {
            this.showError(`Tipo de arquivo não permitido: ${file.type}`);
            return false;
        }
        
        // Verificar tamanho
        const maxSizeBytes = allowedType.max_size;
        if (file.size > maxSizeBytes) {
            const maxSizeMB = Math.round(maxSizeBytes / (1024 * 1024));
            this.showError(`Arquivo muito grande. Máximo: ${maxSizeMB}MB`);
            return false;
        }
        
        return true;
    }

    /**
     * Adicionar elementos de progresso
     */
    addProgressElements(file) {
        const previewElement = file.previewElement;
        if (!previewElement) return;
        
        // Barra de progresso personalizada
        const progressBar = document.createElement('div');
        progressBar.className = 'upload-progress-bar';
        progressBar.innerHTML = `
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <div class="upload-info">
                <span class="upload-speed">Calculando...</span>
                <span class="upload-time">Tempo restante: --</span>
            </div>
        `;
        
        previewElement.appendChild(progressBar);
        file.progressBar = progressBar;
        file.uploadStartTime = Date.now();
    }

    /**
     * Atualizar barra de progresso
     */
    updateProgressBar(file, progress) {
        if (!file.progressBar) return;
        
        const progressBarElement = file.progressBar.querySelector('.progress-bar');
        if (progressBarElement) {
            progressBarElement.style.width = `${progress}%`;
            progressBarElement.textContent = `${Math.round(progress)}%`;
        }
    }

    /**
     * Calcular tempo restante
     */
    calculateTimeRemaining(file, progress, bytesSent) {
        if (!file.uploadStartTime || progress <= 0) {
            return null;
        }
        
        const elapsed = (Date.now() - file.uploadStartTime) / 1000; // segundos
        const speed = bytesSent / elapsed; // bytes por segundo
        const remainingBytes = file.size - bytesSent;
        const remainingTime = remainingBytes / speed; // segundos
        
        return remainingTime;
    }

    /**
     * Atualizar tempo restante
     */
    updateTimeRemaining(file, timeRemaining) {
        if (!file.progressBar || !timeRemaining) return;
        
        const timeElement = file.progressBar.querySelector('.upload-time');
        const speedElement = file.progressBar.querySelector('.upload-speed');
        
        if (timeElement) {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = Math.floor(timeRemaining % 60);
            timeElement.textContent = `Tempo restante: ${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        
        if (speedElement) {
            const elapsed = (Date.now() - file.uploadStartTime) / 1000;
            const speed = file.size * (file.upload?.progress || 0) / 100 / elapsed;
            const speedMB = (speed / (1024 * 1024)).toFixed(1);
            speedElement.textContent = `${speedMB} MB/s`;
        }
    }

    /**
     * Remover elementos de progresso
     */
    removeProgressElements(file) {
        if (file.progressBar) {
            file.progressBar.remove();
        }
    }

    /**
     * Mostrar miniatura
     */
    showThumbnail(file, thumbnailUrl) {
        const previewElement = file.previewElement;
        if (!previewElement) return;
        
        const thumbnail = previewElement.querySelector('.dz-image img');
        if (thumbnail) {
            thumbnail.src = thumbnailUrl;
        }
    }

    /**
     * Mostrar notificação de sucesso
     */
    showSuccess(message) {
        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#28a745"
            }).showToast();
        } else {
            console.log('Sucesso:', message);
        }
    }

    /**
     * Mostrar notificação de erro
     */
    showError(message) {
        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: message,
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545"
            }).showToast();
        } else {
            console.error('Erro:', message);
        }
    }

    /**
     * Disparar evento personalizado
     */
    dispatchEvent(eventName, detail) {
        const event = new CustomEvent(eventName, { detail });
        document.dispatchEvent(event);
    }

    /**
     * Definir callback
     */
    on(event, callback) {
        this.callbacks[event] = callback;
    }

    /**
     * Obter arquivos enviados
     */
    getUploadedFiles() {
        return this.uploadedFiles;
    }

    /**
     * Limpar arquivos enviados
     */
    clearUploadedFiles() {
        this.uploadedFiles = [];
        if (this.dropzone) {
            this.dropzone.removeAllFiles();
        }
    }

    /**
     * Destruir uploader
     */
    destroy() {
        if (this.dropzone) {
            this.dropzone.destroy();
        }
    }
}

// Exportar para uso global
window.HomeMechanicUploader = HomeMechanicUploader;

// Auto-inicializar se elemento existir
document.addEventListener('DOMContentLoaded', function() {
    const uploadElement = document.getElementById('upload-dropzone');
    if (uploadElement && !uploadElement.dataset.initialized) {
        window.uploader = new HomeMechanicUploader();
        uploadElement.dataset.initialized = 'true';
    }
});