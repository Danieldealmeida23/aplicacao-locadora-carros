<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col"  v-for="t, key in titulos" :key="key" class="text-uppercase">{{ t }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj in dados" :key="obj.id">
                    <td v-for="valor, chave in obj" :key="chave" >
                        <span v-if="titulos.includes(chave)">
                            <span v-if="chave == 'imagem'">
                                <img :src="caminho+valor" width="30" height="30">
                            </span>
                            <span v-else>
                                {{ valor }}
                            </span>
                        </span>
                    </td>
                </tr>
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
    props: ['dados', 'titulos']
}
</script>
