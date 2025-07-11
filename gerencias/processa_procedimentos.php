<?php
header('Content-Type: application/json; charset=utf-8');

// Simula um banco de dados de procedimentos
$procedimentos_db = [
    [
        "id" => 1,
        "numero_procedimento" => "001",
        "ano_procedimento" => "2023",
        "numero_ano" => "001/2023",
        "bairro" => "Centro",
        "territorio_bairro" => "Zona Urbana",
        "nome_pessoa" => "Maria Silva",
        "data_nascimento_pessoa" => "1990-05-15",
        "sexo_pessoa" => "Feminino",
        "nome_genitora" => "Ana Silva",
        "data_nascimento_genitora" => "1970-01-20",
        "sexo_genitora" => "Feminino",
        "demandante" => "João Souza"
    ],
    [
        "id" => 2,
        "numero_procedimento" => "002",
        "ano_procedimento" => "2024",
        "numero_ano" => "002/2024",
        "bairro" => "Vila Nova",
        "territorio_bairro" => "Zona Rural",
        "nome_pessoa" => "Pedro Santos",
        "data_nascimento_pessoa" => "2000-11-22",
        "sexo_pessoa" => "Masculino",
        "nome_genitora" => "Carla Santos",
        "data_nascimento_genitora" => "1975-03-10",
        "sexo_genitora" => "Feminino",
        "demandante" => "Prefeitura"
    ]
];

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
