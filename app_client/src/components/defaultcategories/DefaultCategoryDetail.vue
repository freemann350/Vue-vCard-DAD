<script setup>
  import { ref, watch, inject } from "vue";
  import { useDefaultCategoriesStore } from "../../stores/defaultcategories.js"

  const defaultCategoriesStore = useDefaultCategoriesStore()
  const props = defineProps({
    defaultCategory: {
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
  const editingDefaultCategory = ref(props.defaultCategory)

  watch(
    () => props.defaultCategory,
    (newDefaultCategory) => {
      editingDefaultCategory.value = newDefaultCategory
    },
    { immediate: true }
  )

  const save = () => {
    emit("save", editingDefaultCategory.value);
  }

  const cancel = () => {
    emit("cancel", editingDefaultCategory.value);
  }
</script>

<template>
    <form
      class="row g-3 needs-validation"
      novalidate
      @submit.prevent="save"
    >
      <h3 class="mt-5 mb-3">New default category</h3>
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
          placeholder="Default category name"
          required
          v-model="editingDefaultCategory.name"

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
            v-model="editingDefaultCategory.type"
          >
            <option :value="null" hidden>(Credit/Debit)</option>
            <option value="C">Credit</option>
            <option value="D">Debit</option>
          </select>
          <field-error-message :errors="errors" fieldName="type"></field-error-message>
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