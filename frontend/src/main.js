import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router' // Importamos nuestro nuevo módulo de router
import './assets/styles/main.scss'
import './style.css'
import App from './App.vue'

// Crear aplicación Vue
const app = createApp(App)

// Usar plugins
app.use(createPinia())
app.use(router)

// Función para inicializar la aplicación
function initializeApp() {
  // Configurar tema inicial
  const savedTheme = localStorage.getItem('onoxfri_theme') || 'light'
  document.documentElement.setAttribute('data-theme', savedTheme)

  // Montar aplicación
  app.mount('#app')
}

initializeApp()
