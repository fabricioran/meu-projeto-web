<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

        require_once __DIR__ . '/../Views/home/home.php';
    }
}
