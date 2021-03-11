<template>
    <div>
        <b-container fluid>
            <validation-observer ref="observer" v-slot="{ invalid }">
                <b-form @submit="onSubmit">
                    <b-container class="action input">
                        <b-row class="form-title">
                            <b-col cols="12">Login:</b-col>
                        </b-row>
                        <b-row class="form-input-row">
                            <b-col cols="12">
                                <ValidationProvider name="login-name" rules="required|email" v-slot="validationContext">
                                    <b-input-group size="lg" class="mt-2">
                                        <b-input-group-prepend is-text>
                                            <b-icon icon="envelope-fill"></b-icon>
                                        </b-input-group-prepend>
                                        <b-form-input
                                            id="login-name"
                                            name="login-name"
                                            placeholder="your email"
                                            v-model="formData.email"
                                            :state="getValidationState(validationContext)"
                                            aria-describedby="login-name-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback class="invalid-feedback" id="flow-name-feedback">
                                            {{ validationContext.errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-input-group>
                                </ValidationProvider>
                            </b-col>
                        </b-row>
                        <b-row class="form-input-row">
                            <b-col cols="12">
                                <b-input-group size="lg" class="mt-1">
                                    <b-input-group-prepend is-text>
                                        <b-icon icon="lock-fill"></b-icon>
                                    </b-input-group-prepend>
                                    <b-form-input
                                        id="password-name"
                                        name="password-name"
                                        placeholder="password"
                                        type="password"
                                        v-model="formData.password"
                                        aria-describedby="password-feedback"
                                    ></b-form-input>
                                </b-input-group>
                            </b-col>
                        </b-row>
                        <b-row class="form-feedback message mt-3 mb-3">
                            <b-col cols="12">
                                <span>{{ feedback }}</span>
                            </b-col>
                        </b-row>
                        <b-row v-if="showForgotPassword" class="form-feedback mt-2">
                            <b-col cols="12">
                                <span>Forgot your password? <router-link :to="{name: 'emailPasswordRecovery'}">go and reset!</router-link></span>
                            </b-col>
                        </b-row>
                        <b-row class="form-feedback mt-5" align-v="center">
                            <b-col cols="8">
                                <b-form-checkbox
                                    id="remember-me"
                                    size="lg"
                                    v-model="formData.remember_me"
                                    >
                                   <span>Remember me</span>
                                </b-form-checkbox>
                            </b-col>
                            <b-col class="text-right" cols="4">
                                <b-button size="lg" :disabled="invalid" class="mt-3" type="submit">Login</b-button>
                            </b-col>
                        </b-row>
                        <b-row class="form-feedback mt-3">
                            <b-col cols="12" class="text-center">
                                <span>Don't have a account yet? <router-link :to="{name: 'createAccount'}">Sign Up!</router-link></span>
                            </b-col>
                        </b-row>
                        <b-row class="divider text-center mt-3">
                            <b-col class="line" cols="5">
                            </b-col>
                            <b-col cols="2">
                                <span>OR</span>
                            </b-col>
                            <b-col class="line" cols="5">
                            </b-col>
                        </b-row>
                        <b-row align-h="center" class="social mt-3">
                            <b-col cols="12" class="text-center">
                                <img style="width:220px;" src="/storage/social/fb.jpg" />
                            </b-col>
                        </b-row>
                        <b-row align-h="center" class="social mt-3">
                            <b-col cols="12" class="text-center">
                                <img style="width:220px;" src="/storage/social/googl.jpg" />
                            </b-col>
                        </b-row>
                    </b-container>
                </b-form>
            </validation-observer>
        </b-container>
    </div>
</template>
<script>

import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { mapActions, mapGetters, mapMutations } from 'vuex'

export default {
  name: 'login',
  components: { ValidationProvider, ValidationObserver },
  props: ['message'],
  data() {
    return {
      formData: {
        email: '',
        password: '',
        remember_me: false
      },
      showForgotPassword: true,
      feedback: ''
    }
  },
  computed: {
    ...mapGetters('auth', {
      getRememberMe: 'rememberMe'
    })
  },
  mounted() {
      this.feedback = this.$route.query.status ? this.$route.query.status : ''
      this.formData.email = this.$route.query.email ? this.$route.query.email : ''

    // auto login = goto home when rememberMe is true and accessToken is available
    // router params has message? 
      if(this.message) {
          this.showForgotPassword = false
          this.feedback += (' '+ this.message)
      }

      this.getRememberMe ? this.formData.remember_me = this.getRememberMe : this.formData.remember_me = false
      if(this.getRememberMe && this.getAccessToken) {
        this.$router.push({name: 'home'}).catch(()=>{})
      }
  },
  methods: {
    ...mapActions('auth', {
      login: 'login',
      retrieveUser: 'retrieveUser'
    }),
    ...mapActions('components', {
        initData: 'initData'
    }),
    ...mapMutations('auth', {
      setRememberMe: 'SET_REMEMBER_ME',
      setUser: 'SET_USER'
    }),
    // on submit login
    async onSubmit(evt) {

        evt.preventDefault()

        // set remember me for auto-login if form option is checked, clear if not
        this.setRememberMe(this.formData.remember_me)

        // login via formData
        let response = await this.login(this.formData)
        if(response.status != 200) {
            this.feedback = response.message
            return
        }

        // get minimum user details into local storage as logged in user (top menu)
        response =  await this.retrieveUser()
        if(response.status != 200) {
            this.feedback = response.message
            return
        }

        // get data for components
        response =  await this.initData()
        if(response.status != 200) {
            this.feedback = response.message
            return
        }

        // no errors, change route to myProfile and display user profile
        this.$router.push({name: 'myProfile'}).catch(()=>{})
    },
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null
    },
  }
}

</script>
<style lang="scss" scoped>

@import '~@/_variables.scss';

.modal-body {
    min-height: 25rem;
}

.form-title {
    font-size: 2rem;
}

.form-feedback, .invalid-feedback {
    line-height: 1.6rem;
    font-size: 1.4rem;
}

.invalid-feedback {
    color: $form-errors;
    display: inline-block;
}

.form-feedback.message {
    color: red;
}

.form-input-row {
    min-height: 5.5rem;
    margin-bottom: 1rem;
}

.row .line {
    border-bottom: solid 1px $form-divider;
    margin-bottom: 1rem;
}

</style>