
window._ = require('lodash');

window.moment = require('moment');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.App.csrfToken;

try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap-sass');
} catch (e) {}

import Vue from 'vue'

window.events = new Vue()
window.flash = (message) => window.events.$emit('flash', message)
