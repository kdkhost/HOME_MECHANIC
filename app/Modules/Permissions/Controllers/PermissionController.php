<?php

namespace App\Modules\Permissions\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    /**
     * Lista de modulos disponiveis no sistema.
     */
    private array $modules = [
        'dashboard' => 'Dashboard',
        'users' => 'Usuarios',
        'services' => 'Servicos',
        'testimonials' => 'Depoimentos',
        'gallery' => 'Galeria',
        'posts' => 'Blog/Posts',
        'seo' => 'SEO',
        'contact' => 'Contato',
        'settings' => 'Configuracoes',
        'email_templates' => 'Templates de E-mail',
        'permissions' => 'Permissoes',
    ];

    /**
     * Acoes padrao para permissoes.
     */
    private array $actions = [
        'view' => 'Visualizar',
        'create' => 'Criar',
        'edit' => 'Editar',
        'delete' => 'Excluir',
        'manage' => 'Gerenciar',
        'reply' => 'Responder',
        'publish' => 'Publicar',
        'backup' => 'Backup',
        'email' => 'E-mail',
    ];

    /**
     * Listar todas as permissoes organizadas por modulo.
     */
    public function index()
    {
        $permissions = Permission::orderBy('module')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return view('modules.permissions.index', [
            'permissions' => $permissions,
            'modules' => $this->modules,
        ]);
    }

    /**
     * Formulario para criar nova permissao.
     */
    public function create()
    {
        return view('modules.permissions.form', [
            'permission' => null,
            'modules' => $this->modules,
            'actions' => $this->actions,
        ]);
    }

    /**
     * Salvar nova permissao.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|unique:permissions,slug|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = Permission::where('module', $validated['module'])->max('sort_order') + 1;

        try {
            Permission::create($validated);
            Log::info('Permissao criada', ['slug' => $validated['slug'], 'user_id' => auth()->id()]);
            return redirect()->route('admin.permissions.index')->with('success', 'Permissao criada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar permissao', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao criar permissao: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Formulario para editar permissao.
     */
    public function edit(Permission $permission)
    {
        return view('modules.permissions.form', [
            'permission' => $permission,
            'modules' => $this->modules,
            'actions' => $this->actions,
        ]);
    }

    /**
     * Atualizar permissao.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        try {
            $permission->update($validated);
            Log::info('Permissao atualizada', ['slug' => $permission->slug, 'user_id' => auth()->id()]);
            return redirect()->route('admin.permissions.index')->with('success', 'Permissao atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar permissao', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao atualizar permissao: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Excluir permissao.
     */
    public function destroy(Permission $permission)
    {
        // Verificar se a permissao esta sendo usada
        if ($permission->users()->exists()) {
            return back()->with('error', 'Nao e possivel excluir: permissao esta atribuida a usuarios.');
        }

        try {
            $slug = $permission->slug;
            $permission->delete();
            Log::info('Permissao excluida', ['slug' => $slug, 'user_id' => auth()->id()]);
            return back()->with('success', 'Permissao excluida com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir permissao', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao excluir permissao: ' . $e->getMessage());
        }
    }

    /**
     * Ativar/Desativar permissao.
     */
    public function toggle(Permission $permission)
    {
        try {
            $permission->update(['is_active' => !$permission->is_active]);
            $status = $permission->is_active ? 'ativada' : 'desativada';
            Log::info("Permissao {$status}", ['slug' => $permission->slug, 'user_id' => auth()->id()]);
            return back()->with('success', "Permissao {$status} com sucesso!");
        } catch (\Exception $e) {
            Log::error('Erro ao alterar status da permissao', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao alterar status da permissao.');
        }
    }

    /**
     * Pagina para gerenciar permissoes de um usuario.
     */
    public function userPermissions(User $user)
    {
        $userPermissions = $user->permissions()->pluck('permissions.id')->toArray();
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('module');

        return view('modules.permissions.user', [
            'user' => $user,
            'permissions' => $permissions,
            'userPermissions' => $userPermissions,
            'modules' => $this->modules,
        ]);
    }

    /**
     * Atualizar permissoes de um usuario.
     */
    public function updateUserPermissions(Request $request, User $user)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $validated['permissions'] ?? [];

        try {
            $user->syncPermissions($permissionIds, auth()->id());
            Log::info('Permissoes do usuario atualizadas', ['user_id' => $user->id, 'granted_by' => auth()->id()]);
            return redirect()->route('admin.users.index')->with('success', 'Permissoes do usuario atualizadas com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar permissoes do usuario', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao atualizar permissoes: ' . $e->getMessage());
        }
    }
}
