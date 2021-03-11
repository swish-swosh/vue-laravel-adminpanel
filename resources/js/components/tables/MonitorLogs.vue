<template>
  <b-container fluid>
    <b-row align-v="center" class="mt-3">
      <b-col cols="6" class="mt-2">
        <b-form-group label="Filter" label-cols-sm="1" label-align-sm="right" label-size="sm" label-for="filterInput">
          <b-input-group size="sm">
            <b-form-input v-model="table.filter" debounce="750" type="search" id="filterInput" placeholder="Search data column"></b-form-input>
            <b-input-group-append>
              <b-button :disabled="!table.filter" @click="table.filter = ''">Clear</b-button>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <VueCtkDateTimePicker
          id="logs-start" label="From date - time"
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
          id="logs-end" label="To date - time"
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
      <b-col cols="3" class="mt-2">
        <p class="mt-3">Monitor: {{ resource.logType }}<b></b></p>
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

      <b-col cols="3">
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

      <b-col cols="3" class="mt-3 text-right">
        <p>Rows: <b>{{ table.totalRows }}</b></p>
      </b-col>
    </b-row>

    <b-row>
      <b-col cols="12">
        <b-table
          ref="logsTable"
          :busy.sync="isBusy"
          :items="tableProvider"
          :fields="table.fields"
          :current-page="table.currentPage"
          :per-page="table.perPage"
          :sort-by.sync="table.sortBy"
          :sort-desc.sync="table.sortDesc"
          :filter="table.filter"
          fixed
          sort-icon-left
          hover
          responsive="sm"
        >
          <!-- style rows -->
          <template v-slot:table-colgroup="scope">
            <col v-for="field in scope.fields" :key="field.key" :style="field.style" />
          </template>

          <template v-slot:cell(data)="row">
            <div class="data" >
                <json-view :data="row.item.data[0]" class="customize-json-view" :maxDepth="row.index==0? 99:0" rootKey="view" />
            </div>
          </template>
          
          <template v-slot:cell(created_at)="row">
            <div class="data">
              {{ row.item.created_at }}
            </div>
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
        <span>{{feedback}}</span>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'
import { JSONView } from 'vue-json-component';

import VueCtkDateTimePicker from 'vue-ctk-date-time-picker'
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css'
import dateTime from '~/mixins/dateTime'

export default {
  name: 'logs-table',
  mixins: [dateTime],
  props: {
      resource: Object,
      feedback: String
  },
  components: {
    'json-view': JSONView,
    VueCtkDateTimePicker
  },
  data() {
    return {
      isBusy: false,
      table: {
        fields: [
          { key: 'id', sortable: false, label: 'id', style: 'width:25px; text-align:right;' },
          { key: 'data', sortable: true, label: 'Data' },
          { key: 'created_at', sortable: true, style: '', label: 'Run at' }
        ],
        selectedRows: null,
        lastSelectedRow: null,
        selectedNumRows: 0,
        currentPage: 1,
        perPage: 10,
        totalRows: 0,
        pageOptions: [5, 10, 25],
        sortBy: 'created_at',
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
          selected: this.getCurrentDateWithOffset( true, { months: -1 }),
        },
        end: {
          error: false,
          min: this.getCurrentDateWithOffset( true, { years: -20 }),
          max: this.getCurrentDateWithOffset( true ),
          format: 'YYYY-MM-DD HH:mm',
          selected: this.getCurrentDateWithOffset( true ),
        }
      },
      feedback: ''
    }
  },

  mounted() {

  },
  methods: {
    ...mapActions('logs', {
        retrieveLogs: 'retrieveLogs'
    }),
    dateTimePicked($event, dateTime, action) {
      this.calcDateTime($event, dateTime, action)
      try {
        typeof this.$refs.logsTable.refresh()
      } catch(err){
        // console.log(err)
        return
      }
    },
    tableProvider(ctx, callback){
      this.isBusy = true
      let params = 
        '?resourceType=' + this.resource.resource_type +
        '&dateTime=start=' + this.getISODateWithOffset(this.dateTime.start.selected) +
            ',end='+ this.getISODateWithOffset(this.dateTime.end.selected) +
        '&page=' + ctx.currentPage +
        '&size=' + ctx.perPage +
        '&filterJson=' + ctx.filter +
        '&orderBy=' + ctx.sortBy +
        '&order=' + (ctx.sortDesc ? 'desc' : 'asc')
      this.loadTable(params, callback)
    },
    // async table loader for Table provider
    async loadTable(params, callback) {
        let response = await this.retrieveLogs(params)
        this.table.totalRows = response.meta == null ? 0 : response.meta.total
        this.feedback = response.message
        this.isBusy = false
        callback(response.data)
    }
  }
}
</script>

<style lang="scss" scoped>
.customize-json-view {
  --vjc-valueKey-color: green;
}
.customize-json-view.dark {
  --vjc-valueKey-color: red;
}

.customize-json-view .button.data-key {
  padding: 0 0 1px 0 !important;
  color: red !important;
}

// .row {
//   background-color: green !important;
// }
</style>