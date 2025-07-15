<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require "conexaoBanco.php";

if(!isset($_POST['acao'])){
    return ["mensagem" => "Nenhuma ação especificada.", "dados" => []];
}
else{
    $acao = $mysqli -> real_escape_string($_POST['acao']);
}

if($acao==="buscar"){
    if(!isset($_POST['tipo'])|| !isset($_POST['parametroBusca'])){
        return ["mensagem" => "Nenhum tipo ou parâmetro especificado.", "dados" => []];
    }
    else{
        $tipo = $mysqli -> real_escape_string($_POST['tipo']);
        $parametroBusca= $mysqli->real_escape_string($_POST['parametroBusca']);
    }
    $procedimentosEncontrados = [];
    $sql="SELECT
            proc.id AS 'id',
            proc.numero_procedimento AS 'numero',
            proc.ano_procedimento AS 'ano',
            t.nome AS 'territorio',
            b.nome AS 'bairro',
            p.nome AS 'nome_pessoa',
            p.data_nascimento AS 'nascimento_pessoa',
            sp.nome AS 'sexo_pessoa',
            g.nome AS 'nome_genitora',
            g.data_nascimento AS 'nascimento_genitora',
            sg.nome AS 'sexo_genitora',
            d.nome AS 'demandante',
            proc.ativo AS 'ativo',
            proc.migrado AS 'migrado',
            m.numero_novo AS 'numero_novo',
            m.ano_novo AS 'ano_novo',
            m.territorio_novo AS 'territorio_novo'
        FROM
            procedimentos proc
        LEFT JOIN
            territorios_ct t ON t.id = proc.id_territorio
        LEFT JOIN
            bairros b ON b.id = proc.id_bairro
        LEFT JOIN
            pessoas p ON p.id = proc.id_pessoa
        LEFT JOIN
            pessoas g ON g.id = proc.id_genitora_pessoa
        LEFT JOIN
            demandantes d ON d.id = proc.id_demandante
        LEFT JOIN
            migracoes m ON m.id = proc.id_migracao
        LEFT JOIN
            sexos sp ON sp.id = p.id_sexo
        LEFT JOIN
            sexos sg ON sg.id = g.id_sexo
        WHERE ";
    $whereQuery="";
    $binds="";
    $variavel="";

    if($tipo=="numero"){
        $whereQuery='proc.numero_procedimento =?';
        $binds="i";
        $variavel=$parametroBusca;
    }
    if($tipo=="nome"){
        $whereQuery='p.nome like ?';
        $binds="s";
        $variavel= '%' . $parametroBusca . '%';
    }
    if($tipo=="genitora"){
        $whereQuery='g.nome like ?';
        $binds="s";
        $variavel= '%' . $parametroBusca . '%';
    }
    if($tipo=="nascimento"){
        $whereQuery='p.data_nascimento =?';
        $binds="s";
        $variavel= $parametroBusca;
    }
    if($_SESSION['usuario']['territorio']<4){
        $whereQuery.=' AND proc.id_territorio =' . $_SESSION['usuario']['territorio'];
    }
    $stmt = $mysqli->prepare($sql . $whereQuery . ' AND proc.ativo=1 ORDER BY ano DESC, numero DESC');
    $stmt -> bind_param($binds, $variavel);

    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        $linhas = $resultado ->num_rows;
        if($linhas > 0){
            while($row = $resultado->fetch_assoc()) {
                $token = bin2hex(random_bytes(32));
                $procedimentosEncontrados[] = [
                    'numero' => $row['numero'],
                    'ano' => $row['ano'],
                    'territorio' => $row['territorio'],
                    'nome_pessoa' => $row['nome_pessoa'],
                    'nascimento_pessoa' => $row['nascimento_pessoa'],
                    'nome_genitora' => $row['nome_genitora'],
                    'token'=>$token
                ];
                $_SESSION['tokens'][$token] = $row['id'];
            }
            echo json_encode(['mensagem' => 'Sucesso', 'dados' => $procedimentosEncontrados]);
            $stmt->close();
            $mysqli->close();
            exit();
        }
        else{
            echo json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
            $stmt->close();
            $mysqli->close();
            exit();
        }
    }
}
if($acao==="visualizar"){
    if(!isset($_POST['token'])){
        return ["mensagem" => "Erro na passagem de dados.", "dados" => []];
    }
    else{
        $token = $_POST['token'];
        if(!isset($_SESSION['tokens'][$token])){
            return ["mensagem" => "Procedimento não localizado.", "dados" => []];
        }
        else{
            $idProcedimento =$_SESSION['tokens'][$token];
            $sql="SELECT
                proc.id AS 'id',
                proc.numero_procedimento AS 'numero',
                proc.ano_procedimento AS 'ano',
                t.nome AS 'territorio',
                b.nome AS 'bairro',
                p.nome AS 'nome_pessoa',
                p.data_nascimento AS 'nascimento_pessoa',
                sp.nome AS 'sexo_pessoa',
                g.nome AS 'nome_genitora',
                g.data_nascimento AS 'nascimento_genitora',
                sg.nome AS 'sexo_genitora',
                d.nome AS 'demandante',
                proc.ativo AS 'ativo',
                proc.migrado AS 'migrado',
                m.numero_novo AS 'numero_novo',
                m.ano_novo AS 'ano_novo',
                m.territorio_novo AS 'territorio_novo'
            FROM
                procedimentos proc
            LEFT JOIN
                territorios_ct t ON t.id = proc.id_territorio
            LEFT JOIN
                bairros b ON b.id = proc.id_bairro
            LEFT JOIN
                pessoas p ON p.id = proc.id_pessoa
            LEFT JOIN
                pessoas g ON g.id = proc.id_genitora_pessoa
            LEFT JOIN
                demandantes d ON d.id = proc.id_demandante
            LEFT JOIN
                migracoes m ON m.id = proc.id_migracao
            LEFT JOIN
                sexos sp ON sp.id = p.id_sexo
            LEFT JOIN
                sexos sg ON sg.id = g.id_sexo
            WHERE proc.id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt -> bind_param('i', $idProcedimento);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                $linhas = $resultado ->num_rows;
                if($linhas > 0){
                    while($row = $resultado->fetch_assoc()) {
                        $procedimentosEncontrados[] = [
                            'numero' => $row['numero'],
                            'ano' => $row['ano'],
                            'bairro' => $row['bairro'],
                            'territorio' => $row['territorio'],
                            'nome_pessoa' => $row['nome_pessoa'],
                            'nascimento_pessoa' => $row['nascimento_pessoa'],
                            'sexo_pessoa' => $row['sexo_pessoa'],
                            'nome_genitora' => $row['nome_genitora'],
                            'nascimento_genitora' => $row['nascimento_genitora'],
                            'sexo_genitora' => $row['sexo_genitora'],
                            'demandante' => $row['demandante']
                        ];
                    }
                    echo json_encode(['mensagem' => 'Sucesso', 'dados' => $procedimentosEncontrados]);
                    $stmt->close();
                    $mysqli->close();
                    exit();
                }
                else{
                    echo json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                    $stmt->close();
                    $mysqli->close();
                    exit();
                }
            }   
        }
    }
}
if($acao==="editar"){
    if(!isset($_POST['token'])){
        return ["mensagem" => "Erro na passagem de dados.", "dados" => []];
    }
    else{
        $token = $_POST['token'];
        if(!isset($_SESSION['tokens'][$token])){
            return ["mensagem" => "Procedimento não localizado.", "dados" => []];
        }
        else{
            $idProcedimento =$_SESSION['tokens'][$token];
            $sql="SELECT
                proc.id AS 'id',
                proc.numero_procedimento AS 'numero',
                proc.ano_procedimento AS 'ano',
                t.nome AS 'territorio',
                b.nome AS 'bairro',
                p.nome AS 'nome_pessoa',
                p.data_nascimento AS 'nascimento_pessoa',
                sp.nome AS 'sexo_pessoa',
                g.nome AS 'nome_genitora',
                g.data_nascimento AS 'nascimento_genitora',
                sg.nome AS 'sexo_genitora',
                d.nome AS 'demandante',
                proc.ativo AS 'ativo',
                proc.migrado AS 'migrado',
                m.numero_novo AS 'numero_novo',
                m.ano_novo AS 'ano_novo',
                m.territorio_novo AS 'territorio_novo',
                proc.id_bairro as 'id_bairro',
                proc.id_pessoa as 'id_pessoa',
                p.id_sexo as 'id_sexo',
                proc.id_genitora_pessoa as 'id_genitora',
                g.id_sexo as 'id_sexo_genitora',
                proc.id_demandante as 'id_demandante'               
            FROM
                procedimentos proc
            LEFT JOIN
                territorios_ct t ON t.id = proc.id_territorio
            LEFT JOIN
                bairros b ON b.id = proc.id_bairro
            LEFT JOIN
                pessoas p ON p.id = proc.id_pessoa
            LEFT JOIN
                pessoas g ON g.id = proc.id_genitora_pessoa
            LEFT JOIN
                demandantes d ON d.id = proc.id_demandante
            LEFT JOIN
                migracoes m ON m.id = proc.id_migracao
            LEFT JOIN
                sexos sp ON sp.id = p.id_sexo
            LEFT JOIN
                sexos sg ON sg.id = g.id_sexo
            WHERE proc.id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt -> bind_param('i', $idProcedimento);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                $linhas = $resultado ->num_rows;
                if($linhas > 0){
                    while($row = $resultado->fetch_assoc()) {
                        $procedimentosEncontrados[] = [
                            'numero' => $row['numero'],
                            'ano' => $row['ano'],
                            'bairro' => $row['bairro'],
                            'territorio' => $row['territorio'],
                            'nome_pessoa' => $row['nome_pessoa'],
                            'nascimento_pessoa' => $row['nascimento_pessoa'],
                            'sexo_pessoa' => $row['sexo_pessoa'],
                            'nome_genitora' => $row['nome_genitora'],
                            'nascimento_genitora' => $row['nascimento_genitora'],
                            'sexo_genitora' => $row['sexo_genitora'],
                            'demandante' => $row['demandante'],
                            'id_bairro' => $row['id_bairro'],
                            'id_pessoa'  => $row['id_pessoa'],
                            'id_sexo' => $row['id_sexo'],
                            'id_genitora_pessoa' => $row['id_genitora'],
                            'id_sexo_genitora' => $row['id_sexo_genitora'],
                            'id_demandante' => $row['id_demandante']                            
                        ];
                    }
                    echo json_encode(['mensagem' => 'Sucesso', 'dados' => $procedimentosEncontrados]);
                    $resultado->free_result();
                    $stmt->close();
                    $mysqli->close();
                    exit();
                }
                else{
                    echo json_encode(['mensagem' => 'Nenhum resultado para a busca', 'dados' => []]);
                    $stmt->close();
                    $mysqli->close();
                    exit();
                }
            }   
        }
    }
}
if($acao==="update"){
    if(!isset($_POST['token']) ||
       !isset($_POST['bairro']) ||
       !isset($_POST['pessoa']) ||
       !isset($_POST['nascimento']) ||
       !isset($_POST['sexo']) ||
       !isset($_POST['genitora']) ||
       !isset($_POST['nascimento_genitora']) ||
       !isset($_POST['sexo_genitora']) ||
       !isset($_POST['demandante']))
       {
            return ["mensagem" => "Erro na passagem de dados.", "dados" => []];
       }
    else
    {
        $token = $_POST['token'];
        $bairro=$_POST['bairro'];
        $pessoa=$_POST['pessoa'];
        $nascimento=$_POST['nascimento'];
        $sexo=$_POST['sexo'];
        $genitora=$_POST['genitora'];
        $nascimento_genitora=$_POST['nascimento_genitora'];
        $sexo_genitora=$_POST['sexo_genitora'];
        $demandante=$_POST['demandante'];
        $idProcedimento =$_SESSION['tokens'][$token];

        $stmt = $mysqli->prepare("SET @user_id = ?");
        $stmt->bind_param('i', $_SESSION['usuario']['id']);
        $stmt->execute();
        //$stmt->close();

        $sql="UPDATE procedimentos 
              SET id_bairro=?,
                  id_pessoa=?,
                  id_genitora_pessoa=?,
                  id_demandante=?,
                  id_usuario_atualizacao=?,
                  data_hora_atualizacao=NOW() 
              WHERE id=?";
        
        $stmt = $mysqli->prepare($sql);
        $stmt -> bind_param('iiiiii', $bairro,$pessoa,$genitora,$demandante,$_SESSION['usuario']['id'],$idProcedimento);

        if ($stmt->execute()) 
        {
            if($stmt->affected_rows>0)
            {
                echo json_encode(['mensagem' => 'Sucesso', 'dados' => []]);
                $stmt->close();
                $mysqli->close();
                exit();
            }
            else
            {
                echo json_encode(['mensagem' => 'Erro ao atualizar o procedimento', 'dados' => []]);
                $stmt->close();
                $mysqli->close();
                exit();
            }
        }
    }
}
if($acao==="desativar"){
    if(!isset($_POST['token']))
       {
            return ["mensagem" => "Erro na passagem de dados.", "dados" => []];
       }
    else
    {
        $token = $_POST['token'];
        $idProcedimento =$_SESSION['tokens'][$token];

        $stmt = $mysqli->prepare("SET @user_id = ?");
        $stmt->bind_param('i', $_SESSION['usuario']['id']);
        $stmt->execute();
        //$stmt->close();

        $sql="UPDATE procedimentos 
              SET ativo=?,
                  id_usuario_atualizacao=?,
                  data_hora_atualizacao=NOW() 
              WHERE id=?";
        
        $stmt = $mysqli->prepare($sql);
        $ativo=0;
        $stmt -> bind_param('iii', $ativo,$_SESSION['usuario']['id'],$idProcedimento);

        if ($stmt->execute()) 
        {
            if($stmt->affected_rows>0)
            {
                echo json_encode(['mensagem' => 'Sucesso', 'dados' => []]);
                $stmt->close();
                $mysqli->close();
                exit();
            }
            else
            {
                echo json_encode(['mensagem' => 'Erro ao atualizar o procedimento', 'dados' => []]);
                $stmt->close();
                $mysqli->close();
                exit();
            }
        }
    }
}





// Lógica para adicionar, atualizar e excluir procedimentos (simulado)
// Em um ambiente real, você usaria um banco de dados
function add_procedimento(&$db, $data) {
    $newId = count($db) > 0 ? max(array_column($db, 'id')) + 1 : 1;
    $newProc = [
        "id" => $newId,
        "numero_procedimento" => $data['numero_procedimento'],
        "ano_procedimento" => $data['ano_procedimento'],
        "numero_ano" => $data['numero_procedimento'] . '/' . $data['ano_procedimento'],
        "bairro" => $data['bairro'],
        "territorio_bairro" => $data['territorio_bairro'],
        "nome_pessoa" => $data['nome_pessoa'],
        "data_nascimento_pessoa" => $data['data_nascimento_pessoa'],
        "sexo_pessoa" => $data['sexo_pessoa'],
        "nome_genitora" => $data['nome_genitora'],
        "data_nascimento_genitora" => $data['data_nascimento_genitora'],
        "sexo_genitora" => $data['sexo_genitora'],
        "demandante" => $data['demandante']
    ];
    $db[] = $newProc;
    return ["mensagem" => "Sucesso", "dados" => $newProc];
}
?>
