<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Customizar respostas de erro para requisições AJAX
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return $this->handleJsonException($e, $request);
            }
        });
    }

    /**
     * Handle JSON exceptions for AJAX requests
     */
    protected function handleJsonException(Throwable $e, Request $request)
    {
        $status = 500;
        $message = 'Ocorreu um erro interno no servidor.';

        // Determinar status HTTP e mensagem baseado no tipo de exceção
        if ($e instanceof HttpException) {
            $status = $e->getStatusCode();
            
            switch ($status) {
                case 403:
                    $message = 'Acesso negado.';
                    break;
                case 404:
                    $message = 'Recurso não encontrado.';
                    break;
                case 419:
                    $message = 'Sessão expirada. Recarregue a página e tente novamente.';
                    break;
                case 422:
                    $message = 'Dados de entrada inválidos.';
                    break;
                case 429:
                    $message = 'Muitas tentativas. Aguarde alguns minutos.';
                    break;
                case 500:
                    $message = 'Erro interno do servidor.';
                    break;
                case 503:
                    $message = 'Sistema temporariamente indisponível.';
                    break;
            }
        } elseif ($e instanceof TokenMismatchException) {
            $status = 419;
            $message = 'Token CSRF inválido. Recarregue a página e tente novamente.';
        }

        // Retornar resposta JSON limpa sem dados sensíveis
        return response()->json([
            'message' => $message,
            'status' => $status
        ], $status);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Para erros 500 em produção, sempre usar página personalizada
        if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();
            
            // Verificar se existe view personalizada para o erro
            if (view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", [
                    'exception' => $e
                ], $status);
            }
        }

        // Para erros 500 não HTTP, usar página 500 personalizada
        if (!$this->isHttpException($e) && !config('app.debug')) {
            return response()->view('errors.500', [
                'exception' => $e
            ], 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Não autenticado.',
                'status' => 401
            ], 401);
        }

        // Redirecionar para login admin se a rota for do painel
        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->guest(route('admin.login'));
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Get the default context variables for logging.
     */
    protected function context(): array
    {
        return array_filter([
            'userId' => auth()->id(),
            'ip' => request()->ip(),
            'userAgent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }
}