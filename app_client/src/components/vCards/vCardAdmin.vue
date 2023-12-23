<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject  } from 'vue'
import { useVCardStore } from "../../stores/vCards.js"
import vCardAdminTable from "./vCardAdminTable.vue"

const toast = useToast()
const router = useRouter()
const socket = inject("socket")
const vCardStore = useVCardStore()

const filterByIsBlocked = ref(true)
const searchTerm = ref('')
const searchField = ref('P')
const sortBy = ref('P')

const VCardToDelete = ref(null)
const deleteConfirmationDialog = ref(null)

const loadVCards = async () => {
  try 
  {
    await vCardStore.loadVCard()
  } 
  catch (error) 
  {
    console.log(error)
  }
}

const vCardEdit = (vCard) => 
{
    router.push({ name: 'vCardAdminDetails', params: { id: vCard.phone_number }})
}

const vCardBlock = async (vCard) => 
{
    try 
    {
        const response = await axios.patch('vcards/' + vCard.phone_number + '/block', vCard)
        vCard = response.data.data
        if (vCard.blocked == 1) {
            socket.emit('vCardBlockedOrDeleted', vCard)
        }
        toast.success('vCard ' + vCard.phone_number + ' was updated successfully.')
    } 
    catch (error) 
    {
        toast.error('vCard ' + vCard.phone_number + ' was not updated due to unknown server error!')
    }
}

const deleteVCard = (vCard) => 
{
    VCardToDelete.value = vCard
    deleteConfirmationDialog.value.show()
}

const deleteVCardConfirmed = async () => 
{
    try 
    {
        await vCardStore.deleteVCard(VCardToDelete.value)
        socket.emit('vCardBlockedOrDeleted', VCardToDelete.value)
        toast.info(`vCard ${VCardToDelete.value.phone_number} was deleted`)
    } 
    catch (error) 
    {
        console.log(error)
        toast.error(`It was not possible to delete vCard ${VCardToDelete.value.phone_number}!`)
    } 
}

const VCardToDeleteDescription = computed(() => VCardToDelete.value
    ? `${VCardToDelete.value.phone_number}`
    : "")

const filteredVCards = computed(() =>
    vCardStore.getVCardsByFilter(searchTerm.value, searchField.value, filterByIsBlocked.value, sortBy.value)
)

onMounted(() => {
  loadVCards()
})

</script>

<template>
    <ConfirmationDialog
        ref="deleteConfirmationDialog"
        confirmationBtn="Delete vCard"
        :msg="`Do you really want to delete the vCard ${VCardToDeleteDescription}?`"
        @confirmed="deleteVCardConfirmed">
    </ConfirmationDialog>
    <div class="d-flex justify-content-between">
        <div class="mx-2">
        <h3 class="mt-4">vCard List</h3>
        </div>
        <div class="mx-2 total-filtro">
        <h5 class="mt-4">Total: {{ filteredVCards.length }} </h5>
        </div>
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
            placeholder="Search vCards Here"/>
        </div>
        <div class="mx-2 mt-2 flex-grow-3 filter-div">
            <label for="selectStatus" class="form-label">Search by:</label>
            <select v-model="searchField" class="form-select" id="selectSearchBy">
                <option selected value="P">Phone Number</option>
                <option value="N">Name</option>
                <option value="E">Email</option>
            </select>
        </div>
        <div class="mx-2 mt-2 flex-grow-3 filter-div">
            <label for="selectStatus" class="form-label">Sort by:</label>
            <select v-model="sortBy" class="form-select" id="selectSortBy"> 
                <option selected value="P">Phone Number</option>
                <option value="N">Name</option>
                <option value="BA">Balance Asc.</option>
                <option value="BD">Balance Des.</option>
                <option value="MA">Max Debit Asc.</option>
                <option value="MD">Max Debit Des.</option>
            </select>
        </div>
        <div class="mx-2 mt-2 flex-grow-3 filter-div">
            <label for="selectStatus" class="form-label">Filters:</label><br>
            <button class="form-select text-start" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Select Filters
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li class="dropdown-checkbox">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="checkbox1" checked v-model="filterByIsBlocked">
                    <label class="form-check-label" for="checkbox1">
                    Show Blocked
                    </label>
                </div>
                </li>
            </ul>
        </div>
    </div>

  <vCardAdminTable
    :vCards="filteredVCards" 
    @edit="vCardEdit"
    @block="vCardBlock"
    @delete="deleteVCard"
  ></vCardAdminTable> 

</template>

<style scoped>
.filter-div {
    min-width: 12rem;
}
.total-filtro {
    margin-top: 0.35rem;
}
.btn-addprj {
    margin-top: 1.85rem;
}

.dropdown-checkbox {
    padding-left: 5%;
}

</style>
