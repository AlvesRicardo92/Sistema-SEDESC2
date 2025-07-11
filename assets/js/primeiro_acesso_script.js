$(document).ready(function() {
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


    // Função para validar a senha
    function validarSenha(senha) {
        let erros = [];

        // 1. Pelo menos 6 caracteres
        if (senha.length < 6) {
            erros.push("A senha deve ter pelo menos 6 caracteres.");
        }

        // 2. Pelo menos uma letra maiúscula
        if (!/[A-Z]/.test(senha)) {
            erros.push("A senha deve conter pelo menos uma letra maiúscula.");
        }

        // 3. Pelo menos 1 número
        if (!/[0-9]/.test(senha)) {
            erros.push("A senha deve conter pelo menos um número.");
        }

        return erros;
    }

    // Adiciona um listener para o evento de submit do formulário
    $('#firstAccessForm').submit(function(event) {
        // Previne o comportamento padrão do formulário (recarregar a página)
        event.preventDefault();

        // Esconde qualquer mensagem anterior
        hideMessage();

        const novaSenha = $('#newPassword').val();
        const confirmarSenha = $('#confirmPassword').val();

        if (novaSenha !== confirmarSenha) {
            showMessage("As senhas não coincidem.", 'danger');
            return;
        }

        const passwordValidationErrors = validarSenha(novaSenha);
        if (passwordValidationErrors.length > 0) {
            showMessage(passwordValidationErrors.join(' '), 'danger');
            return;
        }

        // Se todas as validações passarem, envia a requisição AJAX
        $.ajax({
            url: 'gerencias/processa_primeiro_acesso.php', // Script PHP para processar a nova senha
            method: 'POST',
            data: {
                novaSenha: novaSenha
            },
            dataType: 'json', // Espera uma resposta JSON
            success: function(resposta) {
                if (resposta && resposta.mensagem) {
                    if (resposta.dados && Object.keys(resposta.dados).length > 0) {
                        // Sucesso: exibe a mensagem de sucesso
                        showMessage(resposta.mensagem, 'success');
                        // Redireciona para o dashboard após definir a senha
                        setTimeout(function() {
                            window.location.href = 'dashboard.php';
                        }, 2000); // Redireciona após 2 segundos para o usuário ler a mensagem
                    } else {
                        // Falha: exibe a mensagem de erro
                        showMessage(resposta.mensagem, 'danger');
                    }
                } else {
                    // Caso a resposta não tenha a estrutura esperada
                    showMessage("Erro inesperado na resposta do servidor.", 'danger');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Lida com erros de rede ou servidor
                showMessage("Erro ao conectar com o servidor. Tente novamente.", 'danger');
            }
        });
    });
});
