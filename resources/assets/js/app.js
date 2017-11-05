require('./bootstrap')

import Vue from 'vue'

import Flash from './components/Flash.vue'

const app = new Vue({
    el: '#app',

    components: {
        Flash
    }
})
