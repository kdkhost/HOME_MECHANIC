/**
 * HomeMechanic - JavaScript do Instalador com Debug
 * Versão melhorada com logs detalhados e tratamento de erros
 */

// Função para log de debug
function debugLog(message, data = null) {
    console.log(`[HomeMechanic Installer] ${message}`, data || '');
}

// Função para mostrar erro detalhado
function showDetailedError(title, message, details = null) {
    console.error(`[HomeMechanic Error] ${title}:`, message, details);
    
    let errorText = message;
    if (details) {
        errorText += '\n\nDetalhes técnicos:\n' + JSON.stringify(details, null, 2);
    }
    
    Swal.fire({
        icon: 'error',
        title: title,
        text: errorText,
        confirmButtonColor: '#FF6B00',
        footer: 'Verifique o console do navegador (F12) para mais detalhes'
    });
}

// Verificar se todas as dependências estão carregadas
function checkDependencies() {
    debugLog('Verificando dependências...');
    
    const dependencies = {
        'Swal (SweetAlert2)': typeof Swal !== 'undefined',
        'fetch': typeof fetch !== 'undefined',
        'FormData': typeof FormData !== 'undefined',
        'Promise': typeof Promise !== 'undefined'
    };
    
    let allOk = true;
    for (const [name, available] of Object.entries(dependencies)) {
        if (available) {
            debugLog(`✅ ${name} disponível`);
        } else {
            debugLog(`❌ ${name} NÃO disponível`);
            allOk = false;
        }
    }
    
    return allOk;
}

// Função melhorada para testar conexão com banco
async function testDatabaseConnection() {
    debugLog('Iniciando teste de conexão com banco...');
    
    const btn = document.getElementById('testDbBtn');
    if (!btn) {
        debugLog('❌ Botão de teste não encontrado');
        return;
    }
    
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Testando...';
    
    try {
        const formData = new FormData();
        
        // Coletar dados do formulário
        const dbData = {
            db_host: document.getElementById('db_host')?.value || '',
            db_port: document.getElementById('db_port')?.value || '3306',
            db_name: document.getElementById('db_name')?.value || '',
            db_user: document.getElementById('db_user')?.value || '',
            db_password: document.getElementById('db_password')?.value || ''
        };
        
        debugLog('Dados do banco coletados:', { ...dbData, db_password: '***' });
        
        // Validar dados obrigatórios
        const requiredFields = ['db_host', 'db_name', 'db_user'];
        const missingFields = requiredFields.filter(field => !dbData[field]);
        
        if (missingFields.length > 0) {
            throw new Error(`Campos obrigatórios não preenchidos: ${missingFields.join(', ')}`);
        }
        
        // Adicionar dados ao FormData
        Object.entries(dbData).forEach(([key, value]) => {
            formData.append(key, value);
        });
        
        // Adicionar CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
            debugLog('✅ CSRF token adicionado');
        } else {
            debugLog('⚠️ CSRF token não encontrado');
        }
        
        debugLog('Enviando requisição para /install/test-database...');
        
        const response = await fetch('/install/test-database', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        debugLog(`Resposta recebida: Status ${response.status}`);
        
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status} ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        debugLog(`Content-Type: ${contentType}`);
        
        const responseText = await response.text();
        debugLog(`Tamanho da resposta: ${responseText.length} caracteres`);
        
        let data;
        try {
            data = JSON.parse(responseText);
            debugLog('Resposta JSON parseada:', data);
        } catch (e) {
            debugLog('❌ Erro ao parsear JSON:', e.message);
            debugLog('Resposta raw:', responseText.substring(0, 500));
            throw new Error('Resposta do servidor não é um JSON válido');
        }
        
        if (data.success) {
            debugLog('✅ Teste de conexão bem-sucedido');
            Swal.fire({
                icon: 'success',
                title: 'Conexão Bem-sucedida!',
                text: data.message,
                confirmButtonColor: '#FF6B00'
            });
        } else {
            debugLog('❌ Teste de conexão falhou:', data.message);
            Swal.fire({
                icon: 'error',
                title: 'Erro na Conexão',
                text: data.message,
                confirmButtonColor: '#FF6B00'
            });
        }
        
    } catch (error) {
        debugLog('❌ Erro durante teste de conexão:', error);
        showDetailedError('Erro no Teste de Conexão', error.message, {
            stack: error.stack,
            name: error.name
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
        debugLog('Teste de conexão finalizado');
    }
}

// Função melhorada para instalação
async function performInstallation(formElement) {
    debugLog('Iniciando processo de instalação...');
    
    try {
        // Mostrar overlay de loading
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
            debugLog('✅ Loading overlay exibido');
        } else {
            debugLog('⚠️ Loading overlay não encontrado');
        }
        
        // Coletar dados do formulário
        const formData = new FormData(formElement);
        
        // Log dos dados (sem senhas)
        const formDataObj = {};
        for (let [key, value] of formData.entries()) {
            formDataObj[key] = key.includes('password') ? '***' : value;
        }
        debugLog('Dados do formulário coletados:', formDataObj);
        
        // Verificar CSRF token
        const csrfToken = formData.get('_token');
        if (csrfToken) {
            debugLog('✅ CSRF token presente no formulário');
        } else {
            debugLog('❌ CSRF token ausente no formulário');
        }
        
        debugLog('Enviando requisição de instalação...');
        
        const response = await fetch(formElement.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        debugLog(`Resposta da instalação: Status ${response.status}`);
        
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
        
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status} ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        debugLog(`Content-Type da resposta: ${contentType}`);
        
        const responseText = await response.text();
        debugLog(`Tamanho da resposta: ${responseText.length} caracteres`);
        
        let data;
        try {
            data = JSON.parse(responseText);
            debugLog('Resposta da instalação parseada:', data);
        } catch (e) {
            debugLog('❌ Erro ao parsear resposta JSON:', e.message);
            debugLog('Resposta raw (primeiros 1000 chars):', responseText.substring(0, 1000));
            throw new Error('Resposta do servidor não é um JSON válido');
        }
        
        if (data.success) {
            debugLog('✅ Instalação concluída com sucesso!');
            
            Swal.fire({
                icon: 'success',
                title: 'Instalação Concluída!',
                text: data.message,
                confirmButtonColor: '#FF6B00',
                allowOutsideClick: false
            }).then(() => {
                const redirectUrl = data.redirect || data.admin_url || '/';
                debugLog(`Redirecionando para: ${redirectUrl}`);
                window.location.href = redirectUrl;
            });
        } else {
            debugLog('❌ Instalação falhou:', data.message);
            
            showDetailedError('Erro na Instalação', data.message, {
                details: data.details,
                debug_info: data.debug_info
            });
        }
        
    } catch (error) {
        debugLog('❌ Erro durante instalação:', error);
        
        // Esconder loading overlay
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
        
        showDetailedError('Erro Durante a Instalação', error.message, {
            stack: error.stack,
            name: error.name
        });
    }
}

// Inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    debugLog('DOM carregado, inicializando instalador...');
    
    // Verificar dependências
    if (!checkDependencies()) {
        alert('ERRO: Algumas dependências JavaScript não foram carregadas. Verifique sua conexão com a internet e recarregue a página.');
        return;
    }
    
    // Configurar evento do formulário
    const installForm = document.getElementById('installForm');
    if (installForm) {
        debugLog('✅ Formulário de instalação encontrado');
        
        installForm.addEventListener('submit', function(e) {
            e.preventDefault();
            debugLog('Evento submit capturado, iniciando validação...');
            
            // Validar confirmação de senha
            const password = document.getElementById('admin_password')?.value || '';
            const confirmation = document.getElementById('admin_password_confirmation')?.value || '';
            
            if (password !== confirmation) {
                debugLog('❌ Senhas não conferem');
                Swal.fire({
                    icon: 'error',
                    title: 'Senhas não conferem',
                    text: 'A senha e confirmação devem ser iguais.',
                    confirmButtonColor: '#FF6B00'
                });
                return;
            }
            
            debugLog('✅ Validação de senha OK');
            
            // Mostrar confirmação
            Swal.fire({
                title: 'Confirmar Instalação',
                text: 'Tem certeza que deseja instalar o sistema com essas configurações?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF6B00',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, instalar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    debugLog('✅ Instalação confirmada pelo usuário');
                    performInstallation(this);
                } else {
                    debugLog('ℹ️ Instalação cancelada pelo usuário');
                }
            });
        });
    } else {
        debugLog('❌ Formulário de instalação NÃO encontrado!');
        console.error('ERRO: Formulário com ID "installForm" não foi encontrado na página');
    }
    
    // Configurar botão de teste de banco
    const testDbBtn = document.getElementById('testDbBtn');
    if (testDbBtn) {
        debugLog('✅ Botão de teste de banco encontrado');
        testDbBtn.addEventListener('click', testDatabaseConnection);
    } else {
        debugLog('⚠️ Botão de teste de banco não encontrado');
    }
    
    // Configurar validação em tempo real de senha
    const passwordConfirmation = document.getElementById('admin_password_confirmation');
    if (passwordConfirmation) {
        passwordConfirmation.addEventListener('input', function() {
            const password = document.getElementById('admin_password')?.value || '';
            const confirmation = this.value;
            
            if (confirmation && password !== confirmation) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (confirmation) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
    }
    
    debugLog('✅ Instalador inicializado com sucesso!');
});

// Expor funções globalmente para debug
window.HomeMechanicInstaller = {
    debugLog,
    testDatabaseConnection,
    performInstallation,
    checkDependencies
};