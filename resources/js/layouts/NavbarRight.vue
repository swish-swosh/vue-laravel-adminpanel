<template>
    <b-sidebar :visible="state" @hidden="closeClicked" right shadow id="sidebar-border" sidebar-class="border-right border-danger">
      <div class="px-3 py-2">
        <p>
          Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis
          in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.
        </p>
        <b-img src="https://picsum.photos/500/500/?image=54" fluid thumbnail></b-img>
      </div>
    </b-sidebar>
</template>

<script>
    // import navBarRightData from '../data/navBarRight.json'
    export default {
        name: 'NavbarRight',
        data() {
          return {
            navBarRight: null
          }
        },
        mounted() {
        },
        props: {
          state: {
            required: true,
            type: Boolean
          }
        },
        methods: {
          closeClicked() {
            this.$emit('rightPanelClosed')
          }
        }
    }
</script>

<style lang="scss" scoped>

@import '~@/_variables.scss';

</style>