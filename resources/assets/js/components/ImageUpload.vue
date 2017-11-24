<template>
    <input type="file" accept="image/*" @change="handleChange">
</template>


<script>
export default {
    data() {
        return {

        }
    },

    methods: {
        handleChange(e) {
            if (e.target.files.length === 0) return

            let file = e.target.files[0]
            let reader = new FileReader

            reader.readAsDataURL(file)

            reader.onload = (e) => {
                this.$emit('read', e.target.result)
            }

            this.$emit('change', file)
        }
    }
}
</script>
