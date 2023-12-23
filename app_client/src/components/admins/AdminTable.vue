<script setup>
import { useUserStore } from "../../stores/user.js"

const userStore = useUserStore()

const props = defineProps({
  admins: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(["delete"])

const deleteClick = (defaultCategory) => {
  emit("delete", defaultCategory)  
}
</script>

<template>
  <table class="table">
    <thead>
      <tr>
        <th class="align-middle">#</th>
        <th class="align-middle">Name</th>
        <th class="align-middle">Email</th>
        <th class="align-middle">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="admin in admins" :key="admin.id">
        <td class="align-middle">{{ admin.id }}</td>
        <td class="align-middle">{{ admin.name }}</td>
        <td class="align-middle">{{ admin.email }}</td>
        <td class=" align-middle">
          <div class="">
            <button
              class="btn btn-xs btn-danger me-1"
              @click="deleteClick(admin)"
              v-if="admin.id != userStore.userId"
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