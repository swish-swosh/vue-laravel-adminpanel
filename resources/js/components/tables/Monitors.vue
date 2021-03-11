<template>
  <b-container fluid>
    <b-row align-v="center" class="mt-3">
      <b-col cols="6" class="mt-2">
        <b-form-group label="Filter" label-cols-sm="1" label-align-sm="right" label-size="sm" label-for="filterInput">
          <b-input-group size="sm">
            <b-form-input v-model="table.filter" debounce="500" type="search" id="filterInput" placeholder="Search monitor name"></b-form-input>
            <b-input-group-append>
              <b-button :disabled="!table.filter" @click="table.filter = ''">Clear</b-button>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <VueCtkDateTimePicker
          id="monitors-start" label="From next run"
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
          id="monitors-end" label="To next run"
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
    </b-row>
    <b-row align-v="center" class="mt-2">
      <b-col cols="5">
        <b-button-group size="sm">
          <b-button variant="primary" @click="addRow()">Add</b-button>
          <b-button variant="primary" @click="editRow(table.lastSelectedRow)" :disabled="rowsSelected !== 1 ? true : false">Edit</b-button>
          <b-button variant="primary" :disabled="rowsSelected === 0" @click="clearSelected('table')">Clear selection</b-button>
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
              size="sm"
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
          ref="monitorsTable"
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
          select-mode="multi"
          hover
          responsive="sm"
        >
          <!-- style rows -->
          <template v-slot:table-colgroup="scope">
            <col v-for="field in scope.fields" :key="field.key" :style="field.style" />
          </template>

          <template v-slot:cell(name)="row">
            <div class="name">
                {{ row.item.data.name }}
            </div>
          </template>

          <template v-slot:cell(next_run)="row">
            <div class="next-run">
                {{ row.item.data.nextRun }}
            </div>
          </template>

          <template v-slot:cell(logType)="row">
            <div class="monitor-type">
                {{ row.item.resource_type }}
            </div>
          </template>

          <!-- <template v-slot:cell(user)="row">
            <div class="user">
              <b-form-select
                id="user-input"
                name="user-input"
                placeholder="Owner"
                v-model="row.item.user_id"
                @change="updateRow({
                  user_id: row.item.user_id,
                  id: row.item.id })"
                :options="users"
                value-field="id"
                text-field="name"
                aria-describedby="user-input-feedback"
              ></b-form-select>
            </div>
          </template> -->

          <template v-slot:cell(user)="row">
            <div class="user">
              {{ row.item.user }}
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
              <b-row v-if="row.item.data.description" class="mb-2">
                <b-col cols="12">{{ row.item.data.description }}</b-col>
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
    <b-row class="text-right mt-4 w-100">
        <b-col>
            <p>{{feedback}}</p>
        </b-col>
    </b-row>
    <MultiModal :modalData ="{
        size: 'xl',
        title: 'Monitors',
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
  name: 'monitors-table',
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
          { key: 'id', sortable: true, style: 'width:20px' },
          { key: 'data.name', sortable: true, style: 'width:30%', label: 'Name' },
          { key: 'next_run', sortable: true, style: '', label: 'Next run' },
          { key: 'resource_type', sortable: true, style: '', label: 'Type' },
          { key: 'user', sortable: true, style: '', label: 'Owner' }
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
          max: this.getCurrentDateWithOffset( true, { years: +20 } ),
          format: 'YYYY-MM-DD HH:mm',
          selected: this.getCurrentDateWithOffset( true, { months: -6 } ),
        },
        end: {
          error: false,
          min: this.getCurrentDateWithOffset( true, { years: -20 }),
          max: this.getCurrentDateWithOffset( true, { years: +20 }),
          format: 'YYYY-MM-DD HH:mm',
          selected: this.getCurrentDateWithOffset( true, { months: +6 } ),
        }
      },
      feedback: '',
      users: [] // for select list
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
      dateTimePicked($event, dateTime, action) {
        this.calcDateTime($event, dateTime, action)
        try {
          typeof this.$refs.monitorsTable.refresh()
        } catch(err){
          // console.log(err)
          return
        }
      },
      tableProvider(ctx, callback){
        this.isBusy = true
        let params = 
          '?resourceTypeGroup=Monitors' +
          '&nextRunDateTime=start=' + this.getISODateWithOffset(this.dateTime.start.selected) +
              ',end='+ this.getISODateWithOffset(this.dateTime.end.selected) +
          '&page=' + ctx.currentPage +
          '&size=' + ctx.perPage +
          '&filter=' + ctx.filter +
          '&orderBy=' + ctx.sortBy +
          '&order=' + (ctx.sortDesc ? 'desc' : 'asc')
        this.loadTable(params, callback)
      },
      // async table loader for Table provider
      async loadTable(params, callback) {
          let response = await this.retrieveResources(params)
          this.feedback = response.message
          this.table.totalRows = response.meta.total
          this.feedback = response.meta.total == 0 ? 'no data available' : response.message
          this.isBusy = false
          callback(response.data)
      },
      editRow(row){
        // show modal and selected content by route and parameter from row
        this.$bvModal.show('multi-modal')
        this.$router.push({ name: 'monitor', params: { id: row.id } }).catch(()=>{})
      },
      addRow(){
        // show modal and and signal new item via parameter 'new'
        this.$bvModal.show('multi-modal')
        this.$router.push({ name: 'monitor', params: { id: 'new' } }).catch(()=>{})
      },
      async deleteRows(rows){
        // create ids array of id's to delete
        let ids = rows.map(item => item.id);
        let response = await this.destroyManyResources(ids)
        if(response.status === 200) { this.feedback = 'Monitor(s) deleted ' }
        this.feedback += response.message
        this.$refs.monitorTable.refresh()
      },
      async updateRow(patch) {
        // update changes to table without opening modal
        // patch contains field(s)
        let response = await this.patchResources(patch)
        this.feedback = response.message
        this.$refs.taskTable.refresh()
      },
      tableActions(action) {
        switch(action)
          {
            case 'update':
              this.$router.push({ name: 'monitors'}).catch(()=>{})
              this.loadTable()
            break
            case 'closed':
              this.$router.push({ name: 'monitors'}).catch(()=>{})
            break
          }
      }
  }
}
</script>