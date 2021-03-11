import axios from 'axios'
import {globals} from '../index.js'

const state = () => ({
    countries: [],
    roles: [],
    resourceTypes: []
})
  
// getters
const getters = { 
    countries: (state, getters) => {
        return state.countries
    },
    roles: (state, getters) => {
        return state.roles
    },
    resourceTypes: (state, getters) => {
        return state.resourceTypes
    }
}

// mutations
const mutations = {
    SET_COUNTRIES(state, countries ) {
        state.countries = countries
    },
    SET_ROLES(state, roles ) {
        state.roles = roles
    },
    SET_RESOURCE_TYPES(state, resourceTypes ) {
        state.resourceTypes = resourceTypes
    }
}
  
// actions
const actions = {
    async initData({ state, commit, rootState }) {
        let results = []
        let status, message, data, combinedStatus = 200

        // ---- resourceTypes -----
        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion +  'resourceTypes'
            )
            status = response.status
            message = 'resource types loaded'
            data = response.data.data
            commit('SET_RESOURCE_TYPES', data)

        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
            commit('SET_RESOURCE_TYPES', [])
            combinedStatus = 500
        }      

        results.push({
            'status': status,
            'message': message,
            'items': data,
            'component': 'resourceTypes'
        })

        // ---- countries ----
        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion + 'countries'
            )
            status = response.status
            message = 'countries loaded'
            data = response.data.data
            commit('SET_COUNTRIES', data)

        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
            commit('SET_COUNTRIES', [])
            combinedStatus = 500
        }  

        results.push({
            'status': status,
            'message': message,
            'items': data,
            'component': 'countries'
        })

        // ---- roles ----
        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion + 'roles'
            )

            status = response.status
            message = 'rolesloaded'
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
