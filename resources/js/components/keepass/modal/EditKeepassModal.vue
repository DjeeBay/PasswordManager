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
                        <input :type="showPassword ? 'text' : 'password'" v-model="keepassComputed.password" class="form-control">
                        <div class="mt-1">
                            <button v-on:click="showPassword = !showPassword" type="button" class="btn btn-sm btn-secondary"><i :class="[showPassword ? 'cui-lock-unlocked' : 'cui-lock-locked']"></i></button>
                            <button v-on:click="generatePassword" type="button" class="btn btn-sm btn-dark">Generate ({{passwordLength}})</button>
                            <button v-on:click="passwordLength++" type="button" class="btn btn-sm btn-dark"><i class="cui-caret-top"></i></button>
                            <button v-on:click="passwordLength > 5 ? passwordLength-- : null" type="button" class="btn btn-sm btn-dark"><i class="cui-caret-bottom"></i></button>
                        </div>
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
                        <textarea v-model="keepassComputed.notes" class="form-control" rows="5"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer" :class="[keepass.id ? '' : 'text-right']">
            <div>
                <button v-if="keepass.id" @mousedown="submit" @mouseup="cancelSubmit" @mouseleave="cancelSubmit" @touchstart="submit" @touchend="cancelSubmit" @touchcancel="cancelSubmit" type="button" class="btn btn-danger">Delete</button>
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
            generatePassword() {
                let chars = 'abcdefghijklmnopqrstuvwxyz!@#$%^&*()-+<>{}[]ABCDEFGHIJKLMNOP1234567890'
                let pass = ''
                for (let x = 0; x < this.passwordLength; x++) {
                    let i = Math.floor(Math.random() * chars.length)
                    pass += chars.charAt(i)
                }
                this.keepassComputed.password = pass
            },
            save() {
                if (this.keepassComputed.title) {
                    axios.post(this.saveRoute, {keepass: this.keepassComputed}).then(res => {
                        if (res.data.keepass) {
                            this.$notify({title: 'Success', text: 'Item has been saved !', type: 'success'})
                            EventBus.$emit('keepass-saved', res.data.keepass, !this.keepass.id)
                        }
                    })
                        .catch(res => this.$notify({title: 'Error', text: res.response.status !== 500 ? res.response.data.message : 'Internal error.', type: 'error'}))
                        .finally(() => this.close())
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
            if (!this.keepass.id) {
                this.keepassComputed.password = null
                this.keepassComputed = JSON.parse(JSON.stringify(this.keepassComputed))
            }
        },
        data() {
            return {
                counter: 0,
                interval: false,
                keepassComputed: {},
                passwordLength: 10,
                showPassword: false
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