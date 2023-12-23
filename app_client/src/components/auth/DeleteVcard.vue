<script setup>
import { useToast } from "vue-toastification"
import { useRouter } from 'vue-router'
const router = useRouter()

import { useUserStore } from '../../stores/user.js'
import { ref } from 'vue'

const userStore = useUserStore()
const toast = useToast()
const deleteConfirmationDialog = ref(null)

const delete_vcard = ref({
  password: '',
  confirmation_code: ''
})

const errors = ref(null)

const deleteVcard = () => {
  deleteConfirmationDialog.value.show()
}


const deleteVcardConfirmed = async () => {
  try {
    await userStore.deleteVcard(delete_vcard.value)
    toast.warning('vCard has been deleted.')
  } catch (error) {
    console.log(error)
    if (error.response.status == 422) {
      errors.value = error.response.data.errors
      toast.error('vCard has not been deleted due to validation errors! One of your credentials might be wrong')
    } else {
      toast.error('vCard has not been deleted due to unknown server error!')
    }
  }
}
</script>

<template>
  <ConfirmationDialog
    ref="deleteConfirmationDialog"
    confirmationBtn="Delete vCard"
    msg="Do you really delete this vCard?"
    @confirmed="deleteVcardConfirmed">
  </ConfirmationDialog>  

  <form class="row g-3 needs-validation" novalidate @submit.prevent="deleteVcard">
    <h3 class="mt-5 mb-3">Delete vCard confirmation</h3>
    <hr>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputCurrentPassword" class="form-label">Current Password</label>
        <input type="password" class="form-control" id="inputCurrentPassword" required
          v-model="delete_vcard.password">
        <field-error-message :errors="errors" fieldName="password"></field-error-message>
      </div>
    </div>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputConfirmationCode" class="form-label">Confirmation code</label>
        <input type="password" class="form-control" id="inputConfirmationCode" required v-model="delete_vcard.confirmation_code">
        <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-center">
      <button type="button" class="btn btn-danger px-5" @click="deleteVcard">Delete this vCard</button>
    </div>
  </form>
</template>