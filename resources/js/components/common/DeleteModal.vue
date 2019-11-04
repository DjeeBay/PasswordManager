<template>
    <div class="card border-danger h100">
        <div class="card-header bg-danger">Confirm to delete</div>
        <div class="card-body">
            <h2>Are you sure ?</h2>
            <div v-if="bodyText">{{bodyText}}</div>
        </div>
        <div class="card-footer">
            <div class="text-right">
                <button @mousedown="submit" @mouseup="cancelSubmit" @mouseleave="cancelSubmit" @touchstart="submit" @touchend="cancelSubmit" @touchcancel="cancelSubmit" type="button" class="btn btn-danger">Confirm</button>
                <button v-on:click="close()" type="button" class="btn btn-secondary rounded">Cancel</button>
            </div>
            <div class="progress bg-dark mt-2">
                <div class="progress-bar bg-warning" role="progressbar" :style="'width:'+Math.round(counter / 5)+'%'" :aria-valuenow="counter / 5" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import {EventBus} from './../../eventBus'

    export default {
        name: 'DeleteModal',
        props: {
            bodyText: {
                type: String,
                required: false
            },
            route: {
                type: String,
                required: true
            },
            xhrData: {
                type: Object,
                required: false
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
            submit(e) {
                if (!this.interval) {
                    this.interval = setInterval(() => {
                        this.counter++
                        if (this.counter >= 500) {
                            this.cancelSubmit()
                            axios.delete(this.route, this.xhrData).then(res => {
                                EventBus.$emit('data-deleted', res, this.xhrData)
                                if (res.data.redirect) location.href = res.data.redirect
                            })
                                .catch(res => this.$notify({title: 'Error', text: res.response.status !== 500 ? res.response.data.message : 'Internal error.', type: 'error'}))
                                .finally(() => this.$emit('close'))

                        }
                    }, 10)
                }
            }
        },
        data() {
            return {
                counter: 0,
                interval: false
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
