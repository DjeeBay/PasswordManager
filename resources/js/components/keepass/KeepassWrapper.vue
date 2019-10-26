<template>
    <div>
        <div id="btnGroup" class="mb-2">
            <div>
                <button v-on:click="openAddFolderModal()" type="button" class="btn btn-primary rounded"><i class="cui-plus"></i> <i class="cui-folder"></i></button>
            </div>
            <div>
                <button v-on:click="openEditModal({is_folder: 0, parent_id: selection.length ? selection[0].id : null})" type="button" class="btn rounded" :class="[!selection.length ? 'btn-secondary' : 'btn-success']" :disabled="!selection.length"><i class="cui-plus"></i> <i class="cui-file"></i></button>
            </div>
            <div>
                <button v-on:click="openDeleteFolderModal" type="button" class="btn rounded" :class="[!selection.length ? 'btn-secondary' : 'btn-danger']" :disabled="!selection.length"><i class="cui-minus"></i> <i class="cui-trash"></i></button>
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
                    :search="search"
                    :sort="sortTreeView"
                    :labels="{'search.placeholder': 'Search (min. 3 char.)'}"
                ></TreeView>
            </div>
            <div class="col-md-8">
                <div v-if="selection.length" class="form-inline mb-2">
                    <input type="text" v-model="selection[0].title" class="w-50 form-control form-control-sm" :class="[selection[0].title ? 'is-valid' : 'is-invalid']">
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
                            <template v-for="keepass in selection[0].children" v-if="!keepass.is_folder">
                                <tr>
                                    <th colspan="4" class="text-center">
                                        <span v-on:click="openEditModal(keepass)" class="badge badge-blue handHover">{{keepass.title}}</span>
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
    import AddKeepassFolderModal from "./modal/AddKeepassFolderModal";
    import DeleteModal from "../common/DeleteModal";

    export default {
        name: 'KeepassWrapper',
        components: {TreeView},
        props: {
            categoryId: {
                type: Number,
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
            addFolder(keepass) {
                keepass.children = []
                if (this.selection && this.selection.length) {
                    this.selection[0].children.push(keepass)
                } else {
                    this.model.push(keepass)
                }
            },
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
            findParent(through, parentID) {
                for (let i = 0; i < through.length; i++) {
                    if (through[i].id === parentID) {
                        return through[i]
                    } else if (through[i].children && through[i].children.length) {
                        let parent = this.findParent(through[i].children, parentID)
                        if (parent) {
                            return parent
                        }
                    }
                }
            },
            getParents() {
                //TODO it works, but it's ugly...
                let parents = []
                if (!this.selection || !this.selection.length) {
                    return parents
                }
                let parentID = this.selection[0].parent_id
                let allParentsFound = false
                while(!allParentsFound) {
                    let parentFound = this.findParent(this.model, parentID)
                    if (parentFound) {
                        parents.push(parentFound)
                        if (!parentFound.parent_id) {
                            allParentsFound = true
                        } else {
                            parentID = parentFound.parent_id
                        }

                    } else {
                        allParentsFound = true
                    }
                }

                return parents.reverse()
            },
            getPath() {
                let path = '/'
                if (!this.selection || !this.selection.length) {
                    return path
                }
                if (!this.selection[0].parent_id) {
                    return path+this.selection[0].title+'/'
                }
                let parents = this.getParents()
                for (let i = 0; i < parents.length; i++) {
                    path += parents[i].title+'/'
                }

                return path+this.selection[0].title+'/'
            },
            hideEmptyItems() {
                //TODO find a better way to hide those items
                setTimeout(() => {
                    let items = document.getElementsByClassName('item')
                    for (let i = 0; i < items.length; i++) {
                        if (!items[i].innerHTML) {
                            items[i].style.display = 'none'
                        }
                    }
                }, 50)
            },
            manageFolderIcons() {
                //TODO bosket can probably manage that
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
            openAddFolderModal() {
                let props = {
                    categoryId: this.categoryId,
                    parentId: this.selection && this.selection.length ? this.selection[0].id : null,
                    path: this.getPath(),
                    saveRoute: this.saveRoute
                }
                this.$modal.show(AddKeepassFolderModal, props, {adaptive: true, height: 'auto'})
            },
            openDeleteFolderModal() {
                if (this.selection && this.selection.length) {
                    this.$modal.show(DeleteModal, {
                        bodyText: 'It will delete the folder '+this.selection[0].title+' and all its children.',
                        xhrData: {keepass: this.selection[0]},
                        route: '/keepass/'+this.categoryId+'/delete/'+this.selection[0].id
                    }, {adaptive: true})
                }
            },
            openEditModal(keepass) {
                if (keepass && keepass.parent_id) {
                    let props = {
                        deleteRoute: keepass.id ? '/keepass/'+this.categoryId+'/delete/'+keepass.id : '',
                        keepass: keepass,
                        saveRoute: this.saveRoute,
                    }
                    this.$modal.show(EditKeepassModal, props, {adaptive: true, height: 'auto'})
                }
            },
            removeFolder(keepass) {
                console.log(keepass)
                if (!keepass.parent_id) {
                    let index = this.model.findIndex(k => k.id === keepass.id)
                    if (index !== -1) {
                        this.model.splice(index, 1)
                        this.$notify({title: 'Success', text: 'Folder has been deleted !', type: 'success'})
                        this.selection = []
                    }
                } else {
                    let parent = this.findParent(this.model, keepass.parent_id)
                    if (parent) {
                        let index = parent.children.findIndex(k => k.id === keepass.id)
                        if (index !== -1) {
                            parent.children.splice(index, 1)
                            this.$notify({title: 'Success', text: 'Folder has been deleted !', type: 'success'})
                            this.selection = []
                        }
                    }
                }
            },
            saveFolderTitle() {
                if (this.selection && this.selection.length) {
                    if (this.selection[0].title) {
                        axios.post(this.saveRoute, {keepass: this.selection[0]}).then(res => {
                            if (res.data.keepass) {
                                this.$notify({title: 'Success', text: 'Title has been updated !', type: 'success'})
                            }
                        }).catch(res => this.$notify({title: 'Error', text: res.response.status !== 500 ? res.response.data.message : 'Internal error.', type: 'error'}))
                    } else {
                        this.$notify({title: 'Warning', text: 'Title is required !', type: 'warn'})
                    }
                }
            },
            search(input) {
                if (input.length < 3) return item => false
                EventBus.$emit('keepass-search')
                return item => {
                    return item.title.match(new RegExp(`.*${ input }.*`, 'gi'))
                        || (item.login && item.login.match(new RegExp(`.*${ input }.*`, 'gi')))
                        || (item.url && item.url.match(new RegExp(`.*${ input }.*`, 'gi')))
                        || (item.notes && item.notes.match(new RegExp(`.*${ input }.*`, 'gi')))
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
                        if (first > second) {
                            comparison = -1
                        } else if (first < second) {
                            comparison = 1
                        }

                        return comparison
                    }
                })
            },
            sortChildrenByTitle() {
                if (this.selection && this.selection.length && this.selection[0].children && this.selection[0].children.length) {
                    this.selection[0].children = this.sortByProperty(this.selection[0].children, 'title')
                }
            },
            sortTreeView(a, b) {
                const sameChildrenState = 'children' in a === 'children' in b
                if (sameChildrenState) {
                    return a.title.localeCompare(b.title)
                } else if ('children' in a) {
                    return -1
                } else {
                    return 1
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
            EventBus.$on('keepass-search', this.hideEmptyItems)
            EventBus.$on('keepass-saved', (keepass, isNew) => this.update(keepass, isNew))
            EventBus.$on('keepass-deleted', keepass => this.delete(keepass))
            EventBus.$on('keepass-folder-created', keepass => this.addFolder(keepass))
            EventBus.$on('data-deleted', (res, xhrData) => this.removeFolder(xhrData.keepass))
            this.model = JSON.parse(JSON.stringify(this.items))

            let btnGroup = document.getElementById('btnGroup')
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 100) {
                    btnGroup.classList.add('sticked')
                } else {
                    btnGroup.classList.remove('sticked')
                }
            })

            let searchInput = document.getElementsByClassName('search')
            if (searchInput && searchInput.length) {
                searchInput[0].classList.add('form-control')
            }
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
                    return null
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

    .TreeView {
        max-height: 100vh;
        overflow-y: auto;
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
