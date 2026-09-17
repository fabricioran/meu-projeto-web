<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Inter", sans-serif;
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .error-card {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 420px;
            width: 90%;
        }
        .error-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        h1 {
            color: #e53e3e;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }
        p {
            color: #4a5568;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .btn-back {
            display: inline-block;
            background-color: #2b6cb0;
            color: #ffffff;
            padding: 0.65rem 1.25rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }
        .btn-back:hover {
            background-color: #2c5282;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">🚫</div>
        <h1>Acesso Negado</h1>
        <p>Perfil de recepção não possui acesso a prontuários.</p>
        <a href="/agendamentos" class="btn-back">⬅ Voltar para Agendamentos</a>
    </div>
</body>
</html>
