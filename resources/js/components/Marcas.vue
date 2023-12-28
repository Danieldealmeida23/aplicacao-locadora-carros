<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component titulo="Marcas">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID: " id="inputId" idHelp="idHelp" texto-ajuda="Informe o ID da marca">
                                    <input type="number" id="inputId" aria-describedby="idHelp" class="form-control">
                                </input-container-component>
                            </div>

                            <div class="col mb-3">
                                <input-container-component titulo="Nome da marca: " id="inputNome" idHelp="nomeHelp" texto-ajuda="Informe o nome da marca">
                                    <input type="text" id="inputNome" aria-describedby="nomeHelp" class="form-control">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Pesquisar</button>
                    </template>
                </card-component>

                <hr>

                                    <!-- Card de registros -->

                <card-component titulo="Tabela de Registros">
                    <template v-slot:conteudo>
                        <table-component :dados="marcas" :titulos="['id', 'nome', 'imagem']"></table-component>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#modalMarca">Adicionar</button>
                    </template>
                </card-component>

                <modal-component titulo="Adicionar marca" id="modalMarca">

                    <template v-slot:alertas>
                        <alert-component tipo="success" :detalhes="transacaoDetalhes" v-if="transacaoStatus == 'Adicionado'" titulo="Cadastro realizado com sucesso !"></alert-component>
                        <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca !" v-if=" transacaoStatus === 'Erro' "></alert-component>
                    </template>

                    <template v-slot:conteudo>
                        <div class="form-group">
                            <input-container-component titulo="Nome da marca: " id="novoNome" idHelp="novoNomeHelp" texto-ajuda="Informe o nome da marca">
                                <input type="text" id="novoNome" aria-describedby="novoNomeHelp" class="form-control" v-model="nomeMarca">
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
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

    export default {
        data: function() {
            return{
                urlBase: 'http://localhost:8000/api/v1/marca',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: {},
                marcas: []
            }
        }, 
        methods: {
            carregarLista(){
                axios.get(this.urlBase, this.config)
                    .then(response => {
                        this.marcas = response.data
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
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])
                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }
                console.log(this.urlBase, formData, config)
                axios.post(this.urlBase, formData, config)
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
        computed: {
                token(){
                    let token = document.cookie.split(";").find(indice => {
                        return indice.includes('token=')
                    })

                    return token.replace('token=','Bearer ')
                }
        },
        mounted(){
            this.carregarLista()
        }

    }
</script>