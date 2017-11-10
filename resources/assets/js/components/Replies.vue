<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <paginator :data-set="dataSet" @change="fetchItems"></paginator>

        <new-reply @created="add"></new-reply>
    </div>
</template>


<script>
import Reply from './Reply'
import NewReply from './NewReply'
import Paginator from './Paginator'
import collection from '../mixins/collection'

export default {
    data() {
        return {
            dataSet: false
        }
    },

    methods: {
        fetchItems(page) {
            axios.get(this.url(page)).then(this.refresh)
        },
        refresh(response) {
            this.items = response.data.data
            this.dataSet = response.data

            this.$emit('loaded', response.data)
            window.scrollTo(0, 0)
        },
        url(page) {
            if (!page) {
                let query = location.search.match(/page=(\d+)/)
                page = query ? query[1] : 1
            }

            let path = `${window.location.pathname}/replies`
            return page > 1 ? `${path}?page=${page}` : path
        }
    },

    created() {
        this.fetchItems()
    },

    components: {
        Reply,
        NewReply,
        Paginator
    },

    mixins: [collection]
}
</script>
