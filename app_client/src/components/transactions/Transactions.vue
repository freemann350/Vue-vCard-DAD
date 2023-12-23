<script setup>
import { useRouter } from 'vue-router'
import { useTransactionStore } from "../../stores/transactions.js"
import { ref, computed, onMounted } from 'vue'
import TransactionTable from "./TransactionTable.vue"

const router = useRouter()
const transactionsStore = useTransactionStore()
const addTransaction = () => {
    router.push({ name: 'NewTransaction' })
}
const filterByType = ref(null)
const filterByPaymentType = ref(null)
const searchTerm = ref('')
const sortBy = ref('DD')

const loadTransactions = async () => {
  try {
    await transactionsStore.loadTransactions()
  } catch (error) {
    console.log(error)
  }
}
const editTransaction = (transaction) => {
  router.push({ name: 'Transaction', params: { id: transaction.id } })
}

const filteredTransactions = computed(() =>
  transactionsStore.getTransactionsByFilter(searchTerm.value, filterByType.value, filterByPaymentType.value,sortBy.value)
)

onMounted(() => {
  loadTransactions()
})

</script>
  
<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Transactions</h3>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ filteredTransactions.length }} </h5>
    </div>
  </div>
  <div class="mx-2 mt-2">
      <button
        type="button"
        class="btn btn-success px-4 btn-addtransaction"
        @click="addTransaction"
      ><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add New Transaction</button>
    </div>
  <hr>
  <div class="mb-3 d-flex justify-content-between flex-wrap">
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
        <label for="inputName" class="form-label">Search by date</label>
        <input
        v-model="searchTerm"
        type="text"
        class="form-control"
        id="inputSearch"
        placeholder="YYYY-MM-DD"/>
    </div>
    <div class="mx-2 mt-2 flex-grow-3 filter-div">
        <label for="selectType" class="form-label">Type:</label>
        <select v-model="filterByType" class="form-select" id="selectType">
            <option value="null">All types</option>
            <option value="D">Debit</option>
            <option value="C">Credit</option>
        </select>
    </div>
    <div class="mx-2 mt-2 flex-grow-3 filter-div">
        <label for="selectPaymentType" class="form-label">Payment type:</label>
        <select v-model="filterByPaymentType" class="form-select" id="selectPaymentType">
            <option value="null">All payment types</option>
            <option value="vC">vCard</option>
            <option value="MW">MBWay</option>
            <option value="P">Paypal</option>
            <option value="I">IBAN</option>
            <option value="M">MB</option>
            <option value="V">VISA</option>
        </select>
    </div>
    <div class="mx-2 mt-2 flex-grow-3 filter-div">
        <label for="selectSortBy" class="form-label">Sort by:</label>
        <select v-model="sortBy" class="form-select" id="selectSortBy"> 
            <option value="I">#</option>
            <option value="DD">Date Des.</option>
            <option value="DA">Date Asc.</option>
            <option value="T">Type</option>
            <option value="PT">Payment type</option>
        </select>
    </div>
  </div>
  <TransactionTable
    :transactions="filteredTransactions"
    @edit="editTransaction"
  ></TransactionTable>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 0.35rem;
}
.btn-addtransaction {
  margin-top: 1.85rem;
}
</style>
