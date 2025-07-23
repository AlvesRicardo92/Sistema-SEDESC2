$(document).ready(function() {
    $('button.criarUsuario').on('click', function(){
        var usuario;
        var nome;
        var territorio;
        var permissoes;
        var permissoesAdm='';

        usuario=$('#nomeUsuario').val();
        nome=$('#nomeCompleto').val();
        territorio=$('#territorio_id').val();
        permissoes=$('[name=perm_0]:checked').val();
        permissoes+=$('[name=perm_1]:checked').val();
        permissoes+=$('[name=perm_2]:checked').val();
        permissoes+=$('[name=perm_3]:checked').val();
        permissoes+=$('[name=perm_4]:checked').val();
        $('input[type=checkbox]').each(function(){
            if ($(this).is(':checked')) {
                permissoesAdm+='1';
            }
            else{
                permissoesAdm+='0';
            }
        });

        const $mensagem = $('#mensagemFeedback');
        $mensagem.addClass('d-none').empty();

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

        $.ajax({
            url: '../processa_criar_usuario.php',
            method: 'POST', 
            data: { usuario:usuario,
                    nome:nome,
                    permissoes:permissoes,
                    territorio:territorio,
                    permissoesAdm:permissoesAdm
                },
            dataType: 'json',
            success: function(resposta) {
                if(resposta.mensagem=="Sucesso"){
                    exibirMensagem("Usuário cadastrado com sucesso","success");
                    $('button.criarUsuario').attr('disabled',true)
                    $('#nomeUsuario').val('');
                    $('#nomeCompleto').val('');
                    $('#territorio_id').val(0);
                    $('input[name="perm_0"][value="0"]').prop('checked', true);
                    $('input[name="perm_1"][value="0"]').prop('checked', true);
                    $('input[name="perm_2"][value="0"]').prop('checked', true);
                    $('input[name="perm_3"][value="0"]').prop('checked', true);
                    $('input[name="perm_4"][value="0"]').prop('checked', true);
                    $('#perm_6_criar_usuario').prop('checked', false);
                    $('#perm_7_resetar_senha').prop('checked', false);
                    $('#perm_8_alterar_pessoa').prop('checked', false);
                }
                else{
                    exibirMensagem(resposta.mensagem,"error");
                }
                setTimeout(function() {
                    $mensagem.addClass('d-none').empty();
                    $('button.criarUsuario').removeAttr('disabled')
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
});