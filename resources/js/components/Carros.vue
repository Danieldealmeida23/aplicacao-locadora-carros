<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component titulo="Carros">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID: " id="inputId" idHelp="idHelp" texto-ajuda="Informe o ID do carro">
                                    <input type="number" id="inputId" aria-describedby="idHelp" class="form-control" v-model="busca.id">
                                </input-container-component>
                            </div>

                            <div class="col mb-3">
                                <input-container-component titulo="Placa do carro: " id="inputPlaca" idHelp="placaHelp" texto-ajuda="Informe o placa do carro">
                                    <input type="text" id="inputPlaca" aria-describedby="placaHelp" class="form-control" v-model="busca.placa">
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
                    <template v-slot:conteudo>
                        <table-component v-if="carros.data"
                        :dados="carros.data"
                        :titulos="
                        {

                            modelo: {titulo: 'Modelo', tipo: 'texto'},
                            placa: {titulo: 'Placa', tipo: 'texto'},
                            disponivel: {titulo: 'Disponibilidade', tipo: 'texto'},
                            created_at: {titulo: 'Criado em', tipo: 'data'}
                        }" 
                        :visualizar="{dataBsTarget: '#modalCarroVisualizar',dataBsToggle: 'modal',visivel: true}" 
                        :atualizar="{dataBsTarget: '#modalCarroAtualizar',dataBsToggle: 'modal',visivel: true}" 
                        
                        ></table-component>
                    </template>
                    <template v-slot:rodape>
                        <paginate-component>
                            <li v-for="l, key in carros.links" :key="key" :class="l.active ? 'page-item active': 'page-item'" @click="paginacao(l)">
                                <a class="page-link" v-if="l.label == 'pagination.previous'">Anterior</a>  
                                <a class="page-link" v-else-if="l.label == 'pagination.next'">Proxima</a>
                                <a class="page-link" v-else>{{l.label}}</a> 
                            </li>
                        </paginate-component>
                        <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#modalCarro" @click="carregarModelos">Adicionar</button>
                    </template>
                </card-component>


                <!-- Início do Modal de inclusão de carro -->
                <modal-component titulo="Adicionar carro" id="modalCarro">

                    <template v-slot:alertas>
                        <alert-component tipo="success" :detalhes="transacaoDetalhes" v-if="transacaoStatus == 'Adicionado'" titulo="Cadastro realizado com sucesso !"></alert-component>
                        <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar o carro !" v-if=" transacaoStatus === 'Erro' "></alert-component>
                    </template>

                    <template v-slot:conteudo>
                        <div class="form-group">
                            <input-container-component titulo="Modelo do carro: " id="novoModelo" idHelp="novoModeloHelp" texto-ajuda="Informe o modelo do carro">
                            <select id="novoModelo" class="form-control" v-model="idmodelo">
                                <option value="">Selecione o Modelo </option>
                                <option v-for="modelo in modelos" :value="modelo.id" :key="modelo.id">{{ modelo.nome}}</option>
                            </select>
                            </input-container-component>
                        </div>

                        <div class="form-group">
                            <input-container-component titulo="Placa do carro: " id="novoPlaca" idHelp="novoPlacaHelp" texto-ajuda="Informe a placa do carro">
                                <input type="text" id="novoPlaca" aria-describedby="novoPlacaHelp" class="form-control" v-model="placa">
                            </input-container-component>
                        </div>

                        <div class="form-group">
                            <input-container-component titulo="Disponível ? " id="novoDisponivel" idHelp="novoDisponivelHelp" texto-ajuda="Verifique se o veículo está disponível">
                                <input type="text" id="novoDisponivel" aria-describedby="novoDisponivelHelp" class="form-control" :value="disponibilidade == '1' ? 'Sim' : 'Não'" disabled >
                            </input-container-component>
                        </div>

                        <div class="form-group">
                            <input-container-component titulo="Kilometragem de entrada: " id="novoKm" idHelp="novoKmHelp" texto-ajuda="Informe a kilometragem do carro">
                                <input type="text" id="novoKm" aria-describedby="novoKmHelp" class="form-control" v-model="kilometragem">
                            </input-container-component>
                        </div>


                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" @click="salvar" >Salvar</button>
                    </template>
                </modal-component>
                <!-- Final do Modal de inclusão do carro -->


                <!-- Início do Modal de visualização do carro -->
                <modal-component titulo="Visualizar Carro" id="modalCarroVisualizar">

                    <template v-slot:alertas v-if="this.$store.state.transacao.status">
                        <alert-component tipo="success" :detalhes="this.$store.state.transacao" titulo="Transação realizada com sucesso" v-if="this.$store.state.transacao.status == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" :detalhes="this.$store.state.transacao" titulo="Erro" v-if="this.$store.state.transacao.status == 'erro'"></alert-component>
                    </template>

                    <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'" >
                        
                        <input-container-component titulo="Modelo do carro" v-if="this.$store.state.item.modelo">
                            <input type="text" class="form-control" :value="this.$store.state.item.modelo.nome" disabled>
                        </input-container-component>

                        <input-container-component titulo="Placa do carro">
                            <input type="text" class="form-control" :value="this.$store.state.item.placa" disabled>
                        </input-container-component>

                        <input-container-component titulo="Disponibilidade">
                            <input type="text" class="form-control" :value="this.$store.state.item.disponivel == '1' ? 'Disponível' : 'Não disponível' " disabled>
                        </input-container-component>

                        <input-container-component v-if="this.$store.state.item.modelo">
                            <img width="200px" :src="'http://localhost:8000/storage/'+this.$store.state.item.modelo.imagem" v-if="this.$store.state.item.modelo.imagem != undefined"> 
                        </input-container-component>

                        <input-container-component titulo="Data de Criação">
                        <input type="text" class="form-control" :value="$filters.formataDataTempo($store.state.item.created_at)" disabled>
                        </input-container-component>


                    </template>
                    <template v-slot:rodape>
                        <button class="btn btn-outline-danger btn-sm float-left" @click="remover(obj)" v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </template>

                </modal-component>
                <!-- Final do Modal de visualização do carro -->

                <!-- Início do Modal de atualização do carro -->
                <modal-component titulo="Atualizar carro" id="modalCarroAtualizar">

                    <template v-slot:alertas>
                        <alert-component tipo="success" :detalhes="transacaoDetalhes" v-if="transacaoStatus == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" :detalhes="transacaoDetalhes" v-if=" transacaoStatus === 'erro' "></alert-component>
                    </template>

                    <template v-slot:conteudo>
                        <div class="form-group">
                            <input-container-component titulo="Modelo do carro: " id="atualizaModelo" idHelp="atualizaModeloHelp" texto-ajuda="Informe o modelo do carro" @click="carregarModelos" v-if="this.$store.state.item.modelo">
                            <select id="atualizaModelo" class="form-control" v-model="this.$store.state.item.modelo_id">
                                <option value="">Selecione o Modelo </option>
                                <option v-for="modelo in modelos" :value="modelo.id" :key="modelo.id">{{ modelo.nome}}</option>
                            </select>
                            </input-container-component>
                        </div>
                        <div class="form-group">
                            <input-container-component titulo="Placa: " id="atualizarPlaca" idHelp="atualizarPlacaHelp" texto-ajuda="Informe a placa do carro">
                                <input type="text" id="atualizarPlaca" aria-describedby="atualizarPlacaHelp" class="form-control" v-model="this.$store.state.item.placa">
                            </input-container-component>
                        </div>
                        <div class="form-group">
                            <input-container-component titulo="Disponibilidade: " id="atualizarDisponivel" idHelp="atualizarDisponivelHelp" >
                                <input type="text" id="atualizarDisponivel" aria-describedby="atualizarDisponivelHelp" class="form-control" :value="this.$store.state.item.disponivel == '1' ? 'Disponível' : 'Indisponível'" disabled>
                            </input-container-component>
                        </div>
                        <div class="form-group">
                            <input-container-component titulo="Kilometragem: " id="atualizarKilometragem" idHelp="atualizarKilometragemHelp" texto-ajuda="Informe a kilometragem do carro">
                                <input type="text" id="atualizarKilometragem" aria-describedby="atualizarKilometragemHelp" class="form-control" v-model="this.$store.state.item.km">
                            </input-container-component>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Fechar</button>
                        <button type="button" class="btn btn-primary" @click="atualizar" >Atualizar</button>
                    </template>
                </modal-component>
                <!-- Final do Modal de atualização do carro -->
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
            modelos: '',
            idmodelo: '',
            placa: '',
            disponibilidade: '1',
            kilometragem: '',
            transacaoStatus: '',
            transacaoDetalhes: {},
            carros: {data: []},
            busca: {
                id: '',
                placa: ''
            },
            
            
        }
    }, 
    methods: {
        carregarModelos(){
            let lista = this.$store.state.urlBases.urlModelosGet
            axios.get(lista)
                .then(response => {
                    this.modelos = response.data
                })
                .catch(errors => {
                })
        },
        atualizar(){
            let formData = new FormData();
            formData.append('_method', 'patch')
            formData.append('modelo_id', this.$store.state.item.modelo_id)
            formData.append('disponivel', this.$store.state.item.disponivel)
            formData.append('km', this.$store.state.item.km)
            formData.append('placa', this.$store.state.item.placa)
            let url = this.$store.state.urlBases.urlCarros + '/' + this.$store.state.item.id
            axios.post(url, formData)
                .then(response => {
                    this.transacaoStatus = 'sucesso'
                    this.transacaoDetalhes = {mensagem: 'Atualizado com sucesso'} 
                    this.arquivoImagem.value = ''
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.mensagem, dados: errors.response.data } 
                })
        },
        remover(){
            let confirmacao = confirm('Você tem certeza que deseja apagar o registro ?')
            if(!confirmacao){
                return false
            }
            let formData = new FormData()
            formData.append('_method', 'delete')

            let url = this.$store.state.urlBases.urlCarros + '/' + this.$store.state.item.id
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
            let filtro = ''

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
                //this.$store.state.urlBases.urlCarros = l.url
                this.urlPaginacao = l.url.split('?')[1]
                this.carregarLista()
            }

        },
        carregarLista(){
            let lista = this.$store.state.urlBases.urlCarros + '?' + this.urlPaginacao + this.urlFiltro
            axios.get(lista)
                .then(response => {
                    this.carros = response.data
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
        carregarImagem(e){
            this.arquivoImagem = e.target.files
        }, 
        salvar(){

            let formData = new FormData();
            formData.append('modelo_id', this.idmodelo)
            formData.append('placa', this.placa)
            formData.append('disponivel', this.disponibilidade)
            formData.append('km', this.kilometragem)
            axios.post(this.$store.state.urlBases.urlCarros, formData)
                .then(response => {
                    this.transacaoStatus = 'Adicionado'
                    this.transacaoDetalhes = {mensagem: 'ID do registro: '+response.data.id} 
                })
                .catch(errors => {
                    this.transacaoStatus = 'Erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                })
                this.carregarLista()
        }
    },
    mounted(){
        this.carregarLista()
    }

}
</script>