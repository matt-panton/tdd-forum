<script>
import Replies from '../components/Replies'

export default {
    data() {
        return {
            repliesCount: this.initialRepliesCount
        }
    },

    props: ['initialRepliesCount'],

    methods: {
        handleRepliesLoaded(data) {
            this.repliesCount = data.total
        }
    },

    components: {
        Replies
    }
}
</script>
