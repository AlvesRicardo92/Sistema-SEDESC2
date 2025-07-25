<?php
// public/administracao/alterar_pessoa.php
session_start();

if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
}
require_once '../conexaoBanco.php';
require_once '../Pessoa.php';
require_once '../Sexo.php';

$classePessoa = new Pessoa($mysqli);

$respostaJSONPessoa=$classePessoa->buscarTodos();

$jsonDecodificadoPessoa=json_decode($respostaJSONPessoa,true);
if($jsonDecodificadoPessoa['mensagem']=="Sucesso"){
    $dadosPessoa=$jsonDecodificadoPessoa['dados'];
}

$classeSexo = new Sexo($mysqli);

$respostaJSONSexo=$classeSexo->buscarTodosAtivos();

$jsonDecodificadoSexo=json_decode($respostaJSONSexo,true);
if($jsonDecodificadoSexo['mensagem']=="Sucesso"){
    $dadosSexo=$jsonDecodificadoSexo['dados'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Pessoa - Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="alterarPessoa">
    <div class="alterarPessoa container">
        
        <h4>Alterar Dados da Pessoa</h4>
        <p class="text-muted">Selecione uma pessoa abaixo e altere seus dados.</p>
        <div id="mensagemFeedback" class="alert d-none" role="alert"></div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="pessoas" class="form-label">Pessoas</label>
                <select class="form-select" id="pessoas" name="territorio_id" required>
                    <option value="0">Selecione uma pessoa</option>
                    <?php 
                        if(!empty($dadosPessoa)){
                            foreach($dadosPessoa as $pessoa){
                                echo "<option value=".$pessoa['id'].">".$pessoa['nome']."</option>";
                            }
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9 mb-3">
                <label for="nome_pessoa" class="form-label">Nome da Pessoa</label>
                <input type="text" class="alterarPessoa form-control" id="nome_pessoa" name="nome" disabled required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9 mb-3">
                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="alterarPessoa form-control" id="data_nascimento" name="data_nascimento" disabled required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="sexo_id" class="form-label">Sexo</label>
                <select class="form-select" id="sexo_id" name="sexo_id" disabled>
                    <option value="0">Selecione um sexo</option>
                    <?php 
                        if(!empty($dadosSexo)){
                            foreach($dadosSexo as $sexo){
                                echo "<option value=".$sexo['id'].">".$sexo['nome']."</option>";
                            }
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <input type="checkbox" class="form-check-input" id="inativo" name="ativo" value="1" disabled checked>
                <label class="form-check-label" for="inativo">Inativo</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5">
                <button class="btn btn-primary mt-3" id="btnSalvar" disabled><i class="fas fa-save"></i> Salvar Alterações</button>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../assets/js/alterar_pessoa_script.js"></script>
</body>
</html>