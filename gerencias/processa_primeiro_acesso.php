<?php
session_start();
// Define o cabeçalho para indicar que a resposta será um JSON
header('Content-Type: application/json');

require_once "conexaoBanco.php";

if(!isset($_POST["novaSenha"])){
    $mensagem = "Falha. Nova senha não recebida";
    $dados = [];
    $resposta = [
        "mensagem" => $mensagem,
        "dados" => $dados
    ];
    echo json_encode($resposta, JSON_PRETTY_PRINT);
    exit();
}
else{
    $idUsuario = $_SESSION['usuario']['id'];
    $novaSenha=$mysqli -> real_escape_string($_POST["novaSenha"]);
    if (!empty($novaSenha) && strlen($novaSenha) >= 6) {
        $novaSenha=password_hash($novaSenha, PASSWORD_DEFAULT);
        $mysqli->begin_transaction();
        $stmt = $mysqli->prepare("UPDATE usuarios SET senha=?, primeiro_acesso=0 WHERE id =?");
        $stmt->bind_param('si', $novaSenha,$idUsuario);

        if ($stmt->execute()) {
            $mysqli->commit();
            $mensagem = "Senha atualizada com sucesso";
            $dados = ["Senha atualizada com sucesso"];
            $resposta = [
                "mensagem" => $mensagem,
                "dados" => $dados
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
        }
        else{
            $mysqli->rollback();
            $mensagem = "Falha ao atualizar a senha";
            $dados = [];
            $resposta = [
                "mensagem" => $mensagem,
                "dados" => $dados
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
        }
        
    } else {
        $mensagem = "Falha ao checar a senha";
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
