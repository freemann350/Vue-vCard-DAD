<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch} from 'vue'
import DefaultCategoryDetail from "./DefaultCategoryDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'

const toast = useToast()
const router = useRouter()

const props = defineProps({
    id: {
      type: Number,
      default: null
    }
})

const newDefaultCategory = () => {
    return {
      id: null,
      name: '',
      type: null
    }
}

const defaultCategory = ref(newDefaultCategory())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
let originalValueStr = null

const inserting = (id) => !id || (id < 0)

const loadDefaultCategory = async (id) => {
  originalValueStr = JSON.stringify(newDefaultCategory())
  errors.value = null
  if (inserting(id)) {
    defaultCategory.value = newDefaultCategory()
  } else {
      try {
        const response = await axios.get('defaultcategories/' + id)
        defaultCategory.value = response.data.data
        originalValueStr = JSON.stringify(defaultCategory.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const save = async (defaultCategoryToSave) => {
  errors.value = null
  if (inserting(props.id)) {
    try {
      const response = await axios.post('defaultcategories', defaultCategoryToSave)
      defaultCategory.value = response.data.data
      originalValueStr = JSON.stringify(defaultCategory.value)
      toast.success('Default category #' + defaultCategory.value.id + ' was registered successfully.')
      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Default category was not registered due to validation errors!')
      } else {
        toast.error('Default category was not registered due to unknown server error!')
      }
    }
  } else {
    try {
      const response = await axios.put('defaultcategories/' + props.id, defaultCategoryToSave)
      defaultCategory.value = response.data.data
      originalValueStr = JSON.stringify(defaultCategory.value)
      toast.success('Default category #' + defaultCategory.value.id + ' was updated successfully.')
      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Default category #' + props.id + ' was not updated due to validation errors!')
      } else {
        toast.error('Default category #' + props.id + ' was not updated due to unknown server error!')
      }
    }
  }
}
const cancel = () => {
  originalValueStr = JSON.stringify(defaultCategory.value)
  router.back()
}

watch(
  () => props.id,
  (newValue) => {
    loadDefaultCategory(newValue)
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
  let newValueStr = JSON.stringify(defaultCategory.value)
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
  <default-category-detail
    :defaultCategory="defaultCategory"
    :errors="errors"
    :inserting="inserting(id)"
    @save="save"
    @cancel="cancel"
  ></default-category-detail>
</template>

<style>

</style>
  