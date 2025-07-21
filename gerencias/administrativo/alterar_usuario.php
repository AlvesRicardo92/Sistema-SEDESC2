<?php
// public/administracao/alterar_usuario.php
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
    <title>Alterar Usuário - Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body classe="alterarUsuario">
    <div class="alterarUsuario container">
        <h4>Alterar Dados do Usuário</h4>
        <p class="text-muted">Busque um usuário pelo nome de usuário e altere seus dados.</p>

        <div id="alertMessage" class="alert d-none" role="alert"></div>

        <form id="alterarUsuarioForm">
            <input type="hidden" id="user_id" name="id">

            <div class="mb-3 d-flex">
                <div class="flex-grow-1 me-2">
                    <label for="username_search" class="form-label">Nome de Usuário</label>
                    <input type="text" class="alterarUsuario form-control" id="username_search" name="username_search" required>
                </div>
                <button type="button" class="btn btn-primary align-self-end mb-3" id="btnBuscarUsuario"><i class="fas fa-search"></i> Buscar</button>
            </div>

            <div class="mb-3">
                <label for="nomeCompleto" class="form-label">Nome Completo</label>
                <input type="text" class="alterarUsuario form-control" id="nomeCompleto" name="fullName" disabled>
            </div>

            <div class="mb-3">
                <label for="territorio_id" class="form-label">Território</label>
                <select class="form-select" id="territorio_id" name="territorio_id" disabled>
                    <option value="">Selecione um Território</option>
                    <!--<?php foreach ($territorios as $territorio): ?>
                        <option value="<?= htmlspecialchars($territorio->getId()) ?>"><?= htmlspecialchars($territorio->getNome()) ?></option>
                    <?php endforeach; ?>-->
                </select>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" disabled>
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="primeiro_acesso" name="primeiro_acesso" value="1" disabled>
                <label class="form-check-label" for="primeiro_acesso">Primeiro Acesso (forçar troca de senha)</label>
            </div>

            <!-- Seção de Permissões -->
            <h5 class="alterarUsuario">Permissões:</h5>
            <div class="alterarUsuario permission-section">
                <h5>Telas</h5>
                <div class="alterarUsuario permission-row">
                    <div class="alterarUsuario permission-label">Procedimentos</div>
                    <div class="alterarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_none" value="0" disabled>
                            <label class="form-check-label" for="perm_0_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_view" value="1" disabled>
                            <label class="form-check-label" for="perm_0_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_edit" value="2" disabled>
                            <label class="form-check-label" for="perm_0_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_delete" value="3" disabled>
                            <label class="form-check-label" for="perm_0_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_create" value="4" disabled>
                            <label class="form-check-label" for="perm_0_create">Criar</label>
                        </div>
                    </div>
                </div>
                <hr class="alterarUsuario permission-divider">

                <div class="alterarUsuario permission-row">
                    <div class="alterarUsuario permission-label">Ofício Recebido</div>
                    <div class="alterarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_none" value="0" disabled>
                            <label class="form-check-label" for="perm_1_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_view" value="1" disabled>
                            <label class="form-check-label" for="perm_1_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_edit" value="2" disabled>
                            <label class="form-check-label" for="perm_1_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_delete" value="3" disabled>
                            <label class="form-check-label" for="perm_1_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_create" value="4" disabled>
                            <label class="form-check-label" for="perm_1_create">Criar</label>
                        </div>
                    </div>
                </div>
                <hr class="alterarUsuario permission-divider">

                <div class="alterarUsuario permission-row">
                    <div class="alterarUsuario permission-label">Ofício Expedido</div>
                    <div class="alterarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_none" value="0" disabled>
                            <label class="form-check-label" for="perm_2_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_view" value="1" disabled>
                            <label class="form-check-label" for="perm_2_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_edit" value="2" disabled>
                            <label class="form-check-label" for="perm_2_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_delete" value="3" disabled>
                            <label class="form-check-label" for="perm_2_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_create" value="4" disabled>
                            <label class="form-check-label" for="perm_2_create">Criar</label>
                        </div>
                    </div>
                </div>
                <hr class="alterarUsuario permission-divider">

                <div class="alterarUsuario permission-row">
                    <div class="alterarUsuario permission-label">Denúncia</div>
                    <div class="alterarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_none" value="0" disabled>
                            <label class="form-check-label" for="perm_3_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_view" value="1" disabled>
                            <label class="form-check-label" for="perm_3_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_edit" value="2" disabled>
                            <label class="form-check-label" for="perm_3_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_delete" value="3" disabled>
                            <label class="form-check-label" for="perm_3_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_create" value="4" disabled>
                            <label class="form-check-label" for="perm_3_create">Criar</label>
                        </div>
                    </div>
                </div>
                <hr class="alterarUsuario permission-divider">

                <div class="alterarUsuario permission-row">
                    <div class="alterarUsuario permission-label">Atendimento Presencial</div>
                    <div class="alterarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_none" value="0" disabled>
                            <label class="form-check-label" for="perm_4_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_view" value="1" disabled>
                            <label class="form-check-label" for="perm_4_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_edit" value="2" disabled>
                            <label class="form-check-label" for="perm_4_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_delete" value="3" disabled>
                            <label class="form-check-label" for="perm_4_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_create" value="4" disabled>
                            <label class="form-check-label" for="perm_4_create">Criar</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alterarUsuario permission-section">
            <h5 class="alterarUsuario">Administração</h5>
                <p class="text-muted">Acesso ao menu "Administração" no cabeçalho será concedido se qualquer uma das opções abaixo for marcada.</p>
                <div class="alterarUsuario admin-checkbox-grid">
                    <div class="form-check">
                        <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_6_criar_usuario" data-position="6" value="1" disabled>
                        <label class="form-check-label" for="perm_6_criar_usuario">Criar Usuário</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_7_resetar_senha" data-position="7" value="1" disabled>
                        <label class="form-check-label" for="perm_7_resetar_senha">Resetar Senha</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_8_alterar_pessoa" data-position="8" value="1" disabled>
                        <label class="form-check-label" for="perm_8_alterar_pessoa">Alterar Pessoa</label>
                    </div>
                </div>
            </div>

            <input type="hidden" id="permissoesUsuario" name="permissoes">

            <button type="submit" class="btn btn-primary mt-3" id="btnSalvar" disabled><i class="fas fa-save"></i> Salvar Alterações</button>
            <button type="button" class="btn btn-danger mt-3" id="btnDesativar" disabled data-bs-toggle="modal" data-bs-target="#confirmDeactivateModal"><i class="fas fa-user-slash"></i> Desativar Usuário</button>
            <button type="button" class="btn btn-success mt-3" id="btnAtivar" disabled><i class="fas fa-user-plus"></i> Ativar Usuário</button>
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
                    <button type="button" class="btn btn-danger" id="confirm-deactivate-btn"><i class="fas fa-user-slash"></i> Sim, Desativar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usernameSearchInput = document.getElementById('username_search');
            const btnBuscarUsuario = document.getElementById('btnBuscarUsuario');
            const userIdInput = document.getElementById('user_id');
            const nomeCompletoInput = document.getElementById('nomeCompleto');
            const territorioSelect = document.getElementById('territorio_id');
            const ativoCheckbox = document.getElementById('ativo');
            const primeiroAcessoCheckbox = document.getElementById('primeiro_acesso');
            const permissoesInput = document.getElementById('permissoesUsuario');
            const btnSalvar = document.getElementById('btnSalvar');
            const btnDesativar = document.getElementById('btnDesativar');
            const btnAtivar = document.getElementById('btnAtivar');
            const alterarUsuarioForm = document.getElementById('alterarUsuarioForm');
            const alertMessage = document.getElementById('alertMessage');

            // Elementos do modal de desativação
            const confirmDeactivateModal = document.getElementById('confirmDeactivateModal');
            const deactivateModalMessage = document.getElementById('deactivate-modal-message');
            const confirmDeactivateBtn = document.getElementById('confirm-deactivate-btn');

            let currentPermissoes = '0'.repeat(9); // String de 9 caracteres para permissões

            // Função para exibir mensagens de alerta
            function showAlert(message, type) {
                alertMessage.textContent = message;
                alertMessage.className = `alert alert-${type}`;
                alertMessage.classList.remove('d-none');
                setTimeout(() => {
                    alertMessage.classList.add('d-none');
                }, 5000);
            }

            // Função para habilitar/desabilitar campos e botões
            function toggleFormFields(enable) {
                nomeCompletoInput.disabled = !enable;
                territorioSelect.disabled = !enable;
                ativoCheckbox.disabled = !enable;
                primeiroAcessoCheckbox.disabled = !enable;
                btnSalvar.disabled = !enable;
                
                document.querySelectorAll('.perm-radio').forEach(radio => radio.disabled = !enable);
                document.querySelectorAll('.admin-perm-checkbox').forEach(checkbox => checkbox.disabled = !enable);

                // Lógica para ativar/desativar botões de acordo com o status 'ativo' do usuário
                if (enable) {
                    if (ativoCheckbox.checked) {
                        btnDesativar.disabled = false;
                        btnAtivar.disabled = true;
                    } else {
                        btnDesativar.disabled = true;
                        btnAtivar.disabled = false;
                    }
                } else {
                    btnDesativar.disabled = true;
                    btnAtivar.disabled = true;
                }
            }

            // Função para preencher os radio buttons e checkboxes de permissões
            function setPermissionsOnForm(permissionsString) {
                // Preenche as permissões de 0 a 4 (Telas)
                for (let i = 0; i <= 4; i++) {
                    const value = permissionsString[i] || '0';
                    // Mapeia o valor para o sufixo do ID do rádio
                    let suffix = '';
                    switch (value) {
                        case '0': suffix = 'none'; break;
                        case '1': suffix = 'view'; break;
                        case '2': suffix = 'edit'; break;
                        case '3': suffix = 'delete'; break;
                        case '4': suffix = 'create'; break;
                        default: suffix = 'none'; break;
                    }
                    const radio = document.getElementById(`perm_${i}_${suffix}`);
                    if (radio) {
                        radio.checked = true;
                    } else {
                        // Fallback para 'Nenhum' se o valor não corresponder
                        document.getElementById(`perm_${i}_none`).checked = true;
                    }
                }

                // Preenche as permissões de 6 a 8 (Administração)
                for (let i = 6; i <= 8; i++) {
                    const checkbox = document.querySelector(`.admin-perm-checkbox[data-position="${i}"]`);
                    if (checkbox) {
                        checkbox.checked = (permissionsString[i] === '1');
                    }
                }
                updatePermissoesString(); // Garante que a string interna esteja atualizada
            }

            // Função para atualizar a string de permissões (igual ao criar_usuario.php)
            function updatePermissoesString() {
                let tempPermissoes = '0'.repeat(9).split(''); // Inicia com 9 zeros

                // Posições 0 a 4 (Telas)
                for (let i = 0; i <= 4; i++) { 
                    const selectedRadio = document.querySelector(`input[name="perm_${i}"]:checked`);
                    if (selectedRadio) {
                        tempPermissoes[i] = selectedRadio.value;
                    }
                }

                // Posição 5: Acesso geral ao menu "Administração"
                const adminCheckboxes = document.querySelectorAll('.admin-perm-checkbox');
                let adminAccessGranted = false;
                adminCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        adminAccessGranted = true;
                    }
                });
                tempPermissoes[5] = adminAccessGranted ? '1' : '0';

                // Posições 6, 7, 8 (Criar Usuário, Resetar Senha, Alterar Pessoa)
                adminCheckboxes.forEach(checkbox => {
                    const position = parseInt(checkbox.dataset.position);
                    if (checkbox.checked) {
                        tempPermissoes[position] = '1';
                    } else {
                        tempPermissoes[position] = '0';
                    }
                });

                currentPermissoes = tempPermissoes.join('');
                permissoesInput.value = currentPermissoes;
                console.log("Permissões geradas:", currentPermissoes);
            }

            // Função para buscar detalhes do usuário e preencher o formulário
            function fetchUserDetails(username, suppressAlert = false) {
                // Limpa a mensagem de alerta apenas quando uma nova busca explícita é realizada
                // Isso evita sobrescrever as mensagens de sucesso de salvar/ativar
                if (!suppressAlert) {
                    alertMessage.classList.add('d-none'); // Esconde qualquer alerta anterior
                    alertMessage.textContent = ''; // Limpa o texto
                }

                if (username.length === 0) {
                    if (!suppressAlert) {
                        showAlert('Por favor, digite o nome de usuário completo para buscar.', 'warning');
                    }
                    toggleFormFields(false);
                    userIdInput.value = '';
                    nomeCompletoInput.value = '';
                    territorioSelect.value = '';
                    ativoCheckbox.checked = false;
                    primeiroAcessoCheckbox.checked = false;
                    setPermissionsOnForm('0'.repeat(9)); // Reseta permissões visuais
                    return;
                }

                fetch(`../../app/api/usuarios_api.php?action=get_user_details&username=${encodeURIComponent(username)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            const user = data.data;
                            userIdInput.value = user.id;
                            nomeCompletoInput.value = user.fullName;
                            territorioSelect.value = user.territorioId || '';
                            ativoCheckbox.checked = user.ativo;
                            primeiroAcessoCheckbox.checked = user.primeiroAcesso;
                            setPermissionsOnForm(user.permissions); // Preenche as permissões
                            toggleFormFields(true); // Habilita os campos para edição
                            if (!suppressAlert) { // Exibe o alerta "Usuário encontrado!" apenas se não for suprimido
                                showAlert('Usuário encontrado!', 'success');
                            }
                        } else {
                            // Exibe o erro apenas se não estiver suprimindo alertas (ou seja, é uma busca direta)
                            if (!suppressAlert) {
                                showAlert(data.message || 'Usuário não encontrado.', 'danger');
                            }
                            toggleFormFields(false); // Desabilita se não encontrar
                            userIdInput.value = '';
                            nomeCompletoInput.value = '';
                            territorioSelect.value = '';
                            ativoCheckbox.checked = false;
                            primeiroAcessoCheckbox.checked = false;
                            setPermissionsOnForm('0'.repeat(9)); // Reseta permissões visuais
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar usuário:', error);
                        // Exibe o erro apenas se não estiver suprimindo alertas
                        if (!suppressAlert) {
                            showAlert('Erro de comunicação ao buscar usuário.', 'danger');
                        }
                        toggleFormFields(false);
                        userIdInput.value = '';
                        nomeCompletoInput.value = '';
                        territorioSelect.value = '';
                        ativoCheckbox.checked = false;
                        primeiroAcessoCheckbox.checked = false;
                        setPermissionsOnForm('0'.repeat(9)); // Reseta permissões visuais
                    });
            }

            // Inicialmente, desabilita os campos e botões de edição
            toggleFormFields(false);

            // Adiciona listeners para os radio buttons e checkboxes de permissão
            document.querySelectorAll('.perm-radio, .admin-perm-checkbox').forEach(element => {
                element.addEventListener('change', updatePermissoesString);
            });

            // Listener para o botão Buscar Usuário
            btnBuscarUsuario.addEventListener('click', function() {
                fetchUserDetails(usernameSearchInput.value.trim());
            });

            // Listener para envio do formulário (Salvar Alterações)
            alterarUsuarioForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const userId = userIdInput.value;
                if (!userId) {
                    showAlert('Nenhum usuário selecionado para salvar.', 'danger');
                    return;
                }

                updatePermissoesString(); // Garante que a string de permissões está atualizada

                const formData = new URLSearchParams();
                formData.append('action', 'update_user');
                formData.append('id', userId);
                formData.append('username', usernameSearchInput.value); // O username é o do campo de busca
                formData.append('fullName', nomeCompletoInput.value);
                formData.append('territorio_id', territorioSelect.value);
                formData.append('permissions', permissoesInput.value);
                formData.append('ativo', ativoCheckbox.checked ? '1' : '0');
                formData.append('primeiro_acesso', primeiroAcessoCheckbox.checked ? '1' : '0');

                fetch('../../app/api/usuarios_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Após salvar, recarrega os detalhes para atualizar o estado dos botões, suprimindo o alerta de "Usuário encontrado!"
                        fetchUserDetails(usernameSearchInput.value.trim(), true); 
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
                const userId = userIdInput.value;
                const username = usernameSearchInput.value;
                if (userId) {
                    deactivateModalMessage.innerHTML = `Deseja realmente desativar o usuário <u><strong>${username}</strong></u>?`;
                    confirmDeactivateBtn.disabled = false;
                } else {
                    deactivateModalMessage.textContent = 'Nenhum usuário selecionado para desativação.';
                    confirmDeactivateBtn.disabled = true;
                }
            });

            // Listener para o botão "Sim, Desativar" dentro do modal
            confirmDeactivateBtn.addEventListener('click', function() {
                const userId = userIdInput.value;
                if (!userId) {
                    showAlert('Nenhum usuário selecionado para desativação.', 'danger');
                    const modal = bootstrap.Modal.getInstance(confirmDeactivateModal);
                    modal.hide();
                    return;
                }

                const formData = new URLSearchParams();
                formData.append('action', 'deactivate_user');
                formData.append('id', userId);

                fetch('../../app/api/usuarios_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(confirmDeactivateModal);
                    modal.hide(); // Esconde o modal primeiro

                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Após desativar, limpa o formulário e desabilita os campos
                        usernameSearchInput.value = '';
                        userIdInput.value = '';
                        nomeCompletoInput.value = '';
                        territorioSelect.value = '';
                        ativoCheckbox.checked = false;
                        primeiroAcessoCheckbox.checked = false;
                        setPermissionsOnForm('0'.repeat(9));
                        toggleFormFields(false);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao desativar usuário:', error);
                    showAlert('Erro de comunicação ao desativar usuário.', 'danger');
                    const modal = bootstrap.Modal.getInstance(confirmDeactivateModal);
                    modal.hide();
                });
            });

            // Listener para o botão "Ativar Usuário"
            btnAtivar.addEventListener('click', function() {
                const userId = userIdInput.value;
                if (!userId) {
                    showAlert('Nenhum usuário selecionado para ativação.', 'danger');
                    return;
                }

                const formData = new URLSearchParams();
                formData.append('action', 'activate_user');
                formData.append('id', userId);

                fetch('../../app/api/usuarios_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Após ativar, recarrega os detalhes para atualizar o estado dos botões, suprimindo o alerta de "Usuário encontrado!"
                        fetchUserDetails(usernameSearchInput.value.trim(), true); 
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao ativar usuário:', error);
                    showAlert('Erro de comunicação ao ativar usuário.', 'danger');
                });
            });
        });
    </script>
</body>
</html>
