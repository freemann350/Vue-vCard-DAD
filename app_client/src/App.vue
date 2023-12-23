<script setup>
import { useRouter, RouterLink, RouterView } from 'vue-router'
import { useToast } from "vue-toastification"
import { useUserStore } from './stores/user.js'
import { onMounted } from 'vue';

const toast = useToast()
const userStore = useUserStore()
const router = useRouter()

const logout = async () => {
  if (await userStore.logout()) {
    toast.success('User has logged out of the application.')
    clickMenuOption()
    router.push({ name: 'Login' })
  } else {
    toast.error('There was a problem logging out of the application!')
  }
}

const clickMenuOption = () => {
  const domReference = document.getElementById('buttonSidebarExpandId')
  if (domReference) {
    if (window.getComputedStyle(domReference).display !== "none") {
      domReference.click()
    }
  }
}

onMounted(() => {
      document.title = 'vCard App';
});
</script>

<template>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top flex-md-nowrap p-0 shadow">
    <router-link class="navbar-brand col-md-3 col-lg-2 me-0" :to="{ name: 'home' }" @click="clickMenuOption">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#079E65" class="bi bi-credit-card-2-back-fill add_margin" viewBox="0 0 16 16">
        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5H0zm11.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM0 11v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1z"/>
      </svg>
      vCard
    </router-link>
    <button id="buttonSidebarExpandId" class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item" v-show="!userStore.user">
          <router-link class="nav-link" :class="{ active: $route.name === 'vCardRegister'}" :to="{ name: 'vCardRegister' }" @click="clickMenuOption">
            <i class="bi bi-person-check-fill"></i>
            Register
          </router-link >
        </li>
        <li class="nav-item" v-show="!userStore.user">
          <router-link class="nav-link" :class="{ active: $route.name === 'Login' }"
                        :to="{ name: 'Login' }" @click="clickMenuOption">
            <i class="bi bi-box-arrow-in-right"></i>
            Login
          </router-link>
        </li>
        <li class="nav-item centered-content" v-if="userStore.user && userStore.userType == 'V'">
          <router-link class="nav-link bg-lightgrey rounded-pill py-2" 
                        :class="{ active: $route.name == 'User' && $route.params.id == userStore.userId }"
                        :to="{ name: 'User', params: { id: userStore.userId }}" @click="clickMenuOption">
            <span class="text-white fw-bold d-block">Balance:  {{ userStore.user.balance }} € </span>
          </router-link>
        </li>
        <li class="nav-item dropdown" v-show="userStore.user">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img :src="userStore.userPhotoUrl" class="rounded-circle z-depth-0 avatar-img" alt="avatar image">
            <span class="avatar-text">{{ userStore.userName }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
            <li>
              <router-link class="dropdown-item"
                          :class="{ active: $route.name == 'vCardAdminDetails' && $route.params.id == userStore.userId }"
                          :to="{ name: 'vCardAdminDetails', params: { id: userStore.userId }}" @click="clickMenuOption"
                          v-if="userStore.user && userStore.userType == 'V'">
                <i class="bi bi-person-square"></i>
                Profile
              </router-link>
              <router-link class="dropdown-item"
                          :class="{ active: $route.name == 'User' && $route.params.id == userStore.userId }"
                          :to="{ name: 'User', params: { id: userStore.userId }}" @click="clickMenuOption"
                          v-if="userStore.user && userStore.userType == 'A'">
                <i class="bi bi-person-square"></i>
                Profile
              </router-link>
            </li>
            <li>
              <router-link class="dropdown-item" :class="{ active: $route.name === 'ChangePassword' }"
                            :to="{ name: 'ChangePassword' }" @click="clickMenuOption">
                <i class="bi bi-key-fill"></i>
                Change Password
              </router-link>
            </li>
            <li v-if="userStore.user && userStore.userType == 'V'">
              <router-link class="dropdown-item" :class="{ active: $route.name === 'ChangeConfirmationCode' }"
                            :to="{ name: 'ChangeConfirmationCode' }" @click="clickMenuOption">
                <i class="bi bi-key-fill"></i>
                Change Code Confirmation
              </router-link>
            </li>
            <li v-if="userStore.user && userStore.userType == 'V' && userStore.user.balance == 0">
              <router-link class="dropdown-item" :class="{ active: $route.name === 'vCardDelete' }"
                            :to="{ name: 'vCardDelete'}" @click="clickMenuOption">
              <i class="bi bi-trash"></i>
                vCard Deletion
              </router-link>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item" @click.prevent="logout">
                <i class="bi bi-arrow-right"></i>Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" v-if="userStore.user">
        <div class="position-sticky pt-3">
          <ul class="nav flex-column" v-if="userStore.user">
            <li class="nav-item" v-if="userStore.user.user_type == 'V'">
              <router-link class="nav-link" :class="{ active: $route.name === 'Dashboard' }"
                          :to="{ name: 'Dashboard' }" @click="clickMenuOption">
                <i class="bi bi-house"></i>
                Dashboard
              </router-link>
            </li>
            <li class="nav-item" v-if="userStore.user.user_type == 'A'">
              <router-link class="nav-link" :class="{ active: $route.name === 'DashboardAdmin' }"
                          :to="{ name: 'DashboardAdmin' }" @click="clickMenuOption">
                <i class="bi bi-house"></i>
                Dashboard
              </router-link>
              
            </li>
            <li class="nav-item" v-if="userStore.userType == 'V'">
              <router-link class="nav-link" :class="{ active: $route.name === 'Categories' }"
                          :to="{ name: 'Categories' }" @click="clickMenuOption">
                <i class="bi bi-tag-fill"></i>
                Categories
              </router-link>
            </li>
            <li class="nav-item" v-if="userStore.userType == 'V'">
              <router-link class="nav-link" :class="{ active: $route.name === 'Transactions' }"
                          :to="{ name: 'Transactions' }" @click="clickMenuOption">
                <i class="bi bi-journal-text"></i>
                Transactions
              </router-link>
            </li> 
          </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted" v-if="userStore.userType == 'A'">
            <span>Administration</span>
          </h6>
          <ul class="nav flex-column mb-2" v-if="userStore.user">
            <li class="nav-item" v-if="userStore.userType == 'A'">
              <router-link class="nav-link" :class="{ active: $route.name === 'Admins' }"
                          :to="{ name: 'Admins' }" @click="clickMenuOption">
                <i class="bi bi-person-fill-lock"></i>
                Admins
              </router-link>
            </li>
            <li class="nav-item" v-if="userStore.userType == 'A'">
              <router-link class="nav-link" :class="{ active: $route.name === 'DefaultCategories' }"
                          :to="{ name: 'DefaultCategories' }" @click="clickMenuOption">
                <i class="bi bi-tag"></i>
                Default Categories
              </router-link>
            </li>
            <li class="nav-item"  v-if="userStore.userType == 'A'">
              <router-link class="nav-link" :class="{ active: $route.name === 'vCardAdmin' }"
                          :to="{ name: 'vCardAdmin' }" @click="clickMenuOption">
                <i class="bi bi-credit-card"></i>
                vCards
              </router-link>
            </li>
            <li class="nav-item" v-if="userStore.userType == 'A'">
              <router-link class="nav-link" :class="{ active: $route.name === 'NewTransaction' }"
                          :to="{ name: 'NewTransaction' }" @click="clickMenuOption">
                <i class="bi bi-arrow-left-right"></i>
                Create transaction
              </router-link>
            </li>
          </ul>

          <div class="d-block d-md-none">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>User</span>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item" v-show="!userStore.user">
                <router-link class="nav-link" :class="{ active: $route.name === 'vCardRegister'}" :to="{ name: 'vCardRegister'}"  @click="clickMenuOption">
                  <i class="bi bi-person-check-fill"></i>
                  Register
                </router-link>
              </li>
              <li class="nav-item" v-show="!userStore.user">
                <router-link class="nav-link" :class="{ active: $route.name === 'Login' }"
                              :to="{ name: 'Login' }" @click="clickMenuOption">
                  <i class="bi bi-box-arrow-in-right"></i>
                  Login
                </router-link>
              </li>
              <li class="nav-item dropdown" v-show="userStore.user">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink2" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <img :src="userStore.userPhotoUrl" class="rounded-circle z-depth-0 avatar-img" alt="avatar image">
                  <span class="avatar-text">{{ userStore.userName }}</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                  <li>
                    <router-link class="dropdown-item"
                            :class="{ active: $route.name == 'User' && $route.params.id == userStore.userId }"
                            :to="{ name: 'User', params: { id: userStore.userId }}" @click="clickMenuOption">
                      <i class="bi bi-person-square"></i>
                      Profile
                    </router-link>
                  </li>
                  <li>
                    <router-link class="dropdown-item" :class="{ active: $route.name === 'ChangePassword' }"
                                  :to="{ name: 'ChangePassword' }" @click="clickMenuOption">
                      <i class="bi bi-key-fill"></i>
                      Change password
                    </router-link>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <a class="dropdown-item" @click.prevent="logout">
                      <i class="bi bi-arrow-right"></i>Logout
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>

        </div>
      </nav>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<style>
@import "./assets/dashboard.css";

.avatar-img {
  margin: -1.2rem 0.8rem -2rem 0.8rem;
  width: 3.3rem;
  height: 3.3rem;
}

.avatar-text {
  line-height: 2.2rem;
  margin: 1rem 0.5rem -2rem 0;
  padding-top: 1rem;
}

.dropdown-item {
  font-size: 0.875rem;
}

.btn:focus {
  outline: none;
  box-shadow: none;
}

#sidebarMenu {
  overflow-y: auto;
}
.add_margin{
  margin-left: 15px;
}
.bg-lightgrey {
  background-color: rgb(117, 129, 145);
}
.centered-content {
  display: flex  !important;
  justify-content: center  !important;
  align-items: center  !important;
  margin-right: 10px;
}


</style>