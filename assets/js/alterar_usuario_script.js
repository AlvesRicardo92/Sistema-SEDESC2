$(document).ready(function() {
    const $mensagem = $('.mensagemFeedback');
    $mensagem.addClass('d-none').empty();
    $('button#btnBuscarUsuario').on('click', function(){
        if ($('#username_search').val()=="" || $('#username_search').val().lenght<3){
            exibirMensagem('Erro no usuário',"error");
        }
        else{
            let usuario=$('#username_search').val();
            $.ajax({
                url: '../Usuario.php',
                method: 'POST', 
                data: { tipo:'porUsuario',
                        usuario:usuario
                      },
                dataType: 'json',
                success: function(resposta) {
                    if(resposta.mensagem=="Sucesso"){
                        $('#nomeCompleto').val(resposta.dados[0].nome);
                        $('#territorio_id').val(resposta.dados[0].territorio_id);
                        if(resposta.dados[0].ativo==1){
                            $('#ativo').prop('checked',true);
                        }
                        else if(resposta.dados[0].ativo==0){
                            $('#ativo').prop('checked',false);
                        }
                        if(resposta.dados[0].primeiro_acesso==1){
                            $('#primeiro_acesso').prop('checked',true);
                        }
                        else if(resposta.dados[0].primeiro_acesso==0){
                            $('#primeiro_acesso').prop('checked',false);
                        }
                        let arrayPermissoes = Array.from(resposta.dados[0].permissoes);
                        $('input[name="perm_0"][value="' + arrayPermissoes[0] + '"]').prop('checked',true);
                        $('input[name="perm_1"][value="' + arrayPermissoes[1] + '"]').prop('checked',true);
                        $('input[name="perm_2"][value="' + arrayPermissoes[2] + '"]').prop('checked',true);
                        $('input[name="perm_3"][value="' + arrayPermissoes[3] + '"]').prop('checked',true);
                        $('input[name="perm_4"][value="' + arrayPermissoes[4] + '"]').prop('checked',true);
                        if(arrayPermissoes[6]==1){
                            $('#perm_6_criar_usuario').prop('checked',true);
                        }
                        else{
                            $('#perm_6_criar_usuario').prop('checked',false);
                        }
                        if(arrayPermissoes[7]==1){
                            $('#perm_7_resetar_senha').prop('checked',true);
                        }
                        else{
                            $('#perm_7_resetar_senha').prop('checked',false);
                        }
                        if(arrayPermissoes[8]==1){
                            $('#perm_8_alterar_pessoa').prop('checked',true);
                        }
                        else{
                            $('#perm_8_alterar_pessoa').prop('checked',false);
                        }
                        $('#nomeCompleto').removeAttr('disabled');
                        $('#territorio_id').removeAttr('disabled');
                        $('#ativo').removeAttr('disabled');
                        $('#primeiro_acesso').removeAttr('disabled');
                        $('input[name="perm_0"]').removeAttr('disabled');
                        $('input[name="perm_1"]').removeAttr('disabled');
                        $('input[name="perm_2"]').removeAttr('disabled');
                        $('input[name="perm_3"]').removeAttr('disabled');
                        $('input[name="perm_4"]').removeAttr('disabled');
                        $('#perm_6_criar_usuario').removeAttr('disabled');
                        $('#perm_7_resetar_senha').removeAttr('disabled');
                        $('#perm_8_alterar_pessoa').removeAttr('disabled');
                        $('#btnSalvar').removeAttr('disabled');
                        $('#btnSalvar').data('id', resposta.dados[0].id);


                    }
                    else{
                        exibirMensagem(resposta.mensagem,"error");
                        $('#nomeCompleto').attr('disabled',true);
                        $('#territorio_id').attr('disabled',true);
                        $('#ativo').attr('disabled',true);
                        $('#primeiro_acesso').attr('disabled',true);
                        $('input[name="perm_0"]').attr('disabled',true);
                        $('input[name="perm_1"]').attr('disabled',true);
                        $('input[name="perm_2"]').attr('disabled',true);
                        $('input[name="perm_3"]').attr('disabled',true);
                        $('input[name="perm_4"]').attr('disabled',true);
                        $('#perm_6_criar_usuario').attr('disabled',true);
                        $('#perm_7_resetar_senha').attr('disabled',true);
                        $('#perm_8_alterar_pessoa').attr('disabled',true);
                        $('#btnSalvar').attr('disabled',true);
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
        }
    });

    $('button#btnSalvar').on('click', function(){
        let nome;
        let territorio;
        let ativo;
        let primeiro_acesso;
        let permissoes;
        let permissoesAdm='';
        let criar_usuario;
        let resetar_senha;
        let alterar_pessoa;

        nome=$('#nomeCompleto').val();
        territorio=$('#territorio_id').val();
        permissoes=$('[name=perm_0]:checked').val();
        permissoes+=$('[name=perm_1]:checked').val();
        permissoes+=$('[name=perm_2]:checked').val();
        permissoes+=$('[name=perm_3]:checked').val();
        permissoes+=$('[name=perm_4]:checked').val();
        
        if($('#ativo').is(':checked')) {
            ativo=1;
        }
        else{
            ativo=0;
        }
        if($('#primeiro_acesso').is(':checked')) {
            primeiro_acesso=1;
        }
        else{
            primeiro_acesso=0;
        }

        if($('#perm_6_criar_usuario').is(':checked')) {
            permissoesAdm+='1';
        }
        else{
            permissoesAdm+='0';
        }

        if($('#perm_7_resetar_senha').is(':checked')) {
            permissoesAdm+='1';
        }
        else{
            permissoesAdm+='0';
        }

        if($('#perm_8_alterar_pessoa').is(':checked')) {
            permissoesAdm+='1';
        }
        else{
            permissoesAdm+='0';
        }

        let id = $('#btnSalvar').data('id');
        $.ajax({
            url: '../Usuario.php',
            method: 'POST', 
            data: { tipo:'updateDados',
                    id:id,
                    nome:nome,
                    permissoes:permissoes,
                    territorio:territorio,
                    permissoesAdm:permissoesAdm,
                    ativo:ativo,
                    primeiro_acesso:primeiro_acesso,
                    permissoesAdm:permissoesAdm
                },
            dataType: 'json',
            success: function(resposta) {
                if(resposta.mensagem=="Sucesso"){
                    exibirMensagem("Usuário atualizado com sucesso","success");
                    $('#nomeUsuario').val('');
                    $('#nomeCompleto').val('');
                    $('#territorio_id').val(0);
                    $('#ativo').prop('checked', true);
                    $('#primeiro_acesso').prop('checked', true);
                    $('input[name="perm_0"][value="0"]').prop('checked', true);
                    $('input[name="perm_1"][value="0"]').prop('checked', true);
                    $('input[name="perm_2"][value="0"]').prop('checked', true);
                    $('input[name="perm_3"][value="0"]').prop('checked', true);
                    $('input[name="perm_4"][value="0"]').prop('checked', true);
                    $('#perm_6_criar_usuario').prop('checked', false);
                    $('#perm_7_resetar_senha').prop('checked', false);
                    $('#perm_8_alterar_pessoa').prop('checked', false);

                    $('#nomeCompleto').attr('disabled',true);
                    $('#territorio_id').attr('disabled',true);
                    $('#ativo').attr('disabled',true);
                    $('#primeiro_acesso').attr('disabled',true);
                    $('input[name="perm_0"]').attr('disabled',true);
                    $('input[name="perm_1"]').attr('disabled',true);
                    $('input[name="perm_2"]').attr('disabled',true);
                    $('input[name="perm_3"]').attr('disabled',true);
                    $('input[name="perm_4"]').attr('disabled',true);
                    $('#perm_6_criar_usuario').attr('disabled',true);
                    $('#perm_7_resetar_senha').attr('disabled',true);
                    $('#perm_8_alterar_pessoa').attr('disabled',true);
                    $('#btnSalvar').attr('disabled',true);
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