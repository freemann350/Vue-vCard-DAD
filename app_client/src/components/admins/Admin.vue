<script setup>
  import { useToast } from "vue-toastification"
  import { useAdminsStore } from "../../stores/admins.js"
  import { useRouter, onBeforeRouteLeave } from 'vue-router'
  import { ref } from 'vue'

  const toast = useToast()
  const router = useRouter()
  const adminsStore = useAdminsStore()
  const newAdmin = () => { 
    return {
      id: null,
      name: '',
      email: '',
      password: ''
    }
  }
  const admin = ref(newAdmin())
  const errors = ref(null)
  const confirmationLeaveDialog = ref(null)
  let originalValueStr = JSON.stringify(newAdmin())

  const save = async () => {
    errors.value = null
    try {
      admin.value = await adminsStore.insertAdmin(admin.value)
      originalValueStr = JSON.stringify(admin.value)
      toast.success('Admin #' + admin.value.id + ' was created successfully.')
      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        console.log(errors.value)
        toast.error('Admin was not created due to validation errors!')
      } else {
        toast.error('Admin was not created due to unknown server error!')
      }
    }
  }
  
  const cancel = () => {
    originalValueStr = JSON.stringify(admin.value)
    router.back()
  }

  let nextCallBack = null
  const leaveConfirmed = () => {
    if (nextCallBack) {
      nextCallBack()
    }
  }

  onBeforeRouteLeave((to, from, next) => {
    nextCallBack = null
    let newValueStr = JSON.stringify(admin.value)
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

  <form
    class="row g-3 needs-validation"
    novalidate
    @submit.prevent="save"
  >
    <h3 class="mt-5 mb-3">New admin</h3>
    <hr>
    <div class="mb-3">
      <label
        for="inputName"
        class="form-label"
      >Name</label>
      <input
        type="text"
        class="form-control"
        :class="{ 'is-invalid': errors ? errors['name'] : false }"
        id="inputName"
        placeholder="Name"
        required
        v-model="admin.name"
      >
      <field-error-message :errors="errors" fieldName="name"></field-error-message>
    </div>
    <div class="d-flex flex-wrap justify-content-between">

      <div class="mb-3 me-3 flex-grow-1">
        <label
          for="inputPassword"
          class="form-label"
        >Password</label>
      <input
        type="password"
        class="form-control"
        :class="{ 'is-invalid': errors ? errors['password'] : false }"
        id="inputPassword"
        placeholder="Password"
        required
        v-model="admin.password"
        >
      <field-error-message :errors="errors" fieldName="password"></field-error-message>
      </div>

      <div class="mb-3 ms-xs-3 flex-grow-1">
        <label
          for="inputAdmin"
          class="form-label"
        >Email</label>
        <input
          type="email"
          class="form-control"
          :class="{ 'is-invalid': errors ? errors['email'] : false }"
          id="inputEmail"
          placeholder="Email"
          required
          v-model="admin.email"
        />
        <field-error-message :errors="errors" fieldName="email"></field-error-message>
      </div>
    </div>

    <div class="mb-3 d-flex justify-content-end">
      <button
        type="button"
        class="btn btn-primary px-5"
        @click="save"
      >Save</button>
      <button
        type="button"
        class="btn btn-light px-5"
        @click="cancel"
      >Cancel</button>
    </div>

  </form>
</template>

<style scoped>
.total_price {
  width: 26rem;
}
.checkBilled {
  margin-top: 0.4rem;
  min-height: 2.375rem;
}
</style>
