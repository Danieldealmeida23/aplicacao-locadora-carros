/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex'

/* importação e configuração do Vuex */

export const store = createStore({
    state: {
        item: {},
        transacao: {
            status: '',
            mensagem: ''
        }
    }
  })




/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({
    store
});
app.use(store)
app.config.globalProperties.$filters = {
    formataDataTempo(d){
        if(!d){ return ''}
        d = d.split('T')
        let data = d[0]
        let tempo = d[1].split('.')[0]
        data = new Date(data).toLocaleDateString('pt-BR');
        return data + ' as: ' + tempo
        
    }
}


import ExampleComponent from './components/ExampleComponent.vue';
import LoginComponent from './components/Login.vue';
import HomeComponent from './components/Home.vue';
import MarcasComponent from './components/Marcas.vue';
import InputContainer from './components/InputContainer.vue';
import TableComponent from './components/Table.vue';
import CardComponent from './components/Card.vue';
import ModalComponent from './components/Modal.vue';
import AlertComponent from './components/Alert.vue';
import PaginateComponent from './components/Paginate.vue';

app.component('example-component', ExampleComponent);
app.component('login-component', LoginComponent);
app.component('home-component', HomeComponent);
app.component('marcas-component', MarcasComponent);
app.component('input-container-component', InputContainer);
app.component('table-component', TableComponent);
app.component('card-component', CardComponent);
app.component('modal-component', ModalComponent);
app.component('alert-component', AlertComponent);
app.component('paginate-component', PaginateComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
