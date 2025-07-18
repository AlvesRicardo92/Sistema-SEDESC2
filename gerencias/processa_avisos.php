<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . "/conexaoBanco.php"; // Inclui o arquivo de conexão com o banco de dados

// Função para sanitizar e normalizar texto (remover acentos, espaços extras, maiúsculas)
function normalizarTexto($text) {
    $text = preg_replace('/\s+/', ' ', $text); // Múltiplos espaços para um único
    $text = trim($text); // Remove espaços no início e no final

    if (class_exists('Normalizer') && method_exists('Normalizer', 'normalize')) {
        $text = Normalizer::normalize($text, Normalizer::FORM_KD);
        $text = preg_replace('/[^\\x00-\\x7F]/u', '', $text);
    } else {
        $search = array(
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß',
            'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ'
        );
        $replace = array(
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'TH', 'ss',
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'th', 'y'
        );
        $text = str_replace($search, $replace, $text);
    }
    
    $text = mb_strtoupper($text, 'UTF-8');
    return $text;
}

$response = ['status' => 'erro', 'mensagem' => 'Ação inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    // Define a variável de sessão MySQL @user_id para triggers
    $id_usuario_logado = $_SESSION['usuario']['id'] ?? null;
    if ($id_usuario_logado) {
        $stmt_set_user_id = $mysqli->prepare("SET @user_id = ?");
        if ($stmt_set_user_id) {
            $stmt_set_user_id->bind_param('i', $id_usuario_logado);
            $stmt_set_user_id->execute();
            $stmt_set_user_id->close();
        }
    }

    if ($acao === 'add_aviso') {
        // Inicia a transação
        $mysqli->autocommit(FALSE);

        try {
            // Validação dos dados do formulário
            $id_territorio_exibicao = filter_var($_POST['id_territorio_exibicao'] ?? '', FILTER_VALIDATE_INT);
            $data_inicio_exibicao = $_POST['data_inicio_exibicao'] ?? '';
            $data_fim_exibicao = $_POST['data_fim_exibicao'] ?? '';
            $descricao = $_POST['descricao'] ?? ''; // Conteúdo do TinyMCE

            var_dump($descricao);
            exit();

            if (!$id_territorio_exibicao || empty($data_inicio_exibicao) || empty($data_fim_exibicao) || empty($descricao)) {
                throw new Exception("Todos os campos obrigatórios (Território, Datas, Conteúdo) devem ser preenchidos.");
            }

            // Validação de datas
            if (strtotime($data_inicio_exibicao) > strtotime($data_fim_exibicao)) {
                throw new Exception("A data de início da exibição não pode ser posterior à data de fim.");
            }

            $nome_imagem = null;
            // Processa o upload da imagem do carrossel
            if (isset($_FILES['carousel_image']) && $_FILES['carousel_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/img/avisos/'; // Caminho para a pasta de imagens dos avisos
//                if (!is_dir($uploadDir)) {
//                    mkdir($uploadDir, 0755, true); // Cria o diretório se não existir
//                }

                $fileTmpPath = $_FILES['carousel_image']['tmp_name'];
                $fileName = basename($_FILES['carousel_image']['name']);
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new Exception("Formato de imagem inválido. Apenas JPG, JPEG, PNG e GIF são permitidos.");
                }

                $newFileName = uniqid('aviso_img_') . '.' . $fileExtension; // Nome único para a imagem
                $destPath = $uploadDir . $newFileName;

                if (!move_uploaded_file($fileTmpPath, $destPath)) {
                    throw new Exception("Falha ao mover o arquivo de imagem para o diretório de destino.");
                }
                $nome_imagem = $newFileName;
            }

            // Prepara a query de inserção
            $sql = "INSERT INTO avisos (
                        descricao, 
                        nome_imagem, 
                        data_inicio_exibicao, 
                        data_fim_exibicao, 
                        id_territorio_exibicao,
                        id_usuario_criacao, 
                        data_hora_criacao, 
                        id_usuario_atualizacao, 
                        data_hora_atualizacao
                    ) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, NOW())";
            
            $stmt = $mysqli->prepare($sql);

            if (!$stmt) {
                throw new Exception("Erro ao preparar a declaração SQL: " . $mysqli->error);
            }

            // Bind dos parâmetros
            // s: descricao, s: nome_imagem, s: data_inicio, s: data_fim, i: id_territorio, i: id_usuario_criacao, i: id_usuario_atualizacao
            $stmt->bind_param(
                'sssiiisii', // String de tipos
                $descricao,
                $nome_imagem,
                $data_inicio_exibicao,
                $data_fim_exibicao,
                $id_territorio_exibicao,
                $_SESSION['usuario']['id'],
                $_SESSION['usuario']['id']
            );

            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar a inserção do aviso: " . $stmt->error);
            }

            $mysqli->commit(); // Confirma a transação
            $response = ['status' => 'sucesso', 'mensagem' => 'Aviso criado com sucesso!'];

        } catch (Exception $e) {
            $mysqli->rollback(); // Desfaz a transação em caso de erro
            $response = ['status' => 'erro', 'mensagem' => $e->getMessage()];
            error_log("Erro ao adicionar aviso: " . $e->getMessage());
        } finally {
            $mysqli->autocommit(TRUE); // Reabilita o autocommit
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}

$mysqli->close(); // Fecha a conexão com o banco de dados
echo json_encode($response);
?>
