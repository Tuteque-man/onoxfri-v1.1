<template>
  <!-- Sidebar para desktop (integrado en el nuevo layout) -->
  <div class="onoxfri-sidebar">
    <div class="sidebar-header">
      <div class="logo-container">
        <img src="@/assets/img/l1.png" alt="ONOXFRI Logo" class="logo-img" />
      </div>
      <div class="logo-subtitle">Inventario Inteligente</div>
    </div>

    <nav class="sidebar-nav">
      <div
        v-for="(item, index) in menuItems"
        :key="item.id"
        class="nav-item"
        :style="{ animationDelay: `${index * 0.1}s` }"
      >
        <router-link
          :to="item.route"
          class="nav-link"
          :class="{ active: isActiveRoute(item.route) }"
          @click="handleNavClick"
        >
          <i :class="item.icon"></i>
          <span class="link-text">{{ item.label }}</span>
        </router-link>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="user-info-small">
        <div class="user-avatar-small">
          <i class="fas fa-user"></i>
        </div>
        <div class="user-details-small">
          <div class="user-name">{{ displayName }}</div>
          <div class="user-role">{{ displayRole }}</div>
        </div>
      </div>

      <button
        class="btn-secondary-futuristic settings-btn"
        @click="goToSettings"
      >
        <i class="fas fa-cog"></i>
        Configuración
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import apiClient from '@/apiClient'

const router = useRouter()
const route = useRoute()

// Estado reactivo
const currentUser = ref(null)
const currentUserRole = ref('')

// Elementos del menú
const menuItems = ref([
  {
    id: 'dashboard',
    label: 'Dashboard',
    route: '/dashboard',
    icon: 'bi bi-grid-1x2-fill'
  },
  {
    id: 'productos',
    label: 'Productos',
    route: '/productos',
    icon: 'bi bi-box-seam'
  },
  {
    id: 'categorias',
    label: 'Categorías',
    route: '/categorias',
    icon: 'bi bi-tags'
  },
  {
    id: 'empresas',
    label: 'Empresas',
    route: '/empresas',
    icon: 'bi bi-building'
  },
  {
    id: 'inventario',
    label: 'Inventario',
    route: '/inventario',
    icon: 'bi bi-clipboard-check'
  },
  {
    id: 'reportes',
    label: 'Reportes',
    route: '/reportes',
    icon: 'bi bi-graph-up'
  },
  {
    id: 'usuarios',
    label: 'Usuarios',
    route: '/usuarios',
    icon: 'bi bi-people'
  }
])

// Computed
const isActiveRoute = (itemRoute) => {
  return route.path === itemRoute || route.path.startsWith(itemRoute + '/')
}

// Métodos
const handleNavClick = () => {
  // Emitir evento para cerrar menú móvil si existe
  window.dispatchEvent(new CustomEvent('sidebar-nav-click'))
}

const goToSettings = () => {
  router.push('/configuracion')
}

const toTitle = (s) => s ? s.replace(/[_\-.]+/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : s
const emailPrefix = (e) => typeof e === 'string' && e.includes('@') ? e.split('@')[0] : ''
const resolveName = (u) => {
  const byField = u?.name || u?.username || u?.Nombre || u?.usuario
  if (byField) return byField
  const fromEmail = emailPrefix(u?.email)
  return fromEmail ? toTitle(fromEmail) : null
}
const resolveRole = (u) => {
  // soportar estructuras comunes: string, objeto, arrays
  if (!u) return ''
  if (typeof u.role === 'string') return u.role
  if (typeof u.role_name === 'string') return u.role_name
  if (typeof u.rol === 'string') return u.rol
  if (u.role?.name) return u.role.name
  if (u.rol?.name) return u.rol.name
  if (Array.isArray(u.roles) && u.roles.length) return u.roles[0]?.name || u.roles[0]
  if (u.role_id != null) {
    const id = Number(u.role_id)
    if (id === 1) return 'Superusuario'
    if (id === 2) return 'Gerente'
    if (id === 3) return 'Asistente'
  }
  return ''
}

const fetchUserFromApi = async () => {
  const ep = import.meta.env.VITE_USER_ME_ENDPOINT
  // Solo intenta si está definido explícitamente en el entorno
  if (ep && typeof ep === 'string' && ep.trim() !== '') {
    try {
      const { data } = await apiClient.get(ep)
      if (data) return data.user || data.usuario || data
    } catch (e) {
      // ignorar y usar fallback localStorage
    }
  }
  return null
}

const loadUserInfo = async () => {
  // 1) intentar API
  const apiUser = await fetchUserFromApi()
  if (apiUser) {
    currentUser.value = apiUser
    currentUserRole.value = resolveRole(apiUser) || '—'
    return
  }
  // 2) fallback localStorage
  try {
    const raw = localStorage.getItem('onoxfri_user')
    const lsUser = raw ? JSON.parse(raw) : null
    currentUser.value = lsUser
    currentUserRole.value = resolveRole(lsUser) || localStorage.getItem('onoxfri_role') || '—'
  } catch {
    currentUser.value = null
    currentUserRole.value = '—'
  }
}

// Lifecycle
onMounted(() => {
  loadUserInfo()
})

// Computed display values
const displayName = computed(() => {
  const n = resolveName(currentUser.value)
  return n || '—'
})
const displayRole = computed(() => {
  // calcular dinámicamente desde currentUser; fallback al estado previo o localStorage
  const dyn = resolveRole(currentUser.value)
  if (dyn) return dyn
  if (currentUserRole.value) return currentUserRole.value
  try {
    const stored = localStorage.getItem('onoxfri_role')
    if (stored) return stored
  } catch {}
  return '—'
})
const displayCompany = computed(() => {
  const c = currentUser.value?.empresa_nombre || currentUser.value?.company || currentUser.value?.empresa || ''
  return c || '—'
})
</script>

<style lang="scss" scoped>
/* =====================================================
   SIDEBAR INTEGRADO EN EL NUEVO LAYOUT
   ===================================================== */

.onoxfri-sidebar {
  width: 100%;
  height: 100%;
  background: linear-gradient(180deg, #4B0082 0%, #2D004D 50%, #1a0026 100%);
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
  }

  &::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 0%, rgba(138, 43, 226, 0.05) 50%, transparent 100%);
    pointer-events: none;
  }
}

.sidebar-header {
  text-align: center;
  margin-bottom: 2rem;
  position: relative;
  z-index: 2;

  .logo-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 0.5rem;
    position: relative;
  }

  .logo-img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    margin-bottom: 0.25rem;
    filter: drop-shadow(0 0 10px rgba(138, 43, 226, 0.3));
    animation: logoGlow 3s ease-in-out infinite alternate;
  }

  .bot-container {
    margin-top: 0.5rem;

    .bot-img {
      width: 60px;
      height: 60px;
      object-fit: contain;
      filter: drop-shadow(0 0 8px rgba(75, 0, 130, 0.3));
      animation: botPulse 2s ease-in-out infinite;
    }
  }

  .logo-subtitle {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
    letter-spacing: 1px;
    text-transform: uppercase;
  }
}

.sidebar-nav {
  flex: 1;
  position: relative;
  z-index: 2;

  .nav-item {
    margin-bottom: 0.5rem;
    opacity: 0;
    animation: slideInLeft 0.5s ease-out forwards;

    .user-under-users {
      margin: 6px 0 10px 44px; /* alineado al texto de enlaces (icono 20px + gap) */
      display: grid;
      gap: 4px;
      .row {
        display: flex;
        align-items: center;
        gap: 8px;
        color: rgba(255,255,255,0.8);
        font-size: 0.78rem;
        .txt { 
          overflow: hidden; 
          text-overflow: ellipsis; 
          white-space: nowrap; 
          max-width: 180px;
        }
        i { font-size: 0.9rem; width: 16px; text-align: center; color: rgba(255,255,255,0.85); }
      }
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 1rem 1.25rem;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      border-radius: 12px;
      transition: all 0.3s ease;
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 500;
      position: relative;
      overflow: hidden;

      i {
        margin-right: 1rem;
        font-size: 1.25rem;
        width: 20px;
        text-align: center;
        transition: transform 0.3s ease;
      }

      .link-text {
        flex: 1;
        font-size: 0.875rem;
      }

      &::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transition: left 0.3s ease;
      }

      &:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        transform: translateX(5px);

        i {
          transform: scale(1.2);
        }

        &::before {
          left: 0;
        }
      }

      &.active {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        box-shadow: 0 0 20px rgba(138, 43, 226, 0.3);

        &::after {
          content: '';
          position: absolute;
          left: 0;
          top: 0;
          bottom: 0;
          width: 4px;
          background: linear-gradient(180deg, #8A2BE2, #FF00FF);
        }
      }
    }
  }
}

.sidebar-footer {
  margin-top: auto;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
  z-index: 2;

  .user-info-small {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;

    .user-avatar-small {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, #6B1FA8, #8A2BE2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 0.875rem;
      margin-right: 0.75rem;
      flex-shrink: 0;
    }

    .user-details-small {
      flex: 1;
      min-width: 0;

      .user-name {
        font-size: 0.75rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .user-role {
        font-size: 0.625rem;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
    }
  }

  .settings-btn {
    width: 100%;
    font-size: 0.75rem;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);

    &:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: rgba(255, 255, 255, 0.3);
    }
  }
}

/* =====================================================
   ANIMACIONES
   ===================================================== */

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes logoGlow {
  0% {
    filter: drop-shadow(0 0 10px rgba(138, 43, 226, 0.3));
  }
  100% {
    filter: drop-shadow(0 0 20px rgba(138, 43, 226, 0.6)) drop-shadow(0 0 30px rgba(255, 0, 255, 0.3));
  }
}

@keyframes botPulse {
  0%, 100% {
    filter: drop-shadow(0 0 8px rgba(75, 0, 130, 0.3));
    transform: scale(1);
  }
  50% {
    filter: drop-shadow(0 0 15px rgba(75, 0, 130, 0.5));
    transform: scale(1.05);
  }
}

/* =====================================================
   RESPONSIVE
   ===================================================== */

@media (max-width: 768px) {
  .onoxfri-sidebar {
    width: 280px;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    position: fixed;
    z-index: 1100;
  }

  .onoxfri-sidebar.open {
    transform: translateX(0);
  }
}

@media (max-width: 480px) {
  .onoxfri-sidebar {
    width: 100vw;
    padding: 1rem;
  }

  .sidebar-header {
    margin-bottom: 1.5rem;

    .logo-img {
      width: 60px;
      height: 60px;
    }

    .bot-img {
      width: 45px;
      height: 45px;
    }

    .logo-subtitle {
      font-size: 0.7rem;
    }
  }

  .nav-item .nav-link {
    padding: 0.875rem 1rem;

    .link-text {
      font-size: 0.8rem;
    }

    i {
      font-size: 1.125rem;
      margin-right: 0.875rem;
    }
  }

  .sidebar-footer {
    .user-info-small {
      padding: 0.625rem;

      .user-avatar-small {
        width: 28px;
        height: 28px;
        font-size: 0.75rem;
      }

      .user-details-small .user-name {
        font-size: 0.7rem;
      }

      .user-details-small .user-role {
        font-size: 0.6rem;
      }
    }

    .settings-btn {
      font-size: 0.7rem;
      padding: 0.625rem;
    }
  }
}

@media (max-width: 280px) {
  .onoxfri-sidebar {
    padding: 0.75rem;
  }

  .sidebar-header {
    margin-bottom: 1rem;

    .logo-img {
      width: 50px;
      height: 50px;
    }

    .bot-img {
      width: 40px;
      height: 40px;
    }
  }

  .nav-item .nav-link {
    padding: 0.75rem;

    .link-text {
      font-size: 0.75rem;
    }

    i {
      font-size: 1rem;
      margin-right: 0.75rem;
    }
  }
}
</style>