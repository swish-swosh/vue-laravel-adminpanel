// import Vue from 'vue';
import axios from 'axios'
import {globals} from '../index.js'

const state = () => ({
    user: { // init defaults to avoid type errors when data not loaded yet
        id: null,
        email: '',
        name: '',
        user_image: '',
        resource_id: '',
        roles: []
    }
})
  
// getters
const getters = { 
    user: (state, getters) => {
        return state.user
    },
    hasRole: (state, getters, rootState) => {
        return role => {
            try {
                let id = rootState.components.roles.find(x => x.type == role).id
                if(id) return state.user.roles.includes(id)
            } catch(err) {
                return false // avoid type errors
            }               
        }
    },
    hasAnyRoles: (state, getters, rootState) => {
        return roles => {
            try {
                let n=0, id
                while(n<roles.length) {
                    id = rootState.components.roles.find(x => x.type == roles[n]).id
                    if(id) return state.user.roles.includes(id)
                    n++
                }
            } catch(err) {
                return false // avoid type errors
            }               
        }
    },
    topRoleName: (state, getters, rootState) => {
        let id = Math.min(...state.user.roles)
        try {
            if(id) return rootState.components.roles.find(x => x.id == id).name
        } catch(err) {
            return '' // avoid type errors 
        }
    }
}

// mutations
const mutations = {
    SET_USER(state, user ) {
        if(user == null){
            state.user = {
                id: null,
                name: '',
                email: '',
                roles: [],
                resource_id: null,
                user_image: ''
            }
        } else {
            state.user = user
        }
    }
}
  
// actions
const actions = {
    // minimum data returned placed in local storage ( roles, image, token, email, name )
    async retrieveUserByToken({ state, commit, rootState }) {
        let status, message,data

        try {
            const response = await axios.get(
                globals.baseUrlBackend + globals.APIVersion+
                'user-by-token'
            )
            status = response.status
            message = response.statusText
            data = response.data.data    
            commit('SET_USER', data)
        } catch(err) {
            status = err.response.status
            message = err.response.statusText
            data=null
        }

        return {
            'status': status,
            'message': message,
            'user': data
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


