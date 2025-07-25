<?php
//gerencias/buscarPessoa.php
require_once 'conexaoBanco.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

class Pessoa{
    private $mysqli;

    // Construtor que recebe o objeto $mysqli
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    public function buscarTodos(){
        $sql="SELECT id, nome FROM pessoas ORDER BY nome";
        $stmt = $this->mysqli->prepare($sql);
    
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado ->num_rows;
            if($linhas > 0){
                while($row = $resultado->fetch_assoc()) {
                    $territoriosEncontrados[] = [
                        'id' => $row['id'],
                        'nome' => $row['nome']
                    ];
                }
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $territoriosEncontrados]);
                $stmt->close();
                exit();
            }
            else{
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                $stmt->close();
                exit();
            }
        }
    }

    public function buscarTodosAtivos(){
        $sql="SELECT id, nome FROM pessoas WHERE ativo=1 ORDER BY nome";
        $stmt = $this->mysqli->prepare($sql);
    
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado ->num_rows;
            if($linhas > 0){
                while($row = $resultado->fetch_assoc()) {
                    $territoriosEncontrados[] = [
                        'id' => $row['id'],
                        'nome' => $row['nome']
                    ];
                }
                $stmt->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $territoriosEncontrados]);
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
        $sql="SELECT id, nome,data_nascimento,id_sexo, ativo FROM pessoas WHERE id=? ORDER BY nome";
        $stmt = $this->mysqli->prepare($sql);
        $stmt -> bind_param('i', $id);
    
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado ->num_rows;
            if($linhas > 0){
                while($row = $resultado->fetch_assoc()) {
                    $territoriosEncontrados[] = [
                        'id' => $row['id'],
                        'nome' => $row['nome'],
                        'data_nascimento' => $row['data_nascimento'],
                        'id_sexo' => $row['id_sexo'],
                        'ativo' => $row['ativo']
                    ];
                }
                $stmt->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $territoriosEncontrados]);
                exit();
            }
            else{
                $stmt->close();
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                exit();
            }
        }
    }
    public function criarPessoa($nome,$nascimento,$sexo){
        $usuarioLogado = $_SESSION['usuario']['id'];

        $sql="INSERT INTO pessoas(
                                    nome, 
                                    data_nascimento, 
                                    id_sexo, 
                                    ativo, 
                                    id_usuario_criacao, 
                                    data_hora_criacao, 
                                    id_usuario_atualizacao, 
                                    data_hora_atualizacao) 
                            VALUES (?,?,?,1,?,NOW(),?,NOW())";
        $stmt = $this->mysqli->prepare($sql);
        $stmt -> bind_param('ssiii', $nome,$nascimento,$sexo,$usuarioLogado,$usuarioLogado);
    
        if ($stmt->execute()) {
            if($this->mysqli->affected_rows > 0){
                $stmt->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => []]);
                exit();
            }
            else{
                $stmt->close();
                return json_encode(['mensagem' => 'Não foi possível registrar', 'dados' => []]);
                exit();
            }
        }
    }
    public function updateDados($nome,$nascimento,$sexo,$ativo,$id){
        $usuarioLogado = $_SESSION['usuario']['id'];

        $sql="UPDATE pessoas SET
                                nome=?, 
                                data_nascimento=?, 
                                id_sexo=?, 
                                ativo=?, 
                                id_usuario_atualizacao=?, 
                                data_hora_atualizacao=NOW()
                             WHERE id=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt -> bind_param('ssiiii', $nome,$nascimento,$sexo,$ativo,$usuarioLogado,$id);
    
        if ($stmt->execute()) {
            if($this->mysqli->affected_rows > 0){
                $stmt->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => []]);
                exit();
            }
            else{
                $stmt->close();
                return json_encode(['mensagem' => 'Não foi possível registrar', 'dados' => []]);
                exit();
            }
        }
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
    else if($tipo=="updateDados"){
        $nome='';
        $data_nascimento='';
        $id_sexo=0;
        $id=0;
        $ativo=2;

        if (!isset($_POST['nome']) || empty($_POST['nome']) || strlen($_POST['nome'])<5) {
            echo json_encode(['mensagem' => 'Erro no nome.', 'dados' => []]);
            exit();
        }
        else{
            
            $nome=$_POST['nome'];
            $nome = removerAcentos($nome);
            $nome = preg_replace('/\s+/', ' ', $nome);
            $nome = trim($nome);
            $nome = mb_strtoupper($nome, 'UTF-8');
        }
        if (!isset($_POST['data_nascimento']) || empty($_POST['data_nascimento'])) {
            echo json_encode(['mensagem' => 'Erro no nascimento.', 'dados' => []]);
            exit();
        }
        else{
            $data_nascimento=$_POST['data_nascimento'];
        }
        if (!isset($_POST['id_sexo']) || $_POST['id_sexo']==0) {
            echo json_encode(['mensagem' => 'Erro no sexo.', 'dados' => []]);
            exit();
        }
        else{
            $id_sexo=$_POST['id_sexo'];
        }
        if (!isset($_POST['id']) || $_POST['id']==0) {
            echo json_encode(['mensagem' => 'Erro na pessoa.', 'dados' => []]);
            exit();
        }
        else{
            $id=$_POST['id'];
        }
        if (!isset($_POST['ativo']) || $_POST['ativo']>1) {
            echo json_encode(['mensagem' => 'Erro na inatividade.', 'dados' => []]);
            exit();
        }
        else{
            $ativo=$_POST['ativo'];
        }

        echo $pessoa->updateDados($nome,$data_nascimento,$id_sexo,$ativo,$id);

    }
    
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