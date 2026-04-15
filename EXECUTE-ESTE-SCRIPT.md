# 🚀 EXECUTE ESTE SCRIPT PARA DEPLOY COMPLETO

## 📋 Pré-requisitos
1. Ter acesso SSH ao servidor (testado)
2. Ter Git instalado localmente
3. Ter publicado o repositório no GitHub

## 🔧 Passo 1: Publique no GitHub
```bash
# Se ainda não publicou, execute:
git remote add origin https://github.com/SEU_USUARIO/homemechanic-system.git
git branch -M main
git push -u origin main
```

## 🚀 Passo 2: Execute o Deploy
Copie e cole este comando no seu terminal:

```bash
ssh homemechanic@15.235.57.3 << 'ENDSSH'
cd /home/homemechanic/public_html
if [ "$(ls -A . 2>/dev/null)" ]; then
    mkdir -p ../backups/backup_$(date +%Y%m%d_%H%M%S)
    mv * ../backups/backup_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
fi
git clone https://github.com/SEU_USUARIO/homemechanic-system.git .
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 777 storage bootstrap/cache
ln -sf ../storage/app/public public/storage
cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
EOF
echo "✅ Deploy concluído! Acesse seu domínio para instalar."
ENDSSH
```

## 🌐 Passo 3: Acesse seu Domínio
1. Abra seu navegador
2. Acesse seu domínio
3. Será redirecionado automaticamente para `/install`
4. Configure com os dados:
   - **Host:** localhost
   - **Banco:** homemechanic_2026
   - **Usuário:** homemechanic_2026
   - **Senha:** homemechanic_2026

## ⚡ Comando Ultra-Rápido (Tudo em Uma Linha)
```bash
ssh homemechanic@15.235.57.3 "cd /home/homemechanic/public_html && [ \"\$(ls -A . 2>/dev/null)\" ] && mkdir -p ../backups/backup_\$(date +%Y%m%d_%H%M%S) && mv * ../backups/backup_\$(date +%Y%m%d_%H%M%S)/ 2>/dev/null; git clone https://github.com/SEU_USUARIO/homemechanic-system.git . && chmod -R 755 . && chmod -R 777 storage bootstrap/cache && ln -sf ../storage/app/public public/storage && echo '<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteRule ^(.*)$ public/\$1 [L]\n</IfModule>' > .htaccess && echo '✅ Deploy concluído!'"
```

**Substitua `SEU_USUARIO` pela sua conta do GitHub!**

---

## 🆘 Se Der Erro

### Erro de Permissão SSH:
```bash
ssh-keygen -R 15.235.57.3
ssh homemechanic@15.235.57.3
```

### Erro de Git:
```bash
ssh homemechanic@15.235.57.3 "which git || (sudo yum install -y git || sudo apt-get install -y git)"
```

### Verificar Status:
```bash
ssh homemechanic@15.235.57.3 "cd /home/homemechanic/public_html && ls -la && php -v"
```

---

## ✅ Resultado Esperado
Após executar, você deve ver:
- ✅ Deploy concluído!
- Arquivos do sistema no servidor
- Redirecionamento automático para instalador
- Sistema funcionando perfeitamente

**🎯 Execute o comando acima e em 30 segundos seu sistema estará online!**