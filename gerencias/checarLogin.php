<?php 
	header('Content-Type: application/json; charset=utf-8');
	ini_set('default_charset','utf-8');
    clearstatcache();

	require "conexaoBanco.php";



    
    if(!isset($_POST["usuario"])|| !isset($_POST["senha"])){
        $mensagem = "Falha. Erro na recepção dos dados";
        $dados = []
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => $dados
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
        exit();
    }
    else{
        $usuarioPOST = $mysqli -> real_escape_string($_POST["usuario"]);
        $senhaPOST = $mysqli -> real_escape_string($_POST["senha"]);
        $senhaPOST = password_hash($senhaPOST, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("SELECT id, nome, usuario, senha, territorio_id, ativo, permissoes, primeiro_acesso FROM usuarios WHERE usuario like ?");
        $stmt->bind_param('s', $usuarioPOST);

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado ->num_rows;
            if($linhas>0){ 
                while($row = $resultado->fetch_assoc()) {
                    $idBanco= $row['id'];
                    $idNome= $row['nome'];
                    $usuarioBanco = $row['usuario'];
					$senhaBanco = $row['senha'];
					$territorio = $row['territorio_id'];
                    $ativo=$row['ativo'];
                    $permissoes=$row['permissoes'];
                    $primeiroAcesso=$row['primeiro_acesso'];
                }
                $resultado -> free_result();
                if ($senhaPOST == $senhaBanco){
					session_start();
                    $_SESSION['usuario']['id']= $idBanco;
                    $_SESSION['usuario']['nome']= $nomeBanco;
                    $_SESSION['usuario']['territorio']= $territorioBanco;
                    $_SESSION['usuario']['permissoes']= $permissoesBanco;
                    $mensagem = "Sucesso";
                    $dados = [
                        "ativo" => $ativo,
                        "primeiro_acesso" => $primeiroAcesso
                    ]
                    $resposta = [
                        "mensagem" => $mensagem,
                        "dados" => $dados
                    ];
                    echo json_encode($resposta, JSON_PRETTY_PRINT);
				}
            }
            else{
                $resultado -> free_result();
                $mensagem = "Falha. Nenhum usuário encontrado";
                $dados = []
                $resposta = [
                    "mensagem" => $mensagem,
                    "dados" => $dados
                ];
                echo json_encode($resposta, JSON_PRETTY_PRINT);
                exit();
            }
        }
        else{
            $mensagem = "Falha. Erro ao executar a query";
            $dados = []
            $resposta = [
                "mensagem" => $mensagem,
                "dados" => $dados
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
            exit();
        }
        $stmt->close();
        $mysqli->close();
    }
?>