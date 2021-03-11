<template>
  <b-container fluid>
    <b-row align-v="center" class="mt-3">
      <b-col cols="3">
        <VueCtkDateTimePicker
          id="users-start" label="Created from"
          input-size="sm"
          :error="dateTime.start.error"
          @input="dateTimePicked($event, dateTime, 'start')"
          :minDate="dateTime.start.min"
          :maxDate="dateTime.start.max"
          :format="dateTime.start.format"
          :formatted="dateTime.start.format"
          v-model="dateTime.start.selected">
        </VueCtkDateTimePicker>
      </b-col>
      <b-col cols="3">
        <VueCtkDateTimePicker
          id="users-end" label="Created to"
          input-size="sm"
          :error="dateTime.end.error"
          @input="dateTimePicked($event, dateTime, 'end')"
          :minDate="dateTime.end.min"
          :maxDate="dateTime.end.max"
          :format="dateTime.end.format"
          :formatted="dateTime.end.format"
          v-model="dateTime.end.selected">
        </VueCtkDateTimePicker>
      </b-col>
      <b-col cols="6" class="mt-2">
        <b-form-group label="Filter" label-cols-sm="1" label-align-sm="right" label-size="sm" label-for="filterInput">
          <b-input-group size="md">
            <b-form-input v-model="table.filter" debounce="500" type="search" id="filterInput" placeholder="Search user name"></b-form-input>
            <b-input-group-append>
              <b-button :disabled="!table.filter" @click="table.filter = ''">Clear</b-button>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row align-v="center" class="mt-2">
      <b-col cols="5">
        <b-button-group size="md">
          <b-button variant="primary" @click="editRow(table.lastSelectedRow)" :disabled="rowsSelected !== 1 ? true : false">Edit</b-button>
          <!-- <b-button variant="primary" :disabled="rowsSelected === 0" @click="clearSelected('table')">Clear selection</b-button> -->
          <b-button variant="danger" @click="deleteRows(table.selectedRows)" :disabled="rowsSelected === 0 ? true : false">Delete</b-button>
        </b-button-group>
      </b-col>
      <b-col cols="3">
        <b-row class="border-secondairy">
          <b-col cols="12">
            <b-pagination
              v-model="table.currentPage"
              :total-rows="table.totalRows"
              :per-page="table.perPage"
              align="left"
              size="md"
              class="mt-4"
            ></b-pagination>
          </b-col>
        </b-row>
      </b-col>

      <b-col cols="2">
        <b-form-group
          label="Per page"
          label-cols-sm="6"
          label-cols-md="6"
          label-cols-lg="6"
          label-align-sm="right"
          label-size="sm"
          label-for="perPageSelect"
          class=""
        >
          <b-form-select v-model="table.perPage" id="perPageSelect" size="sm" :options="table.pageOptions"></b-form-select>
        </b-form-group>
      </b-col>

      <b-col cols="2" class="mt-3 text-right">
        <p>Rows: <b>{{ table.totalRows }}</b></p>
      </b-col>
    </b-row>

    <b-row>
      <b-col cols="12">
        <b-table
          ref="userTable"
          :busy.sync="isBusy"
          :items="tableProvider"
          :fields="table.fields"
          :current-page="table.currentPage"
          :per-page="table.perPage"
          :sort-by.sync="table.sortBy"
          :sort-desc.sync="table.sortDesc"
          :filter="table.filter"
          :filterIncludedFields="table.filterOn"
          @filtered="(filteredItems) => onFiltered(filteredItems, 'table')"
          @row-selected="(item) => recordSelectedRows(item, 'table')"
          @row-clicked="(item) => recordLastSelectedRow(item, 'table')"
          fixed
          selectable
          sort-icon-left
          select-mode="single"
          hover
          responsive="sm"
        >
          <!-- style rows -->
          <template v-slot:table-colgroup="scope">
            <col v-for="field in scope.fields" :key="field.key" :style="field.style" />
          </template>

          <template v-slot:cell(name)="row">
            <div class="description">
                {{ row.item.name}}
            </div>
          </template>

          <template v-slot:cell(email)="row">
            <div class="description">
                {{ row.item.email }}
            </div>
          </template>

          <template v-slot:cell(isActive)="row">
            <div :class="'is-active-' + row.item.is_active">
              <b-form-checkbox 
                v-model="row.item.is_active"
                @change="updateRow({
                  is_active: !row.item.is_active,
                  id: row.item.id })">
              </b-form-checkbox>
            </div>
          </template>

          <template v-slot:cell(created_at)="row">
            <div class="description">
                {{ row.item.created_at }}
            </div>
          </template>

          <template v-slot:cell(selected)="row">
            <div class="w-100 hand-cursor text-center" @click="row.toggleDetails">
              <b-icon v-if="row.detailsShowing" variant="success" icon="eye"></b-icon>
              <b-icon v-else-if="row.rowSelected" variant="success" icon="eye-fill"></b-icon>
            </div>
          </template>

          <template v-slot:row-details="row">
            <b-card border-variant="light" class="font-size-small w-100">
              <b-row v-if="row.item.data" class="mb-2">
                {{ row.item.data }}
                <b-col cols="12">{{ 'about_me' in row.item.data ? row.item.data.about_me : ''}}</b-col>
              </b-row>
            </b-card>
          </template>
        </b-table>
        <div>
          Sort by <b>{{ table.sortBy }}, {{ table.sortDesc ? 'descending' : 'ascending' }}</b
          >.
        </div>
      </b-col>
    </b-row>
    <b-row class="text-right mt-4">
    <b-col>
    <p>{{feedback}}</p>
    </b-col>
    <b-col class="text-right">
    </b-col>
    </b-row>
    <MultiModal :modalData ="{
        size: 'xl',
        title: 'User',
        hideFooter: true
      }"
      @actions="tableActions"
    />
  </b-container>
</template>

<script>
import MultiModal from '~/components/modals/MultiModal.vue'
import tableMethods from '~/mixins/b-tableMethods'
import { mapGetters, mapActions } from 'vuex'

import VueCtkDateTimePicker from 'vue-ctk-date-time-picker'
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css'
import dateTime from '~/mixins/dateTime'

export default {
  name: 'users-table',
  mixins: [tableMethods, dateTime],
  components: {
    MultiModal,
    VueCtkDateTimePicker
  },
  data() {
    return {
      isBusy: false,
      table: {
        fields: [
          { key: 'name', sortable: true, style: 'width:50%', label:'Name' },
          { key: 'email', sortable: true, style: '', label:'Email' },
          { key: 'isActive', sortable: true, style: 'width:120px' },
          { key: 'created_at', sortable: true, label:'Created at' },
          { key: 'selected', sortable: false, style: 'width:80px', thClass: 'd-none' }
        ],
        selectedRows: null,
        lastSelectedRow: null,
        selectedNumRows: 0,
        currentPage: 1,
        perPage: 10,
        totalRows: 0,
        pageOptions: [5, 10, 15],
        sortBy: 'id',
        sortDesc: false,
        filter: '',
        filterOn: []
      },
      dateTime: {
        start: {
          error: false,
          min: this.getCurrentDateWithOffset( true, { years: -20 }),
          max: this.getCurrentDateWithOffset( true ),
          format: 'YYYY-MM-DD HH:mm',
          selected: this.getCurrentDateWithOffset( true, { months: -6 }),
        },
        end: {
          error: false,
          min: this.getCurrentDateWithOffset( true, { years: -20 }),
          max: this.getCurrentDateWithOffset( true, { days: 1 } ),
          format: 'YYYY-MM-DD HH:mm',
          selected: this.getCurrentDateWithOffset( true ),
        }
      },
      feedback: ''
    }
  },
  mounted() {
  },
  computed: {
    rowsSelected: function () {
      return this.table.selectedNumRows
    },
    ...mapGetters('resources', {
      getResources: 'resources',
      getResourcesTypes: 'resourceTypes'
    })
  },
  methods: {
    ...mapActions('users', {
        retrieveUsers: 'retrieveUsers'
    }),
    ...mapActions('resources', {
        retrieveResources: 'retrieveResources',
        patchResources: 'patchResources',
        destroyManyResources: 'destroyManyResources'
    }),
    exists: function (data) {
      return typeof variable === 'undefined' ? false : true
    },
    dateTimePicked($event, dateTime, action) {
      this.calcDateTime($event, dateTime, action)
      try {
        typeof this.$refs.userTable.refresh()
      } catch(err){
        this.feedback = err
        return
      }
    },
    tableProvider(ctx, callback){
      this.isBusy = true
      let params = 
          '?resourceType=UserProfile' +
          '&dateTime=start=' + this.getISODateWithOffset(this.dateTime.start.selected) +
              ',end='+ this.getISODateWithOffset(this.dateTime.end.selected) +
          '&page=' + ctx.currentPage +
          '&size=' + ctx.perPage +
          '&resourceUser=' + ctx.filter +
          '&orderBy=' + ctx.sortBy +
          '&order=' + (ctx.sortDesc ? 'desc' : 'asc')
          this.loadTable(params, callback)
    },
    // async table loader for Table provider
    async loadTable(params, callback) {
      let response = await this.retrieveResources(params)
      this.feedback = response.message
      this.table.totalRows = response.meta == null ? 0 : response.meta.total
      this.feedback = response.message
      this.isBusy = false
      callback(response.data)
    },
    async editRow(row){
      // show modal and selected content by route and parameter from row
      this.$bvModal.show('multi-modal')
      this.$router.push({ name: 'user', params: { id: row.id } }).catch(()=>{})
    },
    async deleteRows(rows){
      // create ids array of id's to delete
      let ids = rows.map(item => item.user_id);
      let response = await this.destroyManyResources(ids)
      if(response.status === 200) { this.feedback = 'User(s) deleted ' }
      this.feedback += response.message
      this.$refs.userTable.refresh()
    },
    async updateRow(patch) {
      // update changes to table without opening modal
      // patch contains field(s)
        let response = await this.patchResources(patch)
        this.feedback = response.message
        this.$refs.userTable.refresh()
    },
    tableActions(action) {
      switch(action)
        {
          case 'update':
            this.$refs.userTable.refresh()
            this.$router.push({ name: 'users'}).catch(()=>{})
          break
          case 'closed':
            this.$router.push({ name: 'users'}).catch(()=>{})
          break
        }
    }
  }
}
</script>