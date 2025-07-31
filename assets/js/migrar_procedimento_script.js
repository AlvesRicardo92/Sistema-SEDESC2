$(document).ready(function() {
    const $mensagem = $('#mensagemFeedback');
    $mensagem.addClass('d-none').empty();

    $('#new_bairro_id').on('change', function(){
        let idBairro = $('#new_bairro_id').val();
        if(idBairro!=0){
            $.ajax({
                url: '../Bairro.php',
                method: 'POST', 
                data: { tipo:'territorioBairro',
                        idBairro:idBairro
                    },
                dataType: 'json',
                success: function(resposta) {
                    if(resposta.mensagem=="Sucesso"){
                        $('#new_bairro_territorio_nome').val(resposta.dados[0].nome)
                        $('#btnMigrar').data('territorio', resposta.dados[0].id);
                    }
                    else{
                        exibirMensagem(resposta.mensagem,"error");
                        $('#btnMigrar').data('territorio', '0');
                    }
                },
                error: function() {            
                    exibirMensagem("Erro de execução","error");
                    setTimeout(function() {
                        $mensagem.addClass('d-none').empty();
                    }, 3000);
                }
            });
        }
        else{
            $('#new_bairro_territorio_nome').val('');
        }
    });


    $('#buscarProcedimento').on('click', function(){
        if ($('#search_numero').val().length<1 || $('#search_ano').val().length<1){
            exibirMensagem("Erro no procedimento","error");
            setTimeout(function() {
                $mensagem.addClass('d-none').empty();
            }, 3000);
        }
        else{
            let numero = $('#search_numero').val();
            let ano = $('#search_ano').val();
            let territorio=$('#territorio_procedimento').val();
            $.ajax({
                url: '../Procedimento.php',
                method: 'POST', 
                data: { tipo:'porNumeroAnoEterritorio',
                        numero:numero,
                        ano:ano,
                        territorio:territorio
                      },
                dataType: 'json',
                success: function(resposta) {
                    if(resposta.mensagem=="Sucesso"){
                        if(resposta.dados[0].migrado==1){
                            exibirMensagem("Procedimento já migrado. Atualmente é o "+resposta.dados[0].numero_novo+"/"+resposta.dados[0].ano_novo+" do Território " + resposta.dados[0].territorio_novo,"error");
                            setTimeout(function() {
                                $mensagem.addClass('d-none').empty();
                            }, 15000);
                        }
                        else{
                            $('#nome_pessoa_principal').val(resposta.dados[0].nome);
                            $('#data_nascimento_pessoa_principal').val(resposta.dados[0].nascimento);
                            $('#btnMigrar').data('id', resposta.dados[0].id);
                            $.ajax({
                                url: '../Bairro.php',
                                method: 'POST', 
                                data: { tipo:'ativosExcetoTerritorios',
                                        territorio:territorio
                                    },
                                dataType: 'json',
                                success: function(resposta) {
                                    if(resposta.mensagem=="Sucesso"){
                                        selectBairros=$('#new_bairro_id');
                                        // Limpa as opções existentes (se houver)
                                        selectBairros.empty();

                                        // Adiciona uma opção padrão (opcional)
                                        selectBairros.append($('<option>', {
                                            value: 0,
                                            text: 'Selecione um Bairro'
                                        }));

                                        // Itera sobre os dados recebidos e adiciona as opções
                                        $.each(resposta.dados, function(index, bairro) {
                                            selectBairros.append($('<option>', {
                                                value: bairro.id,   // O valor da opção será o ID da cidade
                                                text: bairro.nome   // O texto visível será o nome da cidade
                                            }));
                                        });
                                        $('#btnMigrar').removeAttr('disabled');
                                        
                                    }
                                    else{
                                        exibirMensagem(resposta.mensagem,"error");
                                    }
                                },
                                error: function() {            
                                    exibirMensagem("Erro de execução","error");
                                    setTimeout(function() {
                                        $mensagem.addClass('d-none').empty();
                                    }, 3000);
                                }
                            });
                        }
                        
                    }
                    else{
                        exibirMensagem(resposta.mensagem,"error");
                        $('#btnMigrar').data('id','0');
                    }
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
    $('#btnMigrar').on('click', function(){
        if($('#btnMigrar').data('id')==0){
            exibirMensagem("Busque pelo procedimento","error");
        }
        else if($('#btnMigrar').data('territorio')==0){
            exibirMensagem("Selecione o novo bairro","error");
        }
        else if($('#motivo_migracao_id').val()==0){
            exibirMensagem("Selecione o motivo da migração","error");
        }
        else{
            let id = $('#btnMigrar').data('id');
            let territorio = $('#btnMigrar').data('territorio');
            let motivo = $('#motivo_migracao_id').val();
            $.ajax({
                url: '../Procedimento.php',
                method: 'POST', 
                data: { tipo:'migrar',
                        territorio:territorio,
                        id:id,
                        motivo:motivo
                      },
                dataType: 'json',
                success: function(resposta) {
                    if(resposta.mensagem=="Sucesso"){
                        exibirMensagem("Procedimento migrado com sucesso. Passa a ser o "+resposta.dados.novo_numero+"/"+resposta.dados.novo_ano+" do Território "+resposta.dados.novo_territorio,"success");          
                    }
                    else{
                        exibirMensagem(resposta.mensagem,"error");
                    }
                    setTimeout(function() {
                        $mensagem.addClass('d-none').empty();
                    }, 15000);
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