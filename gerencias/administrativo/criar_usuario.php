<?php
// public/administracao/criar_usuario.php
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
    <title>Criar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="p-3 criarUsuario">
    <div class="criarUsuario container">
        <h4>Formulário de Criação de Usuário</h4>
        <p>Preencha os dados do novo usuário e defina suas permissões.</p>

        <div id="alertMessage" class="alert d-none criarUsuario" role="alert"></div>

        <form id="create-user-form">
            <div class="mb-3">
                <label for="nomeUsuario" class="form-label">Nome de Usuário</label>
                <input type="text" class="form-control" id="nomeUsuario" name="username" required>
            </div>
            <div class="mb-3">
                <label for="nomeCompleto" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nomeCompleto" name="fullName">
            </div>
            <!-- NOVO CAMPO: Seleção de Território -->
            <div class="mb-3">
                <label for="territorio_id" class="form-label">Território</label>
                <select class="form-select" id="territorio_id" name="territorio_id" required>
                    <option value="">Selecione um Território</option>
                    <?php foreach ($territorios as $territorio): ?>
                        <option value="<?= htmlspecialchars($territorio->getId()) ?>"><?= htmlspecialchars($territorio->getNome()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- A senha inicial será definida automaticamente como Pmsbc@123 -->
            <p class="text-muted">A senha inicial será definida como: <strong>Pmsbc@123</strong> (o usuário será obrigado a alterá-la no primeiro acesso).</p>

            <!-- Seção de Permissões -->
            <h5 class="criarUsuario">Permissões:</h5>
            <div class="criarUsuario permission-section">
                <h5>Telas</h5>
                <div class="criarUsuario permission-row">
                    <div class="criarUsuario permission-label">Procedimentos</div>
                    <div class="criarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_none" value="0" checked>
                            <label class="form-check-label" for="perm_0_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_view" value="1">
                            <label class="form-check-label" for="perm_0_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_edit" value="2">
                            <label class="form-check-label" for="perm_0_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_delete" value="3">
                            <label class="form-check-label" for="perm_0_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_create" value="4">
                            <label class="form-check-label" for="perm_0_create">Criar</label>
                        </div>
                    </div>
                </div>
                <hr class="criarUsuario permission-divider">

                <div class="criarUsuario permission-row">
                    <div class="criarUsuario permission-label">Ofício Recebido</div>
                    <div class="criarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_none" value="0" checked>
                            <label class="form-check-label" for="perm_1_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_view" value="1">
                            <label class="form-check-label" for="perm_1_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_edit" value="2">
                            <label class="form-check-label" for="perm_1_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_delete" value="3">
                            <label class="form-check-label" for="perm_1_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_create" value="4">
                            <label class="form-check-label" for="perm_1_create">Criar</label>
                        </div>
                    </div>
                </div>
                <hr class="criarUsuario permission-divider">

                <div class="criarUsuario permission-row">
                    <div class="criarUsuario permission-label">Ofício Expedido</div>
                    <div class="criarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_none" value="0" checked>
                            <label class="form-check-label" for="perm_2_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_view" value="1">
                            <label class="form-check-label" for="perm_2_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_edit" value="2">
                            <label class="form-check-label" for="perm_2_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_delete" value="3">
                            <label class="form-check-label" for="perm_2_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_create" value="4">
                            <label class="form-check-label" for="perm_2_create">Criar</label>
                        </div>
                    </div>
                </div>
                <hr class="criarUsuario permission-divider">

                <div class="criarUsuario permission-row">
                    <div class="criarUsuario permission-label">Denúncia</div>
                    <div class="criarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_none" value="0" checked>
                            <label class="form-check-label" for="perm_3_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_view" value="1">
                            <label class="form-check-label" for="perm_3_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_edit" value="2">
                            <label class="form-check-label" for="perm_3_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_delete" value="3">
                            <label class="form-check-label" for="perm_3_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_create" value="4">
                            <label class="form-check-label" for="perm_3_create">Criar</label>
                        </div>
                    </div>
                </div>
                <hr class="criarUsuario permission-divider">

                <div class="criarUsuario permission-row">
                    <div class="criarUsuario permission-label">Atendimento Presencial</div>
                    <div class="criarUsuario permission-options">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_none" value="0" checked>
                            <label class="form-check-label" for="perm_4_none">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_view" value="1">
                            <label class="form-check-label" for="perm_4_view">Visualizar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_edit" value="2">
                            <label class="form-check-label" for="perm_4_edit">Editar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_delete" value="3">
                            <label class="form-check-label" for="perm_4_delete">Excluir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_create" value="4">
                            <label class="form-check-label" for="perm_4_create">Criar</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="criarUsuario permission-section">
                <h5 class="alterarUsuario">Administração</h5>
                <p class="text-muted">Acesso ao menu "Administração" no cabeçalho será concedido se qualquer uma das opções abaixo for marcada.</p>
                <div class="criarUsuario admin-checkbox-grid">
                    <div class="form-check">
                        <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_6_criar_usuario" data-position="6" value="1">
                        <label class="form-check-label" for="perm_6_criar_usuario">Criar Usuário</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_7_resetar_senha" data-position="7" value="1">
                        <label class="form-check-label" for="perm_7_resetar_senha">Resetar Senha</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_8_alterar_pessoa" data-position="8" value="1">
                        <label class="form-check-label" for="perm_8_alterar_pessoa">Alterar Pessoa</label>
                    </div>
                </div>
            </div>

            <input type="hidden" id="permissoesUsuario" name="permissoes">

            <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Usuário</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('create-user-form');
            const permissoesInput = document.getElementById('permissoesUsuario');
            const alertMessage = document.getElementById('alertMessage');
            const territorioSelect = document.getElementById('territorio_id'); // Novo elemento

            // Inicializa a string de permissões com '0's.
            // A string terá 9 caracteres (índices 0 a 8).
            let currentPermissoes = '0'.repeat(9); 

            // Função para exibir mensagens de alerta
            function showAlert(message, type) {
                alertMessage.textContent = message;
                alertMessage.className = `alert alert-${type}`;
                alertMessage.classList.remove('d-none');
                setTimeout(() => {
                    alertMessage.classList.add('d-none');
                }, 5000); // Esconde a mensagem após 5 segundos
            }

            // Função para atualizar a string de permissões
            function updatePermissoesString() {
                let tempPermissoes = currentPermissoes.split(''); // Converte para array para fácil manipulação

                // Posições 0 a 4 (Procedimentos, Ofício Recebido, Ofício Expedido, Denúncia, Atendimento Presencial)
                for (let i = 0; i <= 4; i++) { 
                    const selectedRadio = document.querySelector(`input[name="perm_${i}"]:checked`);
                    if (selectedRadio) {
                        tempPermissoes[i] = selectedRadio.value;
                    }
                }

                // Posição 5: Acesso geral ao menu "Administração"
                // Se qualquer sub-permissão de administração (posições 6, 7, 8) for marcada, a posição 5 será '1'.
                // Caso contrário, será '0'.
                const adminCheckboxes = document.querySelectorAll('.admin-perm-checkbox');
                let adminAccessGranted = false;
                adminCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        adminAccessGranted = true;
                    }
                });
                tempPermissoes[5] = adminAccessGranted ? '1' : '0';


                // Posições 6, 7, 8 (Criar Usuário, Resetar Senha, Alterar Pessoa)
                // Ajustado para as posições corretas (6, 7, 8)
                // Não é necessário redefinir data-position aqui, pois já está no HTML
                // document.getElementById('perm_6_criar_usuario').dataset.position = '6';
                // document.getElementById('perm_7_resetar_senha').dataset.position = '7';
                // document.getElementById('perm_8_alterar_pessoa').dataset.position = '8';


                adminCheckboxes.forEach(checkbox => {
                    const position = parseInt(checkbox.dataset.position);
                    // Garante que a string tempPermissoes tem o tamanho necessário
                    while (tempPermissoes.length <= position) {
                        tempPermissoes.push('0');
                    }
                    if (checkbox.checked) {
                        tempPermissoes[position] = '1';
                    } else {
                        tempPermissoes[position] = '0';
                    }
                });

                currentPermissoes = tempPermissoes.join(''); // Converte de volta para string
                permissoesInput.value = currentPermissoes; // Atualiza o campo hidden
                console.log("Permissões geradas:", currentPermissoes); // Para depuração
            }

            // Adiciona listeners para os radio buttons
            document.querySelectorAll('.perm-radio').forEach(radio => {
                radio.addEventListener('change', updatePermissoesString);
            });

            // Adiciona listeners para os checkboxes de administração
            document.querySelectorAll('.admin-perm-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updatePermissoesString);
            });

            // Atualiza a string de permissões no carregamento inicial
            updatePermissoesString();

            // Lidar com o envio do formulário
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Impede o envio padrão do formulário

                // Garante que a string de permissões está atualizada antes de enviar
                updatePermissoesString(); 
                
                const username = document.getElementById('nomeUsuario').value;
                const fullName = document.getElementById('nomeCompleto').value;
                const territorioId = territorioSelect.value; // Obtém o ID do território selecionado
                const permissions = permissoesInput.value; // Pega o valor do campo hidden

                // Validação para território
                if (!territorioId) {
                    showAlert('Por favor, selecione um território para o usuário.', 'danger');
                    return;
                }

                // A senha padrão será "Pmsbc@123" e será hasheada no backend
                const defaultPassword = "Pmsbc@123"; 

                // Requisição AJAX para o backend para criar o usuário
                fetch('../../app/api/usuarios_api.php', { // Novo endpoint para usuários
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded', // Usar urlencoded para FormData simples
                    },
                    body: new URLSearchParams({
                        action: 'create_user',
                        username: username,
                        password: defaultPassword, // Envia a senha padrão
                        fullName: fullName,
                        territorio_id: territorioId, // Envia o ID do território
                        permissions: permissions
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Usuário criado com sucesso! A senha inicial é Pmsbc@123.', 'success');
                        form.reset(); // Limpa o formulário
                        updatePermissoesString(); // Reseta as permissões para o estado inicial
                    } else {
                        showAlert('Erro ao criar usuário: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                    showAlert('Erro de comunicação com o servidor.', 'danger');
                });
            });
        });
    </script>
</body>
</html>
