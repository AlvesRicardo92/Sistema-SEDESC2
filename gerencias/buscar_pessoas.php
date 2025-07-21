<?php 
	header('Content-Type: application/json; charset=utf-8');
	ini_set('default_charset','utf-8');
    clearstatcache();

	require_once "conexaoBanco.php";
    session_start();

    $id='';
    if (isset($_POST['id'])){
        $id=$_POST['id'];
    }
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
                nome,
                data_nascimento,
                id_sexo
            FROM
                pessoas
            WHERE
                ativo = 1";
    if(!empty($id)){
        $sql.=" AND id=?";
    }
    $sql.=" ORDER BY nome";

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
    if(!empty($id)){
        $stmt -> bind_param('i', $id);
    }
    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        $linhas = $resultado ->num_rows;
        if($linhas>0){ 
            while($row = $resultado->fetch_assoc()) {
                $pessoasEncontradas[] = [
                    'id' => $row['id'],
                    'nome' => $row['nome'],
                    'data_nascimento' => $row['data_nascimento'],
                    'id_sexo' => $row['id_sexo']
                ];
            }
            $resultado -> free_result();
            $resposta = [
                "mensagem" => "Sucesso",
                "dados" => $pessoasEncontradas
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
        }
        else{
            $resultado -> free_result();
            $mensagem = "Falha. Nenhuma pessoa encontrada";
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