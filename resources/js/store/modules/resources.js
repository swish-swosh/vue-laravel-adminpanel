import axios from 'axios'
import {globals} from '../index.js'

const state = () => ({
    resources: [],
    resourceAccesses: []
})
  
// getters
const getters = {
    resources: (state, getters) => {
        return state.resources
    },
    resourceAccesses: (state, getters) => {
        return state.resourceAccesses
    }
}

// mutations
const mutations = {
    SET_RESOURCE_ACCESSES(state, resourceAccesses ) {
        state.resourceAccesses = resourceAccesses
    },
    SET_RESOURCES(state, resources) {
        state.resources = resources
    }
}
  
// actions
const actions = {
    async retrieveResources({ state, commit, rootState }, params) {
        let status, message, data, links, meta

        console.log('retrieveResources')
        console.log(params)

        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion+ 
                'resources/' +params
            )
            status = response.status
            message = 'Resources loaded'
            links = response.data.links
            meta = response.data.meta
            data = response.data.data
            commit('SET_RESOURCES', data)
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
    },
    async retrieveResource({ state, commit, rootState }, params) {
        let status, message, data, links, meta

        console.log('retrieveResource')
        console.log(params)

        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion+
                'resources/' + params.id
            )
            status = response.status
            message = 'Resource loaded'
            data = response.data

        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
        }

        return {
            'status': status,
            'message': message,
            'data': data
        }
    },
    async patchResource({ state, commit, rootState }, patchData) {
        let status, message, data, links, meta

        try {
            const response = await axios.patch(
                globals.baseUrlBackend + globals.APIVersion+
                'resources/' + patchData.id,
                patchData
            )
            status = response.status
            message = 'Resources loaded'
            data = response.data
        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
        }

        return {
            'status': status,
            'message': message,
            'data': data
        }
    },
    async createResource({ state, commit, rootState }, createdData) {
        let status, message, data, links, meta

        try {
            const response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion+
                'resources/',
                createdData
            )
            status = response.status
            message = 'Resources loaded'
            data = response.data
        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
        }

        return {
            'status': status,
            'message': message,
            'data': data
        }
    },
    async retrieveResourceAccesses({ state, commit, rootState }) {
        let status, message, data
        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion+ 'resourceAccesses'
            )
            status = response.status
            message = 'Access resources loaded'
            data = response.data.data
            commit('SET_RESOURCE_ACCESSES', data)
        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
        }

        return {
            'status': status,
            'message': message,
            'data': data
        }
    },
    async patchResourceTypes({ state, commit, rootState }, data) {
        let status, message
        await axios.patch( // patch method defined in formData
                globals.baseUrlBackend + globals.APIVersion+ 'resourceAccesses/'+data.id,
                data
            ).then(response => {
            status = response.status
            message = response.statusText
            response = response.data
            commit('SET_ACCESS_RIGHTS', data)
        }).catch(err => {
            status = err.response.status
            message = err.response.statusText
        })

        return {
            'status': status,
            'message': message
        }
    },
    async destroyResourceTypes({ state, commit, rootState }, ids) {
        let status, message
        await axios.post(
            globals.baseUrlBackend + globals.APIVersion+ 'resourceAccesses',
                { ids }, // ids = [id array which to delete]
            ).then(response => {
            status = response.status
            message = response.statusText
        }).catch(err => {
            status = err.response.status
            message = err.response.statusText
        })
        return {
            'status': status,
            'message': message
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


