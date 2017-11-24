<script>
import Replies from '../components/Replies'
import SubscribeButton from '../components/SubscribeButton'

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
        Replies,
        SubscribeButton
    }
}
</script>
