# Documento de Requisitos

## Introdução

O **HomeMechanic** é um sistema web completo para uma empresa de auto mecânica especializada em carros de luxo esportivos e tuning. O sistema é composto por um site institucional moderno e responsivo com frontend avançado, painel administrativo baseado em AdminLTE 4, blog completo, galeria de fotos interativa, gerenciamento de serviços e clientes, além de funcionalidades avançadas como instalador automático com verificação de requisitos, página de manutenção avançada, segurança robusta contra invasões e configurações de SMTP internas com teste em tempo real via AJAX.

A stack tecnológica utiliza Laravel 13, PHP 8.4 e MySQL/MariaDB com arquitetura modular. O sistema implementa CRUD totalmente em AJAX com notificações Toastify e confirmações SweetAlert2, upload via drag and drop com barra de progresso animada e tempo restante, preloader personalizado, páginas de erro customizadas e pasta public oculta na URL. A paleta de cores padrão é laranja (#FF6B00), preto (#0D0D0D), grafite (#2C2C2C) e branco (#FFFFFF).

---

## Glossário

- **Sistema**: A aplicação web HomeMechanic como um todo.
- **Admin**: Usuário autenticado com acesso ao painel administrativo.
- **Visitante**: Usuário não autenticado que acessa o site institucional.
- **Painel_Admin**: Interface administrativa baseada em AdminLTE 4 com paleta personalizada.
- **Frontend**: Páginas públicas do site institucional responsivo acessíveis por visitantes.
- **Instalador**: Módulo de instalação automática do sistema com verificação completa de requisitos do servidor.
- **CRUD_AJAX**: Operações de criação, leitura, atualização e exclusão realizadas via requisições AJAX sem recarregamento de página, com notificações Toastify e confirmações SweetAlert2.
- **Uploader**: Componente de upload de arquivos com suporte a drag and drop, barra de progresso animada e cálculo de tempo restante.
- **Galeria**: Módulo de gerenciamento e exibição de fotos com categorização e lightbox interativo.
- **Blog**: Módulo completo de criação e publicação de artigos com editor WYSIWYG e SEO.
- **SMTP_Config**: Módulo de configuração de servidor de e-mail interno com teste de envio em tempo real via AJAX.
- **Autenticador**: Módulo responsável por autenticação segura, controle de sessão e proteção contra ataques de força bruta.
- **Rate_Limiter**: Componente que bloqueia tentativas excessivas de login com bloqueio temporário por IP.
- **Sanitizador**: Componente responsável por validar e sanitizar todas as entradas do usuário contra injeção de código.
- **Manutencao**: Módulo de página de manutenção avançada com controle granular de acesso por IP.
- **Modulo**: Unidade funcional independente do sistema, com suas próprias rotas, controllers, models e views organizadas modularmente.
- **Preloader**: Componente de carregamento inicial com animação personalizada e logotipo da empresa.
- **Security_Layer**: Camada de segurança multicamadas contra SQL injection, XSS, CSRF e tentativas de invasão.

---

## Requisitos

### Requisito 1: Instalador Automático com Verificação Completa

**User Story:** Como administrador, quero um instalador automático completo ao acessar o sistema pela primeira vez, para que eu possa configurar o ambiente sem editar arquivos manualmente e com garantia de que todos os requisitos do servidor são atendidos.

#### Critérios de Aceitação

1. WHEN o sistema é acessado sem arquivo `storage/installed`, THE Instalador SHALL redirecionar automaticamente o visitante para a rota `/install`.
2. THE Instalador SHALL verificar todos os requisitos mínimos do servidor: PHP 8.4+, extensões obrigatórias (`pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`), mod_rewrite do Apache habilitado e permissões de escrita nas pastas `storage` e `bootstrap/cache`.
3. WHEN todos os requisitos são atendidos, THE Instalador SHALL exibir um formulário moderno de configuração com seções organizadas: configuração de banco de dados, credenciais do administrador e dados da empresa.
4. WHEN o formulário de instalação é submetido, THE Instalador SHALL criar o arquivo `.env` completo, executar `php artisan key:generate`, executar todas as migrations e seeders, e configurar o sistema automaticamente.
5. IF a conexão com o banco de dados falhar durante a instalação, THEN THE Instalador SHALL exibir uma mensagem de erro descritiva e específica sem expor credenciais sensíveis no frontend.
6. WHEN a instalação é concluída com sucesso, THE Instalador SHALL criar um arquivo `storage/installed` como marcador e redirecionar automaticamente para o painel administrativo.
7. WHEN o arquivo `storage/installed` existe, THE Instalador SHALL bloquear permanentemente o acesso à rota `/install` e redirecionar para a página inicial para prevenir reinstalações acidentais.
8. THE Instalador SHALL exibir uma interface visual moderna com indicadores de progresso e feedback em tempo real durante o processo de instalação.

---

### Requisito 2: Autenticação e Segurança Multicamadas

**User Story:** Como administrador, quero um sistema de login altamente seguro com múltiplas camadas de proteção contra ataques, para que o painel administrativo seja completamente protegido contra tentativas de invasão e acesso não autorizado.

#### Critérios de Aceitação

1. THE Sistema SHALL disponibilizar uma página de login moderna em duas colunas com animações CSS suaves e design responsivo, localizada na rota `/admin/login`.
2. WHEN o Admin submete credenciais válidas, THE Autenticador SHALL iniciar uma sessão autenticada segura, regenerar o token de sessão e redirecionar para o Painel_Admin.
3. IF o Admin submete credenciais inválidas 5 vezes consecutivas no mesmo IP em um período de 10 minutos, THEN THE Rate_Limiter SHALL bloquear automaticamente novas tentativas de login desse IP por 15 minutos e exibir uma mensagem informativa com contador regressivo do tempo restante de bloqueio.
4. THE Autenticador SHALL implementar proteção CSRF robusta em todos os formulários de autenticação com tokens únicos por sessão.
5. THE Sanitizador SHALL sanitizar e validar rigorosamente todos os campos de entrada do formulário de login antes do processamento, removendo tags HTML e caracteres maliciosos.
6. WHEN uma sessão autenticada fica inativa por 120 minutos, THE Autenticador SHALL encerrar automaticamente a sessão e redirecionar para a página de login com mensagem explicativa.
7. THE Sistema SHALL armazenar todas as senhas utilizando exclusivamente o algoritmo bcrypt com fator de custo mínimo de 12 para máxima segurança.
8. THE Sistema SHALL implementar proteção completa contra SQL Injection utilizando exclusivamente Eloquent ORM e Query Builder parametrizado do Laravel, sem queries SQL diretas.
9. THE Sistema SHALL implementar cabeçalhos HTTP de segurança obrigatórios em todas as respostas: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection: 1; mode=block` e `Referrer-Policy: strict-origin-when-cross-origin`.
10. WHEN o Admin realiza logout, THE Autenticador SHALL invalidar completamente a sessão, regenerar o token CSRF, limpar todos os cookies de sessão e redirecionar para a página de login.
11. THE Sistema SHALL registrar automaticamente todas as tentativas de login (sucessos e falhas) em logs de auditoria com timestamp, IP, user agent e resultado da tentativa.
12. THE Sistema SHALL implementar proteção contra ataques de força bruta com bloqueio progressivo: 1ª tentativa inválida = delay de 1s, 2ª = 2s, 3ª = 4s, 4ª = 8s, 5ª = bloqueio de 15 minutos.

---

### Requisito 3: Painel Administrativo AdminLTE 4 Personalizado

**User Story:** Como administrador, quero um painel administrativo completo e moderno baseado em AdminLTE 4 com personalização visual da marca, para que eu possa gerenciar todos os conteúdos e configurações do sistema de forma intuitiva e eficiente.

#### Critérios de Aceitação

1. THE Painel_Admin SHALL utilizar o tema AdminLTE 4 mais recente com personalização completa da paleta de cores: laranja (#FF6B00) como cor primária, preto (#0D0D0D), grafite (#2C2C2C) e branco (#FFFFFF) como cores secundárias.
2. THE Painel_Admin SHALL exibir um dashboard principal com cards de resumo informativos e atualizados em tempo real: total de serviços ativos, total de posts do blog publicados, total de fotos na galeria ativas e total de mensagens de contato não lidas.
3. THE Painel_Admin SHALL conter um menu lateral organizado e intuitivo com as seções principais: Dashboard, Serviços, Galeria, Blog, Depoimentos, Mensagens de Contato, Configurações Gerais, Configurações SMTP, Manutenção, Usuários e Logs de Auditoria.
4. THE Painel_Admin SHALL ser completamente responsivo e funcional em todos os dispositivos móveis com largura mínima de 320px, adaptando automaticamente o layout e navegação.
5. WHEN o Admin acessa qualquer rota do painel sem autenticação válida, THE Autenticador SHALL redirecionar imediatamente para a página de login com mensagem explicativa.
6. THE Painel_Admin SHALL exibir um preloader personalizado e animado com o logotipo da empresa durante o carregamento inicial de cada página e transições AJAX.
7. THE Painel_Admin SHALL implementar breadcrumbs dinâmicos em todas as páginas para facilitar a navegação e orientação do usuário.
8. THE Painel_Admin SHALL exibir notificações em tempo real no header para novas mensagens de contato, atualizações do sistema e alertas importantes.
9. THE Painel_Admin SHALL incluir um perfil de usuário completo com opções para alterar senha, dados pessoais e preferências de interface.

---

### Requisito 4: CRUD Completo via AJAX com Notificações Avançadas

**User Story:** Como administrador, quero realizar todas as operações de gerenciamento de conteúdo via AJAX com feedback visual imediato e confirmações inteligentes, para que a experiência seja fluida, moderna e sem recarregamentos de página desnecessários.

#### Critérios de Aceitação

1. THE CRUD_AJAX SHALL implementar operações completas de criação, leitura, atualização e exclusão para todos os módulos principais: Serviços, Posts do Blog, Categorias, Fotos da Galeria, Depoimentos, Mensagens de Contato e Usuários.
2. WHEN uma operação CRUD é concluída com sucesso, THE CRUD_AJAX SHALL exibir uma notificação elegante via Toastify com mensagem descritiva personalizada, ícone apropriado e cor correspondente ao tipo de operação (verde para sucesso, vermelho para erro, amarelo para aviso, azul para informação).
3. WHEN o Admin solicita a exclusão de qualquer registro, THE CRUD_AJAX SHALL exibir uma confirmação moderna via SweetAlert2 com título personalizado, descrição do impacto da ação e botões estilizados antes de executar a operação destrutiva.
4. IF uma operação CRUD retorna erro de validação, THEN THE CRUD_AJAX SHALL exibir os erros de validação inline nos campos correspondentes com destaque visual, mantendo o formulário aberto e os dados preenchidos para correção.
5. THE CRUD_AJAX SHALL utilizar tokens CSRF únicos e validados em todas as requisições de escrita (POST, PUT, DELETE) para máxima segurança.
6. WHEN uma listagem de registros é carregada, THE CRUD_AJAX SHALL suportar paginação dinâmica, busca em tempo real, filtros avançados e ordenação por colunas via AJAX sem recarregar a página.
7. THE Sanitizador SHALL validar, sanitizar e filtrar rigorosamente todos os dados recebidos nas requisições AJAX antes de persistir no banco de dados, removendo código malicioso.
8. THE CRUD_AJAX SHALL implementar loading states visuais durante as operações, desabilitando botões e exibindo spinners para prevenir duplo clique e melhorar UX.
9. THE CRUD_AJAX SHALL suportar operações em lote (bulk actions) para seleção múltipla e ações simultâneas em vários registros.

---

### Requisito 5: Upload Avançado com Drag and Drop e Progresso Animado

**User Story:** Como administrador, quero fazer upload de imagens e mídias via drag and drop com feedback visual completo em tempo real, para que o processo de envio de arquivos seja intuitivo, informativo e profissional.

#### Critérios de Aceitação

1. THE Uploader SHALL aceitar arquivos arrastados diretamente para a área de upload destacada visualmente ou selecionados via clique no botão de seleção, com feedback visual imediato ao arrastar sobre a zona.
2. THE Uploader SHALL exibir uma barra de progresso animada e elegante com percentual de conclusão preciso, velocidade de upload atual e tempo restante estimado calculado dinamicamente durante todo o processo de upload.
3. THE Uploader SHALL aceitar exclusivamente os formatos de imagem seguros: JPEG, PNG, WebP e GIF, com tamanho máximo rigorosamente limitado a 10MB por arquivo individual.
4. THE Uploader SHALL aceitar exclusivamente os formatos de vídeo otimizados: MP4 e WebM, com tamanho máximo rigorosamente limitado a 100MB por arquivo individual.
5. IF o arquivo enviado exceder o tamanho máximo permitido, tiver formato não suportado ou falhar na validação de segurança, THEN THE Uploader SHALL rejeitar imediatamente o arquivo e exibir uma mensagem de erro específica e descritiva via Toastify antes mesmo do envio ao servidor.
6. THE Uploader SHALL suportar upload simultâneo de múltiplos arquivos com filas de progresso individuais e organizadas por arquivo, permitindo cancelamento individual de uploads em andamento.
7. WHEN o upload é concluído com sucesso, THE Uploader SHALL exibir automaticamente uma miniatura de pré-visualização do arquivo enviado com opções de edição básica (rotação, crop) e botão de remoção.
8. THE Sistema SHALL armazenar todos os arquivos enviados no diretório seguro `storage/app/public/uploads` com nomes únicos gerados por UUID para evitar colisões, sobrescrita e ataques de path traversal.
9. THE Uploader SHALL implementar validação de tipo MIME real do arquivo (não apenas extensão) usando `finfo_file()` para detectar arquivos maliciosos disfarçados.
10. THE Uploader SHALL gerar automaticamente thumbnails otimizados para imagens com múltiplos tamanhos (150x150, 400x300, 800x600) para diferentes contextos de uso.

---

### Requisito 6: Galeria de Fotos Interativa e Avançada

**User Story:** Como administrador, quero gerenciar uma galeria de fotos categorizada e interativa, para que os visitantes possam visualizar os trabalhos realizados pela empresa de forma elegante e profissional.

#### Critérios de Aceitação

1. THE Galeria SHALL organizar fotos em categorias totalmente configuráveis pelo Admin com suporte a hierarquia, ordenação personalizada e descrições detalhadas.
2. THE CRUD_AJAX SHALL permitir ao Admin criar, editar, reordenar via drag and drop e excluir fotos e categorias da galeria com confirmações SweetAlert2 e notificações Toastify.
3. WHEN o Visitante acessa a página de galeria no Frontend, THE Galeria SHALL exibir as fotos em um layout em grade responsivo moderno com filtro dinâmico por categoria via JavaScript sem recarregar a página e animações suaves de transição.
4. WHEN o Visitante clica em uma foto, THE Galeria SHALL abrir um lightbox elegante e responsivo com navegação fluida entre fotos da mesma categoria, zoom, rotação e compartilhamento social.
5. THE Galeria SHALL implementar lazy loading inteligente para otimizar drasticamente o tempo de carregamento, carregando imagens apenas quando entram na viewport do usuário.
6. THE Sistema SHALL gerar automaticamente thumbnails otimizados das imagens enviadas com múltiplas dimensões (150x150 para grid, 400x300 para preview, 800x600 para lightbox) mantendo sempre a proporção original e qualidade.
7. THE Galeria SHALL suportar busca avançada por título, descrição, categoria e tags com resultados em tempo real.
8. THE Galeria SHALL implementar sistema de favoritos para visitantes marcarem fotos preferidas (armazenado em localStorage).
9. THE Galeria SHALL exibir metadados das fotos (data, câmera, configurações) quando disponíveis nos dados EXIF.

---

### Requisito 7: Blog Completo com SEO Avançado

**User Story:** Como administrador, quero gerenciar um blog completo e otimizado para SEO com posts categorizados e tags, para que a empresa possa publicar conteúdo relevante sobre mecânica e tuning e atrair mais visitantes através dos mecanismos de busca.

#### Critérios de Aceitação

1. THE Blog SHALL suportar posts completos com título, slug único automaticamente gerado, conteúdo rico via editor WYSIWYG avançado (TinyMCE), imagem de capa, categoria, múltiplas tags, status (rascunho/publicado/agendado), data de publicação agendada e campos completos de SEO.
2. THE CRUD_AJAX SHALL permitir ao Admin criar, editar, duplicar, publicar e excluir posts e categorias do blog com preview em tempo real e autosave automático.
3. WHEN o Admin salva um post, THE Blog SHALL gerar automaticamente o slug otimizado para SEO a partir do título caso o slug não seja informado manualmente, removendo acentos e caracteres especiais.
4. WHEN dois posts possuem o mesmo slug, THE Blog SHALL acrescentar automaticamente um sufixo numérico incremental (-2, -3, etc.) para garantir unicidade absoluta na URL.
5. WHEN o Visitante acessa a listagem do blog no Frontend, THE Blog SHALL exibir os posts publicados em ordem cronológica decrescente com paginação elegante de 9 posts por página, filtros por categoria e busca em tempo real.
6. WHEN o Visitante acessa um post individual, THE Blog SHALL exibir o conteúdo completo formatado, posts relacionados da mesma categoria, breadcrumbs, tempo estimado de leitura, botões de compartilhamento social e sistema de comentários.
7. THE Blog SHALL implementar meta tags de SEO completas e individuais por post: title otimizado, meta description, og:image, og:title, og:description, canonical URL e structured data (JSON-LD).
8. THE Blog SHALL suportar agendamento de posts com publicação automática na data/hora especificada via job queue do Laravel.
9. THE Blog SHALL implementar sistema de tags inteligente com sugestões automáticas baseadas no conteúdo e tags populares.
10. THE Blog SHALL gerar automaticamente sitemap XML atualizado dinamicamente para melhor indexação pelos mecanismos de busca.

---

### Requisito 8: Site Institucional Moderno e Responsivo

**User Story:** Como visitante, quero acessar um site institucional moderno, elegante e completamente responsivo, para que eu possa conhecer os serviços da empresa, visualizar trabalhos realizados e entrar em contato facilmente.

#### Critérios de Aceitação

1. THE Frontend SHALL conter todas as páginas essenciais: Home (com seções hero, serviços, galeria, depoimentos, contato), Sobre Nós, Serviços Completos, Galeria Interativa, Blog, Contato e Política de Privacidade.
2. THE Frontend SHALL utilizar exclusivamente a paleta de cores da marca: laranja (#FF6B00), preto (#0D0D0D), grafite (#2C2C2C) e branco (#FFFFFF) com tipografia moderna (Rajdhani para títulos, Inter para textos) e animações CSS suaves e profissionais.
3. THE Frontend SHALL exibir um preloader personalizado e animado com o logotipo da empresa HomeMechanic durante o carregamento inicial de cada página.
4. THE Frontend SHALL ser completamente responsivo com breakpoints otimizados para mobile (320px-767px), tablet (768px-1023px) e desktop (1024px+) com layout fluido e imagens adaptáveis.
5. THE Frontend SHALL implementar um menu de navegação moderno e sticky com efeito de transparência ao rolar a página, destacando a seção ativa e com menu hambúrguer animado no mobile.
6. WHEN o Visitante submete o formulário de contato, THE Sistema SHALL validar rigorosamente todos os campos, enviar o e-mail via SMTP configurado, salvar a mensagem no banco de dados e exibir confirmação com notificação Toastify.
7. IF o envio do e-mail de contato falhar por problemas de SMTP, THEN THE Sistema SHALL salvar a mensagem no banco de dados, registrar o erro nos logs e exibir uma mensagem de sucesso ao visitante sem revelar o erro técnico interno.
8. THE Frontend SHALL exibir uma seção de depoimentos de clientes com carrossel elegante e animado (Swiper.js) incluindo fotos, nomes, avaliações em estrelas e depoimentos completos.
9. THE Frontend SHALL exibir uma seção de serviços com cards modernos e animados ao entrar na viewport (scroll animations via IntersectionObserver) com ícones, descrições e links para páginas detalhadas.
10. THE Frontend SHALL implementar meta tags de SEO globais configuráveis pelo Admin e meta tags específicas por página para otimização completa nos mecanismos de busca.
11. THE Frontend SHALL implementar schema markup (JSON-LD) para empresa local, serviços e avaliações para melhor visibilidade no Google.
12. THE Frontend SHALL incluir botão flutuante do WhatsApp com número configurável pelo Admin para contato direto.

---

### Requisito 9: Configurações de SMTP Internas com Teste em Tempo Real

**User Story:** Como administrador, quero configurar o servidor de e-mail diretamente pelo painel administrativo com teste completo em tempo real via AJAX, para que eu possa garantir que todos os e-mails do sistema sejam enviados corretamente sem necessidade de conhecimento técnico.

#### Critérios de Aceitação

1. THE SMTP_Config SHALL disponibilizar um formulário completo e intuitivo no Painel_Admin com todos os campos necessários: host SMTP, porta, protocolo de segurança (TLS/SSL/none), usuário, senha, e-mail remetente, nome remetente e configurações avançadas de timeout.
2. WHEN o Admin clica em "Testar Configuração", THE SMTP_Config SHALL enviar um e-mail de teste personalizado para o endereço do Admin via AJAX em tempo real e exibir o resultado detalhado via Toastify sem recarregar a página, incluindo tempo de resposta do servidor.
3. WHEN o Admin salva as configurações de SMTP, THE SMTP_Config SHALL persistir todos os valores no banco de dados de forma segura e atualizar dinamicamente as configurações de e-mail do Laravel em tempo de execução sem necessidade de reiniciar o servidor.
4. IF a conexão com o servidor SMTP falhar durante o teste, THEN THE SMTP_Config SHALL exibir uma mensagem de erro técnica e descritiva via Toastify incluindo o código de falha específico, sugestões de correção e links para documentação.
5. THE SMTP_Config SHALL armazenar a senha do SMTP de forma criptografada no banco de dados utilizando o sistema de criptografia nativo do Laravel (Crypt::encryptString) para máxima segurança.
6. THE SMTP_Config SHALL incluir templates de configuração pré-definidos para provedores populares (Gmail, Outlook, SendGrid, Mailgun) para facilitar a configuração.
7. THE SMTP_Config SHALL implementar log detalhado de todos os e-mails enviados pelo sistema com status de entrega, timestamps e possíveis erros.
8. THE SMTP_Config SHALL suportar configuração de múltiplos servidores SMTP com failover automático em caso de falha do servidor principal.

---

### Requisito 10: Página de Manutenção Avançada com Controle Granular

**User Story:** Como administrador, quero ativar uma página de manutenção avançada e elegante com controle granular de acesso por IP, para que eu possa realizar atualizações e manutenções sem interromper completamente o acesso administrativo e de usuários autorizados.

#### Critérios de Aceitação

1. THE Manutencao SHALL disponibilizar uma interface completa no Painel_Admin para ativar e desativar o modo de manutenção com toggle visual, configuração de mensagem personalizada, estimativa de retorno e upload de imagem de fundo.
2. WHEN o modo de manutenção está ativo, THE Manutencao SHALL exibir uma página de manutenção elegante e personalizada com a identidade visual da empresa, mensagem configurável, contador regressivo até o retorno estimado e animações CSS atrativas para todos os Visitantes não autorizados.
3. WHILE o modo de manutenção está ativo, THE Manutencao SHALL permitir acesso completo ao Painel_Admin e todas as funcionalidades administrativas exclusivamente para IPs cadastrados na lista de IPs permitidos, mantendo total funcionalidade para administradores.
4. THE Manutencao SHALL retornar o código HTTP 503 (Service Unavailable) com cabeçalho `Retry-After` apropriado para visitantes bloqueados durante a manutenção, informando aos crawlers e ferramentas quando tentar novamente.
5. THE Manutencao SHALL permitir ao Admin configurar e gerenciar uma lista completa de IPs com acesso liberado durante a manutenção diretamente pelo painel, incluindo descrição/label para cada IP e data de expiração opcional.
6. THE Manutencao SHALL suportar ranges de IP (CIDR) para liberar redes inteiras e detecção automática do IP atual do administrador com opção de auto-inclusão.
7. THE Manutencao SHALL registrar todos os acessos durante o modo de manutenção em logs de auditoria para monitoramento de segurança.
8. THE Manutencao SHALL permitir agendamento automático de ativação e desativação do modo de manutenção para janelas de manutenção programadas.

---

### Requisito 11: Páginas de Erro Personalizadas e Elegantes

**User Story:** Como visitante, quero ver páginas de erro personalizadas, informativas e elegantes, para que a experiência seja consistente com o design do site mesmo em situações de erro e eu receba orientações úteis para resolver o problema.

#### Critérios de Aceitação

1. THE Sistema SHALL exibir páginas de erro completamente personalizadas e elegantes para todos os códigos HTTP relevantes: 403 (Acesso Negado), 404 (Página Não Encontrada), 419 (Sessão Expirada), 429 (Muitas Tentativas), 500 (Erro Interno) e 503 (Em Manutenção).
2. THE Sistema SHALL manter rigorosamente o layout, paleta de cores e identidade visual completa do Frontend em todas as páginas de erro para consistência da marca.
3. WHEN o Visitante acessa uma página de erro 404, THE Sistema SHALL exibir um link de retorno para a página inicial, sugestões inteligentes de páginas relevantes baseadas na URL acessada e um campo de busca para encontrar o conteúdo desejado.
4. WHEN ocorre um erro 500 (erro interno do servidor), THE Sistema SHALL registrar automaticamente o erro completo com stack trace nos logs do Laravel para análise técnica e exibir ao Visitante apenas uma mensagem genérica e amigável sem revelar detalhes técnicos sensíveis.
5. THE Sistema SHALL implementar páginas de erro responsivas que funcionem perfeitamente em todos os dispositivos e tamanhos de tela.
6. THE Sistema SHALL incluir animações CSS sutis e ícones apropriados em cada página de erro para melhorar a experiência visual.
7. THE Sistema SHALL implementar redirecionamento inteligente para URLs comuns com erros de digitação (ex: /contato para /contato).

---

### Requisito 12: URL Limpa sem /public e Configuração Apache

**User Story:** Como administrador, quero que a URL do sistema seja completamente limpa e profissional sem `/public`, para que o endereço seja elegante, fácil de lembrar e transmita credibilidade aos visitantes.

#### Critérios de Aceitação

1. THE Sistema SHALL configurar automaticamente o servidor web Apache para que a pasta `public` do Laravel seja tratada como a raiz do documento, eliminando completamente `/public` de todas as URLs do sistema.
2. THE Sistema SHALL incluir um arquivo `.htaccess` otimizado na raiz do projeto com regras de redirecionamento que encaminhem todas as requisições para a pasta `public` de forma transparente e eficiente.
3. THE Sistema SHALL incluir um arquivo `.htaccess` completo e otimizado dentro da pasta `public` com todas as regras de reescrita do Laravel, redirecionamento HTTPS forçado, proteção de arquivos sensíveis e otimizações de performance.
4. THE Instalador SHALL verificar obrigatoriamente se o módulo `mod_rewrite` do Apache está habilitado como parte crítica da verificação de requisitos do sistema, impedindo a instalação se não estiver disponível.
5. THE Sistema SHALL implementar proteção completa contra acesso direto a arquivos sensíveis (`.env`, `storage/installed`, logs) através de regras `.htaccess` específicas.
6. THE Sistema SHALL configurar cabeçalhos de cache otimizados para assets estáticos (CSS, JS, imagens) para melhorar significativamente a performance de carregamento.
7. THE Sistema SHALL implementar compressão Gzip automática para todos os recursos textuais para reduzir o tempo de carregamento.

---

### Requisito 13: Arquitetura Modular Avançada e Escalável

**User Story:** Como desenvolvedor, quero que o sistema seja organizado em uma arquitetura modular avançada e completamente escalável, para que cada funcionalidade possa ser mantida, expandida e reutilizada de forma totalmente isolada e eficiente.

#### Critérios de Aceitação

1. THE Sistema SHALL organizar cada funcionalidade principal em um Módulo completamente independente dentro do diretório `app/Modules/`, contendo suas próprias subpastas organizadas: `Controllers`, `Models`, `Requests`, `Resources`, `Routes`, `Services`, `Policies` e `Views`.
2. THE Sistema SHALL registrar automaticamente as rotas de cada Módulo através de um `ModuleServiceProvider` centralizado e inteligente que descobre dinamicamente novos módulos sem configuração manual.
3. WHEN um novo Módulo é adicionado ao diretório `app/Modules/`, THE Sistema SHALL carregá-lo automaticamente e disponibilizá-lo imediatamente sem necessidade de alterações em arquivos de configuração globais ou reinicialização do servidor.
4. THE Sistema SHALL manter os assets (CSS, JS, imagens) de cada Módulo organizados de forma modular em `resources/modules/{nome_modulo}/` com compilação automática via Vite.
5. THE Sistema SHALL implementar um sistema de dependências entre módulos para garantir carregamento na ordem correta quando necessário.
6. THE Sistema SHALL suportar ativação e desativação dinâmica de módulos através do painel administrativo para máxima flexibilidade.
7. THE Sistema SHALL implementar versionamento de módulos para controle de compatibilidade e atualizações seguras.
8. THE Sistema SHALL gerar automaticamente documentação da API de cada módulo para facilitar desenvolvimento e manutenção.

---

### Requisito 14: Segurança Multicamadas e Proteção Avançada

**User Story:** Como administrador, quero que o sistema implemente múltiplas camadas de segurança robustas e proteção avançada contra todos os tipos de ataques, para que os dados da empresa e dos clientes sejam completamente protegidos contra invasões, injeções e tentativas maliciosas.

#### Critérios de Aceitação

1. THE Sanitizador SHALL aplicar rigorosamente `htmlspecialchars`, `strip_tags` e filtros avançados contra XSS em todos os dados de entrada antes de renderizar nas views, com whitelist de tags HTML permitidas quando necessário.
2. THE Sistema SHALL utilizar exclusivamente o sistema de autorização por políticas (Policies) do Laravel para controlar granularmente o acesso a cada recurso do Painel_Admin, verificando permissões em cada ação.
3. THE Sistema SHALL registrar automaticamente em logs de auditoria detalhados todas as ações administrativas críticas (criação, edição, exclusão de registros) com timestamp preciso, IP de origem, user agent, dados anteriores e novos valores para rastreabilidade completa.
4. THE Sistema SHALL implementar validação rigorosa de tipo MIME real dos arquivos enviados via upload usando `finfo_file()`, não confiando apenas na extensão do arquivo, e verificar assinaturas de arquivo para detectar malware.
5. THE Sistema SHALL definir e aplicar permissões de arquivo restritivas e seguras: `644` para arquivos e `755` para diretórios na pasta `storage`, impedindo execução não autorizada.
6. IF uma requisição AJAX receber um token CSRF inválido ou expirado, THEN THE Sistema SHALL retornar HTTP 419 com resposta JSON estruturada de erro sem expor detalhes sensíveis da sessão, IDs internos ou stack traces.
7. THE Sistema SHALL implementar proteção avançada contra SQL Injection usando exclusivamente prepared statements e ORM, com validação de entrada e escape de caracteres especiais.
8. THE Sistema SHALL implementar rate limiting granular por IP, usuário e tipo de ação para prevenir ataques de força bruta e spam.
9. THE Sistema SHALL criptografar todos os dados sensíveis armazenados no banco de dados usando o sistema de criptografia nativo do Laravel.
10. THE Sistema SHALL implementar monitoramento de segurança em tempo real com alertas automáticos para tentativas de invasão e atividades suspeitas.
11. THE Sistema SHALL forçar HTTPS em produção e implementar HSTS (HTTP Strict Transport Security) para prevenir ataques man-in-the-middle.
12. THE Sistema SHALL implementar Content Security Policy (CSP) rigorosa para prevenir ataques XSS e injeção de código malicioso.

---

### Requisito 15: Preloader Personalizado e Animações

**User Story:** Como visitante, quero ver um preloader elegante e personalizado durante o carregamento das páginas, para que a experiência seja profissional e eu tenha feedback visual do progresso de carregamento.

#### Critérios de Aceitação

1. THE Preloader SHALL exibir o logotipo da empresa HomeMechanic de forma elegante e centralizada durante o carregamento inicial de todas as páginas do sistema.
2. THE Preloader SHALL implementar uma barra de progresso animada e suave que reflita o progresso real de carregamento dos recursos da página.
3. THE Preloader SHALL utilizar a paleta de cores da marca (laranja, preto, grafite) com animações CSS modernas e profissionais.
4. THE Preloader SHALL desaparecer automaticamente com animação de fade-out suave quando o carregamento da página estiver 100% completo.
5. THE Preloader SHALL ser responsivo e funcionar perfeitamente em todos os dispositivos e tamanhos de tela.
6. THE Preloader SHALL incluir um indicador de progresso percentual para carregamentos mais longos.
7. THE Sistema SHALL implementar preloader específico para operações AJAX longas no painel administrativo.

---

### Requisito 16: Configurações Avançadas do Sistema

**User Story:** Como administrador, quero ter controle completo sobre todas as configurações do sistema através de uma interface intuitiva, para que eu possa personalizar completamente o comportamento e aparência do sistema.

#### Critérios de Aceitação

1. THE Sistema SHALL disponibilizar uma seção completa de configurações gerais no Painel_Admin incluindo: nome do site, logotipo, favicon, descrição, informações de contato, redes sociais e configurações de SEO globais.
2. THE Sistema SHALL permitir upload e gerenciamento de logotipos em múltiplos formatos (horizontal, vertical, favicon) com preview em tempo real.
3. THE Sistema SHALL permitir configuração completa das cores da paleta através de color pickers com preview instantâneo em todo o sistema.
4. THE Sistema SHALL incluir configurações avançadas de performance: cache, compressão, otimização de imagens e lazy loading.
5. THE Sistema SHALL permitir configuração de integrações com serviços externos: Google Analytics, Google Tag Manager, Facebook Pixel, reCAPTCHA.
6. THE Sistema SHALL incluir configurações de backup automático com agendamento e armazenamento em nuvem.
7. THE Sistema SHALL permitir configuração de notificações por e-mail para administradores sobre eventos importantes do sistema.

---

### Requisito 17: Sistema de Backup e Restauração

**User Story:** Como administrador, quero um sistema completo de backup e restauração automática, para que eu possa proteger todos os dados do sistema e restaurá-los rapidamente em caso de problemas.

#### Critérios de Aceitação

1. THE Sistema SHALL implementar backup automático completo do banco de dados com agendamento configurável (diário, semanal, mensal).
2. THE Sistema SHALL implementar backup automático de todos os arquivos enviados (uploads, imagens, documentos) com sincronização incremental.
3. THE Sistema SHALL permitir backup manual sob demanda através do painel administrativo com progresso em tempo real.
4. THE Sistema SHALL suportar armazenamento de backups em múltiplos destinos: local, FTP, Amazon S3, Google Drive, Dropbox.
5. THE Sistema SHALL implementar restauração completa do sistema a partir de backups com interface guiada passo a passo.
6. THE Sistema SHALL manter histórico de backups com limpeza automática de backups antigos conforme política configurada.
7. THE Sistema SHALL notificar administradores sobre status de backups (sucesso, falha, espaço insuficiente) via e-mail.

---

### Requisito 18: Otimização de Performance e SEO

**User Story:** Como administrador, quero que o sistema seja otimizado para máxima performance e SEO, para que o site carregue rapidamente e tenha excelente posicionamento nos mecanismos de busca.

#### Critérios de Aceitação

1. THE Sistema SHALL implementar cache inteligente em múltiplas camadas: cache de views, cache de queries, cache de assets e cache de páginas completas.
2. THE Sistema SHALL otimizar automaticamente todas as imagens enviadas com compressão sem perda de qualidade e conversão para formatos modernos (WebP).
3. THE Sistema SHALL implementar lazy loading inteligente para imagens, vídeos e conteúdo abaixo da dobra.
4. THE Sistema SHALL gerar automaticamente sitemap XML dinâmico e atualizado para todos os conteúdos públicos.
5. THE Sistema SHALL implementar structured data (JSON-LD) completo para empresa, serviços, posts do blog e avaliações.
6. THE Sistema SHALL otimizar automaticamente meta tags, títulos e descrições para SEO baseado no conteúdo.
7. THE Sistema SHALL implementar minificação automática de CSS, JavaScript e HTML em produção.
8. THE Sistema SHALL incluir ferramentas de análise de performance com relatórios detalhados de Core Web Vitals.
