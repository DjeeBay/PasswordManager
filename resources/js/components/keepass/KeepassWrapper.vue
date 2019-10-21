<template>
    <div>
        <div id="btnGroup" class="mb-2">
            <div>
                <button type="button" class="btn btn-primary rounded"><i class="cui-plus"></i> <i class="cui-folder"></i></button>
            </div>
            <div>
                <button v-on:click="openEditModal({is_folder: 0, parent_id: selection.length ? selection[0].id : null})" type="button" class="btn rounded" :class="[!selection.length ? 'btn-secondary' : 'btn-success']" :disabled="!selection.length"><i class="cui-plus"></i> <i class="cui-file"></i></button>
            </div>
            <div>
                <button type="button" class="btn rounded" :class="[!selection.length ? 'btn-secondary' : 'btn-danger']" :disabled="!selection.length"><i class="cui-minus"></i> <i class="cui-trash"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <TreeView
                    :model="model"
                    :display="display"
                    category="children"
                    :selection="selection"
                    :onSelect="onSelect"
                    :transition="transition"
                ></TreeView>
            </div>
            <div class="col-md-8">
                <div v-if="selection.length" class="form-inline mb-2 w-50">
                    <input type="text" v-model="selection[0].title" class="form-control form-control-sm" :class="[selection[0].title ? 'is-valid' : 'is-invalid']">
                    <button v-on:click="saveFolderTitle" type="button" class="btn btn-sm btn-primary ml-1">Edit</button>
                </div>
                <div v-if="selectionHasEntries()">
                    <div class="table-responsive table-sm">
                        <table class="table table-dark table-striped">
                            <thead>
                            <tr>
                                <th>Login</th>
                                <th>Password</th>
                                <th>URL</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="keepass in selection[0].children">
                                <tr>
                                    <th colspan="4" class="text-center">
                                        <span v-on:click="openEditModal(keepass)" class="badge badge-info handHover">{{keepass.title}}</span>
                                    </th>
                                </tr>
                                <tr>
                                    <td v-on:click="copy(keepass.login, 'Login')" class="handHover">{{keepass.login}}</td>
                                    <td v-on:click="copy(keepass.password, 'Password')" class="handHover"><i v-if="keepass.password">(length {{keepass.password.length}})</i></td>
                                    <td><a :href="keepass.url" target="_blank">{{keepass.url}}</a></td>
                                    <td><div class="notes">{{keepass.notes}}</div></td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-else class="text-center">
                    <i>No entries</i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from './../../eventBus'
    import {TreeView} from '@bosket/vue'
    import EditKeepassModal from './modal/EditKeepassModal'

    export default {
        name: 'KeepassWrapper',
        components: {TreeView},
        props: {
            deleteRoute: {
                type: String,
                required: true
            },
            items: {
                type: Array,
                required: false,
                default: []
            },
            saveRoute: {
                type: String,
                required: true
            },
        },
        methods: {
            copy(value, type) {
                if (value && value !== '<!---->') {
                    navigator.permissions.query({name: 'clipboard-write'}).then(res => {
                        if (res.state === 'granted' || res.state === 'prompt') {
                            navigator.clipboard.writeText(value).then(() => {
                                this.$notify({text: type+' copied !'})
                            }, () => {
                                this.$notify({text: type+' not copied !', type: 'error'})
                            });
                        } else {
                            let fakeInput = document.createElement('textarea')
                            document.body.appendChild(fakeInput)
                            fakeInput.value = value
                            fakeInput.select()
                            fakeInput.setSelectionRange(0, 99999)
                            document.execCommand('copy')
                            document.body.removeChild(fakeInput)
                            this.$notify({text: type+' copied !'})
                        }
                    });
                }
            },
            delete(keepass) {
                let index = this.selection[0].children.findIndex(k => k.id === keepass.id)
                if (index !== -1) {
                    this.selection[0].children.splice(index, 1)
                }
            },
            manageFolderIcons() {
                setTimeout(() => {
                    let itemsNotFolded = document.querySelectorAll('li.category:not(.folded)>.item a i')
                    let itemsFolded = document.querySelectorAll('li.category.folded>.item a i')
                    for (let i = 0; i < itemsNotFolded.length; i++) {
                        itemsNotFolded[i].classList.remove('cui-folder')
                        itemsNotFolded[i].classList.add('cui-folder-open')
                    }
                    for (let i = 0; i < itemsFolded.length; i++) {
                        itemsFolded[i].classList.remove('cui-folder-open')
                        itemsFolded[i].classList.add('cui-folder')
                    }
                }, 50)
            },
            openEditModal(keepass) {
                if (keepass && keepass.parent_id) {
                    let props = {
                        deleteRoute: this.deleteRoute,
                        keepass: keepass,
                        saveRoute: this.saveRoute,
                    }
                    this.$modal.show(EditKeepassModal, props, {adaptive: true, height: 'auto'})
                }
            },
            saveFolderTitle() {
                if (this.selection && this.selection.length) {
                    if (this.selection[0].title) {
                        axios.post(this.saveRoute, {keepass: this.selection[0]}).then(res => {
                            if (res.data.keepass) {
                                this.$notify({title: 'Success', text: 'Title has been updated !', type: 'success'})
                            }
                        })
                    } else {
                        this.$notify({title: 'Warning', text: 'Title is required !', type: 'warn'})
                    }
                }
            },
            selectionHasEntries() {
                if (!this.selection || !this.selection.length || !this.selection[0].children || !this.selection[0].children.length) {
                    return false
                }
                for (let i = 0; i < this.selection[0].children.length; i++) {
                    if (!this.selection[0].children[i].is_folder) {
                        return true
                    }
                }

                return false
            },
            sortByProperty(sortable, prop) {
                return sortable.sort((a, b) => {
                    let comparison = 0
                    if (a.hasOwnProperty(prop) && b.hasOwnProperty(prop)) {
                        let first = a.title.toUpperCase()
                        let second = b.title.toUpperCase()
                        if (first < second) {
                            comparison = -1
                        } else if (first > second) {
                            comparison = 1
                        }

                        return comparison
                    }
                })
            },
            sortChildrenByTitle() {
                if (this.selection && this.selection.length && this.selection[0].children && this.selection[0].children.length) {
                    this.selection[0].children = JSON.parse(JSON.stringify(this.sortByProperty(this.selection[0].children, 'title')))
                }
            },
            update(keepass, isNew) {
                if (isNew) {
                    this.selection[0].children.push(keepass)
                } else {
                    let index = this.selection[0].children.findIndex(k => k.id === keepass.id)
                    if (index !== -1) {
                        this.selection[0].children[index] = JSON.parse(JSON.stringify(keepass))
                        this.$forceUpdate()
                    }
                }

                this.sortChildrenByTitle()
            }
        },
        mounted() {
            EventBus.$on('keepass-saved', (keepass, isNew) => this.update(keepass, isNew))
            EventBus.$on('keepass-deleted', keepass => this.delete(keepass))
            this.model = JSON.parse(JSON.stringify(this.items))

            let btnGroup = document.getElementById('btnGroup')
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 100) {
                    btnGroup.classList.add('sticked')
                } else {
                    btnGroup.classList.remove('sticked')
                }
            })
        },
        data() {
            return {
                model: [],
                selection: [],
                onSelect: newSelection => {
                    this.selection = newSelection
                    this.manageFolderIcons()
                },
                transition: {
                    attrs: { appear: true },
                    props: { name: 'TreeViewTransition' }
                },
                display: (item, inputs) => {
                    if (item.is_folder) {
                        return <a><i class="cui-folder text-primary"></i> {item.title}</a>
                    }
                },
            }
        }
    }
</script>

<style scoped>
    #btnGroup {
        text-align: right;
    }

    #btnGroup div {
        display: inline-block;
    }

    .notes {
        max-height: 120px;
        overflow-y: auto;
        white-space: pre-wrap;
    }

    .sticked {
        position: fixed;
        right: 30px;
        top: 60px;
        z-index: 10;
    }

    th, td {
        max-width: 200px;
    }

    @media (min-width: 768px) {
        .sticked {
            right: 35px;
            top: 15px;
        }
    }

    @media (max-width: 768px) {
        .TreeView {
            margin-bottom: 15px;
            max-height: 400px;
            overflow-y: auto;
        }
    }
</style>
