<?php
// public/administracao/resetar_senha.php
session_start();

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
}

$message = '';
$messageType = ''; // 'success' or 'danger'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameToReset = trim($_POST['username_to_reset'] ?? '');
    $idUsuarioLogado = $_SESSION['user_id']; // ID do usuário que está realizando a ação

    if (empty($usernameToReset)) {
        $message = 'Por favor, digite o nome de usuário para redefinir a senha.';
        $messageType = 'danger';
    } else {
        try {
            $usuarioDAO = new UsuarioDAO();
            $usuario = $usuarioDAO->findByUsername($usernameToReset);

            if ($usuario) {
                // Senha padrão a ser definida e hasheada
                $novaSenhaPadrao = "Pmsbc@123";
                $hashedPassword = password_hash($novaSenhaPadrao, PASSWORD_DEFAULT);

                // Atualiza o objeto Usuário com a nova senha hasheada e o ID do usuário que atualizou
                $usuario->setSenha($hashedPassword);
                $usuario->setIdUsuarioAtualizacao($idUsuarioLogado);
                // NOVO: Define primeiro_acesso como true (1) para forçar a troca de senha no próximo login
                $usuario->setPrimeiroAcesso(true); 

                if ($usuarioDAO->update($usuario)) {
                    $message = "Senha do usuário '{$usernameToReset}' redefinida com sucesso para '{$novaSenhaPadrao}'. O usuário será solicitado a alterá-la no próximo login.";
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao redefinir a senha no banco de dados. Tente novamente.';
                    $messageType = 'danger';
                }
            } else {
                $message = 'Usuário não encontrado.';
                $messageType = 'danger';
            }
        } catch (\Exception $e) {
            error_log("Erro ao redefinir senha: " . $e->getMessage() . " em " . $e->getFile() . " na linha " . $e->getLine() . "\nStack Trace:\n" . $e->getTraceAsString());
            $message = 'Ocorreu um erro inesperado ao redefinir a senha. Tente novamente mais tarde.';
            $messageType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        
    </style>
</head>
<body class="resetarSenha">
    <div class="reset-password-container">
        <h4>Redefinir Senha do Usuário</h4>
        <p class="text-muted">Digite o nome de usuário para redefinir a senha para o padrão 'Pmsbc@123'.</p>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="resetar_senha.php">
            <div class="mb-3">
                <label for="username_to_reset" class="form-label">Nome de Usuário</label>
                <input type="text" class="form-control" id="username_to_reset" name="username_to_reset" required autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Redefinir Senha</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
