<template>
    <div class="">   
        <User :showAuthenticatedUser="true" />
    </div>
</template>

<script>
    import User from '~/components/forms/User'
    export default {
        name: 'my-profile-page',
        components: {
            User
        },
        data() {
            return {}
        },
        mounted() {
        }
    }
</script>