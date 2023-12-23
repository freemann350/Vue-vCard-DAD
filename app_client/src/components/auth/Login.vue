<script setup>
import { useToast } from "vue-toastification"
import { useRouter, RouterLink } from 'vue-router'
import { useUserStore } from '../../stores/user.js'
import { ref } from 'vue'

const toast = useToast()
const router = useRouter()
const userStore = useUserStore()

const credentials = ref({
      username: '',
      password: ''
  })

const emit = defineEmits(['login'])

const login = async () => {
  if (await userStore.login(credentials.value)) {
    toast.success('User ' + userStore.user.name + ' has entered the application.')
    emit('login')
    if (userStore.user.user_type == 'V') {
      router.push({ name: 'Dashboard' })
    }
    else if (userStore.user.user_type == 'A') {
      router.push({ name: 'DashboardAdmin' })
    } 
  } else {
    credentials.value.password = ''
    toast.error('User credentials are invalid!')
  }
}

</script>


<template>
  <br>
  <form class="row g-3 needs-validation mt-4" novalidate @submit.prevent="login">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-6 bg-light p-4 rounded">
            <h3 class="mt-5 mb-3">Login</h3>
            <hr class="mt-4">
            <label for="inputUsername" class="form-label mt-4">Username</label>
            <div class="d-flex justify-content-center">
              
              <div class="input-group mb-3">
                <input type="text" class="form-control" id="inputUsername" required v-model="credentials.username">          
              </div>
            </div>
            <label for="inputPassword" class="form-label">Password</label>
            <div class="d-flex justify-content-center">
              <div class="input-group mb-3">
                <input type="password" class="form-control" id="inputPassword" required v-model="credentials.password">
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <div class="mb-3">
                <button type="button" class="btn btn-success px-5" @click="login">Login</button>
              </div>
            </div>
          </div>
        </div>
      </div>
  </form>
</template>

<style>
.floating-icon {
  position: fixed;
  bottom: 20px;
  right: 30px;
  color: black;
}
</style>
