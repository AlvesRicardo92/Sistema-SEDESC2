<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Primeiro Acesso - Definir Senha</title>
    <!-- Incluindo Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluindo nosso CSS personalizado (reutilizado do login) -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow-lg custom-card-width">
            <h2 class="text-center mb-4 text-primary">Definir Nova Senha</h2>

            <!-- Área reservada para mensagens de erro/sucesso -->
            <div id="messageArea" class="alert text-center" role="alert" style="display: none;">
                <!-- A mensagem será inserida aqui pelo JavaScript -->
            </div>

            <form id="firstAccessForm">
                <div class="mb-3">
                    <label for="newPassword" class="form-label">Nova Senha</label>
                    <input type="password" class="form-control" id="newPassword" name="new_password" autocomplete="off" required>
                    <small id="passwordHelp" class="form-text text-muted">Mínimo 6 caracteres, 1 maiúscula, 1 número.</small>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" autocomplete="off" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary custom-login-button">Definir Senha</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Incluindo Bootstrap JS (Bundle com Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Incluindo jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Incluindo nosso JavaScript externo para primeiro acesso -->
    <script src="assets/js/primeiro_acesso_script.js"></script>
</body>
</html>
