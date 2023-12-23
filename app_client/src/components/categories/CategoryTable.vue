<script setup>
const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(["delete","edit"])

const deleteClick = (category) => {
  emit("delete", category)  
}

const editClick = (category) => {
  emit("edit", category)  
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
      <tr v-for="category in categories" :key="category.id">
        <td class="align-middle">{{ category.id }}</td>
        <td class="align-middle">{{ category.name }}</td>
        <td class="align-middle">{{ category.type == 'C' ? 'Credit' : 'Debit' }}</td>
        <td class="text-end align-middle">
          <div class="d-flex">
            <button
              class="btn btn-xs btn-primary me-1"
              @click="editClick(category)"
            >
                <i class="bi bi-xs bi-zoom-in"></i>
            </button>
            <button
              class="btn btn-xs btn-danger me-1"
              @click="deleteClick(category)"
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