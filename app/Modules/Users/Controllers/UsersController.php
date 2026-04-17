<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone'    => 'nullable|string|max:20',
            'bio'      => 'nullable|string|max:500',
        ]);

        try {
            $data = [
                'name'               => $request->name,
                'email'              => $request->email,
                'password'           => Hash::make($request->password),
                'role'               => $request->role,
                'email_verified_at'  => now(),
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
                $data['phone'] = $request->phone;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'bio')) {
                $data['bio'] = $request->bio;
            }

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->uploadAvatar($request->file('avatar'));
            }

            User::create($data);

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário criado com sucesso!');
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
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone'    => 'nullable|string|max:20',
            'bio'      => 'nullable|string|max:500',
        ]);

        try {
            $user = User::findOrFail($id);

            $data = [
                'name'  => $request->name,
                'email' => $request->email,
                'role'  => $request->role,
            ];

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

            if ($request->hasFile('avatar')) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'avatar')) {
                    $data['avatar'] = $this->uploadAvatar($request->file('avatar'));
                }
            }

            // Remover avatar
            if ($request->input('remove_avatar') === '1' && \Illuminate\Support\Facades\Schema::hasColumn('users', 'avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $data['avatar'] = null;
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
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Erro ao excluir usuário: ' . $e->getMessage());
        }
    }

    // ── Helper ────────────────────────────────────────────────
    private function uploadAvatar($file): string
    {
        $filename = 'avatars/' . uniqid('avatar_') . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->put($filename, file_get_contents($file));
        return $filename;
    }
}
