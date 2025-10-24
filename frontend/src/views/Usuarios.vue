<template>
  <component :is="currentComponent" v-if="isMobile" />
  <div v-else class="layout">
    <Sidebar />
    <main class="page">
      <div class="page-header">
        <h1><i class="bi bi-people"></i> Gestión de Usuarios</h1>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button 
          class="tab" 
          :class="{ active: activeTab === 'usuarios' }"
          @click="activeTab = 'usuarios'"
        >
          <i class="bi bi-people-fill"></i> Usuarios
        </button>
        <button 
          class="tab" 
          :class="{ active: activeTab === 'actividad' }"
          @click="activeTab = 'actividad'"
        >
          <i class="bi bi-activity"></i> Actividad en Vivo
        </button>
        <button 
          class="tab" 
          :class="{ active: activeTab === 'permisos' }"
          @click="activeTab = 'permisos'"
        >
          <i class="bi bi-shield-lock"></i> Permisos por Rol
        </button>
      </div>

      <!-- Tab: Usuarios -->
      <section v-if="activeTab === 'usuarios'" class="content-section">
        <div class="users-grid">
          <div v-for="user in usuarios" :key="user.id" class="user-card">
            <div class="user-avatar">
              <i class="bi bi-person-circle"></i>
            </div>
            <div class="user-info">
              <h3>{{ user.nombre }} {{ user.apellido }}</h3>
              <p class="user-email"><i class="bi bi-envelope"></i> {{ user.email }}</p>
              <p class="user-username"><i class="bi bi-at"></i> {{ user.nombre_usuario }}</p>
              <div class="user-meta">
                <span class="badge" :class="getRoleBadgeClass(user.role)">{{ user.role }}</span>
                <span class="status" :class="{ active: user.is_active }">
                  <i class="bi" :class="user.is_active ? 'bi-circle-fill' : 'bi-circle'"></i>
                  {{ user.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Tab: Actividad en Vivo -->
      <section v-if="activeTab === 'actividad'" class="content-section">
        <div class="activity-header">
          <h2><i class="bi bi-broadcast"></i> Usuarios Conectados</h2>
          <span class="online-count">{{ usuariosOnline.length }} en línea</span>
        </div>
        <div class="activity-grid">
          <div v-for="activity in usuariosOnline" :key="activity.userId" class="activity-card">
            <div class="activity-user">
              <div class="user-avatar-sm">
                <i class="bi bi-person-circle"></i>
              </div>
              <div>
                <h4>{{ activity.nombre }}</h4>
                <span class="role-badge" :class="getRoleBadgeClass(activity.role)">{{ activity.role }}</span>
              </div>
            </div>
            <div class="activity-details">
              <div class="activity-item">
                <i class="bi bi-window"></i>
                <span>Pestaña: <strong>{{ activity.currentTab }}</strong></span>
              </div>
              <div class="activity-item">
                <i class="bi bi-clock"></i>
                <span>Última actividad: {{ activity.lastActivity }}</span>
              </div>
              <div class="activity-item">
                <i class="bi bi-geo-alt"></i>
                <span>IP: {{ activity.ip }}</span>
              </div>
            </div>
            <div class="pulse-indicator"></div>
          </div>
        </div>
      </section>

      <!-- Tab: Permisos por Rol -->
      <section v-if="activeTab === 'permisos'" class="content-section">
        <div class="permissions-grid">
          <div v-for="role in roles" :key="role.id" class="permission-card" :class="{ 'span-2': role.id === 3 }">
            <div class="permission-header">
              <h3><i class="bi bi-shield-check"></i> {{ role.name }}</h3>
              <span class="users-count">{{ getUserCountByRole(role.name) }} usuarios</span>
            </div>
            <div class="permissions-list">
              <div v-for="perm in permisos" :key="perm.id" class="permission-item">
                <label class="checkbox-label">
                  <input 
                    type="checkbox" 
                    :checked="hasPermission(role.id, perm.id)"
                    @change="togglePermission(role.id, perm.id)"
                  />
                  <span class="checkbox-custom"></span>
                  <div class="permission-info">
                    <i class="bi" :class="perm.icon"></i>
                    <div>
                      <strong>{{ perm.name }}</strong>
                      <p>{{ perm.description }}</p>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, computed, defineAsyncComponent } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const MobileUsuarios = defineAsyncComponent(() => import('./mobile/Usuarios.vue'))

const isMobile = ref(window.innerWidth <= 992)
const currentComponent = computed(() => isMobile.value ? MobileUsuarios : null)
const handleResize = () => { isMobile.value = window.innerWidth <= 992 }

const activeTab = ref('usuarios')
const showAddUser = ref(false)

const usuarios = ref([
  {
    id: 1,
    nombre: 'Juan',
    apellido: 'Pérez',
    email: 'juan.perez@onoxfri.com',
    nombre_usuario: 'jperez',
    role: 'Superusuario',
    role_id: 1,
    is_active: true
  },
  {
    id: 2,
    nombre: 'María',
    apellido: 'García',
    email: 'maria.garcia@onoxfri.com',
    nombre_usuario: 'mgarcia',
    role: 'Gerente',
    role_id: 2,
    is_active: true
  },
  {
    id: 3,
    nombre: 'Carlos',
    apellido: 'López',
    email: 'carlos.lopez@onoxfri.com',
    nombre_usuario: 'clopez',
    role: 'Asistente',
    role_id: 3,
    is_active: true
  }
])

const usuariosOnline = ref([
  {
    userId: 1,
    nombre: 'Juan Pérez',
    role: 'Superusuario',
    currentTab: 'Dashboard',
    lastActivity: 'Hace 2 min',
    ip: '192.168.1.100'
  },
  {
    userId: 2,
    nombre: 'María García',
    role: 'Gerente',
    currentTab: 'Inventario',
    lastActivity: 'Hace 5 min',
    ip: '192.168.1.101'
  },
  {
    userId: 3,
    nombre: 'Carlos López',
    role: 'Asistente',
    currentTab: 'Pedidos',
    lastActivity: 'Hace 1 min',
    ip: '192.168.1.102'
  }
])

const roles = ref([
  { id: 1, name: 'Superusuario' },
  { id: 2, name: 'Gerente' },
  { id: 3, name: 'Asistente' }
])

const permisos = ref([
  { id: 1, name: 'Ver Dashboard', description: 'Acceso a la página principal', icon: 'bi-speedometer2' },
  { id: 2, name: 'Gestionar Inventario', description: 'Crear, editar y eliminar productos', icon: 'bi-box-seam' },
  { id: 3, name: 'Ver Pedidos', description: 'Visualizar lista de pedidos', icon: 'bi-cart' },
  { id: 4, name: 'Gestionar Pedidos', description: 'Crear y modificar pedidos', icon: 'bi-cart-plus' },
  { id: 5, name: 'Ver Reportes', description: 'Acceso a reportes y análisis', icon: 'bi-graph-up' },
  { id: 6, name: 'Exportar Datos', description: 'Descargar reportes en PDF/Excel', icon: 'bi-download' },
  { id: 7, name: 'Gestionar Usuarios', description: 'Crear, editar y eliminar usuarios', icon: 'bi-people' },
  { id: 8, name: 'Configurar Sistema', description: 'Acceso a configuración avanzada', icon: 'bi-gear' }
])

const rolePermissions = ref({
  1: [1, 2, 3, 4, 5, 6, 7, 8], // Superusuario: todos los permisos
  2: [1, 2, 3, 4, 5, 6], // Gerente: sin usuarios ni configuración
  3: [1, 3, 5] // Asistente: solo ver
})

const getRoleBadgeClass = (role) => {
  const classes = {
    'Superusuario': 'badge-super',
    'Gerente': 'badge-gerente',
    'Asistente': 'badge-asistente'
  }
  return classes[role] || ''
}

const getUserCountByRole = (roleName) => {
  return usuarios.value.filter(u => u.role === roleName).length
}

const hasPermission = (roleId, permId) => {
  return rolePermissions.value[roleId]?.includes(permId) || false
}

const togglePermission = (roleId, permId) => {
  if (!rolePermissions.value[roleId]) {
    rolePermissions.value[roleId] = []
  }
  
  const index = rolePermissions.value[roleId].indexOf(permId)
  if (index > -1) {
    rolePermissions.value[roleId].splice(index, 1)
  } else {
    rolePermissions.value[roleId].push(permId)
  }
  
  console.log('Permisos actualizados:', rolePermissions.value)
}

const editUser = (user) => {
  console.log('Editar usuario:', user)
}

const toggleUserStatus = (user) => {
  user.is_active = !user.is_active
  console.log('Estado actualizado:', user)
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
.layout { 
  display: grid; 
  grid-template-columns: 260px 1fr; 
  height: 100vh; 
  overflow: hidden; 
}

.page { 
  position: relative; 
  padding: 26px;
  padding-bottom: 40px;
  height: 100vh;
  overflow-y: auto;
  overflow-x: hidden;
  box-sizing: border-box;
  
  scrollbar-width: thin;
  scrollbar-color: transparent transparent;
  
  &::-webkit-scrollbar {
    width: 8px;
  }
  
  &::-webkit-scrollbar-track {
    background: transparent;
  }
  
  &::-webkit-scrollbar-thumb {
    background: transparent;
    border-radius: 10px;
    transition: background 0.3s ease;
  }
  
  &:hover {
    scrollbar-color: rgba(138, 43, 226, 0.5) rgba(255, 255, 255, 0.05);
    
    &::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.05);
    }
    
    &::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, rgba(138, 43, 226, 0.6), rgba(189, 147, 255, 0.4));
      
      &:hover {
        background: linear-gradient(180deg, rgba(138, 43, 226, 0.8), rgba(189, 147, 255, 0.6));
      }
    }
  }
}

.page::before {
  content: "";
  position: fixed;
  top: 0;
  left: 260px;
  right: 0;
  bottom: 0;
  pointer-events: none;
  background:
    radial-gradient(1200px 600px at 10% 10%, rgba(138,43,226,0.10), transparent 60%),
    radial-gradient(900px 500px at 90% 20%, rgba(189,147,255,0.08), transparent 60%),
    linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.00));
  animation: background-pulse 12s infinite ease-in-out;
  z-index: 0;
}

@keyframes background-pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.02); opacity: 0.85; }
}

.page-header { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.8rem; color: #ffffff; display: flex; align-items: center; gap: 12px; text-shadow: 0 0 8px rgba(255,255,255,.35); }

.users-grid, .permissions-section { position: relative; z-index: 1; }
.btn-add { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 24px rgba(138,43,226,0.25); transition: all .3s ease; }
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(138,43,226,0.35); }

.tabs { display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 2px solid rgba(138,43,226,0.3); }
.tab { background: transparent; border: none; color: #a89bb9; padding: 12px 20px; font-size: 1rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; border-bottom: 3px solid transparent; transition: all .3s ease; }
.tab:hover { color: #bd93ff; }
.tab.active { color: #ffffff; border-bottom-color: #8B5CF6; }

.content-section { position: relative; z-index: 1; }

/* Usuarios Grid */
.users-grid { display: flex; flex-direction: column; gap: 18px; max-width: 800px; }
.user-card { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 20px; display: flex; gap: 16px; align-items: flex-start; box-shadow: 0 10px 30px rgba(138,43,226,0.15); transition: all .3s ease; }
.user-card:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(138,43,226,0.25); }
.user-avatar { width: 60px; height: 60px; background: linear-gradient(135deg, rgba(138,43,226,0.3), rgba(189,147,255,0.2)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #bd93ff; flex-shrink: 0; }
.user-info { flex: 1; }
.user-info h3 { margin: 0 0 8px 0; color: #ffffff; font-size: 1.1rem; }
.user-email, .user-username { margin: 4px 0; color: #a89bb9; font-size: 0.9rem; display: flex; align-items: center; gap: 6px; }
.user-meta { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
.badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
.badge-super { background: linear-gradient(135deg, #FF6B6B, #FF8E53); color: #fff; }
.badge-gerente { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; }
.badge-asistente { background: linear-gradient(135deg, #00B4DB, #0083B0); color: #fff; }
.status { display: flex; align-items: center; gap: 4px; font-size: 0.85rem; color: #a89bb9; }
.status.active { color: #4ade80; }
.user-actions { display: flex; flex-direction: column; gap: 8px; }
.btn-icon { background: rgba(138,43,226,0.2); border: 1px solid rgba(189,147,255,0.3); color: #ffffff; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .2s ease; }
.btn-icon:hover { background: rgba(138,43,226,0.4); transform: scale(1.1); }

/* Actividad */
.activity-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.activity-header h2 { margin: 0; color: #ffffff; font-size: 1.4rem; display: flex; align-items: center; gap: 10px; }
.online-count { background: linear-gradient(135deg, #4ade80, #22c55e); color: #fff; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; }
.activity-grid { display: grid; gap: 16px; }
.activity-card { position: relative; background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 20px; display: flex; gap: 20px; box-shadow: 0 10px 30px rgba(138,43,226,0.15); }
.activity-user { display: flex; gap: 12px; align-items: center; min-width: 200px; }
.user-avatar-sm { width: 50px; height: 50px; background: linear-gradient(135deg, rgba(138,43,226,0.3), rgba(189,147,255,0.2)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #bd93ff; }
.activity-user h4 { margin: 0; color: #ffffff; font-size: 1rem; }
.role-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; margin-top: 4px; }
.activity-details { flex: 1; display: flex; flex-direction: column; gap: 8px; }
.activity-item { display: flex; align-items: center; gap: 8px; color: #a89bb9; font-size: 0.9rem; }
.activity-item i { color: #8B5CF6; }
.activity-item strong { color: #ffffff; }
.pulse-indicator { position: absolute; top: 20px; right: 20px; width: 12px; height: 12px; background: #4ade80; border-radius: 50%; animation: pulse 2s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.2); } }

/* Permisos */
.permissions-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.permission-card { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 20px; box-shadow: 0 10px 30px rgba(138,43,226,0.15); }
.permission-card.span-2 { grid-column: span 2; }
.permission-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid rgba(138,43,226,0.3); }
.permission-header h3 { margin: 0; color: #ffffff; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
.users-count { background: rgba(138,43,226,0.3); color: #bd93ff; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.permissions-list { display: flex; flex-direction: column; gap: 12px; }
.permission-item { }
.checkbox-label { display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border-radius: 10px; border: 1px solid rgba(189,147,255,0.15); background: rgba(255,255,255,0.02); transition: all .2s ease; }
.checkbox-label:hover { background: rgba(189,147,255,0.06); border-color: rgba(189,147,255,0.3); }
.checkbox-label input[type="checkbox"] { display: none; }
.checkbox-custom { width: 20px; height: 20px; border: 2px solid rgba(189,147,255,0.5); border-radius: 4px; position: relative; flex-shrink: 0; transition: all .2s ease; }
.checkbox-label input[type="checkbox"]:checked + .checkbox-custom { background: linear-gradient(135deg, #4B0082, #8B5CF6); border-color: #8B5CF6; }
.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after { content: '✓'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; font-weight: bold; font-size: 14px; }
.permission-info { display: flex; gap: 12px; align-items: flex-start; flex: 1; }
.permission-info i { color: #8B5CF6; font-size: 1.2rem; margin-top: 2px; }
.permission-info strong { display: block; color: #ffffff; font-size: 0.95rem; margin-bottom: 4px; }
.permission-info p { margin: 0; color: #a89bb9; font-size: 0.85rem; line-height: 1.4; }

@media (max-width: 1100px) {
  .layout { grid-template-columns: 80px 1fr; }
  .users-grid { grid-template-columns: 1fr; }
  .permissions-grid { grid-template-columns: 1fr; }
}
</style>