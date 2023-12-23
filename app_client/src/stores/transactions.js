import axios from 'axios'
import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useTransactionStore = defineStore('transactions', () => {
    const transactions = ref([])

    const totalTransactions = computed(() => {
        return transactions.value.length
    })

    function clearTransactions() {
        transactions.value = []
    }

    async function loadTransactions() {
        try {
            const response = await axios.get('transactions')
            transactions.value = response.data.data
            return transactions.value
        } catch (error) {
            clearTransactions()
            throw error
        }
    }

    async function insertTransaction(newTransaction) {
        const response = await axios.post('transactions', newTransaction)
        transactions.value.push(response.data.data)
        return response.data.data
    }
    
    function getTransactionsByFilter(searchTerm, type, payment_type, sort) 
    {
        let filtered = transactions.value
        const date = searchTerm.toString()

        filtered = transactions.value.filter((transaction) =>
            transaction.date.includes(date)
        );
    
        switch (type) {
            case "C":
                filtered = filtered.filter((transaction) => transaction.type.includes("C"));
                break;
            case "D":
                filtered = filtered.filter((transaction) => transaction.type.includes("D"));
                break;
        }

        switch (payment_type) {
            case "vC":
                filtered = filtered.filter((transaction) => transaction.payment_type.includes("VCARD"));
                break;
            case "MW":
                filtered = filtered.filter((transaction) => transaction.payment_type.includes("MBWAY"));
                break;
            case "P":
                filtered = filtered.filter((transaction) => transaction.payment_type.includes("PAYPAL"));
                break;
            case "I":
                filtered = filtered.filter((transaction) => transaction.payment_type.includes("IBAN"));
                break;
            case "M":
                filtered = filtered.filter((transaction) => transaction.payment_type.includes("MB"));
                break;
            case "V":
                filtered = filtered.filter((transaction) => transaction.payment_type.includes("VISA"));
                break;
        }

        switch (sort) 
        {
            case "I":
                filtered.sort((a, b) => a.id - b.id)
                break;
            case "DA":
                filtered.sort((a, b) => new Date(a.datetime) - new Date(b.datetime))
                break;
            case "DD":
                filtered.sort((a, b) => new Date(b.datetime) - new Date(a.datetime))
                break;
            case "T":
                filtered.sort((a, b) => a.type.localeCompare(b.type))
                break;
            case "PT":
                filtered.sort((a, b) => a.payment_type.localeCompare(b.payment_type))
                break;
        }

      return filtered
    }

    return {
        transactions,
        totalTransactions,
        loadTransactions,
        insertTransaction,
        getTransactionsByFilter
    }
})