<script setup>
import { useRouter } from 'vue-router'
import { useToast } from "vue-toastification"
import { useCategoriesStore } from "../../stores/categories.js"
import { ref, computed, onMounted } from 'vue'
import CategoryTable from "./CategoryTable.vue"

const toast = useToast()
const router = useRouter()
const categoriesStore = useCategoriesStore()
const addCategory = () => {
    router.push({ name: 'NewCategory' })
}

const categoryToDelete = ref(null)
const deleteConfirmationDialog = ref(null)

const filterByType = ref(null)
const searchTerm = ref('')
const sortBy = ref('I')

const loadCategories = async () => {
  try {
    await categoriesStore.loadCategories()
  } catch (error) {
    console.log(error)
  }
}
const editCategory = (category) => {
  router.push({ name: 'Category', params: { id: category.id } })
}

const deleteCategory = (category) => {
  categoryToDelete.value = category
  deleteConfirmationDialog.value.show()
}

const deleteCategoryConfirmed = async () => {
  try {
    await categoriesStore.deleteCategory(categoryToDelete.value)
    toast.info(`Category ${categoryToDeleteDescription.value} was deleted`)
  } catch (error) {
    console.log(error)
    toast.error(`It was not possible to delete the category ${categoryToDeleteDescription.value}!`)
  }  
}

const categoryToDeleteDescription = computed(() => categoryToDelete.value
    ? `"${categoryToDelete.value.name}"`
    : "")

const filteredCategories = computed(() =>
  categoriesStore.getCategoriesByFilter(searchTerm.value, filterByType.value, sortBy.value)
)

onMounted(() => {
  loadCategories()
})  

</script>
  
<template>
  <ConfirmationDialog
  ref="deleteConfirmationDialog"
  confirmationBtn="Delete category"
  :msg="`Do you really want to delete the category ${categoryToDeleteDescription}?`"
  @confirmed="deleteCategoryConfirmed"
  >
  </ConfirmationDialog>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Categories</h3>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ filteredCategories.length }} </h5>
    </div>
  </div>
  <div class="mx-2 mt-2">
      <button
        type="button"
        class="btn btn-success px-4 btn-addcategory"
        @click="addCategory"
      ><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add New Category</button>
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
        placeholder="Search categories"/>
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
  <CategoryTable
    :categories="filteredCategories"
    @edit="editCategory"
    @delete="deleteCategory"
  ></CategoryTable>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 0.35rem;
}
.btn-addcategory {
  margin-top: 1.85rem;
}
</style>
