<?php 
	header('Content-Type: application/json; charset=utf-8');
	ini_set('default_charset','utf-8');
    clearstatcache();

	require_once "conexaoBanco.php";
    session_start();
    
    $sql='';

    if ($mysqli->connect_errno) {
        $mensagem = "Falha na conexão com o banco de dados: " . $mysqli->connect_error;
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => []
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
        exit();
    }
    
    $sql = "SELECT
                id,
                nome
            FROM
                demandantes
            WHERE
                ativo = 1
            ORDER BY nome";        
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        $mensagem = "Falha ao preparar a query: " . $mysqli->error;
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => []
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
        exit();
    }
    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        $linhas = $resultado ->num_rows;
        if($linhas>0){ 
            while($row = $resultado->fetch_assoc()) {
                $demandantesEncontrados[] = [
                    'id' => $row['id'],
                    'nome' => $row['nome']
                ];
            }
            $resultado -> free_result();
            $resposta = [
                "mensagem" => "Sucesso",
                "dados" => $demandantesEncontrados
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
        }
        else{
            $resultado -> free_result();
            $mensagem = "Falha. Nenhum demandante encontrado";
            $dados = [];
            $resposta = [
                "mensagem" => $mensagem,
                "dados" => $dados
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
        }
    }
    else{
        $mensagem = "Falha. Erro ao executar a query";
        $dados = [];
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => $dados
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
    }
    $stmt->close();
    $mysqli->close();
?>