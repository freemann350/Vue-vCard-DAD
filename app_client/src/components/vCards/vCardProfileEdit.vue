<script setup>
import { ref, watch, computed, inject } from "vue"
import { useRouter } from 'vue-router'
import avatarNoneUrl from '@/assets/avatar-none.png'
import QRCodeVue3 from "qrcode-vue3";



const serverBaseUrl = inject("serverBaseUrl")
const router = useRouter()

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  isAdmin: {
    type: Boolean,
    required: true,
  },
  errors: {
    type: Object,
    required: false,
  },
});

const emit = defineEmits(["save", "cancel"]);

const editingUser = ref(props.user)
const inputPhotoFile = ref(null)
const editingImageAsBase64 = ref(null)
const deletePhotoOnTheServer = ref(false)

const socket = inject("socket")

const vCardNumber = ref(null)
const valueString = computed(() =>
  serverBaseUrl + '/vcard/share/' + vCardNumber.value
)

watch(
  () => props.user,
  (newUser) => {
    editingUser.value = newUser
    vCardNumber.value = newUser.phone_number
  },
  { immediate: true }
)

const photoFullUrl = computed(() => {
  if (deletePhotoOnTheServer.value) {
    return avatarNoneUrl
  }
  if (editingImageAsBase64.value) {
    return editingImageAsBase64.value
  } else {
    return editingUser.value.photo_url
      ? serverBaseUrl + "/storage/fotos/" + editingUser.value.photo_url
      : avatarNoneUrl
  }
})

const save = () => {
  const vcardToSave = editingUser.value
  vcardToSave.deletePhotoOnServer = deletePhotoOnTheServer.value
  vcardToSave.base64ImagePhoto = editingImageAsBase64.value
  emit("save", vcardToSave)
}

const cancel = () => {
  emit("cancel", editingUser.value);
}

const changePhotoFile = () => {
  try {
    const file = inputPhotoFile.value.files[0]
    if (!file) {
      editingImageAsBase64.value = null
    } else {
      const reader = new FileReader()
      reader.addEventListener(
          'load',
          () => {
            editingImageAsBase64.value = reader.result
            deletePhotoOnTheServer.value = false
          },
          false,
      )
      if (file) {
        reader.readAsDataURL(file)
      }
    }
  } catch (error) {
    editingImageAsBase64.value = null
  }
}

const resetToOriginalPhoto = () => {
  deletePhotoOnTheServer.value = false
  inputPhotoFile.value.value = ''
  changePhotoFile()
}

const cleanPhoto = () => {
  deletePhotoOnTheServer.value = true
}

socket.on('vCardUpdated', (vCard) => {
        // A Small Amount of Trolling
        router.push({ name: 'Dashboard'})
    })

</script>


<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-50 pe-4">
        <h3 class="mt-5 mb-1">vCard - {{ editingUser.phone_number }}</h3>
      </div>
      <div class="w-50 pe-4">
        <h5 class="d-flex justify-content-end mt-5 mb-1">Total Balance:  {{ editingUser.balance }} €</h5>
      </div>
    </div>
    <hr />

    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-50 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['name'] : false }"
            id="inputName"
            placeholder="User Name"
            required
            v-model="editingUser.name"
          />
          <field-error-message :errors="errors" fieldName="name"></field-error-message>
        </div>

        <div class="mb-3 px-1">
          <label for="inputEmail" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['email'] : false }"
            id="inputEmail"
            placeholder="Email"
            required
            v-model="editingUser.email"
          />
          <field-error-message :errors="errors" fieldName="email"></field-error-message>
        </div>
        <div class="d-flex ms-1 mt-4 flex-wrap justify-content-between">
          <div class="mb-3 me-3 bg-lightgrey p-3 d-inline-flex align-items-center">
              <label class="me-3 mb-0" >
                Max Debit: 
              </label>
              <div class="d-inline-block align-middle">
                {{ editingUser.max_debit }} 
              </div>
          </div>
        </div>
        <div class="d-flex ms-1 mt-4 flex-wrap justify-content-between" v-if="isAdmin == true">
          <div class="mb-3 me-3 p-2 d-inline-flex align-items-center">
              <label class="me-3 mb-0" >
                Block vCard:
              </label>
              <div class="d-inline-block align-middle">
                <input
                  type="checkbox"
                  true-value="1"
                  false-value="0"
                  v-model="editingUser.blocked"/>  
              </div>
          </div>
          <div class="mb-3 me-1 p-3 ">
              <label class="me-3 mb-0" >
                Max Debit 
              </label>
              <input
                type="number"
                class="form-control"
                :class="{ 'is-invalid': errors ? errors['max_debit'] : false }"
                id="max_debit"
                v-model="editingUser.max_debit"
                required>
          </div>
        </div>
      </div>
      <div class="w-25">
        <div class="d-flex flex-column">
          <label class="me-3 mb-0" >
            Share vCard
          </label>
          <QRCodeVue3
            v-if="vCardNumber"
            :value="valueString"
            :imageOptions="{ hideBackgroundDots: true, imageSize: 0.4, margin: 0 }"
            :dotsOptions="{
              type: 'dots',
              color: '#06c20f',
              gradient: {
                type: 'linear',
                rotation: 0,
                colorStops: [
                  { offset: 0, color: '#06c20f' },
                  { offset: 1, color: '#06c20f' },
                ],
              },
            }"/>
        </div>
      </div>
      <div class="w-25">
        <div class="d-flex flex-column">
          <label class="form-label">Photo</label>
          <div class="form-control text-center">
            <img :src="photoFullUrl" class="w-100" />
          </div>
          <div class="mt-3 d-flex justify-content-between flex-wrap">
            <label for="inputPhoto" class="btn btn-dark flex-grow-1 mx-1">Upload</label>
            <button class="btn btn-secondary flex-grow-1 mx-1" @click.prevent="resetToOriginalPhoto" v-if="editingUser.photo_url">Default</button>
            <button class="btn btn-danger flex-grow-1 mx-1" @click.prevent="cleanPhoto" v-show="editingUser.photo_url || editingImageAsBase64">Delete</button>
          </div>
          <div>
            <field-error-message :errors="errors" fieldName="base64ImagePhoto"></field-error-message>
          </div>
        </div>
      </div>
    </div>
    <hr class="mt-5" v-if="isAdmin == false"/>
    <div class="mb-3 mt-5 d-flex justify-content-end">
        <button type="button" class="btn btn-primary px-5" @click="save" style="margin-right:10px">Save</button>
        <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>
  <input type="file" style="visibility:hidden;" id="inputPhoto" ref="inputPhotoFile" @change="changePhotoFile" />
</template>

<style scoped>
.bg-lightgrey {
  background-color: rgb(250, 250, 250);
}
</style>