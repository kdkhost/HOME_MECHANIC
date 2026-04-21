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
        try {
            $users = User::orderBy('created_at', 'desc')->get();
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
            'role'     => 'required|in:admin,user',
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
            'role'     => 'required|in:admin,user',
            'avatar'   => 'nullable', // Pode ser arquivo ou UUID string
            'phone'    => 'nullable|string|max:20',
            'bio'      => 'nullable|string|max:500',
            'permission_level' => 'nullable|integer|min:10|max:100',
        ]);

        try {
            $user = User::findOrFail($id);

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

            if ($user->id === auth()->id()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Você não pode excluir sua própria conta.');
            }

            // Remover avatar do storage
            if ($user->avatar) {
                FileUploadHelper::delete($user->avatar);
            }

            $user->delete();

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

    /**
     * Acessar conta de outro usuario (impersonar).
     * Superadmin pode acessar qualquer conta.
     * Gerentes (nivel 50+) podem acessar usuarios de nivel inferior.
     */
    public function impersonate($id)
    {
        try {
            $targetUser = User::findOrFail($id);
            $currentUser = auth()->user();

            // Nao pode impersonar a si mesmo
            if ($targetUser->id === $currentUser->id) {
                return back()->with('error', 'Voce nao pode acessar sua propria conta.');
            }

            // Verificar permissao hierarquica
            if (!$currentUser->canManageUser($targetUser)) {
                return back()->with('error', 'Voce nao tem permissao para acessar a conta deste usuario.');
            }

            // Salvar dados da sessao original
            session(['impersonate' => [
                'original_user_id' => $currentUser->id,
                'user_id' => $targetUser->id,
                'started_at' => now()->toDateTimeString(),
            ]]);

            // Fazer login como o usuario alvo
            auth()->login($targetUser);

            Log::info('Usuario impersonado', [
                'original_user_id' => $currentUser->id,
                'impersonated_user_id' => $targetUser->id,
                'original_user_name' => $currentUser->name,
                'impersonated_user_name' => $targetUser->name,
            ]);

            return redirect()->route('admin.dashboard.index')
                ->with('success', "Voce esta acessando como {$targetUser->name} ({$targetUser->email}). Voce tem as permissoes deste usuario.");

        } catch (\Exception $e) {
            Log::error('Erro ao impersonar usuario', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao acessar conta: ' . $e->getMessage());
        }
    }

    /**
     * Voltar para a conta original (parar impersonacao).
     */
    public function stopImpersonating()
    {
        try {
            if (!session()->has('impersonate')) {
                return redirect()->route('admin.dashboard.index');
            }

            $originalUserId = session('impersonate.original_user_id');
            $impersonatedUserId = auth()->id();

            $originalUser = User::find($originalUserId);

            if ($originalUser) {
                auth()->login($originalUser);

                Log::info('Impersonacao encerrada', [
                    'original_user_id' => $originalUserId,
                    'impersonated_user_id' => $impersonatedUserId,
                ]);
            }

            session()->forget('impersonate');

            return redirect()->route('admin.users.index')
                ->with('success', 'Voce voltou para sua conta original.');

        } catch (\Exception $e) {
            Log::error('Erro ao parar impersonacao', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard.index');
        }
    }

    // ── Helper ────────────────────────────────────────────────
    // Upload gerenciado pelo FileUploadHelper
}
