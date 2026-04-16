# HomeMechanic System - CloudLinux + LiteSpeed + Imunify360

## 🌟 Configuração Otimizada

O HomeMechanic System foi especialmente otimizado para funcionar perfeitamente em ambientes **CloudLinux + LiteSpeed + Imunify360** com **PHP 8.4.x**, oferecendo máxima performance, segurança e estabilidade.

## ⚠️ Requisitos Específicos de Versão

### PHP 8.4.x (OBRIGATÓRIO)
- **Versão exata:** PHP 8.4.x
- **NÃO usar:** PHP 8.5+ (pode causar incompatibilidades)
- **NÃO usar:** PHP 8.3 ou inferior (recursos ausentes)
- **Configuração:** Via CloudLinux PHP Selector no cPanel

## 🔧 Configurações Específicas

### LiteSpeed Web Server

O sistema detecta automaticamente o LiteSpeed e aplica configurações otimizadas:

- **Cache nativo**: Configuração automática do LiteSpeed Cache
- **Compressão**: Otimizada para LiteSpeed
- **Rewrite rules**: Compatíveis com LiteSpeed
- **Headers de segurança**: Configurados especificamente para LiteSpeed

### CloudLinux

Compatibilidade total com CloudLinux CageFS e LVE:

- **CageFS**: Isolamento de arquivos respeitado
- **LVE Limits**: Configurações PHP otimizadas para limites LVE
- **Resource Management**: Uso eficiente de CPU e memória
- **Selector**: Compatível com CloudLinux PHP Selector

### Imunify360

Integração com sistema de segurança Imunify360:

- **Firewall**: Regras compatíveis com Imunify360
- **Malware Scanner**: Arquivos otimizados para escaneamento
- **Proactive Defense**: Configurações que não conflitam
- **Reputation Management**: Headers de segurança adequados

## 📁 Arquivos de Configuração

### `.htaccess` Otimizado

```apache
# Configuração otimizada para LiteSpeed + CloudLinux + Imunify360
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Compatibilidade com CloudLinux CageFS
    # Proteção Imunify360
    # Otimizações LiteSpeed
</IfModule>
```

### `.user.ini` para PHP

```ini
; Configuração PHP otimizada para CloudLinux + LiteSpeed
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 100M
post_max_size = 100M

; Segurança Imunify360
expose_php = Off
allow_url_fopen = Off

; OPcache LiteSpeed otimizado
opcache.enable = 1
opcache.memory_consumption = 128
```

### `.litespeed_conf.dat`

Configuração específica do LiteSpeed Cache para máxima performance.

## 🚀 Performance

### Otimizações Aplicadas

1. **Cache Inteligente**
   - LiteSpeed Cache configurado automaticamente
   - Cache de assets estáticos otimizado
   - Headers de cache apropriados

2. **Compressão**
   - Gzip/Brotli para LiteSpeed
   - Minificação de CSS/JS
   - Otimização de imagens

3. **Database**
   - Queries otimizadas para CloudLinux
   - Connection pooling eficiente
   - Índices apropriados

## 🔒 Segurança

### Camadas de Proteção

1. **Imunify360 Integration**
   - Firewall rules compatíveis
   - Malware protection
   - Proactive defense

2. **CloudLinux Security**
   - CageFS isolation
   - Resource limits
   - User isolation

3. **Application Security**
   - CSRF protection
   - XSS prevention
   - SQL injection protection
   - Rate limiting

## 📊 Monitoramento

### Métricas Importantes

- **LVE Usage**: Monitoramento de recursos CloudLinux
- **Cache Hit Rate**: Eficiência do LiteSpeed Cache
- **Security Events**: Logs do Imunify360
- **Performance**: Core Web Vitals

### Logs Disponíveis

```
storage/logs/laravel.log          # Application logs
storage/logs/security.log         # Security events
storage/logs/performance.log      # Performance metrics
```

## 🛠️ Troubleshooting

### Problemas Comuns

#### 1. Erro 500 após instalação
```bash
# Verificar permissões
chmod -R 755 /home/usuario/public_html
chmod -R 777 /home/usuario/public_html/storage
chmod -R 777 /home/usuario/public_html/bootstrap/cache
```

#### 2. Cache não funcionando
```bash
# Limpar cache LiteSpeed
# Via painel de controle ou:
php artisan cache:clear
php artisan config:clear
```

#### 3. Problemas de upload
```bash
# Verificar limites PHP
php -i | grep -E "(upload_max_filesize|post_max_size|memory_limit)"
```

#### 4. Erro de conexão com banco
- Verificar credenciais no `.env`
- Testar conexão via phpMyAdmin
- Verificar limites CloudLinux

### Comandos Úteis

```bash
# Verificar status LiteSpeed
systemctl status lshttpd

# Verificar CloudLinux
cat /proc/lve/list

# Verificar Imunify360
imunify360-agent status

# Logs do sistema
tail -f storage/logs/laravel.log
```

## 📞 Suporte Especializado

Para suporte específico em ambientes CloudLinux + LiteSpeed + Imunify360:

1. **Logs detalhados**: Sempre incluir logs completos
2. **Informações do ambiente**: Versões de software
3. **Configurações**: Arquivos `.htaccess` e `.user.ini`
4. **Métricas**: Usage LVE e performance

## 🔄 Atualizações

O sistema verifica automaticamente compatibilidade com:
- Atualizações do LiteSpeed
- Patches do CloudLinux
- Updates do Imunify360
- Novas versões do PHP

---

**Sistema HomeMechanic v1.0.0**  
Otimizado para CloudLinux + LiteSpeed + Imunify360  
Máxima Performance, Segurança e Estabilidade