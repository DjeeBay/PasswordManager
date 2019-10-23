<template>
    <div class="border-primary h100">
        <div class="card-header bg-primary">New folder</div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div><small>Path : <span class="text-danger">{{path+(keepass.title ? keepass.title : '')}}</span></small></div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" v-model="keepass.title" class="form-control" :class="[keepass.title ? 'is-valid' : 'is-invalid']">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <div>
                <button v-on:click="save()" type="button" class="btn btn-primary rounded ml-2">Save</button>
                <button v-on:click="close()" type="button" class="btn btn-secondary rounded">Cancel</button>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from './../../../eventBus'

    export default {
        name: 'AddKeepassFolderModal',
        props: {
            categoryId: {
                type: Number,
                required: true
            },
            parentId: {
                type: Number|null,
                required: true
            },
            path: {
                type: String,
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
                if (this.keepass.title) {
                    axios.post(this.saveRoute, {keepass: this.keepass}).then(res => {
                        if (res.data.keepass) {
                            this.$notify({title: 'Success', text: 'Folder has been created !', type: 'success'})
                            EventBus.$emit('keepass-folder-created', res.data.keepass)
                        }
                    }).finally(() => this.close())
                } else {
                    this.$notify({title: 'Warning', text: 'Please enter a title.', type: 'warn'})
                }
            },
        },
        mounted() {
            this.keepass.category_id = this.categoryId
            this.keepass.parent_id = this.parentId
        },
        data() {
            return {
                keepass: {
                    title: null,
                    category_id: null,
                    is_folder: 1,
                    parent_id: null
                },
            }
        }
    }
</script>

<style scoped>
    .h100 {height: 100%}
</style>
