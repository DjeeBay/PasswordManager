<template>
    <div>
        <vue-good-table
            ref="favs-table"
            :columns="columns"
            :rows="favorites"
            :sort-options="{enabled: true, initialSortBy: {field: 'title'}}"
            :selectOptions="{enabled: true, selectionText: 'favorite(s) selected', selectOnCheckboxOnly: true}"
            theme="black-rhino"
            styleClass="vgt-table condensed striped"
        >
            <div slot="selected-row-actions">
                <button v-on:click="removeFromFavs" class="btn btn-sm btn-danger">Delete from favs</button>
            </div>
            <template slot="table-row" slot-scope="props">
                <span v-if="props.column.field === 'title'">
                    <img v-if="props.row.icon_id && props.row.icon" :src="'/storage/'+props.row.icon.path" :alt="props.row.icon.filename" height="16" width="16"> {{props.row.title}}
                    <div><small><em>({{props.row.fullpath}})</em></small></div>
                </span>
                <span v-else-if="props.column.field === 'password'">
                    <span v-on:click="copy(props.row.password, 'Password')" class="handHover"><i v-if="props.row.password">(length {{props.row.password.length}})</i></span>
                    <span v-if="props.row.password && props.row.login" v-on:click="copy(props.row.login+':'+props.row.password, 'Login & Password')" class="handHover"><small class="text-warning">L:P</small></span>
                </span>
                <span v-else-if="props.column.field === 'login'">
                    <span v-on:click="copy(props.row.login, 'Login')" class="handHover">{{props.row.login}}</span>
                </span>
                <div v-else-if="props.column.field === 'url'" class="text-nowrap">
                    <a :href="getURL(props.row.url)" target="_blank">{{props.row.url && props.row.url.length > 25 ? props.row.url.substr(0, 24)+'&hellip;' : props.row.url}}</a>
                    <span v-if="props.row.url" v-on:click="copy(props.row.url, 'URL')" class="handHover"><i class="cui-copy text-warning"></i></span>
                </div>
                <div v-else-if="props.column.field === 'notes'" class="notes">
                    {{props.row.notes}}
                </div>
            </template>
        </vue-good-table>
    </div>
</template>

<script>
    import axios from 'axios'
    import {utilsMixin} from './../../mixins/utilsMixin'

    export default {
        name: 'FavoriteList',
        mixins: [utilsMixin],
        props: {
            favoriteList: {
                type: Array,
                required: true
            },
            removeRoute: {
                type: String,
                required: true
            }
        },
        methods: {
            removeFromFavs() {
                if (this.$refs['favs-table'].selectedRows && this.$refs['favs-table'].selectedRows.length) {
                    axios.post(this.removeRoute, {keepasses: this.$refs['favs-table'].selectedRows}).then(res => {
                        if (res.data) {
                            for (let i = 0; i < this.$refs['favs-table'].selectedRows.length; i++) {
                                let index = this.favorites.findIndex(f => f.id === this.$refs['favs-table'].selectedRows[i].id)
                                if (index !== -1) {
                                    this.favorites.splice(index, 1)
                                }
                            }
                            this.$refs['favs-table'].unselectAllInternal()
                            this.$notify({title: 'Success', text: 'Removed !', type: 'success'})
                        }
                    })
                }
            }
        },
        mounted() {
            for (let i = 0; i < this.favoriteList.length; i++) {
                this.favorites.push(JSON.parse(JSON.stringify(this.favoriteList[i].keepass)))
            }
        },
        data() {
            return {
                columns: [
                    {
                        label: 'Title',
                        field: 'title',
                        filterOptions: {
                            enabled: true,
                        },
                    },
                    {
                        label: 'Login',
                        field: 'login',
                        filterOptions: {
                            enabled: true,
                        },
                    },
                    {
                        label: 'Password',
                        field: 'password'
                    },
                    {
                        label: 'URL',
                        field: 'url',
                        filterOptions: {
                            enabled: true,
                        },
                    },
                    {
                        label: 'Notes',
                        field: 'notes',
                        filterOptions: {
                            enabled: true,
                        },
                    },
                ],
                favorites: []
            }
        }
    }
</script>

<style scoped>
    .notes {
        max-height: 120px;
        overflow-y: auto;
        white-space: pre-wrap;
    }
</style>
