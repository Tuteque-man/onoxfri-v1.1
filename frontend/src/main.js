import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia } from 'pinia'
import './style.css'
import App from './App.vue'

// Configurar rutas
const routes = [
  {
    path: '/',
    name: 'Landing',
    component: () => import('./views/Landing.vue'),
    meta: { requiresAuth: false, title: 'Bienvenida', description: 'Página de inicio y presentación' }
  },
  {
    path: '/auth',
    name: 'Auth',
    component: () => import('./views/Auth.vue'),
    meta: { requiresAuth: false, title: 'Autenticación', description: 'Inicio de sesión y registro' }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('./views/Dashboard.vue'),
    meta: { requiresAuth: true, title: 'Panel Principal', description: 'Vista general del sistema' }
  },
  {
    path: '/productos',
    name: 'Productos',
    component: () => import('./views/Productos.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/categorias',
    name: 'Categorias',
    component: () => import('./views/Categorias.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/empresas',
    name: 'Empresas',
    component: () => import('./views/Empresas.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/inventario',
    name: 'Inventario',
    component: () => import('./views/Inventario.vue'),
    meta: { requiresAuth: true, title: 'Inventario', description: 'Control de stock' }
  },
  {
    path: '/reportes',
    name: 'Reportes',
    component: () => import('./views/Reportes.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/usuarios',
    name: 'Usuarios',
    component: () => import('./views/Usuarios.vue'),
    meta: { requiresAuth: true, title: 'Usuarios', description: 'Gestión de usuarios' }
  },
  {
    path: '/configuracion',
    name: 'Configuracion',
    component: () => import('./views/Configuracion.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('./views/NotFound.vue'),
    meta: { requiresAuth: false }
  }
]

// Crear router
const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('onoxfri_token')

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Redirigir a auth si requiere autenticación pero no está autenticado
    next('/auth')
  } else if (to.path === '/auth' && isAuthenticated) {
    // Redirigir a dashboard si ya está autenticado e intenta ir a auth
    next('/dashboard')
  } else {
    next()
  }
})

// Crear aplicación Vue
const app = createApp(App)

// Usar plugins
app.use(createPinia())
app.use(router)

// Configurar tema inicial
const savedTheme = localStorage.getItem('onoxfri_theme') || 'light'
document.documentElement.setAttribute('data-theme', savedTheme)

// Montar aplicación
app.mount('#app')
