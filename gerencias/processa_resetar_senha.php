<?php
session_start();
// Define o cabeçalho para indicar que a resposta será um JSON
header('Content-Type: application/json');

require_once "conexaoBanco.php";

if(!isset($_POST["usuario"])){
    $mensagem = "Falha no Usuário";
    $dados = [];
    $resposta = [
        "mensagem" => $mensagem,
        "dados" => $dados
    ];
    echo json_encode($resposta, JSON_PRETTY_PRINT);
    exit();
}
else{
    if(empty($_POST["usuario"]) || !is_numeric($_POST["usuario"])){
        $mensagem = "Usuário inválido";
        $dados = [];
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => $dados
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
        exit();
    }    
    $idUsuario = $_SESSION['usuario']['id'];
    $ativo=0;

    $usuario=$mysqli -> real_escape_string($_POST["usuario"]);
    $usuario = preg_replace('/[.\-\/\\\\s]/', ' ', $usuario);

    $sql='SELECT usuario,ativo FROM usuarios WHERE usuario=?;';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $usuario);
    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        $linhas = $resultado ->num_rows;
        if($linhas > 0){
            while($row = $resultado->fetch_assoc()) {
                $ativo = $row['ativo'];
            }
            if($ativo==0){
                $mensagem = "Usuário inativo. Não é possível redefinir a senha";
                $dados = [""];
                $resposta = [
                    "mensagem" => $mensagem,
                    "dados" => $dados
                ];
                $stmt->close();
                $mysqli->close();
                echo json_encode($resposta, JSON_PRETTY_PRINT);
            }
            else{
                $mysqli->begin_transaction();
                $sql='UPDATE usuarios 
                      SET
                        senha=?,
                        primeiro_acesso=1,
                        id_usuario_atualizacao=?,
                        data_hora_atualizacao=NOW()
                      WHERE 
                        usuario=?';
                $stmt = $mysqli->prepare($sql);
                $senha=password_hash("Pmsbc@123", PASSWORD_DEFAULT);
                $stmt->bind_param('sis', 
                                   $senha,
                                   $idUsuario,
                                   $usuario);
                if ($stmt->execute()) {
                    $mysqli->commit();
                    $mensagem = "Sucesso";
                    $dados = ["Senha redefinida com sucesso"];
                    $resposta = [
                        "mensagem" => $mensagem,
                        "dados" => $dados
                    ];
                    $stmt->close();
                    $mysqli->close();
                    echo json_encode($resposta, JSON_PRETTY_PRINT);
                }
                else{
                    $mysqli->rollback();
                    $mensagem = "Falha ao redefinir a senha";
                    $dados = [];
                    $resposta = [
                        "mensagem" => $mensagem,
                        "dados" => $dados
                    ];
                    $stmt->close();
                    $mysqli->close();
                    echo json_encode($resposta, JSON_PRETTY_PRINT);
                }
            }
        }
        else{
            $mensagem = "Usuário não encontrado.";
            $dados = [""];
            $resposta = [
                "mensagem" => $mensagem,
                "dados" => $dados
            ];
            $stmt->close();
            $mysqli->close();
            echo json_encode($resposta, JSON_PRETTY_PRINT);
        }
    }   
}
function validarCPF(string $cpf): bool
{
    // 1. Limpa o CPF: remove pontos, hífens e outros caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);

    // 2. Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) !== 11) {
        return false;
    }

    // 3. Verifica se todos os dígitos são iguais (padrões inválidos conhecidos)
    // Ex: 111.111.111-11, 222.222.222-22, etc.
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // 4. Calcula o primeiro dígito verificador
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }

    // Se passou por todas as verificações, o CPF é válido
    return true;
}
function removerAcentos(string $text): string
{
    $search = [
        'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß',
        'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ',
        "'"
    ];
    $replace = [
        'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'TH', 'ss',
        'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'th', 'y',
        " "
    ];
    $text = str_replace($search, $replace, $text);
    
    return $text;
}
?>
