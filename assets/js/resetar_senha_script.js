$(document).ready(function() {
    const $mensagem = $('#mensagemFeedback');
    $mensagem.addClass('d-none').empty();
    let usuario ='';

    $('button.buscarUsuario').on('click', function(){
        console.log($('#username_to_reset').val().length);
        console.log($.isNumeric($("#username_to_reset").val()));
        if ($('#username_to_reset').val().length<3 || !$.isNumeric($("#username_to_reset").val())){
            exibirMensagem("Usuário inválido","error");
            setTimeout(function() {
                $mensagem.addClass('d-none').empty();
            }, 3000);
        }
        else{
            usuario =$('#username_to_reset').val();
            $.ajax({
                url: '../buscar_usuarios.php',
                method: 'POST', 
                data: { tipo:'usuario',
                        usuario:usuario},
                dataType: 'json',
                success: function(resposta) {
                    if(resposta.mensagem=="Sucesso"){ 
                        console.log(resposta.dados[0].ativo);
                        if(resposta.dados[0].ativo ==0){
                            exibirMensagem("Usuário desativado. Não é possível redefinir a senha","error");
                        }
                        else{
                            $('#nome_completo').val(resposta.dados[0].nome);
                            $('button.resetarSenha').removeAttr('disabled')
                        } 
                    }
                    else{
                        exibirMensagem(resposta.mensagem,"error");
                    }
                    setTimeout(function() {
                        $mensagem.addClass('d-none').empty();
                    }, 3000);
    
                },
                error: function() {            
                    exibirMensagem("Erro de execução","error");
                    setTimeout(function() {
                        $mensagem.addClass('d-none').empty();
                    }, 3000);
                }
            });
        }
    });
    $('button.resetarSenha').on('click', function(){
        if ($('#username_to_reset').val().length<3  || !$.isNumeric($("#username_to_reset").val())){
            exibirMensagem("Usuário inválido","error");
        }        
        else if($('#nome_completo').val().length<1){
            exibirMensagem("Primeiro busque pelo usuário","error");
        }
        else{
            usuario=$('#username_to_reset').val();
        }
        setTimeout(function() {
            $mensagem.addClass('d-none').empty();
        }, 3000);

        $.ajax({
            url: '../processa_resetar_senha.php',
            method: 'POST', 
            data: { usuario:usuario},
            dataType: 'json',
            success: function(resposta) {
                if(resposta.mensagem=="Sucesso"){
                    exibirMensagem("Senha redefinida para Pmsbc@123","success");
                    $('button.resetarSenha').attr('disabled',true);
                    $('#username_to_reset').val('');
                    $('#nome_completo').val('');
                }
                else{
                    exibirMensagem(resposta.mensagem,"error");
                }
                setTimeout(function() {
                    $mensagem.addClass('d-none').empty();
                    $('button.resetarSenha').removeAttr('disabled')
                }, 3000);

            },
            error: function() {            
                exibirMensagem("Erro de execução","error");
                setTimeout(function() {
                    $mensagem.addClass('d-none').empty();
                }, 3000);
            }
        });
    });
    // Função para exibir a mensagem
    function exibirMensagem(message, type) {
        // Limpa classes anteriores e oculta
        $mensagem.removeClass('d-none alert-success alert-danger alert-warning').empty();
        // Adiciona a classe de estilo e a mensagem
        $mensagem.text(message);
        if (type === 'success') {
            $mensagem.addClass('alert-success');
        } else if (type === 'error') {
            $mensagem.addClass('alert-danger');
        } else if (type === 'warning') {
            $mensagem.addClass('alert-warning');
        }

        // Exibe a div de mensagem
        $mensagem.removeClass('d-none').slideDown(); // slideDown para uma animação suave
    }
});