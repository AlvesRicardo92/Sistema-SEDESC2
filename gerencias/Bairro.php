<?php
//gerencias/buscarTerritorio.php

require_once 'conexaoBanco.php';

class Bairro{
    private $mysqli;

    // Construtor que recebe o objeto $mysqli
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    public function buscarTodos(){
        $sql="SELECT id, nome FROM bairros ORDER BY nome";
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
                $this->mysqli->close();
                exit();
            }
            else{
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                $stmt->close();
                $this->mysqli->close();
                exit();
            }
        }
    }

    public function buscarTodosAtivos(){
        $sql="SELECT id, nome FROM bairros WHERE ativo=1 ORDER BY nome";
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
                $this->mysqli->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $territoriosEncontrados]);
                exit();
            }
            else{
                $stmt->close();
                $this->mysqli->close();
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                exit();
            }
        }
    }

    public function buscarPorId($id){
        $sql="SELECT id, nome FROM bairros WHERE id=? ORDER BY nome";
        $stmt = $this->mysqli->prepare($sql);
        $stmt -> bind_param('i', $id);
    
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
                $this->mysqli->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $territoriosEncontrados]);
                exit();
            }
            else{
                $stmt->close();
                $this->mysqli->close();
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                exit();
            }
        }
    }
    public function buscarTerritorioBairro($idBairro){
        $sql="SELECT
                  t.id as 'id',
                  t.nome as 'nome'
              FROM
                  bairros b
              LEFT JOIN territorios_ct t ON
                  t.id = b.territorio_id
              WHERE
                  b.id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt -> bind_param('i', $idBairro);
    
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
                $this->mysqli->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $territoriosEncontrados]);
                exit();
            }
            else{
                $stmt->close();
                $this->mysqli->close();
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                exit();
            }
        }
    }
    public function buscarAtivosExcetoTerritorios(array $territoriosExcluir) {
        // Verifica se o array de territórios para excluir não está vazio
        if (empty($territoriosExcluir)) {
            return json_encode(['mensagem' => 'Erro na passagem da exceção ', 'dados' => []]);
        } else {
            // Cria uma string de placeholders para a cláusula NOT IN
            $placeholders = implode(',', array_fill(0, count($territoriosExcluir), '?'));
            $sql = "SELECT id, nome FROM bairros WHERE ativo = 1 AND territorio_id NOT IN ($placeholders) ORDER BY nome";
        }

        $stmt = $this->mysqli->prepare($sql);

        if ($stmt === false) {
            // Lidar com erro na preparação da consulta
            return json_encode(['mensagem' => 'Erro ao preparar a consulta: ' . $this->mysqli->error, 'dados' => []]);
        }

        // Se houver territórios para excluir, vincula os parâmetros
        if (!empty($territoriosExcluir)) {
            // Cria uma string de tipos para os parâmetros (assumindo que os IDs de território são inteiros)
            $types = str_repeat('i', count($territoriosExcluir));
            $stmt->bind_param($types, ...$territoriosExcluir);
        }

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado->num_rows;
            $bairrosEncontrados = [];

            if ($linhas > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $bairrosEncontrados[] = [
                        'id' => $row['id'],
                        'nome' => $row['nome']
                    ];
                }
                $stmt->close();
                return json_encode(['mensagem' => 'Sucesso', 'dados' => $bairrosEncontrados]);
            } else {
                $stmt->close();
                return json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
            }
        } else {
            return json_encode(['mensagem' => 'Erro ao executar a consulta: ' . $stmt->error, 'dados' => []]);
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Certifique-se de que a conexão com o banco de dados está disponível
    global $mysqli; // Assume que $mysqli está disponível globalmente após o require_once

    $bairro = new Bairro($mysqli);
    if (isset($_POST['tipo'])) {
        $tipo = $_POST['tipo'];
    }
    else{
        echo json_encode(['mensagem' => 'Falha no tipo', 'dados' => []]);
        exit();
    }
    
    if($tipo=="ativosExcetoTerritorios"){
        // Verifica se o ID foi enviado via POST
        if (!isset($_POST['territorio'])) {
            echo json_encode(['mensagem' => 'Dados inválidos.', 'dados' => []]);
        }
        else{
            $territorio=$_POST['territorio'];
            echo $bairro->buscarAtivosExcetoTerritorios([$territorio,4]);
        }
    }
    else if($tipo=="criar"){
        
    }
    else if($tipo=="territorioBairro"){
        // Verifica se o ID foi enviado via POST
        if (!isset($_POST['idBairro'])) {
            echo json_encode(['mensagem' => 'Dados inválidos.', 'dados' => []]);
        }
        else{
            $idBairro=$_POST['idBairro'];
            echo $bairro->buscarTerritorioBairro($idBairro);
        }
    }
    
}
?>