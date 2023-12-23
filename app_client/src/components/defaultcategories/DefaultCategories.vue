<script setup>
import { useRouter } from 'vue-router'
import { useToast } from "vue-toastification"
import { useDefaultCategoriesStore } from "../../stores/defaultcategories.js"
import { ref, computed, onMounted } from 'vue'
import DefaultCategoryTable from "./DefaultCategoryTable.vue"

const toast = useToast()
const router = useRouter()
const defaultCategoriesStore = useDefaultCategoriesStore()
const addDefaultCategory = () => {
    router.push({ name: 'NewDefaultCategory' })
}
const defaultCategoryToDelete = ref(null)
const deleteConfirmationDialog = ref(null)
const filterByType = ref(null)
const searchTerm = ref('')
const sortBy = ref('I')

const loadDefaultCategories = async () => {
  try {
    await defaultCategoriesStore.loadDefaultCategories()
  } catch (error) {
    console.log(error)
  }
}
const editDefaultCategory = (defaultCategory) => {
  router.push({ name: 'DefaultCategory', params: { id: defaultCategory.id } })
}

const deleteDefaultCategory = (defaultCategory) => {
  defaultCategoryToDelete.value = defaultCategory
  deleteConfirmationDialog.value.show()
}

const deleteDefaultCategoryConfirmed = async () => {
  try {
    await defaultCategoriesStore.deleteDefaultCategory(defaultCategoryToDelete.value)
    toast.info(`Default category ${defaultCategoryToDeleteDescription.value} was deleted`)
  } catch (error) {
    console.log(error)
    toast.error(`It was not possible to delete the default category ${defaultCategoryToDeleteDescription.value}!`)
  }  
}

const defaultCategoryToDeleteDescription = computed(() => defaultCategoryToDelete.value
    ? `#${defaultCategoryToDelete.value.id} (${defaultCategoryToDelete.value.name})`
    : "")

const filteredDefaultCategories = computed(() =>
  defaultCategoriesStore.getDefaultCategoriesByFilter(searchTerm.value, filterByType.value, sortBy.value)
)

onMounted(() => {
  loadDefaultCategories()
})

</script>
  
<template>
  <ConfirmationDialog
  ref="deleteConfirmationDialog"
  confirmationBtn="Delete default category"
  :msg="`Do you really want to delete the default category ${defaultCategoryToDeleteDescription}?`"
  @confirmed="deleteDefaultCategoryConfirmed"
  >
  </ConfirmationDialog>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Default Categories</h3>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ filteredDefaultCategories.length }} </h5>
    </div>
  </div>
  <div class="mx-2 mt-2">
      <button
        type="button"
        class="btn btn-success px-4 btn-adddefaultcategory"
        @click="addDefaultCategory"
      ><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add new default category</button>
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
        placeholder="Search default categories"/>
    </div>
    <div class="mx-2 mt-2 flex-grow-3 filter-div">
        <label for="selectStatus" class="form-label">Type:</label>
        <select v-model="filterByType" class="form-select" id="selectType">
            <option value="null">All types</option>
            <option value="D">Debit</option>
            <option value="C">Credit</option>
        </select>
    </div>
    <div class="mx-2 mt-2 flex-grow-3 filter-div">
        <label for="selectStatus" class="form-label">Sort by:</label>
        <select v-model="sortBy" class="form-select" id="selectSortBy"> 
            <option selected value="I">#</option>
            <option value="N">Name</option>
            <option value="T">Type</option>
        </select>
    </div>
  </div>
  <DefaultCategoryTable
    :defaultCategories="filteredDefaultCategories"
    @edit="editDefaultCategory"
    @delete="deleteDefaultCategory"
  ></DefaultCategoryTable>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 0.35rem;
}
.btn-adddefaultcategory {
  margin-top: 1.85rem;
}
</style>
