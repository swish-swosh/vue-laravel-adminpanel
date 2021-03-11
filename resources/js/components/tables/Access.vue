<template>
  <b-container fluid>
    <b-row align-h="center">
      <b-col cols="12">
        <b-table-simple hover caption-top responsive>
          <b-thead head-variant="dark">
            <b-tr>
              <b-th class="w-25">Resource</b-th>
              <b-th>Role</b-th>
              <b-th class="text-center">Create</b-th>
              <b-th class="text-center">Read</b-th>
              <b-th class="text-center">Update</b-th>
              <b-th class="text-center">Delete</b-th>
            </b-tr>
          </b-thead>

          <b-tbody>
              <template v-for="resourceAccess in resourceAccesses">
                <b-tr v-for="(role, n) in resourceAccess.roles" :key="resourceAccess.name+n">
                  <b-th class="w-25" v-if="n==0" :rowspan="resourceAccess.roles.length">{{resourceAccess.name}}</b-th>
                  <b-td>{{role.name}}</b-td>
                  <b-td class="text-center">
                    <b-form-checkbox
                        v-model="role.access.can_create"
                        @input="updateRow(resourceAccess.id, role )">
                    </b-form-checkbox>
                  </b-td>
                  <b-td class="text-center">
                    <b-form-checkbox
                        v-model="role.access.can_read"
                        @input="updateRow(resourceAccess.id, role )">
                    </b-form-checkbox>
                  </b-td>
                  <b-td class="text-center">
                    <b-form-checkbox
                        v-model="role.access.can_update"
                        @input="updateRow(resourceAccess.id, role )">
                    </b-form-checkbox>
                  </b-td>
                  <b-td class="text-center">
                    <b-form-checkbox
                        v-model="role.access.can_delete"
                        @input="updateRow(resourceAccess.id, role )">
                    </b-form-checkbox>
                  </b-td>
                </b-tr>
              </template>
          </b-tbody>

          <b-tfoot>
            <b-tr>
              <b-td colspan="8" variant="secondary" class="text-right">
                {{feedback}}
              </b-td> 
            </b-tr>
          </b-tfoot>
        </b-table-simple>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import tableMethods from '~/mixins/b-tableMethods'
import { mapGetters, mapActions } from 'vuex'
export default {
  name: 'access-table',
  mixins: [tableMethods],
  components: {
  },
  data() {
    return {
      feedback: '',
      resourceAccesses: []
    }
  },
  mounted() {
    // prepare table data
    this.loadTable()
  },
  computed: {
    ...mapGetters('components', {
        getRoles: 'roles',
        getPolicies: 'policies',
        getResourceTypes: 'resourceTypes'
    })
  },
  methods: {
      ...mapActions('accessRights', {
          retrieveAccessRights: 'retrieveAccessRights',
          patchAccessRights: 'patchAccessRights',
          destroyAccessRights: 'destroyAccessRights'
      }),
      ...mapActions('resources', {
          retrieveResourceAccesses: 'retrieveResourceAccesses'
      }),

      async updateRow(type, role) {

        // mutate table data so it can be imported as a object into the database
        // Set the initials
        let self = this
        let grants = []

        // get selected grants array with id's selected (checkboxes) for role -> policy -> id's array
        patch.grants.forEach((grant) => {
          if(grant.state===true) grants.push(grant.id)
        })

        // update patch data
        patch = {
            id: patch.id, // access to
            role: 'role'+patch.role_id,
            policy: patch.policyName,
            grants: grants
        }

        let response = await this.patchAccessRights(patch)
        if(response.status === 200) {
          this.feedback = 'Resource access policy updated'
        }
          this.feedback += response.message
          
      },
      async loadTable(){
        const response = await this.retrieveResourceAccesses()
        this.feedback = response.message
        this.resourceAccesses = response.data          
      }
  }
}
</script>