<?php

namespace Core;

class Router
{
    public static function run()
    {
        $rotas = [
            '/'                     => ['controller' => 'HomeController', 'action' => 'index'],
            '/home'                 => ['controller' => 'HomeController', 'action' => 'index'],
            '/login'                => ['controller' => 'AuthController', 'action' => 'login'],
            '/logout'               => ['controller' => 'AuthController', 'action' => 'logout'],
            '/cadastrar'            => ['controller' => 'AuthController', 'action' => 'cadastrar'],
            '/agendamentos'         => ['controller' => 'PacienteController', 'action' => 'index'],
            '/pacientes/salvar'     => ['controller' => 'PacienteController', 'action' => 'salvar'],
            '/pacientes/editar'     => ['controller' => 'PacienteController', 'action' => 'editar'],
            '/pacientes/atualizar'  => ['controller' => 'PacienteController', 'action' => 'atualizar'],
            '/pacientes/excluir'    => ['controller' => 'PacienteController', 'action' => 'excluir'],
        ];

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $basePath = str_replace('\\', '/', $basePath);

        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        if (empty($uri)) {
            $uri = '/';
        } elseif ($uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        if (array_key_exists($uri, $rotas)) {
            $controllerName = "App\\Controllers\\" . $rotas[$uri]['controller'];
            $action = $rotas[$uri]['action'];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        http_response_code(404);
        echo "<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Erro 404 - Página não encontrada</h1>";
    }
}
