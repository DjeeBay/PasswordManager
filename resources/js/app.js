/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import '@coreui/coreui';
import '@fortawesome/fontawesome-free/js/all.min'
import VModal from 'vue-js-modal'
import Notifications from 'vue-notification'
import Popover from 'vue-js-popover'
import VueGoodTablePlugin from 'vue-good-table'
import 'vue-good-table/dist/vue-good-table.css'

import Vue from 'vue'
import Vuex from 'vuex'
import 'es6-promise/auto'

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.use(Vuex)
Vue.use(VModal, { dynamic: true, injectModalsContainer: true, dialog: true })
Vue.use(Notifications)
Vue.use(Popover)
Vue.use(VueGoodTablePlugin)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
