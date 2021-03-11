<template>
    <div class=""> 
        <TaskTable />
    </div>
</template>

<script>
    import TaskTable from '~/components/tables/Tasks'
    export default {
        name: 'tasks-page',
        components: {
            TaskTable
        },
        data() {
            return {}
        },
        mounted() {
        }
    }
</script>