export const utilsMixin = {
    methods: {
        copy(value, type) {
            if (value && value !== '<!---->') {
                if (navigator.userAgent.match(/ipad|iphone/i) || navigator.userAgent.indexOf('Safari') !== -1 && navigator.userAgent.indexOf('Chrome') === -1) {
                    this.copyFromInput(value, type)
                } else {
                    navigator.permissions.query({name: 'clipboard-write'}).then(res => {
                        if (res.state === 'granted' || res.state === 'prompt') {
                            navigator.clipboard.writeText(value).then(() => {
                                this.$notify({text: type+' copied !'})
                            }, () => {
                                this.$notify({text: type+' not copied !', type: 'error'})
                            });
                        } else {
                            this.copyFromInput(value, type)
                        }
                    }).catch(error => this.copyFromInput(value, type))
                }
            }
        },
        copyFromInput(value, type) {
            let fakeInput = document.createElement('textarea')
            document.body.appendChild(fakeInput)
            fakeInput.value = value
            fakeInput.select()
            fakeInput.setSelectionRange(0, 99999)
            document.execCommand('copy')
            document.body.removeChild(fakeInput)
            this.$notify({text: type+' copied !'})
        },
        getURL(url) {
            if (!url) return url
            return (url.startsWith('https://') || url.startsWith('http://')) ? url : 'http://'+url
        },
    }
}
