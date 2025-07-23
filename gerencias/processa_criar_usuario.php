<?php
session_start();
// Define o cabeçalho para indicar que a resposta será um JSON
header('Content-Type: application/json');

require_once "conexaoBanco.php";

if(!isset($_POST["usuario"]) ||
   !isset($_POST["nome"]) ||
   !isset($_POST["territorio"]) ||
   !isset($_POST["permissoes"]) ||
   !isset($_POST["permissoesAdm"])){
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
    if(strlen($_POST["usuario"]) === 11){
        if(!validarCPF($_POST["usuario"])){
            $mensagem = "CPF inválido";
            $dados = [];
            $resposta = [
                "mensagem" => $mensagem,
                "dados" => $dados
            ];
            echo json_encode($resposta, JSON_PRETTY_PRINT);
            exit();
        }
    }
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
    if(empty($_POST["nome"])){
        $mensagem = "Nome Completo inválido";
        $dados = [];
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => $dados
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
        exit();
    }
    if($_POST["territorio"] == "0"){
        $mensagem = "Território inválido";
        $dados = [];
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => $dados
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
        exit();
    }
    
    $idUsuario = $_SESSION['usuario']['id'];
    
    $usuario=$mysqli -> real_escape_string($_POST["usuario"]);
    $usuario = preg_replace('/[.\-\/\\\\s]/', ' ', $usuario);
    
    $nome=$mysqli -> real_escape_string($_POST["nome"]);
    $nome = removerAcentos($nome);
    $nome = preg_replace('/\s+/', ' ', $nome);
    $nome = trim($nome);
    $nome = mb_strtoupper($nome, 'UTF-8');

    $territorio=$mysqli -> real_escape_string($_POST["territorio"]);
    $permissoes=$mysqli -> real_escape_string($_POST["permissoes"]);
    $permissoesAdm=$mysqli -> real_escape_string($_POST["permissoesAdm"]);

    if (str_contains($permissoesAdm, '1')) {
        $permissoes.='1';
        $permissoes.=$permissoesAdm;
    }
    else {
        $permissoes.='0';
        $permissoes.=$permissoesAdm;
    }
    $permissoes = "E" . $permissoes;
    $mysqli->begin_transaction();
    $sql='INSERT INTO usuarios(nome, 
                               usuario, 
                               senha, 
                               territorio_id, 
                               ativo, 
                               permissoes, 
                               primeiro_acesso, 
                               id_usuario_criacao, 
                               data_hora_criacao, 
                               id_usuario_atualizacao, 
                               data_hora_atualizacao) 
           VALUES (?,?,?,?,1,?,1,?,NOW(),?,NOW())';
    $stmt = $mysqli->prepare($sql);
    $senha=password_hash("Pmsbc@123", PASSWORD_DEFAULT);
    $stmt->bind_param('sssisii', 
                        $nome,
                        $usuario,
                        $senha,
                        $territorio,
                        $permissoes,
                        $idUsuario,
                        $idUsuario);

    if ($stmt->execute()) {
        $mysqli->commit();
        $mensagem = "Sucesso";
        $dados = ["Usuário cadastrado com sucesso"];
        $resposta = [
            "mensagem" => $mensagem,
            "dados" => $dados
        ];
        echo json_encode($resposta, JSON_PRETTY_PRINT);
    }
    else{
        $mysqli->rollback();
        $mensagem = "Falha ao cadastrar o usuário";
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
