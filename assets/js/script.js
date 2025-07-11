$(document).ready(function() {
    // Adiciona um listener para o evento de submit do formulário
    $('#loginForm').submit(function(event) {
        // Previne o comportamento padrão do formulário (recarregar a página)
        event.preventDefault();

        // Obtém os valores dos campos de usuário e senha (opcional para o alerta)
        const usuario = $('#username').val();
        const senha = $('#password').val();

        // Referência para a área de mensagens
        const $messageArea = $('#messageArea');

        // Função para exibir mensagens
        function showMessage(message, type = 'danger') {
            $messageArea.removeClass('alert-success alert-danger alert-info alert-warning'); // Remove classes antigas
            $messageArea.addClass('alert-' + type); // Adiciona a classe de tipo (e.g., alert-danger)
            $messageArea.text(message); // Define o texto da mensagem
            $messageArea.fadeIn(); // Mostra a área de mensagem com um efeito de fade
        }

        // Função para esconder mensagens
        function hideMessage() {
            $messageArea.fadeOut(function() {
                $(this).text(''); // Limpa o texto após esconder
            });
        }

        // --- Adicionado para depuração: Log da URL completa da requisição AJAX ---
        const relativeUrl = 'gerencias/checarLogin.php';
        const baseUrl = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);
        const fullAjaxUrl = baseUrl + relativeUrl;
        console.log("Tentando chamar a URL AJAX:", fullAjaxUrl);
        // -----------------------------------------------------------------------
        $.ajax({
            url: 'gerencias/checarLogin.php',
            async:false,
            type: 'POST',
            data: {usuario:usuario,
                   senha:senha},
            dataType:'json',
            success: function (resultado) {
                // Verifica se a resposta contém uma mensagem
                if (resultado && resultado.mensagem) {
                    // Verifica se há dados para determinar sucesso ou falha
                    if (resultado.dados && Object.keys(resultado.dados).length > 0) {
                        if(resultado.dados.ativo==0){
                            showMessage("Usuário desativado. Verifique com o administrador do sistema", 'danger');    
                        }
                        else{
                            if(resultado.dados.primeiro_acesso==1){
                                window.location.href = 'primeiro_acesso.php';
                            }
                            else{
                                window.location.href = 'dashboard.php';
                            }
                        }
                    } else {
                        // Falha: exibe a mensagem de erro
                        showMessage(resultado.mensagem, 'danger');
                    }
                } else {
                    // Caso a resposta não tenha a estrutura esperada
                    showMessage("Erro inesperado na resposta do servidor.", 'danger');
                }
            },
            error: function(){
                // Lida com erros de rede ou servidor
                showMessage("Erro ao conectar com o servidor. Tente novamente.", 'danger');
            }
        });
    });
});