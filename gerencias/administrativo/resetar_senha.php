<?php
// public/administracao/resetar_senha.php
session_start();

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="resetarSenha">
    <div class="reset-password-container">
        <h4>Redefinir Senha do Usuário</h4>
        <p class="text-muted">Digite o nome de usuário para redefinir a senha para o padrão 'Pmsbc@123'.</p>
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="username_to_reset" class="form-label">Nome de Usuário</label>
                <input type="text" class="form-control" id="username_to_reset" name="username_to_reset" required autocomplete="off">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100 mt-3 buscarUsuario" style="margin-top:32px !important">Buscar</button>
            </div>
        </div>
        <div class="row">
            <label for="nome_completo" class="form-label">Nome Completo</label>
            <input type="text" class="form-control" id="nome_completo" name="nome_completo" disabled>
        </div>
        <div id="mensagemFeedback" class="alert d-none" role="alert"></div>
        <div class="row">
            <button class="btn btn-primary w-100 mt-3 resetarSenha" disabled>Redefinir Senha</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../assets/js/resetar_senha_script.js"></script>
</body>
</html>
