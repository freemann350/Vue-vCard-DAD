import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { useRouter, RouterLink, RouterView } from 'vue-router'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'
import { useToast } from "vue-toastification"

export const useUserStore = defineStore('user', () => 
{
    const socket = inject("socket")
    const toast = useToast()
    const router = useRouter()

    const serverBaseUrl = inject('serverBaseUrl')

    const user = ref(null)

    const userName = computed(() => user.value?.name ?? 'Anonymous')

    const userId = computed(() => user.value?.id ?? -1)
    const userType = computed(() => user.value?.user_type ?? null)

    const userPhotoUrl = computed(() =>
        user.value?.photo_url
        ? serverBaseUrl + '/storage/fotos/' + user.value.photo_url
        : avatarNoneUrl
    )

    async function loadUser() 
    {
        try 
        {
            const response = await axios.get('users/me')
            user.value = response.data.data

            if (user.value.user_type == 'V') {
                const responseBalance = await axios.get('vcards/' + user.value.id)
                user.value.balance = responseBalance.data.data.balance
            }   
            socket.emit('loggedIn', user.value)
        } 
        catch (error) 
        {
            clearUser()
            throw error
        }
    }

    function clearUser() {
        delete axios.defaults.headers.common.Authorization
        sessionStorage.removeItem('token')
        user.value = null
    }

    async function login(credentials) {
        try {
            const response = await axios.post('login', credentials)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)
            await loadUser()
            return true
        }
        catch(error) {
            clearUser()
            return false
        }
    }

    async function logout () {
        try {
            await axios.post('logout')
            socket.emit('loggedOut', user.value)
            clearUser()
            return true
        } catch (error) {
            return false
        }
    }

    async function changePassword(credentials) {
        if (userId.value < 0) {
            throw 'Anonymous users cannot change the password!'
        }
        try {
            if (userType.value == 'V'){
                await axios.patch(`vcards/${user.value.id}/password`, credentials)
                return true
            } else if (userType.value == 'A'){
                await axios.patch(`admins/${user.value.id}/password`, credentials)
                return true
            }
        } catch (error) {
            throw error
        }
    }

    async function changeConfirmationCode(credentials) {
        if (userId.value < 0) {
            throw 'Anonymous users cannot change the confirmation code!'
        }
        try {
            await axios.patch(`vcards/${user.value.id}/confirmation_code`, credentials)
            return true
        } catch (error) {
            throw error
        }
    }

    async function deleteVcard(credentials) {
        if (userId.value < 0) {
            throw 'Anonymous users cannot delete any vCard!'
        }
        try {
            let request = null
            request = await axios.post(`vcards/${user.value.id}/verify`, credentials)
            if (request.data) {
                request = await axios.delete(`vcards/${user.value.id}`)
                router.push({ name: 'home' })
                clearUser()
            }
            return true
        } catch (error) {
            throw error
        }
    }

    async function restoreToken () {
        let storedToken = sessionStorage.getItem('token')
        if (storedToken) {
            axios.defaults.headers.common.Authorization = "Bearer " + storedToken
            await loadUser()
            socket.emit('loggedIn', user.value)
            
            if (user.value.user_type == 'V') {
                router.push({ name: 'Dashboard' })
            }
            else
            {
                router.push({ name: 'DashboardAdmin' })
            }
           
            return true
        }
        clearUser()
        return false
    }

    socket.on('vCardUpdated', (vCard) => {
        user.value.balance = vCard.balance
        user.value.email = vCard.email
        user.value.name = vCard.name
        user.value.blocked = vCard.blocked
        user.value.photo_url = vCard.photo_url
        toast.info(`vCard profile #${vCard.phone_number} (${vCard.name}) has changed!`)
    })

    socket.on('vCardBlockedOrDeleted', () => {
        logout()
        toast.error(`Your vCard has Been Blocked or Banned.`)
    })

    socket.on('vCardTransactionNotify', (transactionToSave) => {
        user.value.balance = Number(user.value.balance) + Number(transactionToSave);
        toast.info(`Balance Update`)
    })
    socket.on('vCardAddCredit', (transactionToSave) => {
        user.value.balance = Number(transactionToSave);
        user.value.balance = user.value.balance.toFixed(2)
        toast.info(`Balance Update`)
    })
    
    return {
        user,
        userId,
        userName,
        userType,
        userPhotoUrl,
        loadUser,
        clearUser,
        login,
        logout,
        restoreToken,
        changePassword,
        changeConfirmationCode,
        deleteVcard
    }
})
