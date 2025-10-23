<template>
   <div id="app" class="onoxfri-container">
     <!-- Fondo matrix para toda la aplicación -->
     <div class="matrix-bg"></div>

     <!-- Layout principal estructurado (oculto en /auth) -->
    <div class="app-layout" v-if="!isAuth">
       <!-- Header con navegación de fotogramas -->
       <header class="app-header" :class="{ 'no-sidebar': isLanding || isFullWidth }">
         <div class="header-content" v-if="!isDashboard">
           <div class="header-left">
             <div class="app-logo">
               <img src="@/assets/img/l1.png" alt="ONOXFRI Logo" class="header-logo-img" />
               <span class="app-logo-text">ONOXFRI</span>
             </div>
           </div>
        </div>
       </header>
 
       <!-- Contenido principal -->
       <main class="app-main" :class="{ 'no-sidebar': isLanding || isFullWidth }">
         <router-view />
       </main>

       <!-- Floating AI Assistant Button -->
       <template v-if="isDashboard">
         <button class="ai-fab" type="button" aria-label="Asistente IA" @click="showAi = true">
           <i class="fas fa-robot"></i>
         </button>
         <!-- AI Modal -->
         <AIAssistantModal :visible="showAi" @close="showAi = false" />
       </template>
       <!-- Downbar móvil -->
       <Downbar />
    </div>
 
    <!-- Vista limpia para /auth (sin header/sidebar) -->
    <div v-else class="auth-route-container">
      <router-view v-slot="{ Component }">
        <transition name="fade-slide" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </div>

    <!-- Indicador de carga global -->
    <div v-if="isLoading" class="global-loading">
      <div class="loading-futuristic"></div>
      <div class="loading-text">Cargando ONOXFRI...</div>
    </div>
  </div>
</template>


<script setup>
 import { ref, computed, onMounted } from 'vue'
 import { useRoute } from 'vue-router'
 import AIAssistantModal from '@/components/AIAssistantModal.vue'
 import Downbar from '@/components/Downbar.vue'

 const isLoading = ref(false)
 const showAi = ref(false)

 const route = useRoute()
 const routeMetaTitle = computed(() => route.meta?.title || '')
 const routeMetaDescription = computed(() => route.meta?.description || '')
 const isLanding = computed(() => route.name === 'landing')
 const isAuth = computed(() => route.name === 'auth')
 const isFullWidth = computed(() => Boolean(route.meta?.fullWidth))
 const isDashboard = computed(() => route.name === 'dashboard')

 const currentTheme = computed(() => {
   return localStorage.getItem('onoxfri_theme') || 'dark'
 })

 const toggleTheme = () => {
   const newTheme = currentTheme.value === 'dark' ? 'light' : 'dark'
   localStorage.setItem('onoxfri_theme', newTheme)
   document.documentElement.setAttribute('data-theme', newTheme)
 }

 onMounted(() => {
   document.documentElement.setAttribute('data-theme', currentTheme.value)
 })
 </script>

<style lang="scss" scoped>
 /* =====================================================
    ONOXFRI - LAYOUT CON SIDEBAR MODERNO
    ===================================================== */

 /* Reset global */
 *, *::before, *::after {
   box-sizing: border-box;
 }

 html, body {
   margin: 0;
   padding: 0;
   width: 100%;
   height: 100%;
   overflow-x: hidden;
   font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
   background: #000;
 }

 /* Contenedor principal */
 .onoxfri-container {
  position: relative;
  width: 100%;
  min-height: 100vh;
  background: #000;
  z-index: 1;
 }

 /* Fondo matrix animado */
 .matrix-bg {
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   background:
     radial-gradient(circle at 20% 80%, rgba(75, 0, 130, 0.3) 0%, transparent 50%),
     radial-gradient(circle at 80% 20%, rgba(107, 31, 168, 0.2) 0%, transparent 50%),
     linear-gradient(135deg, #000 0%, #0a0a0a 50%, #000 100%);
   animation: matrixPulse 8s ease-in-out infinite;
   z-index: 1;
 }

 @keyframes matrixPulse {
   0%, 100% {
     opacity: 1;
     transform: scale(1);
   }
   50% {
     opacity: 0.8;
     transform: scale(1.02);
   }
 }

/* Rejilla de fondo para Landing */
.onoxfri-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(75,0,130,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 0.3;
  z-index: 1; /* Asegura que esté detrás del contenido */
}

 /* Layout principal con sidebar */
 .app-layout {
  display: flex;
  flex-direction: column;
  width: 100%;
  min-height: 100vh;
  position: relative;
  margin: 0;
  padding: 0;
  z-index: 2;
}

/* =====================================================
   SIDEBAR DE NAVEGACIÓN
   ===================================================== */

.app-sidebar {
  width: 280px;
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  z-index: 1000;
  transition: transform 0.3s ease;
  transform: translateX(0);
}

.sidebar-collapsed .app-sidebar {
  transform: translateX(-280px);
}

/* =====================================================
   HEADER SUPERIOR SIMPLIFICADO
   ===================================================== */

.app-header {
  position: sticky;
  top: 0;
  z-index: 900;
  background: rgba(0, 0, 0, 0.95);
  backdrop-filter: blur(10px);
  border-bottom: 2px solid rgba(75, 0, 130, 0.3);
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
  margin-left: 0;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 2rem;
  min-height: 80px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.app-logo {
  display: flex;
  align-items: center;
  gap: 1rem;

  .header-logo-img {
    height: 40px;
    width: auto;
    object-fit: contain;
    filter: drop-shadow(0 0 8px rgba(75, 0, 130, 0.3));
    transition: all 0.3s ease;
  }

  .header-logo-img:hover {
    filter: drop-shadow(0 0 15px rgba(75, 0, 130, 0.5));
    transform: scale(1.05);
  }

  .app-logo-text {
    font-family: 'Orbitron', monospace;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    letter-spacing: 2px;
  }
}

.header-center {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;

  .page-title {
    text-align: center;

    h1 {
      font-family: 'Orbitron', monospace;
      font-size: 1.5rem;
      font-weight: 700;
      color: #4B0082;
      margin: 0 0 0.25rem 0;
      text-shadow: 0 0 10px rgba(75, 0, 130, 0.3);
    }

    p {
      font-size: 0.875rem;
      color: #ccc;
      margin: 0;
      opacity: 0.8;
    }
  }
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.theme-toggle-btn {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: rgba(75, 0, 130, 0.1);
  color: #4B0082;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.theme-toggle-btn:hover {
  background: rgba(75, 0, 130, 0.2);
  transform: scale(1.1);
}

.sidebar-toggle-btn {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: rgba(75, 0, 130, 0.1);
  color: #4B0082;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.sidebar-toggle-btn:hover {
  background: rgba(75, 0, 130, 0.2);
  transform: scale(1.1);
}

/* =====================================================
   CONTENIDO PRINCIPAL
   ===================================================== */

.app-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  margin-left: 280px;
  min-height: 100vh;
  height: auto;
  position: relative;
  transition: margin-left 0.3s ease;
  overflow-y: auto;
}

.sidebar-collapsed .app-main {
  margin-left: 0;
}

/* Ajuste para cuando no hay navegación administrativa */
.app-main.no-admin-nav {
  margin-left: 0;
}

/* Quitar margen cuando no hay sidebar (ej. Landing page) */
.app-main.no-sidebar {
  margin-left: 0;
}

.main-container {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  background: rgba(0, 0, 0, 0.5);
}

.page-content {
  flex: 1;
  width: 100%;
  position: relative;
  overflow-y: auto;
  overflow-x: hidden;
  box-sizing: border-box;
  padding: 2rem;
}

/* Scrollbar personalizado para el contenido */
.page-content::-webkit-scrollbar {
  width: 8px;
}

.page-content::-webkit-scrollbar-track {
  background: rgba(75, 0, 130, 0.1);
  border-radius: 4px;
}

.page-content::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #4B0082, #6B1FA8);
  border-radius: 4px;
}

.page-content::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #6B1FA8, #8A2BE2);
}

/* =====================================================
   INDICADOR DE CARGA GLOBAL
   ===================================================== */

.global-loading {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  gap: 1rem;
}

.loading-futuristic {
  width: 60px;
  height: 60px;
  border: 4px solid rgba(75, 0, 130, 0.3);
  border-top: 4px solid #4B0082;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  font-family: 'Orbitron', monospace;
  color: #4B0082;
  font-size: 1.125rem;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

  /* Transición suave entre rutas */
  .fade-slide-enter-active,
  .fade-slide-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
  }
  .fade-slide-enter-from,
  .fade-slide-leave-to {
    opacity: 0;
    transform: translateY(8px);
  }

/* =====================================================
   RESPONSIVE DESIGN
   ===================================================== */

@media (max-width: 1200px) {
  .app-sidebar {
    width: 240px;
  }

  .app-main {
    margin-left: 240px;
  }

  .app-header {
    margin-left: 240px;
  }

  .sidebar-collapsed .app-main,
  .sidebar-collapsed .app-header {
    margin-left: 0;
  }
  .app-header.no-sidebar { margin-left: 0 !important; }

  .header-content {
    padding: 1rem 1.5rem;
  }
}

@media (max-width: 992px) {
  .app-sidebar {
    transform: translateX(-100%);
  }

  .sidebar-collapsed .app-sidebar {
    transform: translateX(0);
  }

  .app-main,
  .app-header {
    margin-left: 0;
  }

  /* Evitar que la downbar tape el contenido en móviles */
  .app-main {
    padding-bottom: 72px; /* downbar height + margen */
    width: 100vw; /* ocupar todo el ancho del viewport */
    margin: 0; /* sin márgenes laterales */
  }

  .header-content {
    padding: 1rem;
  }
}

@media (max-width: 768px) {
  .app-sidebar {
    width: 280px;
    z-index: 1100;
  }

  .header-content {
    padding: 0.75rem;
  }

  .app-logo .header-logo-img {
    height: 35px;
  }

  .app-logo-text {
    font-size: 1.25rem;
  }

  .page-content {
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .header-content {
    padding: 0.5rem;
  }

  .app-logo-text {
    font-size: 1rem;
    letter-spacing: 1px;
  }

  .page-content {
    padding: 0.5rem;
  }
}

/* =====================================================
   ACCESIBILIDAD
   ===================================================== */

.theme-toggle-btn:focus-visible,
.sidebar-toggle-btn:focus-visible {
  outline: 2px solid #4B0082;
  outline-offset: 2px;
}

/* Alto contraste */
@media (prefers-contrast: high) {
  .app-header {
    border-bottom-width: 3px;
  }

  .app-sidebar {
    border-right: 1px solid rgba(255, 255, 255, 0.2);
  }
}

/* Reducción de movimiento */
@media (prefers-reduced-motion: reduce) {
  .app-sidebar {
    transition: none;
  }

  .app-main,
  .app-header {
    transition: none;
  }

  .loading-futuristic {
    animation: none;
  }

  .loading-text {
    animation: none;
  }

  .matrix-bg {
    animation: none;
  }
}

/* =====================================================
   TEMAS
   ===================================================== */

:root {
  --bg-primary: #0a0a0a;
  --bg-secondary: #1a1a1a;
  --text-primary: #ffffff;
  --text-secondary: #cccccc;
  --accent-primary: #4B0082;
  --accent-secondary: #6B1FA8;
}

/* Tema oscuro */
[data-theme="dark"] {
  --bg-primary: #0a0a0a;
  --bg-secondary: #1a1a1a;
  --text-primary: #ffffff;
  --text-secondary: #cccccc;
  --accent-primary: #4B0082;
  --accent-secondary: #6B1FA8;
}

/* Tema claro */
[data-theme="light"] {
  --bg-primary: #f8f9fa;
  --bg-secondary: #ffffff;
  --text-primary: #212529;
  --text-secondary: #6c757d;
  --accent-primary: #4B0082;
  --accent-secondary: #6B1FA8;
}

/* Floating AI Assistant Button */
.ai-fab {
  position: fixed;
  right: 20px;
  bottom: 20px;
  width: 54px;
  height: 54px;
  border-radius: 50%;
  border: 1px solid rgba(138, 43, 226, 0.35);
  background: linear-gradient(135deg, rgba(75,0,130,0.9), rgba(138,43,226,0.9));
  color: #fff;
  box-shadow: 0 10px 25px rgba(0,0,0,0.35), 0 0 15px rgba(138,43,226,0.35) inset;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1200;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.ai-fab:hover { transform: translateY(-2px) scale(1.03); }
.ai-fab:active { transform: translateY(0) scale(0.98); }
.ai-fab i { font-size: 1.25rem; }

  /* Subir FAB en móviles para no chocar con Downbar */
  @media (max-width: 992px) {
    .ai-fab {
      right: 16px;
      bottom: calc(92px + env(safe-area-inset-bottom)); /* ~60px downbar + margen */
    }
  }
</style>
