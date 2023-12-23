<script setup>
import { ref, watch, computed, inject } from "vue";

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  errors: {
    type: Object,
    required: false,
  }
});

const emit = defineEmits(["register", "cancel"]);

const newUser = () => {
  return {
    name: '',
    email: '',
    password: '',
    confirmation_code: '',
  }
}

const vcardToRegister = ref(newUser())

const register = () => {
  emit("register", vcardToRegister);
}

const cancel = () => {
  emit("cancel");
}
</script>

<template>
<form class="row g-3 needs-validation" novalidate @submit.prevent="register">
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-50 pe-4">
        <h3 class="mt-5 mb-1">New vCard</h3>
      </div>
    </div>
    <hr />

    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-50 pe-4">
        <div class="mb-3">
          <label for="inputPhoneNumber" class="form-label">Phone number</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['phone_number'] : false }"
            id="inputPhoneNumber"
            placeholder="Phone Number"
            required
            v-model="vcardToRegister.phone_number"
          />
          <field-error-message :errors="errors" fieldName="phone_number"></field-error-message>
        </div>

        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['name'] : false }"
            id="inputName"
            placeholder="User Name"
            required
            v-model="vcardToRegister.name"
          />
          <field-error-message :errors="errors" fieldName="name"></field-error-message>
        </div>

        <div class="mb-3">
          <label for="inputEmail" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['email'] : false }"
            id="inputEmail"
            placeholder="Email"
            required
            v-model="vcardToRegister.email"
          />
          <field-error-message :errors="errors" fieldName="email"></field-error-message>
        </div>

        <div class="mb-3">
          <label for="inputPassword" class="form-label">Password</label>
          <input
            type="password"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['password'] : false }"
            id="inputPassword"
            placeholder="Password"
            required
            v-model="vcardToRegister.password"
          />
          <field-error-message :errors="errors" fieldName="password"></field-error-message>
        </div>

        <div class="mb-3">
          <label for="inputCode" class="form-label">Confirmation Code</label>
          <input
            type="password"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['confirmation_code'] : false }"
            id="inputCode"
            placeholder="Confirmation Code"
            required
            v-model="vcardToRegister.confirmation_code"
          />
          <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
        </div>
      </div>
    </div>
    <div class="mb-3 mt-5 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="register">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>
  <hr/>

</template>

<style scoped>
.bg-lightgrey {
  background-color: rgb(250, 250, 250);
}
</style>
  