<?php
// public/administracao/alterar_pessoa.php
session_start();

if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
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
<body class='alterarPessoa'>
    <div class="alterarPessoa container">
        <h4>Alterar Dados da Pessoa</h4>
        <p class="text-muted">Busque uma pessoa pelo nome e altere seus dados.</p>

        <div id="alertMessage" class="alert d-none" role="alert"></div>

        <form id="alterarPessoaForm">
            <input type="hidden" id="pessoa_id" name="id">

            <div class="mb-3 position-relative">
                <label for="nome_pessoa" class="form-label">Nome da Pessoa</label>
                <input type="text" class="form-control" id="nome_pessoa" name="nome" autocomplete="off" required>
                <div id="autocomplete-results" class="alterarPessoa autocomplete-suggestions"></div>
            </div>

            <div class="mb-3">
                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" disabled>
            </div>

            <div class="mb-3">
                <label for="sexo_id" class="form-label">Sexo</label>
                <select class="form-select" id="sexo_id" name="sexo_id" disabled>
                    <option value="">Selecione</option>
                    <!--<?php foreach ($sexos as $sexo): ?>
                        <option value="<?= htmlspecialchars($sexo->getId()) ?>"><?= htmlspecialchars($sexo->getNome()) ?></option>
                    <?php endforeach; ?>-->
                </select>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" disabled checked>
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>

            <button type="submit" class="btn btn-primary mt-3" id="btnSalvar" disabled><i class="fas fa-save"></i> Salvar Alterações</button>
            <button type="button" class="btn btn-danger mt-3" id="btnExcluir" disabled data-bs-toggle="modal" data-bs-target="#confirmDeactivateModal"><i class="fas fa-trash-alt"></i> Desativar Pessoa</button>
        </form>
    </div>

    <!-- Modal de Confirmação de Desativação -->
    <div class="modal fade" id="confirmDeactivateModal" tabindex="-1" aria-labelledby="confirmDeactivateModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeactivateModalLabel">Confirmar Desativação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p id="deactivate-modal-message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                    <button type="button" class="btn btn-danger" id="confirm-deactivate-btn"><i class="fas fa-trash-alt"></i> Sim, Desativar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nomePessoaInput = document.getElementById('nome_pessoa');
            const autocompleteResults = document.getElementById('autocomplete-results');
            const pessoaIdInput = document.getElementById('pessoa_id');
            const dataNascimentoInput = document.getElementById('data_nascimento');
            const sexoIdSelect = document.getElementById('sexo_id');
            const ativoCheckbox = document.getElementById('ativo');
            const btnSalvar = document.getElementById('btnSalvar');
            const btnExcluir = document.getElementById('btnExcluir');
            const alterarPessoaForm = document.getElementById('alterarPessoaForm');
            const alertMessage = document.getElementById('alertMessage');

            // Elementos do modal de desativação
            const confirmDeactivateModal = document.getElementById('confirmDeactivateModal');
            const deactivateModalMessage = document.getElementById('deactivate-modal-message');
            const confirmDeactivateBtn = document.getElementById('confirm-deactivate-btn');

            let debounceTimer;

            // Função para exibir mensagens de alerta
            function showAlert(message, type) {
                alertMessage.textContent = message;
                alertMessage.className = `alert alert-${type}`;
                alertMessage.classList.remove('d-none');
                setTimeout(() => {
                    alertMessage.classList.add('d-none');
                }, 5000); // Esconde a mensagem após 5 segundos
            }

            // Função para habilitar/desabilitar campos e botões
            function toggleFormFields(enable) {
                dataNascimentoInput.disabled = !enable;
                sexoIdSelect.disabled = !enable;
                ativoCheckbox.disabled = !enable;
                btnSalvar.disabled = !enable;
                btnExcluir.disabled = !enable;
            }

            // Inicialmente, desabilita os campos e botões de edição
            toggleFormFields(false);

            // Listener para o input de nome (autocomplete)
            nomePessoaInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) { // Começa a buscar após 2 caracteres
                    autocompleteResults.innerHTML = '';
                    autocompleteResults.classList.remove('d-block');
                    toggleFormFields(false); // Desabilita se a busca for muito curta
                    pessoaIdInput.value = ''; // Limpa o ID da pessoa selecionada
                    return;
                }

                debounceTimer = setTimeout(() => {
                    // Caminho da API ajustado para o novo local
                    fetch(`../../app/api/pessoas_api.php?action=search&query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            autocompleteResults.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(item => {
                                    const div = document.createElement('div');
                                    div.classList.add('alterarPessoa');
                                    div.classList.add('autocomplete-suggestion-item');
                                    div.textContent = item.nome;
                                    div.dataset.id = item.id;
                                    div.dataset.nome = item.nome;
                                    div.dataset.dataNascimento = item.dataNascimento || '';
                                    div.dataset.sexoId = item.sexoId || '';
                                    autocompleteResults.appendChild(div);
                                });
                                autocompleteResults.classList.add('d-block');
                            } else {
                                autocompleteResults.classList.remove('d-block');
                            }
                        })
                        .catch(error => {
                            console.error('Erro na busca de pessoas:', error);
                            showAlert('Erro ao buscar sugestões de pessoas.', 'danger');
                        });
                }, 300); // Pequeno atraso para evitar muitas requisições
            });

            // Listener para clique nas sugestões do autocomplete
            autocompleteResults.addEventListener('click', function(event) {
                const target = event.target;
                if (target.classList.contains('autocomplete-suggestion-item')) {
                    const pessoaId = target.dataset.id;
                    const pessoaNome = target.dataset.nome;

                    nomePessoaInput.value = pessoaNome; // Preenche o input com o nome selecionado
                    pessoaIdInput.value = pessoaId;     // Armazena o ID da pessoa
                    autocompleteResults.innerHTML = ''; // Limpa as sugestões
                    autocompleteResults.classList.remove('d-block');

                    // Caminho da API ajustado para o novo local
                    fetch(`../../app/api/pessoas_api.php?action=get_details&id=${encodeURIComponent(pessoaId)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.id) {
                                dataNascimentoInput.value = data.dataNascimento || '';
                                sexoIdSelect.value = data.sexoId || '';
                                ativoCheckbox.checked = data.ativo;
                                toggleFormFields(true); // Habilita os campos e botões para edição
                                // Se a pessoa já estiver inativa, desabilita o checkbox e o botão de desativar
                                if (!data.ativo) {
                                    ativoCheckbox.disabled = false;
                                    btnExcluir.disabled = true; // Desabilita o botão de desativar se já estiver inativo
                                } else {
                                    ativoCheckbox.disabled = true; // Habilita o checkbox se estiver ativo
                                    btnExcluir.disabled = false; // Habilita o botão de desativar se estiver ativo
                                }
                            } else {
                                showAlert('Não foi possível carregar os detalhes da pessoa.', 'danger');
                                toggleFormFields(false);
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao carregar detalhes da pessoa:', error);
                            showAlert('Erro ao carregar detalhes da pessoa.', 'danger');
                            toggleFormFields(false);
                        });
                }
            });

            // Listener para envio do formulário (Salvar Alterações)
            alterarPessoaForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                formData.append('action', 'update');
                formData.append('id', pessoaIdInput.value); // Garante que o ID está no FormData

                // Caminho da API ajustado para o novo local
                fetch('../../app/api/pessoas_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Opcional: Limpar formulário ou recarregar detalhes
                        nomePessoaInput.value = '';
                        pessoaIdInput.value = '';
                        dataNascimentoInput.value = '';
                        sexoIdSelect.value = '';
                        ativoCheckbox.checked = true; // Volta para o padrão
                        toggleFormFields(false);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao salvar alterações:', error);
                    showAlert('Erro de comunicação ao salvar alterações.', 'danger');
                });
            });

            // Lógica para o Modal de Confirmação de Desativação
            confirmDeactivateModal.addEventListener('show.bs.modal', function (event) {
                const pessoaId = pessoaIdInput.value;
                const pessoaNome = nomePessoaInput.value;
                if (pessoaId) {
                    deactivateModalMessage.innerHTML = `Deseja realmente desativar a pessoa <u><strong>${pessoaNome}</strong></u>?`;
                    confirmDeactivateBtn.disabled = false; // Habilita o botão de confirmação
                } else {
                    deactivateModalMessage.textContent = 'Nenhuma pessoa selecionada para desativação.';
                    confirmDeactivateBtn.disabled = true; // Desabilita o botão de confirmação
                }
            });

            // Listener para o botão "Sim, Desativar" dentro do modal
            confirmDeactivateBtn.addEventListener('click', function() {
                const pessoaId = pessoaIdInput.value;
                if (!pessoaId) {
                    showAlert('Nenhuma pessoa selecionada para desativação.', 'danger');
                    const modal = bootstrap.Modal.getInstance(confirmDeactivateModal);
                    modal.hide();
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'deactivate');
                formData.append('id', pessoaId);

                // Caminho da API ajustado para o novo local
                fetch('../../app/api/pessoas_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Limpa o formulário após a desativação
                        nomePessoaInput.value = '';
                        pessoaIdInput.value = '';
                        dataNascimentoInput.value = '';
                        sexoIdSelect.value = '';
                        ativoCheckbox.checked = true;
                        toggleFormFields(false);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                    const modal = bootstrap.Modal.getInstance(confirmDeactivateModal);
                    modal.hide();
                })
                .catch(error => {
                    console.error('Erro ao desativar pessoa:', error);
                    showAlert('Erro de comunicação ao desativar pessoa.', 'danger');
                    const modal = bootstrap.Modal.getInstance(confirmDeactivateModal);
                    modal.hide();
                });
            });
        });
    </script>
</body>
</html>
