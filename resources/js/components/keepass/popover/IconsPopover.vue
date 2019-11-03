<template>
    <popover :name="popoverName" ref="popover">
        <div class="container">
            <div class="row">
                <div class="col-10">
                    <button v-on:click="saveIcon(null)" type="button" class="btn btn-light btn-block">Default</button>
                </div>
                <div class="col-2">
                    <button v-on:click="toggleInput" type="button" class="btn btn-ghost-light"><i :class="[showAddIconInput ? 'cui-minus' : 'cui-plus']"></i></button>
                </div>
            </div>
            <div v-if="showAddIconInput" class="row">
                <div class="col-10">
                    <input type="file" class="form-control-file form-control-sm" accept="image/png" @change="setIcon">
                </div>
                <div class="col-2">
                    <button v-if="file" type="button" v-on:click="storeIcon" class="btn btn-sm btn-success"><i class="cui-save"></i></button>
                </div>
            </div>
            <div class="row">
                <div v-for="(icon, i) in icons" :key="i" v-on:click="saveIcon(icon)" class="col-2 text-center handHover" :class="[keepass.icon_id === icon.id ? 'border border-danger' : '']">
                    <img :src="'/storage/'+icon.path" :alt="icon.filename" height="20" width="20">
                </div>
            </div>
        </div>
    </popover>
</template>

<script>
    import {EventBus} from './../../../eventBus.js'

    export default {
        name: 'IconsPopover',
        props: {
            icons: {
                type: Array,
                required: false,
                default: []
            },
            keepass: {
                type: Object,
                required: true
            },
            popoverName: {
                type: String,
                required: true
            },
            saveRoute: {
                type: String,
                required: true
            },
        },
        methods: {
            saveIcon(icon) {
                let keepassComputed = JSON.parse(JSON.stringify(this.keepass))
                keepassComputed.icon_id = icon ? icon.id : null
                if (keepassComputed.id) {
                    axios.post(this.saveRoute, {keepass: keepassComputed}).then(res => {
                        if (res.data.keepass) {
                            this.$emit('icon-changed', res.data.keepass)
                            this.$notify({title: 'Success', text: 'Icon has been saved !', type: 'success'})
                            if (this.keepass.is_folder) {
                                this.keepass.icon_id = res.data.keepass.icon_id
                                this.keepass.icon = res.data.keepass.icon
                            } else {
                                EventBus.$emit('keepass-saved', res.data.keepass, false)
                            }
                        }
                    })
                        .catch(response => this.$notify({title: 'Error', text: response.status !== 500 ? response.data.message : 'Internal error.', type: 'error'}))
                        .finally(() => this.$refs.popover.visible = false)
                } else {
                    keepassComputed.icon = icon
                    this.$emit('icon-changed', keepassComputed)
                    this.$refs.popover.visible = false
                }
            },
            setIcon(event) {
                if (event.target.files.length) {
                    this.file = event.target.files[0]
                } else {
                    this.file = null
                }
            },
            storeIcon() {
                if (this.file) {
                    let formData = new FormData()
                    formData.append('icon', this.file, this.file.name);
                    axios.post('/icon/add', formData, {headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(res => {
                        if (res.data.icon) {
                            this.icons.push(res.data.icon)
                            this.$notify({title: 'Success', text: 'Icon has been created !', type: 'success'})
                        }
                    }).catch(res => {
                        this.$notify({title: 'Error', text: res.response.status !== 500 ? res.response.data.message : 'Internal error.', type: 'error'})
                    }).finally(() => this.toggleInput())
                }
            },
            toggleInput() {
                this.showAddIconInput = !this.showAddIconInput
                if (!this.showAddIconInput) this.file = null
            }
        },
        mounted() {},
        data() {
            return {
                file: null,
                showAddIconInput: false
            }
        }
    }
</script>

<style scoped>
    div[data-popover$="Icon"] {
        background: #444;
        color: #f9f9f9;
        left: calc(50% - 200px) !important;
        line-height: 2.5;
        max-height: 500px;
        max-width: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 5px;
        top: 30px !important;
        width: 400px !important;
    }

    @media (max-width: 992px) {
        div[data-popover$="Icon"] {
            left: 0 !important;
        }
    }
</style>
