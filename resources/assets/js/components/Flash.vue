<template>
    <div 
        v-show="show"
        class="alert alert-success"
        role="alert"
    >
        {{ body }}
    </div>
</template>


<script>
export default {
    data() {
        return {
            body: this.message,
            show: false
        }
    },

    props: ['message'],

    methods: {
        flash(message) {
            this.body = message
            this.show = true

            this.hide()
        },
        hide() {
            setTimeout(() => this.show = false, 3000)
        }
    },

    created() {
        if (this.message) {
            this.flash(this.message)
        } 

        window.events.$on('flash', message => this.flash(message))
    }
}
</script>


<style scoped>
.alert {
    position: fixed;
    right: 25px;
    bottom: 25px;
}
</style>
