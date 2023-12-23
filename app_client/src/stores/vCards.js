import axios from 'axios'
import { ref } from 'vue'
import { defineStore } from 'pinia'


export const useVCardStore = defineStore('vCards', () => 
{
    const vCards = ref([])

    async function loadVCard() 
    {
        try 
        {
            const response = await axios.get('vcards')
            vCards.value = response.data.data
            return vCards.value
        } 
        catch (error) 
        {
            clearVCard()
            throw error
        }
    }

    function clearVCard () 
    {
        vCards.value = []
    }

    function getVCardsByFilter(searchTerm, searchField, status, sort) 
    {
        let filtered = vCards.value
        if (searchTerm && searchField) 
        {
            const term = searchTerm.toString()
            
            switch (searchField) 
            {
                case "P":
                    filtered = vCards.value.filter((vCard) =>
                        vCard.phone_number.includes(term)
                    );
                    break;
                case "N":
                    filtered = vCards.value.filter((vCard) =>
                        vCard.name.toLowerCase().includes(term.toLowerCase())
                    );
                    break;
                case "E":
                    filtered = vCards.value.filter((vCard) =>
                        vCard.email.toLowerCase().includes(term.toLowerCase())
                    );
                    break;
            }
        }

        switch (sort) 
        {
            case "P":
                filtered.sort((a, b) => a.phone_number - b.phone_number)
                break;
            case "N":
                filtered.sort((a, b) => a.name.localeCompare(b.name))
                break;
            case "BA":
                filtered.sort((a, b) => a.balance - b.balance)
                break;
            case "BD":
                filtered.sort((a, b) => b.balance - a.balance)
                break;
            case "MA":
                filtered.sort((a, b) => a.max_debit - b.max_debit)
                break;
            case "MD":
                filtered.sort((a, b) => b.max_debit - a.max_debit)
                break;
        }
        
        filtered = filtered.filter( vC =>
            (status || status == vC.blocked))

      return filtered
    }

    async function deleteVCard(deleteVCard)
    {
        const response = await axios.delete('vcards/' + deleteVCard.phone_number, deleteVCard)
        let idx = vCards.value.findIndex((t) => t.phone_number === response.data.data.phone_number)
        if (idx >= 0) 
        {
            vCards.value.splice(idx, 1)
        }
        return response.data.data
    }  


    return {
        vCards,
        getVCardsByFilter,
        loadVCard,
        clearVCard,
        deleteVCard
    }

})