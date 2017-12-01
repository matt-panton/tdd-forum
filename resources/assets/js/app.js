require('./bootstrap')

import Vue from 'vue'
import InstantSearch from 'vue-instantsearch'
import authorizations from './authorizations'

Vue.use(InstantSearch)

Vue.prototype.authorize = (...params) => {
    if (!window.App.signedIn) return false

    if (typeof params[0] === 'string') {
        return authorizations[params[0]](params[1])
    }

    return params[0](window.App.user)
}

Vue.prototype.signedIn = window.App.signedIn

import Flash from './components/Flash.vue'
import ThreadView from './pages/Thread.vue'
import UserNotifications from './components/UserNotifications.vue'
import AvatarForm from './components/AvatarForm.vue'

const app = new Vue({
    data: window.App,

    el: '#app',

    components: {
        Flash,
        ThreadView,
        UserNotifications,
        AvatarForm
    }
})
