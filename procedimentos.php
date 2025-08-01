<?php
session_start();
// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Procedimentos</title>
    <!-- Incluindo Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluindo nosso CSS personalizado (reutilizado do projeto) -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        
    </style>
</head>
<body>
    <?php
        require_once __DIR__ . '/utils/cabecalho.php';
    ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4 text-primary">Gerenciar Procedimentos</h2>

        <!-- Campos de Pesquisa -->
        <div class="row search-fields-row">
            <div class="col-md-3 mb-3">
                <label for="searchNumero" class="form-label">Número</label>
                <input type="text" class="form-control form-control-custom" id="searchNumero" name="numero">
            </div>
            <div class="col-md-3 mb-3">
                <label for="searchNome" class="form-label">Nome</label>
                <input type="text" class="form-control form-control-custom" id="searchNome" name="nome">
            </div>
            <div class="col-md-3 mb-3">
                <label for="searchNascimento" class="form-label">Nascimento</label>
                <input type="date" class="form-control form-control-custom" id="searchNascimento" name="nascimento">
            </div>
            <div class="col-md-3 mb-3">
                <label for="searchGenitora" class="form-label">Genitora/Responsável</label>
                <input type="text" class="form-control form-control-custom" id="searchGenitora" name="genitora">
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="d-flex justify-content-between mb-4">
            <button type="button" class="btn btn-primary btn-custom" id="btnProcurar"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                </svg> Procurar</button>
            <button type="button" class="btn btn-success btn-custom" id="btnNovoProcedimento" data-bs-toggle="modal" data-bs-target="#novoProcedimentoModal"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"></path>
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"></path>
                </svg> Novo Procedimento</button>
        </div>

        <!-- Tabela de Resultados -->
        <div class="table-responsive table-responsive-custom">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Número/Ano</th>
                        <th>Território</th>
                        <th>Nome</th>
                        <th>Nascimento</th>
                        <th>Genitora/Responsável</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="procedimentosTableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted">Digite um dos campos acima e clique em Procurar</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modais -->
    <!-- Modal Visualizar -->
    <div class="modal fade" id="visualizarModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="visualizarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom-visualizar">
                    <h5 class="modal-title" id="visualizarModalLabel">Visualizar Procedimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewNumeroProcedimento" class="form-label">Número Procedimento</label>
                                <input type="text" class="form-control" id="viewNumeroProcedimento" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="viewAnoProcedimento" class="form-label">Ano Procedimento</label>
                                <input type="text" class="form-control" id="viewAnoProcedimento" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewBairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="viewBairro" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="viewTerritorioBairro" class="form-label">Território Bairro</label>
                                <input type="text" class="form-control" id="viewTerritorioBairro" disabled>
                            </div>
                        </div>
                        <div class="separador-horizontal"><strong>Pessoa</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="viewNomePessoa" class="form-label">Nome Pessoa</label>
                                <input type="text" class="form-control" id="viewNomePessoa" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewDataNascimentoPessoa" class="form-label">Data Nascimento Pessoa</label>
                                <input type="date" class="form-control" id="viewDataNascimentoPessoa" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="viewSexoPessoa" class="form-label">Sexo Pessoa</label>
                                <input type="text" class="form-control" id="viewSexoPessoa" disabled>
                            </div>
                        </div>
                        <div class="separador-horizontal"><strong>Genitora/Responsável</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="viewNomeGenitora" class="form-label">Nome Genitora/Responsável</label>
                                <input type="text" class="form-control" id="viewNomeGenitora" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewDataNascimentoGenitora" class="form-label">Data Nascimento</label>
                                <input type="date" class="form-control" id="viewDataNascimentoGenitora" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="viewSexoGenitora" class="form-label">Sexo</label>
                                <input type="text" class="form-control" id="viewSexoGenitora" disabled>
                            </div>
                        </div>
                        <div class="separador-horizontal"><strong>Demandante</strong></div>
                        <div class="mb-3">
                            <label for="viewDemandante" class="form-label">Demandante</label>
                            <input type="text" class="form-control" id="viewDemandante" disabled>
                        </div>
                        <div class="row mb-3 divMotivoMigracao" style="display:none">
                            <div class="col-md-12">
                                <div class="separador-horizontal"><strong>Migração</strong></div>
                            </div>
                            <div class="col-md-12">
                                <label for="viewMotivoMigracao" class="form-label">Motivo Migração</label>
                                <input type="text" class="form-control" id="viewMotivoMigracao" disabled>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editarModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom-editar">
                    <h5 class="modal-title" id="editarModalLabel">Editar Procedimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalMessage" class="alert d-none" role="alert">
                    </div>
                    <form id="formEditarProcedimento">
                        <input type="hidden" id="editProcedimentoId" name="id">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editNumeroProcedimento" class="form-label">Número Procedimento</label>
                                <input type="text" class="form-control" id="editNumeroProcedimento" name="numero_procedimento" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="editAnoProcedimento" class="form-label">Ano Procedimento</label>
                                <input type="text" class="form-control" id="editAnoProcedimento" name="ano_procedimento" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="select-bairros" class="form-label">Bairro</label>
                                <select class="select-bairros form-select" id="select-bairros" required>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="editTerritorioBairro" class="form-label">Território Bairro</label>
                                <input type="text" class="form-control" id="editTerritorioBairro" name="territorio_bairro" disabled>
                            </div>
                        </div>
                        <div class="separador-horizontal"><strong>Pessoa</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="select-pessoas" class="form-label">Pessoa</label>
                                <select class="select-pessoas form-select" id="select-pessoas" required>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editDataNascimentoPessoa" class="form-label">Data Nascimento</label>
                                <input type="date" class="form-control" id="editDataNascimentoPessoa" name="data_nascimento_pessoa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="select-sexos" class="form-label">Sexo</label>
                                    <select class="select-sexos form-select" id="select-sexos" required>
                                </select>
                            </div>
                        </div>
                        <div class="separador-horizontal"><strong>Genitora/Responsável</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="select-genitoras" class="form-label">Genitora/Responsável</label>
                                <select class="select-genitoras form-select" id="select-genitoras" required>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editDataNascimentoGenitora" class="form-label">Data Nascimento</label>
                                <input type="date" class="form-control" id="editDataNascimentoGenitora" name="data_nascimento_genitora" required>
                            </div>
                            <div class="col-md-6">
                                <label for="select-sexos-genitora" class="form-label">Sexo</label>
                                <select class="select-sexos-genitora form-select" id="select-sexos-genitora" required>
                                </select>
                            </div>
                        </div>
                        <div class="separador-horizontal"><strong>Demandante</strong></div>
                        <div class="mb-3">
                            <label for="select-demandantes" class="form-label">Demandantes</label>
                            <select class="select-demandantes form-select" id="select-demandantes" required>
                            </select>
                        </div>
                        <div class="modal-footer modal-footer-custom">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="salvarAlteracoes">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Excluir -->
    <div class="modal fade" id="excluirModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="excluirModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom-desativar">
                    <h5 class="modal-title" id="excluirModalLabel">Confirmar Desativação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalMessageDesativar" class="alert d-none" role="alert">
                    </div>
                    <p>Tem certeza de que deseja excluir este procedimento?</p>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Desativar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Procedimento -->
    <div class="modal fade" id="novoProcedimentoModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="novoProcedimentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="novoProcedimentoModalLabel">Novo Procedimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalMessageNovo" class="alert d-none" role="alert">
                    </div>
                    <form id="formNovoProcedimento">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="newNumeroProcedimento" class="form-label">Número Procedimento</label>
                                <input type="text" class="form-control" id="newNumeroProcedimento" name="numero_procedimento" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="newAnoProcedimento" class="form-label">Ano Procedimento</label>
                                <input type="text" class="form-control" id="newAnoProcedimento" name="ano_procedimento" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="new-select-bairros" class="form-label">Bairro</label>
                                <select class="new-select-bairros form-select" id="new-select-bairros">
                                </select>
                            </div>
                            <div class="col-md-6 classe-input-territorio">
                                <label for="newTerritorioBairro" class="form-label">Território Bairro</label>
                                <input type="text" class="form-control" id="newTerritorioBairro" name="territorio_bairro" disabled>
                            </div>
                            <!--<div class="col-md-6 classe-select-territorio" style="display: none;">
                                <label for="new-select-territorios" class="form-label">Território Bairro</label>
                                <select class="new-select-territorios form-select" id="new-select-territorios">
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <input type="text" class="form-control" id="newBairro" name="bairro" placeholder="Digite aqui se não encontou o Bairro acima">
                            </div>-->
                        </div>
                        <div class="separador-horizontal"><strong>Pessoa</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="new-select-pessoas" class="form-label">Nome Pessoa</label>
                                <select class="new-select-pessoas form-select" id="new-select-pessoas">
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12 mb-2">
                                <input type="text" class="form-control" id="newPessoa" name="pessoa" placeholder="Digite aqui se não encontou a pessoa acima">
                            </div>
                            <div class="col-md-6">
                                <label for="newDataNascimentoPessoa" class="form-label">Data Nascimento Pessoa</label>
                                <input type="date" class="form-control" id="newDataNascimentoPessoa" name="data_nascimento_pessoa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="new-select-sexos" class="form-label">Sexo</label>
                                <select class="new-select-sexos form-select" id="new-select-sexos" required>
                                </select>
                            </div>
                        </div>
                        <div class="separador-horizontal"><strong>Genitora/Responsável</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="new-select-genitoras" class="form-label">Nome Genitora/Responsável</label>
                                <select class="new-select-genitoras form-select" id="new-select-genitoras">
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12 mb-2">
                                <input type="text" class="form-control" id="newNomeGenitora" name="nome_genitora" placeholder="Digite aqui se não encontou a genitora/Responsável acima">
                            </div>
                            <div class="col-md-6">
                                <label for="newDataNascimentoGenitora" class="form-label">Data Nascimento</label>
                                <input type="date" class="form-control" id="newDataNascimentoGenitora" name="data_nascimento_genitora" required>
                            </div>
                            <div class="col-md-6">
                                <label for="new-select-sexos-genitora" class="form-label">Sexo</label>
                                <select class="new-select-sexos-genitora form-select" id="new-select-sexos-genitora" required>
                                </select>
                            </div>
                        </div>
                        <div class="separador-horizontal"><strong>Demandante</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="new-select-demandantes" class="form-label">Demandante</label>
                                <select class="new-select-demandantes form-select" id="new-select-demandantes">
                                </select>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control mt-3" id="newDemandante" name="demandante" placeholder="Digite aqui se não encontou o demandante acima">
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-custom">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary salvar-procedimento" disabled><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy" viewBox="0 0 16 16">
                                <path d="M11 2H9v3h2z"/>
                                <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z"/>
                                </svg> Salvar Procedimento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluindo Bootstrap JS (Bundle com Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Incluindo jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Incluindo nosso JavaScript externo para procedimentos -->
    <script src="assets/js/procedimentos_script.js"></script>
</body>
</html>
