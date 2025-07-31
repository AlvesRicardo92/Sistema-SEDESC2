<?php
//gerencias/Procedimento.php

// Define um manipulador de erros global para capturar erros e exceções não capturadas
set_error_handler(function ($severity, $message, $file, $line) {
    // Se o nível de erro não estiver incluído no relatório de erros atual, ignora
    if (!(error_reporting() & $severity)) {
        return false;
    }
    // Loga o erro não tratado
    error_log("Erro PHP Não Tratado: {$message} em {$file} na linha {$line} (Severidade: {$severity})");
    // Retorna uma resposta JSON genérica para o cliente
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode(['mensagem' => 'Erro interno do servidor. Por favor, tente novamente mais tarde. (Detalhes no log do servidor)', 'dados' => []]);
    exit();
}, E_ALL); // Captura todos os tipos de erros

set_exception_handler(function (Throwable $exception) {
    // Loga a exceção não capturada
    error_log("Exceção PHP Não Tratada: " . $exception->getMessage() . " em " . $exception->getFile() . " na linha " . $exception->getLine());
    // Retorna uma resposta JSON genérica para o cliente
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode(['mensagem' => 'Erro interno do servidor. Por favor, tente novamente mais tarde. (Detalhes no log do servidor)', 'dados' => []]);
    exit();
});

// Register a shutdown function to catch fatal errors that might occur very early
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING])) {
        // This is a fatal error, which means our error handlers might not have been fully executed.
        // Log it as a critical error.
        error_log("ERRO FATAL DE SHUTDOWN: " . $error['message'] . " em " . $error['file'] . " na linha " . $error['line']);
        // Attempt to send a JSON response if headers haven't been sent
        if (!headers_sent()) {
            header('Content-Type: application/json');
            echo json_encode(['mensagem' => 'Erro crítico interno do servidor. Por favor, verifique os logs para mais detalhes.', 'dados' => []]);
        }
    }
});


// Inicia a sessão PHP se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializa $mysqli como null para evitar avisos de variável indefinida
$mysqli = null;

// --- Bloco de Execução Principal ---
// Este bloco será executado quando o ficheiro for acedido diretamente via AJAX.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inicia um bloco try-catch mais abrangente para capturar erros iniciais
    try {
        // Tenta incluir o ficheiro de conexão com o banco de dados
        $conexaoBancoPath = 'conexaoBanco.php';

        // Verifica se o arquivo de conexão existe antes de tentar incluí-lo
        if (!file_exists($conexaoBancoPath)) {
            throw new Exception("O arquivo de conexão '{$conexaoBancoPath}' não foi encontrado. Código: 12");
        }

        // Inclui o arquivo de conexão. Usamos @ para suprimir avisos/erros se o arquivo tiver problemas de sintaxe,
        // mas a verificação subsequente de $mysqli ainda deve capturar a falha.
        @require_once $conexaoBancoPath;

        // Verifica se a conexão com o banco de dados ($mysqli) está disponível e é válida
        // Se conexaoBanco.php não definir $mysqli corretamente, este é o ponto de falha.
        // É CRÍTICO que $mysqli seja uma instância válida de mysqli aqui.
        if (!isset($mysqli) || !$mysqli instanceof mysqli || $mysqli->connect_error) {
            $errorDetails = 'Conexão mysqli não inicializada ou inválida.';
            if (isset($mysqli) && $mysqli instanceof mysqli && $mysqli->connect_error) {
                $errorDetails = $mysqli->connect_error; // Detalhes do erro de conexão mysqli
            } else if (isset($mysqli) && !$mysqli instanceof mysqli) {
                $errorDetails = 'Variável $mysqli existe, mas não é uma instância de mysqli.';
            }
            throw new Exception("Falha na inicialização da conexão com o banco de dados: {$errorDetails}. Código: 6");
        }

        // Instancia a classe Procedimento, passando a conexão $mysqli
        $procedimento = new Procedimento($mysqli);

        // Verifica se o parâmetro 'tipo' foi enviado via POST
        if (isset($_POST['tipo'])) {
            $tipo = $_POST['tipo'];
        } else {
            // Se 'tipo' não for definido, lança uma exceção com um novo código de erro
            throw new Exception('Parâmetro "tipo" não fornecido. Código: 7');
        }

        // Lógica para diferentes tipos de operações
        if ($tipo == "porNumeroAnoEterritorio") {
            // Verifica se os dados necessários foram enviados via POST para esta operação
            if (!isset($_POST['numero']) || !isset($_POST['ano']) || !isset($_POST['territorio'])) {
                throw new Exception('Dados inválidos para busca por número, ano e território. Código: 8');
            } else {
                $numero = $_POST['numero'];
                $ano = $_POST['ano'];
                $territorio = $_POST['territorio'];

                echo $procedimento->buscarPorNumeroAnoEterritorio($numero, $ano, $territorio);
            }
        } else if ($tipo == "migrar") {
            // Verifica se os dados necessários foram enviados via POST para esta operação
            if (!isset($_POST['id']) || !isset($_POST['territorio']) || !isset($_POST['motivo'])) {
                throw new Exception('Dados inválidos para migração. Código: 9');
            } else {
                $id_procedimento_original = $_POST['id'];
                $novo_territorio_id = $_POST['territorio'];
                $motivo_migracao_id = $_POST['motivo'];

                echo $procedimento->migrarProcedimento($id_procedimento_original, $novo_territorio_id, $motivo_migracao_id);
            }
        }
        // Se o tipo de operação não for reconhecido, lança uma exceção
        else {
            throw new Exception('Tipo de operação inválido. Código: 10');
        }

    } catch (Exception $e) {
        // Captura qualquer exceção que ocorra no bloco principal
        // Loga a mensagem de erro completa e o código da exceção para depuração
        error_log("Erro no bloco principal de execução (Código: " . ($e->getCode() ? $e->getCode() : '0') . "): " . $e->getMessage());
        // Retorna uma mensagem de erro para o cliente em formato JSON
        echo json_encode(['mensagem' => 'Erro inesperado durante a operação. ' . $e->getMessage(), 'dados' => []]);
        exit(); // Garante que o script pare após um erro fatal
    }
}

// Definição da classe Procedimento (mantida como está, sem alterações neste segmento)
class Procedimento{
    private $mysqli;

    // Construtor que recebe o objeto $mysqli
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    // Método para buscar todos os procedimentos (não implementado na requisição, mas mantido)
    public function buscarTodos(){

    }

    // Método para buscar todos os procedimentos ativos (não implementado na requisição, mas mantido)
    public function buscarTodosAtivos(){

    }

    // Método para buscar procedimento por ID
    public function buscarPorId($id){
        $sql = "SELECT
                    p.id AS 'id_pessoa',
                    p.nome AS 'nome_pessoa',
                    p.data_nascimento AS 'data_nascimento_pessoa',
                    proc.numero_procedimento AS 'numero_procedimento_original',
                    proc.ano_procedimento AS 'ano_procedimento_original',
                    proc.id_territorio AS 'id_territorio_original',
                    proc.id_genitora_pessoa AS 'id_genitora',
                    proc.data_criacao AS 'data_cadastro',
                    proc.id_usuario_criacao AS 'id_usuario_cadastro'
                FROM
                    procedimentos proc
                LEFT JOIN pessoas p ON
                    p.id = proc.id_pessoa
                WHERE
                    proc.id = ?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Erro na preparação da consulta buscarPorId: " . $this->mysqli->error);
            return null;
        }
        $stmt -> bind_param('i', $id);

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            if($resultado->num_rows > 0){
                $data = $resultado->fetch_assoc();
                $stmt->close();
                return $data;
            }
            else{
                $stmt->close();
                return null;
            }
        } else {
            error_log("Erro na execução da consulta buscarPorId: " . $stmt->error);
            $stmt->close();
            return null;
        }
    }

    public function buscarPorNumero($numero){}
    public function buscarPorNumeroEano($numero, $ano){}

    // Método para buscar procedimento por número, ano e território
    public function buscarPorNumeroAnoEterritorio($numero, $ano, $territorio){
        $sql="  SELECT
                    proc.id AS 'id',
                    proc.migrado as 'migrado',
                    m.numero_novo as 'numero_novo',
                    m.ano_novo as 'ano_novo',
                    m.territorio_novo as 'territorio_novo',
                    p.nome AS 'nome',
                    p.data_nascimento AS 'nascimento'
                FROM
                    procedimentos proc
                LEFT JOIN pessoas p ON
                    p.id = proc.id_pessoa
                LEFT JOIN migracoes m ON
                m.id = proc.id_migracao
                WHERE
                    proc.numero_procedimento = ? AND
                    proc.ano_procedimento = ? AND
                    proc.id_territorio = ?";

        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Erro na preparação da consulta buscarPorNumeroAnoEterritorio: " . $this->mysqli->error);
            return json_encode(['mensagem' => 'Erro interno do servidor.', 'dados' => []]);
        }
        $stmt -> bind_param('iii', $numero,$ano,$territorio);

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            if($resultado->num_rows > 0){
                $procedimentosEncontrados = [];
                while($row = $resultado->fetch_assoc()) {
                    $procedimentosEncontrados[] = [
                        'id' => $row['id'],
                        'nome' => $row['nome'],
                        'nascimento' => $row['nascimento'],
                        'migrado' => $row['migrado'],
                        'numero_novo' => $row['numero_novo'],
                        'ano_novo' => $row['ano_novo'],
                        'territorio_novo' => $row['territorio_novo'],
                    ];
                }
                $stmt->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $procedimentosEncontrados]);
            }
            else{
                $stmt->close();
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
            }
        } else {
            error_log("Erro na execução da consulta buscarPorNumeroAnoEterritorio: " . $stmt->error);
            $stmt->close();
            return json_encode(['mensagem' => 'Erro interno do servidor.', 'dados' => []]);
        }
    }

    // Método para buscar o último número de procedimento para um dado território
    public function buscarUltimoNumeroProcedimentoPorTerritorio($territorio){
        $sql = "SELECT
                    MAX(numero_procedimento) AS 'maximo'
                FROM
                    procedimentos
                WHERE
                    id_territorio = ?";

        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Erro na preparação da consulta buscarUltimoNumeroProcedimentoPorTerritorio: " . $this->mysqli->error);
            return 0; // Retorna 0 em caso de erro ou nenhum procedimento
        }
        $stmt -> bind_param('i', $territorio);

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();
            $stmt->close();
            return $row['maximo'] ? $row['maximo'] : 0; // Retorna o máximo ou 0 se não houver
        } else {
            error_log("Erro na execução da consulta buscarUltimoNumeroProcedimentoPorTerritorio: " . $stmt->error);
            $stmt->close();
            return 0;
        }
    }

    // Método para cadastrar um novo procedimento
    public function cadastrarNovoProcedimento($numero, $ano, $id_territorio, $id_pessoa, $id_genitora, $data_cadastro, $id_usuario_cadastro){
        $sql = "INSERT INTO procedimentos (
                    numero_procedimento,
                    ano_procedimento,
                    id_territorio,
                    id_pessoa,
                    id_genitora_pessoa,
                    data_criacao,
                    id_usuario_criacao,
                    migrado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 0)"; // migrado = 0 por padrão para novos procedimentos

        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Erro na preparação da consulta cadastrarNovoProcedimento: " . $this->mysqli->error);
            return false;
        }
        $stmt->bind_param('iiiiisi', $numero, $ano, $id_territorio, $id_pessoa, $id_genitora, $data_cadastro, $id_usuario_cadastro);

        if ($stmt->execute()) {
            $new_id = $this->mysqli->insert_id;
            $stmt->close();
            return $new_id; // Retorna o ID do novo procedimento
        } else {
            error_log("Erro na execução da consulta cadastrarNovoProcedimento: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Método para registrar a migração
    public function cadastrarMigracao($numero_antigo, $ano_antigo, $territorio_antigo, $numero_novo, $ano_novo, $territorio_novo, $id_motivo_migracao, $id_usuario_criacao){
        $sql =" INSERT INTO migracoes(
                    numero_antigo,
                    ano_antigo,
                    territorio_antigo,
                    numero_novo,
                    ano_novo,
                    territorio_novo,
                    id_motivo_migracao,
                    id_usuario_criacao,
                    data_hora_criacao
                )
                VALUES(
                    ?, ?, ?, ?, ?, ?, ?, ?, NOW()
                )";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Erro na preparação da consulta cadastrarMigracao: " . $this->mysqli->error);
            return false;
        }
        $stmt -> bind_param('iiiiiiii', $numero_antigo, $ano_antigo, $territorio_antigo, $numero_novo, $ano_novo, $territorio_novo, $id_motivo_migracao, $id_usuario_criacao);

        if ($stmt->execute()) {
            $new_id = $this->mysqli->insert_id;
            $stmt->close();
            return $new_id; // Retorna o ID da nova migração
        } else {
            error_log("Erro na execução da consulta cadastrarMigracao: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Método para atualizar o procedimento original
    public function atualizarDadosMigracao($idProcedimento, $idMigracao){
        // A variável $usuarioLogado é definida dentro da função migrarProcedimento antes de chamar esta função
        // Se esta função for chamada independentemente, $_SESSION['usuario']['id'] deve ser acessível
        $usuarioLogado = isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : null;

        $sql =  "UPDATE
                    procedimentos
                SET
                    migrado = 1,
                    id_migracao = ?,
                    id_usuario_atualizacao = ?,
                    data_hora_atualizacao = NOW()
                WHERE
                    id = ?";

        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            error_log("Erro na preparação da consulta atualizarDadosMigracao: " . $this->mysqli->error);
            return false;
        }
        $stmt -> bind_param('iii', $idMigracao, $usuarioLogado, $idProcedimento);

        if ($stmt->execute()) {
            if($this->mysqli->affected_rows > 0){
                $stmt->close();
                return true;
            }
            else{
                $stmt->close();
                return false;
            }
        } else {
            error_log("Erro na execução da consulta atualizarDadosMigracao: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Método principal para realizar a migração completa
    public function migrarProcedimento($id_procedimento_original, $novo_territorio_id, $motivo_migracao_id){
        $this->mysqli->begin_transaction(); // Inicia a transação
        $erro_code = 0; // Inicializa o código de erro para depuração

        try {
            // Verifica se o ID do usuário está na sessão
            if (!isset($_SESSION['usuario']['id'])) {
                $erro_code = 5; // Novo código de erro para usuário não logado
                throw new Exception('Usuário não autenticado. ID da sessão não encontrado.');
            }
            $usuarioLogado = $_SESSION['usuario']['id'];

            // 1. Buscar informações do procedimento original
            $procedimento_original = $this->buscarPorId($id_procedimento_original);
            if (!$procedimento_original) {
                $erro_code = 1; // Define o código de erro para esta etapa
                throw new Exception('Procedimento original não encontrado.');
            }

            // 2. Encontrar o último número de procedimento para o novo território
            $ultimo_numero = $this->buscarUltimoNumeroProcedimentoPorTerritorio($novo_territorio_id);
            $novo_numero_procedimento = $ultimo_numero + 1;
            $ano_atual = date('Y'); // Ano atual para o novo procedimento

            // 3. Cadastrar o novo procedimento
            $data_cadastro_novo = date('Y-m-d H:i:s'); // Usar data e hora atual para o novo cadastro

            $novo_procedimento_id = $this->cadastrarNovoProcedimento(
                $novo_numero_procedimento,
                $ano_atual,
                $novo_territorio_id,
                $procedimento_original['id_pessoa'],
                $procedimento_original['id_genitora'],
                $data_cadastro_novo, // Usar a data/hora atual para o novo registro
                $usuarioLogado // Usuário que está realizando a migração
            );

            if (!$novo_procedimento_id) {
                $erro_code = 2; // Define o código de erro para esta etapa
                throw new Exception('Falha ao cadastrar novo procedimento.');
            }

            // 4. Registrar a migração na tabela 'migracoes'
            $id_migracao = $this->cadastrarMigracao(
                $procedimento_original['numero_procedimento_original'],
                $procedimento_original['ano_procedimento_original'],
                $procedimento_original['id_territorio_original'],
                $novo_numero_procedimento,
                $ano_atual,
                $novo_territorio_id,
                $motivo_migracao_id,
                $usuarioLogado // Usuário que está realizando a migração
            );

            if (!$id_migracao) {
                $erro_code = 3; // Define o código de erro para esta etapa
                throw new Exception('Falha ao registrar a migração.');
            }

            // 5. Atualizar o procedimento original (marcar como migrado e linkar com a migração)
            $atualizacao_sucesso = $this->atualizarDadosMigracao($id_procedimento_original, $id_migracao);

            if (!$atualizacao_sucesso) {
                $erro_code = 4; // Define o código de erro para esta etapa
                throw new Exception('Falha ao atualizar procedimento original.');
            }

            $this->mysqli->commit(); // Confirma todas as operações
            return json_encode(['mensagem' => 'Sucesso', 'dados' => [
                'novo_numero' => $novo_numero_procedimento,
                'novo_ano' => $ano_atual,
                'novo_territorio' => $novo_territorio_id

            ]]);

        } catch (Exception $e) {
            $this->mysqli->rollback(); // Reverte em caso de qualquer erro
            error_log("Erro na migração do procedimento (Código: {$erro_code}): " . $e->getMessage()); // Loga a mensagem de erro completa e o código
            return json_encode(['mensagem' => 'Erro inesperado durante a migração. Código: ' . $erro_code, 'dados' => []]);
        }
    }
}
?>
