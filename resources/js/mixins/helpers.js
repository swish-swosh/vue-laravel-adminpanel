export default {
    methods: {
        isValidId(id) {
            if(/^\d+$/.test(id)) {
                if(id>0) return true
            }
            return false
        },
        isValidTabIndex(index, n) {
            let tb = Number.parseInt(index)
            if(tb >= 0 && tb < n) return tb
            return 0
        }
    }
}