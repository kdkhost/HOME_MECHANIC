<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['slug' => 'dashboard.view', 'name' => 'Ver Dashboard', 'description' => 'Acesso ao painel principal', 'module' => 'dashboard', 'action' => 'view', 'sort_order' => 1],

            // Usuarios
            ['slug' => 'users.view', 'name' => 'Listar Usuarios', 'description' => 'Ver lista de usuarios', 'module' => 'users', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'users.create', 'name' => 'Criar Usuario', 'description' => 'Cadastrar novos usuarios', 'module' => 'users', 'action' => 'create', 'sort_order' => 2],
            ['slug' => 'users.edit', 'name' => 'Editar Usuario', 'description' => 'Modificar dados de usuarios', 'module' => 'users', 'action' => 'edit', 'sort_order' => 3],
            ['slug' => 'users.delete', 'name' => 'Excluir Usuario', 'description' => 'Remover usuarios do sistema', 'module' => 'users', 'action' => 'delete', 'sort_order' => 4],
            ['slug' => 'users.manage', 'name' => 'Gerenciar Permissoes', 'description' => 'Atribuir/revogar permissoes de usuarios', 'module' => 'users', 'action' => 'manage', 'sort_order' => 5],

            // Servicos
            ['slug' => 'services.view', 'name' => 'Listar Servicos', 'description' => 'Ver servicos cadastrados', 'module' => 'services', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'services.create', 'name' => 'Criar Servico', 'description' => 'Cadastrar novos servicos', 'module' => 'services', 'action' => 'create', 'sort_order' => 2],
            ['slug' => 'services.edit', 'name' => 'Editar Servico', 'description' => 'Modificar servicos existentes', 'module' => 'services', 'action' => 'edit', 'sort_order' => 3],
            ['slug' => 'services.delete', 'name' => 'Excluir Servico', 'description' => 'Remover servicos', 'module' => 'services', 'action' => 'delete', 'sort_order' => 4],

            // Depoimentos
            ['slug' => 'testimonials.view', 'name' => 'Listar Depoimentos', 'description' => 'Ver depoimentos', 'module' => 'testimonials', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'testimonials.create', 'name' => 'Criar Depoimento', 'description' => 'Adicionar depoimentos', 'module' => 'testimonials', 'action' => 'create', 'sort_order' => 2],
            ['slug' => 'testimonials.edit', 'name' => 'Editar Depoimento', 'description' => 'Modificar depoimentos', 'module' => 'testimonials', 'action' => 'edit', 'sort_order' => 3],
            ['slug' => 'testimonials.delete', 'name' => 'Excluir Depoimento', 'description' => 'Remover depoimentos', 'module' => 'testimonials', 'action' => 'delete', 'sort_order' => 4],

            // Galeria
            ['slug' => 'gallery.view', 'name' => 'Ver Galeria', 'description' => 'Acessar galeria de fotos', 'module' => 'gallery', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'gallery.create', 'name' => 'Adicionar Fotos', 'description' => 'Enviar fotos para galeria', 'module' => 'gallery', 'action' => 'create', 'sort_order' => 2],
            ['slug' => 'gallery.edit', 'name' => 'Editar Fotos', 'description' => 'Modificar informacoes das fotos', 'module' => 'gallery', 'action' => 'edit', 'sort_order' => 3],
            ['slug' => 'gallery.delete', 'name' => 'Excluir Fotos', 'description' => 'Remover fotos da galeria', 'module' => 'gallery', 'action' => 'delete', 'sort_order' => 4],

            // Blog/Posts
            ['slug' => 'posts.view', 'name' => 'Listar Posts', 'description' => 'Ver posts do blog', 'module' => 'posts', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'posts.create', 'name' => 'Criar Post', 'description' => 'Escrever novos posts', 'module' => 'posts', 'action' => 'create', 'sort_order' => 2],
            ['slug' => 'posts.edit', 'name' => 'Editar Post', 'description' => 'Modificar posts existentes', 'module' => 'posts', 'action' => 'edit', 'sort_order' => 3],
            ['slug' => 'posts.delete', 'name' => 'Excluir Post', 'description' => 'Remover posts', 'module' => 'posts', 'action' => 'delete', 'sort_order' => 4],
            ['slug' => 'posts.publish', 'name' => 'Publicar/Despublicar', 'description' => 'Controlar visibilidade dos posts', 'module' => 'posts', 'action' => 'publish', 'sort_order' => 5],

            // SEO
            ['slug' => 'seo.view', 'name' => 'Ver Configuracoes SEO', 'description' => 'Acessar painel SEO', 'module' => 'seo', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'seo.edit', 'name' => 'Editar SEO', 'description' => 'Modificar configuracoes de SEO', 'module' => 'seo', 'action' => 'edit', 'sort_order' => 2],

            // Mensagens de Contato
            ['slug' => 'contact.view', 'name' => 'Ver Mensagens', 'description' => 'Listar mensagens recebidas', 'module' => 'contact', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'contact.reply', 'name' => 'Responder Mensagens', 'description' => 'Enviar respostas aos contatos', 'module' => 'contact', 'action' => 'reply', 'sort_order' => 2],
            ['slug' => 'contact.delete', 'name' => 'Excluir Mensagens', 'description' => 'Remover mensagens', 'module' => 'contact', 'action' => 'delete', 'sort_order' => 3],

            // Configuracoes
            ['slug' => 'settings.view', 'name' => 'Ver Configuracoes', 'description' => 'Acessar configuracoes do site', 'module' => 'settings', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'settings.edit', 'name' => 'Editar Configuracoes', 'description' => 'Modificar configuracoes gerais', 'module' => 'settings', 'action' => 'edit', 'sort_order' => 2],
            ['slug' => 'settings.email', 'name' => 'Configurar E-mail', 'description' => 'Gerenciar SMTP e templates', 'module' => 'settings', 'action' => 'email', 'sort_order' => 3],
            ['slug' => 'settings.backup', 'name' => 'Gerenciar Backups', 'description' => 'Criar e restaurar backups', 'module' => 'settings', 'action' => 'backup', 'sort_order' => 4],

            // Templates de E-mail
            ['slug' => 'email_templates.view', 'name' => 'Ver Templates', 'description' => 'Listar templates de e-mail', 'module' => 'email_templates', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'email_templates.edit', 'name' => 'Editar Templates', 'description' => 'Modificar templates de e-mail', 'module' => 'email_templates', 'action' => 'edit', 'sort_order' => 2],

            // Permissoes
            ['slug' => 'permissions.view', 'name' => 'Ver Permissoes', 'description' => 'Listar permissoes do sistema', 'module' => 'permissions', 'action' => 'view', 'sort_order' => 1],
            ['slug' => 'permissions.manage', 'name' => 'Gerenciar Permissoes', 'description' => 'Criar, editar e atribuir permissoes', 'module' => 'permissions', 'action' => 'manage', 'sort_order' => 2],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
