<?php

namespace Core;

use Config\Database;

class Router
{
    public static function run(): void
    {
        $rotas = [
            '/'                            => ['controller' => 'HomeController', 'action' => 'index'],
            '/home'                        => ['controller' => 'HomeController', 'action' => 'index'],
            '/login'                       => ['controller' => 'AuthController', 'action' => 'login'],
            '/logout'                      => ['controller' => 'AuthController', 'action' => 'logout'],
            '/cadastrar'                   => ['controller' => 'FuncionarioController', 'action' => 'cadastrar'],
            '/funcionarios/status'         => ['controller' => 'FuncionarioController', 'action' => 'alterarStatus'],
            '/funcionarios/excluir'        => ['controller' => 'FuncionarioController', 'action' => 'excluir'],
            '/agendamentos'                => ['controller' => 'AgendamentosController', 'action' => 'index'],
            '/agendamentos/salvar'         => ['controller' => 'AgendamentosController', 'action' => 'salvar'],
            '/agendamentos/atualizar'      => ['controller' => 'AgendamentosController', 'action' => 'atualizar'],
            '/agendamentos/excluir'        => ['controller' => 'AgendamentosController', 'action' => 'excluir'],
            '/agendamentos/verificar-cpf'  => ['controller' => 'AgendamentosController', 'action' => 'verificarCpf'],
            '/dashboard'                   => ['controller' => 'DashboardController', 'action' => 'index'],
            '/prontuarios'                 => ['controller' => 'ProntuarioController', 'action' => 'index'],
            '/prontuarios/criar'           => ['controller' => 'ProntuarioController', 'action' => 'criar'],
            '/prontuarios/salvar'          => ['controller' => 'ProntuarioController', 'action' => 'salvar'],
            '/prontuarios/editar'          => ['controller' => 'ProntuarioController', 'action' => 'editar'],
            '/prontuarios/atualizar'       => ['controller' => 'ProntuarioController', 'action' => 'atualizar'],
            '/prontuarios/excluir'         => ['controller' => 'ProntuarioController', 'action' => 'excluir'],
        ];

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = str_replace('\\', '/', dirname($scriptName));

        $basePathClean = rtrim($basePath, '/');
        if (!empty($basePathClean) && str_starts_with($uri, $basePathClean)) {
            $uri = substr($uri, strlen($basePathClean));
        }

        $uri = '/' . trim($uri, '/');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

         // Tela de aviso (503) para agendamentos de usuários não autenticados
        if (strpos($uri, '/agendamentos') === 0 && !isset($_SESSION['usuario_id'])) {
            http_response_code(503);
            header('Retry-After: 3600');
            require_once __DIR__ . '/../app/Views/errors/503.php';
            exit;
        }

        // 1. Verificação de Rotas Protegidas (Redireciona para o login)
        $rotasProtegidas = ['/agendamentos', '/prontuarios', '/dashboard', '/cadastrar'];
        $precisaAutenticacao = false;

        foreach ($rotasProtegidas as $rotaProtegida) {
            if (str_starts_with($uri, $rotaProtegida)) {
                $precisaAutenticacao = true;
                break;
            }
        }

        if ($precisaAutenticacao && !isset($_SESSION['usuario_id'])) {
            header('Location: ' . $basePathClean . '/login');
            exit;
        }

        // 2. Execução da Rota
        if (array_key_exists($uri, $rotas)) {
            $perfilBruto = $_SESSION['usuario_perfil'] ?? $_SESSION['perfil'] ?? $_SESSION['cargo'] ?? null;
            $perfilUsuario = $perfilBruto ? strtolower(trim((string)$perfilBruto)) : null;

           // Restrição de Acesso às Recepcionistas no Prontuário
            if (str_starts_with($uri, '/prontuarios')) {
                if (in_array($perfilUsuario, ['recepcao', 'recepcionista', 'recepção'], true)) {
                    http_response_code(403);
                    require_once __DIR__ . '/../app/Views/errors/403.php';
                    exit;
                }
            }

            

            $controllerName = "App\\Controllers\\" . $rotas[$uri]['controller'];
            $action = $rotas[$uri]['action'];

            if (class_exists($controllerName)) {
                $pdo = Database::getConnection();
                $controller = new $controllerName($pdo);

                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        // 3. Fallback 404 (Ajustado o caminho relativo de busca das Views)
        http_response_code(404);
        $view404 = __DIR__ . '/../Views/errors/404.php';
        if (file_exists($view404)) {
            require_once $view404;
        } else {
            echo "<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Erro 404 - Página não encontrada</h1>";
        }
    }
}