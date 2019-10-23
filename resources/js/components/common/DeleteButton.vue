<template>
    <button v-on:click="openDeleteModal()" type="button" class="btn btn-danger rounded"><i class="cui-trash"></i></button>
</template>

<script>
    import DeleteModal from './DeleteModal'

    export default {
        name: 'DeleteButton',
        props: {
            route: {
                type: String,
                required: true
            }
        },
        methods: {
            openDeleteModal() {
                this.$modal.show(DeleteModal, {route: this.route}, {adaptive: true})
            }
        }
    }
</script>

<style scoped>

</style>
