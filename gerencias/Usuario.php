<?php
//gerencias/Usuario.php

require_once 'conexaoBanco.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
class Usuario{
    private $mysqli;

    // Construtor que recebe o objeto $mysqli
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    public function buscarTodos(){
        $sql="SELECT id, nome FROM usuarios ORDER BY nome";
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
        $sql="SELECT id, nome FROM usuarios WHERE ativo=1 ORDER BY nome";
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
        $sql="SELECT id, nome,data_nascimento,id_sexo, ativo FROM usuarios WHERE id=? ORDER BY nome";
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

    public function buscarPorUsuario($usuario){
        $sql="SELECT id, nome, territorio_id, ativo, permissoes, primeiro_acesso FROM usuarios WHERE usuario=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt -> bind_param('s', $usuario);
    
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado ->num_rows;
            if($linhas > 0){
                while($row = $resultado->fetch_assoc()) {
                    $usuariosEncontrados[] = [
                        'id' => $row['id'],
                        'nome' => $row['nome'],
                        'territorio_id' => $row['territorio_id'],
                        'ativo' => $row['ativo'],
                        'permissoes' => $row['permissoes'],
                        'primeiro_acesso' => $row['primeiro_acesso']
                    ];
                }
                $stmt->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $usuariosEncontrados]);
                exit();
            }
            else{
                $stmt->close();
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                exit();
            }
        }
    }
    public function criarUsuario($nome,$nascimento,$sexo){
        $usuarioLogado = $_SESSION['usuario']['id'];

        $sql="INSERT INTO usuarios(
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
    public function updateSenha($id){
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
    public function updateDados($id,$nome,$permissoes,$territorio,$permissoesAdm,$ativo,$primeiro_acesso){
        $usuarioLogado = $_SESSION['usuario']['id'];
        echo $permissoes;
        echo $permissoesAdm;
        if (strpos($permissoesAdm, '1') !== false) {
            $permissoes=$permissoes.'1'.$permissoesAdm;
        } else {
            $permissoes=$permissoes.'0'.$permissoesAdm;
        }
        echo $permissoes;
        exit();
        

        $sql="UPDATE
                  usuarios
              SET
                  nome = ?,
                  territorio_id = ?,
                  ativo = ?,
                  permissoes = ?,
                  primeiro_acesso = ?,
                  id_usuario_atualizacao = ?,
                  data_hora_atualizacao = NOW()
              WHERE
                  id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt -> bind_param('siisiii', $nome,$territorio,$ativo,$permissoes,$primeiro_acesso,$usuarioLogado,$id);
    
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

    $classeUsuario = new Usuario($mysqli);
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
                echo $classeUsuario->buscarPorId($id);
            } else {
                echo json_encode(['mensagem' => 'ID inválido fornecido.', 'dados' => []]);
            }
        }
        else{
            echo json_encode(['mensagem' => 'Nenhum ID ou tipo de busca especificado.', 'dados' => []]);
        }
    }
    if($tipo=="porUsuario"){
        if (isset($_POST['usuario']) && (!empty($_POST['usuario']) || strlen($_POST['usuario'])>3)) {
            $usuario = $_POST['usuario'];
            echo $classeUsuario->buscarPorUsuario($usuario);
        }
        else{
            echo json_encode(['mensagem' => 'Erro no usuário.', 'dados' => []]);
        }
    }
    else if($tipo=="criar"){
        if (isset($_POST['nome'])) {
            $nome=$_POST['nome'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no nome.', 'dados' => []]);
            exit();
        }
        if (isset($_POST['data_nascimento'])) {
            $nascimento=$_POST['data_nascimento'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro na data de nascimento.', 'dados' => []]);
            exit();
        }
        if (isset($_POST['id_sexo'])) {
            $sexo=$_POST['id_sexo'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no sexo.', 'dados' => []]);
            exit();
        }

        echo $classeUsuario-> criarUsuario($nome,$nascimento,$sexo);
    }
    else if($tipo=="updateSenha"){
        if (isset($_POST['nome'])) {
            $nome=$_POST['nome'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no nome.', 'dados' => []]);
            exit();
        }
        if (isset($_POST['data_nascimento'])) {
            $nascimento=$_POST['data_nascimento'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro na data de nascimento.', 'dados' => []]);
            exit();
        }
        if (isset($_POST['id_sexo'])) {
            $sexo=$_POST['id_sexo'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no sexo.', 'dados' => []]);
            exit();
        }

        echo $classeUsuario-> updateSenha($id);
    }
    else if($tipo=="updateDados"){
        
        if (isset($_POST['id'])) {
            $id=$_POST['id'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no parâmetro.', 'dados' => []]);
            exit();
        }

        if (isset($_POST['nome'])) {
            $nome=$_POST['nome'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no nome.', 'dados' => []]);
            exit();
        }

        if (isset($_POST['permissoes'])) {
            $permissoes=$_POST['permissoes'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro na permissão.', 'dados' => []]);
            exit();
        }

        if (isset($_POST['territorio'])) {
            $territorio=$_POST['territorio'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no território.', 'dados' => []]);
            exit();
        }

        if (isset($_POST['permissoesAdm'])) {
            $permissoesAdm=$_POST['permissoesAdm'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro nas permissões Adm.', 'dados' => []]);
            exit();
        }

        if (isset($_POST['ativo'])) {
            $ativo=$_POST['ativo'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no dado ativo.', 'dados' => []]);
            exit();
        }

        if (isset($_POST['primeiro_acesso'])) {
            $primeiro_acesso=$_POST['primeiro_acesso'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro no dado primeiro acesso.', 'dados' => []]);
            exit();
        }

        echo $classeUsuario-> updateDados($id,
                                          $nome,
                                          $permissoes,
                                          $territorio,
                                          $permissoesAdm,
                                          $ativo,
                                          $primeiro_acesso);
    }
    
}
?>