# Configuração SMTP - Sistema de E-mail

Este guia explica como configurar o sistema de e-mail SMTP no HomeMechanic System.

## Visão Geral

O sistema utiliza SMTP para envio de e-mails, incluindo:
- Formulários de contato
- Notificações administrativas
- E-mails de recuperação de senha
- Alertas do sistema

## Acessando as Configurações

1. Faça login no painel administrativo
2. Acesse **Configurações > SMTP**
3. Preencha os campos necessários
4. Teste a configuração antes de salvar

## Configurações por Provedor

### Gmail

**Configurações recomendadas:**
- **Servidor SMTP:** `smtp.gmail.com`
- **Porta:** `587`
- **Criptografia:** `TLS`
- **Usuário:** seu-email@gmail.com
- **Senha:** senha do app (não a senha da conta)

**Passos para configurar:**

1. **Ativar autenticação de dois fatores** na sua conta Google
2. **Gerar senha de app:**
   - Acesse [myaccount.google.com](https://myaccount.google.com)
   - Vá em "Segurança" > "Senhas de app"
   - Gere uma nova senha para "E-mail"
   - Use esta senha no campo "Senha SMTP"

### Outlook/Hotmail

**Configurações recomendadas:**
- **Servidor SMTP:** `smtp-mail.outlook.com`
- **Porta:** `587`
- **Criptografia:** `STARTTLS`
- **Usuário:** seu-email@outlook.com
- **Senha:** senha da sua conta

### Yahoo Mail

**Configurações recomendadas:**
- **Servidor SMTP:** `smtp.mail.yahoo.com`
- **Porta:** `587` ou `465`
- **Criptografia:** `TLS` (porta 587) ou `SSL` (porta 465)
- **Usuário:** seu-email@yahoo.com
- **Senha:** senha de app

### Provedores Brasileiros

#### UOL
- **Servidor SMTP:** `smtps.uol.com.br`
- **Porta:** `587`
- **Criptografia:** `TLS`

#### Terra
- **Servidor SMTP:** `smtp.terra.com.br`
- **Porta:** `587`
- **Criptografia:** `TLS`

#### Locaweb
- **Servidor SMTP:** `email-ssl.com.br`
- **Porta:** `465`
- **Criptografia:** `SSL`

### Serviços Profissionais

#### Amazon SES
- **Servidor SMTP:** `email-smtp.us-east-1.amazonaws.com`
- **Porta:** `587`
- **Criptografia:** `TLS`
- **Usuário:** Chave de acesso SMTP
- **Senha:** Senha SMTP

#### SendGrid
- **Servidor SMTP:** `smtp.sendgrid.net`
- **Porta:** `587`
- **Criptografia:** `TLS`
- **Usuário:** `apikey`
- **Senha:** Sua API Key do SendGrid

#### Mailgun
- **Servidor SMTP:** `smtp.mailgun.org`
- **Porta:** `587`
- **Criptografia:** `TLS`
- **Usuário:** Usuário SMTP do Mailgun
- **Senha:** Senha SMTP do Mailgun

## Configuração Passo a Passo

### 1. Acessar Configurações SMTP

No painel admin:
1. Clique em **Configurações** no menu lateral
2. Selecione **SMTP** no submenu
3. Você verá o formulário de configuração

### 2. Preencher Campos Obrigatórios

**Campos principais:**
- **Servidor SMTP:** Endereço do servidor (ex: smtp.gmail.com)
- **Porta:** Porta de conexão (587, 465, 25)
- **Criptografia:** Tipo de segurança (TLS, SSL, STARTTLS)
- **Usuário:** Seu e-mail completo
- **Senha:** Senha ou senha de app
- **Nome do Remetente:** Nome que aparecerá nos e-mails
- **E-mail do Remetente:** E-mail que aparecerá como remetente

### 3. Configurações Avançadas

**Timeout:** Tempo limite para conexão (padrão: 30 segundos)
**Autenticação:** Sempre deixar ativado para SMTP moderno
**Verificar SSL:** Recomendado manter ativado para segurança

### 4. Testar Configuração

1. Preencha todos os campos
2. Clique em **"Testar Configuração"**
3. Digite um e-mail de teste
4. Clique em **"Enviar Teste"**
5. Verifique se o e-mail foi recebido

### 5. Salvar Configurações

Após o teste bem-sucedido:
1. Clique em **"Salvar Configurações"**
2. As configurações serão criptografadas e armazenadas
3. O sistema estará pronto para enviar e-mails

## Solução de Problemas

### Erro: "Connection refused"

**Possíveis causas:**
- Servidor SMTP incorreto
- Porta bloqueada pelo firewall
- Provedor bloqueando conexões

**Soluções:**
1. Verificar servidor e porta
2. Testar com telnet: `telnet smtp.gmail.com 587`
3. Contatar provedor de hospedagem

### Erro: "Authentication failed"

**Possíveis causas:**
- Usuário ou senha incorretos
- Autenticação de dois fatores não configurada
- Conta bloqueada por segurança

**Soluções:**
1. Verificar credenciais
2. Usar senha de app (Gmail, Yahoo)
3. Verificar se a conta não está bloqueada

### Erro: "Certificate verification failed"

**Possíveis causas:**
- Certificado SSL inválido
- Configuração de criptografia incorreta

**Soluções:**
1. Tentar com criptografia diferente
2. Desativar temporariamente verificação SSL
3. Atualizar certificados do servidor

### E-mails Vão para Spam

**Possíveis causas:**
- Falta de registros SPF/DKIM
- Reputação do IP baixa
- Conteúdo suspeito

**Soluções:**
1. Configurar registros DNS (SPF, DKIM, DMARC)
2. Usar serviço profissional (SendGrid, Amazon SES)
3. Evitar palavras que ativam filtros de spam

## Configurações DNS Recomendadas

### Registro SPF
```
v=spf1 include:_spf.google.com ~all
```

### Registro DKIM
Configure através do seu provedor de e-mail

### Registro DMARC
```
v=DMARC1; p=quarantine; rua=mailto:dmarc@seudominio.com
```

## Monitoramento

### Logs de E-mail

Os logs de envio ficam em:
- **Painel Admin:** Configurações > Logs de E-mail
- **Arquivo:** `storage/logs/laravel.log`

### Métricas Importantes

- Taxa de entrega
- E-mails rejeitados
- Tempo de resposta SMTP
- Erros de autenticação

## Backup das Configurações

As configurações SMTP são armazenadas criptografadas no banco de dados. Para backup:

1. **Via painel admin:** Configurações > Backup
2. **Via banco de dados:**
   ```sql
   SELECT * FROM settings WHERE key LIKE 'smtp_%';
   ```

## Segurança

### Boas Práticas

1. **Use sempre criptografia** (TLS/SSL)
2. **Senhas de app** em vez de senhas principais
3. **Monitore logs** regularmente
4. **Limite taxa de envio** para evitar spam
5. **Mantenha credenciais seguras**

### Configurações de Segurança

- **Rate Limiting:** Máximo 100 e-mails/hora por padrão
- **Validação de destinatário:** Verificar formato de e-mail
- **Sanitização:** Remover scripts maliciosos do conteúdo
- **Logs de auditoria:** Registrar todos os envios

## Configuração para Produção

### Recomendações

1. **Use serviço profissional** (não Gmail pessoal)
2. **Configure domínio próprio** para melhor reputação
3. **Implemente filas** para envios em massa
4. **Configure monitoramento** de falhas
5. **Teste regularmente** o funcionamento

### Exemplo de Configuração Profissional

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.seudominio.com
MAIL_PORT=587
MAIL_USERNAME=noreply@seudominio.com
MAIL_PASSWORD=senha_segura
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seudominio.com
MAIL_FROM_NAME="HomeMechanic System"
```

## Suporte

Para problemas específicos de configuração SMTP:

- **E-mail:** suporte@homemechanic.com.br
- **Documentação:** Consulte este guia
- **Logs:** Sempre inclua logs de erro ao solicitar suporte

---

**Última atualização:** {{ date('d/m/Y') }}
**Versão:** 1.0.0