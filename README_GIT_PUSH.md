# Git Push - Instruções

## Commits Pendentes
Os commits foram criados localmente mas ainda precisam ser enviados (push) para o repositório remoto.

## No Servidor, execute:

```bash
cd /caminho/do/projeto

# Verificar status
git status

# Ver commits pendentes
git log --oneline -5

# Fazer push
git push origin master

# Se der erro de autenticação:
git push https://USERNAME:TOKEN@github.com/kdkhost/homemechanic.git master
```

## Alterações pendentes de push:

1. **fix: trocar texto do menu de contato para Atendimento**
   - Arquivo: `resources/views/layouts/frontend.blade.php`
   - Alteração: "Falar com a HomeMechanic" → "Atendimento"

2. **remove: funcionalidade de acessar conta de outros usuarios (impersonacao)**
   - Rotas removidas
   - Métodos do controller removidos
   - Middleware removido
   - Botão removido da lista

3. Outras correções de segurança e hierarquia de usuários

## Se o push falhar:

```bash
# Forçar push (cuidado!)
git push origin master --force

# Ou com verbose para ver o erro
git push origin master -v
```
