<template>
    <div class="border-primary h100">
        <div class="card-header bg-primary">{{keepass.id ? 'Edit' : 'New'}} {{keepass.title}}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" v-model="keepassComputed.title" class="form-control" :class="[keepassComputed.title ? 'is-valid' : 'is-invalid']">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Login</label>
                        <input type="text" v-model="keepassComputed.login" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="text" v-model="keepassComputed.password" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" v-model="keepassComputed.url" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea v-model="keepassComputed.notes" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer" :class="[keepass.id ? '' : 'text-right']">
            <div>
                <button v-if="keepass.id" v-on:mousedown="submit" v-on:mouseup="cancelSubmit" v-on:mouseleave="cancelSubmit" type="button" class="btn btn-danger">Delete</button>
                <button v-on:click="save()" type="button" class="btn btn-primary rounded ml-2" :class="[keepass.id ? 'float-right' : '']">Save</button>
                <button v-on:click="close()" type="button" class="btn btn-secondary rounded" :class="[keepass.id ? 'float-right' : '']">Cancel</button>
            </div>
            <div v-if="keepass.id" class="progress bg-dark mt-2">
                <div class="progress-bar bg-warning" role="progressbar" :style="'width:'+Math.round(counter / 5)+'%'" :aria-valuenow="counter / 5" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from './../../../eventBus'

    export default {
        name: 'DeleteModal',
        props: {
            deleteRoute: {
                type: String,
                required: true
            },
            keepass: {
                type: Object,
                required: true
            },
            saveRoute: {
                type: String,
                required: true
            },
        },
        methods: {
            cancelSubmit() {
                clearInterval(this.interval)
                this.interval = false
                this.counter = 0
            },
            close() {
                this.$emit('close')
            },
            save() {
                if (this.keepassComputed.title) {
                    axios.post(this.saveRoute, {keepass: this.keepassComputed}).then(res => {
                        if (res.data.keepass) {
                            this.$notify({title: 'Success', text: 'Item has been saved !', type: 'success'})
                            EventBus.$emit('keepass-saved', res.data.keepass, !this.keepass.id)
                        }
                    }).finally(() => this.close())
                } else {
                    this.$notify({title: 'Warning', text: 'Please enter a title.', type: 'warn'})
                }
            },
            submit(e) {
                if (!this.interval) {
                    this.interval = setInterval(() => {
                        this.counter++
                        if (this.counter >= 500) {
                            this.cancelSubmit()
                            axios.delete(this.deleteRoute).then(res => {
                                if (res.data) {
                                    EventBus.$emit('keepass-deleted', this.keepass)
                                    this.$notify({title: 'Success', text: 'Item has been deleted !', type: 'success'})
                                }
                            }).catch(res => {
                                this.$notify({title: 'Error', text: res.response.status !== 500 ? res.response.data.message : 'Internal error.', type: 'error'})
                            }).finally(() => this.close())
                        }
                    }, 10)
                }
            }
        },
        mounted() {
            this.keepassComputed = JSON.parse(JSON.stringify(this.keepass))
        },
        data() {
            return {
                counter: 0,
                interval: false,
                keepassComputed: {}
            }
        }
    }
</script>

<style scoped>
    .h100 {height: 100%}

    .progress-bar {
        -webkit-transition: width .1s ease;
        -o-transition: width .1s ease;
        transition: width .1s ease;
    }
</style>
