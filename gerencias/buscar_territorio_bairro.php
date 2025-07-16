<?php 
	header('Content-Type: application/json; charset=utf-8');
	ini_set('default_charset','utf-8');
    clearstatcache();

	require "conexaoBanco.php";
    session_start();
    
    $sql='';

    if(!isset($_POST['id_bairro'])){

    }
    else{
        $idBairro=$_POST['id_bairro'];
    }
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
                t.nome
            FROM
                bairros b
            LEFT JOIN territorios_ct t ON
                t.id = b.territorio_id
            WHERE
                b.id = ?";        
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
    $stmt -> bind_param('i', $idBairro);
    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        $linhas = $resultado ->num_rows;
        if($linhas>0){ 
            while($row = $resultado->fetch_assoc()) {
                $territorioEncontrado[] = [
                    'nome' => $row['nome']
                ];
            }
            $resultado -> free_result();
            $resposta = [
                "mensagem" => "Sucesso",
                "dados" => $territorioEncontrado
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
        }
        else{
            $resultado -> free_result();
            $mensagem = "Falha. Nenhum território encontrado";
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