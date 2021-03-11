<template>
  <b-modal 
      ref="multi-modal"
      id="multi-modal"
      @close="modalActions('closeModal')"
      :size="modalData.size"
      :title="modalData.title"
      :hide-footer="modalData.hideFooter">
      <b-container fluid>
        <router-view @actions="modalActions"></router-view>
    </b-container>
  </b-modal>
</template>

<script>
export default {
  name: 'multi-modal',
  props: 
  {
    modalData: {
      required: true,
      type: Object
    }
  },
  components: {
  },
  data(){
    return {
    }
  },
  mounted() {
    // only show modal when route parameter id is provided.
    if(typeof this.$route.params.id !== 'undefined' ) {
      this.$refs['multi-modal'].show()
    }
  },
  methods: {
    modalActions(action){
      switch(action)
      {
        case 'closeModal':
          this.$bvModal.hide('multi-modal')
          this.$emit('actions', 'closed')
        break
        case 'formUpdated-closeModal':
          this.$bvModal.hide('multi-modal')
          console.log('modal close')
          this.$emit('actions', 'update')
        break
      }
    }
  }
}
</script>

<style scoped>

</style>
