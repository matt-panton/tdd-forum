<template>
    <button type="button" class="btn btn-xs" :class="isFavourited ? 'btn-primary' : 'btn-default'" @click="toggle">
        <span class="glyphicon glyphicon-heart"></span>
        {{ count }}
    </button>
    </template>


<script>
export default {
    data() {
        return {
            count: this.reply.favourites_count,
            isFavourited: this.reply.is_favourited
        }
    },

    computed: {
        endpoint(){
            return `/replies/${this.reply.id}/favourite`
        }
    },

    props: ['reply'],

    methods: {
        toggle(){
            this.isFavourited ? this.destroy() : this.create()
        },
        create(){
            axios.post(this.endpoint)
            this.isFavourited = true
            this.count++
        },
        destroy(){
            axios.delete(this.endpoint)
            this.isFavourited = false
            this.count--
        }
    }
}
</script>
