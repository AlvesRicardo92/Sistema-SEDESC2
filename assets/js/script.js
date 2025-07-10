$(document).ready(function() {
    // Adiciona um listener para o evento de submit do formulário
    $('#loginForm').submit(function(event) {
        // Previne o comportamento padrão do formulário (recarregar a página)
        event.preventDefault();

        // Obtém os valores dos campos de usuário e senha (opcional para o alerta)
        const username = $('#username').val();
        const password = $('#password').val();

        // Exibe um alerta simples
        $.ajax({
            url: 'trazInfoSI.php',
            async:false,
            type: 'POST',
            data: {idSI:siNumeroAno[0],
                   anoSI:siNumeroAno[1]},
            dataType:'text',
            done: function () {
                alert("feito");
            },
            success: function (resultado) {
                //console.log("resultado " + resultado);
                var retorno = resultado.split('|');
                //console.log("retorno " + retorno);
                if (Array.isArray(retorno)){
                    if(retorno[0]>0){
                        $('#siNumero').val(retorno[0]);
                        $('#siData').val(retorno[1].substring(0,10));
                        $('#resp01 option:contains("'+$.trim(retorno[3])+'")').prop('selected', true);
                        $('#resp02 option:contains("'+$.trim(retorno[4])+'")').prop('selected', true);
                        $('#destino option:contains("'+$.trim(retorno[5])+'")').prop('selected', true);
                        $('#solicitante').val(retorno[6]);
                        $('#assunto').val(retorno[7]);
                        $('#logradouro').val(retorno[8]);
                        $('#numEndereco').val(retorno[9]);
                        $('#bairro').val(retorno[10]);
                        $('#obs').val(retorno[11]);
                        $('#anotacoes').val(retorno[12]);
                        if(retorno[2]=="URGENCIAR"){
                            document.getElementById("urgente").checked=true;
                        }
                        else if(retorno[2]=="PRIORIZAR"){
                            document.getElementById("priorizar").checked=true;
                        }
                        else if(retorno[2]=="NORMAL"){
                            document.getElementById("normal").checked=true;
                        }
                        $('#fecharModalPesquisa').click();
                        if(retorno[13]===document.getElementById("iniciais").innerText){
                            //desbloquearAlteracaoSI();
                            alert("Mesmo usuário da criação");
                        }
                        else{
                            alert("Não é o mesmo usuário da criação");
                        }
                    }
                    else{
                        console.log("primeiro item não é >0 = " + retorno);
                        alert("Erro ao pesquisar a SI. Verifique o console");
                    }
                }
                else{
                    console.log("retorno não é um array = " + retorno);
                    alert("Erro ao pesquisar a SI. Verifique o console");
                }
            },
            fail: function(){
                alert("falha");
            },
            error: function(){
                alert("error");
            }
        });

        // Aqui você faria a validação real do login, por exemplo, enviando os dados para o servidor via AJAX
        // Ex: $.post('processa_login.php', { username: username, password: password }, function(data) { ... });
    });
});