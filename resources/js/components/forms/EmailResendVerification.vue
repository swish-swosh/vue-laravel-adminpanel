<template>
    <div>
        <b-container fluid>
            <validation-observer ref="observer" v-slot="{ invalid }">
                <b-form @submit="onSubmit">
                    <b-container class="action input">

                        <b-row class="form-title">
                            <b-col cols="12">Resend verification mail</b-col>
                        </b-row>
                        <b-row class="form-input-row">
                            <b-col cols="12" class="min-input-col-height">
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
                        <b-row class="form-input-row">
                            <b-col cols="12">
                                <div class="feedback-message mt-2">
                                        <span>{{ feedback }}</span>
                                </div>
                            </b-col> 
                        </b-row>
                        <b-row class="form-feedback mt-5" align-v="center">
                            <b-col cols="8">
                            </b-col>
                            <b-col class="text-right" cols="4">
                                <b-button size="lg" :disabled="invalid" class="mt-3" type="submit">Send</b-button>
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
  name: 'resend-verification',
  components: { ValidationProvider, ValidationObserver },
  data() {
    return {
      formData: {
        email: ''
      },
      feedback: 'If the email verification link is expired, you can resend the link here'
    }
  },
  mounted() {
    // load emailaddress via url param, if not supplied, present empty form field
    this.formData.email =  this.$route.query.email ? this.$route.query.email : ''

  },
  methods: {
    ...mapActions('auth', {
      emailResendNotification: 'emailResendNotification'
    }),
    async onSubmit(evt) {

        evt.preventDefault()
        let response = await this.emailResendNotification(this.formData)

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

.modal-body {
  height: 4rem;
}

.feedback-message {
    color: red;
    font-size: small;
}
</style>