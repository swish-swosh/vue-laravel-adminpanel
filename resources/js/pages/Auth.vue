<template>
    <div>
        <AuthModal />
    </div>
</template>
<script>

import AuthModal from '../components/modals/Auth'
export default {
  name: 'auth-page',
  components: {
      AuthModal
  },
  data() {
    return {}
  },
  mounted() {
  },
  computed: {
  },
  methods: {
  }
}
</script>
<style scoped>
/*
.form-input-row {
  min-height: 5.75rem;
}

.Auth-form {
  min-height: 32rem;
}

.Auth-container {
  margin: auto;
}
*/
</style>
