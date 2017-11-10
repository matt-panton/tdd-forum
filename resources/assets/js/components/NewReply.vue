<template>
    <div>
        <div v-if="$root.signedIn">
            <div class="form-group">
                <textarea name="body" id="body" class="form-control" placeholder="Have something to say?" rows="5" v-model="body"></textarea>
            </div>
            <button type="button" class="btn btn-primary" @click="addReply">Post</button>
        </div>
        
        <p v-else>Please <a href="/login">sign in</a> to participate in this discussion.</p>
    </div>
</template>


<script>
export default {
    data() {
        return {
            body: ''        }
    },

    methods: {
        addReply() {
            axios.post(`${location.pathname}/replies`, {body: this.body}).then(response => {
                this.body = ''
                
                flash('Your reply has been posted.')

                this.$emit('created', response.data)
            })
        }
    }
}
</script>
