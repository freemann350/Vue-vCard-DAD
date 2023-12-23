<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from '../../stores/user.js'
import vCardProfileEdit from "../vCards/vCardProfileEdit.vue"
import UserProfileEdit from "../users/UserProfileEdit.vue"
import vCardRegister from '../vCards/vCardRegister.vue'
 
const toast = useToast()
const router = useRouter()
const userStore = useUserStore()
 
const props = defineProps({
  id: {
    type: Number,
    default: null
  }
})
 
const newUser = () => {
  return {
    id: null,
    name: '',
    email: '',
    password: '',
    confirmation_code: '',
    blocked: null,
    balance: null,
    max_debit: null
  }
}
 
const user = ref(newUser())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
let originalValueStr = ''
 
const loadUser = async (id) => {
  originalValueStr = ''
  errors.value = null
  if (!id || (id < 0)) 
  {
    user.value = newUser()
  } 
  else 
  {
    let userTypeURL = userStore.userType == 'V' ? 'vcards/' : 'admins/';
    try 
    {
      const response = await axios.get(userTypeURL + id)
      user.value = response.data.data
      originalValueStr = JSON.stringify(user.value)
    } 
    catch (error) {
      console.log(error)
    }
  }
}

const register = async(vcardToSave) => {
  errors.value = null
  try {
    const response = await axios.post('vcards', vcardToSave.value)
    vcardToSave.value = response.data.data

    toast.success('vCard was created successfully.')
    router.back()
  } catch (error) {
    if (error.response.status == 422) {
      errors.value = error.response.data.errors

      toast.error('vCard was not created due to validation errors!')
    } else {
      toast.error('vCard was not created due to unknown server error!')
    }
  }
}
 
const save = async () => {
  errors.value = null
  let userTypeURL = userStore.userType == 'V' ? 'vcards/' : 'admins/';
  try {
    const response = await axios.put(userTypeURL + props.id, user.value)
    user.value = response.data.data
    originalValueStr = JSON.stringify(user.value)
     
    let toast_string = userStore.userType == 'V' 
      ? 'vCard ' + user.value.phone_number
      : user.value.name 

    toast.success(toast_string + ' was updated successfully.')
    if (user.value.phone_number == userStore.userId || user.value.id == userStore.userId) {
        await userStore.loadUser()
    }
    router.back()
  } catch (error) {
    if (error.response.status == 422) {
      errors.value = error.response.data.errors
 
      let toast_string = userStore.userType == 'V' 
        ? 'vCard ' + user.value.phone_number
        : userStore.userName;
 
      toast.error(toast_string + ' was not updated due to validation errors!')
    } else {
      toast.error(toast_string + ' was not updated due to unknown server error!')
    }
  }
}
 
 const cancel = () => {
  if (props.id != -1) {
    originalValueStr = JSON.stringify(user.value)
  }
   router.back()
 }
 
 watch(
   () => props.id,
   (newValue) => {
       loadUser(newValue)
     },
   {immediate: true}  
 )
 
 let nextCallBack = null
 const leaveConfirmed = () => {
   if (nextCallBack) {
     nextCallBack()
   }
 }
 
 onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = JSON.stringify(user.value)
  if ((originalValueStr != newValueStr) && (props.id != -1)) {
    nextCallBack = next
    confirmationLeaveDialog.value.show()
  } else {
    next()
  }
})
 
 
 </script>
 
 <template>
   <confirmation-dialog
     ref="confirmationLeaveDialog"
     confirmationBtn="Discard changes and leave"
     msg="Do you really want to leave? You have unsaved changes!"
     @confirmed="leaveConfirmed"
   >
   </confirmation-dialog>  
 
   <vCardProfileEdit
     :user="user"
     :isAdmin="false"
     :errors="errors"
     @save="save"
     @cancel="cancel"
     v-if="userStore.userType == 'V'"
   ></vCardProfileEdit>
 
   <UserProfileEdit
     :user="user"
     :errors="errors"
     @save="save"
     @cancel="cancel"
     v-if="userStore.userType == 'A'"
   ></UserProfileEdit>

   <vCardRegister
    :errors="errors"
    @register="register"
    @cancel="cancel"
    v-if="props.id == -1"
  ></vCardRegister>
 </template>