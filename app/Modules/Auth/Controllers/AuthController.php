<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Exibir página de login
     */
    public function showLogin()
    {
        // Redirecionar se já estiver autenticado
        if (Auth::check()) {
            return redirect()->route('admin.dashboard.index');
        }

        return view('modules.auth.login');
    }

    /**
     * Processar login
     */
    public function login(LoginRequest $request)
    {
        // Verificar rate limiting
        $key = $this->throttleKey($request);
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            
            Log::warning('Login rate limit exceeded', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
                'user_agent' => $request->userAgent()
            ]);

            // Formatação da mensagem em português com tempo restante
            $timeMessage = $minutes > 1 
                ? "Tente novamente em {$minutes} minutos" 
                : "Tente novamente em {$seconds} segundos";

            return response()->json([
                'success' => false,
                'message' => "Muitas tentativas de login. {$timeMessage}.",
                'retry_after' => $seconds,
                'retry_after_minutes' => $minutes
            ], 429);
        }

        // Tentar autenticação
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Login bem-sucedido
            $request->session()->regenerate();
            
            // Limpar tentativas de rate limiting
            RateLimiter::clear($key);
            
            // Log de sucesso
            Log::info('User logged in successfully', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'redirect' => route('admin.dashboard.index')
            ]);
        }

        // Login falhou - incrementar rate limiting
        RateLimiter::hit($key, 600); // 10 minutos

        // Log de falha
        Log::warning('Failed login attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Credenciais inválidas. Verifique seu e-mail e senha.',
            'attempts_left' => 5 - RateLimiter::attempts($key)
        ], 401);
    }

    /**
     * Processar logout
     */
    public function logout(Request $request)
    {
        // Log de logout
        if (Auth::check()) {
            Log::info('User logged out', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'ip' => $request->ip()
            ]);
        }

        // Fazer logout
        Auth::logout();
        
        // Invalidar sessão
        $request->session()->invalidate();
        
        // Regenerar token CSRF
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logout realizado com sucesso!');
    }

    /**
     * Verificar status de autenticação (AJAX)
     */
    public function checkAuth(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role
                ],
                'session_expires_in' => $this->getSessionTimeRemaining()
            ]);
        }

        return response()->json([
            'authenticated' => false
        ], 401);
    }

    /**
     * Renovar sessão (AJAX)
     */
    public function renewSession(Request $request)
    {
        if (Auth::check()) {
            $request->session()->regenerate();
            
            return response()->json([
                'success' => true,
                'message' => 'Sessão renovada com sucesso!',
                'session_expires_in' => $this->getSessionTimeRemaining()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Usuário não autenticado'
        ], 401);
    }

    /**
     * Obter informações de rate limiting
     */
    public function getRateLimitInfo(Request $request)
    {
        $key = $this->throttleKey($request);
        $attempts = RateLimiter::attempts($key);
        $maxAttempts = 5;
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            $minutes = ceil($retryAfter / 60);
            
            $timeMessage = $minutes > 1 
                ? "Bloqueado por {$minutes} minutos" 
                : "Bloqueado por {$retryAfter} segundos";
            
            return response()->json([
                'blocked' => true,
                'attempts' => $attempts,
                'max_attempts' => $maxAttempts,
                'retry_after' => $retryAfter,
                'retry_after_minutes' => $minutes,
                'message' => "{$timeMessage} devido a muitas tentativas de login."
            ]);
        }

        return response()->json([
            'blocked' => false,
            'attempts' => $attempts,
            'max_attempts' => $maxAttempts,
            'attempts_left' => $maxAttempts - $attempts,
            'message' => $attempts > 0 
                ? "Tentativas restantes: " . ($maxAttempts - $attempts) 
                : "Nenhuma tentativa de login registrada."
        ]);
    }

    /**
     * Gerar chave para rate limiting
     */
    private function throttleKey(Request $request): string
    {
        return 'login.' . $request->ip();
    }

    /**
     * Obter tempo restante da sessão em segundos
     */
    private function getSessionTimeRemaining(): int
    {
        $lifetime = config('session.lifetime', 120); // minutos
        $lastActivity = Session::get('_token_timestamp', time());
        $maxLifetime = $lifetime * 60; // converter para segundos
        $elapsed = time() - $lastActivity;
        
        return max(0, $maxLifetime - $elapsed);
    }
}