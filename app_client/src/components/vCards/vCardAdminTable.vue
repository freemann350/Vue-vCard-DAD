<script setup>

const props = defineProps({
    vCards: {
      type: Array,
      default: () => [],
    }
})

const emit = defineEmits(['edit', 'block', 'delete'])

const editClick = (vCard) => {
  emit('edit', vCard)
}

const blockClick = (vCard) => {
  vCard.blocked = 1
  emit('block', vCard)
}
const unblockClick = (vCard) => {
  vCard.blocked = 0
  emit('block', vCard)
}
const deleteClick = (vCard) => {
  emit('delete', vCard)
}

</script>

<template>
<table class="table">
  <thead>
    <tr>
      <th class="align-middle">Phone Number</th>
      <th class="align-middle">Name</th>
      <th class="align-middle">Email</th>
      <th class="align-middle">Balance</th>
      <th class="align-middle">Max Debit</th>
      <th class="align-middle">Actions</th> 
    </tr>
  </thead>
  <tbody>
    <tr v-for="vCard in vCards" :key="vCard.phone_number">
      <td class="align-middle" :class="{ 'bg-lightgrey': vCard.blocked == 1 }">{{ vCard.phone_number }}</td>
      <td class="align-middle" :class="{ 'bg-lightgrey': vCard.blocked == 1 }">{{ vCard.name }}</td>
      <td class="align-middle" :class="{ 'bg-lightgrey': vCard.blocked == 1 }">{{ vCard.email }}</td>
      <td class="align-middle" :class="{ 'bg-lightgrey': vCard.blocked == 1 }">{{ vCard.balance }}</td>
      <td class="align-middle" :class="{ 'bg-lightgrey': vCard.blocked == 1 }">{{ vCard.max_debit }}</td>
      <td class="text-end align-middle" :class="{ 'bg-lightgrey': vCard.blocked == 1 }">
        <div class="d-flex">
            <button class="btn btn-primary me-2" @click="editClick(vCard)">
              <i class="bi bi-xs bi-zoom-in"></i>
            </button>   
            <button class="btn btn-danger me-5" @click="blockClick(vCard)" v-if="vCard.blocked == 0 ">
              <i class="bi bi-xs bi-lock-fill"></i>
            </button>
            <button class="btn btn-success me-5" @click="unblockClick(vCard)"  v-if="vCard.blocked == 1 ">
              <i class="bi bi-xs bi-unlock-fill"></i>
            </button>   
            <button class="btn btn-danger" @click="deleteClick(vCard)" v-if="vCard.balance == 0 ">
              <i class="bi bi-xs bi-trash"></i>
            </button> 
        </div>
      </td>
    </tr>
  </tbody>
</table>
</template>

<style scoped>
.bg-lightgrey {
  background-color: rgb(218, 217, 217) !important;
}
</style>