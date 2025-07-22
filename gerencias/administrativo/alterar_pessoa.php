<?php
// public/administracao/alterar_pessoa.php
session_start();

if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../conexaoBanco.php'
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Pessoa - Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class='alterarPessoa'>
    <div class="alterarPessoa container">
    <h4>Redefinir Senha do Usuário</h4>
        <p class="text-muted">Digite o nome de usuário para redefinir a senha para o padrão 'Pmsbc@123'.</p>
        <form method="POST" action="resetar_senha.php">
            <div class="mb-3">
                <label for="username_to_reset" class="form-label">Nome de Usuário</label>
                <input type="text" class="form-control" id="username_to_reset" name="username_to_reset" required autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Redefinir Senha</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../../assets/js/alterar_pessoa_script.js"></script>
</body>
</html>
