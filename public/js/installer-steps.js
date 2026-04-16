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
        
        console.log('Testando conexão com banco de dados...');
        
        // Tentar primeiro a rota do Laravel
        let response;
        try {
            response = await fetch('/install/test-database', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('Resposta da rota Laravel:', response.status);
        } catch (error) {
            console.warn('Erro na rota Laravel, tentando rota direta...', error);
            
            // Fallback: usar teste direto sem Laravel
            response = await fetch('/test-database-direct.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Resposta da rota direta:', response.status);
        }
        
        const data = await response.json();
        console.log('Dados da resposta:', data);
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Conexão Bem-sucedida!',
                html: `
                    <p>${data.message}</p>
                    ${data.version ? `<p><small>MySQL ${data.version}</small></p>` : ''}
                `,
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
        console.error('Erro ao testar conexão:', error);
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
    // Validar todos os steps antes de iniciar
    console.log('Validando dados antes da instalação...');
    
    // Validar Step 1: Banco de Dados
    const dbHost = document.getElementById('db_host').value;
    const dbName = document.getElementById('db_name').value;
    const dbUser = document.getElementById('db_user').value;
    
    if (!dbHost || !dbName || !dbUser) {
        Swal.fire({
            icon: 'error',
            title: 'Dados Incompletos',
            text: 'Por favor, preencha todos os dados do banco de dados no Step 1.',
            confirmButtonColor: '#FF6B00'
        });
        // Voltar para step 1
        currentStep = 1;
        updateStepsUI();
        showStepContent(1);
        return;
    }
    
    // Validar Step 2: Administrador
    const adminName = document.getElementById('admin_name').value;
    const adminEmail = document.getElementById('admin_email').value;
    const adminPassword = document.getElementById('admin_password').value;
    const adminPasswordConf = document.getElementById('admin_password_confirmation').value;
    
    if (!adminName || !adminEmail || !adminPassword || !adminPasswordConf) {
        Swal.fire({
            icon: 'error',
            title: 'Dados Incompletos',
            text: 'Por favor, preencha todos os dados do administrador no Step 2.',
            confirmButtonColor: '#FF6B00'
        });
        // Voltar para step 2
        currentStep = 2;
        updateStepsUI();
        showStepContent(2);
        return;
    }
    
    if (adminPassword.length < 8) {
        Swal.fire({
            icon: 'error',
            title: 'Senha Inválida',
            text: 'A senha deve ter no mínimo 8 caracteres.',
            confirmButtonColor: '#FF6B00'
        });
        currentStep = 2;
        updateStepsUI();
        showStepContent(2);
        return;
    }
    
    if (adminPassword !== adminPasswordConf) {
        Swal.fire({
            icon: 'error',
            title: 'Senhas não conferem',
            text: 'A senha e confirmação devem ser iguais.',
            confirmButtonColor: '#FF6B00'
        });
        currentStep = 2;
        updateStepsUI();
        showStepContent(2);
        return;
    }
    
    // Validar Step 3: Empresa
    const companyName = document.getElementById('company_name').value;
    
    if (!companyName) {
        Swal.fire({
            icon: 'error',
            title: 'Dados Incompletos',
            text: 'Por favor, preencha o nome da empresa no Step 3.',
            confirmButtonColor: '#FF6B00'
        });
        currentStep = 3;
        updateStepsUI();
        showStepContent(3);
        return;
    }
    
    console.log('Todos os dados validados com sucesso!');
    
    // Confirmar instalação
    const result = await Swal.fire({
        title: 'Confirmar Instalação',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                <p><strong>Banco de Dados:</strong> ${dbName} @ ${dbHost}</p>
                <p><strong>Administrador:</strong> ${adminName} (${adminEmail})</p>
                <p><strong>Empresa:</strong> ${companyName}</p>
            </div>
            <p style="color: #dc3545; font-weight: bold;">Esta ação não pode ser desfeita!</p>
        `,
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
    formData.append('db_host', dbHost);
    formData.append('db_port', document.getElementById('db_port').value);
    formData.append('db_name', dbName);
    formData.append('db_user', dbUser);
    formData.append('db_password', document.getElementById('db_password').value);
    formData.append('admin_name', adminName);
    formData.append('admin_email', adminEmail);
    formData.append('admin_password', adminPassword);
    formData.append('admin_password_confirmation', adminPasswordConf);
    formData.append('company_name', companyName);
    formData.append('company_description', document.getElementById('company_description').value);
    formData.append('system_url', document.getElementById('system_url').value);
    formData.append('terms_accepted', document.getElementById('terms_accepted').checked ? '1' : '0');
    
    // Guardar dados para exibir depois
    installationData = {
        email: adminEmail,
        password: adminPassword
    };
    
    console.log('Iniciando processo de instalação...');
    
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
        console.log('Enviando requisição de instalação...');
        
        // Log dos dados sendo enviados (sem senhas)
        const debugData = {};
        for (let [key, value] of formData.entries()) {
            if (!key.includes('password')) {
                debugData[key] = value;
            }
        }
        console.log('Dados enviados:', debugData);
        
        // Enviar requisição de instalação
        const response = await fetch('/install', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('Resposta recebida:', response.status, response.statusText);
        
        // Verificar se a resposta é JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            console.error('Resposta não é JSON:', textResponse);
            throw new Error('Resposta do servidor não é JSON. Verifique os logs do servidor.');
        }
        
        const data = await response.json();
        console.log('Dados da resposta:', data);
        
        if (data.success) {
            console.log('Instalação concluída com sucesso!');
            
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
            console.error('Instalação falhou:', data);
            
            // Se houver erros de validação, mostrar detalhadamente
            if (data.errors) {
                let errorList = '<ul style="text-align: left;">';
                for (let field in data.errors) {
                    errorList += `<li><strong>${field}:</strong> ${data.errors[field].join(', ')}</li>`;
                }
                errorList += '</ul>';
                
                throw new Error(`Erros de validação:\n${errorList}`);
            }
            
            throw new Error(data.message || 'Erro durante a instalação');
        }
    } catch (error) {
        console.error('Erro capturado:', error);
        
        // Marcar como erro
        progressContainer.innerHTML += `
            <div class="progress-step error">
                <strong><i class="bi bi-x-circle me-2"></i>Erro na Instalação</strong>
                <div class="text-danger">${error.message}</div>
                ${error.stack ? `<details style="margin-top: 10px;"><summary>Detalhes técnicos</summary><pre style="font-size: 11px;">${error.stack}</pre></details>` : ''}
            </div>
        `;
        
        Swal.fire({
            icon: 'error',
            title: 'Erro na Instalação',
            html: `
                <p>${error.message}</p>
                <p style="font-size: 12px; color: #6c757d; margin-top: 15px;">
                    Verifique o console do navegador (F12) para mais detalhes.
                </p>
            `,
            confirmButtonColor: '#FF6B00',
            footer: '<a href="/fix-env-key.php">Tentar corrigir problemas</a>'
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