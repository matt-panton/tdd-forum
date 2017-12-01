<script>
import Replies from '../components/Replies'
import SubscribeButton from '../components/SubscribeButton'

export default {
    data() {
        return {
            repliesCount: this.initialThread.replies_count,
            thread: this.initialThread,
            editedThread: {
                title: this.initialThread.title,
                body: this.initialThread.body
            },
            editing: false,
            saving: false
        }
    },

    props: ['initialThread'],

    methods: {
        handleRepliesLoaded(data) {
            this.repliesCount = data.total
        },
        toggleLock() {
            let method = this.thread.locked ? 'delete' : 'post'
            axios[method](`/locked-threads/${this.thread.slug}`).then(response => {
                this.thread.locked = !this.thread.locked
            })
        },
        save() {
            this.saving = true
            let data = {title: this.editedThread.title, body: this.editedThread.body}
            axios.patch(window.location.pathname, data).then(response => {
                this.saving = false
                this.editing = false
                this.syncEditedThread()
                flash('Your thread has been updated.')
            })
        },
        syncEditedThread() {
            this.$set(this.thread, 'title', this.editedThread.title)
            this.$set(this.thread, 'body', this.editedThread.body)
        }
    },

    components: {
        Replies,
        SubscribeButton
    }
}
</script>
