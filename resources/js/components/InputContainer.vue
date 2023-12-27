<template>
    <div class="form-group">
        <label :for="id" class="form-label">{{ titulo }} </label>
        <slot></slot>
        <div class="form-text" :id="idHelp">{{ textoAjuda }}</div> 
    </div>
</template>
<script>
    export default{
        props: ['id', 'titulo', 'idHelp', 'textoAjuda']
    }
</script>
