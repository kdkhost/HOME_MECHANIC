<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Helpers\FileUploadHelper;

class UsersController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();

        try {
            // Filtrar usuarios de acordo com hierarquia
            if ($currentUser->isSuperAdmin()) {
                // Superadmin ve todos (inclusive outros admins)
                $users = User::orderBy('created_at', 'desc')->get();
            } elseif ($currentUser->permission_level >= 50) {
                // Admin so ve usuarios de nivel inferior (nivel 10)
                $users = User::where('permission_level', '<', $currentUser->permission_level)
                    ->orWhere('id', $currentUser->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Usuario comum so ve a si mesmo
                $users = User::where('id', $currentUser->id)->get();
            }
        } catch (\Exception $e) {
            $users = collect([[
                'id' => 1, 'name' => 'Administrador',
                'email' => 'admin@homemechanic.com.br',
                'role' => 'admin', 'avatar' => null,
                'created_at' => now(), 'email_verified_at' => now(),
            ]]);
        }
        return view('modules.users.index', compact('users'));
    }

    public function create()
    {
        return view('modules.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:superadmin,admin,user',
            'avatar'   => 'nullable', // Pode ser arquivo ou UUID string
            'phone'    => 'nullable|string|max:20',
            'bio'      => 'nullable|string|max:500',
        ]);

        try {
            $data = [
                'name'               => $request->name,
                'email'              => $request->email,
                'password'           => Hash::make($request->password),
                'role'               => $request->role,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
                $data['phone'] = $request->phone;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'bio')) {
                $data['bio'] = $request->bio;
            }

            $avatarResolved = FileUploadHelper::resolveFromRequest($request, 'avatar', 'uploads/avatars');
            if ($avatarResolved !== null) {
                $data['avatar'] = $avatarResolved ?: null;
            }

            $user = User::create($data);

            // Enviar e-mail de verificação
            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário criado com sucesso! Um e-mail de verificação foi enviado para ' . $user->email);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar usuário: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = auth()->user();

            // Verificar se pode ver dados deste usuario (proprio ou que pode gerenciar)
            if ($user->id !== $currentUser->id && !$currentUser->canManageUser($user)) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Você não tem permissão para ver os dados deste usuário.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Usuário não encontrado.');
        }
        return view('modules.users.show', compact('user'));
    }

    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);

            // Verificar hierarquia - so pode editar usuarios que pode gerenciar
            if (!auth()->user()->canManageUser($user) && $user->id !== auth()->id()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Você não tem permissão para editar este usuário.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Usuário não encontrado.');
        }
        return view('modules.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:superadmin,admin,user',
            'avatar'   => 'nullable', // Pode ser arquivo ou UUID string
            'phone'    => 'nullable|string|max:20',
            'bio'      => 'nullable|string|max:500',
            'permission_level' => 'nullable|integer|min:10|max:100',
        ]);

        try {
            $user = User::findOrFail($id);

            // Verificar hierarquia - nao pode atualizar usuarios que nao pode gerenciar (exceto si proprio)
            if (!auth()->user()->canManageUser($user) && $user->id !== auth()->id()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Você não tem permissão para atualizar este usuário.');
            }

            $data = [
                'name'  => $request->name,
                'email' => $request->email,
                'role'  => $request->role,
            ];

            // Preservar permission_level ou atualizar se fornecido (apenas superadmin/gerente pode alterar)
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'permission_level')) {
                if ($request->filled('permission_level') && auth()->user()->canManageUser($user)) {
                    $data['permission_level'] = $request->permission_level;
                }
                // Se nao fornecido, mantem o valor atual (nao inclui no data)
            }

            // Campos opcionais — só inclui se a coluna existir no banco
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
                $data['phone'] = $request->phone;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'bio')) {
                $data['bio'] = $request->bio;
            }

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'avatar')) {
                // remove_avatar manual OU _clear do FilePond
                if ($request->input('remove_avatar') === '1' || $request->input('avatar_clear') === '1') {
                    FileUploadHelper::delete($user->avatar);
                    $data['avatar'] = null;
                } else {
                    $avatarResolved = FileUploadHelper::resolveFromRequest($request, 'avatar', 'uploads/avatars', $user->avatar);
                    if ($avatarResolved !== null) {
                        $data['avatar'] = $avatarResolved ?: null;
                    }
                }
            }

            $user->update($data);

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = auth()->user();

            // Verificar hierarquia - nao pode excluir superiores ou iguais
            if (!$currentUser->canManageUser($user)) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Você não tem permissão para excluir este usuário.');
            }

            // Remover avatar do storage
            if ($user->avatar) {
                FileUploadHelper::delete($user->avatar);
            }

            $user->delete();

            Log::info('Usuario excluido', ['deleted_user_id' => $id, 'deleted_by' => $currentUser->id]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Erro ao excluir usuário: ' . $e->getMessage());
        }
    }

    /**
     * Enviar/reenviar e-mail de verificação para o usuário
     */
    public function sendVerification($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'O e-mail deste usuário já está verificado.',
                ]);
            }

            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'message' => "E-mail de verificação enviado para {$user->email}!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar e-mail: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar e-mail manualmente (marcar como verificado pelo admin)
     */
    public function verifyManual($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'O e-mail deste usuário já está verificado.',
                ]);
            }

            $user->markEmailAsVerified();

            return response()->json([
                'success' => true,
                'message' => "E-mail de {$user->name} verificado manualmente com sucesso!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar e-mail: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar e-mail via link (rota assinada do Laravel)
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Implementacao do metodo verify...
        // (codigo continua...)
    }

    // ── Helper ────────────────────────────────────────────────
    // Upload gerenciado pelo FileUploadHelper
}
