import axios from 'axios'
import {globals} from '../index.js'

const state = () => ({
    logs: []
})
  
// getters
const getters = { 
    logs: (state, getters) => {
        return state.logs
    }
}

// mutations
const mutations = {
    SET_LOGS(state, logs ) {
        state.logs = logs
    }
}
  
const actions = {
    async retrieveLogs({ state, commit, rootState }, params) {
        let status, message, data, links, meta
        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion+
                'logs/'+params
            )
            status = response.status
            message = 'Logs loaded'
            links = response.data.links
            meta = response.data.meta
            data = response.data.data
        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
            links = null
            meta = null
        }
        return {
            'status': status,
            'message': message,
            'data': data,
            'links': links,
            'meta': meta
        }
    }
}
    
export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
