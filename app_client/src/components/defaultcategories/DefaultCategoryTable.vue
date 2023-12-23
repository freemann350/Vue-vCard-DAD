<script setup>
import { inject } from "vue";
import { useUserStore } from "../../stores/user.js"

const serverBaseUrl = inject("serverBaseUrl");
const userStore = useUserStore()

const props = defineProps({
  defaultCategories: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(["delete","edit"])

const deleteClick = (defaultCategory) => {
  emit("delete", defaultCategory)  
}

const editClick = (defaultCategory) => {
  emit("edit", defaultCategory)  
}
</script>

<template>
  <table class="table">
    <thead>
      <tr>
        <th class="align-middle">#</th>
        <th class="align-middle">Name</th>
        <th class="align-middle">Type</th>
        <th class="align-middle">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="defaultCategory in defaultCategories" :key="defaultCategory.id">
        <td class="align-middle">{{ defaultCategory.id }}</td>
        <td class="align-middle">{{ defaultCategory.name }}</td>
        <td class="align-middle">{{ defaultCategory.type == 'C' ? 'Credit' : 'Debit' }}</td>
        <td class="text-end align-middle">
          <div class="d-flex">
            <button
              class="btn btn-xs btn-primary me-1"
              @click="editClick(defaultCategory)"
            >
                <i class="bi bi-xs bi-zoom-in"></i>
            </button>
            <button
              class="btn btn-xs btn-danger me-1"
              @click="deleteClick(defaultCategory)"
            >
                <i class="bi bi-xs bi-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <!--<p>{{ totalusers }}</p>-->
</template>