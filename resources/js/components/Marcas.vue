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
                        <table-component></table-component>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary btn-sm float-right">Adicionar</button>
                    </template>
                </card-component>
            </div>
        </div>
    </div>
</template>

<script>

</script>