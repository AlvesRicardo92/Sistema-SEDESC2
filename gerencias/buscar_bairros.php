<?php 
	header('Content-Type: application/json; charset=utf-8');
	ini_set('default_charset','utf-8');
    clearstatcache();

	require "conexaoBanco.php";
    session_start();
    
    $sql='';

    if(!isset($_POST['tipo'])){
        $mensagem = "Falha no tipo ";
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => []
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
        exit();
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
    $territorioUsuario = $_SESSION['usuario']['territorio'];
    if($_POST['tipo']=='editar'){
        $sql = "SELECT
                    b.id AS id,
                    b.nome AS nome
                FROM
                    bairros b
                LEFT JOIN
                    territorios_ct t ON t.id = b.territorio_id
                WHERE
                    b.ativo = 1";
        if($territorioUsuario<4){
            $sql.= " AND b.territorio_id = ?
                    ORDER BY nome";
        }
        else{
            $sql.=" ORDER BY nome";
        }
        
    }
    else if($_POST['tipo']=='novo'){
        $sql="SELECT
                b.id AS id,
                b.nome AS nome
            FROM
                bairros b
            WHERE
                b.ativo = 1
            ORDER BY nome";
    }
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
    if($_POST['tipo']=='editar'){
        if($territorioUsuario<4){
            $stmt->bind_param('i', $territorioUsuario);
        }
    }
    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        $linhas = $resultado ->num_rows;
        if($linhas>0){ 
            while($row = $resultado->fetch_assoc()) {
                $bairrosEncontrados[] = [
                    'id' => $row['id'],
                    'nome' => $row['nome']
                ];
            }
            $resultado -> free_result();
            $resposta = [
                "mensagem" => "Sucesso",
                "dados" => $bairrosEncontrados
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
        }
        else{
            $resultado -> free_result();
            $mensagem = "Falha. Nenhum bairro encontrado";
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