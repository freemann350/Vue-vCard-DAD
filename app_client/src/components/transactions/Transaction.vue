<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { onMounted, ref, watch, inject } from 'vue'
import TransactionDetail from "./TransactionDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useCategoriesStore } from "../../stores/categories.js"
import { useUserStore } from "../../stores/user.js"

const toast = useToast()
const router = useRouter()
const categoriesStore = useCategoriesStore()
const userStore = useUserStore()
const socket = inject("socket")

const props = defineProps({
    id: {
      type: Number,
      default: null
    }
})

  
const loadCategories = async () => {
  if (userStore.userType == 'V') {
    try {
      await categoriesStore.loadCategories()
    } catch (error) {
      console.log(error)
    }
  }
}

const newTransaction = () => {
    return {
      vcard: "",
      value: "",
      type: "",
      category: "",
      description: "",
      payment_type: ""
    }
}

const transaction = ref(newTransaction())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
let originalValueStr = null

const inserting = (id) => !id || (id < 0)

const loadTransaction = async (id) => {
  originalValueStr = JSON.stringify(newTransaction())
  errors.value = null
  if (inserting(id)) {
    transaction.value = newTransaction()
  } else {
      try {
        const response = await axios.get('transactions/' + id)
        transaction.value = response.data.data
        originalValueStr = JSON.stringify(transaction.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const save = async (transactionToSave) => {
  errors.value = null
  if (inserting(props.id)) {
    toast.info('Please wait while we process the transaction...')
    try {
      let response = null
      userStore.userType == "V" ? transactionToSave.type="D" : transactionToSave.type="C"
      
      if (userStore.userType == "V") {
        transactionToSave.vcard = userStore.userId.toString()
        response = await axios.post('transactions/debit', transactionToSave)
      }
      if (userStore.userType == "A")
      {
        response = await axios.post('transactions/credit', transactionToSave)
      }

      transaction.value = response.data.data
      originalValueStr = JSON.stringify(transaction.value)
      toast.success('Transaction #' + transaction.value.id + ' was registered successfully.')
      
      if (transactionToSave.payment_type == 'VCARD') 
      {
        socket.emit('vCardTransactionNotify',  transaction.value)  
      }

      if (userStore.userType == "V") 
      {
        userStore.user.balance = transaction.value.new_balance
      }
      else
      {
        socket.emit('vCardAddCredit',  transaction.value)
      }
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Transaction was not registered due to validation errors!')
      } else {
        toast.error('Transaction was not registered due to unknown server error!')
      }
    }
  } else {
    try {
      const response = await axios.put('transactions/' + props.id, transactionToSave)
      transaction.value = response.data.data
      originalValueStr = JSON.stringify(transaction.value)
      toast.success('Transaction #' + transaction.value.id + ' was updated successfully.')
      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Transaction #' + props.id + ' was not updated due to validation errors!')
      } else {
        toast.error('Transaction #' + props.id + ' was not updated due to unknown server error!')
      }
    }
  }
}
const cancel = () => {
  originalValueStr = JSON.stringify(transaction.value)
  router.back()
}

watch(
  () => props.id,
  (newValue) => {
    loadTransaction(newValue)
    },
  {immediate: true}
)

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}
onMounted(() => {
    loadCategories()
  })  
onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = JSON.stringify(transaction.value)
  if (originalValueStr != newValueStr && to.name !== 'Login') {
    // Some value has changed - only leave after confirmation
    nextCallBack = next
    confirmationLeaveDialog.value.show()
  } else {
    // No value has changed, so we can leave the component without confirming
    next()
  }
})

</script>
  
<template>
  <ConfirmationDialog
    ref="confirmationLeaveDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </ConfirmationDialog>
  <TransactionDetail
    :transaction="transaction"
    :categories="categoriesStore.categories"
    :isAdmin="userStore.userType == 'A'"
    :errors="errors"
    :inserting="inserting(id)"
    @save="save"
    @cancel="cancel"
  ></TransactionDetail>
</template> 