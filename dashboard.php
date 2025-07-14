<?php
    require __DIR__ . "/gerencias/conexaoBanco.php";
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Link para o CSS externo -->
</head>
<body>
    <?php
        require __DIR__ . '/utils/cabecalho.php';

        $contador=0;
        $stmt = $mysqli->prepare("SELECT descricao, nome_imagem FROM avisos WHERE id_territorio_exibicao=? AND CURDATE() BETWEEN data_inicio_exibicao AND data_fim_exibicao");
        $stmt->bind_param('i', $_SESSION['usuario']['territorio']);

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $linhas = $resultado ->num_rows;
    ?>
    <!-- Conteúdo Principal - Carrossel de Avisos -->
    <div class="main-content-wrapper">
        <div class="carousel-container">
            <?php
            if($linhas == 0){
                echo "<div class='alert alert-info text-center alert-custom' role='alert'>";
                echo "Nenhum aviso disponível no momento.";
                echo "</div>";
            }
            else{
                echo "<div id='carouselExampleIndicators' class='carousel slide' data-bs-ride='carousel'>";
                echo "<div class='carousel-indicators'>";
                
                $resultado->data_seek(0);

                while($row = $resultado->fetch_assoc()) {
                    echo '<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="' . $contador . '" ' . ($contador == 0 ? 'class="active" ' : '') . 'aria-current="true" aria-label="Slide ' . ($contador + 1) . '"></button>';
                    $contador+=1;
                }
                $contador=0;
                echo "</div>";

                echo "<div class='carousel-inner'>";
                
                $resultado->data_seek(0);

                while($row = $resultado->fetch_assoc()) {
                        echo "<div class='carousel-item " . ($contador == 0 ? "active" : "") . "'>";
                        echo '<img src="assets/imagens/' . (empty($row['nome_imagem']) ? 'template.png' : $row['nome_imagem']) . '" class="d-block w-100" alt="Aviso ' . ($contador + 1) . '">';
                            echo "<div class='carousel-caption d-none d-md-block'>";
                                echo '<span class="texto-carrossel">' . $row['descricao'] . '</span>';
                            echo "</div>";
                        echo "</div>";
                        $contador+=1;
                }
                echo "</div>";

                echo "<button class='carousel-control-prev' type='button' data-bs-target='#carouselExampleIndicators' data-bs-slide='prev'>";
                    echo "<span class='carousel-control-prev-icon' aria-hidden='true'></span>";
                    echo "<span class='visually-hidden'>Anterior</span>";
                echo "</button>";
                echo "<button class='carousel-control-next' type='button' data-bs-target='#carouselExampleIndicators' data-bs-slide='next'>";
                    echo "<span class='carousel-control-next-icon' aria-hidden='true'></span>";
                    echo "<span class='visually-hidden'>Próximo</span>";
                echo "</button>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    <?php
        }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
