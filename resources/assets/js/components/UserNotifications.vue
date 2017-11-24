<template>
    <li class="dropdown" v-if="notifications.length">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-bell"></span>
        </a>    

        <ul class="dropdown-menu">
            <li v-for="notification in notifications">
                <a :href="notification.data.link" @click="markAsRead(notification)">{{ notification.data.message }}</a>
            </li>
        </ul>
    </li>
</template>


<script>
export default {
    data() {
        return {
            notifications: []
        }
    },

    methods: {
        markAsRead(notification){
            axios.delete(`/profile/${this.$root.user.name}/notifications/${notification.id}`);    
        }
    },

    created() {
        axios.get(`/profile/${this.$root.user.name}/notifications`).then(response => {
            this.notifications = response.data;
        });
    }
}
</script>
