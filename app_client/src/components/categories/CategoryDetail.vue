<script setup>
  import { ref, watch, inject } from "vue";
  import { useCategoriesStore } from "../../stores/categories.js"

  const serverBaseUrl = inject("serverBaseUrl");
  const categoriesStore = useCategoriesStore()
  const props = defineProps({
    category: {
      type: Object,
      required: true,
    },
    inserting: {
      type: Boolean,
      default: false,
    },
    errors: {
      type: Object,
      required: false,
    },
  });
  const emit = defineEmits(["save", "cancel"]);
  const editingCategory = ref(props.category)

  watch(
    () => props.category,
    (newCategory) => {
      editingCategory.value = newCategory
    },
    { immediate: true }
  )

  const save = () => {
    emit("save", editingCategory.value);
  }

  const cancel = () => {
    emit("cancel", editingCategory.value);
  }
</script>

<template>
    <form
      class="row g-3 needs-validation"
      novalidate
      @submit.prevent="save"
    >
      <h3 class="mt-5 mb-3">New category</h3>
      <hr>
      <div class="d-flex flex-wrap justify-content-between">
        <div class="mb-3 me-3 flex-grow-1">
          <label
          for="inputName"
          class="form-label"
        >Name</label>
        <input
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors ? errors['name'] : false }"
          id="inputName"
          placeholder="Category name"
          required
          v-model="editingCategory.name"

        >
        <field-error-message :errors="errors" fieldName="name"></field-error-message>
        </div>
        <div class="mb-3 ms-xs-3 flex-grow-1">
          <label
            for="inputAdmin"
            class="form-label"
          >Type</label>
          <select
            class="form-select"
            :class="{ 'is-invalid': errors ? errors['type'] : false }"
            id="inputAdmin"
            required
            v-model="editingCategory.type"
          >
            <option :value="null" hidden>(Credit/Debit)</option>
            <option value="C">Credit</option>
            <option value="D">Debit</option>
          </select>
          <field-error-message :errors="errors" fieldName="status"></field-error-message>
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