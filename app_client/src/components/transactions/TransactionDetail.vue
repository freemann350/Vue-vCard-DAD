<script setup>
  import { ref, watch } from "vue";
  import { useTransactionStore } from "../../stores/transactions.js"
  import { useUserStore } from "../../stores/user.js"

  
  const transactionsStore = useTransactionStore()
  const userStore = useUserStore()

  const props = defineProps({
    transaction: {
      type: Object,
      required: true,
    },
    categories: {
      type: Array,
      required: true,
    },
    inserting: {
      type: Boolean,
      default: false,
    },
    isAdmin: {
      type: Boolean,
      default: false,
    },
    errors: {
      type: Object,
      required: false,
    },
  });

  const emit = defineEmits(["save", "cancel"]);
  const editingTransaction = ref(props.transaction)

  watch(
    () => props.transaction,
    (newTransaction) => {
      editingTransaction.value = newTransaction
    },
    { immediate: true }
  )

  const save = () => {
    emit("save", editingTransaction.value);
  }

  const cancel = () => {
    emit("cancel", editingTransaction.value);
  }

</script>

<template>
    <form
      class="row g-3 needs-validation"
      novalidate
      @submit.prevent="save"
    >
      <h3 class="mt-5 mb-3">New transaction</h3>
      <hr>
      <div class="d-flex flex-wrap justify-content-between">
        <div class="mb-3 me-3 flex-grow-1">
          <label
          for="inputValue"
          class="form-label"
        >Value</label>
        <input
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors ? errors['value'] : false }"
          :disabled="!inserting"
          id="inputName"
          placeholder="Transaction value"
          required
          v-model="editingTransaction.value"
        >
        <field-error-message :errors="errors" fieldName="value"></field-error-message>
        </div>
        <div class="mb-3 ms-xs-3 flex-grow-1"  v-if="!inserting">
          <label
          for="inputValue"
          class="form-label"
        >Date & time</label>
        <input
          type="text"
          class="form-control"
          :disabled="!inserting"
          id="inputName"
          placeholder="Date & time"
          v-model="editingTransaction.datetime"
        >
        </div>
        <div class="mb-3 ms-xs-3 flex-grow-1"  v-if="isAdmin && inserting">
          <label
          for="inputValue"
          class="form-label"
        >vCard</label>
        <input
          type="text"
          class="form-control"
          :disabled="!inserting"
          :class="{ 'is-invalid': errors ? errors['vcard'] : false }"
          id="inputVcard"
          placeholder="vCard"
          :required="isAdmin"
          v-model="editingTransaction.vcard"
        >
        <field-error-message :errors="errors" fieldName="vcard"></field-error-message>
        </div>
      </div>
      <div class="mb-3 ms-xs-3 flex-grow-1"  v-if="!inserting">
          <label
          for="inputValue"
          class="form-label"
        >vCard</label>
        <input
          type="text"
          class="form-control"
          :disabled="!inserting"
          :class="{ 'is-invalid': errors ? errors['vcard'] : false }"
          id="inputVcard"
          placeholder="vCard"
          required
          v-model="editingTransaction.vcard"
        >
        <field-error-message :errors="errors" fieldName="vcard"></field-error-message>
        </div>
      <div class="d-flex flex-wrap justify-content-between">
        <div class="mb-3 me-3 flex-grow-1"  v-if="!inserting">
          <label
            for="inputType"
            class="form-label"
          >Type</label>
          <select
            class="form-select"
            :class="{ 'is-invalid': errors ? errors['type'] : false }"
            id="inputType"
            :disabled="!inserting"
            required
            v-model="editingTransaction.type"
          >
            <option :value="null" hidden>(Credit/Debit)</option>
            <option value="C">Credit</option>
            <option value="D">Debit</option>
          </select>
          <field-error-message :errors="errors" fieldName="type"></field-error-message>
        </div>
        <div class="mb-3 ms-xs-3 flex-grow-1">
          <label
            for="inputPaymentType"
            class="form-label"
          >Payment type</label>
          <select
            class="form-select"
            :class="{ 'is-invalid': errors ? errors['payment_type'] : false }"
            :disabled="!inserting"
            id="inputPaymentType"
            required
            v-model="editingTransaction.payment_type"
          >
            <option :value="''" hidden>Select payment type</option>
            <option v-if="!isAdmin" value="VCARD">vCard</option>
            <option value="MBWAY">MBWay</option>
            <option value="PAYPAL">Paypal</option>
            <option value="IBAN">IBAN</option>
            <option value="MB">MB</option>
            <option value="VISA">VISA</option>
            
          </select>
          <field-error-message :errors="errors" fieldName="payment_type"></field-error-message>
        </div>
      </div>
      <div class="d-flex flex-wrap justify-content-between">
        <div class="mb-3 me-3 flex-grow-1">
        <label
        for="inputPaymentReference"
        class="form-label"
        >Reference</label>
        <input
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors ? errors['payment_reference'] : false }"
          :disabled="!inserting"
          id="inputPaymentReference"
          placeholder="Payment Reference"
          required
          v-model="editingTransaction.payment_reference"
        >
        <field-error-message :errors="errors" fieldName="payment_reference"></field-error-message>
        </div>
        <div class="mb-3 ms-xs-3 flex-grow-1" v-if="!isAdmin">
          <label
            for="inputType"
            class="form-label"
          >Category</label>
          <select
            class="form-select"
            :class="{ 'is-invalid': errors ? errors['category_id'] : false }"
            id="inputCategory"
            required
            v-model="editingTransaction.category_id"
          >
            <option :value="''" selected>None</option>
            <option
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
          >{{category.name}}</option>
          </select>
          <field-error-message :errors="errors" fieldName="category_id"></field-error-message>
        </div>
      </div>
      <div v-if="!inserting" class="d-flex flex-wrap justify-content-between">
        <div class="mb-3 me-3 flex-grow-1">
        <label
        for="inputPaymentReference"
        class="form-label"
        >Old balance</label>
        <input
          type="text"
          class="form-control"
          :disabled="!inserting"
          id="inputOldBalance"
          placeholder="Old balance"
          required
          v-model="editingTransaction.old_balance"
        >
        </div>
        <div class="mb-3 ms-xs-3 flex-grow-1">
          <label
          for="inputNewBalance"
          class="form-label"
        >New balance</label>
        <input
          type="text"
          class="form-control"
          :disabled="!inserting"
          id="inputNewBalance"
          placeholder="New balance"
          required
          v-model="editingTransaction.new_balance"
        >
        </div>
      </div>
      <div class="d-flex flex-wrap justify-content-between">
        <div class="mb-3 ms-xs-3 flex-grow-1">
            <label
            for="inputDescription"
            class="form-label"
          >Description</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['description'] : false }"
            id="inputDescription"
            placeholder="Description"
            required
            v-model="editingTransaction.description"
          >
          <field-error-message :errors="errors" fieldName="description"></field-error-message>
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