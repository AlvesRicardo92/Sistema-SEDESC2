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
        // Inclui o cabeçalho, assumindo que está em um diretório acima do atual e dentro de 'utils'
        // require __DIR__ . '/utils/cabecalho.php'; // Descomente se tiver um cabeçalho PHP
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
                <label for="searchGenitora" class="form-label">Genitora</label>
                <input type="text" class="form-control form-control-custom" id="searchGenitora" name="genitora">
            </div>
            <div class="col-md-3 mb-3">
                <label for="searchNascimento" class="form-label">Nascimento</label>
                <input type="date" class="form-control form-control-custom" id="searchNascimento" name="nascimento">
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="d-flex justify-content-between mb-4">
            <button type="button" class="btn btn-primary btn-custom" id="btnProcurar">Procurar</button>
            <button type="button" class="btn btn-success btn-custom" id="btnNovoProcedimento" data-bs-toggle="modal" data-bs-target="#novoProcedimentoModal">Novo Procedimento</button>
        </div>

        <!-- Tabela de Resultados -->
        <div class="table-responsive table-responsive-custom">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Número/Ano</th>
                        <th>Nome</th>
                        <th>Genitora</th>
                        <th>Nascimento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="procedimentosTableBody">
                    <tr>
                        <td colspan="5" class="text-center text-muted">Digite um dos campos acima e clique em Procurar</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modais -->

    <!-- Modal Visualizar -->
    <div class="modal fade" id="visualizarModal" tabindex="-1" aria-labelledby="visualizarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="visualizarModalLabel">Visualizar Procedimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewNumeroProcedimento" class="form-label">Número Procedimento</label>
                                <input type="text" class="form-control" id="viewNumeroProcedimento" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="viewAnoProcedimento" class="form-label">Ano Procedimento</label>
                                <input type="text" class="form-control" id="viewAnoProcedimento" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewBairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="viewBairro" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="viewTerritorioBairro" class="form-label">Território Bairro</label>
                                <input type="text" class="form-control" id="viewTerritorioBairro" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewNomePessoa" class="form-label">Nome Pessoa</label>
                                <input type="text" class="form-control" id="viewNomePessoa" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="viewDataNascimentoPessoa" class="form-label">Data Nascimento Pessoa</label>
                                <input type="date" class="form-control" id="viewDataNascimentoPessoa" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewSexoPessoa" class="form-label">Sexo Pessoa</label>
                                <input type="text" class="form-control" id="viewSexoPessoa" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="viewNomeGenitora" class="form-label">Nome Genitora</label>
                                <input type="text" class="form-control" id="viewNomeGenitora" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="viewDataNascimentoGenitora" class="form-label">Data Nascimento Genitora</label>
                                <input type="date" class="form-control" id="viewDataNascimentoGenitora" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="viewSexoGenitora" class="form-label">Sexo Genitora</label>
                                <input type="text" class="form-control" id="viewSexoGenitora" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="viewDemandante" class="form-label">Demandante</label>
                            <input type="text" class="form-control" id="viewDemandante" readonly>
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
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="editarModalLabel">Editar Procedimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarProcedimento">
                        <input type="hidden" id="editProcedimentoId" name="id">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editNumeroProcedimento" class="form-label">Número Procedimento</label>
                                <input type="text" class="form-control" id="editNumeroProcedimento" name="numero_procedimento" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editAnoProcedimento" class="form-label">Ano Procedimento</label>
                                <input type="text" class="form-control" id="editAnoProcedimento" name="ano_procedimento" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editBairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="editBairro" name="bairro" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editTerritorioBairro" class="form-label">Território Bairro</label>
                                <input type="text" class="form-control" id="editTerritorioBairro" name="territorio_bairro" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editNomePessoa" class="form-label">Nome Pessoa</label>
                                <input type="text" class="form-control" id="editNomePessoa" name="nome_pessoa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editDataNascimentoPessoa" class="form-label">Data Nascimento Pessoa</label>
                                <input type="date" class="form-control" id="editDataNascimentoPessoa" name="data_nascimento_pessoa" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editSexoPessoa" class="form-label">Sexo Pessoa</label>
                                <input type="text" class="form-control" id="editSexoPessoa" name="sexo_pessoa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editNomeGenitora" class="form-label">Nome Genitora</label>
                                <input type="text" class="form-control" id="editNomeGenitora" name="nome_genitora" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editDataNascimentoGenitora" class="form-label">Data Nascimento Genitora</label>
                                <input type="date" class="form-control" id="editDataNascimentoGenitora" name="data_nascimento_genitora" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editSexoGenitora" class="form-label">Sexo Genitora</label>
                                <input type="text" class="form-control" id="editSexoGenitora" name="sexo_genitora" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editDemandante" class="form-label">Demandante</label>
                            <input type="text" class="form-control" id="editDemandante" name="demandante" required>
                        </div>
                        <div class="modal-footer modal-footer-custom">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Excluir -->
    <div class="modal fade" id="excluirModal" tabindex="-1" aria-labelledby="excluirModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="excluirModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza de que deseja excluir este procedimento?</p>
                    <input type="hidden" id="deleteProcedimentoId">
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Procedimento -->
    <div class="modal fade" id="novoProcedimentoModal" tabindex="-1" aria-labelledby="novoProcedimentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="novoProcedimentoModalLabel">Novo Procedimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNovoProcedimento">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="newNumeroProcedimento" class="form-label">Número Procedimento</label>
                                <input type="text" class="form-control" id="newNumeroProcedimento" name="numero_procedimento" required>
                            </div>
                            <div class="col-md-6">
                                <label for="newAnoProcedimento" class="form-label">Ano Procedimento</label>
                                <input type="text" class="form-control" id="newAnoProcedimento" name="ano_procedimento" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="newBairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="newBairro" name="bairro" required>
                            </div>
                            <div class="col-md-6">
                                <label for="newTerritorioBairro" class="form-label">Território Bairro</label>
                                <input type="text" class="form-control" id="newTerritorioBairro" name="territorio_bairro" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="newNomePessoa" class="form-label">Nome Pessoa</label>
                                <input type="text" class="form-control" id="newNomePessoa" name="nome_pessoa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="newDataNascimentoPessoa" class="form-label">Data Nascimento Pessoa</label>
                                <input type="date" class="form-control" id="newDataNascimentoPessoa" name="data_nascimento_pessoa" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="newSexoPessoa" class="form-label">Sexo Pessoa</label>
                                <input type="text" class="form-control" id="newSexoPessoa" name="sexo_pessoa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="newNomeGenitora" class="form-label">Nome Genitora</label>
                                <input type="text" class="form-control" id="newNomeGenitora" name="nome_genitora" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="newDataNascimentoGenitora" class="form-label">Data Nascimento Genitora</label>
                                <input type="date" class="form-control" id="newDataNascimentoGenitora" name="data_nascimento_genitora" required>
                            </div>
                            <div class="col-md-6">
                                <label for="newSexoGenitora" class="form-label">Sexo Genitora</label>
                                <input type="text" class="form-control" id="newSexoGenitora" name="sexo_genitora" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="newDemandante" class="form-label">Demandante</label>
                            <input type="text" class="form-control" id="newDemandante" name="demandante" required>
                        </div>
                        <div class="modal-footer modal-footer-custom">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Procedimento</button>
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
