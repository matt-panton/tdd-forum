<template>
    <div 
        v-show="show"
        class="alert"
        :class="`alert-${level}`"
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
            show: false,
            level: this.type ? this.type : 'success'
        }
    },

    props: ['message', 'type'],

    methods: {
        flash(message, level) {
            this.body = message
            this.level = level
            this.show = true

            this.hide()
        },
        hide() {
            setTimeout(() => this.show = false, 3000)
        }
    },

    created() {
        if (this.message) {
            this.flash(this.message, this.level)
        } 

        window.events.$on('flash', ({message, level}) => {
            this.flash(message, level)
        })
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
