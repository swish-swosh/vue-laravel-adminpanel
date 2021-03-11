export default {
  methods: {
    // PAGINATION Trigger pagination to update the number of buttons/pages due to filtering
    onFiltered(filteredItems, table) {
      this[table].totalRows = filteredItems.length
      this[table].currentPage = 1
    },
    // clear selected table rows
    clearSelected(ref) {
      this.$refs[ref].clearSelected()
    },
    recordSelectedRows(items, table) {
      this[table].selectedRows = items
      this[table].selectedNumRows = items.length
    },
    recordLastSelectedRow(item, table) {
      this[table].lastSelectedRow = item
    }
  }
}
