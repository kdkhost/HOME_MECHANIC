<?php

namespace App\Modules\Contact\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Lista de mensagens de contato
     */
    public function index()
    {
        // Dados simulados
        $messages = [
            [
                'id' => 1,
                'name' => 'João Silva',
                'email' => 'joao@email.com',
                'subject' => 'Orçamento para revisão',
                'message' => 'Gostaria de um orçamento para revisão do meu carro...',
                'status' => 'new',
                'created_at' => now()->subHours(2)
            ],
            [
                'id' => 2,
                'name' => 'Maria Santos',
                'email' => 'maria@email.com',
                'subject' => 'Dúvida sobre serviços',
                'message' => 'Vocês fazem troca de óleo?',
                'status' => 'replied',
                'created_at' => now()->subDays(1)
            ]
        ];

        return view('modules.contact.index', compact('messages'));
    }

    /**
     * Exibir mensagem específica
     */
    public function show($id)
    {
        // TODO: Buscar mensagem no banco
        $message = [
            'id' => $id,
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'phone' => '(11) 99999-9999',
            'subject' => 'Orçamento para revisão',
            'message' => 'Gostaria de um orçamento para revisão do meu carro. É um Honda Civic 2018.',
            'status' => 'new',
            'created_at' => now()->subHours(2)
        ];

        return view('modules.contact.show', compact('message'));
    }

    /**
     * Responder mensagem
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string'
        ]);

        // TODO: Implementar envio de resposta por email
        
        return redirect()->route('admin.contact.index')
            ->with('success', 'Resposta enviada com sucesso!');
    }

    /**
     * Excluir mensagem
     */
    public function destroy($id)
    {
        // TODO: Implementar exclusão no banco
        
        return redirect()->route('admin.contact.index')
            ->with('success', 'Mensagem excluída com sucesso!');
    }
}