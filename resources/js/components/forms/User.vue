<template>
    <validation-observer ref="observer" v-slot="{ invalid }">
      <b-form @submit="onSubmit">
        <b-container fluid>
          <b-row>
            <b-col cols="12" md="6"> 
              <b-form-group class="mt-2" id="name" label="Name" label-for="i-user" rules="required">
                    <b-form-input
                    id="i-user"
                    v-model="formData.name"
                    placeholder="Enter your first and last name"
                    ></b-form-input>
                </b-form-group>
            </b-col>

            <b-col cols="12" md="6">
                <b-form-group
                  class="mt-2"
                  id="user-email"
                  label="Email"
                  label-for="i-email"
                  placeholder=""
                  description="Your e-mail is used as login, it cannot be changed "
                >
                <b-form-input
                    id="i-email"
                    readonly
                    v-model="formData.email"
                    type="email"
                ></b-form-input>
                </b-form-group>
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="12" md="1">
              <b-form-group class="mt-2" id="user-is_active" label="Is active" label-for="i-is_active">
                <b-form-checkbox
                  id="i-is_active"
                  v-model="formData.is_active">
                </b-form-checkbox>
              </b-form-group>
            </b-col>
            <b-col cols="12" md="5">
              <ValidationProvider name="company" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-company" label="Company" label-for="i-company">
                  <b-form-input
                    id="i-company"
                    v-model="formData.data.company"
                    :state="getValidationState(validationContext)"
                    placeholder="Company name"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-company">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
            <b-col cols="12" md="6">
              <ValidationProvider name="vat" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-vat" label="Vat" label-for="i-vat">
                  <b-form-input
                    id="i-vat"
                    v-model="formData.data.vat"
                    :state="getValidationState(validationContext)"
                    placeholder="Company vat number"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-vat">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="12" md="6">
              <ValidationProvider name="function" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-function" label="Function*" label-for="i-function">
                  <b-form-input
                    id="i-function"
                    v-model="formData.data.function"
                    :state="getValidationState(validationContext)"
                    placeholder="Your function"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-function">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
            <b-col cols="12" md="6">
              <ValidationProvider name="department" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-department" label="Department" label-for="i-department">
                  <b-form-input
                    id="i-department"
                    v-model="formData.data.department"
                    type="text"
                    :state="getValidationState(validationContext)"
                    placeholder="Your department"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-department">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12" md="6">
              <ValidationProvider name="telephone" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-telephone" label="Telephone" label-for="i-telephone">
                  <b-form-input
                    id="i-telephone"
                    v-model="formData.data.telephone"
                    :state="getValidationState(validationContext)"
                    placeholder="Enter your telephone number"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-telephone">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
            <b-col cols="12" md="6">
              <ValidationProvider name="mobile" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-mobile" label="Mobile" label-for="i-mobile">
                  <b-form-input
                    id="i-mobile"
                    v-model="formData.data.mobile"
                    :state="getValidationState(validationContext)"
                    placeholder="Enter your mobile number"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-mobile">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12" md="6">
              <ValidationProvider name="address" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-address" label="Address" label-for="i-address">
                  <b-form-input
                    id="i-address"
                    v-model="formData.data.address"
                    :state="getValidationState(validationContext)"
                    placeholder="Enter your address"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-address">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
            <b-col cols="12" md="2">
              <ValidationProvider name="Postcode" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-postal-code" label="Postal code" label-for="i-post-code">
                  <b-form-input
                    id="i-post-code"
                    v-model="formData.data.postal_code"
                    :state="getValidationState(validationContext)"
                    placeholder="Enter your postal code"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-post-code">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
            <b-col cols="12" md="4">
              <ValidationProvider name="city" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-city" label="City" label-for="i-city">
                  <b-form-input
                    id="i-city"
                    v-model="formData.data.city"
                    :state="getValidationState(validationContext)"
                    placeholder="Enter your city of residence"
                  ></b-form-input>
                </b-form-group>
                <b-form-invalid-feedback id="i-city">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12" md="6">
              <ValidationProvider rules="image|size:250"  v-slot="{ validate, errors }">
                <b-form-group class="mt-2" id="user-image" label="User image" label-for="i-user-image">
                  <b-input-group>
                  <b-form-file
                    type="file"
                    id="i-user-image"
                    @change="fileUploadAndPreview($event, userImage),
                              fileUpload(userImage.fileUpload)"
                    drop-placeholder="Drop file here..."
                    :state="getValidationState(validate)"
                    placeholder="Choose image..."
                  ></b-form-file>
                  <b-input-group-append class="ml-2" style="cursor:pointer">
                    <b-input-group-text @click="fileUploadAndPreviewCancel(userImage), formData.data.user_image= ''">
                      <b-icon icon="x" />
                    </b-input-group-text>
                  </b-input-group-append>
                  </b-input-group>
                  <span v-if="errors[0]">{{ errors[0] + ' (250KB)' }}</span>
                </b-form-group>
                
              </ValidationProvider>
            </b-col>
            <b-col cols="12" md="6">
              <ValidationProvider name="country" rules="" v-slot="validationContext">
                <b-form-group class="mt-2" id="user-country" label="Country" label-for="i-country">
                  <b-form-select
                    id="i-country"
                    v-model="formData.data.country_id"
                    :options="getCountries"
                    value-field="id"
                    text-field="name"
                    :state="getValidationState(validationContext)"
                    placeholder="Enter your country"
                  ></b-form-select>
                </b-form-group>
                <b-form-invalid-feedback id="i-country">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </ValidationProvider>
            </b-col>
          </b-row>
          <b-row class="text-center mt-4">
            <b-col> <!-- 1 upload preview available, 2 uploadPreview not available but profile image is, 3 nothing available -->
              <b-img v-if="userImage.filePreview" class="mt-3" width="100" height="100" :src="userImage.filePreview" rounded="circle" alt="Circle image"></b-img>
              <b-img v-if="!userImage.filePreview && formData.data.user_image" class="mt-3" width="100" height="100" :src="'/storage/uploads/'+formData.data.user_image" rounded="circle" alt="Circle image"></b-img>
              <b-img v-if="!userImage.filePreview && !formData.data.user_image" style="opacity: 0.2" class="mt-3" width="100" height="100" src="/storage/uploads/incognito.png" rounded="circle" alt="Circle image"></b-img>
            </b-col>
            <b-col class="">
            <b-form-group class="mt-2" id="user-about-me" label="About me" label-for="i-about-me">
            <b-form-textarea
                id="i-about-me"
                v-model="formData.data.about_me"
                placeholder="Describe yourself..."
                rows="6"
                max-rows="15"
              ></b-form-textarea>
            </b-form-group>
            </b-col>
          </b-row>
          <b-row class="text-center mt-4">
            <b-col>

              <b-form-checkbox-group v-model="formData.roles" name="roles">
              <b-table  :fields="roleFields" :items="getRoles" responsive="sm">
                <template v-slot:cell(name)="data">
                  {{ data.value }}
                </template>

                <!-- show checkbox if user is administrator / icon if not (read only) -->
                <template v-if="userHasAnyRoles(['Admins', 'superAdmin'])" v-slot:cell(granted)="data">
                  <b-form-checkbox :value="data.item.id"></b-form-checkbox>
                </template>
                <template v-else v-slot:cell(granted)="data">
                  <b-icon v-if="data.value" icon="check" aria-hidden="true"></b-icon>
                </template>

              </b-table>
              </b-form-checkbox-group>
            </b-col>
          </b-row>
          <b-row class="text-right mt-1">
            <b-col>
            <p>{{feedback}} </p>
            </b-col>
          </b-row>
          <!-- custom footer buttons -->
          <b-row class="mt-1 mb-0 w-100">
            <b-col class="text-left">
                <b-button @click="$emit('actions', 'closeModal')" variant="primary" size="md" class="float-left">
                <span>Cancel</span>
              </b-button>
              <b-button type="submit" :disabled="invalid || isBusy" variant="primary" size="md" class="float-right">
                <span>Update</span>
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
import formMethods from '~/mixins/b-formMethods'
import helpers from '~/mixins/helpers'
import {globals} from '~/store/index.js'
import axios from 'axios'

import { mapGetters, mapActions, mapMutations } from 'vuex'

export default {
    name: 'user-form',
    props: ['id', 'showAuthenticatedUser'], // route-viewer
    components: {
        ValidationProvider,
        ValidationObserver
    },
    data() {
        return {
            formData: {
              id: '', // resource id
              is_active: false,
              user_id: '',
              name: '',
              email: '',
              data: { // resource payload
                company: '',
                vat: '',
                function: '',
                department: '',
                telephone: '',
                mobile: '',
                address: '',
                postal_code: '',
                city: '',
                country_id: null,
                user_image: '',
                background_image: '',
                about_me: ''
              }
            },
            roles: [],
            roleFields: [
              { key: 'name', label: 'Role' },
              {
                key: 'granted',
                label: 'Granted',
                formatter: (value, key, item) => {
                    let r = this.roles.find(x => {
                      return x === item.id       // return true if role selected
                    })
                    return typeof r !== "undefined"
                }
              }
            ],
            userImage: {
              filePreview: null,
              fileUpload: null
            },
            feedback: null,
            isBusy: false,
            isAuthUser: false
        }
    },
    computed: {
      ...mapGetters('auth', {
          getUser: 'user',
          userHasAnyRoles: 'hasAnyRoles'
      }),
      ...mapGetters('components', {
          getCountries: 'countries',
          getRoles: 'roles'
      })
    },
    mixins: [formMethods, helpers],
    mounted() {

      let userId;

      // resource id supplied? (router)
      if(this.isValidId(this.id)) {
        // authenticated user loaded?
        this.isAuthUser = this.getUser.resource_id == this.id ? true : false
        userId = this.id
      }else
      { // show auth user when id is not supplied, 
        // new users can only be created on registration
       this.isAuthUser = true
       userId = this.getUser.resource_id
      }

      this.loadForm(userId)
      

    },
    methods: {
      ...mapActions('resources', {
        retrieveResource: 'retrieveResource',
        retrieveResources: 'retrieveResources',
        createResource: 'createResource',
        patchResource: 'patchResource'
      }),
      ...mapMutations('auth', {
        setUser: 'SET_USER'
      }),
      async loadForm(id) {

        // load user profile with resource_id on router
        let response = await this.retrieveResource({
          'id': id
        })
        this.feedback = response.message
        this.formData = response.data

        // get user roles
        let response = await this.retrieveResource({
          'id': id
        })
        this.feedback += response.message
        this.roles = response.data

      },
      async onSubmit(evt) {
        evt.preventDefault()
        // updating user profiles resources is always a patch, users get created on registration
        try {
          let response = await this.patchResource(this.formData)
          // update user profile in local storage, for when auth user is updated
          // for instand updating logged in user
          if(this.isAuthUser) this.setUser({
              id: response.data.user_id,
              name: response.name,
              email: response.email,
              roles: response.data.roles,
              resource_id: response.id,
              user_image: response.data.data.user_image
          })
          this.feedback = response.message
          this.$emit('actions', 'formUpdated-closeModal')
        } catch(err) {
          this.feedback = err.response
        }

      }, // formvalidations
      getValidationState({ dirty, validated, valid = null }) {
          return dirty || validated ? valid : null
      },
      // use the getFormdata mixin to create multiple (or single) file uploads (uses formData)
      // use an object to represent the file(s): {file1: filedata, file2: filedata}
      // feel free to choose the key values, the post will
      // return an object with chosenKey -> value = the filename (string) created
      async fileUpload(upload) {
        if(upload instanceof File) {
          this.isBusy = true
          let files = { profileImage: upload }
          let uploadData = this.getFormData(files)
          try {
            const response = await axios.post(
                globals.baseUrlBackend + globals.APIVersion + 'fileUploads',
                uploadData
            )
            this.feedback = response.data.statusText
            this.formData.data.user_image = response.data.filenames.profileImage
          } catch(err) {
            this.feedback = err.response
          }
          this.isBusy = false
        }
      }
  }
}
</script>

<style lang="scss" scoped>

@import '~@/_variables.scss';

</style>
