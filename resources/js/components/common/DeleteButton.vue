<template>
    <button v-on:click="openDeleteModal()" type="button" class="btn btn-danger rounded"><i class="cil-trash"></i></button>
</template>

<script>
    import DeleteModal from './DeleteModal'

    export default {
        name: 'DeleteButton',
        props: {
            confirmDelayInSeconds: {
                type: Number,
                required: false,
                default: 5
            },
            route: {
                type: String,
                required: true
            }
        },
        methods: {
            openDeleteModal() {
                this.$modal.show(DeleteModal, {confirmDelayInSeconds: this.confirmDelayInSeconds, route: this.route}, {adaptive: true})
            }
        }
    }
</script>

<style scoped>

</style>
