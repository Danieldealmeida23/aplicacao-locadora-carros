<template>
    <div> {{ dadosFiltrados }}
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col"  v-for="t, key in titulos" :key="key">{{ t.titulo }}</th>
                    <th v-if="visualizar || atualizar"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                    <td v-for="valor, chaveValor in obj" :key="chaveValor">
                        <span v-if="titulos[chaveValor].tipo == 'imagem'"><img :src="caminho+valor" width="30" height="30" v-if="valor != undefined"></span>
                        <span v-else-if="titulos[chaveValor].tipo == 'data'">{{ $filters.formataDataTempo(valor) }}</span>
                        <span v-else-if="titulos[chaveValor].tipo == 'texto' && titulos[chaveValor].titulo == 'Disponibilidade'">
                            <button :class="verificaDiponibilidade(valor)">
                                {{ valor == "1" ? "Disponível" : "Não disponível" }}
                            </button>
                        </span>
                        <span v-else-if="titulos[chaveValor].tipo == 'texto' &&  valor.nome ">{{ valor.nome}}</span>
                        <span v-else-if="titulos[chaveValor].tipo == 'texto' &&  valor.placa ">{{ valor.placa}}</span>
                        <span v-else-if="titulos[chaveValor].titulo == 'Status' &&  valor.data_final_realizado_periodo != null ">Entregue</span>
                        <span v-else-if="titulos[chaveValor].titulo == 'Status' && valor.data_final_realizado_periodo == null ">Pendente</span>
                        <span v-else-if="titulos[chaveValor].tipo == 'dataSistema'">{{ $filters.formataDataTempoSistema(valor) }}</span>
                        <span v-else-if="titulos[chaveValor].tipo == 'texto'">{{ valor }}</span>
                    </td>
                    <td v-if="visualizar || atualizar" :indice="0">
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" :data-bs-toggle="visualizar.dataBsToggle" :data-bs-target="visualizar.dataBsTarget" @click="setStore(chave)">Visualizar</button>
                        <button v-if="atualizar.visivel" class="btn btn-outline-warning btn-sm" :data-bs-toggle="atualizar.dataBsToggle" :data-bs-target="atualizar.dataBsTarget" @click="setStore(chave)">Atualizar</button>
                    </td>
                </tr>
                <!--
                <tr v-for="obj in dados" :key="obj.id">
                    <template v-for="valor, chave in obj" :key="chave">
                        <td v-if="titulos.includes(chave)"> 
                            <span v-if="chave == 'imagem'">
                                <img :src="caminho+valor" width="30" height="30">
                            </span>
                            <span v-else>
                                {{ valor }}
                            </span>
                        </td>
                    </template>
                </tr>
            -->
            </tbody>
        </table>
    </div>
</template>
    
<script>
export default {
    props: ['dados',  'titulos', 'atualizar', 'visualizar', 'pagina'],
    data: function(){
        return {
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
            let campos = Object.keys(this.titulos)
            let dadosFiltrados = []
            this.dados.map((item, chave) => {
                let itemFiltrado={}
                campos.forEach(campo => {
                    itemFiltrado[campo] = item[campo]
                })
            dadosFiltrados.push(itemFiltrado)
            })
            return dadosFiltrados
        },
        dadosFiltradosLocacao(){
            let campos = object.keys(this.titulos)
            let dadosFiltrados = []
            this.dados.map((item, chave) => {
                let itemFiltrado={}
                campos.forEach(campo => {
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

            return dadosFiltrados

        }
    },
    mounted() {
    },
}
</script>
