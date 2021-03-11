import Vue from 'vue';

// Install BootstrapVue
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// font awesome free
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'

library.add(fas)
Vue.component('font-awesome-icon', FontAwesomeIcon)

// main 
import App from './Main.vue'
import VueRouteMiddleware from 'vue-route-middleware';
import router from './routes'

router.beforeEach(VueRouteMiddleware());

Vue.config.productionTip = false

// VUEX STORE + token interceptor
import store from './store/index'
import interceptorsSetup from './store/interceptors'
interceptorsSetup()

// FORM FIELD VALIDATIONS + RULES
import { ValidationProvider } from 'vee-validate';
import './formvalidations/vee-validate.js'

const app = new Vue({
  store: store,
  router,
  render: h => h(App),
  components: {
    ValidationProvider
  }
}).$mount('#app')
