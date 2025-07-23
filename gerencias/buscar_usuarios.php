<?php 
	header('Content-Type: application/json; charset=utf-8');
	ini_set('default_charset','utf-8');
    clearstatcache();

	require_once "conexaoBanco.php";
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
    if($_POST['tipo']=='usuario'){
        if(!isset($_POST['usuario'])){
            $mensagem = "Falha no usuário ";
            $resposta = [
                "mensagem" => $mensagem,
                "dados" => []
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
            exit();
        }
        else{
            $usuario = $_POST['usuario'];
        }
        $sql = "SELECT
                    nome, 
                    usuario, 
                    ativo
                FROM
                    usuarios 
                WHERE 
                    usuario=?";  
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
        $stmt->bind_param('s', $usuario);
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado ->num_rows;
            if($linhas>0){ 
                while($row = $resultado->fetch_assoc()) {
                    $usuarioEncontrado[] = [
                        'nome' => $row['nome'],
                        'ativo' => $row['ativo']
                    ];
                }
                $resultado -> free_result();
                $resposta = [
                    "mensagem" => "Sucesso",
                    "dados" => $usuarioEncontrado
                ];
                echo json_encode($resposta, JSON_PRETTY_PRINT);
            }
            else{
                $resultado -> free_result();
                $mensagem = "Falha. Nenhum usuário encontrado";
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
    }
?>