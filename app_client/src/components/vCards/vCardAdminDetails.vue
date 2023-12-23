<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import vCardProfileEdit from "../vCards/vCardProfileEdit.vue"
import { useUserStore } from '../../stores/user'

const toast = useToast()
const router = useRouter()
const userStore = useUserStore()
const socket = inject("socket")

const props = defineProps({
  id: {
    type: Number,
    default: null
    }
})

const newVCard = () => {
  return {
    id: null,
    name: '',
    email: '', 
    photo_url: null
  }
}
const vCard = ref(newVCard())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
let originalValueStr = ''

const loadVCard = async (id) => 
{
  originalValueStr = ''
  try 
  {
    const response = await axios.get('vcards/' + id)
    vCard.value = response.data.data
    originalValueStr = JSON.stringify(vCard.value)
  } 
  catch (error) {
    console.log(error)
  }
}

const save = async () => {
  errors.value = null
  try 
  {
    if (userStore.userType == 'A') {
      await axios.patch('vcards/' + props.id + '/max_debit', vCard.value)

      await axios.patch('vcards/' + props.id + '/block', vCard.value)

      if (vCard.value.blocked == 1) 
      {
        socket.emit('vCardBlockedOrDeleted', vCard.value)
      }
    }
    const response = await axios.put('vcards/' + props.id, vCard.value)
    vCard.value = response.data.data
    if (userStore.userId == props.id)
      userStore.user.name = vCard.value.name
    originalValueStr = JSON.stringify(vCard.value)

    toast.success('vCard ' + vCard.value.phone_number + ' was updated successfully.')

    socket.emit('vCardUpdated', vCard.value)

    router.back()
  } 
  catch (error) 
  {
    if (error.response.status == 422) 
    {
      errors.value = error.response.data.errors;
      toast.error('vCard ' + vCard.value.phone_number + ' was not updated due to validation errors!')
    } 
    else 
    {
      toast.error('vCard ' + vCard.value.phone_number + ' was not updated due to unknown server error!')
    }
  }

}

const cancel = () => {
  originalValueStr = JSON.stringify(vCard.value)
  router.back()
}

watch(
  () => props.id,
  (newValue) => {
    loadVCard(newValue)
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
  let newValueStr = JSON.stringify(vCard.value)
  if (originalValueStr != newValueStr && to.name !== 'Login') {
    nextCallBack = next
    confirmationLeaveDialog.value.show()
  } else {
    next()
  }
})

</script>

<template>

  <ConfirmationDialog
    ref="confirmationLeaveDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed">
  </ConfirmationDialog>

    <vCardProfileEdit
    :user="vCard"
    :isAdmin="userStore.userType == 'A'"
    :errors="errors"
    @save="save"
    @cancel="cancel"
    ></vCardProfileEdit>
</template>
  