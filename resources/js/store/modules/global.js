import axios from 'axios'
import {globals} from '../index.js'

const state = () => ({
    configuration: [],
    roles: []
})
  
// getters
const getters = { 
    configuration: (state, getters) => {
        return state.configuration
    },
    roles: (state, getters) => {
        return state.roles
    }
}

// mutations
const mutations = {
    SET_CONFIGURATION(state, configuration ) {
        state.configuration = configuration
    },
    SET_ROLES(state, roles ) {
        state.roles = roles
    }
}
  
// actions
const actions = {
    async initData({ state, commit, rootState }) {
        let results = []
        let status, message, data, combinedStatus = 200

        // ---- configuration -----
        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion+ 'configuration'
            )
            status = response.status
            message = 'Configuration loaded'
            data = response.data
            commit('SET_CONFIGURATION', data)

        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
            commit('SET_CONFIGURATION', [])
            combinedStatus = 500
        }     

        results.push({
            'status': status,
            'message': message,
            'items': data,
            'component': 'configuration'
        })

        // ---- roles ----
        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion+ 'roles'
            )

            status = response.status
            message = 'Roles loaded'
            data = response.data.data
            commit('SET_ROLES', data)

        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
            commit('SET_ROLES', [])
            combinedStatus = 500
        } 

        results.push({
            'status': status,
            'message': message,
            'items': data,
            'component': 'roles'
        })
        
        return {
            'results': results,
            'status':combinedStatus
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
