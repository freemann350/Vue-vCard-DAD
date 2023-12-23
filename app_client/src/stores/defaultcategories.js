import axios from 'axios'
import { ref, computed } from 'vue'
import { defineStore } from 'pinia'


export const useDefaultCategoriesStore = defineStore('defaultCategories', () => {
    const defaultCategories = ref([])

    const totalDefaultCategories = computed(() => {
        return defaultCategories.value.length
    })

    function clearDefaultCategories() {
        defaultCategories.value = []
    }

    async function loadDefaultCategories() {
        try {
            const response = await axios.get('defaultcategories')
            defaultCategories.value = response.data.data
            return defaultCategories.value
        } catch (error) {
            clearDefaultCategories()
            throw error
        }
    }

    async function insertDefaultCategory(newDefaultCategory) {
        const response = await axios.post('defaultcategories', newDefaultCategory)
        defaultCategories.value.push(response.data.data)
        return response.data.data
    }

    async function deleteDefaultCategory(deleteDefaultCategory) {
        const response = await axios.delete('defaultcategories/' + deleteDefaultCategory.id)
        let idx = defaultCategories.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            defaultCategories.value.splice(idx, 1)
        }
        return response.data.data
    }  

    function getDefaultCategoriesByFilter(searchTerm, type, sort) 
    {
        let filtered = defaultCategories.value
        const term = searchTerm.toString()

        filtered = defaultCategories.value.filter((defaultCategory) =>
            defaultCategory.name.toLowerCase().includes(term.toLowerCase())
        );
    
        switch (type) {
            case "C":
                filtered = filtered.filter((defaultCategory) => defaultCategory.type.includes("C"));
                break;
            case "D":
                filtered = filtered.filter((defaultCategory) => defaultCategory.type.includes("D"));
                break;
        }

        switch (sort) 
        {
            case "I":
                filtered.sort((a, b) => a.id - b.id)
                break;
            case "N":
                filtered.sort((a, b) => a.name.localeCompare(b.name))
                break;
            case "T":
                filtered.sort((a, b) => a.type.localeCompare(b.type))
                break;
        }

      return filtered
    }

    return {
        defaultCategories,
        totalDefaultCategories,
        loadDefaultCategories,
        insertDefaultCategory,
        deleteDefaultCategory,
        getDefaultCategoriesByFilter
    }
})