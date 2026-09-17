<?php
http_response_code(503);
header('Retry-After: 3600');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Agendamento Indisponível</title>
    <style>
        :root {
            --primary: #0a2540;
            --accent: #0284c7;
            --text-muted: #64748b;
            --bg-light: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        a {
            text-decoration: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 24px;
            background-color: var(--accent);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
        }

        .maintenance-card {
            background-color: #ffffff;
            border-radius: 24px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            width: 100%;
            max-width: 520px;
        }

        .icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .whatsapp-icon-bg {
            position: relative;
            width: 90px;
            height: 90px;
            background-color: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.25);
        }

        .svg-whatsapp {
            width: 44px;
            height: 44px;
            fill: #ffffff;
        }

        .gear-badge {
            position: absolute;
            bottom: -5px;
            right: -5px;
            background-color: var(--primary);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .maintenance-card h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .text-muted {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .alert-box {
            background-color: var(--bg-light);
            border-left: 4px solid var(--accent);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 24px 0;
            text-align: left;
        }

        .alert-box p {
            font-size: 0.9rem;
            color: var(--primary);
            margin: 0;
        }

        .text-small {
            font-size: 0.85rem;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <div class="maintenance-card">
        <div class="icon-wrapper">
            <div class="whatsapp-icon-bg">
                <svg viewBox="0 0 24 24" class="svg-whatsapp">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397 0 11.948 0c3.179.001 6.165 1.24 8.413 3.494 2.25 2.253 3.487 5.244 3.484 8.423-.003 6.599-5.34 11.948-11.892 11.948-2.015-.001-4.004-.51-5.772-1.482L0 24zm6.59-4.846c1.657.983 3.28 1.498 4.774 1.499 5.385 0 9.766-4.382 9.769-9.768.002-2.607-1.012-5.059-2.859-6.908C16.425 2.128 13.979 1.11 11.37 1.11c-5.391 0-9.774 4.382-9.777 9.769-.001 1.637.439 3.238 1.272 4.666l-.995 3.633 3.737-.98z"/>
                </svg>
                <div class="gear-badge">⚙️</div>
            </div>
        </div>

        <h1>Agendamento Indisponível</h1>
        <p class="text-muted">Nosso canal automatizado do WhatsApp está temporariamente fora do ar para manutenções preventivas.</p>
        
        <div class="alert-box">
            <p><strong>Status do Serviço:</strong> Sistema em Manutenção Temporária</p>
        </div>

        <p class="text-muted text-small">Por favor, retorne à tela inicial e tente novamente mais tarde.</p>

        <a href="/home" class="btn">
            Voltar para a Página Inicial
        </a>
    </div>

</body>
</html>
