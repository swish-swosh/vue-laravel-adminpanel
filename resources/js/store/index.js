import Vue from 'vue';
import Vuex from 'vuex';
// import axios from 'axios'
import createPersistedState from "vuex-persistedstate";
// import Cookies from 'js-cookie';
import components from './modules/components'
import auth from './modules/auth'
import users from './modules/users'
import logs from './modules/logs'
import resources from './modules/resources'

import SecureLS from "secure-ls";
var ls = new SecureLS({ isCompression: false });

Vue.use(Vuex);

const globals = {
  baseUrlBackend: 'http://vue-adminpanel/api/',
  APIVersion: 'V1/',
  urlRefreshPath: 'auth/refreshToken'
}
export {globals}

const dataState = createPersistedState({
  // only persist these states:
  paths: [
    'auth.accessToken',
    'auth.refreshToken',
    'auth.expireDateTime',
    'auth.rememberMe',
    'auth.user',
    'users.user',
    'components.countries',
    'components.roles',
    'components.resourceTypes'
  ],

// no encryption?, storage object not needed. comment out for development!
// max item size = 5MB !

// encryption
/*
  storage: {
        getItem: (key) => ls.get(key),
        setItem: (key, value) => ls.set(key, value),
        removeItem: (key) => ls.remove(key),
  },
*/

  /* cookie way, max size of all cookies should not exceed 4096 bytes! ( we don't need more for now )
  storage: {
    getItem: key => Cookies.get(key),
    setItem: (key, value) => Cookies.set(key, value, {expires: 30 }),
    removeItem: key => Cookies.remove(key)
  }
  */
})

export default new Vuex.Store({
  plugins: [dataState],
  modules: {
    auth,
    users,
    components,
    logs,
    resources
  }
})

 