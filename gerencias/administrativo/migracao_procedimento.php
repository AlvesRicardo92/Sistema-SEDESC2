<?php
//migracao_procedimento.php
session_start();

if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
}
require_once '../conexaoBanco.php';
require_once '../Territorio.php';
require_once '../MotivoMigracao.php';

$classeTerritorio = new Territorio($mysqli);

$respostaJSONTerritorio=$classeTerritorio->buscarTodosCTAtivos();

$jsonDecodificadoTerritorio=json_decode($respostaJSONTerritorio,true);
if($jsonDecodificadoTerritorio['mensagem']=="Sucesso"){
    $dadosTerritorio=$jsonDecodificadoTerritorio['dados'];
}


$classeMotivoMigracao = new MotivoMigracao($mysqli);

$respostaJSONMotivoMigracao=$classeMotivoMigracao->buscarTodosAtivos();

$jsonDecodificadoMotivoMigracao=json_decode($respostaJSONMotivoMigracao,true);
if($jsonDecodificadoMotivoMigracao['mensagem']=="Sucesso"){
    $dadosMotivoMigracao=$jsonDecodificadoMotivoMigracao['dados'];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrar Procedimento - Sistema SEDESC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-control[disabled] {
            background-color: #e9ecef;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Migrar Procedimento</h2>
        <div id="mensagemFeedback" class="alert d-none" role="alert"></div>
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="search_numero" class="form-label">Número do Procedimento</label>
                    <input class="form-control" id="search_numero" name="numero" required>
                </div>
                <div class="col-md-5">
                    <label for="search_ano" class="form-label">Ano do Procedimento</label>
                    <input class="form-control" id="search_ano" name="ano" required min="1900" max="<?= date('Y') ?>">
                </div>
                <div class="col-md-5">
                    <select class="form-select" id="territorio_procedimento" required>
                        <option value="0">Selecione o Território</option>
                        <?php 
                            if(!empty($dadosTerritorio)){
                                foreach($dadosTerritorio as $territorio){
                                    echo "<option value=".$territorio['id'].">".$territorio['nome']."</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="buscarProcedimento">Buscar</button>
                </div>
            </div>
        <hr>

        <div id="procedimentoDetails">
            <h4 class="mb-3">Detalhes do Procedimento Original</h4>
            <input type="hidden" id="procedimento_id_original">
            <input type="hidden" id="procedimento_territorio_original_id">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome_pessoa_principal" class="form-label">Nome da Pessoa</label>
                    <input type="text" class="form-control" id="nome_pessoa_principal" disabled>
                </div>
                <div class="col-md-6">
                    <label for="data_nascimento_pessoa_principal" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" id="data_nascimento_pessoa_principal" disabled>
                </div>
            </div>

            <div id="migrationSection">
                <h4 class="mb-3 mt-4">Dados para Nova Migração</h4>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="new_bairro_id" class="form-label">Selecionar Novo Bairro</label>
                        <select class="form-select" id="new_bairro_id" required>
                            <option value="0">Selecione um Bairro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="new_bairro_territorio_nome" class="form-label">Território do Novo Bairro</label>
                        <input type="text" class="form-control" id="new_bairro_territorio_nome" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="motivo_migracao_id" class="form-label">Motivo da Migração</label>
                    <select class="form-select" id="motivo_migracao_id" required>
                        <option value="0">Selecione o Motivo</option>
                        <?php 
                            if(!empty($dadosMotivoMigracao)){
                                foreach($dadosMotivoMigracao as $motivo){
                                    echo "<option value=".$motivo['id'].">".$motivo['nome']."</option>";
                                }
                            }
                        ?>
                    </select>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn btn-success" id="btnMigrar" data-id="0" data-territorio="0" disabled>Migrar Procedimento</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../assets/js/migrar_procedimento_script.js"></script>
    
</body>
</html>