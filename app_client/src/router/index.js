import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from "../stores/user.js"
import HomeView from '../views/HomeView.vue'
import Dashboard from "../components/Dashboard.vue"
import DashboardAdmin from "../components/DashboardAdmin.vue"
import Login from "../components/auth/Login.vue"
import ChangePassword from "../components/auth/ChangePassword.vue"
import ChangeConfirmationCode from "../components/auth/ChangeConfirmationCode.vue"
import DeleteVcard from "../components/auth/DeleteVcard.vue"
import Admins from "../components/admins/Admins.vue"
import Admin from "../components/admins/Admin.vue"
import Categories from "../components/categories/Categories.vue"
import Category from "../components/categories/Category.vue"
import DefaultCategories from "../components/defaultcategories/DefaultCategories.vue"
import DefaultCategory from "../components/defaultcategories/DefaultCategory.vue"
import AuthUsers from "../components/view_auth_users/AuthUsers.vue"
import vCardAdminDetails from "../components/vCards/vCardAdminDetails.vue"
import vCardAdmin from "../components/vCards/vCardAdmin.vue"
import vCardShare from "../components/vCards/vCardShare.vue"
import Transactions from "../components/transactions/Transactions.vue"
import Transaction from "../components/transactions/Transaction.vue"

let handlingFirstRoute = true

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/vcard/share/:id',
      name: 'vCardShare',
      component: vCardShare,
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue')
    },
    {
      path: '/login',
      name: 'Login',
      component: Login
    },
    {
      path: '/password',
      name: 'ChangePassword',
      component: ChangePassword
    },
    {
      path: '/confirmationcode',
      name: 'ChangeConfirmationCode',
      component: ChangeConfirmationCode
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: Dashboard
    },
    {
      path: '/dashboard',
      name: 'DashboardAdmin',
      component: DashboardAdmin
    },
    {
      path: '/admin/new',
      name: 'NewAdmin',
      component: Admin,
    },
    {
      path: '/admins',
      name: 'Admins',
      component: Admins,
    },
    {
      path: '/defaultcategories/new',
      name: 'NewDefaultCategory',
      component: DefaultCategory,
      props: { id: -1 }
    },
    {
      path: '/defaultcategories',
      name: 'DefaultCategories',
      component: DefaultCategories,
    },
    {
      path: '/defaultcategories/:id',
      name: 'DefaultCategory',
      component: DefaultCategory,
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/categories/new',
      name: 'NewCategory',
      component: Category,
      props: { id: -1 }
    },
    {
      path: '/categories',
      name: 'Categories',
      component: Categories,
    },
    {
      path: '/categories/:id',
      name: 'Category',
      component: Category,
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/vCard/new',
      name: 'vCardRegister',
      component: AuthUsers,
      props: route => ({ id: -1 })
    },
    {
      path: '/user/:id',
      name: 'User',
      component: AuthUsers,
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/vCards/',
      name: 'vCards',
      component: AuthUsers,
    },  
    { 
      path: '/vCard/delete',
      name: 'vCardDelete',
      component: DeleteVcard,
    },
    {
      path: '/vCard/:id',
      name: 'vCardEdit',
      component: AuthUsers,
      props: route => ({ id: parseInt(route.params.id) })
    },    
    {
      path: '/vCard/:id',
      name: 'vCardAdminDetails',
      component: vCardAdminDetails,
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/vCardsList/',
      name: 'vCardAdmin',
      component: vCardAdmin,
    },  
    {
      path: '/transactions/',
      name: 'Transactions',
      component: Transactions,
    },
    {
      path: '/transaction',
      name: 'NewTransaction',
      component: Transaction,
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/transaction/:id',
      name: 'Transaction',
      component: Transaction,
      props: route => ({ id: parseInt(route.params.id) })
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()
  if (handlingFirstRoute) {
    handlingFirstRoute = false
    await userStore.restoreToken()
  }
  if ((to.name == 'Login') || (to.name == 'home') || (to.name == 'vCardRegister') || (to.name == 'vCardShare'))  {
    next()
    return
  }
  if (!userStore.user) {
    next({ name: 'Login' })
    return
  }
  if (to.name == 'Categories' || to.name == 'NewCategory' || to.name == 'Category' || to.name == 'Transactions' || to.name == 'Transaction' || to.name == 'vCardDelete' || to.name == 'ChangeConfirmationCode') {
    if (userStore.userType != 'V') {
      next({ name: 'home' })
      return
    }
  }

  if (to.name == 'DefaultCategories' || to.name == 'NewDefaultCategory' || to.name == 'DefaultCategory' || to.name == 'Admins' || to.name == 'Admin' || to.name == 'vCardAdmin' || to.name == 'NewAdmin' || to.name == 'vCards') {
    if (userStore.userType != 'A') {
      next({ name: 'home' })
      return
    }
  }
  if (to.name == 'Dashboard') {
    if (userStore.user.type == 'A') {
      next({ name: 'DashboardAdmin' })
      return
    }
  }
  if (to.name == 'User') {
    if ((userStore.userType == 'A') && (userStore.userId == to.params.id)) {
      next()
      return
    }
    next({ name: 'home' })
    return
  }
  if (to.name == 'vCardAdminDetails') {
    if ((userStore.userId == to.params.id || userStore.userType == 'A')) {
      next()
      return
    }
    next({ name: 'home' })
    return
  }

  if (to.name == 'vCardDelete') {
    if (( userStore.userType == 'V' && userStore.user.balance == 0)) {
      next()
      return
    }
    next({ name: 'home' })
    return
  }
  next()
})


export default router