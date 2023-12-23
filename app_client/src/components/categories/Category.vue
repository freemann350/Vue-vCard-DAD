<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch} from 'vue'
import CategoryDetail from "./CategoryDetail.vue"
import { useUserStore } from '../../stores/user'
import { useRouter, onBeforeRouteLeave } from 'vue-router'

const toast = useToast()
const router = useRouter()
const userStore = useUserStore()
const props = defineProps({
    id: {
      type: Number,
      default: null
    }
})

const newCategory = () => {
    return {
      id: null,
      name: '',
      vcard: String(userStore.userId),
      type: null
    }
}

const category = ref(newCategory())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
let originalValueStr = null

const inserting = (id) => !id || (id < 0)

const loadCategory = async (id) => {
  originalValueStr = JSON.stringify(newCategory())
  errors.value = null
  if (inserting(id)) {
    category.value = newCategory()
  } else {
      try {
        const response = await axios.get('categories/' + id)
        category.value = response.data.data
        originalValueStr = JSON.stringify(category.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const save = async (categoryToSave) => {
  errors.value = null
  if (inserting(props.id)) {
    try {
      const response = await axios.post('categories', categoryToSave)
      category.value = response.data.data
      originalValueStr = JSON.stringify(category.value)
      toast.success('Category "' + category.value.name + '" was registered successfully.')
      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Category was not registered due to validation errors!')
      } else {
        toast.error('Category was not registered due to unknown server error!')
      }
    }
  } else {
    try {
      const response = await axios.put('categories/' + props.id, categoryToSave)
      category.value = response.data.data
      originalValueStr = JSON.stringify(category.value)
      toast.success('Category "' + category.value.name + '" was updated successfully.')
      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Category "' + props.name + '" was not updated due to validation errors!')
      } else {
        toast.error('Category "' + props.name + '" was not updated due to unknown server error!')
      }
    }
  }
}
const cancel = () => {
  originalValueStr = JSON.stringify(category.value)
  router.back()
}

watch(
  () => props.id,
  (newValue) => {
    loadCategory(newValue)
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
  let newValueStr = JSON.stringify(category.value)
  if (originalValueStr != newValueStr && to.name !== 'Login') {
    // Some value has changed - only leave after confirmation
    nextCallBack = next
    confirmationLeaveDialog.value.show()
  } else {
    // No value has changed, so we can leave the component without confirming
    next()
  }
})


</script>
  
<template>
  <ConfirmationDialog
    ref="confirmationLeaveDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </ConfirmationDialog>
  <category-detail
    :category="category"
    :errors="errors"
    :inserting="inserting(id)"
    @save="save"
    @cancel="cancel"
  ></category-detail>
</template>
