<script setup>
import { useRouter } from 'vue-router'
import { useToast } from "vue-toastification"
import { useAdminsStore } from "../../stores/admins.js"
import { ref, computed, onMounted } from 'vue'
import AdminTable from "./AdminTable.vue"

const toast = useToast()
const router = useRouter()
const adminsStore = useAdminsStore()
const addAdmin = () => {
    router.push({ name: 'NewAdmin' })
}
const adminToDelete = ref(null)
const deleteConfirmationDialog = ref(null)
const searchField=ref('N')
const searchTerm = ref('')
const sortBy = ref('I')

const loadAdmins = async () => {
  try {
    await adminsStore.loadAdmins()
  } catch (error) {
    console.log(error)
  }
}

const deleteAdmin = (admin) => {
  adminToDelete.value = admin
  deleteConfirmationDialog.value.show()
}

const deleteAdminConfirmed = async () => {
  try {
    await adminsStore.deleteAdmin(adminToDelete.value)
    toast.info(`Admin ${adminToDeleteDescription.value} was deleted`)
  } catch (error) {
    console.log(error)
    toast.error(`It was not possible to delete the admin ${adminToDeleteDescription.value}!`)
  }  
}

const adminToDeleteDescription = computed(() => adminToDelete.value
    ? `#${adminToDelete.value.id} (${adminToDelete.value.name})`
    : "")

const filteredAdmins = computed(() =>
  adminsStore.getAdminsByFilter(searchTerm.value, searchField.value, sortBy.value)
)

onMounted(() => {
  loadAdmins()
})

</script>

<template>
  <ConfirmationDialog
  ref="deleteConfirmationDialog"
  confirmationBtn="Delete admin"
  :msg="`Do you really want to delete the admin ${adminToDeleteDescription}?`"
  @confirmed="deleteAdminConfirmed"
  >
  </ConfirmationDialog>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Admins</h3>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ adminsStore.totalAdmins }} </h5>
    </div>
  </div>
  <div class="mx-2 mt-2">
      <button
        type="button"
        class="btn btn-success px-4 btn-addadmin"
        @click="addAdmin"
      ><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add new admin</button>
  </div>
  <hr>
  <div class="mb-3 d-flex justify-content-between flex-wrap">
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
        <label for="inputName" class="form-label">Search</label>
        <input
        v-model="searchTerm"
        type="text"
        class="form-control"
        id="inputSearch"
        placeholder="Search admins"/>
    </div>
    <div class="mx-2 mt-2 flex-grow-3 filter-div">
      <label for="selectStatus" class="form-label">Search by:</label>
      <select v-model="searchField" class="form-select" id="selectSearchBy">
        <option value="N">Name</option>
        <option value="E">Email</option>
      </select>
    </div>
    <div class="mx-2 mt-2 flex-grow-3 filter-div">
        <label for="selectStatus" class="form-label">Sort by:</label>
        <select v-model="sortBy" class="form-select" id="selectSortBy"> 
            <option selected value="I">#</option>
            <option value="N">Name</option>
            <option value="E">Email</option>
        </select>
    </div>
  </div>
  <AdminTable
    :admins="filteredAdmins"
    @delete="deleteAdmin"
  ></AdminTable>
</template>

<style scoped>
  .filter-div {
    min-width: 12rem;
  }
  .total-filtro {
    margin-top: 0.35rem;
  }
  .btn-addadmin {
    margin-top: 1.85rem;
  }
</style>
