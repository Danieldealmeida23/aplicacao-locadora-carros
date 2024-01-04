<template>
    <div> 
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col"  v-for="t, key in titulos" :key="key"><span> {{ t.titulo }} </span></th>
                    <th v-if="visualizar || atualizar"></th>
                </tr>
            </thead>
            <tbody v-if="dadosFiltrados && dadosFiltradosInfo != ''">
                <tr v-for="obj, chave in dadosFiltradosInfo" :key="chave">
                    <td v-for="valor, chaveValor in obj" :key="chaveValor">
                            <span v-if="titulos[chaveValor].tipo == 'imagem'"><img :src="caminho+valor" width="30" height="30" v-if="valor != undefined"></span>
                            <span v-else-if="titulos[chaveValor].tipo == 'data'">{{ $filters.formataDataTempo(valor) }}</span>
                            <span v-else-if="titulos[chaveValor].tipo == 'dataSistema'">{{ $filters.formataDataTempoSistema(valor) }}</span>
                            <span v-else-if="titulos[chaveValor].tipo == 'texto' && titulos[chaveValor].titulo == 'Disponibilidade'">
                                <button :class="verificaDiponibilidade(valor)">
                                    {{ valor == "1" ? "Disponível" : "Não disponível" }}
                                </button>
                            </span>
                            <span v-else-if="titulos[chaveValor].tipo == 'texto' &&  valor.nome ">{{ valor.nome}}</span>
                            <span v-else-if="titulos[chaveValor].tipo == 'texto' &&  valor.placa ">{{ valor.placa}}</span>
                            <span v-else-if="titulos[chaveValor].tipo == 'texto'">{{ valor }}</span>
                    </td>
                    <td v-if="visualizar || atualizar" :indice="0">
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" :data-bs-toggle="visualizar.dataBsToggle" :data-bs-target="visualizar.dataBsTarget" @click="setStore(chave)">Visualizar</button>
                        <button v-if="atualizar.visivel" class="btn btn-outline-warning btn-sm" :data-bs-toggle="atualizar.dataBsToggle" :data-bs-target="atualizar.dataBsTarget" @click="setStore(chave)">Atualizar</button>
                    </td>
                </tr>
            </tbody>
            <tbody v-if="dadosFiltradosLocacoes && dadosFiltradosLocacaoInfo != ''">
                <tr v-for="obj, chave in dadosFiltradosLocacaoInfo" :key="chave">
                    <td v-for="valor, chaveValor in obj" :key="chaveValor">
                            <span v-if="titulos[chaveValor].tipo == 'data'">{{ $filters.formataDataTempo(valor) }}</span>
                            <span v-else-if="titulos[chaveValor].tipo == 'dataSistema'">{{ $filters.formataDataTempoSistema(valor) }}</span>
                            <span v-else-if="titulos[chaveValor].tipo == 'texto' && titulos[chaveValor].titulo == 'Cliente' &&  valor.nome">{{ valor.nome }}</span>
                            <span v-else-if="titulos[chaveValor].tipo == 'texto' && titulos[chaveValor].titulo == 'Veiculo-placa' && valor.placa">{{ valor.placa }}</span> 
                    </td>
                    <td v-if="visualizar || atualizar" :indice="0">
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" :data-bs-toggle="visualizar.dataBsToggle" :data-bs-target="visualizar.dataBsTarget" @click="setStore(chave)">Visualizar</button>
                        <button v-if="atualizar.visivel" class="btn btn-outline-warning btn-sm" :data-bs-toggle="atualizar.dataBsToggle" :data-bs-target="atualizar.dataBsTarget" @click="setStore(chave)">Atualizar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
    
<script>
export default {
    props: ['dados',  'titulos', 'atualizar', 'visualizar', 'pagina'],
    data: function(){
        return {
            campos: Object.keys(this.titulos),
            dadosFiltradosInfo: '',
            dadosFiltradosLocacaoInfo: '',
            caminho: 'http://localhost:8000/storage/'
        }
    },
    methods: {
        verificaDiponibilidade(valor){
            if(valor == "1"){
                return "btn btn-success"
            }else{
                return "btn btn-warning"
            }
        },
        setStore(indice){
            this.$store.state.transacao.status = ''
            this.$store.state.transacao.mensagem = ''
            this.$store.state.item = this.dados[indice]
        }
    },
    computed: {
        dadosFiltrados(){
            if(this.dados[0] != undefined){
                if(this.dados[0].data_inicio_periodo == undefined){
                    let dadosFiltrados = []
                    this.dados.map((item, chave) => {
                        let itemFiltrado={}
                        this.campos.forEach(campo => {
                            if(campo == 'cliente'){
                                itemFiltrado[campo] = item.cliente[campo]
                            }else if(campo == 'carro'){
                                itemFiltrado[campo] = item.carro[campo]
                            }else {
                                itemFiltrado[campo] = item[campo]
                            }
                        })
                    
                    dadosFiltrados.push(itemFiltrado)
                    })
                    this.dadosFiltradosInfo = dadosFiltrados
                    return true
                }else{
                    return false
                }

            }
            
        },
        dadosFiltradosLocacoes(){
            if(this.dados[0] != undefined){
                if(this.dados[0].data_inicio_periodo != undefined){
                    let dadosFiltradosLocacao = []
                    this.dados.map((item, chave) => {
                        let itemFiltradoLocacao={carro: {disponivel: ''}}
                        this.campos.forEach(campo => {
                            if(campo == 'cliente'){
                                itemFiltradoLocacao[campo] = item.cliente
                            }else if(campo == 'carro'){
                                itemFiltradoLocacao[campo] = item.carro
                            }else{
                                itemFiltradoLocacao[campo] = item[campo]
                            }
                        })
                        if(itemFiltradoLocacao.carro.disponivel == '1'){

                        }else{
                            dadosFiltradosLocacao.push(itemFiltradoLocacao)
                        }
                        })

                    this.dadosFiltradosLocacaoInfo = dadosFiltradosLocacao
                    return true
                }
            return false
            }
        }
    },
    mounted() {
    },
}
</script>
