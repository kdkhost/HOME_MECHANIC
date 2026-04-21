<?php

namespace App\Modules\Contact\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Modules\Contact\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ContactMessage::latest()
                ->when($request->filled('search'), fn($q) => $q->search($request->search))
                ->when($request->filled('status'), function ($q) use ($request) {
                    if ($request->status === 'new')      $q->unread();
                    if ($request->status === 'replied')  $q->where('read', true);
                });

            $messages = $query->paginate(20);
            return view('modules.contact.index', compact('messages'));

        } catch (\Exception $e) {
            Log::error('Erro ao listar mensagens', ['error' => $e->getMessage()]);
            return view('modules.contact.index', ['messages' => collect()]);
        }
    }

    public function show($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            // Marcar como lida ao abrir
            if (!$message->read) $message->markAsRead();
            return view('modules.contact.show', compact('message'));
        } catch (\Exception $e) {
            return redirect()->route('admin.contact.index')->with('error', 'Mensagem não encontrada.');
        }
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['reply' => 'required|string|min:5']);

        try {
            $message = ContactMessage::findOrFail($id);

            // Tentar enviar e-mail de resposta usando template HTML
            $fromAddr = Setting::get('mail_from_address', config('mail.from.address'));
            $fromName = Setting::get('mail_from_name',    config('mail.from.name'));
            $siteName = Setting::get('site_name',         'Home Mechanic');

            Mail::send(
                'emails.contact_reply',
                [
                    'name' => $message->name,
                    'originalSubject' => $message->subject,
                    'reply' => $request->reply,
                    'siteName' => $siteName,
                ],
                function ($m) use ($message, $fromAddr, $fromName) {
                    $m->to($message->email, $message->name)
                      ->from($fromAddr, $fromName)
                      ->subject("Re: {$message->subject}");
                }
            );

            $message->update(['read' => true, 'email_sent' => true]);
            AuditLog::record('contact_replied', $message, [], ['reply' => $request->reply]);

            return redirect()->route('admin.contact.index')->with('success', 'Resposta enviada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao responder mensagem', ['error' => $e->getMessage(), 'id' => $id]);
            // Mesmo com erro no e-mail, marcar como lida
            try { ContactMessage::find($id)?->update(['read' => true]); } catch (\Exception) {}
            return back()->with('error', 'Erro ao enviar resposta: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $oldData = $message->toArray();
            $message->delete();
            AuditLog::record('contact_deleted', $message, $oldData, []);
            return redirect()->route('admin.contact.index')->with('success', 'Mensagem excluída com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir mensagem', ['error' => $e->getMessage(), 'id' => $id]);
            return redirect()->route('admin.contact.index')->with('error', 'Erro ao excluir mensagem.');
        }
    }

    public function markRead($id)
    {
        try {
            ContactMessage::findOrFail($id)->markAsRead();
            return response()->json(['success' => true, 'message' => 'Marcada como lida.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
