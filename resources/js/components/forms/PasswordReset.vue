<template>
    <div>
        <b-container fluid>
            <validation-observer ref="observer" v-slot="{ invalid }">
                <b-form @submit="onSubmit">
                    <b-container class="action input">
                        <b-row class="form-title">
                            <b-col cols="12">Password reset:</b-col>
                        </b-row>
                        <ValidationObserver>
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
                                    <ValidationProvider name="password" rules="required|min:6|password:@confirm" v-slot="validationContext">
                                        <b-input-group size="lg" class="mt-2">
                                            <b-input-group-prepend is-text>
                                                <b-icon icon="lock"></b-icon>
                                            </b-input-group-prepend>
                                            <b-form-input
                                                id="password"
                                                name="password"
                                                placeholder="password"
                                                type="password"
                                                v-model="formData.password"
                                                :state="getValidationState(validationContext)"
                                                aria-describedby="password-feedback"
                                            ></b-form-input>
                                            <b-form-invalid-feedback class="invalid-feedback" id="password-feedback">
                                                {{ validationContext.errors[0] }}
                                            </b-form-invalid-feedback>
                                        </b-input-group>
                                    </ValidationProvider>
                                </b-col>
                            </b-row>
                            <b-row class="form-input-row">
                                <b-col cols="12">
                                    <ValidationProvider name="confirm" rules="required" v-slot="validationContext">
                                        <b-input-group size="lg" class="mt-2">
                                            <b-input-group-prepend is-text>
                                                <b-icon icon="lock-fill"></b-icon>
                                            </b-input-group-prepend>
                                            <b-form-input
                                                id="password-confirmation"
                                                name="password-confirmation"
                                                placeholder="confirm password"
                                                type="password"
                                                v-model="formData.password_confirmation"
                                                :state="getValidationState(validationContext)"
                                                aria-describedby="password-confirm-feedback"
                                            ></b-form-input>
                                            <b-form-invalid-feedback class="invalid-feedback" id="password-confirm-feedback">
                                                {{ validationContext.errors[0] }}
                                            </b-form-invalid-feedback>
                                        </b-input-group>
                                    </ValidationProvider>
                                </b-col>
                            </b-row>
                        </ValidationObserver>
                        <b-row class="form-feedback message mt-3 mb-3">
                            <b-col cols="12">
                                <span>{{ feedback }}</span>
                            </b-col>
                        </b-row>

                        <b-row v-if="showForgotPassword" class="form-feedback mt-2">
                            <b-col cols="12">
                                <span><router-link :to="{name: 'emailPasswordRecovery'}">Get a new password reset link!</router-link></span>
                            </b-col>
                        </b-row>

                        <b-row  v-if="showLoginRoute" class="form-feedback mt-2">
                            <b-col cols="12">
                                <span><router-link :to="{name: 'login'}">Proceed to login</router-link></span>
                            </b-col>
                        </b-row>

                        <b-row class="form-feedback mt-5" align-v="center">
                            <b-col cols="8">
                            </b-col>
                            <b-col class="text-right" cols="4">
                                <b-button size="lg" :disabled="invalid" class="mt-3" type="submit">Update password</b-button>
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
  name: 'password-reset',
  components: { ValidationProvider, ValidationObserver },
  data() {
    return {
      formData: {
        email: '',
        password: '',
        token: ''
      },
      feedback: '',
      showForgotPassword: false,
      showLoginRoute: false
    }
  },
  mounted() {
        this.formData.email = this.$route.query.email ? this.$route.query.email : ''
        this.formData.token = this.$route.query.token ? this.$route.query.token : ''
  },
  methods: {
    ...mapActions('auth', {
      passwordReset: 'passwordReset'
    }),
    // on submit login
    async onSubmit(evt) {

        evt.preventDefault()
        // passwordReset via formData credentials
        let response = await this.passwordReset(this.formData)
        if(response.status != 200) {
            this.feedback = response.message
            this.showForgotPassword = true
            return
        }
        this.feedback = response.message
        this.showLoginRoute = true
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