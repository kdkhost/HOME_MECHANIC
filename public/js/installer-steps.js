/**
 * HomeMechanic - Instalador em Steps
 * JavaScript para controlar o processo de instalação passo a passo
 */

let currentStep = 1;
let installationData = {};

// Navegar para o próximo step
function nextStep(step) {
    // Validar step atual antes de avançar
    if (!validateCurrentStep()) {
        return;
    }
    
    // Atualizar step atual
    currentStep = step;
    
    // Atualizar UI
    updateStepsUI();
    showStepContent(step);
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Voltar para step anterior
function prevStep(step) {
    currentStep = step;
    updateStepsUI();
    showStepContent(step);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Atualizar UI dos steps
function updateStepsUI() {
    document.querySelectorAll('.step').forEach(step => {
        const stepNum = parseInt(step.dataset.step);
        
        step.classList.remove('active', 'completed');
        
        if (stepNum === currentStep) {
            step.classList.add('active');
        } else if (stepNum < currentStep) {
            step.classList.add('completed');
        }
    });
}

// Mostrar conteúdo do step
function showStepContent(step) {
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    
    document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');
}

// Validar step atual
function validateCurrentStep() {
    switch(currentStep) {
        case 1: // Banco de Dados
            const dbHost = document.getElementById('db_host').value;
            const dbName = document.getElementById('db_name').value;
            const dbUser = document.getElementById('db_user').value;
            
            if (!dbHost || !dbName || !dbUser) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos Obrigatórios',
                    text: 'Preencha todos os campos obrigatórios do banco de dados.',
                    confirmButtonColor: '#FF6B00'
                });
                return false;
            }
            break;
            
        case 2: // Administrador
            const adminName = document.getElementById('admin_name').value;
            const adminEmail = document.getElementById('admin_email').value;
            const adminPassword = document.getElementById('admin_password').value;
            const adminPasswordConf = document.getElementById('admin_password_confirmation').value;
            
            if (!adminName || !adminEmail || !adminPassword || !adminPasswordConf) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos Obrigatórios',
                    text: 'Preencha todos os campos do administrador.',
                    confirmButtonColor: '#FF6B00'
                });
                return false;
            }
            
            if (adminPassword.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Senha Inválida',
                    text: 'A senha deve ter no mínimo 8 caracteres.',
                    confirmButtonColor: '#FF6B00'
                });
                return false;
            }
            
            if (adminPassword !== adminPasswordConf) {
                Swal.fire({
                    icon: 'error',
                    title: 'Senhas não conferem',
                    text: 'A senha e confirmação devem ser iguais.',
                    confirmButtonColor: '#FF6B00'
                });
                return false;
            }
            break;
            
        case 3: // Empresa
            const companyName = document.getElementById('company_name').value;
            
            if (!companyName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo Obrigatório',
                    text: 'Preencha o nome da empresa.',
                    confirmButtonColor: '#FF6B00'
                });
                return false;
            }
            break;
    }
    
    return true;
}

// Testar conexão com banco de dados
async function testDatabase() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Testando...';
    
    try {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('db_host', document.getElementById('db_host').value);
        formData.append('db_port', document.getElementById('db_port').value);
        formData.append('db_name', document.getElementById('db_name').value);
        formData.append('db_user', document.getElementById('db_user').value);
        formData.append('db_password', document.getElementById('db_password').value);
        
        const response = await fetch('/install/test-database', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Conexão Bem-sucedida!',
                text: data.message,
                confirmButtonColor: '#FF6B00'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro na Conexão',
                text: data.message,
                confirmButtonColor: '#FF6B00'
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Erro ao testar conexão: ' + error.message,
            confirmButtonColor: '#FF6B00'
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// Iniciar instalação
async function startInstallation() {
    // Confirmar instalação
    const result = await Swal.fire({
        title: 'Confirmar Instalação',
        text: 'Tem certeza que deseja instalar o sistema com essas configurações?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, instalar!',
        cancelButtonText: 'Cancelar'
    });
    
    if (!result.isConfirmed) {
        return;
    }
    
    // Ir para step de instalação
    nextStep(4);
    
    // Coletar dados do formulário
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('db_host', document.getElementById('db_host').value);
    formData.append('db_port', document.getElementById('db_port').value);
    formData.append('db_name', document.getElementById('db_name').value);
    formData.append('db_user', document.getElementById('db_user').value);
    formData.append('db_password', document.getElementById('db_password').value);
    formData.append('admin_name', document.getElementById('admin_name').value);
    formData.append('admin_email', document.getElementById('admin_email').value);
    formData.append('admin_password', document.getElementById('admin_password').value);
    formData.append('admin_password_confirmation', document.getElementById('admin_password_confirmation').value);
    formData.append('company_name', document.getElementById('company_name').value);
    formData.append('company_description', document.getElementById('company_description').value);
    formData.append('system_url', document.getElementById('system_url').value);
    
    // Guardar dados para exibir depois
    installationData = {
        email: document.getElementById('admin_email').value,
        password: document.getElementById('admin_password').value
    };
    
    // Executar instalação
    await performInstallation(formData);
}

// Executar instalação
async function performInstallation(formData) {
    const progressContainer = document.getElementById('installationProgress');
    
    const steps = [
        { id: 'db-test', text: 'Testando conexão com banco de dados...' },
        { id: 'env-create', text: 'Criando arquivo .env...' },
        { id: 'app-key', text: 'Gerando APP_KEY...' },
        { id: 'migrations', text: 'Criando tabelas do banco de dados...' },
        { id: 'seeders', text: 'Inserindo dados iniciais...' },
        { id: 'admin-user', text: 'Criando usuário administrador...' },
        { id: 'finalize', text: 'Finalizando instalação...' }
    ];
    
    // Criar elementos de progresso
    steps.forEach(step => {
        const div = document.createElement('div');
        div.className = 'progress-step';
        div.id = `step-${step.id}`;
        div.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="spinner me-3"></div>
                <div>
                    <strong>${step.text}</strong>
                    <div class="text-muted small" id="status-${step.id}">Aguardando...</div>
                </div>
            </div>
        `;
        progressContainer.appendChild(div);
    });
    
    try {
        // Enviar requisição de instalação
        const response = await fetch('/install', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Marcar todos os steps como concluídos
            steps.forEach(step => {
                const stepEl = document.getElementById(`step-${step.id}`);
                stepEl.classList.remove('processing');
                stepEl.classList.add('completed');
                document.getElementById(`status-${step.id}`).textContent = 'Concluído ✓';
            });
            
            // Aguardar um pouco para mostrar conclusão
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Ir para step final
            showSuccessScreen();
        } else {
            throw new Error(data.message || 'Erro durante a instalação');
        }
    } catch (error) {
        // Marcar como erro
        progressContainer.innerHTML += `
            <div class="progress-step error">
                <strong><i class="bi bi-x-circle me-2"></i>Erro na Instalação</strong>
                <div class="text-danger">${error.message}</div>
            </div>
        `;
        
        Swal.fire({
            icon: 'error',
            title: 'Erro na Instalação',
            text: error.message,
            confirmButtonColor: '#FF6B00'
        });
    }
}

// Mostrar tela de sucesso
function showSuccessScreen() {
    // Atualizar credenciais
    document.getElementById('displayEmail').textContent = installationData.email;
    document.getElementById('displayPassword').textContent = installationData.password;
    
    // Ir para step final
    nextStep(5);
    
    // Confetti animation (opcional)
    Swal.fire({
        icon: 'success',
        title: 'Instalação Concluída!',
        text: 'O HomeMechanic foi instalado com sucesso!',
        confirmButtonColor: '#FF6B00',
        timer: 3000,
        timerProgressBar: true
    });
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    console.log('HomeMechanic Installer Steps - Inicializado');
    
    // Detectar URL automaticamente
    const protocol = window.location.protocol;
    const host = window.location.host;
    const systemUrl = `${protocol}//${host}`;
    document.getElementById('system_url').value = systemUrl;
});