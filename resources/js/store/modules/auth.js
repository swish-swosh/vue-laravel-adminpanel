import axios from 'axios'
import {globals} from '../index.js'

const state = () => ({
    accessToken: '',
    refreshToken: '',
    expireDateTime: '',
    rememberMe: false,
    user: { // init defaults to avoid type errors when data not loaded yet
        id: null,
        email: '',
        name: '',
        roles: [],
        resource_id: null,
        user_image: ''
    }
})
  
// getters
const getters = { 
    accessToken: (state, getters) => {
        return state.accessToken
    },
    refreshToken: (state, getters) => {
        return state.refreshToken
    },
    expireDateTime: (state, getters) => {
        return state.expireDateTime
    },
    rememberMe: (state, getters) => {
        return state.rememberMe
    },
    user: (state, getters) => {
        return state.user
    },
    hasRole: (state, getters, rootState) => {
        return role => {
            try {
                let id = rootState.global.roles.find(x => x.type == role).id
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
                    id = rootState.global.roles.find(x => x.type == roles[n]).id
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
            if(id) return rootState.global.roles.find(x => x.id == id).name
        } catch(err) {
            return '' // avoid type errors 
        }
    }
}

// mutations
const mutations = {
    SET_ACCESS_TOKEN(state, accessToken ) {
        state.accessToken = accessToken
     },
    SET_REFRESH_TOKEN(state, refreshToken ) {
        state.refreshToken = refreshToken
    },
    SET_EXPIRE_DATETIME(state, expiresIn ) {
        // store as date time Z instead of seconds, more readable in local storage
        let expireDateTime = new Date()
        state.expireDateTime = new Date(expireDateTime.setSeconds(expireDateTime.getSeconds() + expiresIn))
    },
    SET_REMEMBER_ME(state, remember) {
        state.rememberMe = remember
    },
    SET_USER(state, user ) {
        state.user = user
    },
    SET_USER_EMAIL(state, email ) {
        state.user.email = email
    },
    SET_USER_AUTH_EMPTY(state) {

        state.user = { // clean out auth settings
            id: null,
            email: '',
            name: '',
            roles: [],
            resource_id: null,
            user_image: ''
        }

        state.accessToken=''
        state.refreshToken=''
        state.expireDateTime=''
    }
}
  
// actions
const actions = {
    // register new user
    async registerUser({ state, commit }, credentials) {

        try {
            let response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion + 'auth/register', credentials
            )

            return {
                'status': response.status,
                'message': response.data.message
            }
                        
        } catch(err) {

            // something went wrong clear local storage for:
            commit('SET_USER_AUTH_EMPTY')
            return {
                'status': err.status,
                'message': err.response.data.error
            }
        }
    },
    // retrieve user for use in local storage front-end
    async retrieveUser({ state, commit }) {

        try {

            let response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion +  'retrieveUser'
            )

            // minimum user details get stored in local storage for reference (encrypted)
            // & cleared on logout
            let user = {
                id: response.data.user.id,
                email: response.data.user.email,
                name: response.data.user.name,
                roles: response.data.user.roles,
                resource_id: response.data.profile.resource_id,
                user_image: response.data.profile.user_image
            }
    
            commit('SET_USER', user)

            return {
                'status': response.status,
                'message': response.data.message
            }

        } catch(err) {

            commit('SET_USER_AUTH_EMPTY')

            return {
                'status': err.status,
                'message': err.response.data.error
            }            
        }

    },
    // register new user
    async login({ state, commit }, credentials) {

        // clear any past state &
        // prohibit refresh token is used as auto login
        commit('SET_USER_AUTH_EMPTY')

        try {

            let response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion + 'auth/login', credentials
            )

            commit('SET_REMEMBER_ME', credentials.remember_me )
            commit('SET_ACCESS_TOKEN', response.data.access_token )
            commit('SET_REFRESH_TOKEN', response.data.refresh_token)
            commit('SET_EXPIRE_DATETIME', response.data.expires_in)

            return {
                'status': response.status,
                'message': response.data.message
            }

        }catch(err) {

            return {
                'status': err.status,
                'message': err.response.data.message
            }
        }
    },
    async logout({ state, commit }, token) {

        try {
            let response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion + 'auth/logout',
                {}
            )

            commit('SET_USER_AUTH_EMPTY')
            
            return {
                'status': response.status,
                'message': response.data.message
            }

        } catch(err) {
            return {
                'status': err.status,
                'message': err.response.data.message
            }
        }
    },
    async emailPasswordRecovery({ state, commit }, credentials) {

        // user forgot password, send recovery link using given email
        // return errors or recovery message
        try {
            let response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion + 'auth/forgot-password', credentials
            )
            return {
                'status': response.status,
                'message': response.data.message    // Laravel backend message feedback
            }

        } catch(err) {
            return {
                'status': err.status,
                'message': err.response.data.message    // Laravel backend error feedback
            }
        }
    },
    async passwordReset({ state, commit }, credentials) {

        // user forgot password, send new credentials (if token is included then authentication isn't mandatory, the security token = recovery link)
        // return errors or recovery message
        try {
            let response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion + 'auth/reset-password', credentials
            )
            return {
                'status': response.status,
                'message': response.data.message    // Laravel error
            }
        } catch(err) {
            return {
                'status': err.status,
                'message': err.response.data.message
            }
        }
    },
    async emailResendNotification({ state, commit }, email) {

        try {
            let response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion + 'auth/resend-notification', email
            )                    
            return {
                'status': response.status,
                'message': response.data.message    // Laravel error
            }

        } catch(err) {
            return {
                'status': err.status,
                'message': err.response.data.message
            }
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


