$(document).ready(function() {
    const $mensagem = $('#mensagemFeedback');
    $mensagem.addClass('d-none').empty();

    $('#pessoas').on('change', function(){
        $('#nome_pessoa').attr('disabled',true);
        $('#data_nascimento').attr('disabled',true);
        $('#sexo_id').attr('disabled',true);
        $('#inativo').attr('disabled',true);
        $('#btnSalvar').attr('disabled',true);

        let id = $(this).val();
        $.ajax({
            url: '../Pessoa.php',
            method: 'POST', 
            data: { id:id,
                    tipo:'porId'},
            dataType: 'json',
            success: function(resposta) {
                if(resposta.mensagem=="Sucesso"){ 
                    $('#nome_pessoa').val(resposta.dados[0].nome);
                    $('#data_nascimento').val(resposta.dados[0].data_nascimento);
                    $('#sexo_id').val(resposta.dados[0].id_sexo);
                    if(resposta.dados[0].ativo==1){
                        $('#inativo').prop('checked', false);
                    }
                    else{
                        $('#inativo').prop('checked', true);
                    }
                    $('#nome_pessoa').removeAttr('disabled');
                    $('#data_nascimento').removeAttr('disabled');
                    $('#sexo_id').removeAttr('disabled');
                    $('#inativo').removeAttr('disabled');
                    $('#btnSalvar').removeAttr('disabled');
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
    });


    $('button#btnSalvar').on('click', function(){
        let id = $('#pessoas').val();
        if ($('#nome_pessoa').val().length<3 ){
            exibirMensagem("Nome inválido","error");
            setTimeout(function() {
                $mensagem.addClass('d-none').empty();
            }, 3000);
        }
        else if ($('#data_nascimento').val().lenght<10){
            exibirMensagem("Data de nascimento inválida","error");
            setTimeout(function() {
                $mensagem.addClass('d-none').empty();
            }, 3000);
        }
        else if ($('#sexo_id').val()==0){
            exibirMensagem("Sexo inválido","error");
            setTimeout(function() {
                $mensagem.addClass('d-none').empty();
            }, 3000);
        }
        else{
            let nome=$('#nome_pessoa').val();
            let data_nascimento=$('#data_nascimento').val();
            let id_sexo=$('#sexo_id').val();
            let ativo= ($('#inativo').is(":checked")?0:1);
            $.ajax({
                url: '../Pessoa.php',
                method: 'POST', 
                data: { tipo: 'updateDados',
                        nome:nome,
                        data_nascimento:data_nascimento,
                        id_sexo:id_sexo,
                        id:id,
                        ativo:ativo},
                dataType: 'json',
                success: function(resposta) {
                    if(resposta.mensagem=="Sucesso"){ 
                        exibirMensagem('Pessoa atualizada com sucesso',"success");
                        $('#nome_pessoa').attr('disabled',true);
                        $('#nome_pessoa').val('');
                        $('#data_nascimento').attr('disabled',true);
                        $('#data_nascimento').val('');
                        $('#sexo_id').attr('disabled',true);
                        $('#sexo_id').val(0);
                        $('#inativo').attr('disabled',true);
                        $('#inativo').prop('checked', false);
                        $('#btnSalvar').attr('disabled',true);

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