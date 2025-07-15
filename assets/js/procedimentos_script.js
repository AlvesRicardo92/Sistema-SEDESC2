$(document).ready(function() {
    // Referências aos campos de pesquisa
    const $searchNumero = $('#searchNumero');
    const $searchNome = $('#searchNome');
    const $searchGenitora = $('#searchGenitora');
    const $searchNascimento = $('#searchNascimento');
    var parametroBusca ='';
    var tipo='';
    var $linhaTabelaExcluir;
    var sucessoNaDesativacao;

    // Referência ao corpo da tabela
    const $procedimentosTableBody = $('#procedimentosTableBody');

    // Função para limpar outros campos de pesquisa
    function clearOtherSearchFields(currentField) {
        const fields = [$searchNumero, $searchNome, $searchGenitora, $searchNascimento];
        fields.forEach(field => {
            if (field.attr('id') !== currentField.attr('id')) {
                field.val('');
            }
        });
    }

    // Adiciona listeners para os eventos de 'input' nos campos de pesquisa
    $searchNumero.on('input', function() { clearOtherSearchFields($(this)); });
    $searchNome.on('input', function() { clearOtherSearchFields($(this)); });
    $searchGenitora.on('input', function() { clearOtherSearchFields($(this)); });
    $searchNascimento.on('input', function() { clearOtherSearchFields($(this)); });


    $('#btnProcurar').on('click', function() {
        if ($searchNumero.val()!=""){
            parametroBusca=$searchNumero.val();
            tipo='numero';
        }
        else if ($searchNome.val()!=""){
            parametroBusca=$searchNome.val();
            tipo='nome';
        }
        else if ($searchGenitora.val()!=""){
            parametroBusca=$searchGenitora.val();
            tipo='genitora';
        }
        else if ($searchNascimento.val()!=""){
            parametroBusca=$searchNascimento.val();
            tipo='nascimento';
        }
        if(parametroBusca==''){
            $procedimentosTableBody.html('<tr><td colspan="6" class="text-center text-muted">Digite em algum dos campos acima</td></tr>');
        }else{
            loadProcedimentos(parametroBusca,tipo);
        }
        
    });
    // Função para carregar dados na tabela via AJAX
    function loadProcedimentos(parametroBusca = "", tipo="") {
        $procedimentosTableBody.html('<tr><td colspan="6" class="text-center text-muted">Carregando procedimentos...</td></tr>');

        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'POST', 
            data: {parametroBusca:parametroBusca,
                    acao:"buscar",
                    tipo:tipo
                },
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso" && response.dados.length > 0) {
                    let tableRows = '';
                    let dataNascimentoPessoa='';
                    let dataOriginal='';
                    let partes;
                    response.dados.forEach(procedimento => {
                        dataOriginal = procedimento.nascimento_pessoa; 
                        partes = dataOriginal.split('-');
                        dataNascimentoPessoa = `${partes[2]}/${partes[1]}/${partes[0]}`;
                        tableRows += `
                            <tr>
                                <td>${procedimento.numero}/${procedimento.ano}</td>
                                <td>${procedimento.territorio}</td>
                                <td>${procedimento.nome_pessoa}</td>
                                <td>${dataNascimentoPessoa}</td>
                                <td>${procedimento.nome_genitora}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm me-1 btn-visualizar" data-token="${procedimento.token}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                        </svg>&nbspVisualizar
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm me-1 btn-editar" data-token="${procedimento.token}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                        </svg>&nbspEditar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-excluir" data-token="${procedimento.token}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                        </svg>&nbspDesativar
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $procedimentosTableBody.html(tableRows);
                } else {
                    $procedimentosTableBody.html('<tr><td colspan="6" class="text-center text-muted">Nenhum procedimento encontrado.</td></tr>');
                }
            },
            error: function() {
                $procedimentosTableBody.html('<tr><td colspan="6" class="text-center text-danger">Erro ao carregar dados. Tente novamente.</td></tr>');
            }
        });
    }   

    // Lógica para Modais (Visualizar, Editar, Excluir)
    // Usar delegação de eventos para botões dentro da tabela
    $procedimentosTableBody.on('click', '.btn-visualizar', function() {
        const token = $(this).data('token');
        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'POST',
            data: { acao: 'visualizar', token: token },
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso" && response.dados) {
                    const procedimento = response.dados[0];
                    $('#viewNumeroProcedimento').val(procedimento.numero);
                    $('#viewAnoProcedimento').val(procedimento.ano);
                    $('#viewBairro').val(procedimento.bairro);
                    $('#viewTerritorioBairro').val(procedimento.territorio);
                    $('#viewNomePessoa').val(procedimento.nome_pessoa);
                    $('#viewDataNascimentoPessoa').val(procedimento.nascimento_pessoa);
                    $('#viewSexoPessoa').val(procedimento.sexo_pessoa);
                    $('#viewNomeGenitora').val(procedimento.nome_genitora);
                    $('#viewDataNascimentoGenitora').val(procedimento.nascimento_genitora);
                    $('#viewSexoGenitora').val(procedimento.sexo_genitora);
                    $('#viewDemandante').val(procedimento.demandante);
                    $('#visualizarModal').modal('show');
                } else {
                    console.log ("falha dados");
                    alert('Erro ao carregar dados do procedimento.'); // Usar modal customizado em produção
                }
            },
            error: function() {
                console.log ("erro");
                alert('Erro ao buscar o procedimento.');
            }
        });
    });

    $procedimentosTableBody.on('click', '.btn-editar', function() {
        const token = $(this).data('token');
        let procedimentoData = null; // Variável para armazenar os detalhes do procedimento
    
        // Limpar todos os selects no início, antes de carregar
        $('.select-bairros').empty();
        $('.select-pessoas').empty();
        $('.select-genitoras').empty();
        $('.select-sexos').empty();
        $('.select-sexos-genitora').empty();
        $('.select-demandantes').empty();
    
        // Crie um array para armazenar as 'promessas' de cada chamada AJAX
        const ajaxCalls = [];
    
        // 1. AJAX para buscar os detalhes do procedimento (processa_procedimentos.php)
        const getProcedimentoDetails = $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'POST',
            data: {acao:'editar',token:token},
            dataType: 'json'
        }).done(function(response) {
            if (response && response.mensagem === "Sucesso" && response.dados && response.dados.length > 0) {
                procedimentoData = response.dados[0]; // Armazena os dados do procedimento
                // Preenche campos de texto/input imediatamente (não dependem de outros selects)
                $('#editNumeroProcedimento').val(procedimentoData.numero);
                $('#editAnoProcedimento').val(procedimentoData.ano);
                $('#editTerritorioBairro').val(procedimentoData.territorio);
                $('#editDataNascimentoPessoa').val(procedimentoData.nascimento_pessoa);
                $('#editDataNascimentoGenitora').val(procedimentoData.nascimento_genitora);
                $('#salvarAlteracoes').data('token', token);
            } else {
                console.log("falha dados do procedimento para edição");
                alert('Erro ao carregar dados do procedimento para edição.');
                procedimentoData = null; // Indica que os dados não foram carregados com sucesso
            }
        }).fail(function() {
            console.log("erro na requisição de detalhes do procedimento");
            alert('Erro ao buscar o procedimento para edição.');
            procedimentoData = null;
        });
        ajaxCalls.push(getProcedimentoDetails); // Adiciona a promessa ao array
    
        // 2. AJAX para popular o select de bairros
        const populateBairros = $.ajax({
            url: 'gerencias/buscar_bairros.php',
            method: 'POST',
            data: {tipo:'editar'},
            dataType: 'json'
        }).done(function(response) {
            if (response && response.mensagem === "Sucesso" && response.dados && response.dados.length > 0) {
                $('.select-bairros').append('<option value="0">Selecione...</option>');
                response.dados.forEach(bairro => {
                    $('.select-bairros').append(`<option value="${bairro.id}">${bairro.nome}</option>`);
                });
            } else {
                console.log("falha ao carregar bairros");
                $('.select-bairros').append('<option value="0">Nenhum bairro encontrado</option>');
            }
        }).fail(function() {
            console.log("erro na requisição de bairros");
            $('.select-bairros').append('<option value="0">Erro ao carregar</option>');
        });
        ajaxCalls.push(populateBairros);
    
        // 3. AJAX para popular os selects de pessoas e genitoras
        const populatePessoas = $.ajax({
            url: 'gerencias/buscar_pessoas.php',
            method: 'POST',
            data: {},
            dataType: 'json'
        }).done(function(response) {
            if (response && response.mensagem === "Sucesso" && response.dados && response.dados.length > 0) {
                $('.select-pessoas').append('<option value="0">Selecione...</option>');
                $('.select-genitoras').append('<option value="0">Selecione...</option>');
                response.dados.forEach(pessoa => {
                    $('.select-pessoas').append(`<option value="${pessoa.id}">${pessoa.nome}</option>`);
                    $('.select-genitoras').append(`<option value="${pessoa.id}">${pessoa.nome}</option>`);
                });
            } else {
                console.log("falha ao carregar pessoas/genitoras");
                $('.select-pessoas').append('<option value="0">Nenhuma pessoa encontrada</option>');
                $('.select-genitoras').append('<option value="0">Nenhuma genitora encontrada</option>');
            }
        }).fail(function() {
            console.log("erro na requisição de pessoas");
            $('.select-pessoas').append('<option value="0">Erro ao carregar</option>');
            $('.select-genitoras').append('<option value="0">Erro ao carregar</option>');
        });
        ajaxCalls.push(populatePessoas);
    
        // 4. AJAX para popular os selects de sexo
        const populateSexos = $.ajax({
            url: 'gerencias/buscar_sexos.php',
            method: 'POST',
            data: {},
            dataType: 'json'
        }).done(function(response) {
            if (response && response.mensagem === "Sucesso" && response.dados && response.dados.length > 0) {
                $('.select-sexos').append('<option value="0">Selecione...</option>');
                $('.select-sexos-genitora').append('<option value="0">Selecione...</option>');
                response.dados.forEach(sexo => {
                    $('.select-sexos').append(`<option value="${sexo.id}">${sexo.nome}</option>`);
                    $('.select-sexos-genitora').append(`<option value="${sexo.id}">${sexo.nome}</option>`);
                });
            } else {
                console.log("falha ao carregar sexos");
                $('.select-sexos').append('<option value="0">Nenhum sexo encontrado</option>');
                $('.select-sexos-genitora').append('<option value="0">Nenhum sexo encontrado</option>');
            }
        }).fail(function() {
            console.log("erro na requisição de sexos");
            $('.select-sexos').append('<option value="0">Erro ao carregar</option>');
            $('.select-sexos-genitora').append('<option value="0">Erro ao carregar</option>');
        });
        ajaxCalls.push(populateSexos);
    
        // 5. AJAX para popular o select de demandantes
        const populateDemandantes = $.ajax({
            url: 'gerencias/buscar_demandantes.php',
            method: 'POST',
            data: {},
            dataType: 'json'
        }).done(function(response) {
            if (response && response.mensagem === "Sucesso" && response.dados && response.dados.length > 0) {
                $('.select-demandantes').append('<option value="0">Selecione...</option>');
                response.dados.forEach(demandante => {
                    $('.select-demandantes').append(`<option value="${demandante.id}">${demandante.nome}</option>`);
                });
            } else {
                console.log("falha ao carregar demandantes");
                $('.select-demandantes').append('<option value="0">Nenhum demandante encontrado</option>');
            }
        }).fail(function() {
            console.log("erro na requisição de demandantes");
            $('.select-demandantes').append('<option value="0">Erro ao carregar</option>');
        });
        ajaxCalls.push(populateDemandantes);
    
    
        // Usa $.when() para aguardar TODAS as chamadas AJAX finalizarem
        $.when.apply($, ajaxCalls).done(function() {
            // Este bloco só é executado quando TODAS as requisições AJAX foram concluídas e seus `done` (success) ou `fail` callbacks já rodaram.
            
            if (procedimentoData) { // Verifica se os dados do procedimento foram carregados com sucesso
                // Agora é seguro tentar selecionar as opções, pois elas já devem estar no DOM
                $('#select-bairros').val(procedimentoData.id_bairro);
                $('#select-pessoas').val(procedimentoData.id_pessoa);
                $('#select-sexos').val(procedimentoData.id_sexo);
                $('#select-genitoras').val(procedimentoData.id_genitora_pessoa);
                $('#select-sexos-genitora').val(procedimentoData.id_sexo_genitora);
                $('#select-demandantes').val(procedimentoData.id_demandante);
                
                // Finalmente, mostra o modal
                $('#editarModal').modal('show');
            } else {
                // Mensagens de erro já foram exibidas nos .fail ou .done individuais.
                // Poderia ter um alerta final aqui se nenhuma informação de procedimento foi carregada.
            }
        }).fail(function() {
            // Este .fail do $.when() é acionado se *qualquer uma* das requisições AJAX falhar.
            // As mensagens de erro específicas já foram exibidas nos .fail individuais.
            console.error("Um ou mais carregamentos de dados falharam durante a inicialização do modal de edição.");
            alert('Ocorreu um erro ao carregar os dados necessários para o modal de edição.');
        });
    });
    // Referência à div de mensagem no modal
    const $modalMessage = $('#modalMessage');
    

    // Função para exibir a mensagem no modal
    function showModalMessage(message, type) {
        // Limpa classes anteriores e oculta
        $modalMessage.removeClass('d-none alert-success alert-danger alert-warning').empty();

        // Adiciona a classe de estilo e a mensagem
        $modalMessage.text(message);
        if (type === 'success') {
            $modalMessage.addClass('alert-success');
        } else if (type === 'error') {
            $modalMessage.addClass('alert-danger');
        } else if (type === 'warning') { // Para o status 'aviso' do PHP
            $modalMessage.addClass('alert-warning');
        }
        
        // Exibe a div de mensagem
        $modalMessage.removeClass('d-none').slideDown(); // slideDown para uma animação suave
    }

    // Oculta a mensagem quando o modal é fechado ou antes de uma nova submissão
    $('#editarModal').on('hidden.bs.modal', function () {
        $modalMessage.addClass('d-none').empty(); // Oculta e limpa a mensagem
    });

    // Oculta a mensagem quando o modal é exibido (para garantir que esteja limpo ao abrir)
    $('#editarModal').on('shown.bs.modal', function () {
        $modalMessage.addClass('d-none').empty(); // Oculta e limpa a mensagem
    });

    

    $('#formEditarProcedimento').on('submit', function(e) {
        e.preventDefault();
        const token = $('#salvarAlteracoes').data('token');

        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'POST',
            data: {acao:'update',
                   token:token,
                   bairro:$('.select-bairros').val(),
                   pessoa:$('.select-pessoas').val(),
                   nascimento:$('#editDataNascimentoPessoa').val(),
                   sexo:$('.select-sexos').val(),
                   genitora:$('.select-genitoras').val(),
                   nascimento_genitora:$('#editDataNascimentoGenitora').val(),
                   sexo_genitora:$('.select-sexos-genitora').val(),
                   demandante:$('.select-demandantes').val()},
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso") {
                    showModalMessage('Procedimento atualizado com sucesso!', 'success');
                    // Aguarda 2 segundos e esconde o modal
                    setTimeout(function() {
                        $('#editarModal').modal('hide');
                        loadProcedimentos(parametroBusca,tipo);
                    }, 2000);
                } else {
                    showModalMessage('Erro ao atualizar procedimento: ' + response.mensagem, 'error');
                }
            },
            error: function() {
                showModalMessage('Erro de comunicação com o servidor ao atualizar.', 'error');
            }
        });
    });

    $('.select-pessoas').on('change', function() {
        $.ajax({
            url: 'gerencias/buscar_pessoas.php',
            method: 'POST',
            data: {id:$('#select-pessoas').val()},
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso") {
                    $('#editDataNascimentoPessoa').val(response.dados[0].data_nascimento);
                    $('.select-sexos').val(response.dados[0].id_sexo);
                } else {
                    showModalMessage('Erro ao atualizar dados da pessoa: ' + response.mensagem, 'error');
                }
            },
            error: function() {
                showModalMessage('Erro de comunicação com o servidor ao atualizar.', 'error');
            }
        });
    });
    $('.select-genitoras').on('change', function() {
        $.ajax({
            url: 'gerencias/buscar_pessoas.php',
            method: 'POST',
            data: {id:$('.select-genitoras').val()},
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso") {
                    $('#editDataNascimentoGenitora').val(response.dados[0].data_nascimento);
                    $('.select-sexos-genitora').val(response.dados[0].id_sexo);
                } else {
                    showModalMessage('Erro ao atualizar dados da genitora: ' + response.mensagem, 'error');
                }
            },
            error: function() {
                showModalMessage('Erro de comunicação com o servidor ao atualizar.', 'error');
            }
        });
    });

    $procedimentosTableBody.on('click', '.btn-excluir', function() {
        // Encontra a linha (<tr>) mais próxima do botão clicado
        $linhaTabelaExcluir = $(this).closest('tr');

        // Encontra a primeira célula (<td>) dentro dessa linha
        const $primeiraColuna = $linhaTabelaExcluir.find('td:first');

        // Pega o texto dessa primeira célula
        const valorPrimeiraColuna = $primeiraColuna.text().trim();
        $('.modal-body p').html("Deseja realmente desativar o procedimento <strong>" + valorPrimeiraColuna + "</strong>?");
        const token = $(this).data('token');
        $('#confirmDeleteBtn').data('token', token);
        $('#excluirModal').modal('show');
    });

    const $modalMessageDesativar = $('#modalMessageDesativar');
    // Função para exibir a mensagem no modal
    function showModalMessageDesativar(message, type) {
        // Limpa classes anteriores e oculta
        $modalMessageDesativar.removeClass('d-none alert-success alert-danger alert-warning').empty();

        // Adiciona a classe de estilo e a mensagem
        $modalMessageDesativar.text(message);
        if (type === 'success') {
            $modalMessageDesativar.addClass('alert-success');
        } else if (type === 'error') {
            $modalMessageDesativar.addClass('alert-danger');
        } else if (type === 'warning') { // Para o status 'aviso' do PHP
            $modalMessageDesativar.addClass('alert-warning');
        }
        
        // Exibe a div de mensagem
        $modalMessageDesativar.removeClass('d-none').slideDown(); // slideDown para uma animação suave
    }

    // Oculta a mensagem quando o modal é fechado ou antes de uma nova submissão
    $('#excluirModal').on('hidden.bs.modal', function () {
        $modalMessageDesativar.addClass('d-none').empty(); // Oculta e limpa a mensagem
    });

    // Oculta a mensagem quando o modal é exibido (para garantir que esteja limpo ao abrir)
    $('#excluirModal').on('shown.bs.modal', function () {
        $modalMessageDesativar.addClass('d-none').empty(); // Oculta e limpa a mensagem
    });

    $('#confirmDeleteBtn').on('click', function() {
        const token = $(this).data('token');
        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'POST',
            data: { acao: 'desativar', token: token },
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso") {
                    sucessoNaDesativacao=true;
                    showModalMessageDesativar('Procedimento desativado com sucesso!', 'success');
                    setTimeout(function() {
                        $('#excluirModal').modal('hide');
                    }, 2000);
                } else {
                    sucessoNaDesativacao=false;
                    showModalMessageDesativar('Erro ao desativar procedimento: ' + response.mensagem, 'danger');
                    alert('Erro ao excluir procedimento: ' + response.mensagem); // Usar modal customizado
                }
            },
            error: function() {
                sucessoNaDesativacao=false;
                showModalMessageDesativar('Erro de comunicação com o servidor ao desativar', 'danger');
            }
        });
    });

    $('#excluirModal').on('hidden.bs.modal', function () {
        showModalMessageDesativar('', 'd-none'); // Limpa e oculta a mensagem
        
        // *** AQUI: Ação de fadeOut e remoção da linha da tabela APÓS o modal fechar ***
        if (sucessoNaDesativacao && $linhaTabelaExcluir && $linhaTabelaExcluir.length > 0) {
            $linhaTabelaExcluir.fadeOut(500, function() { 
                $(this).remove();
            });
        }
        sucessoNaDesativacao = false;
    });

    $('#formNovoProcedimento').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize(); // Pega todos os dados do formulário
        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'POST',
            data: formData + '&action=add_procedimento', // Adiciona a ação para o PHP
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso") {
                    alert('Procedimento adicionado com sucesso!'); // Usar modal customizado
                    $('#novoProcedimentoModal').modal('hide');
                    $('#formNovoProcedimento')[0].reset(); // Limpa o formulário
                    loadProcedimentos(); // Recarrega a tabela
                } else {
                    alert('Erro ao adicionar procedimento: ' + response.mensagem); // Usar modal customizado
                }
            },
            error: function() {
                alert('Erro de comunicação com o servidor ao adicionar.'); // Usar modal customizado
            }
        });
    });

    // Opcional: Carregar procedimentos ao carregar a página (sem parâmetros de busca)
    // loadProcedimentos();
});
