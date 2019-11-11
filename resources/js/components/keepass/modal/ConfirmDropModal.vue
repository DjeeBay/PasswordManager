<template>
    <div class="border-primary h100">
        <div class="card-header bg-primary">
            Confirm drop
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="text-center">
                        Move <b class="text-primary">{{keepass.title}}</b> in <b class="text-danger">/{{parentTitle}}</b> ?
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <div>
                <button v-on:click="save()" type="button" class="btn btn-primary rounded ml-2">Confirm</button>
                <button v-on:click="close()" type="button" class="btn btn-secondary rounded">Cancel</button>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: 'ConfirmDropModal',
        props: {
            keepass: {
                type: Object,
                required: true
            },
            parentTitle: {
                type: String|null,
                required: true
            },
            saveRoute: {
                type: String,
                required: true
            },
        },
        methods: {
            close() {
                this.$emit('close')
            },
            save() {
                axios.post(this.saveRoute, {keepass: this.keepass}).then(res => {
                    if (res.data.keepass) self.$notify({title: 'Success', text: 'The folder has been moved !', type: 'success'})
                })
            },
        },
        mounted() {},
        data() {}
    }
</script>

<style scoped>
    .h100 {height: 100%}
</style>
