<?php
//gerencias/MotivoMigracao.php

require_once 'conexaoBanco.php';

class MotivoMigracao{
    private $mysqli;

    // Construtor que recebe o objeto $mysqli
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    public function buscarTodos(){
    }

    public function buscarTodosAtivos(){
        $sql="SELECT id, nome FROM motivos_migracao WHERE ativo=1 ORDER BY nome";
        $stmt = $this->mysqli->prepare($sql);
    
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado ->num_rows;
            if($linhas > 0){
                while($row = $resultado->fetch_assoc()) {
                    $motivosMigracaoEncontrados[] = [
                        'id' => $row['id'],
                        'nome' => $row['nome']
                    ];
                }
                $stmt->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $motivosMigracaoEncontrados]);
                exit();
            }
            else{
                $stmt->close();
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                exit();
            }
        }
    }
    public function buscarPorId($id){
        
    }
}
// --- Bloco de Execução Principal ---
// Este bloco será executado quando o arquivo for acessado diretamente via AJAX.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Certifique-se de que a conexão com o banco de dados está disponível
    global $mysqli; // Assume que $mysqli está disponível globalmente após o require_once

    $pessoa = new Pessoa($mysqli);
    if (isset($_POST['tipo'])) {
        $tipo = $_POST['tipo'];
    }
    else{
        echo json_encode(['mensagem' => 'Falha no tipo', 'dados' => []]);
        exit();
    }
    
    if($tipo=="porId"){
        // Verifica se o ID foi enviado via POST
        if (isset($_POST['id'])) {
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT); // Valida e sanitiza o ID

            if ($id !== false && $id > 0) {
                echo $pessoa->buscarPorId($id);
            } else {
                echo json_encode(['mensagem' => 'ID inválido fornecido.', 'dados' => []]);
            }
        }
        else{
            echo json_encode(['mensagem' => 'Nenhum ID ou tipo de busca especificado.', 'dados' => []]);
        }
    }
    else if($tipo=="criar"){
        
    }
    
}
?>