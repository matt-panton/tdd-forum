<template>
    <div class="panel panel-default" :id="`reply-${reply.id}`">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="`/profile/${reply.user.name}`">{{ reply.user.name }}</a>
                     said {{ diffForHumans(reply.created_at) }}:
                 </h5>
                
                <div v-if="$root.signedIn">
                    <favourite :reply="reply"></favourite>
                </div>
            </div>
        </div>

        <form @submit.prevent="save()">
            <div class="panel-body">
                <div v-if="editing">
                    <textarea class="form-control" rows="5" v-model="reply.body" required></textarea>
                </div>
                <div v-else v-html="reply.formatted_body"></div>
            </div>
            
            <div class="panel-footer" v-if="canUpdate()">
                <div class="btn-group" v-if="editing">
                    <button type="button" class="btn btn-default btn-xs" @click="resetEditing()">Cancel</button>
                    <button type="submit" class="btn btn-success btn-xs" :disabled="saving">{{ saving ? 'Saving' : 'Save' }}</button>
                </div>
                <div class="btn-group" v-else>
                    <button type="button" class="btn btn-default btn-xs" @click="startEditing()">Edit</button>
                    <button type="button" class="btn btn-danger btn-xs" @click="destroy()">Delete</button>
                </div>
            </div>
        </form>
    </div>
</template>


<script>
import Favourite from './Favourite'

export default {
    data() {
        return {
            saving: false,
            editing: false,
            reply: this.data
        }
    },

    props: ['data'],

    methods: {
        diffForHumans(date) {
            return window.moment(date).fromNow()
        },
        startEditing(){
            this.editing = true
        },
        resetEditing(){
            this.editing = false
        },
        save(){
            this.saving = true
            axios.patch(`/replies/${this.reply.id}`, {body: this.reply.body})
                .then(({data}) => {
                    this.reply.body = data.body
                    this.reply.formatted_body = data.formatted_body
                    flash('Your reply has been updated.')
                    this.stopEditing()
                })
                .catch(error => {
                    flash(error.response.data.message, 'danger')
                    this.stopEditing()
                })
        },
        destroy(){
            axios.delete(`/replies/${this.reply.id}`)
        
            this.$emit('deleted')
        },
        canUpdate() {
            return this.authorize(user => this.reply.user_id === user.id)
        },
        stopEditing()
        {
            this.saving = false
            this.editing = false
        }
    },

    components: {
        Favourite
    }
}
</script>
