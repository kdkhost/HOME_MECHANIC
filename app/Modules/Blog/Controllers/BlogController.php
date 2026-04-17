<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Modules\Blog\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Post::with('author')
                ->when($request->filled('search'), fn($q) => $q->search($request->search))
                ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
                ->orderBy('created_at', 'desc');

            $posts = $query->paginate(15);
            return view('modules.blog.index', compact('posts'));

        } catch (\Exception $e) {
            Log::error('Erro ao listar posts', ['error' => $e->getMessage()]);
            return view('modules.blog.index', ['posts' => collect()]);
        }
    }

    public function create()
    {
        return view('modules.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published',
            'excerpt' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $post = Post::create([
                'title'        => $request->title,
                'excerpt'      => $request->excerpt,
                'content'      => $request->content,
                'status'       => $request->status,
                'featured'     => $request->boolean('featured'),
                'user_id'      => Auth::id(),
                'published_at' => $request->status === 'published' ? now() : null,
            ]);

            AuditLog::record('post_created', $post, [], $post->toArray());
            DB::commit();

            return redirect()->route('admin.blog.index')->with('success', 'Post criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar post', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Erro ao criar post: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $post = Post::with('author')->findOrFail($id);
            return view('modules.blog.show', compact('post'));
        } catch (\Exception $e) {
            return redirect()->route('admin.blog.index')->with('error', 'Post não encontrado.');
        }
    }

    public function edit($id)
    {
        try {
            $post = Post::findOrFail($id);
            return view('modules.blog.edit', compact('post'));
        } catch (\Exception $e) {
            return redirect()->route('admin.blog.index')->with('error', 'Post não encontrado.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published,archived',
            'excerpt' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $post    = Post::findOrFail($id);
            $oldData = $post->toArray();

            $post->update([
                'title'        => $request->title,
                'excerpt'      => $request->excerpt,
                'content'      => $request->content,
                'status'       => $request->status,
                'featured'     => $request->boolean('featured'),
                'published_at' => $request->status === 'published' && !$post->published_at ? now() : $post->published_at,
            ]);

            AuditLog::record('post_updated', $post, $oldData, $post->fresh()->toArray());
            DB::commit();

            return redirect()->route('admin.blog.index')->with('success', 'Post atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar post', ['error' => $e->getMessage(), 'id' => $id]);
            return back()->withInput()->with('error', 'Erro ao atualizar post: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $post    = Post::findOrFail($id);
            $oldData = $post->toArray();
            $post->delete();
            AuditLog::record('post_deleted', $post, $oldData, []);
            return redirect()->route('admin.blog.index')->with('success', 'Post excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir post', ['error' => $e->getMessage(), 'id' => $id]);
            return redirect()->route('admin.blog.index')->with('error', 'Erro ao excluir post.');
        }
    }
}
