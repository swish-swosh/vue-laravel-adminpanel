<template>
    <b-tabs v-model="tabIndex" @input="setRouter(tabIndex)" content-class="">
      <b-tab title="Settings" :disabled="tabsActive.tab0 ? false:true">
        <MonitorForm 
          v-if="resource"
          :resource="resource"
          :feedback="feedback"
        />
      </b-tab>
      <b-tab title="Charts" :disabled="tabsActive.tab1 ? false:true">
        <MonitorChart
          v-if="resource"
          :resource="resource"
          :feedback="feedback"
        />
      </b-tab>
      <b-tab title="Logs" :disabled="tabsActive.tab2 ? false:true">
        <MonitorLogs
          v-if="resource"
          :resource="resource"
          :feedback="feedback"
        />
      </b-tab>
      <!-- <b-tab title="Owner" :disabled="tabsActive.tab3 ? false:true">
      </b-tab> -->
    </b-tabs>
</template>

<script>
import MonitorLogs from '~/components/tables/MonitorLogs.vue'
import MonitorChart from '~/components/charts/MonitorChart.vue'
import MonitorForm from '~/components/forms/MonitorForm.vue'
import { ValidationProvider } from 'vee-validate'
import { ValidationObserver } from 'vee-validate'
import { mapGetters, mapActions } from 'vuex'
import helpers from '~/mixins/helpers'

export default {
  name: 'monitor-form',
  mixins: [helpers],
  components: {
    MonitorLogs,
    MonitorChart,
    MonitorForm,
    ValidationProvider,
    ValidationObserver
  },
  data() {
    return {
      feedback: '',
      resource: null,
      tabIndex: 0,
      tabsActive: {
        tab0: true,
        tab1: false,
        tab2: false
      }
    }
  },
  props: ['id','tab'],  // from url parameters
  computed: {
  },
  mounted() {

    // parameters has an int id?, edit modus, false = new monitor - no url switch
    if(this.isValidId(this.id)) {
      this.init(this.id)
      this.tabsActive.tab1 = true
      this.tabsActive.tab2 = true
    } 

    this.$nextTick(() => {
      this.tabIndex = this.isValidTabIndex(this.tab, 3) 
      this.setRouter(this.tabIndex)
    })

  },
  methods: {
    ...mapActions('resources', {
        retrieveResource: 'retrieveResource'
    }),
    async init(id) {
      // get latest userlist
      this.feedback = ''
      // load monitor resource data
      let response = await this.retrieveResource({
        'id': id
      })
      this.feedback = response.message
      this.resource = response.data
    },
    setRouter(tab){
      let id = this.id
      this.$router.push({ name: 'monitor', params: { id, tab } }).catch(()=>{})
    }
  }
}
</script>

<style scoped>
</style>

