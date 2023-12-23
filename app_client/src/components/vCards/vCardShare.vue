<script setup>
import axios from 'axios'
import { ref, inject, onMounted, computed } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'


const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
    id: {
      type: Number,
      default: null
    }
})


const vCard = ref(null)

const vCardExists = computed(() => {
  if (vCard.value ) {
    return true
  }
  return false
});

const loadVCard = async () => 
{
  try 
  {
    const response = await axios.get('vcards/share/' + props.id)
    vCard.value = response.data.data
    photoFullUrl.value = serverBaseUrl + "/storage/fotos/" + vCard.value.photo_url
  } 
  catch (error) 
  {
    console.log(error)
  }
}

const photoFullUrl = ref(avatarNoneUrl)

onMounted(() => {
    loadVCard()
})
</script>

<template>
  <div v-if="vCardExists">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-5 pb-2 mb-3">
      <div class="w-50 pe-4">
        <h1 class="h2">vCard {{ vCard.phone_number }}</h1>
        <div class="mb-3">
            <label>Owner's Name</label>
            <input
              type="text"
              class="form-control"
              id="inputName"
              placeholder="User Name"
              disabled
              v-model="vCard.name"/>
          </div>
      </div>
      <div class="w-25">
          <div class="d-flex flex-column">
            <label class="form-label">Photo</label>
            <div class="form-control text-center">
              <img :src="photoFullUrl" class="w-100" />
            </div>
          </div>
        </div>
    </div>
  </div>
  <div v-else>
    <div class="container mt-5">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">This vCard Does Not Exist</h1>
      </div>

      <div class="text-center">
        <h1 class="display-1 text-danger">404</h1>
        <p class="lead">Page Not Found</p>
      </div>
    </div>
  </div>
</template>
  