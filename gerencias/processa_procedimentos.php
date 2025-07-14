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
        $whereQuery+=' AND proc.id_territorio =' . $_SESSION['usuario']['territorio'];
    }
    $stmt = $mysqli->prepare($sql . $whereQuery . ' ORDER BY ano DESC, numero DESC');
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

function update_procedimento(&$db, $data) {
    foreach ($db as $key => $proc) {
        if ($proc['id'] == $data['id']) {
            $db[$key] = [
                "id" => $data['id'],
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
            return ["mensagem" => "Sucesso", "dados" => $db[$key]];
        }
    }
    return ["mensagem" => "Procedimento não encontrado para atualização.", "dados" => []];
}

function delete_procedimento(&$db, $id) {
    foreach ($db as $key => $proc) {
        if ($proc['id'] == $id) {
            array_splice($db, $key, 1);
            return ["mensagem" => "Sucesso", "dados" => ["id" => $id]];
        }
    }
    return ["mensagem" => "Procedimento não encontrado para exclusão.", "dados" => []];
}

// Lógica para processar a requisição
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? null;
    $id = $_GET['id'] ?? null;

    if ($action === 'get_procedimento' && $id) {
        $found = false;
        foreach ($procedimentos_db as $proc) {
            if ($proc['id'] == $id) {
                echo json_encode(["mensagem" => "Sucesso", "dados" => $proc], JSON_PRETTY_PRINT);
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo json_encode(["mensagem" => "Procedimento não encontrado.", "dados" => []], JSON_PRETTY_PRINT);
        }
    } else {
        // Lógica de busca para os campos de pesquisa
        $numero = $_GET['numero'] ?? '';
        $nome = $_GET['nome'] ?? '';
        $genitora = $_GET['genitora'] ?? '';
        $nascimento = $_GET['nascimento'] ?? '';

        $resultados = array_filter($procedimentos_db, function($proc) use ($numero, $nome, $genitora, $nascimento) {
            $match_numero = empty($numero) || strpos(strtolower($proc['numero_procedimento']), strtolower($numero)) !== false;
            $match_nome = empty($nome) || strpos(strtolower($proc['nome_pessoa']), strtolower($nome)) !== false;
            $match_genitora = empty($genitora) || strpos(strtolower($proc['nome_genitora']), strtolower($genitora)) !== false;
            $match_nascimento = empty($nascimento) || $proc['data_nascimento_pessoa'] === $nascimento;
            
            // Retorna true se pelo menos um campo de pesquisa corresponder (ou se todos estiverem vazios)
            return ($match_numero && empty($nome) && empty($genitora) && empty($nascimento)) ||
                   (empty($numero) && $match_nome && empty($genitora) && empty($nascimento)) ||
                   (empty($numero) && empty($nome) && $match_genitora && empty($nascimento)) ||
                   (empty($numero) && empty($nome) && empty($genitora) && $match_nascimento) ||
                   (empty($numero) && empty($nome) && empty($genitora) && empty($nascimento)); // Se todos vazios, retorna todos
        });

        echo json_encode(["mensagem" => "Sucesso", "dados" => array_values($resultados)], JSON_PRETTY_PRINT);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $id = $_POST['id'] ?? null;

    if ($action === 'add_procedimento') {
        echo json_encode(add_procedimento($procedimentos_db, $_POST), JSON_PRETTY_PRINT);
    } elseif ($action === 'update_procedimento') {
        echo json_encode(update_procedimento($procedimentos_db, $_POST), JSON_PRETTY_PRINT);
    } elseif ($action === 'delete_procedimento') {
        echo json_encode(delete_procedimento($procedimentos_db, $id), JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["mensagem" => "Ação POST inválida.", "dados" => []], JSON_PRETTY_PRINT);
    }
} else {
    echo json_encode(["mensagem" => "Método de requisição não suportado.", "dados" => []], JSON_PRETTY_PRINT);
}
?>
