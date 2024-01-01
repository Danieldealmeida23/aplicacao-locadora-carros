<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component titulo="modelos">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID: " id="inputId" idHelp="idHelp" texto-ajuda="Informe o ID do modelo">
                                    <input type="number" id="inputId" aria-describedby="idHelp" class="form-control" v-model="busca.id">
                                </input-container-component>
                            </div>

                            <div class="col mb-3">
                                <input-container-component titulo="Nome do modelo: " id="inputNome" idHelp="nomeHelp" texto-ajuda="Informe o nome do modelo">
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
                        <table-component v-if="modelos.data"
                        :dados="modelos.data" 
                        :titulos="
                        {
                            id: {titulo: 'ID', tipo: 'texto'},
                            nome: {titulo: 'Nome', tipo: 'texto'},
                            created_at: {titulo: 'Criado em', tipo: 'data'}
                        }" 
                        :visualizar="{dataBsTarget: '#modalmodelosVisualizar',dataBsToggle: 'modal',visivel: true}" 
                        :atualizar="{dataBsTarget: '#modalmodelosAtualizar',dataBsToggle: 'modal',visivel: true}" 
                        
                        ></table-component>
                    </template>
                    <template v-slot:rodape>
                        <paginate-component>
                            <li v-for="l, key in modelos.links" :key="key" :class="l.active ? 'page-item active': 'page-item'" @click="paginacao(l)">
                                <a class="page-link" v-if="l.label == 'pagination.previous'">Anterior</a>  
                                <a class="page-link" v-else-if="l.label == 'pagination.next'">Proxima</a>
                                <a class="page-link" v-else>{{l.label}}</a> 
                            </li>
                        </paginate-component>
                        <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#modalmodelo" @click="carregarMarcas">Adicionar</button>
                    </template>
                </card-component>


                <!-- Início do Modal de inclusão de modelo -->
                <modal-component titulo="Adicionar modelo" id="modalmodelo">

                    <template v-slot:alertas>
                        <alert-component tipo="success" :detalhes="transacaoDetalhes" v-if="transacaoStatus == 'Adicionado'" titulo="Cadastro realizado com sucesso !"></alert-component>
                        <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar o modelo !" v-if=" transacaoStatus === 'Erro' "></alert-component>
                    </template>

                    <template v-slot:conteudo>
                        <div class="form-group">
                            <input-container-component titulo="Nome do modelo: " id="novoNome" idHelp="novoNomeHelp" texto-ajuda="Informe o nome do modelo">
                                <input type="text" id="novoNome" aria-describedby="novoNomeHelp" class="form-control" v-model="nomemodelo">
                            </input-container-component>
                        </div>
                        <div class="form-group">
                            <input-container-component titulo="Marca: " id="novaMarca" idHelp="novaMarcaHelp" texto-ajuda="Informe o nome da marca">
                                <select id="novaMarca" class="form-control" v-model="idmarca">
                                    <option value="">Selecione a Marca </option>
                                    <option v-for="marca in marcas" :value="marca.id" :key="marca.id">{{ marca.nome}}</option>
                                </select>
                            </input-container-component>
                        </div>

                        <div class="form-group">
                            <input-container-component titulo="Imagem: " id="novoImagem" idHelp="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG !" @change="carregarImagem($event)">
                                <input type="file" id="novoImagem" aria-describedby="novoImagemHelp" class="form-control-file">
                            </input-container-component>
                        </div>

                        <div class="form-group">
                            <input-container-component>
                                <input type="number" id="novoPortas" class="form-control" v-model="numeroportas" placeholder="Número de Portas">
                            </input-container-component>
                            <input-container-component>
                                <input type="number" id="novoLugares" class="form-control" v-model="numerolugares" placeholder="Número de lugares">
                            </input-container-component>

                            <input-container-component>
                                <select id="novoAirbags" class="form-control" v-model="airbag">
                                    <option value="">Tem Air-Bag? </option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>

                            </input-container-component>
                            <input-container-component>
                                <select id="novoABS" class="form-control" v-model="abs">
                                    <option value="">Tem ABS? </option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </input-container-component>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" @click="salvar" >Salvar</button>
                    </template>
                </modal-component>
                <!-- Final do Modal de inclusão do modelo -->


                <!-- Início do Modal de visualização do modelo -->
                <modal-component titulo="Visualizar modelo" id="modalmodelosVisualizar">

                    <template v-slot:alertas v-if="this.$store.state.transacao.status">
                        <alert-component tipo="success" :detalhes="this.$store.state.transacao" titulo="Transação realizada com sucesso" v-if="this.$store.state.transacao.status == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" :detalhes="this.$store.state.transacao" titulo="Erro" v-if="this.$store.state.transacao.status == 'erro'"></alert-component>
                    </template>

                    <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">

                        <input-container-component titulo="Nome do modelo">
                            <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                        </input-container-component>

                        <input-container-component >
                            <img  width="200px" :src="'http://localhost:8000/storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem != undefined"> 
                        </input-container-component>
                        <input-container-component titulo="Marca: " v-if="$store.state.item.marca">
                            <input type="text" 
                            class="form-control" 
                            :value="$store.state.item.marca.nome" 
                            disabled
                            :style="'background: url(http://localhost:8000/storage/'+$store.state.item.marca.imagem+') no-repeat left/2.5em; padding-left: 3.0em'">
                        </input-container-component>

                        <input-container-component titulo="Quantidade de portas: ">
                        <input type="text" class="form-control" :value="$store.state.item.numero_portas" disabled>
                        </input-container-component>

                        
                        <input-container-component titulo="Lugares:">
                        <input type="text" class="form-control" :value="$store.state.item.lugares" disabled>
                        </input-container-component>

                        <input-container-component titulo="ABS">
                        <input type="text" class="form-control" :value="$store.state.item.abs == 1 ? 'Sim' : 'Não'" disabled>
                        </input-container-component>

                        <input-container-component titulo="Air-Bags">
                        <input type="text" class="form-control" :value="$store.state.item.air_bag == 1 ? 'Sim' : 'Não'" disabled>
                        </input-container-component>



                    </template>
                    <template v-slot:rodape>
                        <button class="btn btn-outline-danger btn-sm float-left" @click="remover(obj)" v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </template>

                </modal-component>
                <!-- Final do Modal de visualização do modelo -->

                <!-- Início do Modal de atualização do modelo -->
                <modal-component titulo="Atualizar modelo" id="modalmodelosAtualizar">

                    <template v-slot:alertas>
                        <alert-component tipo="success" :detalhes="transacaoDetalhes" v-if="transacaoStatus == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" :detalhes="transacaoDetalhes" v-if=" transacaoStatus === 'erro' "></alert-component>
                    </template>

                    <template v-slot:conteudo>
                        <div class="form-group">
                            <input-container-component titulo="Nome do modelo: " id="atualizarNome" idHelp="atualizarNomeHelp" texto-ajuda="Informe o nome do modelo">
                                <input type="text" id="atualizarNome" aria-describedby="atualizarNomeHelp" class="form-control" v-model="this.$store.state.item.nome" :placeholder="this.$store.state.item.nome">
                            </input-container-component>
                        </div>

                        <div class="form-group">
                            <input-container-component titulo="Marca: " id="atualizarMarca" idHelp="atualizarMarcaHelp" texto-ajuda="Informe o nome da marca">
                                <select id="atualizarMarca" class="form-control" v-model="this.$store.state.item.marca_id" @click="carregarMarcas">
                                    <option value="">Selecione a Marca </option>
                                    <option v-for="marca in marcas" :value="marca.id" :key="marca.id">{{ marca.nome}}</option>
                                </select>
                            </input-container-component>
                        </div>

                        <div class="form-group">
                            <input-container-component titulo="Imagem: " id="atualizarImagem" idHelp="atualizarImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG !" @change="carregarImagem($event)">
                                <input type="file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" class="form-control-file" >
                            </input-container-component>
                        </div>

                        <div class="form-group">
                            <input-container-component titulo="Portas">
                                <input type="number" id="atualizarPortas" class="form-control" v-model="this.$store.state.item.numero_portas" placeholder="Número de Portas">
                            </input-container-component>
                            <input-container-component titulo="Lugares">
                                <input type="number" id="atualizarLugares" class="form-control" v-model="this.$store.state.item.lugares" placeholder="Número de lugares">
                            </input-container-component>

                            <input-container-component titulo="Air-Bags">
                                <select id="atualizarAirbags" class="form-control" v-model="this.$store.state.item.air_bag">
                                    <option value="">Tem Air-Bag? </option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>

                            </input-container-component>
                            <input-container-component titulo="ABS">
                                <select id="atualizarABS" class="form-control" v-model="this.$store.state.item.abs">
                                    <option value="">Tem ABS? </option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </input-container-component>
                        </div>


                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Fechar</button>
                        <button type="button" class="btn btn-primary" @click="atualizar">Atualizar</button>
                    </template>
                </modal-component>
                <!-- Final do Modal de atualização do modelo -->
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return{
            marcas: '',
            urlPaginacao: '',
            urlFiltro: '',
            nomemodelo: '',
            numeroportas: '',
            numerolugares: '',
            airbag: '',
            abs: '',
            idmarca: '',
            arquivoImagem: [],
            transacaoStatus: '',
            transacaoDetalhes: {},
            modelos: {data: []},
            busca: {
                id: '',
                nome: ''
            },
            
            
        }
    }, 
    methods: {
        carregarMarcas(){
            let lista = this.$store.state.urlBases.urlMarcasGet
            axios.get(lista)
                .then(response => {
                    this.marcas = response.data
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
        atualizar(){
            this.carregarMarcas()
            let formData = new FormData();
            formData.append('_method', 'patch')
            formData.append('nome', this.$store.state.item.nome)
            formData.append('marca_id', this.$store.state.item.marca_id)
            formData.append('numero_portas', this.$store.state.item.numero_portas)
            formData.append('lugares', this.$store.state.item.lugares)
            formData.append('air_bag', this.$store.state.item.air_bag)
            formData.append('abs', this.$store.state.item.abs)
            if(this.arquivoImagem[0]){
                formData.append('imagem', this.arquivoImagem[0])
            }
            let url = this.$store.state.urlBases.urlModelos + '/' + this.$store.state.item.id
            axios.post(url, formData)
                .then(response => {
                    this.transacaoStatus = 'sucesso'
                    this.transacaoDetalhes = {mensagem: 'Atualizado com sucesso'} 
                    this.arquivoImagem.value = ''
                    this.carregarLista()
                    console.log(response)
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {mensagem: errors.response.data.message, dados: errors.response.data.errors } 
                    console.log(errors)
                })
        },
        remover(){
            let confirmacao = confirm('Você tem certeza que deseja apagar o registro ?')
            if(!confirmacao){
                return false
            }
            let formData = new FormData()
            formData.append('_method', 'delete')

            let url = this.$store.state.urlBases.urlModelos + '/' + this.$store.state.item.id
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
            let lista = this.$store.state.urlBases.urlModelos + '?' + this.urlPaginacao + this.urlFiltro
            axios.get(lista)
                .then(response => {
                    this.modelos = response.data
                })
                .catch(errors => {

                })
        },
        carregarImagem(e){
            this.arquivoImagem = e.target.files
        }, 
        salvar(){

            let formData = new FormData();
            formData.append('nome', this.nomemodelo)
            formData.append('marca_id', this.idmarca)
            formData.append('numero_portas', this.numeroportas)
            formData.append('lugares', this.numerolugares)
            formData.append('abs', this.abs)
            formData.append('air_bag', this.airbag)
            formData.append('imagem', this.arquivoImagem[0])
            axios.post(this.$store.state.urlBases.urlModelos, formData)
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