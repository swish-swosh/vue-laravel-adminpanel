import axios from 'axios'
import store from './index'
import router from './../routes'
import {globals} from './index.js'

// REQUEST handler:
// Inserts a bearer token in each request, relieving the axios http clients. (DRY)
// Handles rejects ( see response )
export default function setup() {
  axios.interceptors.request.use(function(config) {

      // auto inject bearer token on all requests
      const accessToken = store.getters['auth/accessToken']
      if(accessToken) {
          config.headers.Authorization = `Bearer ${accessToken}`
      }
      return config
  }, function(err) {
      return Promise.reject(err)
  })

// RESPONSE handler:
// Multiple requests are allowed.
// When processing a refresh token, subsequent requests are pushed in the holdQueue,
// and sequentially processed when the token refresh is completed.
// When the accesToken and the refreshToken are expired a redirection to the login page is triggered
// ( adjust to your preference )

// HoldQueue + handler
let isRefreshing = false
let holdQueue = []
const processQueue = (error, token = null) => {
  holdQueue.forEach(promis => {
      if (error) {
          promis.reject(error)
      } else {
          promis.resolve(token)
      }
  })
  holdQueue = []
}
axios.interceptors.response.use((response) => {
    return response           // no errors, deliver response
  }, async function (error) { // Handler on error

    // no network connection or server down, error/response will be undefined! return here
    if(typeof error.response === 'undefined') {
      error.message = 'Connection or webserver down, please contact support!'
      error.status = 502
      return Promise.reject(error)
    }

    const originalRequest = error.config

    // Auth, 401 error ? && originalRequest._retry = !true ? 
    // originalRequest will be undefined on initial error response, or on a previous 'return Promise.reject(..) error
    // originalRequest will be set to true when a previous response was a 'return axios(..)'. When the latest response 
    // set to error, the consecutive token refresh must be blocked to avoid an endless loop.
    if (error.response.status === 401 && !originalRequest._retry) {

      // got 401, try access by tokenRefresh
      if(originalRequest.url == globals.baseUrlBackend+globals.APIVersion+globals.urlRefreshPath){

        // clear out local storage / refresh token expired, login mandatory
        // store.commit('auth/SET_USER_AUTH_EMPTY')
        // failed on token refresh = redirect to login (if needed) and end error handling (return)
        // router.push({name: 'login'}).catch(()=>{})

        return Promise.reject(error)
      }

      // hold subsequent requests in the holdQueue while refresh token is being processed which is
      // mandatory when processing multiple requests ( if not, next requests will invalidate the running refresh token process)
      if (isRefreshing) {

        return new Promise(function(resolve, reject) {
          holdQueue.push({ resolve, reject })
        })
        .then(token => {
          originalRequest.headers['Authorization'] = 'Bearer ' + token
          return axios(originalRequest)
        })
        .catch(err => {
          return Promise.reject(err)
        })
      }

      // startup refresh token
      isRefreshing = true
      originalRequest._retry = true
      const headers = {
        'Content-Type': 'application/json',
        'RefreshToken': store.getters['auth/refreshToken']
      }
      try {
        // get new token via refreshToken
        const response = await axios.post(globals.baseUrlBackend+globals.APIVersion+globals.urlRefreshPath,
          {}, // body
          { 
            'headers': headers
          }
        )

        // update tokens + dateTime expire with returned updates
        store.commit('auth/SET_ACCESS_TOKEN', response.data.access_token)
        store.commit('auth/SET_REFRESH_TOKEN', response.data.refresh_token)
        store.commit('auth/SET_EXPIRE_DATETIME', response.data.expires_in)
        processQueue(null, response.data.access_token)
        return axios(originalRequest)
  
      } catch(err) {

        // refresh failed
        processQueue(err, null)
        router.push({name: 'login'}).catch(()=>{})
        return Promise.reject(error)

      } finally {
        // end isRefreshing
        isRefreshing = false
      }
    }

    // Other errors
    if(error.response.status >= 500){ // db down will trigger 500, override:
      error.message = error.response.data.error.substr(0, 41) + '..., please contact support!'
    } else 
    { // none 401 errors (4xx)
      error.message = error.response.data.error
    }

    error.status = error.response.status
    return Promise.reject(error)
  })

}