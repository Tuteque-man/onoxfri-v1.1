<template>
  <component :is="currentComponent" v-if="isMobile" />
  <div v-else class="layout">
    <Sidebar />
    <main class="page">
      <div class="page-header">
        <h1><i class="bi bi-gear"></i> Configuración del Sistema</h1>
      </div>

      <!-- Configuración de Empresa -->
      <section class="config-section">
        <div class="section-header">
          <h2><i class="bi bi-building"></i> Información de la Empresa</h2>
        </div>
        <div class="config-grid">
          <div class="config-item">
            <label>Nombre de la Empresa</label>
            <input type="text" v-model="config.empresa.nombre" placeholder="Mi Empresa S.A." />
          </div>
          <div class="config-item">
            <label>RUC / NIT</label>
            <input type="text" v-model="config.empresa.ruc" placeholder="20123456789" />
          </div>
          <div class="config-item">
            <label>Teléfono</label>
            <input type="text" v-model="config.empresa.telefono" placeholder="+51 999 999 999" />
          </div>
          <div class="config-item">
            <label>Email</label>
            <input type="email" v-model="config.empresa.email" placeholder="contacto@empresa.com" />
          </div>
          <div class="config-item span-2">
            <label>Dirección</label>
            <input type="text" v-model="config.empresa.direccion" placeholder="Av. Principal 123, Lima" />
          </div>
        </div>
      </section>

      <!-- Apariencia -->
      <section class="config-section">
        <div class="section-header">
          <h2><i class="bi bi-palette"></i> Apariencia</h2>
        </div>
        <div class="config-grid">
          <div class="config-item">
            <label>Tema</label>
            <select v-model="config.apariencia.tema">
              <option value="dark">Oscuro</option>
              <option value="light">Claro</option>
              <option value="auto">Automático</option>
            </select>
          </div>
          <div class="config-item">
            <label>Color Principal</label>
            <input type="color" v-model="config.apariencia.colorPrincipal" class="color-input" />
          </div>
          <div class="config-item">
            <label>Tamaño de Fuente</label>
            <select v-model="config.apariencia.fontSize">
              <option value="small">Pequeño</option>
              <option value="medium">Mediano</option>
              <option value="large">Grande</option>
            </select>
          </div>
        </div>
      </section>

      <!-- Notificaciones -->
      <section class="config-section">
        <div class="section-header">
          <h2><i class="bi bi-bell"></i> Notificaciones</h2>
        </div>
        <div class="config-toggles">
          <div class="toggle-item">
            <div class="toggle-info">
              <strong>Stock Bajo</strong>
              <p>Recibir alertas cuando el stock esté por debajo del mínimo</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" v-model="config.notificaciones.stockBajo" />
              <span class="slider"></span>
            </label>
          </div>
          <div class="toggle-item">
            <div class="toggle-info">
              <strong>Nuevos Pedidos</strong>
              <p>Notificar cuando se registre un nuevo pedido</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" v-model="config.notificaciones.nuevosPedidos" />
              <span class="slider"></span>
            </label>
          </div>
          <div class="toggle-item">
            <div class="toggle-info">
              <strong>Reportes Diarios</strong>
              <p>Enviar resumen diario de ventas por email</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" v-model="config.notificaciones.reportesDiarios" />
              <span class="slider"></span>
            </label>
          </div>
        </div>
      </section>

      <!-- Seguridad -->
      <section class="config-section">
        <div class="section-header">
          <h2><i class="bi bi-shield-lock"></i> Seguridad</h2>
        </div>
        <div class="config-toggles">
          <div class="toggle-item">
            <div class="toggle-info">
              <strong>Autenticación de Dos Factores</strong>
              <p>Requiere código adicional al iniciar sesión</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" v-model="config.seguridad.twoFactor" />
              <span class="slider"></span>
            </label>
          </div>
          <div class="toggle-item">
            <div class="toggle-info">
              <strong>Sesión Única</strong>
              <p>Cerrar otras sesiones al iniciar sesión</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" v-model="config.seguridad.sesionUnica" />
              <span class="slider"></span>
            </label>
          </div>
          <div class="config-item">
            <label>Tiempo de Inactividad (minutos)</label>
            <input type="number" v-model="config.seguridad.tiempoInactividad" min="5" max="120" />
          </div>
        </div>
      </section>

      <!-- Integraciones -->
      <section class="config-section">
        <div class="section-header">
          <h2><i class="bi bi-plug"></i> Integraciones</h2>
        </div>
        <div class="integrations-grid">
          <div class="integration-card">
            <div class="integration-icon">
              <i class="bi bi-envelope"></i>
            </div>
            <div class="integration-info">
              <h4>Email (SMTP)</h4>
              <p>Configurar servidor de correo</p>
            </div>
            <button class="btn-config">Configurar</button>
          </div>
          <div class="integration-card">
            <div class="integration-icon">
              <i class="bi bi-whatsapp"></i>
            </div>
            <div class="integration-info">
              <h4>WhatsApp Business</h4>
              <p>Enviar notificaciones por WhatsApp</p>
            </div>
            <button class="btn-config">Configurar</button>
          </div>
          <div class="integration-card">
            <div class="integration-icon">
              <i class="bi bi-cloud"></i>
            </div>
            <div class="integration-info">
              <h4>Backup en la Nube</h4>
              <p>Respaldo automático de datos</p>
            </div>
            <button class="btn-config">Configurar</button>
          </div>
        </div>
      </section>

      <!-- Botones de Acción -->
      <div class="actions">
        <button class="btn-save" @click="saveConfig">
          <i class="bi bi-check-circle"></i> Guardar Cambios
        </button>
        <button class="btn-cancel" @click="resetConfig">
          <i class="bi bi-x-circle"></i> Cancelar
        </button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onBeforeUnmount, computed, defineAsyncComponent } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const MobileConfiguracion = defineAsyncComponent(() => import('./mobile/Configuracion.vue'))

const isMobile = ref(window.innerWidth <= 992)
const currentComponent = computed(() => isMobile.value ? MobileConfiguracion : null)
const handleResize = () => { isMobile.value = window.innerWidth <= 992 }

const config = reactive({
  empresa: {
    nombre: 'Onoxfri',
    ruc: '20123456789',
    telefono: '+51 999 999 999',
    email: 'contacto@onoxfri.com',
    direccion: 'Av. Principal 123, Lima'
  },
  apariencia: {
    tema: 'dark',
    colorPrincipal: '#8B5CF6',
    fontSize: 'medium'
  },
  notificaciones: {
    stockBajo: true,
    nuevosPedidos: true,
    reportesDiarios: false
  },
  seguridad: {
    twoFactor: false,
    sesionUnica: true,
    tiempoInactividad: 30
  }
})

const saveConfig = () => {
  console.log('Guardando configuración:', config)
  // Aquí iría la lógica para guardar en el backend
  alert('Configuración guardada exitosamente')
}

const resetConfig = () => {
  console.log('Cancelando cambios')
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
.layout { display: grid; grid-template-columns: 260px 1fr; height: 100vh; overflow: hidden; }

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
}

.page::-webkit-scrollbar { width: 8px; }
.page::-webkit-scrollbar-track { background: transparent; }
.page::-webkit-scrollbar-thumb { background: transparent; border-radius: 10px; transition: background 0.3s ease; }
.page:hover { scrollbar-color: rgba(138, 43, 226, 0.5) rgba(255, 255, 255, 0.05); }
.page:hover::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
.page:hover::-webkit-scrollbar-thumb { background: linear-gradient(180deg, rgba(138, 43, 226, 0.6), rgba(189, 147, 255, 0.4)); }
.page:hover::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, rgba(138, 43, 226, 0.8), rgba(189, 147, 255, 0.6)); }

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

.page-header { position: relative; z-index: 1; margin-bottom: 24px; }
.page-header h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.8rem; color: #ffffff; display: flex; align-items: center; gap: 12px; text-shadow: 0 0 8px rgba(255,255,255,.35); }

.config-section { position: relative; z-index: 1; background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(138,43,226,0.15); }
.section-header { margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid rgba(138,43,226,0.3); }
.section-header h2 { margin: 0; font-family: 'Space Grotesk', sans-serif; color: #ffffff; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }

.config-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.config-item { display: flex; flex-direction: column; gap: 8px; }
.config-item.span-2 { grid-column: span 2; }
.config-item label { color: #f0e1ff; font-size: 0.9rem; font-weight: 600; }
.config-item input, .config-item select { padding: 10px 14px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.9rem; transition: all .2s ease; }
.config-item input:focus, .config-item select:focus { outline: none; border-color: rgba(189,147,255,0.5); background: rgba(255,255,255,0.08); }
.color-input { height: 45px; cursor: pointer; }

.config-toggles { display: flex; flex-direction: column; gap: 16px; }
.toggle-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: rgba(255,255,255,0.02); border: 1px solid rgba(189,147,255,0.15); border-radius: 12px; transition: all .2s ease; }
.toggle-item:hover { background: rgba(189,147,255,0.06); border-color: rgba(189,147,255,0.3); }
.toggle-info strong { display: block; color: #ffffff; font-size: 1rem; margin-bottom: 4px; }
.toggle-info p { margin: 0; color: #a89bb9; font-size: 0.85rem; }

.toggle-switch { position: relative; display: inline-block; width: 50px; height: 26px; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.1); border-radius: 26px; transition: .3s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: #ffffff; border-radius: 50%; transition: .3s; }
input:checked + .slider { background: linear-gradient(135deg, #4B0082, #8B5CF6); }
input:checked + .slider:before { transform: translateX(24px); }

.integrations-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.integration-card { background: rgba(255,255,255,0.02); border: 1px solid rgba(189,147,255,0.25); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 12px; transition: all .2s ease; }
.integration-card:hover { background: rgba(189,147,255,0.06); border-color: rgba(189,147,255,0.4); transform: translateY(-2px); }
.integration-icon { width: 60px; height: 60px; background: linear-gradient(135deg, rgba(138,43,226,0.3), rgba(189,147,255,0.2)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #bd93ff; }
.integration-info h4 { margin: 0; color: #ffffff; font-size: 1rem; }
.integration-info p { margin: 4px 0 0 0; color: #a89bb9; font-size: 0.85rem; }
.btn-config { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all .2s ease; }
.btn-config:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(138,43,226,0.3); }

.actions { position: relative; z-index: 1; display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; }
.btn-save, .btn-cancel { padding: 12px 24px; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all .2s ease; }
.btn-save { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; box-shadow: 0 8px 24px rgba(138,43,226,0.25); }
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(138,43,226,0.35); }
.btn-cancel { background: rgba(255,255,255,0.05); color: #ffffff; border: 1px solid rgba(189,147,255,0.3); }
.btn-cancel:hover { background: rgba(255,255,255,0.1); }

@media (max-width: 1100px) {
  .layout { grid-template-columns: 80px 1fr; }
  .config-grid { grid-template-columns: 1fr; }
  .config-item.span-2 { grid-column: auto; }
  .integrations-grid { grid-template-columns: 1fr; }
}
</style>