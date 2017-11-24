require('./bootstrap')

import Vue from 'vue'

Vue.prototype.authorize = (handler) => {
    let user = window.App.user

    return user ? handler(user) : false
}

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
