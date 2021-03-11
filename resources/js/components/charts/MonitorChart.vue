<template>
  <b-container fluid>
    <b-row class="mt-3" align-h="center">
      <b-col cols="5">
        <VueCtkDateTimePicker
          id="chart-start" label="From date - time"
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
      <b-col cols="5">
        <VueCtkDateTimePicker
          id="chart-end" label="To date - time"
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
    <b-row align-h="center" class="mt-4">
      <b-col cols="10">
        <b-pagination
          @input="paginationChange($event)"
          v-model="pagination.currentPage"
          :per-page="pagination.perPage"
          :total-rows="pagination.totalRows"
          align="fill">
        </b-pagination>
      </b-col>
    </b-row>
    <b-row class="mt-2">
      <b-col cols="12">
      <template>
        <div>
          <line-chart ref="logsChart"
            v-if="chart.show"
            :resource="resource"
            :chart="chart"
          ></line-chart>
        </div>
      </template>
      </b-col>
    </b-row>
    <b-row class="text-right mt-4 w-100">
      <b-col>
        <span>{{feedback}}</span><span> | {{chartFeedback}}</span>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import LineChart from "~/components/charts/plugins/LineChart.js"

import VueCtkDateTimePicker from 'vue-ctk-date-time-picker'
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css'
import dateTime from '~/mixins/dateTime'

export default {
  name: 'logs-chart',
  mixins: [dateTime],
  props: {
      resource: Object,
      feedback: String
  },
  components: {
    LineChart,
    VueCtkDateTimePicker
  },
  data() {
    return {
      data: {
        monitorType: '',
        chartPlots: ''
      },
      pagination: {
        currentPage: 1,
        totalRows: 0,
        perPage: 25,
        sortBy: 'created_at',
        sortDesc: false,
        filter: ''
      },
      chart: {
        show: false,
        items: [],
        max: 4300,
        datacollection: {
          labels: [],
          colors: [
            '#008080', '#3cb44b', '#ffe119', '#4363d8', '#f58231',
            '#911eb4', '#46f0f0', '#f032e6', '#bcf60c', '#fabebe',
            '#e6194b', '#e6beff', '#9a6324', '#fffac8', '#800000',
            '#aaffc3', '#808000', '#ffd8b1', '#000075', '#808080',
            '#000000'
          ],
          datasets: [
          ]
        },
        options: {
          annotation: {
            annotations: [
              {
                type: "line",
                mode: "vertical",
                scaleID: "x-axis-0",
                borderColor: "#FF0000",
                value: 5,
                borderDash: [4, 4],
                label: {
                  content: "test",
                  enabled: true,
                  position: "top",
                  xAdjust: 15,
                  backgroundColor: '#4ecca3',
                  fontSize: 10,
                }
              }
            ]
          },
          hoverBorderWidth: 20,
          scales: {
              yAxes: [{
                ticks: {
                  max: 4250,
                  fontSize: 8,
                  callback: function(label, index, labels) {
                    if(label>4000) return ' '
                    if(label==4000) return 'lost'
                    return label
                  }
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Latency (logarithmic)',
                },
                type: 'logarithmic',
                position: 'left'
              }]
            },
            responsive: true,
            maintainAspectRatio: false
        }
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
      chartFeedback: ''
    }
  },
  mounted() {
    this.loadChart()
  },
  methods: {
    ...mapActions('logs', {
        retrieveLogs: 'retrieveLogs'
    }),
    dateTimePicked($event, dateTime, action) {
      try {
        this.calcDateTime($event, dateTime, action)
        this.pagination.currentPage = 1
        this.loadChart()
      } catch(err){
          console.log(err)
        return
      }
    },
    paginationChange($event) {
      this.pagination.currentPage = $event
      this.loadChart()
    },
    async loadChart(){
      this.chart.show = false
      // get data from server
      let params = 
        'logs?resourceType=' + this.resource.resource_type +
        '&dateTime=start=' + this.dateTime.start.selected+
          ',end='+this.dateTime.end.selected +
        '&page=' + this.pagination.currentPage +
        '&size=' + this.pagination.perPage +
        '&orderBy=' + this.pagination.sortBy +
        '&order=' + (this.pagination.sortDesc ? 'desc' : 'asc')
      let response = await this.retrieveLogs(params)
      this.chart.items = response.meta.total == 0 ? [] : response.data
      this.chartFeedback = response.meta.total == 0 ? 'no data available' : response.message
      this.pagination.totalRows = response.meta.total
      this.chart.show = true
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

</style>