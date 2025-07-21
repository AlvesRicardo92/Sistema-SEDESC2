<?php
session_start(); // Inicia a sessão para acessar os dados do usuário logado

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . "/gerencias/conexaoBanco.php"; // Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/utils/cabecalho.php'; // Inclui o cabeçalho da página

$aviso = null; // Inicializa a variável do aviso como nula
$mensagem_erro = ''; // Variável para mensagens de erro/acesso negado

// 1. Obter o ID do aviso da URL e validar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $aviso_id = (int)$_GET['id']; // Converte para inteiro para segurança

    // 2. Consultar o banco de dados para buscar os detalhes do aviso
    $sql = "SELECT id, descricao, nome_imagem, id_territorio_exibicao
            FROM avisos
            WHERE id = ? AND CURDATE() BETWEEN data_inicio_exibicao AND data_fim_exibicao"; // Verifica se o aviso está ativo

    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        $mensagem_erro = "Erro na preparação da consulta: " . $mysqli->error;
    } else {
        $stmt->bind_param('i', $aviso_id);

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $aviso = $resultado->fetch_assoc();

                // 3. Validação de Território
                $id_territorio_usuario = $_SESSION['usuario']['territorio'];
                $id_territorio_aviso = $aviso['id_territorio_exibicao'];

                // Lógica de permissão:
                // Se o território do usuário for '0' (ex: administrador global) ou '1' (ex: território que vê todos os avisos)
                // OU se o território do usuário for igual ao território do aviso
                if (($id_territorio_usuario == 0 || $id_territorio_usuario == 1) || ($id_territorio_usuario == $id_territorio_aviso)) {
                    // Aviso permitido, continue para exibição
                } else {
                    $mensagem_erro = "Você não tem permissão para visualizar este aviso.";
                    $aviso = null; // Anula o aviso para não ser exibido
                }

            } else {
                $mensagem_erro = "Aviso não encontrado ou não está mais ativo.";
            }
            $resultado->free_result();
        } else {
            $mensagem_erro = "Erro na execução da consulta: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    $mensagem_erro = "ID do aviso inválido ou não especificado.";
}

// Fechar a conexão com o banco de dados
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Aviso</title>
    <!-- Incluindo Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluindo seu CSS personalizado -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .aviso-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .aviso-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .aviso-description {
            font-size: 1.1em;
            line-height: 1.6;
            color: #333;
            text-align: justify;
        }
        .alert-custom-page {
            max-width: 800px;
            margin: 30px auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if ($aviso): ?>
            <div class="aviso-container">
                <h2 class="text-primary mb-4">Aviso Importante</h2>
                <?php if (!empty($aviso['nome_imagem'])): ?>
                    <img src="assets/img/avisos/<?php echo htmlspecialchars($aviso['nome_imagem']); ?>" 
                         class="aviso-image" 
                         alt="Imagem do Aviso">
                <?php endif; ?>
                <div class="aviso-description">
                    <p><?php echo nl2br(htmlspecialchars($aviso['descricao'])); ?></p>
                </div>
                <a href="dashboard.php" class="btn btn-secondary mt-4">Voltar para o Dashboard</a>
            </div>
        <?php else: ?>
            <div class="alert alert-danger alert-custom-page" role="alert">
                <strong>Erro:</strong> <?php echo htmlspecialchars($mensagem_erro); ?>
                <br><a href="dashboard.php" class="btn btn-danger mt-3">Voltar para o Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="ql-editor">
        <p class="ql-align-center">centro</p>
        <p>esquerda</p>
        <p class="ql-align-right">direita</p>
        <p class="ql-align-justify">justificado</p>
        <p class="ql-align-justify"><br></p>
        <p class="ql-align-justify"><em>italico</em></p>
        <p class="ql-align-justify"><strong>negrito</strong></p>
        <p class="ql-align-justify"><u>underline</u></p>
        <p class="ql-align-justify"><s>tachado</s></p>
        <p class="ql-align-justify"><br></p>
        <ol>
            <li class="ql-align-justify">aaaaa</li>
            <li class="ql-align-justify">bbbbb</li>
        </ol>
        <p class="ql-align-justify"><br></p>
        <ul>
            <li class="ql-align-justify">a1111</li>
            <li class="ql-align-justify">b1111</li>
        </ul>
        <p class="ql-align-justify"><br></p>
        <p class="ql-align-justify ql-indent-1">tab</p>
        <p class="ql-align-justify ql-indent-2">tabtab</p>
    </div>
    <!-- Incluindo Bootstrap JS (Bundle com Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
