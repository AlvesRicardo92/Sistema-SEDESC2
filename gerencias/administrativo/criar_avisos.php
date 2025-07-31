<?php
session_start(); // Inicia a sessão para acessar os dados do usuário logado

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
}

require_once __DIR__ . "/../conexaoBanco.php"; // Inclui o arquivo de conexão com o banco de dados

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Aviso</title>
    <!-- Incluindo Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluindo Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Incluindo seu CSS personalizado -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4 text-primary">Criar Novo Aviso</h2>

        <div class="criarAviso container-form">
            <!-- Div para mensagens de sucesso/erro -->
            <div id="avisoMessage" class="alert d-none criarAviso" role="alert">
                <!-- Mensagens serão inseridas aqui pelo JavaScript -->
            </div>

            <form id="formCriarAviso" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="carouselImage" class="criarAviso form-label">Imagem para o Carrossel (opcional)</label>
                    <input class="form-control" type="file" id="carouselImage" name="carousel_image" accept="image/*">
                    <small class="form-text text-muted">Será exibida no carrossel do Dashboard.</small>
                </div>

                <div class="mb-3">
                    <label for="idTerritorioExibicao" class="criarAviso form-label">Território de Exibição</label>
                    <select class="form-select" id="idTerritorioExibicao" name="id_territorio_exibicao" required>
                        <option value="">Selecione o Território</option>
                        <?php foreach ($territorios as $territorio): ?>
                            <option value="<?php echo htmlspecialchars($territorio['id']); ?>">
                                <?php echo htmlspecialchars($territorio['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="dataInicioExibicao" class="criarAviso form-label">Data de Início da Exibição</label>
                        <input type="date" class="form-control" id="dataInicioExibicao" name="data_inicio_exibicao" required>
                    </div>
                    <div class="col-md-6">
                        <label for="dataFimExibicao" class="criarAviso form-label">Data de Fim da Exibição</label>
                        <input type="date" class="form-control" id="dataFimExibicao" name="data_fim_exibicao" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="avisoContent" class="criarAviso form-label">Conteúdo do Aviso</label>
                    <!-- O Quill será inicializado nesta div -->
                    <div id="editor-container" class="criarAviso"></div>
                    <!-- Campo oculto para enviar o conteúdo HTML do Quill -->
                    <input type="hidden" name="descricao" id="hiddenAvisoContent">
                </div>

                <div class="modal-footer criarAviso modal-footer-custom">
                    <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" id="btnSalvarAviso">Salvar Aviso</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Incluindo Bootstrap JS (Bundle com Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Incluindo jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Incluindo Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializa o Quill no editor-container
            const quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'indent': '-1'}, { 'indent': '+1' }],
                            [{ 'align': [] }],
                            ['link', 'image', 'video'],
                            ['clean']
                        ],
                        handlers: {
                            'link': function(value) {
                                if (value) {
                                    let href = prompt('Digite a URL do link:', 'http://'); // Sugere http://
                                    if (href) {
                                        // Se o usuário digitou algo que parece um domínio mas não tem protocolo, adicione http://
                                        if (!href.startsWith('http://') && !href.startsWith('https://') && href.includes('.')) {
                                            href = 'http://' + href;
                                        }
                                        this.quill.format('link', href);
                                    }
                                } else {
                                    this.quill.format('link', false); // Remove o link
                                }
                            }
                        }
                    }
                }
            });

            
    </script>
</body>
</html>
