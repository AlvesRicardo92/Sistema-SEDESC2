<?php
session_start(); // Inicia a sessão para acessar os dados do usuário logado

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: index.php');
    exit();
}

require __DIR__ . "/gerencias/conexaoBanco.php"; // Inclui o arquivo de conexão com o banco de dados
require __DIR__ . '/utils/cabecalho.php'; // Inclui o cabeçalho da página

$territorios = [];
// Busca os territórios para preencher o select
$sql_territorios = "SELECT id, nome FROM territorios_ct WHERE ativo = 1 ORDER BY nome ASC";
$resultado_territorios = $mysqli->query($sql_territorios);
if ($resultado_territorios) {
    while ($row = $resultado_territorios->fetch_assoc()) {
        $territorios[] = $row;
    }
    $resultado_territorios->free();
} else {
    // Em caso de erro na consulta de territórios, logar ou exibir mensagem
    error_log("Erro ao buscar territórios: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Aviso</title>
    <!-- Incluindo Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluindo seu CSS personalizado -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Incluindo Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .container-form {
            max-width: 900px;
            margin: 30px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .modal-footer-custom {
            justify-content: flex-end;
            border-top: none;
            padding-top: 20px;
        }
        #avisoMessage {
            margin-bottom: 20px;
        }
        /* Estilo para o editor Quill */
        #editor-container {
            height: 300px; /* Altura do editor */
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4 text-primary">Criar Novo Aviso</h2>

        <div class="container-form">
            <!-- Div para mensagens de sucesso/erro -->
            <div id="avisoMessage" class="alert d-none" role="alert">
                <!-- Mensagens serão inseridas aqui pelo JavaScript -->
            </div>

            <form id="formCriarAviso" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="carouselImage" class="form-label">Imagem para o Carrossel (opcional)</label>
                    <input class="form-control" type="file" id="carouselImage" name="carousel_image" accept="image/*">
                    <small class="form-text text-muted">Será exibida no carrossel do Dashboard.</small>
                </div>

                <div class="mb-3">
                    <label for="idTerritorioExibicao" class="form-label">Território de Exibição</label>
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
                        <label for="dataInicioExibicao" class="form-label">Data de Início da Exibição</label>
                        <input type="date" class="form-control" id="dataInicioExibicao" name="data_inicio_exibicao" required>
                    </div>
                    <div class="col-md-6">
                        <label for="dataFimExibicao" class="form-label">Data de Fim da Exibição</label>
                        <input type="date" class="form-control" id="dataFimExibicao" name="data_fim_exibicao" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="avisoContent" class="form-label">Conteúdo do Aviso</label>
                    <!-- O Quill será inicializado nesta div -->
                    <div id="editor-container"></div>
                    <!-- Campo oculto para enviar o conteúdo HTML do Quill -->
                    <input type="hidden" name="descricao" id="hiddenAvisoContent">
                </div>

                <div class="modal-footer modal-footer-custom">
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
                theme: 'snow', // Tema 'snow' (barra de ferramentas no topo)
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],        // negrito, itálico, sublinhado, tachado
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],    // listas ordenadas/não ordenadas
                        [{ 'indent': '-1'}, { 'indent': '+1' }],          // indentação
                        [{ 'align': [] }],                                // alinhamento
                        ['link', 'image', 'video'],                       // link, imagem, vídeo (upload de imagem/vídeo requer backend)
                        ['clean']                                         // remover formatação
                    ]
                }
            });

            // Referência à div de mensagem
            const $avisoMessage = $('#avisoMessage');

            // Função para exibir a mensagem
            function showAvisoMessage(message, type) {
                $avisoMessage.removeClass('d-none alert-success alert-danger').empty();
                $avisoMessage.html(message); // Usa .html() para permitir formatação
                if (type === 'success') {
                    $avisoMessage.addClass('alert-success');
                } else if (type === 'error') {
                    $avisoMessage.addClass('alert-danger');
                }
                $avisoMessage.removeClass('d-none').slideDown();
            }

            // Evento de submissão do formulário
            $('#formCriarAviso').on('submit', function(e) {
                e.preventDefault(); // Previne o envio padrão do formulário

                // Oculta a mensagem anterior
                $avisoMessage.addClass('d-none').empty();

                // Obtém o conteúdo HTML do Quill e o coloca no campo hidden
                const avisoContentHtml = quill.root.innerHTML;
                $('#hiddenAvisoContent').val(avisoContentHtml);

                // Cria um objeto FormData para lidar com o upload de arquivo e outros dados
                const formData = new FormData(this);
                formData.append('acao', 'add_aviso');
                // A 'descricao' já está no formData via o campo hidden #hiddenAvisoContent

                // Desabilita o botão de salvar para evitar múltiplos cliques
                $('#btnSalvarAviso').prop('disabled', true).text('Salvando...');

                $.ajax({
                    url: 'gerencias/processa_avisos.php', // Script PHP que processará os dados
                    type: 'POST',
                    data: formData,
                    processData: false, // Necessário para FormData
                    contentType: false, // Necessário para FormData
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.status === 'sucesso') {
                            showAvisoMessage('Aviso criado com sucesso!', 'success');
                            // Limpa o formulário após o sucesso
                            $('#formCriarAviso')[0].reset();
                            quill.setContents([]); // Limpa o conteúdo do editor Quill
                            // Opcional: Redirecionar para o dashboard ou listar avisos
                            setTimeout(function() {
                                window.location.href = 'dashboard.php';
                            }, 2000); // Redireciona após 2 segundos
                        } else {
                            showAvisoMessage('Erro ao criar aviso: ' + (response ? response.mensagem : 'Resposta inválida do servidor.'), 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Erro de comunicação com o servidor ao criar aviso.';
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse && errorResponse.mensagem) {
                                errorMessage = errorResponse.mensagem;
                            }
                        } catch (e) {
                            console.error("Erro ao analisar resposta de erro:", e, xhr.responseText);
                        }
                        showAvisoMessage(errorMessage, 'error');
                    },
                    complete: function() {
                        // Reabilita o botão de salvar
                        $('#btnSalvarAviso').prop('disabled', false).text('Salvar Aviso');
                    }
                });
            });
        });
    </script>
</body>
</html>
