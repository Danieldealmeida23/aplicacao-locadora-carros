<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col"  v-for="t, key in titulos" :key="key">{{ t.titulo }}</th>
                    <th v-if="visualizar || atualizar || remover"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                    <td v-for="valor, chaveValor in obj" :key="chaveValor">
                        <span v-if="titulos[chaveValor].tipo == 'imagem'"><img :src="caminho+valor" width="30" height="30" v-if="valor != undefined"></span>
                        <span v-else-if="titulos[chaveValor].tipo == 'data'">{{ valor }}</span>
                        <span v-else-if="titulos[chaveValor].tipo == 'texto'">{{ valor }}</span>
                    </td>
                    <td v-if="visualizar || atualizar || remover">
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" :data-bs-toggle="visualizar.dataBsToggle" :data-bs-target="visualizar.dataBsTarget" @click="setStore(obj)">Visualizar</button>
                        <button v-if="atualizar.visivel" class="btn btn-outline-warning btn-sm">Atualizar</button>
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
    data: function(){
        return {
            caminho: '/storage/'
        }
    },
    props: ['dados', 'titulos', 'atualizar', 'visualizar'],
    methods: {
        setStore(obj){
            this.$store.state.transacao.status = ''
            this.$store.state.transacao.mensagem = ''
            this.$store.state.item = obj
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
        }
    },
    mounted() {
    },
}
</script>
