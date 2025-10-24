<template>
  <div class="mobile-page">
    <!-- Header -->
    <div class="mobile-header">
      <button class="back-btn" @click="$router.back()">
        <i class="bi bi-arrow-left"></i>
      </button>
      <h1><i class="bi bi-people"></i> Usuarios</h1>
      <div style="width: 40px;"></div>
    </div>

    <!-- Tabs -->
    <div class="tabs-mobile">
      <button 
        class="tab-mobile" 
        :class="{ active: activeTab === 'usuarios' }"
        @click="activeTab = 'usuarios'"
      >
        <i class="bi bi-people-fill"></i>
        <span>Usuarios</span>
      </button>
      <button 
        class="tab-mobile" 
        :class="{ active: activeTab === 'actividad' }"
        @click="activeTab = 'actividad'"
      >
        <i class="bi bi-activity"></i>
        <span>Actividad</span>
      </button>
      <button 
        class="tab-mobile" 
        :class="{ active: activeTab === 'permisos' }"
        @click="activeTab = 'permisos'"
      >
        <i class="bi bi-shield-lock"></i>
        <span>Permisos</span>
      </button>
    </div>

    <!-- Tab: Usuarios -->
    <section v-if="activeTab === 'usuarios'" class="content-mobile">
      <div class="users-list">
        <div v-for="user in usuarios" :key="user.id" class="user-card-mobile">
          <div class="user-avatar-mobile">
            <i class="bi bi-person-circle"></i>
          </div>
          <div class="user-info-mobile">
            <h3>{{ user.nombre }} {{ user.apellido }}</h3>
            <p class="user-email-mobile"><i class="bi bi-envelope"></i> {{ user.email }}</p>
            <p class="user-username-mobile"><i class="bi bi-at"></i> {{ user.nombre_usuario }}</p>
            <div class="user-meta-mobile">
              <span class="badge-mobile" :class="getRoleBadgeClass(user.role)">{{ user.role }}</span>
              <span class="status-mobile" :class="{ active: user.is_active }">
                <i class="bi" :class="user.is_active ? 'bi-circle-fill' : 'bi-circle'"></i>
                {{ user.is_active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Tab: Actividad en Vivo -->
    <section v-if="activeTab === 'actividad'" class="content-mobile">
      <div class="activity-header-mobile">
        <h2><i class="bi bi-broadcast"></i> Conectados</h2>
        <span class="online-badge">{{ usuariosOnline.length }}</span>
      </div>
      <div class="activity-list">
        <div v-for="activity in usuariosOnline" :key="activity.userId" class="activity-card-mobile">
          <div class="activity-top">
            <div class="activity-user-mobile">
              <div class="user-avatar-sm-mobile">
                <i class="bi bi-person-circle"></i>
              </div>
              <div>
                <h4>{{ activity.nombre }}</h4>
                <span class="role-badge-mobile" :class="getRoleBadgeClass(activity.role)">{{ activity.role }}</span>
              </div>
            </div>
            <div class="pulse-mobile"></div>
          </div>
          <div class="activity-details-mobile">
            <div class="activity-item-mobile">
              <i class="bi bi-window"></i>
              <span>{{ activity.currentTab }}</span>
            </div>
            <div class="activity-item-mobile">
              <i class="bi bi-clock"></i>
              <span>{{ activity.lastActivity }}</span>
            </div>
            <div class="activity-item-mobile">
              <i class="bi bi-geo-alt"></i>
              <span>{{ activity.ip }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Tab: Permisos por Rol -->
    <section v-if="activeTab === 'permisos'" class="content-mobile">
      <div class="permissions-mobile">
        <div v-for="role in roles" :key="role.id" class="permission-card-mobile" :class="{ 'span-2': role.id === 3 }">
          <div class="permission-header-mobile">
            <h3><i class="bi bi-shield-check"></i> {{ role.name }}</h3>
            <span class="users-count-mobile">{{ getUserCountByRole(role.name) }}</span>
          </div>
          <div class="permissions-list-mobile">
            <div v-for="perm in permisos" :key="perm.id" class="permission-item-mobile">
              <label class="checkbox-label-mobile">
                <input 
                  type="checkbox" 
                  :checked="hasPermission(role.id, perm.id)"
                  @change="togglePermission(role.id, perm.id)"
                />
                <span class="checkbox-custom-mobile"></span>
                <div class="permission-info-mobile">
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
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeTab = ref('usuarios')

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
</script>

<style scoped>
.mobile-page { min-height: 100vh; padding-bottom: 80px; }

.mobile-header { position: sticky; top: 0; z-index: 100; background: linear-gradient(135deg, #1a0033 0%, #0a001a 100%); padding: 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid rgba(138,43,226,0.3); box-shadow: 0 4px 20px rgba(138,43,226,0.2); }
.mobile-header h1 { margin: 0; font-size: 1.3rem; color: #ffffff; font-family: 'Orbitron', monospace; display: flex; align-items: center; gap: 8px; }
.back-btn { background: rgba(138,43,226,0.2); border: 1px solid rgba(189,147,255,0.3); color: #ffffff; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; }
.back-btn:active { background: rgba(138,43,226,0.4); }

.tabs-mobile { display: flex; gap: 8px; padding: 12px 16px; background: rgba(10,0,20,0.5); border-bottom: 2px solid rgba(138,43,226,0.2); overflow-x: auto; }
.tab-mobile { background: rgba(138,43,226,0.1); border: 1px solid rgba(189,147,255,0.2); color: #a89bb9; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap; transition: all .3s ease; }
.tab-mobile.active { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #ffffff; border-color: #8B5CF6; }

.content-mobile { padding: 16px; }

/* Usuarios */
.users-list { display: flex; flex-direction: column; gap: 12px; }
.user-card-mobile { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 16px; display: flex; gap: 12px; box-shadow: 0 8px 24px rgba(138,43,226,0.15); }
.user-avatar-mobile { width: 50px; height: 50px; background: linear-gradient(135deg, rgba(138,43,226,0.3), rgba(189,147,255,0.2)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #bd93ff; flex-shrink: 0; }
.user-info-mobile { flex: 1; }
.user-info-mobile h3 { margin: 0 0 6px 0; color: #ffffff; font-size: 1rem; }
.user-email-mobile, .user-username-mobile { margin: 3px 0; color: #a89bb9; font-size: 0.85rem; display: flex; align-items: center; gap: 4px; }
.user-meta-mobile { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
.badge-mobile { padding: 3px 10px; border-radius: 16px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
.badge-super { background: linear-gradient(135deg, #FF6B6B, #FF8E53); color: #fff; }
.badge-gerente { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; }
.badge-asistente { background: linear-gradient(135deg, #00B4DB, #0083B0); color: #fff; }
.status-mobile { display: flex; align-items: center; gap: 4px; font-size: 0.75rem; color: #a89bb9; }
.status-mobile.active { color: #4ade80; }

/* Actividad */
.activity-header-mobile { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.activity-header-mobile h2 { margin: 0; color: #ffffff; font-size: 1.2rem; display: flex; align-items: center; gap: 8px; }
.online-badge { background: linear-gradient(135deg, #4ade80, #22c55e); color: #fff; padding: 4px 12px; border-radius: 16px; font-weight: 700; font-size: 0.85rem; }
.activity-list { display: flex; flex-direction: column; gap: 12px; }
.activity-card-mobile { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 16px; box-shadow: 0 8px 24px rgba(138,43,226,0.15); }
.activity-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.activity-user-mobile { display: flex; gap: 10px; align-items: center; }
.user-avatar-sm-mobile { width: 40px; height: 40px; background: linear-gradient(135deg, rgba(138,43,226,0.3), rgba(189,147,255,0.2)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #bd93ff; }
.activity-user-mobile h4 { margin: 0; color: #ffffff; font-size: 0.95rem; }
.role-badge-mobile { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.65rem; font-weight: 700; margin-top: 4px; }
.pulse-mobile { width: 10px; height: 10px; background: #4ade80; border-radius: 50%; animation: pulse-anim 2s infinite; }
@keyframes pulse-anim { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.2); } }
.activity-details-mobile { display: flex; flex-direction: column; gap: 8px; }
.activity-item-mobile { display: flex; align-items: center; gap: 8px; color: #a89bb9; font-size: 0.85rem; }
.activity-item-mobile i { color: #8B5CF6; }

/* Permisos */
.permissions-mobile { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.permission-card-mobile { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 16px; box-shadow: 0 8px 24px rgba(138,43,226,0.15); }
.permission-card-mobile.span-2 { grid-column: span 2; }
.permission-header-mobile { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid rgba(138,43,226,0.3); }
.permission-header-mobile h3 { margin: 0; color: #ffffff; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; }
.users-count-mobile { background: rgba(138,43,226,0.3); color: #bd93ff; padding: 4px 10px; border-radius: 16px; font-size: 0.75rem; font-weight: 600; }
.permissions-list-mobile { display: flex; flex-direction: column; gap: 10px; }
.permission-item-mobile { }
.checkbox-label-mobile { display: flex; align-items: flex-start; gap: 10px; cursor: pointer; padding: 10px; border-radius: 10px; border: 1px solid rgba(189,147,255,0.15); background: rgba(255,255,255,0.02); transition: all .2s ease; }
.checkbox-label-mobile:active { background: rgba(189,147,255,0.06); border-color: rgba(189,147,255,0.3); }
.checkbox-label-mobile input[type="checkbox"] { display: none; }
.checkbox-custom-mobile { width: 18px; height: 18px; border: 2px solid rgba(189,147,255,0.5); border-radius: 4px; position: relative; flex-shrink: 0; transition: all .2s ease; }
.checkbox-label-mobile input[type="checkbox"]:checked + .checkbox-custom-mobile { background: linear-gradient(135deg, #4B0082, #8B5CF6); border-color: #8B5CF6; }
.checkbox-label-mobile input[type="checkbox"]:checked + .checkbox-custom-mobile::after { content: '✓'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; font-weight: bold; font-size: 12px; }
.permission-info-mobile { display: flex; gap: 10px; align-items: flex-start; flex: 1; }
.permission-info-mobile i { color: #8B5CF6; font-size: 1.1rem; margin-top: 2px; }
.permission-info-mobile strong { display: block; color: #ffffff; font-size: 0.9rem; margin-bottom: 3px; }
.permission-info-mobile p { margin: 0; color: #a89bb9; font-size: 0.8rem; line-height: 1.3; }
</style>