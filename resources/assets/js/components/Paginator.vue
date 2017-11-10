<template>
    <ul class="pagination" v-if="shouldPaginate">
        <li v-if="dataSet.prev_page_url">
            <a href="#" aria-label="Previous" @click.prevent="page--">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <li v-for="i in dataSet.last_page" :class="{'active': i === page}">
            <a :href="pagePath(i)" @click.prevent="page = i">{{ i }}</a>
        </li>
        <li v-if="dataSet.next_page_url">
            <a href="#" aria-label="Next" @click.prevent="page++">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</template>


<script>
export default {
    data() {
        return {
            page: 1
        }
    },

    computed: {
        shouldPaginate() {
            return !! (this.dataSet.prev_page_url || this.dataSet.next_page_url)
        }
    },

    props: ['dataSet'],

    watch: {
        dataSet(newVal) {
            this.page = newVal.current_page
        },
        page(newVal) {
            this.$emit('change', newVal)
            history.pushState(null, null, `?page=${newVal}`)
        }
    },

    methods: {
        pagePath(page){
            return `${this.dataSet.path}?page=${page}`
        }
    }
}
</script>
