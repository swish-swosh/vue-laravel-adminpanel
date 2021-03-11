<template>
        <div>
            <b-modal
            id="auth-modal"
            modal-class="login-modal-backdrop"
            centered
            scrollable
            hide-footer
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
            ref="auth-modal"
            :title="modalTitle"
            @show="resetModal"
            @hidden="resetModal"
            >
            <router-view></router-view>
            </b-modal>
        </div>
</template>
<script>

export default {
  name: 'auth-modal',
  data() {
    return {
        modalTitle : 'SWISH-SWOSH.com'
    }
  },
  components: {
  },
  mounted() {
    this.$bvModal.show('auth-modal')
  },
  methods: {
    resetModal() {
   
    }
  }

}
</script>

<style lang="scss" scoped>
    @import '~@/_variables.scss';

</style>

