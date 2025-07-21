<?php
// public/migracao_procedimento.php
session_start();

if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
}

// Inclua os DAOs e Models necessários
require_once __DIR__ . '/../app/dao/DatabaseDAO.php';
require_once __DIR__ . '/../app/Models/Procedimento.php';
require_once __DIR__ . '/../app/Models/Pessoa.php';
require_once __DIR__ . '/../app/Models/Bairro.php';
require_once __DIR__ . '/../app/Models/Territorio.php';
require_once __DIR__ . '/../app/Models/MotivoMigracao.php'; // Adicionado
require_once __DIR__ . '/../app/dao/ProcedimentoDAO.php';
require_once __DIR__ . '/../app/dao/BairroDAO.php';
require_once __DIR__ . '/../app/dao/MotivoMigracaoDAO.php'; // Adicionado

use App\dao\ProcedimentoDAO;
use App\dao\BairroDAO;
use App\dao\MotivoMigracaoDAO;

$procedimentoDAO = new ProcedimentoDAO();
$bairroDAO = new BairroDAO();
$motivoMigracaoDAO = new MotivoMigracaoDAO();

// Dados iniciais para os selects, podem ser carregados via AJAX na busca também
// $bairros = $bairroDAO->getAllActive(); // Carregar todos os bairros inicialmente, ou via AJAX
// $motivosMigracao = $motivoMigracaoDAO->getAllActive(); // Carregar todos os motivos, ou via AJAX

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
    <?php include '../app/views/navbar.php'; // Incluir a barra de navegação ?>

    <div class="container">
        <h2 class="mb-4 text-center">Migrar Procedimento</h2>

        <div id="alertContainer"></div>

        <form id="searchProcedimentoForm" class="mb-4">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="search_numero" class="form-label">Número do Procedimento</label>
                    <input type="number" class="form-control" id="search_numero" name="numero" required>
                </div>
                <div class="col-md-5">
                    <label for="search_ano" class="form-label">Ano do Procedimento</label>
                    <input type="number" class="form-control" id="search_ano" name="ano" required min="1900" max="<?= date('Y') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </div>
            </div>
        </form>

        <hr>

        <div id="procedimentoDetails" style="display: none;">
            <h4 class="mb-3">Detalhes do Procedimento Original</h4>
            <input type="hidden" id="procedimento_id_original">
            <input type="hidden" id="procedimento_territorio_original_id">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome_pessoa_principal" class="form-label">Nome da Pessoa Principal</label>
                    <input type="text" class="form-control" id="nome_pessoa_principal" disabled>
                </div>
                <div class="col-md-6">
                    <label for="data_nascimento_pessoa_principal" class="form-label">Data de Nascimento</label>
                    <input type="text" class="form-control" id="data_nascimento_pessoa_principal" disabled>
                </div>
            </div>

            <div id="migrationSection">
                <h4 class="mb-3 mt-4">Dados para Nova Migração</h4>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="new_bairro_id" class="form-label">Selecionar Novo Bairro</label>
                        <select class="form-select" id="new_bairro_id" required>
                            <option value="">Selecione um Bairro</option>
                            </select>
                    </div>
                    <div class="col-md-6">
                        <label for="new_bairro_territorio_nome" class="form-label">Território do Novo Bairro</label>
                        <input type="text" class="form-control" id="new_bairro_territorio_nome" disabled>
                        <input type="hidden" id="new_territorio_id">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="motivo_migracao_id" class="form-label">Motivo da Migração</label>
                    <select class="form-select" id="motivo_migracao_id" required>
                        <option value="">Selecione o Motivo</option>
                        </select>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn btn-success" id="btnMigrar">Migrar Procedimento</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchProcedimentoForm = document.getElementById('searchProcedimentoForm');
            const procedimentoDetails = document.getElementById('procedimentoDetails');
            const alertContainer = document.getElementById('alertContainer');
            const procedimentoIdOriginalInput = document.getElementById('procedimento_id_original');
            const procedimentoTerritorioOriginalIdInput = document.getElementById('procedimento_territorio_original_id');
            const nomePessoaPrincipalInput = document.getElementById('nome_pessoa_principal');
            const dataNascimentoPessoaPrincipalInput = document.getElementById('data_nascimento_pessoa_principal');
            const newBairroSelect = document.getElementById('new_bairro_id');
            const newBairroTerritorioNomeInput = document.getElementById('new_bairro_territorio_nome');
            const newTerritorioIdInput = document.getElementById('new_territorio_id');
            const motivoMigracaoSelect = document.getElementById('motivo_migracao_id');
            const btnMigrar = document.getElementById('btnMigrar');
            const migrationSection = document.getElementById('migrationSection');

            function showAlert(message, type) {
                alertContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                                ${message}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>`;
            }

            function clearForm() {
                procedimentoDetails.style.display = 'none';
                nomePessoaPrincipalInput.value = '';
                dataNascimentoPessoaPrincipalInput.value = '';
                newBairroSelect.innerHTML = '<option value="">Selecione um Bairro</option>';
                newBairroTerritorioNomeInput.value = '';
                newTerritorioIdInput.value = '';
                motivoMigracaoSelect.innerHTML = '<option value="">Selecione o Motivo</option>';
                migrationSection.style.display = 'block'; // Ensure migration section is visible by default for new searches
                alertContainer.innerHTML = ''; // Clear previous alerts
            }

            // Function to update territory field based on selected bairro
            function updateTerritoryField(bairroSelectId, territorioNomeInputId, territorioIdInputId) {
                const bairroSelect = document.getElementById(bairroSelectId);
                const territorioNomeInput = document.getElementById(territorioNomeInputId);
                const territorioIdInput = document.getElementById(territorioIdInputId);

                const selectedOption = bairroSelect.options[bairroSelect.selectedIndex];
                territorioNomeInput.value = selectedOption.dataset.territorioNome || '';
                territorioIdInput.value = selectedOption.dataset.territorioId || '';
            }

            // Event listener for bairro select change
            newBairroSelect.addEventListener('change', function() {
                updateTerritoryField('new_bairro_id', 'new_bairro_territorio_nome', 'new_territorio_id');
            });

            searchProcedimentoForm.addEventListener('submit', function(event) {
                event.preventDefault();
                clearForm(); // Clear form on new search

                const numero = document.getElementById('search_numero').value;
                const ano = document.getElementById('search_ano').value;

                if (!numero || !ano) {
                    showAlert('Por favor, preencha o número e o ano do procedimento.', 'warning');
                    return;
                }

                fetch(`../app/api/migracao_api.php?action=search_procedimento&numero=${numero}&ano=${ano}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            procedimentoDetails.style.display = 'block';
                            procedimentoIdOriginalInput.value = data.data.id;
                            procedimentoTerritorioOriginalIdInput.value = data.data.territorioOriginalId;
                            nomePessoaPrincipalInput.value = data.data.pessoaPrincipalNome;
                            dataNascimentoPessoaPrincipalInput.value = data.data.pessoaPrincipalDataNascimento;

                            // Populate Bairros
                            newBairroSelect.innerHTML = '<option value="">Selecione um Bairro</option>';
                            data.bairros.forEach(bairro => {
                                const option = document.createElement('option');
                                option.value = bairro.id;
                                option.textContent = bairro.nome;
                                option.dataset.territorioId = bairro.territorio_id;
                                option.dataset.territorioNome = bairro.territorio_nome;
                                newBairroSelect.appendChild(option);
                            });

                            // Populate Motivos de Migração
                            motivoMigracaoSelect.innerHTML = '<option value="">Selecione o Motivo</option>';
                            data.motivosMigracao.forEach(motivo => {
                                const option = document.createElement('option');
                                option.value = motivo.id;
                                option.textContent = motivo.nome;
                                motivoMigracaoSelect.appendChild(option);
                            });

                            migrationSection.style.display = 'block';
                            showAlert('Procedimento encontrado. Prossiga com a migração.', 'info');

                        } else {
                            if (data.migrated) {
                                showAlert(data.message, 'warning');
                                procedimentoDetails.style.display = 'block'; // Show details to display the message
                                migrationSection.style.display = 'none'; // Hide migration section
                            } else {
                                showAlert(data.message, 'danger');
                                procedimentoDetails.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erro na busca do procedimento:', error);
                        showAlert('Erro de comunicação ao buscar procedimento.', 'danger');
                        procedimentoDetails.style.display = 'none';
                    });
            });

            btnMigrar.addEventListener('click', function() {
                const idProcedimento = procedimentoIdOriginalInput.value;
                const newBairroId = newBairroSelect.value;
                const idMotivoMigracao = motivoMigracaoSelect.value;

                if (!idProcedimento || !newBairroId || !idMotivoMigracao) {
                    showAlert('Por favor, preencha todos os campos obrigatórios para a migração (Bairro e Motivo).', 'warning');
                    return;
                }

                if (!confirm('Tem certeza que deseja migrar este procedimento? Esta ação não pode ser desfeita.')) {
                    return;
                }

                const formData = new URLSearchParams();
                formData.append('action', 'migrate_procedimento');
                formData.append('id_procedimento', idProcedimento);
                formData.append('new_bairro_id', newBairroId);
                formData.append('id_motivo_migracao', idMotivoMigracao);

                fetch('../app/api/migracao_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Optionally clear the form or redirect after successful migration
                        searchProcedimentoForm.reset();
                        clearForm();
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao migrar procedimento:', error);
                    showAlert('Erro de comunicação ao migrar procedimento.', 'danger');
                });
            });
        });
    </script>
</body>
</html>