<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Lista de posts do blog
     */
    public function index()
    {
        // Dados simulados por enquanto
        $posts = [
            [
                'id' => 1,
                'title' => 'Primeiro Post do Blog',
                'excerpt' => 'Este é um exemplo de post do blog...',
                'status' => 'published',
                'created_at' => now()->subDays(5),
                'author' => 'Admin'
            ],
            [
                'id' => 2,
                'title' => 'Dicas de Manutenção Automotiva',
                'excerpt' => 'Confira as melhores dicas para manter seu veículo...',
                'status' => 'draft',
                'created_at' => now()->subDays(2),
                'author' => 'Admin'
            ]
        ];

        return view('modules.blog.index', compact('posts'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        return view('modules.blog.create');
    }

    /**
     * Salvar novo post
     */
    public function store(Request $request)
    {
        // Validação básica
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published'
        ]);

        // TODO: Implementar salvamento no banco
        
        return redirect()->route('admin.blog.index')
            ->with('success', 'Post criado com sucesso!');
    }

    /**
     * Exibir post específico
     */
    public function show($id)
    {
        // TODO: Buscar post no banco
        $post = [
            'id' => $id,
            'title' => 'Post de Exemplo',
            'content' => 'Conteúdo completo do post...',
            'status' => 'published',
            'created_at' => now(),
            'author' => 'Admin'
        ];

        return view('modules.blog.show', compact('post'));
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        // TODO: Buscar post no banco
        $post = [
            'id' => $id,
            'title' => 'Post de Exemplo',
            'content' => 'Conteúdo do post para edição...',
            'status' => 'published',
            'created_at' => now(),
            'author' => 'Admin'
        ];

        return view('modules.blog.edit', compact('post'));
    }

    /**
     * Atualizar post
     */
    public function update(Request $request, $id)
    {
        // Validação básica
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published'
        ]);

        // TODO: Implementar atualização no banco
        
        return redirect()->route('admin.blog.index')
            ->with('success', 'Post atualizado com sucesso!');
    }

    /**
     * Excluir post
     */
    public function destroy($id)
    {
        // TODO: Implementar exclusão no banco
        
        return redirect()->route('admin.blog.index')
            ->with('success', 'Post excluído com sucesso!');
    }
}