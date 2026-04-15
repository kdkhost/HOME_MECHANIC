# ❓ FAQ - Perguntas Frequentes

Respostas para as dúvidas mais comuns sobre o HomeMechanic System.

## 📋 Índice

- [Instalação e Configuração](#instalação-e-configuração)
- [Uso do Sistema](#uso-do-sistema)
- [Problemas Técnicos](#problemas-técnicos)
- [Personalização](#personalização)
- [Segurança](#segurança)
- [Performance](#performance)
- [Suporte](#suporte)

## 🚀 Instalação e Configuração

### P: Quais são os requisitos mínimos do servidor?
**R:** O HomeMechanic System requer:
- PHP 8.4 ou superior
- MySQL 8.0+ ou MariaDB 10.6+
- Apache com mod_rewrite ou Nginx
- Mínimo 512MB RAM
- 2GB de espaço em disco

### P: Como instalo o sistema em hospedagem compartilhada?
**R:** Para hospedagem compartilhada:
1. Faça upload dos arquivos via FTP
2. Configure as permissões: `chmod 755 storage/` e `chmod 755 bootstrap/cache/`
3. Acesse `seu-dominio.com/install`
4. Siga o assistente de instalação

### P: O sistema funciona sem SSL/HTTPS?
**R:** Sim, mas **recomendamos fortemente** usar HTTPS por segurança. O sistema detecta automaticamente se está rodando em HTTPS e ajusta as configurações.

### P: Como configuro o envio de emails?
**R:** Vá para **Configurações → SMTP** no painel admin:
1. Configure servidor, porta, usuário e senha
2. Use o botão "Testar Configuração"
3. Para Gmail, use senha de app, não a senha normal

### P: Posso usar o sistema em subdiretório?
**R:** Sim, mas você precisa:
1. Ajustar o `APP_URL` no arquivo `.env`
2. Modificar o `.htaccess` se necessário
3. Verificar se os links estão funcionando corretamente

## 💻 Uso do Sistema

### P: Como adiciono novos serviços?
**R:** No painel admin:
1. Vá para **Serviços → Adicionar Novo**
2. Preencha título, descrição e conteúdo
3. Faça upload da imagem de capa
4. Marque como "Destaque" se quiser na página inicial
5. Clique em "Salvar"

### P: Como organizo a galeria de fotos?
**R:** A galeria usa categorias:
1. Primeiro crie categorias em **Galeria → Categorias**
2. Depois adicione fotos em **Galeria → Adicionar Foto**
3. Use drag & drop para reordenar
4. Fotos aparecem automaticamente no site público

### P: Como escrevo posts no blog?
**R:** Use o editor WYSIWYG:
1. Vá para **Blog → Novo Post**
2. Escreva o título e conteúdo
3. Adicione tags separadas por vírgula
4. Configure SEO (meta título e descrição)
5. Escolha "Publicado" ou "Rascunho"

### P: Como respondo mensagens de contato?
**R:** As mensagens ficam em **Mensagens**:
1. Clique na mensagem para ver detalhes
2. Use as informações de contato para responder
3. Marque como "Lida" após responder
4. Configure SMTP para receber notificações

### P: Posso agendar posts para o futuro?
**R:** Sim! No formulário do post:
1. Altere a "Data de Publicação" para uma data futura
2. Mantenha o status como "Publicado"
3. O post será publicado automaticamente na data escolhida

## 🔧 Problemas Técnicos

### P: Erro 500 - Internal Server Error
**R:** Verifique:
1. Permissões: `chmod 755 storage/` e `chmod 755 bootstrap/cache/`
2. Logs: `storage/logs/laravel.log`
3. Arquivo `.env` configurado corretamente
4. Limpe o cache: acesse **Configurações → Sistema → Limpar Cache**

### P: Erro "CSRF token mismatch"
**R:** Isso acontece quando:
1. Sessão expirou - recarregue a página
2. Cache de navegador - limpe o cache
3. Configuração de sessão - verifique `SESSION_DRIVER` no `.env`

### P: Upload de arquivos não funciona
**R:** Verifique:
1. Permissões da pasta `storage/`: `chmod 755 storage/`
2. Configurações PHP: `upload_max_filesize` e `post_max_size`
3. Tamanho do arquivo (máximo 10MB para imagens, 100MB para vídeos)
4. Formato suportado (JPEG, PNG, WebP, GIF, MP4, WebM)

### P: Páginas não carregam (404)
**R:** Problema com URL rewriting:
1. Verifique se mod_rewrite está habilitado
2. Confirme se `.htaccess` existe na pasta `public/`
3. Para Nginx, configure as regras de rewrite

### P: Site lento para carregar
**R:** Otimizações:
1. Comprima imagens antes do upload
2. Use cache do navegador (já configurado)
3. Otimize banco de dados
4. Considere usar CDN para imagens

## 🎨 Personalização

### P: Como altero as cores do sistema?
**R:** Edite o arquivo `resources/sass/admin-custom.scss`:
```scss
$primary: #SUA_COR_AQUI;
$dark: #SUA_COR_ESCURA;
```
Depois execute `npm run build`

### P: Como troco o logo?
**R:** Vá para **Configurações → Gerais**:
1. Faça upload do novo logo (recomendado: 200x80px PNG)
2. Salve as configurações
3. O logo aparecerá automaticamente no site e painel

### P: Posso personalizar o layout?
**R:** Sim, você pode:
1. Editar arquivos em `resources/views/layouts/`
2. Modificar CSS em `resources/sass/`
3. Adicionar JavaScript personalizado
4. Criar novos módulos se necessário

### P: Como adiciono páginas personalizadas?
**R:** Crie um novo módulo ou:
1. Adicione rotas em `routes/web.php`
2. Crie controller personalizado
3. Desenvolva as views necessárias

## 🔒 Segurança

### P: Como mantenho o sistema seguro?
**R:** Práticas recomendadas:
1. Mantenha senhas fortes (mínimo 12 caracteres)
2. Use HTTPS sempre
3. Faça backups regulares
4. Monitore logs de auditoria
5. Mantenha o sistema atualizado

### P: Posso ter múltiplos usuários admin?
**R:** Atualmente o sistema suporta um usuário admin. Para múltiplos usuários, você pode:
1. Modificar o sistema de roles
2. Implementar diferentes níveis de acesso
3. Usar o sistema de políticas do Laravel

### P: Como faço backup do sistema?
**R:** Backup completo inclui:
1. **Banco de dados**: `mysqldump -u usuario -p homemechanic > backup.sql`
2. **Arquivos**: Comprima toda a pasta do projeto
3. **Uploads**: Especial atenção para `storage/app/public/uploads/`

### P: O que fazer se esquecer a senha admin?
**R:** Via banco de dados:
1. Acesse phpMyAdmin ou similar
2. Vá para tabela `users`
3. Gere nova senha: `bcrypt('nova_senha', ['rounds' => 12])`
4. Atualize o campo `password`

## ⚡ Performance

### P: Como otimizo o sistema para produção?
**R:** Execute estes comandos:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --no-dev --optimize-autoloader
```

### P: Posso usar CDN para imagens?
**R:** Sim, você pode:
1. Configurar CDN para pasta `storage/app/public/uploads/`
2. Modificar helpers de URL se necessário
3. Usar serviços como CloudFlare, AWS CloudFront

### P: Como monitoro a performance?
**R:** Use ferramentas como:
1. Laravel Telescope (desenvolvimento)
2. New Relic ou similar (produção)
3. Google PageSpeed Insights
4. Logs do servidor web

## 📞 Suporte

### P: Onde encontro ajuda técnica?
**R:** Canais de suporte:
- **Email**: suporte@homemechanic.com.br
- **Documentação**: Esta documentação completa
- **GitHub**: Issues para bugs e sugestões
- **Chat**: Suporte online (horário comercial)

### P: Como reporto um bug?
**R:** Inclua sempre:
1. Versão do sistema
2. Versão do PHP
3. Descrição detalhada do problema
4. Passos para reproduzir
5. Screenshots se aplicável
6. Logs de erro relevantes

### P: Posso contratar suporte personalizado?
**R:** Sim, oferecemos:
- Instalação e configuração
- Personalização visual
- Desenvolvimento de funcionalidades
- Treinamento de usuários
- Suporte técnico dedicado

### P: O sistema tem garantia?
**R:** O software é fornecido "como está", mas oferecemos:
- Suporte técnico gratuito por email
- Correções de bugs críticos
- Atualizações de segurança
- Documentação completa

### P: Posso revender o sistema?
**R:** Consulte a licença MIT para detalhes sobre redistribuição e uso comercial.

## 🔄 Atualizações

### P: Como atualizo o sistema?
**R:** Para atualizações:
1. **Sempre faça backup primeiro**
2. Baixe a nova versão
3. Substitua arquivos (exceto `.env` e `storage/`)
4. Execute `composer install`
5. Execute `php artisan migrate`
6. Limpe caches

### P: Perco dados ao atualizar?
**R:** Não, se seguir o processo correto:
- Dados ficam no banco de dados
- Uploads ficam em `storage/app/public/uploads/`
- Configurações ficam no `.env`
- **Sempre faça backup antes**

---

## 💡 Dicas Extras

### Atalhos de Teclado
- **Ctrl/Cmd + K**: Buscar na documentação
- **Alt + ←/→**: Navegar entre documentos
- **Ctrl/Cmd + P**: Imprimir documentação

### Melhores Práticas
1. **Imagens**: Comprima antes do upload
2. **SEO**: Preencha sempre meta título e descrição
3. **Backup**: Faça semanalmente
4. **Senhas**: Troque a cada 6 meses
5. **Logs**: Monitore regularmente

### Recursos Úteis
- [Documentação Laravel](https://laravel.com/docs)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.0/)
- [AdminLTE 4 Docs](https://adminlte.io/docs/4.0/)

---

**Não encontrou sua dúvida?** Entre em contato conosco em suporte@homemechanic.com.br

**Última atualização**: 15 de Abril de 2026