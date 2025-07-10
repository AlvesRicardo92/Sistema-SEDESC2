<?php
// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$nomeCompleto = $_SESSION['nome_completo'] ?? $_SESSION['username'] ?? 'Usuário';
$permissoes = $_SESSION['permissoes'] ?? '0000000'; // Define um padrão seguro caso não haja permissões

// Função auxiliar para verificar permissão
function hasPermission(string $permissoes, int $position): string {
    // Garante que a string de permissões tem o comprimento necessário
    if (isset($permissoes[$position])) {
        return $permissoes[$position];
    }
    return false;
}

$canAccessProcedimentos = (int)hasPermission($permissoes, 0); // Posição 0 para Procedimentos
$canAccessOficioRecebido = (int)hasPermission($permissoes, 1);    // Posição 1 para Ofício Recebido
$canAccessOficioExpedido = (int)hasPermission($permissoes, 2); // Posição 2 para Ofício Expedido
$canAccessDenuncia = (int)hasPermission($permissoes, 3); // Posição 3 para Denúncia
$canAccessAtendimentoPresencial = (int)hasPermission($permissoes, 4); // Posição 4 para Atendimento Presencial
$canAccessAdministracao = (int)hasPermission($permissoes, 5); // Posição 6 para Administração

?>
<style>
    .tituloCabecalho{
        margin-right:20px;
        padding-right:20px;
        border-right: 3px solid #fff;
    }
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <span class="navbar-brand tituloCabecalho" >SEDESC</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="dashboard.php">Início</a>
                    </li>

                    <?php if ($canAccessProcedimentos>0): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="procedimentos.php">Procedimento</a>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessOficioRecebido>0): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#oficioRecebido">Ofício Recebido</a>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessOficioExpedido>0): ?>
                        <li class="nav-item">
                        <a class="nav-link" href="#oficioExpedido">Ofício Expedido</a>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessDenuncia>0): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#denuncia">Denúncia</a>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessAtendimentoPresencial>0): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#atendimentoPresencial">Atendimento Presencial</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($canAccessAdministracao>0): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="administracao.php">Administração</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Olá, <?= htmlspecialchars($nomeCompleto) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                            <li><a class="dropdown-item" href="perfil.php">Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>