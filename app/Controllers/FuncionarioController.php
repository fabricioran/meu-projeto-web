public function save()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');
        $perfil = $_POST['perfil'] ?? 'usuario';

        if (!empty($nome) && !empty($email) && !empty($senha)) {
            $model = new User();
            
            if ($model->buscarPorEmail($email)) {
                $_SESSION['mensagem_erro'] = 'E-mail já cadastrado!';
                header('Location: /meu-projeto-web/public/usuarios/criar');
                exit;
            }

            // A Model User executa o password_hash() na variável $senha
            $model->inserir($nome, $email, $senha, $perfil);

            $_SESSION['mensagem_sucesso'] = 'Usuário cadastrado com sucesso!';
            header('Location: /meu-projeto-web/public/login');
            exit;
        }
    }

    header('Location: /meu-projeto-web/public/usuarios/criar');
    exit;
}