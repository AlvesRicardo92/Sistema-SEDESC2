<?php
// public/administracao/alterar_usuario.php
session_start();

if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../Territorio.php';

$classeTerritorio = new Territorio($mysqli);

$respostaJSON=$classeTerritorio->buscarTodosAtivos();

$jsonDecodificado=json_decode($respostaJSON,true);
if($jsonDecodificado['mensagem']=="Sucesso"){
    $dados=$jsonDecodificado['dados'];
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

        <div class="alert d-none mensagemFeedback" role="alert"></div>


        <div class="mb-3 d-flex">
            <div class="flex-grow-1 me-2">
                <label for="username_search" class="form-label">Nome de Usuário</label>
                <input type="text" class="alterarUsuario form-control" id="username_search" name="username_search" required>
            </div>
            <button type="button" class="btn btn-primary align-self-end mb-3" id="btnBuscarUsuario" style="margin-top:30px"><i class="fas fa-search"></i> Buscar</button>
        </div>

        <div class="mb-3">
            <label for="nomeCompleto" class="form-label">Nome Completo</label>
            <input type="text" class="alterarUsuario form-control" id="nomeCompleto" name="fullName" disabled>
        </div>

        <div class="mb-3">
            <label for="territorio_id" class="form-label">Território</label>
            <select class="form-select" id="territorio_id" name="territorio_id" disabled>
                <option value="0" selected>Selecione um Território</option>
                <?php 
                    if(!empty($dados)){
                        foreach($dados as $territorio){
                            echo "<option value=".$territorio['id'].">".$territorio['nome']."</option>";
                        }
                    }
                ?>
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
                        <input class="form-check-input perm-radio" type="radio" name="perm_0" id="perm_0_none" value="0" checked disabled>
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
                        <input class="form-check-input perm-radio" type="radio" name="perm_1" id="perm_1_none" value="0" checked disabled>
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
                        <input class="form-check-input perm-radio" type="radio" name="perm_2" id="perm_2_none" value="0" checked disabled>
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
                        <input class="form-check-input perm-radio" type="radio" name="perm_3" id="perm_3_none" value="0" checked disabled>
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
                        <input class="form-check-input perm-radio" type="radio" name="perm_4" id="perm_4_none" value="0" checked disabled>
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
            <div class="criarUsuario admin-checkbox-grid mt-3">
                <div class="form-check">
                    <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_9_alterar_usuario" data-position="9" value="1" disabled>
                    <label class="form-check-label" for="perm_9_alterar_usuario">Alterar Usuário</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_10_avisos" data-position="10" value="1" disabled>
                    <label class="form-check-label" for="perm_10_avisos">Avisos</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input admin-perm-checkbox" type="checkbox" id="perm_11_migrar_procedimento" data-position="11" value="1" disabled>
                    <label class="form-check-label" for="perm_11_migrar_procedimento">Migrar Procedimento</label>
                </div>
            </div>
        </div>
        <div class="alert d-none mensagemFeedback" role="alert"></div>
        <button class="btn btn-primary mt-3" id="btnSalvar" data-id="" disabled><i class="fas fa-save"></i> Salvar Alterações</button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../assets/js/alterar_usuario_script.js"></script>
</body>
</html>
