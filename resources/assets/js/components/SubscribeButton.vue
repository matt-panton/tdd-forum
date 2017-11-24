<template>
    <div>
        <button 
            type="button"
            class="btn"
            :class="active ? 'btn-primary' : 'btn-default'"
            @click="subscribe"
            :disabled="processing"
        >
            {{ text }}
        </button>
    </div>
</template>


<script>
export default {
    data() {
        return {
            active: this.initialActive,
            processing: false
        }
    },

    props: ['initialActive'],

    computed: {
        text() {
            return this.processing ? 'Working' : (this.active ? 'Subscribed' : 'Subscribe')
        }
    },

    methods: {
        subscribe() {
            let requestType = this.active ? 'delete' : 'post';

            this.processing = true;

            axios[requestType](`${location.pathname}/subscriptions`).then(() => {
                this.processing = false;
                this.active = !this.active;
            });
        }
    }
}
</script>
