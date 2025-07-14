$(document).ready(function() {
    // Referências aos campos de pesquisa
    const $searchNumero = $('#searchNumero');
    const $searchNome = $('#searchNome');
    const $searchGenitora = $('#searchGenitora');
    const $searchNascimento = $('#searchNascimento');

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
        var parametroBusca ='';
        var tipo='';
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
        loadProcedimentos(parametroBusca,tipo);
    });
    // Função para carregar dados na tabela via AJAX
    function loadProcedimentos(parametroBusca = "", tipo="") {
        $procedimentosTableBody.html('<tr><td colspan="5" class="text-center text-muted">Carregando procedimentos...</td></tr>');

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
                    response.dados.forEach(procedimento => {
                        tableRows += `
                            <tr>
                                <td>${procedimento.numero}/${procedimento.ano}</td>
                                <td>${procedimento.nome_pessoa}</td>
                                <td>${procedimento.nascimento_pessoa}</td>
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
                                        </svg>&nbspExcluir
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $procedimentosTableBody.html(tableRows);
                } else {
                    $procedimentosTableBody.html('<tr><td colspan="5" class="text-center text-muted">Nenhum procedimento encontrado.</td></tr>');
                }
            },
            error: function() {
                $procedimentosTableBody.html('<tr><td colspan="5" class="text-center text-danger">Erro ao carregar dados. Tente novamente.</td></tr>');
            }
        });
    }

    // Evento para o botão "Procurar"
    //$('#btnProcurar').on('click', function() {
    //    const searchParams = {
    //        numero: $searchNumero.val(),
    //        nome: $searchNome.val(),
    //        genitora: $searchGenitora.val(),
    //        nascimento: $searchNascimento.val()
    //    };
    //    loadProcedimentos(searchParams);
    //});
    

    // Lógica para Modais (Visualizar, Editar, Excluir)
    // Usar delegação de eventos para botões dentro da tabela
    $procedimentosTableBody.on('click', '.btn-visualizar', function() {
        const id = $(this).data('token');
        // Simular busca de dados do procedimento por ID
        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'GET',
            data: { action: 'get_procedimento', id: id },
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso" && response.dados) {
                    const proc = response.dados;
                    $('#viewNumeroProcedimento').val(proc.numero_procedimento);
                    $('#viewAnoProcedimento').val(proc.ano_procedimento);
                    $('#viewBairro').val(proc.bairro);
                    $('#viewTerritorioBairro').val(proc.territorio_bairro);
                    $('#viewNomePessoa').val(proc.nome_pessoa);
                    $('#viewDataNascimentoPessoa').val(proc.data_nascimento_pessoa);
                    $('#viewSexoPessoa').val(proc.sexo_pessoa);
                    $('#viewNomeGenitora').val(proc.nome_genitora);
                    $('#viewDataNascimentoGenitora').val(proc.data_nascimento_genitora);
                    $('#viewSexoGenitora').val(proc.sexo_genitora);
                    $('#viewDemandante').val(proc.demandante);
                    $('#visualizarModal').modal('show');
                } else {
                    alert('Erro ao carregar dados do procedimento.'); // Usar modal customizado em produção
                }
            }
        });
    });

    $procedimentosTableBody.on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        // Simular busca de dados do procedimento por ID para preencher o formulário de edição
        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'GET',
            data: { action: 'get_procedimento', id: id },
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso" && response.dados) {
                    const proc = response.dados;
                    $('#editProcedimentoId').val(proc.id);
                    $('#editNumeroProcedimento').val(proc.numero_procedimento);
                    $('#editAnoProcedimento').val(proc.ano_procedimento);
                    $('#editBairro').val(proc.bairro);
                    $('#editTerritorioBairro').val(proc.territorio_bairro);
                    $('#editNomePessoa').val(proc.nome_pessoa);
                    $('#editDataNascimentoPessoa').val(proc.data_nascimento_pessoa);
                    $('#editSexoPessoa').val(proc.sexo_pessoa);
                    $('#editNomeGenitora').val(proc.nome_genitora);
                    $('#editDataNascimentoGenitora').val(proc.data_nascimento_genitora);
                    $('#editSexoGenitora').val(proc.sexo_genitora);
                    $('#editDemandante').val(proc.demandante);
                    $('#editarModal').modal('show');
                } else {
                    alert('Erro ao carregar dados do procedimento para edição.'); // Usar modal customizado
                }
            }
        });
    });

    $('#formEditarProcedimento').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize(); // Pega todos os dados do formulário
        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'POST',
            data: formData + '&action=update_procedimento', // Adiciona a ação para o PHP
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso") {
                    alert('Procedimento atualizado com sucesso!'); // Usar modal customizado
                    $('#editarModal').modal('hide');
                    loadProcedimentos(); // Recarrega a tabela
                } else {
                    alert('Erro ao atualizar procedimento: ' + response.mensagem); // Usar modal customizado
                }
            },
            error: function() {
                alert('Erro de comunicação com o servidor ao atualizar.'); // Usar modal customizado
            }
        });
    });

    $procedimentosTableBody.on('click', '.btn-excluir', function() {
        const id = $(this).data('id');
        $('#deleteProcedimentoId').val(id);
        $('#excluirModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        const id = $('#deleteProcedimentoId').val();
        $.ajax({
            url: 'gerencias/processa_procedimentos.php',
            method: 'POST',
            data: { action: 'delete_procedimento', id: id },
            dataType: 'json',
            success: function(response) {
                if (response && response.mensagem === "Sucesso") {
                    alert('Procedimento excluído com sucesso!'); // Usar modal customizado
                    $('#excluirModal').modal('hide');
                    loadProcedimentos(); // Recarrega a tabela
                } else {
                    alert('Erro ao excluir procedimento: ' + response.mensagem); // Usar modal customizado
                }
            },
            error: function() {
                alert('Erro de comunicação com o servidor ao excluir.'); // Usar modal customizado
            }
        });
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
