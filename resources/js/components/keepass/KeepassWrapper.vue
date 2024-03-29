<template>
    <div>
        <div id="btnGroup" class="mb-2">
            <div class="floatLeftBtn">
                <button v-on:click="showTree = !showTree" type="button" class="btn btn-dark rounded border-white"><i :class="[showTree ? 'cil-minus' : 'cil-plus']"></i></button>
            </div>
            <div v-if="showLockDrag" class="floatLeftBtn">
                <button v-on:click="toggleLockDrag()" type="button" class="btn btn-blue rounded border-white"><i :class="[draggable && droppable ? 'cil-lock-unlocked' : 'cil-lock-locked']"></i></button>
            </div>
            <div>
                <button v-on:click="openAddFolderModal()" type="button" class="btn btn-primary rounded"><i class="cil-plus"></i> <i class="cil-folder"></i></button>
            </div>
            <div>
                <button v-on:click="openEditModal({is_folder: 0, parent_id: selection.length ? selection[0].id : null, icon_id: null, icon: null})" type="button" class="btn rounded" :class="[!selection.length ? 'btn-secondary' : 'btn-success']" :disabled="!selection.length"><i class="cil-plus"></i> <i class="cil-file"></i></button>
            </div>
            <div>
                <button v-on:click="openDeleteFolderModal" type="button" class="btn rounded" :class="[!selection.length ? 'btn-secondary' : 'btn-danger']" :disabled="!selection.length"><i class="cil-minus"></i> <i class="cil-trash"></i></button>
            </div>
        </div>
        <div class="row">
            <div v-if="showTree" class="col-md-3">
                <TreeView
                    class="border border-secondary"
                    :model="model"
                    :display="display"
                    category="children"
                    :dragndrop="dragndrop()"
                    :selection="selection"
                    :onSelect="onSelect"
                    :transition="transition"
                    :search="search"
                    :sort="sortTreeView"
                    :strategies="strategies"
                    :labels="{'search.placeholder': 'Search (min. 3 char.)'}"
                ></TreeView>
            </div>
            <div class="colTreeView" :class="[showTree ? 'col-md-9' : 'col-md-12']">
                <div v-if="selection.length" class="form-inline mb-2 position-relative">
                    <input type="text" v-model="selection[0].title" class="w-50 form-control form-control-sm" :class="[selection[0].title ? 'is-valid' : 'is-invalid']">
                    <button v-on:click="saveFolderTitle" type="button" class="btn btn-sm btn-primary ml-1">Save folder name</button>
                    <button v-popover:folderIcon.bottom type="button" class="btn btn-sm btn-warning ml-1"><i class="cil-smile"></i></button>
                    <icons-popover :icons="icons" :keepass="selection[0]" :popover-name="'folderIcon'" :save-route="saveRoute"></icons-popover>
                </div>
                <div v-if="selection.length" class="mb-1">
                    <button v-if="selectionHasEntries()" type="button" v-on:click="showSelection = !showSelection" class="btn btn-sm btn-secondary">Selection<span v-if="entriesSelected.length">&nbsp;({{entriesSelected.length}})</span></button>
                    <button type="button" v-on:click="copyFolderLink()" class="btn btn-sm btn-link"><i class="fa fa-link"></i> Copy folder link</button>
                    <button v-if="entriesSelected.length" type="button" v-on:click="entriesSelected = []" class="btn btn-sm btn-ghost-danger">Clear</button>
                    <button v-if="entriesSelected.length" type="button" v-on:click="pasteEntries" class="btn btn-sm btn-ghost-primary">Paste</button>
                    <button v-if="entriesSelected.length" type="button" v-on:click="addToFavorites" class="btn btn-sm btn-ghost-primary">Add to fav.</button>
                </div>
                <div v-if="selectionHasEntries()">
                    <div class="table-responsive table-sm">
                        <table class="table table-dark table-striped">
                            <thead>
                            <tr>
                                <th v-if="showSelection">
                                    <input type="checkbox" v-on:click="toggleAllEntriesSelection()" :checked="areAllEntriesSelected()" class="custom-checkbox">
                                </th>
                                <th>Title</th>
                                <th>Login</th>
                                <th>Password</th>
                                <th>URL</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="keepass in selection[0].children" v-if="!keepass.is_folder">
                                <tr>
                                    <td v-if="showSelection">
                                        <input type="checkbox" v-on:click="$event.target.checked ? entriesSelected.push(keepass) : entriesSelected.splice(entriesSelected.indexOf(keepass), 1)" :checked="entriesSelected.indexOf(keepass) !== -1" class="custom-checkbox">
                                    </td>
                                    <td>
                                        <button type="button" v-on:click="openEditModal(keepass)" class="btn btn-sm btn-blue"><img v-if="keepass.icon_id && keepass.icon" :src="'/storage/'+keepass.icon.path" :alt="keepass.icon.filename" height="16" width="16"> {{keepass.title}}</button>
                                    </td>
                                    <td v-on:click="copy(keepass.login, 'Login')" class="handHover">{{keepass.login}}</td>
                                    <td>
                                        <span v-on:click="copy(keepass.password, 'Password')" class="handHover"><i v-if="keepass.password">(length {{keepass.password.length}})</i></span>
                                        <span v-if="keepass.password && keepass.login" v-on:click="copy(keepass.login+':'+keepass.password, 'Login & Password')" class="handHover"><small class="text-warning">L:P</small></span>
                                    </td>
                                    <td>
                                        <a :href="getURL(keepass.url)" target="_blank">{{keepass.url && keepass.url.length > 25 ? keepass.url.substr(0, 24)+'&hellip;' : keepass.url}}</a>
                                        <span v-if="keepass.url" v-on:click="copy(keepass.url, 'URL')" class="handHover"><i class="cil-copy text-warning"></i></span>
                                    </td>
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
    import {array, tree} from '@bosket/tools'
    import EditKeepassModal from './modal/EditKeepassModal'
    import AddKeepassFolderModal from './modal/AddKeepassFolderModal'
    import DeleteModal from './../common/DeleteModal'
    import IconsPopover from './popover/IconsPopover'
    import {utilsMixin} from './../../mixins/utilsMixin'

    function dragndropToConsumableArray(arr) {
        if (Array.isArray(arr)) {
            let arr2 = Array(arr.length)
            for (let i = 0; i < arr.length; i++) {
                arr2[i] = arr[i]
            }
            return arr2
        } else { return Array.from(arr) }
    }

    export default {
        name: 'KeepassWrapper',
        components: {IconsPopover, TreeView},
        mixins: [utilsMixin],
        props: {
            addFavoritesRoute: {
                type: String,
                required: true
            },
            categoryId: {
                type: Number,
                required: true
            },
            confirmDelayInSeconds: {
                type: Number,
                required: false,
                default: 5
            },
            createMultipleRoute: {
                type: String,
                required: true
            },
            entryMode: {
                type: Boolean,
                required: false,
                default: false
            },
            iconList: {
                type: Array,
                required: false,
                default: []
            },
            isPassphraseEnabled: {
                type: Boolean,
                required: true
            },
            isPrivate: {
                type: Boolean,
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
            addToFavorites() {
                if (this.entriesSelected.length) {
                    axios.post(this.addFavoritesRoute, {keepasses: this.entriesSelected}).then(res => {
                        if (res.data) {
                            this.$notify({title: 'Success', text: 'Added to favorites !', type: 'success'})
                        }
                    })
                }
            },
            addFolder(keepass) {
                keepass.children = []
                if (this.selection && this.selection.length) {
                    this.selection[0].children.push(keepass)
                } else {
                    this.model.push(keepass)
                }
            },
            areAllEntriesSelected() {
                if (!this.selection || !this.selection.length) return false
                let selectionChildren = this.selection[0].children.filter(c => !c.is_folder)
                let selectedChildren = this.entriesSelected.filter(e => e.parent_id === this.selection[0].id)

                return selectionChildren.length !== 0 && selectionChildren.length === selectedChildren.length
            },
            copyFolderLink() {
                try {
                    if (!this.selection.length || !this.selection[0].is_folder) return

                    let splittedUrl = (window.location.href).split('/')
                    splittedUrl.pop()
                    if (!splittedUrl.find(s => s === 'entry')) splittedUrl.push('entry')
                    splittedUrl.push(this.selection[0].id)
                    let url = splittedUrl.join('/')
                    let input = document.createElement('input')
                    document.body.appendChild(input)
                    input.value = url
                    input.select()
                    document.execCommand('copy')
                    document.body.removeChild(input)
                    this.$notify({text: 'URL has been copied to clipboard.'})
                } catch (err) {
                    this.$notify({title: 'Error', text: 'Error while copying the URL : '+err, type: 'error'})
                }
            },
            delete(keepass) {
                let index = this.selection[0].children.findIndex(k => k.id === keepass.id)
                if (index !== -1) {
                    this.selection[0].children.splice(index, 1)
                }
            },
            dragndrop() {
                return {...this.dragndropselection(() => this.model, m => this.model = m)}
            },
            dragndropselection(model, cb) {
                let self = this
                return {
                    draggable: self.draggable,
                    droppable: self.droppable,
                    drag: function drag(item, event, inputs) {
                        event.dataTransfer && event.dataTransfer.setData("application/json", JSON.stringify(inputs.selection));
                    },
                    guard: function guard(target, event, inputs) {
                        // Other data types
                        if (event && event.dataTransfer && event.dataTransfer.types.indexOf("application/json") < 0) return false;
                        // Prevent drop on self
                        var selfDrop = function selfDrop() {
                            return target && array(inputs.selection).contains(target);
                        };
                        // Prevent drop on child
                        var childDrop = function childDrop() {
                            return inputs.ancestors && inputs.ancestors.reduce(function (prev, curr) {
                                return prev || array(inputs.selection).contains(curr);
                            }, false);
                        };

                        return selfDrop() || childDrop();
                    },
                    drop: function drop(target, event, inputs) {
                        let targetID = target ? target.id : null
                        if (inputs.selection && inputs.selection.length && inputs.selection[0].parent_id !== targetID) {
                            let bak = JSON.stringify(model());
                            self.$modal.show('dialog', {
                                title: 'Confirm drop',
                                text: 'Move <b class="text-primary">'+inputs.selection[0].title+'</b> in <b class="text-danger">/'+(target ? target.title : '')+'</b> ?',
                                buttons: [
                                    {
                                        title: 'Confirm',
                                        handler: () => {
                                            let updatedModel = tree(model(), inputs.category).filter(function (e) {
                                                return inputs.selection.indexOf(e) < 0
                                            });
                                            let adjustedTarget = target ? target[inputs.category] && target[inputs.category] instanceof Array ? target : array(inputs.ancestors).last() : null
                                            if (adjustedTarget) adjustedTarget[inputs.category] = [].concat(dragndropToConsumableArray(adjustedTarget[inputs.category]), dragndropToConsumableArray(inputs.selection));else updatedModel = [].concat(dragndropToConsumableArray(updatedModel), dragndropToConsumableArray(inputs.selection))
                                            cb(updatedModel)
                                            inputs.selection[0].parent_id = targetID
                                            axios.post(self.saveRoute, {keepass: inputs.selection[0]}).then(res => {
                                                if (res.data.keepass) self.$notify({title: 'Success', text: 'The folder has been moved !', type: 'success'})
                                            }).finally(() => self.$modal.hide('dialog'))
                                        }
                                    },
                                    {title: '', default: true, handler: () => {}},
                                    {
                                        title: 'Cancel',
                                        handler: () => {
                                            cb(JSON.parse(bak))
                                            self.$modal.hide('dialog')
                                        }
                                    }
                                ]
                            })
                        }
                    }
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
                        itemsNotFolded[i].classList.remove('cil-folder')
                        itemsNotFolded[i].classList.add('cil-folder-open')
                    }
                    for (let i = 0; i < itemsFolded.length; i++) {
                        itemsFolded[i].classList.remove('cil-folder-open')
                        itemsFolded[i].classList.add('cil-folder')
                    }
                }, 50)
            },
            openAddFolderModal() {
                let props = {
                    categoryId: this.categoryId,
                    parentId: this.selection && this.selection.length ? this.selection[0].id : null,
                    path: this.getPath(),
                    icons: this.icons,
                    saveRoute: this.saveRoute
                }
                this.$modal.show(AddKeepassFolderModal, props, {adaptive: true, height: 'auto', classes: 'v--modal overflowAuto modalMaxHeight'})
            },
            openDeleteFolderModal() {
                if (this.selection && this.selection.length) {
                    this.$modal.show(DeleteModal, {
                        bodyText: 'It will delete the folder '+this.selection[0].title+' and all its children.',
                        confirmDelayInSeconds: this.confirmDelayInSeconds,
                        xhrData: {keepass: this.selection[0]},
                        route: '/keepass/'+(this.isPrivate ? 'private/' : '')+this.categoryId+'/delete/'+this.selection[0].id
                    }, {adaptive: true})
                }
            },
            openEditModal(keepass) {
                if (keepass && keepass.parent_id) {
                    let props = {
                        confirmDelayInSeconds: this.confirmDelayInSeconds,
                        deleteRoute: keepass.id ? '/keepass/'+(this.isPrivate ? 'private/' : '')+this.categoryId+'/delete/'+keepass.id : '',
                        icons: this.icons,
                        keepass: keepass,
                        saveRoute: this.saveRoute,
                        isPassphraseEnabled: this.isPassphraseEnabled,
                        isPrivate: this.isPrivate
                    }
                    this.$modal.show(EditKeepassModal, props, {adaptive: true, height: 'auto', clickToClose: false, classes: 'v--modal overflowAuto modalMaxHeight'})
                }
            },
            pasteEntries() {
                if (this.entriesSelected.length && this.selection && this.selection.length) {
                    let entries = []
                    for (let i = 0; i < this.entriesSelected.length; i++) {
                        let entry = JSON.parse(JSON.stringify(this.entriesSelected[i]))
                        entry.id = null
                        entry.parent_id = this.selection && this.selection.length ? this.selection[0].id : null
                        entries.push(entry)
                    }
                    axios.post(this.createMultipleRoute, {keepasses: entries}).then(res => {
                        if (res.data.keepasses) {
                            this.selection[0].children.push(...res.data.keepasses)
                            this.$notify({title: 'Success', text: 'Entries have been created !', type: 'success'})
                        }
                    })
                }
            },
            removeFolder(keepass) {
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
                sortable.sort((a, b) => a[prop].localeCompare(b[prop]))
            },
            sortChildrenByTitle() {
                if (this.selection && this.selection.length && this.selection[0].children && this.selection[0].children.length) {
                    this.sortByProperty(this.selection[0].children, 'title')
                }
            },
            sortTreeView(a, b) {
                const sameChildrenState = 'children' in a === 'children' in b
                if (!a.is_folder && !b.is_folder) return 0
                if (sameChildrenState) {
                    return a.title.localeCompare(b.title)
                } else if ('children' in a) {
                    return -1
                } else {
                    return 1
                }
            },
            toggleAllEntriesSelection() {
                if (!this.selection || !this.selection.length) return
                let selectionChildren = this.selection[0].children.filter(c => !c.is_folder)
                let selectedChildren = this.entriesSelected.filter(e => e.parent_id === this.selection[0].id)
                let areAllEntriesChecked = this.areAllEntriesSelected()
                for (let i = 0; i < selectedChildren.length; i++) {
                    this.entriesSelected.splice(this.entriesSelected.findIndex(e => e.id === selectedChildren[i].id), 1)
                }
                if (!areAllEntriesChecked) {
                    for (let i = 0; i < selectionChildren.length; i++) {
                        this.entriesSelected.push(selectionChildren[i])
                    }
                }
            },
            toggleLockDrag() {
                this.draggable = !this.draggable
                this.droppable = !this.droppable
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
            },
            windowSizeDetector(e) {
                if (window.innerWidth < 768) {
                    this.draggable = false
                    this.droppable = false
                    this.showLockDrag = true
                }
            }
        },
        mounted() {
            this.windowSizeDetector()
            window.addEventListener('resize', this.windowSizeDetector)
            EventBus.$on('keepass-search', this.hideEmptyItems)
            EventBus.$on('keepass-saved', (keepass, isNew) => this.update(keepass, isNew))
            EventBus.$on('keepass-deleted', keepass => this.delete(keepass))
            EventBus.$on('keepass-folder-created', keepass => this.addFolder(keepass))
            EventBus.$on('data-deleted', (res, xhrData) => this.removeFolder(xhrData.keepass))
            this.model = JSON.parse(JSON.stringify(this.items))
            this.icons = JSON.parse(JSON.stringify(this.iconList))
            if (this.entryMode) {
                this.selection = [this.model[0]]
            }

            let btnGroup = document.getElementById('btnGroup')
            let btnFloatLeft = document.getElementsByClassName('floatLeftBtn')
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 100) {
                    btnGroup.classList.add('sticked')
                    if (btnFloatLeft && btnFloatLeft.length) {
                        btnFloatLeft[0].style.float = 'initial'
                    }
                } else {
                    btnGroup.classList.remove('sticked')
                    if (btnFloatLeft && btnFloatLeft.length) {
                        btnFloatLeft[0].style.float = 'left'
                    }
                }
            })

            let searchInput = document.getElementsByClassName('search')
            if (searchInput && searchInput.length) {
                searchInput[0].classList.add('form-control')
            }
        },
        data() {
            return {
                draggable: true,
                droppable: true,
                entriesSelected: [],
                icons: [],
                model: [],
                selection: [],
                onSelect: newSelection => {
                    this.selection = newSelection
                    this.manageFolderIcons()
                },
                showLockDrag: false,
                showSelection: false,
                showTree: true,
                strategies: {
                    selection: ['single'],
                    click: ['select', 'toggle-fold', 'unfold-on-selection'],
                    fold: ['opener-control', 'no-child-selection']
                },
                transition: {
                    attrs: { appear: true },
                    props: { name: 'TreeViewTransition' }
                },
                display: (item, inputs) => {
                    if (item.is_folder) {
                        if (item.icon_id && item.icon) {
                            return <a><img src={'/storage/'+item.icon.path} alt={item.icon.filename} height="14" width="14"></img> {item.title}</a>
                        }
                        return <a><i class="cil-folder text-primary"></i> {item.title}</a>
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

    .floatLeftBtn {float: left;}

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
        max-height: 70vh;
        overflow-y: auto;
    }

    th, td {
        max-width: 200px;
    }

    @media (min-width: 992px) {
        .sticked {
            right: 35px;
            top: 15px;
        }
    }

    @media (max-width: 992px) {
        .colTreeView {
            padding: 0 !important;
        }

        .TreeView {
            margin-bottom: 15px;
            max-height: 400px;
            overflow-y: auto;
        }
    }
</style>
