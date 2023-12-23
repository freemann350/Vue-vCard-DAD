<script setup>
import { ref, watch, inject } from "vue";

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    required: false,
  },
});

const emit = defineEmits(["save", "cancel"]);

const editingUser = ref(props.user)

watch(
  () => props.user,
  (newUser) => {
    editingUser.value = newUser
  },
  { immediate: true }
)


const save = () => {
  emit("save", editingUser.value);
}

const cancel = () => {
  emit("cancel", editingUser.value);
}
</script>


<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-50 pe-4">
        <h3 class="mt-5 mb-1">Admin - {{ editingUser.name }}</h3>
      </div>
    </div>
    <hr />

    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-50 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['name'] : false }"
            id="inputName"
            placeholder="User Name"
            required
            v-model="editingUser.name"
          />
          <field-error-message :errors="errors" fieldName="name"></field-error-message>
        </div>

        <div class="mb-3 px-1">
          <label for="inputEmail" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['email'] : false }"
            id="inputEmail"
            placeholder="Email"
            required
            v-model="editingUser.email"
          />
          <field-error-message :errors="errors" fieldName="email"></field-error-message>
        </div>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>
  <hr />
</template>

<style scoped>
.bg-lightgrey {
  background-color: rgb(250, 250, 250);
}
</style>
