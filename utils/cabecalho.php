<?php
// Redireciona para a página de login se o usuário não estiver logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: index.php');
    exit();
}

$nomeCompleto = $_SESSION['usuario']['nome'] ?? 'Sem Nome';
$permissoes = $_SESSION['usuario']['permissoes'] ?? '00000000000';

?>
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

                <?php
                    switch (true) {
                        case (substr($permissoes, 0, 1) == "A"):                    
                            if (substr($permissoes, 1, 1) == 1){
                            }
                    
                        case (substr($permissoes, 0, 1) == "B"):
                            if (substr($permissoes, 1, 1) == 1){
                            }
                        case (substr($permissoes, 0, 1) == "C"):
                            if (substr($permissoes, 1, 1) == 1){
                            }
                        case (substr($permissoes, 0, 1) == "D"):
                            if (substr($permissoes, 1, 1) == 1){
                            }
                        case (substr($permissoes, 0, 1) == "E"):
                            if (substr($permissoes, 1, 1) == 1){
                                echo '<li class="nav-item">';
                                    echo '<a class="nav-link" href="procedimentos.php">Procedimento</a>';
                                echo '</li>';
                            }
                        default:
                    }
                ?>
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