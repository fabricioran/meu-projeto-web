<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private static function carregarEnvManual(): void
    {
        $caminhoEnv = dirname(__DIR__) . '/.env';
        if (file_exists($caminhoEnv)) {
            $linhas = file($caminhoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($linhas as $linha) {
                if (strpos(trim($linha), '#') === 0) {
                    continue;
                }
                list($chave, $valor) = explode('=', $linha, 2);
                $chave = trim($chave);
                $valor = trim($valor);
                
                if (!array_key_exists($chave, $_ENV)) {
                    $_ENV[$chave] = $valor;
                }
            }
        }
    }

    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            try {
                self::$instance->query('SELECT 1');
            } catch (PDOException $e) {
                self::$instance = null;
            }
        }

        if (self::$instance === null) {
            try {
                self::carregarEnvManual();

                $config = [
                    'driver'   => 'mysql',
                    'host'     => (!empty($_ENV['DB_HOST']))     ? $_ENV['DB_HOST']     : ((!empty($_ENV['MYSQLHOST']))     ? $_ENV['MYSQLHOST']     : '127.0.0.1'),
                    'port'     => (!empty($_ENV['DB_PORT']))     ? $_ENV['DB_PORT']     : ((!empty($_ENV['MYSQLPORT']))     ? $_ENV['MYSQLPORT']     : '3306'),
                    'dbname'   => (!empty($_ENV['DB_NAME']))     ? $_ENV['DB_NAME']     : ((!empty($_ENV['MYSQLDATABASE'])) ? $_ENV['MYSQLDATABASE'] : 'medconnect'),
                    'username' => (!empty($_ENV['DB_USER']))     ? $_ENV['DB_USER']     : ((!empty($_ENV['MYSQLUSER']))     ? $_ENV['MYSQLUSER']     : 'root'),
                    'password' => (isset($_ENV['DB_PASSWORD']) && $_ENV['DB_PASSWORD'] !== '') ? $_ENV['DB_PASSWORD'] : ((isset($_ENV['MYSQLPASSWORD']) && $_ENV['MYSQLPASSWORD'] !== '') ? $_ENV['MYSQLPASSWORD'] : ''),
                    'charset'  => 'utf8mb4'
                ];

                $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";

                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                error_log("Erro de Conexão PDO: " . $e->getMessage());

                http_response_code(500);

                $errorPath = dirname(__DIR__) . '/app/Views/errors/500.php';
                if (file_exists($errorPath)) {
                    require_once $errorPath;
                } else {
                    echo "<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Erro Interno no Servidor</h1>";
                    echo "<p style='text-align:center; font-family:monospace; color:#777;'>" . htmlspecialchars($e->getMessage()) . "</p>";
                }
                exit;
            }
        }

        return self::$instance;
    }
}
