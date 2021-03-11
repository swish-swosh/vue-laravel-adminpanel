<template>
  <validation-observer ref="observer" v-slot="{ invalid }">
    <b-form @submit="onSubmit($event, formData)">
      <b-container fluid>
      <b-row class="mb-3">
        <b-col cols="7">
          <ValidationProvider name="due" rules="" v-slot="validationContext">
            <b-input-group id="input-group-1">

              <VueCtkDateTimePicker
                id="start" label="Task due date - time"
                @input="dateTimePicked($event, 'start')"
                :minDate="dateTime.minDateStart"
                :maxDate="dateTime.maxDateStart"
                :format="dateTime.format"
                :formatted="dateTime.format"
                v-model="formData.data.due">
              </VueCtkDateTimePicker>

              <b-form-invalid-feedback id="due-input-feedback">
                {{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-input-group>
          </ValidationProvider>
        </b-col>
        <b-col cols="5">
          <b-form-checkbox id="active-input" 
          v-model="formData.is_active"
          class="mt-2">Task is active</b-form-checkbox>
        </b-col>
      </b-row>
      <b-row class="mb-3">
        <b-col>
          <ValidationProvider name="user" rules="required" v-slot="validationContext">
            <b-input-group id="input-group-2">
              <b-input-group-prepend is-text>
                <b-icon icon="person-fill"></b-icon>
              </b-input-group-prepend>
              <b-form-select
                id="user-input"
                name="user-input"
                placeholder="Task assigned to"
                v-model="formData.user_id"
                :options="users"
                value-field="user_id"
                text-field="name"
                :state="getValidationState(validationContext)"
                aria-describedby="user-input-feedback"
              ></b-form-select>
              <b-form-invalid-feedback id="user-input-feedback">
                {{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-input-group>
          </ValidationProvider>
        </b-col>
      </b-row>
      <b-row class="mb-3">
        <b-col>
          <ValidationProvider name="name" rules="required" v-slot="validationContext">
            <b-input-group id="input-group-3">
              <b-input-group-prepend is-text>
                <b-icon icon="tag-fill"></b-icon>
              </b-input-group-prepend>
              <b-form-input
                id="name-input"
                name="name-input"
                placeholder="Task name"
                v-model="formData.data.name"
                :state="getValidationState(validationContext)"
                aria-describedby="name-input-feedback"
              ></b-form-input>
              <b-form-invalid-feedback id="name-input-feedback">
                {{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-input-group>
          </ValidationProvider>
        </b-col>
      </b-row>
      <b-row class="">
        <b-col>
          <ValidationProvider name="description" rules="" v-slot="validationContext">
            <label class="mb-1" label-for="description-input">Description:</label>
            <b-input-group id="input-group-4">
              <b-form-textarea
                rows="10"
                max-rows="15"
                id="description-input"
                name="description-input"
                placeholder=""
                v-model="formData.data.description"
                :state="getValidationState(validationContext)"
                aria-describedby="description-input-feedback"
              ></b-form-textarea>
              <b-form-invalid-feedback id="description-input-feedback">
                {{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-input-group>
          </ValidationProvider>
        </b-col>
      </b-row>
      <b-row class="mt-4">
        <b-col class="text-right">
        <p>{{feedback}}</p>
        </b-col>
      </b-row>
      <!-- custom footer buttons -->
      <b-row class="mt-3 mb-0 w-100">
        <b-col class="text-left">
            <b-button @click="$emit('actions', 'closeModal')" variant="primary" size="md" class="float-left">
            <span>Cancel</span>
          </b-button>
          <b-button type="submit" :disabled="invalid" variant="primary" size="md" class="float-right">
            <span v-if="id=='new'">Add task</span>
            <span v-else>Update task</span>
          </b-button>
        </b-col>
      </b-row>
      </b-container>
    </b-form>
  </validation-observer>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { ValidationObserver } from 'vee-validate'
import { mapGetters, mapActions } from 'vuex'
import formMethods from '~/mixins/b-formMethods'
import helpers from '~/mixins/helpers'

import VueCtkDateTimePicker from 'vue-ctk-date-time-picker'
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css'

export default {
  name: 'task-form',
  mixins: [formMethods, helpers],
  components: {
    ValidationProvider,
    ValidationObserver,
    VueCtkDateTimePicker
  },
  data() {
    return {
      dateTime: {
        start: '',
        minDateStart: '',
        maxDateStart: '',
        end: '',
        minDateEnd: '',
        maxDateEnd: '',
        format: 'YYYY-MM-DD HH:mm'
      },
      formData: {
          id: 0,
          data: {
            due: null,
            name: '',
            description: ''
          },
          is_active: false,
          user_id: 0,
      },
      feedback: '',
      users: [],
      isNewTask: true
    }
  },
  props: ['id'],
  computed: {
  },
  mounted() {

      // get userlist where we have access to.
      this.getUserList(this.setUserList)

      // init data if id available / use empty form (for new) if not
      if(this.isValidId(this.id)) {
        this.isNewTask = false
        this.loadForm(this.id);
      } else
      this.isNewTask = true
  },
  methods: {
    ...mapActions('resources', {
        retrieveResources: 'retrieveResources',
        retrieveResource: 'retrieveResource',
        createResource: 'createResource',
        patchResource: 'patchResource'
    }),
    async getUserList(callback) {
      let params = 'resources?resourceType=UserProfile'
      let response = await this.retrieveResources(params)
      this.feedback = '.'
      callback(response.data.filter(users => users.name.length > 0))
    },
    setUserList(users) {
      this.users = users
    },
    async loadForm(id) {

      // load form with resource id
      response = await this.retrieveResource({
        'id': id
      })
      this.feedback += ('. ' + response.message)
      this.formData = response.data
    },
    getValidationState({ dirty, validated, valid = null }) {
        return dirty || validated ? valid : null
    },
    dateTimePicked($event, action) {
      this.formData.data.due = $event
    },
    async onSubmit(evt, formData) {
      evt.preventDefault()
      // create new task ?
      if(this.isNewTask) {
        try {
          console.log('start')
          let response = await this.createResource(this.formData)
          console.log(response)
          this.$emit('actions', 'formUpdated-closeModal')
        } catch(err) {
          this.feedback = err.response
        }
      } else
      {
        try {
          let response = await this.patchResource(this.formData)
          this.$emit('actions', 'formUpdated-closeModal')
        } catch(err) {
          this.feedback = err.response
        }
      }
    }
  }
}
</script>

<style scoped></style>
