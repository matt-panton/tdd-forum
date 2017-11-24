<template>
    <div>
        <div class="level">
            <img :src="avatar" width="50" class="mr-1">
            <h1>
                {{ user.name }}
                <small>Since {{ diffForHumans(user.created_at) }}</small>
            </h1>
        </div>

        <image-upload
            v-if="canUpdate"
            name="avatar"
            @read="handleRead"
            @change="handleChange"
        ></image-upload>
    </div>
</template>


<script>
import ImageUpload from './ImageUpload'

export default {
    data() {
        return {
            user: this.initialUser,
            avatar: this.initialUser.avatar
        }
    },

    props: ['initialUser'],

    computed: {
        canUpdate() {
            return this.authorize(user => this.user.id === user.id)
        }
    },

    methods: {
        diffForHumans(date) {
            return window.moment(date).fromNow()
        },
        handleRead(imageString) {
            this.avatar = imageString
        },
        handleChange(file) {
            this.persist(file)
        },
        persist(file) {
            let data = new FormData()
            data.append('avatar', file)

            axios.post(`/api/users/${this.user.name}/avatar`, data).then(response => {
                flash('Avatar uploaded.')
            })
        }
    },

    components: {
        ImageUpload
    }
}
</script>
