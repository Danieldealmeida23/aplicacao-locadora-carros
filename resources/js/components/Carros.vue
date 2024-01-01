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
                                <input-container-component titulo="Nome do carro: " id="inputNome" idHelp="nomeHelp" texto-ajuda="Informe o nome do carro">
                                    <input type="text" id="inputNome" aria-describedby="nomeHelp" class="form-control" v-model="busca.nome">
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
                            id: {titulo: 'ID', tipo: 'texto'},
                            nome: {titulo: 'Nome', tipo: 'texto'},
                            imagem: {titulo: 'Imagem', tipo: 'imagem'},
                            created_at: {titulo: 'Criado em', tipo: 'data'}
                        }" 
                        :visualizar="{dataBsTarget: '#modalCarrosVisualizar',dataBsToggle: 'modal',visivel: true}" 
                        :atualizar="{dataBsTarget: '#modalCarrosAtualizar',dataBsToggle: 'modal',visivel: true}" 
                        
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
                        <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#modalCarro">Adicionar</button>
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
                            <input-container-component titulo="Nome do carro: " id="novoNome" idHelp="novoNomeHelp" texto-ajuda="Informe o nome do carro">
                                <input type="text" id="novoNome" aria-describedby="novoNomeHelp" class="form-control" v-model="nomeCarro">
                            </input-container-component>
                        </div>
                        <div class="form-group">
                            <input-container-component titulo="Imagem: " id="novoImagem" idHelp="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG !" @change="carregarImagem($event)">
                                <input type="file" id="novoImagem" aria-describedby="novoImagemHelp" class="form-control-file">
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

                    <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                        
                        <input-container-component titulo="ID">
                            <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                        </input-container-component>

                        <input-container-component titulo="Nome do carro">
                            <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                        </input-container-component>

                        <input-container-component titulo="Imagem">
                            <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem != undefined"> 
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
                            <input-container-component titulo="Nome do carro: " id="atualizarNome" idHelp="atualizarNomeHelp" texto-ajuda="Informe o nome do carro">
                                <input type="text" id="atualizarNome" aria-describedby="atualizarNomeHelp" class="form-control" v-model="this.$store.state.item.nome" :placeholder="this.$store.state.item.nome">
                            </input-container-component>
                        </div>
                        <div class="form-group">
                            <input-container-component titulo="Imagem: " id="atualizarImagem" idHelp="atualizarImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG !" @change="carregarImagem($event)">
                                <input type="file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" class="form-control-file" >
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
            urlBase: 'http://localhost:8000/api/v1/carro',
            urlPaginacao: '',
            urlFiltro: '',
            nomeCarro: '',
            arquivoImagem: [],
            transacaoStatus: '',
            transacaoDetalhes: {},
            carros: {data: []},
            busca: {
                id: '',
                nome: ''
            },
            
            
        }
    }, 
    methods: {
        atualizar(){
            let formData = new FormData();
            formData.append('_method', 'patch')
            formData.append('nome', this.$store.state.item.nome)
            if(this.arquivoImagem[0]){
                formData.append('imagem', this.arquivoImagem[0])
            }
            let url = this.urlBase + '/' + this.$store.state.item.id
            axios.post(url, formData)
                .then(response => {
                    this.transacaoStatus = 'sucesso'
                    this.transacaoDetalhes = {mensagem: 'Atualizado com sucesso'} 
                    this.arquivoImagem.value = ''
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                })
        },
        remover(){
            let confirmacao = confirm('Você tem certeza que deseja apagar o registro ?')
            if(!confirmacao){
                return false
            }
            let formData = new FormData()
            formData.append('_method', 'delete')

            let url = this.urlBase + '/' + this.$store.state.item.id
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
            let lista = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro
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
            formData.append('nome', this.nomecarro)
            formData.append('imagem', this.arquivoImagem[0])
            axios.post(this.urlBase, formData)
                .then(response => {
                    this.transacaoStatus = 'Adicionado'
                    this.transacaoDetalhes = {mensagem: 'ID do registro: '+response.data.id} 
                })
                .catch(errors => {
                    this.transacaoStatus = 'Erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                })
        }
    },
    mounted(){
        this.carregarLista()
    }

}
</script>