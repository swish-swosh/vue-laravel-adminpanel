<template>
    <div class=""> 
        <UsersTable />
    </div>
</template>

<script>
    import UsersTable from '~/components/tables/Users'
    export default {
        name: 'users-page',
        components: {
            UsersTable
        },
        data() {
            return {}
        },
        mounted() {
        }
    }
</script>

