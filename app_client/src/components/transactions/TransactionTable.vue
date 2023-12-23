<script setup>
const props = defineProps({
  transactions: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(["edit"])

const editClick = (transaction) => {
  emit("edit", transaction)  
}

</script>

<template>
  <table class="table">
    <thead>
      <tr>
        <th class="align-middle">#</th>
        <th class="align-middle">Date</th>
        <th class="align-middle">Type</th>
        <th class="align-middle">Value</th>
        <th class="align-middle">Payment Type</th>
        <th class="align-middle">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="transaction in transactions" :key="transaction.id">
        <td class="align-middle">{{ transaction.id }}</td>
        <td class="align-middle">{{ transaction.date }} </td>
        <td class="align-middle">{{ transaction.type == 'C' ? 'Credit' : 'Debit' }}</td>
        <td class="align-middle">{{ transaction.value }}</td>
        <td class="align-middle">{{ transaction.payment_type }}</td>
        <td class="text-end align-middle">
          <div class="d-flex">
            <button
              class="btn btn-xs btn-primary me-1"
              @click="editClick(transaction)"
            >
                <i class="bi bi-xs bi-zoom-in"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>