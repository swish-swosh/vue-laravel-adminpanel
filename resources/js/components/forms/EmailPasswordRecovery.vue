<template>
    <div>
        <b-container fluid>
            <validation-observer ref="observer" v-slot="{ invalid }">
                <b-form @submit="onSubmit">
                    <b-container class="action input">

                        <b-row class="form-title">
                            <b-col cols="12">Password recovery:</b-col>
                        </b-row>
                        <b-row class="form-feedback mt-5">
                            <b-col cols="12">
                                <span>Supply your email address so we can send you a recovery link!</span>
                            </b-col>
                        </b-row>
                        <b-row class="form-input-row">
                            <b-col cols="12">
                                <ValidationProvider name="login-name" rules="required|email" v-slot="validationContext">
                                    <b-input-group size="lg" class="mt-3">
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
                                        <b-form-invalid-feedback class="invalid-feedback">
                                            {{ validationContext.errors[0] }}
                                        </b-form-invalid-feedback>
                                    </b-input-group>
                                </ValidationProvider>
                            </b-col>
                        </b-row>
                        <b-row class="form-feedback message mt-3 mb-3">
                            <b-col cols="12">
                               <span>{{ feedback }}</span>
                            </b-col>
                        </b-row>
                        <b-row class="form-feedback mt-2">
                            <b-col cols="12">
                                <span>Remembered your password? <router-link :to="{name: 'login'}">return to login!</router-link></span>
                            </b-col>
                        </b-row>
                        <b-row class="form-feedback mt-1" align-v="center">
                            <b-col cols="8">
                            </b-col>
                            <b-col class="text-right" cols="4">
                                <b-button size="lg" :disabled="invalid" class="mt-3" type="submit">Send</b-button>
                            </b-col>
                        </b-row>
                        <b-row class="divider text-center mt-2">
                            <b-col class="line" cols="5">
                            </b-col>
                            <b-col cols="2">
                                <span>OR</span>
                            </b-col>
                            <b-col class="line" cols="5">
                            </b-col>
                        </b-row>
                        <b-row class="form-feedback mt-3">
                            <b-col cols="12" class="text-center">
                                <span>Don't have a account yet? <router-link :to="{name: 'createAccount'}">Sign Up!</router-link></span>
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
import { mapActions, mapGetters } from 'vuex'
export default {
  name: 'password-reset',
  components: { ValidationProvider, ValidationObserver },
  data() {
    return {
      formData: {
        email: ''
      },
      feedback: ''
    }
  },
  mounted() {
  },
  methods: {
    ...mapActions('auth', {
      emailPasswordRecovery: 'emailPasswordRecovery'
    }),
    async onSubmit(evt) {
        evt.preventDefault()

        let response = await this.emailPasswordRecovery(this.formData)

        if(response.status != 200) {
            this.feedback = response.message
            return
        }
        
        this.feedback = response.message
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
    min-height: 6rem;
    margin-bottom: 2rem;
}

.row .line {
    border-bottom: solid 1px $form-divider;
    margin-bottom: 1rem;
}

.social span {
    font-size: 1.4rem;
}
</style>