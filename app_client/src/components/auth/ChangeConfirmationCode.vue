<script setup>
  import { useToast } from "vue-toastification"
  import { useRouter } from 'vue-router'
  import { useUserStore } from '../../stores/user.js'
  import { ref } from 'vue'

  const toast = useToast()
  const router = useRouter()
  const userStore = useUserStore()

  const confirmationcode = ref({
    current_password: '',
    confirmation_code: '',
    confirmation_code_confirmation: ''
  })

  const errors = ref(null)

  const changeConfirmationCode = async () => {
    try {
      await userStore.changeConfirmationCode(confirmationcode.value)
      toast.success('Confirmation code has been changed.')
      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Confirmation code has not been changed due to validation errors!')
      } else {
        toast.error('Confirmation code has not been changed due to unknown server error!')
      }
    }
  }
</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="changeConfirmationCode">
    <h3 class="mt-5 mb-3">Change confirmation code</h3>
    <hr>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputCurrentPassword" class="form-label">Current Password</label>
        <input type="password" class="form-control" id="inputCurrentPassword" required
          v-model="confirmationcode.current_password">
        <field-error-message :errors="errors" fieldName="current_password"></field-error-message>
      </div>
    </div>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputConfirmationCode" class="form-label">New confirmation code</label>
        <input type="password" class="form-control" id="inputConfirmationCode" required v-model="confirmationcode.confirmation_code">
        <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
      </div>
    </div>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputConfirmationCodeConfirm" class="form-label">Confirmation code confirmation</label>
        <input type="password" class="form-control" id="inputConfirmationCodeConfirm" required
          v-model="confirmationcode.confirmation_code_confirmation">
        <field-error-message :errors="errors" fieldName="confirmation_code_confirmation"></field-error-message>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click="changeConfirmationCode">Change confirmation code</button>
    </div>
  </form>
</template>