<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component titulo="Clientes">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component titulo="Id do cliente: " id="inputId" idHelp="idHelp" texto-ajuda="Informe o id do cliente">
                                    <input type="text" id="inputId" aria-describedby="idHelp" class="form-control" v-model="busca.id">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>

                <hr>

                                    <!-- Card de registros -->

                <card-component titulo="Tabela de Registros">
                    <template v-slot:conteudo v-if="locacoes.data">
                        <table-component
                        :dados="locacoes.data"
                        :titulos="

                        {
                            carro: {titulo: 'Veiculo-placa', tipo: 'texto'},
                            cliente: {titulo: 'Cliente', tipo: 'texto'},
                            data_inicio_periodo: {titulo: 'Data de início', tipo: 'dataSistema'},
                            
                        }" 
                        :visualizar="{dataBsTarget: '#modalLocacaoVisualizar',dataBsToggle: 'modal',visivel: true}" 
                        :atualizar="{dataBsTarget: '#modalLocacaoAtualizar',dataBsToggle: 'modal',visivel: true}" 
                        
                        ></table-component>
                    </template>
                    <template v-slot:rodape>
                        <paginate-component>
                            <li v-for="l, key in locacoes.links" :key="key" :class="l.active ? 'page-item active': 'page-item'" @click="paginacao(l)">
                                <a class="page-link" v-if="l.label == 'pagination.previous'">Anterior</a>  
                                <a class="page-link" v-else-if="l.label == 'pagination.next'">Proxima</a>
                                <a class="page-link" v-else>{{l.label}}</a> 
                            </li>
                        </paginate-component>
                        <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#modalLocacao" >Adicionar</button>
                    </template>
                </card-component>


                <!-- Início do Modal de inclusão de locacao -->
                <modal-component titulo="Adicionar locacao" id="modalLocacao">

                    <template v-slot:alertas>
                        <alert-component tipo="success" :detalhes="transacaoDetalhes" v-if="transacaoStatus == 'Adicionado'" titulo="Cadastro realizado com sucesso !"></alert-component>
                        <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca !" v-if=" transacaoStatus === 'Erro' "></alert-component>
                    </template>

                    <template v-slot:conteudo>
                        <div class="form-group">
                            <input-container-component titulo="Nome do cliente: " id="novoNome" idHelp="novoNomeHelp" texto-ajuda="Informe o nome do cliente">
                                <select id="novaMarca" class="form-control" v-model="idCliente" v-if="clientes[0]">
                                    <option value="">Selecione o cliente</option>
                                    <option v-for="cliente in clientes" :value="cliente.id" :key="cliente.id">{{ cliente.nome}}</option>
                                </select>
                            </input-container-component>

                            <input-container-component titulo="Carro: " id="novoCarro" idHelp="novoCarroHelp" texto-ajuda="Informe o carro">
                                <select id="novoCarro" class="form-control" v-model="idCarro" @click="kmCarroConsulta(idCarro)">
                                    <option value="">Selecione o carro</option>
                                    <option v-for="carro in carros" :value="carro.id" :key="carro.id">
                                        <span v-if="carro.modelo"> {{ carro.placa}} - {{ carro.modelo.nome}} </span> 
                                    </option>
                                </select>
                            </input-container-component>
                            <input-container-component titulo="Data Inicial: " id="novaDataInicial" idHelp="novaDataInicialHelp" texto-ajuda="Informe a data do inicio">
                                <input type="date" id="DataInicial" v-model="dataInicio">

                            </input-container-component>
                            <input-container-component titulo="Data de entrega: " id="novaDataPrevista" idHelp="novaDataPrevistaHelp" texto-ajuda="Informe a data prevista para entrega">
                                <input type="date" id="DataFinal" v-model="dataFinal">

                            </input-container-component>
                            <input-container-component titulo="Valor da diaria: " id="novoValor" idHelp="novoValorHelp" texto-ajuda="Informe o valor da diária">
                                <input type="number" id="valorDiaria" v-model="valorDiaria">

                            </input-container-component>
                            <input-container-component titulo="KM do veículo inicial: " id="novoKmInicial" idHelp="novoKmIniciaHelp" texto-ajuda="KM inicial do veículo">
                                <input type="number" id="kmInicial" v-model="kmInicial" disabled>

                            </input-container-component>

                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" @click="salvar" >Salvar</button>
                    </template>
                </modal-component>
                <!-- Final do Modal de inclusão de locacao -->


                <!-- Início do Modal de visualização de locacao -->
                <modal-component titulo="Visualizar locacao" id="modalLocacaoVisualizar">

                    <template v-slot:alertas v-if="this.$store.state.transacao.status">
                        <alert-component tipo="success" :detalhes="this.$store.state.transacao" titulo="Transação realizada com sucesso" v-if="this.$store.state.transacao.status == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" :detalhes="this.$store.state.transacao" titulo="Erro" v-if="this.$store.state.transacao.status == 'erro'"></alert-component>
                    </template>

                    <template v-slot:conteudo v-if="$store.state.item.carro">
                        <div class="form-group">
                            <input-container-component titulo="Nome do cliente: " idHelp="novoNomeHelp">
                                <input class="form-control"  v-model="this.$store.state.item.cliente.nome" disabled>
                            </input-container-component>

                            <input-container-component titulo="Placa: " idHelp="novoCarroHelp">
                                <input class="form-control"  v-model="this.$store.state.item.carro.placa" disabled>
                            </input-container-component>
                            <input-container-component titulo="Data Inicial: " id="novaDataInicial" idHelp="novaDataInicialHelp" >
                                <input class="form-control" type="text" v-model="this.$store.state.item.data_inicio_periodo" disabled>

                            </input-container-component>
                            <input-container-component titulo="Data prevista de entrega: " id="novaDataPrevista" idHelp="novaDataPrevistaHelp" >
                                <input class="form-control" type="text" v-model="this.$store.state.item.data_final_previsto_periodo" disabled>

                            </input-container-component>
                            <input-container-component titulo="Valor da diaria: " id="novoValor" idHelp="novoValorHelp">
                                <input class="form-control" type="number" id="valorDiaria" v-model="this.$store.state.item.valor_diaria" disabled>

                            </input-container-component>
                            <input-container-component titulo="KM do veículo inicial: " id="novoKmInicial" idHelp="novoKmIniciaHelp">
                                <input class="form-control" type="number" id="kmInicial" v-model="this.$store.state.item.km_inicial" disabled>

                            </input-container-component>

                        </div>


                    </template>
                    <template v-slot:rodape>
                        <button class="btn btn-outline-info btn-sm float-lef" data-bs-toggle="modal" data-bs-target="#modalLocacaoEntrega">Realizar entrega</button>
                        <button class="btn btn-outline-danger btn-sm float-left" @click="remover()" v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
                    </template>

                </modal-component>
                <!-- Final do Modal de visualização de locacao -->

                <!-- Início do Modal de atualização de locacao -->
                <modal-component titulo="Atualizar locacao" id="modalLocacaoAtualizar">

                    <template v-slot:alertas>
                        <alert-component tipo="success" :detalhes="transacaoDetalhes" v-if="transacaoStatus == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" :detalhes="transacaoDetalhes" v-if=" transacaoStatus === 'erro' "></alert-component>
                    </template>

                    <template v-slot:conteudo>
                        <div class="form-group">
                            <input-container-component titulo="Nome do cliente: " id="atualizarNome" idHelp="atualizarNomeHelp" texto-ajuda="Informe o nome do cliente">
                                <input type="text" id="atualizarNome" aria-describedby="atualizarNomeHelp" class="form-control" v-model="this.$store.state.item.nome" :placeholder="this.$store.state.item.nome">
                            </input-container-component>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Fechar</button>
                        <button type="button" class="btn btn-primary" @click="atualizar" >Atualizar</button>
                    </template>
                </modal-component>
                <!-- Final do Modal de atualização de locacao -->

                <!-- Início do Modal de entrega de carro -->
                <modal-component titulo="Entregar Veículo" id="modalLocacaoEntrega">

                    <template v-slot:alertas>
                        <alert-component tipo="success" :detalhes="transacaoDetalhes" v-if="transacaoStatus == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" :detalhes="transacaoDetalhes" v-if=" transacaoStatus === 'erro' "></alert-component>
                    </template>

                    <template v-slot:conteudo v-if="this.$store.state.item.carro">
                        <div class="form-group">
                            <input-container-component titulo="Nome do cliente: " idHelp="atualizarNomeHelp" >
                                <input class="form-control"  v-model="this.$store.state.item.cliente.nome" disabled>
                            </input-container-component>

                            <input-container-component titulo="Placa: " idHelp="atualizarNomeHelp">
                            <input class="form-control"  v-model="this.$store.state.item.carro.placa" disabled>
                            </input-container-component>

                        <input-container-component titulo="Data da entrega: "  idHelp="dataEntregaHelp" texto-ajuda="Informe a data da entrega do veículo">
                            <input type="date"  class="form-control" v-model="dataEntrega">
                        </input-container-component>

                        <input-container-component titulo="Kilometragem final: "  idHelp="kmFinalHelp" texto-ajuda="Informe a kilometragem final do veículo">
                            <input type="number"  class="form-control" v-model="kmFinal">
                        </input-container-component>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Fechar</button>
                        <button type="button" class="btn btn-primary" @click="entregarCarro" >Entregar</button>
                    </template>
                </modal-component>
                <!-- Final do Modal de entrega do carro -->
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return{
            urlPaginacao: '',
            urlFiltro: '',
            idCliente: '',
            idCarro: '',
            dataInicio: '',
            dataFinal: '',
            dataEntrega:'',
            valorDiaria: '',
            kmInicial: '',
            kmFinal: '',
            transacaoStatus: '',
            transacaoDetalhes: {},
            locacoes: {data: []},
            clientes: {data: []},
            carros: {data: [], modelo: []},
            busca: {
                id: ''
            }
        }
    }, 
    methods: {
        entregarCarro(){
            let formData = new FormData();
            formData.append('_method', 'patch')
            formData.append('data_final_realizado_periodo', this.dataEntrega)
            formData.append('km_final', this.kmFinal)
            let url = this.$store.state.urlBases.urlLocacoes + '/' + this.$store.state.item.id
            axios.post(url, formData)
                .then(response => {
                    console.log(response)
                    this.transacaoStatus = 'sucesso'
                    this.transacaoDetalhes = {mensagem: 'Atualizado com sucesso'} 
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                })

            
            let formDataCarro = new FormData();
            formDataCarro.append('_method', 'patch')
            formDataCarro.append('disponivel', '1')
            formDataCarro.append('km', this.kmFinal)
            let urlCarro = this.$store.state.urlBases.urlCarros + '/' + this.$store.state.item.carro.id
            axios.post(urlCarro, formDataCarro)
                .then(response => {
                    console.log(response)
                    this.transacaoStatus = 'sucesso'
                    this.transacaoDetalhes = {mensagem: 'Atualizado com sucesso'} 
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                })
        },
        kmCarroConsulta(id){
            let listaCarros = this.$store.state.urlBases.urlCarros + "/" + id

            axios.get(listaCarros)
                .then(response => {
                    this.kmInicial = response.data.km
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
        consultaCarros(){
            let listaCarros = this.$store.state.urlBases.urlCarrosGet
            axios.get(listaCarros)
                .then(response => {
                    this.carros = response.data
                    
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
        consultaClientes(){
            let listaClientes = this.$store.state.urlBases.urlClientesGet
            axios.get(listaClientes)
                .then(response => {
                    this.clientes = response.data
                })
                .catch(errors => {
                    console.log(errors)
                })            
        },
        atualizar(){/*
            let formData = new FormData();
            formData.append('_method', 'patch')
            formData.append('nome', this.$store.state.item.nome)
            let url = this.$store.state.urlBases.urlLocacoes + '/' + this.$store.state.item.id
            axios.post(url, formData)
                .then(response => {
                    this.transacaoStatus = 'sucesso'
                    this.transacaoDetalhes = {mensagem: 'Atualizado com sucesso'} 
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                })
            */
        },
        remover(){
            let confirmacao = confirm('Você tem certeza que deseja apagar o registro ?')
            if(!confirmacao){
                return false
            }
            let formData = new FormData()
            formData.append('_method', 'delete')

            let url = this.$store.state.urlBases.urlLocacoes + '/' + this.$store.state.item.id
            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }
            
            axios.post(url, formData, config)
                .then(response => {
                    this.$store.state.transacao.status = 'sucesso'
                    this.$store.state.transacao.mensagem = response.data.msg
                    this.carregarLista()
                })
                .catch(errors => {
                    this.$store.state.transacao.status = 'erro'
                    this.$store.state.transacao.mensagem = errors.response.data.erro
                })
            
        },
        pesquisar(){
            let filtro =''

            for(let chave in this.busca){
                if(this.busca[chave]){
                    if(filtro != ''){
                        filtro += ";"
                    }
                    filtro += chave + ':like:' + this.busca[chave] 

                }
                if(filtro != ''){
                    this.urlPaginacao = 'page=1'
                    this.urlFiltro = '&filtro=' + filtro

                }else{
                    this.urlFiltro = ''
                }
            }
            this.carregarLista()
        },
        paginacao(l){
            if(l.url){
                //this.urlBase = l.url
                this.urlPaginacao = l.url.split('?')[1]
                this.carregarLista()
            }

        },
        carregarLista(){
            let lista = this.$store.state.urlBases.urlLocacoes + '?' + this.urlPaginacao + this.urlFiltro
            axios.get(lista)
                .then(response => {
                    this.locacoes = response.data
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
        alocarCarro(id){
            let formData = new FormData();
            formData.append('_method', 'patch')
            formData.append('disponivel', 0)
            axios.post(this.$store.state.urlBases.urlCarros+ '/' +id, formData)
                .then(response => {
                    this.transacaoStatus = 'Adicionado'
                    this.transacaoDetalhes = {mensagem: 'ID do registro: '+response.data.id} 
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacaoStatus = 'Erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                })
        },
        salvar(){
            let formData = new FormData();
            formData.append('cliente_id', this.idCliente)
            formData.append('carro_id', this.idCarro)
            formData.append('data_inicio_periodo', this.dataInicio)
            formData.append('data_final_previsto_periodo', this.dataFinal)
            formData.append('valor_diaria', this.valorDiaria)
            formData.append('km_inicial', this.kmInicial)
            formData.append('km_final', this.kmFinal)
            axios.post(this.$store.state.urlBases.urlLocacoes, formData)
                .then(response => {
                    this.transacaoStatus = 'Adicionado'
                    this.transacaoDetalhes = {mensagem: 'ID do registro: '+response.data.id} 
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacaoStatus = 'Erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                })
            this.alocarCarro(this.idCarro)
            window.location.reload()
        }
    },
    mounted(){
        this.carregarLista()
        this.consultaCarros()
        this.consultaClientes()
    }

}
</script>