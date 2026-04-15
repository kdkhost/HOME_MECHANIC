# 👤 Manual Completo do Usuário - HomeMechanic System

Este manual fornece instruções detalhadas sobre como usar todas as funcionalidades do HomeMechanic System.

## 📋 Índice

1. [Primeiro Acesso](#primeiro-acesso)
2. [Dashboard Principal](#dashboard-principal)
3. [Gerenciamento de Serviços](#gerenciamento-de-serviços)
4. [Sistema de Galeria](#sistema-de-galeria)
5. [Blog e Artigos](#blog-e-artigos)
6. [Depoimentos de Clientes](#depoimentos-de-clientes)
7. [Mensagens de Contato](#mensagens-de-contato)
8. [Configurações do Sistema](#configurações-do-sistema)
9. [Modo de Manutenção](#modo-de-manutenção)
10. [Dicas e Truques](#dicas-e-truques)

## 🚀 Primeiro Acesso

### Fazendo Login
1. Acesse `http://seu-dominio.com/admin/login`
2. Digite seu email e senha
3. Clique em "Entrar"

> **Nota**: Após 5 tentativas incorretas, o sistema bloqueará seu IP por 15 minutos por segurança.

### Recuperação de Senha
Se você esqueceu sua senha:
1. Clique em "Esqueci minha senha" na tela de login
2. Digite seu email
3. Verifique sua caixa de entrada
4. Siga as instruções no email recebido

## 📊 Dashboard Principal

O dashboard é a primeira tela que você vê após fazer login. Ele contém:

### Cards de Resumo
- **Total de Serviços**: Quantidade de serviços cadastrados
- **Posts do Blog**: Número de artigos publicados
- **Fotos na Galeria**: Total de imagens na galeria
- **Mensagens não Lidas**: Contatos pendentes de resposta

### Gráficos e Estatísticas
- Visitantes mensais
- Serviços mais visualizados
- Posts mais lidos
- Mensagens por período

### Ações Rápidas
- Adicionar novo serviço
- Criar post no blog
- Upload de fotos
- Ver mensagens pendentes

## 🔧 Gerenciamento de Serviços

### Adicionando um Novo Serviço

1. **Acesse**: Menu lateral → Serviços → Adicionar Novo
2. **Preencha os campos**:
   - **Título**: Nome do serviço (ex: "Alinhamento e Balanceamento")
   - **Slug**: URL amigável (gerado automaticamente)
   - **Descrição**: Resumo do serviço (máximo 255 caracteres)
   - **Conteúdo**: Descrição detalhada com editor WYSIWYG
   - **Ícone**: Classe do ícone Bootstrap (ex: "bi-tools")
   - **Imagem de Capa**: Upload da imagem principal
   - **Destaque**: Marque se deve aparecer na página inicial
   - **Ordem**: Número para ordenação (menor aparece primeiro)
   - **Status**: Ativo/Inativo

3. **Clique em "Salvar"**

### Editando um Serviço Existente

1. Vá para Serviços → Listar Todos
2. Clique no ícone de edição (lápis) ao lado do serviço
3. Modifique os campos desejados
4. Clique em "Atualizar"

### Excluindo um Serviço

1. Na lista de serviços, clique no ícone de lixeira
2. Confirme a exclusão no popup
3. O serviço será removido permanentemente

> **⚠️ Atenção**: A exclusão é permanente e não pode ser desfeita.

### Reordenando Serviços

1. Na lista de serviços, use os números na coluna "Ordem"
2. Digite a nova ordem desejada
3. Clique em "Salvar Ordem"
4. Os serviços serão reordenados automaticamente

## 📸 Sistema de Galeria

### Gerenciando Categorias

#### Criando uma Nova Categoria
1. Acesse Galeria → Categorias
2. Clique em "Nova Categoria"
3. Preencha:
   - **Nome**: Nome da categoria (ex: "Suspensão")
   - **Slug**: URL amigável
   - **Ordem**: Posição na listagem
4. Salve a categoria

#### Editando/Excluindo Categorias
- Use os ícones de ação na lista de categorias
- Ao excluir uma categoria, todas as fotos dela serão movidas para "Sem Categoria"

### Adicionando Fotos

#### Upload Individual
1. Vá para Galeria → Adicionar Foto
2. Selecione a categoria
3. Faça upload da imagem (arrastar e soltar ou clicar)
4. Preencha:
   - **Título**: Nome da foto
   - **Descrição**: Detalhes sobre o trabalho
   - **Ordem**: Posição na galeria
5. Salve a foto

#### Upload Múltiplo
1. Acesse Galeria → Upload Múltiplo
2. Selecione a categoria de destino
3. Arraste múltiplas imagens para a área de upload
4. Aguarde o processamento
5. Edite os títulos e descrições individualmente

### Organizando a Galeria

#### Reordenação por Drag & Drop
1. Na visualização em grade da galeria
2. Arraste as fotos para a posição desejada
3. A nova ordem é salva automaticamente

#### Filtros e Busca
- Use o filtro por categoria no topo da página
- Digite no campo de busca para encontrar fotos específicas
- Ordene por data, nome ou popularidade

## ✍️ Blog e Artigos

### Criando um Novo Post

1. **Acesse**: Blog → Novo Post
2. **Informações Básicas**:
   - **Título**: Título do artigo
   - **Slug**: URL do post (gerado automaticamente)
   - **Categoria**: Selecione uma categoria existente
   - **Tags**: Adicione tags separadas por vírgula
   - **Resumo**: Texto que aparece na listagem
   - **Imagem de Capa**: Upload da imagem principal

3. **Conteúdo**:
   - Use o editor WYSIWYG para escrever o artigo
   - Adicione imagens, vídeos e formatação
   - Use os botões da barra de ferramentas

4. **SEO**:
   - **Meta Título**: Título para motores de busca
   - **Meta Descrição**: Descrição para Google
   - **Imagem OG**: Imagem para redes sociais

5. **Publicação**:
   - **Status**: Rascunho ou Publicado
   - **Data de Publicação**: Agendar para o futuro se desejar
   - Clique em "Publicar" ou "Salvar Rascunho"

### Gerenciando Categorias do Blog

1. Vá para Blog → Categorias
2. Adicione novas categorias com nome e descrição
3. Organize por ordem de importância
4. Use categorias específicas como "Tuning", "Manutenção", "Dicas"

### Sistema de Tags

- Tags ajudam na organização e busca
- Use tags descritivas como "motor", "suspensão", "freios"
- Evite criar muitas tags similares
- Tags são criadas automaticamente ao digitar

### Agendamento de Posts

1. No formulário de criação/edição
2. Altere a "Data de Publicação" para uma data futura
3. Mantenha o status como "Publicado"
4. O post será publicado automaticamente na data escolhida

## 💬 Depoimentos de Clientes

### Adicionando um Depoimento

1. Acesse Depoimentos → Novo Depoimento
2. Preencha:
   - **Nome do Cliente**: Nome completo
   - **Cargo/Profissão**: Opcional
   - **Foto**: Upload da foto do cliente
   - **Depoimento**: Texto do depoimento
   - **Avaliação**: Estrelas de 1 a 5
   - **Status**: Ativo/Inativo
   - **Ordem**: Para controlar a sequência

3. Salve o depoimento

### Moderação de Depoimentos

- Todos os depoimentos podem ser ativados/desativados
- Use a ordem para controlar quais aparecem primeiro
- Depoimentos inativos não aparecem no site público

### Carrossel de Depoimentos

- No site público, os depoimentos aparecem em carrossel
- Máximo de 6 depoimentos ativos são exibidos
- Rotação automática a cada 5 segundos

## 📧 Mensagens de Contato

### Visualizando Mensagens

1. Acesse Mensagens no menu lateral
2. Veja a lista com:
   - **Status**: Lida/Não lida
   - **Nome**: Quem enviou
   - **Assunto**: Título da mensagem
   - **Data**: Quando foi enviada

### Respondendo Mensagens

1. Clique na mensagem para abrir
2. Leia o conteúdo completo
3. Use as informações de contato para responder
4. Marque como "Lida" após responder

### Filtros e Organização

- **Filtrar por Status**: Apenas não lidas, apenas lidas, todas
- **Buscar**: Por nome, email ou assunto
- **Ordenar**: Por data (mais recentes primeiro)

### Configurações de Email

Para receber notificações de novas mensagens:
1. Vá para Configurações → SMTP
2. Configure seu servidor de email
3. Teste o envio
4. Ative as notificações automáticas

## ⚙️ Configurações do Sistema

### Configurações Gerais

#### Informações da Empresa
1. Acesse Configurações → Gerais
2. Configure:
   - **Nome do Site**: Nome da sua oficina
   - **Descrição**: Breve descrição da empresa
   - **Logo**: Upload do logotipo
   - **Favicon**: Ícone que aparece na aba do navegador

#### Informações de Contato
- **Endereço**: Endereço completo da oficina
- **Telefone**: Número principal
- **Email**: Email de contato
- **Horário de Funcionamento**: Dias e horários

#### Redes Sociais
- **Facebook**: URL da página
- **Instagram**: URL do perfil
- **YouTube**: URL do canal
- **WhatsApp**: Número para contato direto

### Configurações de SEO

#### Meta Tags Globais
- **Título Padrão**: Aparece na aba do navegador
- **Descrição Padrão**: Para motores de busca
- **Palavras-chave**: Separadas por vírgula
- **Imagem OG Padrão**: Para redes sociais

#### Google Analytics
1. Crie uma conta no Google Analytics
2. Obtenha o código de acompanhamento
3. Cole no campo "Google Analytics ID"
4. Salve as configurações

### Configurações de SMTP

#### Configurando Email
1. Vá para Configurações → SMTP
2. Preencha:
   - **Servidor SMTP**: Ex: smtp.gmail.com
   - **Porta**: 587 (TLS) ou 465 (SSL)
   - **Usuário**: Seu email completo
   - **Senha**: Senha do email ou senha de app
   - **Criptografia**: TLS ou SSL
   - **Email Remetente**: Email que aparece como remetente
   - **Nome Remetente**: Nome que aparece como remetente

3. **Teste a Configuração**:
   - Clique em "Testar Configuração"
   - Digite um email para teste
   - Verifique se o email foi recebido

#### Provedores Comuns

**Gmail**:
- Servidor: smtp.gmail.com
- Porta: 587
- Criptografia: TLS
- Nota: Use senha de app, não a senha normal

**Outlook/Hotmail**:
- Servidor: smtp-mail.outlook.com
- Porta: 587
- Criptografia: STARTTLS

**Yahoo**:
- Servidor: smtp.mail.yahoo.com
- Porta: 587 ou 465
- Criptografia: TLS ou SSL

## 🔧 Modo de Manutenção

### Ativando o Modo de Manutenção

1. Acesse Configurações → Manutenção
2. Ative o "Modo de Manutenção"
3. Configure:
   - **Mensagem**: Texto que aparece para visitantes
   - **Estimativa de Retorno**: Quando o site voltará
   - **IPs Permitidos**: IPs que podem acessar normalmente

### Gerenciando IPs Permitidos

#### Adicionando seu IP
1. Na seção "IPs Permitidos"
2. Clique em "Adicionar IP"
3. Digite seu IP atual (mostrado na tela)
4. Adicione um rótulo (ex: "Escritório")
5. Salve a configuração

#### Removendo IPs
- Use o ícone de lixeira ao lado do IP
- Confirme a remoção

### Página de Manutenção

Quando ativo, visitantes verão:
- Mensagem personalizada
- Estimativa de retorno
- Design consistente com o site
- Código HTTP 503 (Service Unavailable)

## 💡 Dicas e Truques

### Otimização de Imagens

#### Tamanhos Recomendados
- **Logo**: 200x80px (PNG com fundo transparente)
- **Favicon**: 32x32px (ICO ou PNG)
- **Serviços**: 800x600px (JPG ou WebP)
- **Galeria**: 1200x800px (JPG ou WebP)
- **Blog**: 1200x630px (JPG ou WebP)
- **Depoimentos**: 150x150px (JPG circular)

#### Formatos Suportados
- **Imagens**: JPEG, PNG, WebP, GIF
- **Vídeos**: MP4, WebM
- **Tamanho Máximo**: 10MB para imagens, 100MB para vídeos

### SEO - Otimização para Buscadores

#### Títulos Eficazes
- Use palavras-chave relevantes
- Mantenha entre 50-60 caracteres
- Seja específico e descritivo

#### Descrições Meta
- Resuma o conteúdo em 150-160 caracteres
- Inclua call-to-action
- Use palavras-chave naturalmente

#### URLs Amigáveis
- Use hífens para separar palavras
- Evite caracteres especiais
- Mantenha curtas e descritivas

### Performance e Velocidade

#### Otimização de Imagens
- Comprima imagens antes do upload
- Use WebP quando possível
- Evite imagens muito grandes

#### Cache do Navegador
- O sistema já configura cache automático
- Imagens ficam em cache por 1 ano
- CSS e JS por 1 mês

### Backup e Segurança

#### Backup Regular
- Faça backup do banco de dados semanalmente
- Backup dos arquivos mensalmente
- Teste a restauração periodicamente

#### Senhas Seguras
- Use senhas com pelo menos 12 caracteres
- Combine letras, números e símbolos
- Troque a senha a cada 6 meses

#### Monitoramento
- Verifique os logs de auditoria regularmente
- Monitore tentativas de login falhadas
- Mantenha o sistema sempre atualizado

### Suporte e Ajuda

#### Logs do Sistema
- Acesse via FTP: `storage/logs/laravel.log`
- Contém erros e informações de debug
- Útil para diagnosticar problemas

#### Limpeza de Cache
Se algo não está funcionando:
1. Vá para Configurações → Sistema
2. Clique em "Limpar Cache"
3. Aguarde a confirmação

#### Contato com Suporte
- Email: suporte@homemechanic.com.br
- Inclua sempre:
  - URL do seu site
  - Descrição detalhada do problema
  - Screenshots se possível
  - Logs de erro relevantes

---

**Última atualização**: 15 de Abril de 2026  
**Versão**: 1.0.0

> 💡 **Dica**: Mantenha este manual sempre à mão e não hesite em consultar a documentação completa em caso de dúvidas!