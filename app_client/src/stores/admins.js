import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useToast } from "vue-toastification"


export const useAdminsStore = defineStore('admins', () => {
    const admins = ref([])
    const socket = inject("socket")
    const toast = useToast()

    const totalAdmins = computed(() => {
        return admins.value.length
    })

    function clearAdmins() {
        admins.value = []
    }

    async function loadAdmins() {
        try {
            const response = await axios.get('admins')
            admins.value = response.data.data
            return admins.value
        } catch (error) {
            clearAdmins()
            throw error
        }
    }

    async function insertAdmin(newAdmin) {
        const response = await axios.post('admins', newAdmin)
        admins.value.push(response.data.data)
        socket.emit('insertedAdmin', response.data.data)
        return response.data.data
    }

    async function deleteAdmin(deleteAdmin) {
        const response = await axios.delete('admins/' + deleteAdmin.id)
        let idx = admins.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            admins.value.splice(idx, 1)
        }
        return response.data.data
    }  

    function getAdminsByFilter(searchTerm, searchField, sort) 
    {
        let filtered = admins.value
        const term = searchTerm.toString()

        switch (searchField) {
            case "N":
                filtered = admins.value.filter((admin) =>
                    admin.name.toLowerCase().includes(term.toLowerCase())
                );
                break;
            case "E":
                filtered = admins.value.filter((admin) =>
                    admin.email.toLowerCase().includes(term.toLowerCase())
                );
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
            case "E":
                filtered.sort((a, b) => a.email.localeCompare(b.email))
                break;
        }

      return filtered
    }

    socket.on('insertedAdmin', (Admin) => {
        admins.value.push(Admin)
        toast.success(`A new Admin was created (#${Admin.id} : ${Admin.name})`)
    })

    return {
        admins,
        totalAdmins,
        loadAdmins,
        insertAdmin,
        deleteAdmin,
        getAdminsByFilter
    }
})