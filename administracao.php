<?php
// public/administracao.php
session_start();

// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: index.php');
    exit();
}

// Inclui o cabeçalho (que já define as variáveis de permissão)
include 'utils/cabecalho.php';

// Obtém a string de permissões novamente para as permissões do menu lateral
$permissoes = $_SESSION['usuario']['permissoes'] ?? '0000000000'; // Padrão com mais dígitos para novas permissões

// Define as permissões específicas para o menu de administração
// Supondo que as permissões para o submenu de administração comecem a partir do índice 6
$canAccessCriarUsuario = substr($permissoes, 7, 1);     // Ex: 7º caractere
$canAccessResetarSenha = substr($permissoes, 8, 1);    // Ex: 8º caractere
$canAccessAlterarPessoa = substr($permissoes, 9, 1);   // Ex: 9º caractere
$canAccessAlterarUsuario = substr($permissoes, 10, 1);   // Ex: 10º caractere
$canAccessAvisos = substr($permissoes, 10, 1);   // Ex: 10º caractere
$canAccessMigrarProcedimento = substr($permissoes, 10, 1);   // Ex: 10º caractere

// Adicione mais variáveis conforme necessário para outras opções do menu lateral
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração - Sistema de Procedimentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="administracao">
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header text-center mb-4">
                <h3>Menu de Administração</h3>
            </div>
            <ul class="list-group list-group-flush">
                <?php if ($canAccessCriarUsuario > 0): // Exemplo: permissão > 0 para Criar Usuário ?>
                <a href="gerencias/administrativo/criar_usuario.php" target="admin_iframe" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-plus me-2"></i> Criar Usuário
                </a>
                <?php endif; ?>

                <?php if ($canAccessResetarSenha > 0): // Exemplo: permissão > 0 para Resetar Senha ?>
                <a href="gerencias/administrativo/resetar_senha.php" target="admin_iframe" class="list-group-item list-group-item-action">
                    <i class="fas fa-key me-2"></i> Redefinir Senha
                </a>
                <?php endif; ?>

                <?php if ($canAccessAlterarPessoa > 0): // Exemplo: permissão > 0 para Alterar Pessoa ?>
                <a href="gerencias/administrativo/alterar_pessoa.php" target="admin_iframe" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-edit me-2"></i> Alterar Pessoa
                </a>
                <?php endif; ?>
                <?php if ($canAccessAlterarUsuario > 0): // Exemplo: permissão > 0 para Alterar Usuário ?>
                <a href="gerencias/administrativo/alterar_usuario.php" target="admin_iframe" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-edit me-2"></i> Alterar Usuário
                </a>
                <?php endif; ?>
                <?php if ($canAccessAvisos > 0): // Exemplo: permissão > 0 para Alterar Usuário ?>
                <a href="gerencias/administrativo/criar_avisos.php" target="admin_iframe" class="list-group-item list-group-item-action">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-square-fill" style="margin-right:8px" viewBox="0 0 16 16">
                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg> Avisos
                </a>
                <?php endif; ?>
                <?php if ($canAccessMigrarProcedimento > 0): // Exemplo: permissão > 0 para Alterar Usuário ?>
                <a href="gerencias/administrativo/migracao_procedimento.php" target="admin_iframe" class="list-group-item list-group-item-action">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5m14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5"/>
                    </svg> Migrar Procedimento
                </a>
                <?php endif; ?>

                <!-- Adicione mais itens de menu aqui com suas respectivas condições de permissão -->
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- O conteúdo da subpágina será carregado aqui -->
            <iframe name="admin_iframe" id="adminIframe" src="gerencias/administrativo/bem_vindo.php"></iframe>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Adiciona classe 'active' ao item do menu lateral clicado
            const sidebarLinks = document.querySelectorAll('#sidebar .list-group-item');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    sidebarLinks.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Opcional: define o item ativo no carregamento inicial se a URL do iframe for conhecida
            const adminIframe = document.getElementById('adminIframe');
            adminIframe.addEventListener('load', function() {
                const currentIframeSrc = adminIframe.contentWindow.location.pathname.split('/').pop();
                sidebarLinks.forEach(link => {
                    const linkHref = link.getAttribute('href').split('/').pop();
                    if (linkHref === currentIframeSrc) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            });
        });
    </script>
</body>
</html>
