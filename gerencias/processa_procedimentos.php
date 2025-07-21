<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once "conexaoBanco.php";

if(!isset($_POST['acao'])){
    echo json_encode( ["mensagem" => "Nenhuma ação especificada.", "dados" => []]);
}
else{
    $acao = $mysqli -> real_escape_string($_POST['acao']);
}

if($acao==="buscar"){
    if(!isset($_POST['tipo'])|| !isset($_POST['parametroBusca'])){
        echo json_encode( ["mensagem" => "Nenhum tipo ou parâmetro especificado.", "dados" => []]);
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
                    'migrado' => $row['migrado'],
                    'numero_novo' => $row['numero_novo'],
                    'ano_novo' => $row['ano_novo'],
                    'territorio_novo' => $row['territorio_novo'],
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
        echo json_encode(["mensagem" => "Erro na passagem de dados.", "dados" => []]);
    }
    else{
        $token = $_POST['token'];
        if(!isset($_SESSION['tokens'][$token])){
            echo json_encode(["mensagem" => "Procedimento não localizado.", "dados" => []]);
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
                    mm.nome AS 'motivo_migracao'
                FROM
                    procedimentos proc
                LEFT JOIN territorios_ct t ON
                    t.id = proc.id_territorio
                LEFT JOIN bairros b ON
                    b.id = proc.id_bairro
                LEFT JOIN pessoas p ON
                    p.id = proc.id_pessoa
                LEFT JOIN pessoas g ON
                    g.id = proc.id_genitora_pessoa
                LEFT JOIN demandantes d ON
                    d.id = proc.id_demandante
                LEFT JOIN migracoes m ON
                    m.id = proc.id_migracao
                LEFT JOIN motivos_migracao mm ON
                    mm.id = m.id_motivo_migracao
                LEFT JOIN sexos sp ON
                    sp.id = p.id_sexo
                LEFT JOIN sexos sg ON
                    sg.id = g.id_sexo
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
                            'migrado' => $row['migrado'],
                            'motivo_migracao' => $row['motivo_migracao']
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
        echo json_encode( ["mensagem" => "Erro na passagem de dados.", "dados" => []]);
    }
    else{
        $token = $_POST['token'];
        if(!isset($_SESSION['tokens'][$token])){
            echo json_encode(["mensagem" => "Procedimento não localizado.", "dados" => []]);
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
        echo json_encode( ["mensagem" => "Erro na passagem de dados.", "dados" => []]);
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
        echo json_encode( ["mensagem" => "Erro na passagem de dados.", "dados" => []]);
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
function removerAcentos($texto) {
    $acentos = array(
        'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ',
        'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý',
        'º', 'ª',"'" // Exemplos para o símbolo de "grau" ou ordinal, se desejar remover
    );
    $semAcentos = array(
        'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y',
        'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y',
        '', '',' ' // Substituição para os símbolos
    );

    return str_replace($acentos, $semAcentos, $texto);
}
function ultimoNumero($idTerritorio, $mysqli) {
    $sql = "SELECT MAX(numero_procedimento) as ultimo 
            FROM procedimentos 
            WHERE id_territorio = ? AND ano_procedimento = YEAR(NOW())";
    
    $stmt = $mysqli->prepare($sql);
    
    // Verifica se a preparação da query foi bem-sucedida
    if ($stmt === false) {
        // Trate o erro de preparação, por exemplo, logando-o ou lançando uma exceção
        error_log("Erro na preparação da query: " . $mysqli->error);
        return 0; // Ou lance uma exceção
    }

    $stmt->bind_param('i', $idTerritorio);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Se um valor for encontrado, retorna-o, caso contrário, retorna 0
        if ($row && isset($row['ultimo'])) {
            return $row['ultimo'];
        } else {
            return 0;
        }
    } else {
        // Trate o erro de execução, por exemplo, logando-o
        //error_log("Erro na execução da query: " . $stmt->error);
        return 0;
    }
}
function diferencaEntreDatasEmAnos($dataGenitora, $dataPessoa){
     // Converte as strings de data para objetos DateTime
     $data1 = new DateTime($dataGenitora);
     $data2 = new DateTime($dataPessoa);

     // Calcula a diferença entre as duas datas
     $intervalo = $data1->diff($data2);

     // Obtém o número de anos completos do intervalo
     $anosDeDiferenca = $intervalo->y;

     // Retorna true se a diferença for menor que o limite
     return $anosDeDiferenca;

}
if($acao==="novo"){
    if(!isset($_POST['sexoPessoa']) || $_POST['sexoPessoa']==0 || !isset($_POST['sexoGenitora']) || $_POST['sexoGenitora']==0 ){
        print_r($_POST['sexoPessoa']);
        print_r($_POST['sexoGenitora']);
        echo json_encode(['mensagem' => 'Falha na seleção de Sexo', 'dados' => []]);
        exit();
    }
    if(!isset($_POST['nascimentoGenitora'])||!isset($_POST['nascimentoPessoa'])){
        echo json_encode(['mensagem' => 'Falha na Data de nascimento', 'dados' => []]);
        exit();
    }
    if(!isset($_POST['selectBairro']) || $_POST['selectBairro']==0 ){
        echo json_encode(['mensagem' => 'Falha na seleção de Bairro', 'dados' => []]);
        exit();
    }
    if(!isset($_POST['inputTerritorio']) || empty($_POST['inputTerritorio'])){
        echo json_encode(['mensagem' => 'Falha no Território do Bairro', 'dados' => []]);
        exit();
    }
    if(!isset($_POST['selectPessoa']) || ($_POST['selectPessoa']==0 && (!isset($_POST['inputPessoa']) || empty($_POST['inputPessoa'])))){
        echo json_encode(['mensagem' => 'Falha no nome da Pessoa', 'dados' => []]);
        exit();
    }
    if(!isset($_POST['selectGenitora']) || ($_POST['selectGenitora']==0 && (!isset($_POST['inputGenitora']) || empty($_POST['inputGenitora'])))){
        echo json_encode(['mensagem' => 'Falha no nome da Genitora', 'dados' => []]);
        exit();
    }
    if(!isset($_POST['selectDemandante']) || ($_POST['selectDemandante']==0 && (!isset($_POST['inputDemandante']) || empty($_POST['inputDemandante'])))){
        echo json_encode(['mensagem' => 'Falha no Demandante', 'dados' => []]);
        exit();
    }
    if(diferencaEntreDatasEmAnos($_POST['nascimentoGenitora'],$_POST['nascimentoPessoa'])<14){
        echo json_encode(['mensagem' => 'Genitora/Responsável com idade muito baixa em relação à pessoa', 'dados' => []]);
        exit();
    }
    $selectBairro=$_POST['selectBairro'];
    $inputTerritorio=$_POST['inputTerritorio'];
    $selectPessoa=$_POST['selectPessoa'];
    $inputPessoa=$_POST['inputPessoa'];
    $nascimentoPessoa=$_POST['nascimentoPessoa'];
    $sexoPessoa=$_POST['sexoPessoa'];
    $selectGenitora=$_POST['selectGenitora'];
    $inputGenitora=$_POST['inputGenitora'];
    $nascimentoGenitora=$_POST['nascimentoGenitora'];
    $sexoGenitora=$_POST['sexoGenitora'];
    $selectDemandante=$_POST['selectDemandante'];
    $inputDemandante=$_POST['inputDemandante'];
    $idBairroNovo=0;
    $idPessoaNova=0;
    $idGenitoraNova=0;
    $idDemandanteNovo=0;
    $numeroProcedimentoNovo=0;
    $idTerritorioBairro=0;
    $idProcedimentoNovo=0;
    $sql='';

    $mysqli->begin_transaction(); // Inicia a transação

    try{
        $stmt = $mysqli->prepare("SET @user_id = ?");
        $stmt->bind_param('i', $_SESSION['usuario']['id']);
        $stmt->execute();
        $stmt->close();

        /*if(!empty($inputBairro)){
            $inputBairro=removerAcentos($inputBairro);
            $inputBairro=trim($inputBairro);
            $inputBairro=preg_replace('/\s+/', ' ', $inputBairro);
            $inputBairro=mb_strtoupper($inputBairro, 'UTF-8');
            
            $sql="INSERT INTO bairros(nome, 
                                    territorio_id, 
                                    ativo, 
                                    id_usuario_criacao, 
                                    data_hora_criacao, 
                                    id_usuario_atualizacao, 
                                    data_hora_atualizacao) 
                                VALUES (?,?,1,?,NOW(),?,NOW())";
            $stmt = $mysqli->prepare($sql);
            $stmt -> bind_param('siii', $inputBairro,$selectTerritorio,$_SESSION['usuario']['id'],$_SESSION['usuario']['id']);
        
            if ($stmt->execute()) 
            {
                $idBairroNovo = $mysqli->insert_id;
            }
            else{
                echo json_encode(['mensagem' => 'Erro 0001', 'dados' => []]);
                exit();
            }
            $stmt->close();
        }*/
        if(!empty($inputPessoa)){
            $inputPessoa=removerAcentos($inputPessoa);
            $inputPessoa=trim($inputPessoa);
            $inputPessoa=preg_replace('/\s+/', ' ', $inputPessoa);
            $inputPessoa=mb_strtoupper($inputPessoa, 'UTF-8');

            $sql="INSERT INTO pessoas(nome, 
                                    data_nascimento, 
                                    id_sexo, 
                                    ativo, 
                                    id_usuario_criacao, 
                                    data_hora_criacao, 
                                    id_usuario_atualizacao, 
                                    data_hora_atualizacao) 
                                VALUES (?,?,?,1,?,NOW(),?,NOW())";
            $stmt = $mysqli->prepare($sql);
            $stmt -> bind_param('ssiii', $inputPessoa,$nascimentoPessoa,$sexoPessoa,$_SESSION['usuario']['id'],$_SESSION['usuario']['id']);
        
            if ($stmt->execute()) 
            {
                $idPessoaNova = $mysqli->insert_id;
            }
            else{
                echo json_encode(['mensagem' => 'Erro 0002. <strong>NÃO</strong> tente salvar novamente', 'dados' => []]);
                exit();
            }
            $stmt->close();
        }
        if(!empty($inputGenitora)){
            $inputGenitora=removerAcentos($inputGenitora);
            $inputGenitora=trim($inputGenitora);
            $inputGenitora=preg_replace('/\s+/', ' ', $inputGenitora);
            $inputGenitora=mb_strtoupper($inputGenitora, 'UTF-8');

            $sql="INSERT INTO pessoas(nome, 
                                    data_nascimento, 
                                    id_sexo, 
                                    ativo, 
                                    id_usuario_criacao, 
                                    data_hora_criacao, 
                                    id_usuario_atualizacao, 
                                    data_hora_atualizacao) 
                                VALUES (?,?,?,1,?,NOW(),?,NOW())";
            $stmt = $mysqli->prepare($sql);
            $stmt -> bind_param('ssiii', $inputGenitora,$nascimentoGenitora,$sexoGenitora, $_SESSION['usuario']['id'],$_SESSION['usuario']['id']);
        
            if ($stmt->execute()) 
            {
                $idGenitoraNova = $mysqli->insert_id;
            }
            else{
                echo json_encode(['mensagem' => 'Erro 0003. <strong>NÃO</strong> tente salvar novamente', 'dados' => []]);
                exit();
            }
            $stmt->close();
        }
        if(!empty($inputDemandante)){
            $inputDemandante=removerAcentos($inputDemandante);
            $inputDemandante=trim($inputDemandante);
            $inputDemandante=preg_replace('/\s+/', ' ', $inputDemandante);
            $inputDemandante=mb_strtoupper($inputDemandante, 'UTF-8');

            $sql="INSERT INTO demandantes(nome, 
                                    ativo, 
                                    id_usuario_criacao, 
                                    data_hora_criacao, 
                                    id_usuario_atualizacao, 
                                    data_hora_atualizacao) 
                                VALUES (?,1,?,NOW(),?,NOW())";
            $stmt = $mysqli->prepare($sql);
            $stmt -> bind_param('sii', $inputDemandante,$_SESSION['usuario']['id'],$_SESSION['usuario']['id']);
        
            if ($stmt->execute()) 
            {
                $idDemandanteNovo = $mysqli->insert_id;
            }
            else{
                echo json_encode(['mensagem' => 'Erro 0004. <strong>NÃO</strong> tente salvar novamente', 'dados' => []]);
                exit();
            }
            $stmt->close();
        }
        $idBairroNovo = empty($idBairroNovo)?$selectBairro:$idBairroNovo;
        $idPessoaNova=empty($idPessoaNova)?$selectPessoa:$idPessoaNova;
        $idGenitoraNova=empty($idGenitoraNova)?$selectGenitora:$idGenitoraNova;
        $idDemandanteNovo=empty($idDemandanteNovo)?$selectDemandante:$idDemandanteNovo;
        
        $sql="SELECT id FROM territorios_ct WHERE nome = ?;";
        $stmt = $mysqli->prepare($sql);
        $stmt -> bind_param('s',$inputTerritorio);    
        if ($stmt->execute()) 
        {
            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();
            $idTerritorioBairro = $row['id'];
        }
        else{
            echo json_encode(['mensagem' => 'Erro 0005. <strong>NÃO</strong> tente salvar novamente', 'dados' => []]);
            exit();
        }

        $ultimo = ultimoNumero($idTerritorioBairro, $mysqli);
        $numeroProcedimentoNovo= $ultimo+1;
        
        $sql="INSERT INTO procedimentos(numero_procedimento, 
                                        ano_procedimento, 
                                        id_territorio, 
                                        id_bairro, 
                                        id_pessoa, 
                                        id_genitora_pessoa, 
                                        id_demandante, 
                                        ativo, 
                                        migrado,  
                                        data_criacao, 
                                        hora_criacao, 
                                        id_usuario_criacao, 
                                        id_usuario_atualizacao, 
                                        data_hora_atualizacao) 
                            VALUES (?,YEAR(NOW()),?,?,?,?,?,1,0,CURRENT_DATE(),CURRENT_TIME(),?,?,NOW())";
        $stmt = $mysqli->prepare($sql);
        $stmt -> bind_param('iiiiiiii', $numeroProcedimentoNovo,$idTerritorioBairro, $idBairroNovo,$idPessoaNova,$idGenitoraNova,$idDemandanteNovo,$_SESSION['usuario']['id'],$_SESSION['usuario']['id']);
        if ($stmt->execute()) 
        {
            $idProcedimentoNovo = $mysqli->insert_id;
            echo json_encode(['mensagem' => 'Sucesso', 'dados' => ['numero' => $numeroProcedimentoNovo]]);
            $mysqli->commit();
            exit();
        }
        else{
            echo json_encode(['mensagem' => 'Erro 0006. <strong>NÃO</strong> tente salvar novamente', 'dados' => []]);
            throw new Exception("Erro ao registrar formulário principal: " . $stmt_form->error);
        }
        $mysqli->commit();
    }
    catch (Exception $e) {
        // --- Se algo deu errado, faz o rollback ---
        $conn->rollback();
        $sucesso_transacao = false;
        echo json_encode(['mensagem' => 'Erro 0007. Nenhum dado foi salvo! <strong>NÃO</strong> tente salvar novamente', 'dados' => []]);
    } finally {
        // Fecha a conexão com o banco de dados
        $mysqli->close();
    }
    
}
?>
