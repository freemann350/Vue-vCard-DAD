import axios from 'axios'
import { ref, computed } from 'vue'
import { defineStore } from 'pinia'


export const useCategoriesStore = defineStore('categories', () => {
    const categories = ref([])

    const totalCategories = computed(() => {
        return categories.value.length
    })

    function clearCategories() {
        categories.value = []
    }

    async function loadCategories() {
        try {
            const response = await axios.get('categories')
            categories.value = response.data.data
            return categories.value
        } catch (error) {
            clearCategories()
            throw error
        }
    }

    async function insertCategory(newCategory) {
        const response = await axios.post('categories', newCategory)
        categories.value.push(response.data.data)
        return response.data.data
    }

    async function deleteCategory(deleteCategory) {
        const response = await axios.delete('categories/' + deleteCategory.id)
        let idx = categories.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            categories.value.splice(idx, 1)
        }
        return response.data.data
    }  

    function getCategoriesByFilter(searchTerm, type, sort) 
    {
        let filtered = categories.value
        const term = searchTerm.toString()

        filtered = categories.value.filter((category) =>
            category.name.toLowerCase().includes(term.toLowerCase())
        );
    
        switch (type) {
            case "C":
                filtered = filtered.filter((category) => category.type.includes("C"));
                break;
            case "D":
                filtered = filtered.filter((category) => category.type.includes("D"));
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
        categories,
        totalCategories,
        loadCategories,
        insertCategory,
        deleteCategory,
        getCategoriesByFilter
    }
})