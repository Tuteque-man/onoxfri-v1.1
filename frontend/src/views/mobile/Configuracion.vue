<template>
  <div class="mobile-page">
    <!-- Header -->
    <div class="mobile-header">
      <button class="back-btn" @click="$router.back()">
        <i class="bi bi-arrow-left"></i>
      </button>
      <h1><i class="bi bi-gear"></i> Configuración</h1>
      <div style="width: 40px;"></div>
    </div>

    <!-- Content -->
    <div class="content-mobile">
      <!-- Información de la Empresa -->
      <div class="config-section-mobile">
        <div class="section-header-mobile">
          <i class="bi bi-building"></i>
          <h2>Información de la Empresa</h2>
        </div>
        <div class="config-form">
          <div class="form-group">
            <label>Nombre de la Empresa</label>
            <input type="text" v-model="config.empresa.nombre" placeholder="Mi Empresa S.A." />
          </div>
          <div class="form-group">
            <label>RUC / NIT</label>
            <input type="text" v-model="config.empresa.ruc" placeholder="20123456789" />
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" v-model="config.empresa.telefono" placeholder="+51 999 999 999" />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" v-model="config.empresa.email" placeholder="contacto@empresa.com" />
          </div>
          <div class="form-group">
            <label>Dirección</label>
            <input type="text" v-model="config.empresa.direccion" placeholder="Av. Principal 123, Lima" />
          </div>
        </div>
      </div>

      <!-- Apariencia -->
      <div class="config-section-mobile">
        <div class="section-header-mobile">
          <i class="bi bi-palette"></i>
          <h2>Apariencia</h2>
        </div>
        <div class="config-form">
          <div class="form-group">
            <label>Tema</label>
            <select v-model="config.apariencia.tema">
              <option value="dark">Oscuro</option>
              <option value="light">Claro</option>
              <option value="auto">Automático</option>
            </select>
          </div>
          <div class="form-group">
            <label>Color Principal</label>
            <input type="color" v-model="config.apariencia.colorPrincipal" class="color-input-mobile" />
          </div>
          <div class="form-group">
            <label>Tamaño de Fuente</label>
            <select v-model="config.apariencia.fontSize">
              <option value="small">Pequeño</option>
              <option value="medium">Mediano</option>
              <option value="large">Grande</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Notificaciones -->
      <div class="config-section-mobile">
        <div class="section-header-mobile">
          <i class="bi bi-bell"></i>
          <h2>Notificaciones</h2>
        </div>
        <div class="toggles-list">
          <div class="toggle-item-mobile">
            <div class="toggle-info-mobile">
              <strong>Stock Bajo</strong>
              <p>Recibir alertas cuando el stock esté por debajo del mínimo</p>
            </div>
            <label class="toggle-switch-mobile">
              <input type="checkbox" v-model="config.notificaciones.stockBajo" />
              <span class="slider-mobile"></span>
            </label>
          </div>
          <div class="toggle-item-mobile">
            <div class="toggle-info-mobile">
              <strong>Nuevos Pedidos</strong>
              <p>Notificar cuando se registre un nuevo pedido</p>
            </div>
            <label class="toggle-switch-mobile">
              <input type="checkbox" v-model="config.notificaciones.nuevosPedidos" />
              <span class="slider-mobile"></span>
            </label>
          </div>
          <div class="toggle-item-mobile">
            <div class="toggle-info-mobile">
              <strong>Reportes Diarios</strong>
              <p>Enviar resumen diario de ventas por email</p>
            </div>
            <label class="toggle-switch-mobile">
              <input type="checkbox" v-model="config.notificaciones.reportesDiarios" />
              <span class="slider-mobile"></span>
            </label>
          </div>
        </div>
      </div>

      <!-- Seguridad -->
      <div class="config-section-mobile">
        <div class="section-header-mobile">
          <i class="bi bi-shield-lock"></i>
          <h2>Seguridad</h2>
        </div>
        <div class="toggles-list">
          <div class="toggle-item-mobile">
            <div class="toggle-info-mobile">
              <strong>Autenticación de Dos Factores</strong>
              <p>Requiere código adicional al iniciar sesión</p>
            </div>
            <label class="toggle-switch-mobile">
              <input type="checkbox" v-model="config.seguridad.twoFactor" />
              <span class="slider-mobile"></span>
            </label>
          </div>
          <div class="toggle-item-mobile">
            <div class="toggle-info-mobile">
              <strong>Sesión Única</strong>
              <p>Cerrar otras sesiones al iniciar sesión</p>
            </div>
            <label class="toggle-switch-mobile">
              <input type="checkbox" v-model="config.seguridad.sesionUnica" />
              <span class="slider-mobile"></span>
            </label>
          </div>
          <div class="form-group">
            <label>Tiempo de Inactividad (minutos)</label>
            <input type="number" v-model="config.seguridad.tiempoInactividad" min="5" max="120" />
          </div>
        </div>
      </div>

      <!-- Integraciones -->
      <div class="config-section-mobile">
        <div class="section-header-mobile">
          <i class="bi bi-plug"></i>
          <h2>Integraciones</h2>
        </div>
        <div class="integrations-list">
          <div class="integration-card-mobile">
            <div class="integration-icon-mobile">
              <i class="bi bi-envelope"></i>
            </div>
            <div class="integration-info-mobile">
              <h4>Email (SMTP)</h4>
              <p>Configurar servidor de correo</p>
            </div>
            <button class="btn-config-mobile">Configurar</button>
          </div>
          <div class="integration-card-mobile">
            <div class="integration-icon-mobile">
              <i class="bi bi-whatsapp"></i>
            </div>
            <div class="integration-info-mobile">
              <h4>WhatsApp Business</h4>
              <p>Enviar notificaciones por WhatsApp</p>
            </div>
            <button class="btn-config-mobile">Configurar</button>
          </div>
          <div class="integration-card-mobile">
            <div class="integration-icon-mobile">
              <i class="bi bi-cloud"></i>
            </div>
            <div class="integration-info-mobile">
              <h4>Backup en la Nube</h4>
              <p>Respaldo automático de datos</p>
            </div>
            <button class="btn-config-mobile">Configurar</button>
          </div>
        </div>
      </div>

      <!-- Botones de Acción -->
      <div class="actions-mobile">
        <button class="btn-save-mobile" @click="saveConfig">
          <i class="bi bi-check-circle"></i> Guardar Cambios
        </button>
        <button class="btn-cancel-mobile" @click="resetConfig">
          <i class="bi bi-x-circle"></i> Cancelar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

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
  alert('Configuración guardada exitosamente')
}

const resetConfig = () => {
  console.log('Cancelando cambios')
  router.back()
}
</script>

<style scoped>
.mobile-page { 
  height: 100vh; 
  overflow-y: auto;
  overflow-x: hidden;
  background: linear-gradient(180deg, #0a0014 0%, #1a0033 100%);
  
  /* Scrollbar styling */
  scrollbar-width: thin;
  scrollbar-color: rgba(138, 43, 226, 0.5) transparent;
}

.mobile-page::-webkit-scrollbar { width: 6px; }
.mobile-page::-webkit-scrollbar-track { background: transparent; }
.mobile-page::-webkit-scrollbar-thumb { 
  background: linear-gradient(180deg, rgba(138, 43, 226, 0.6), rgba(189, 147, 255, 0.4)); 
  border-radius: 10px; 
}
.mobile-page::-webkit-scrollbar-thumb:hover { 
  background: linear-gradient(180deg, rgba(138, 43, 226, 0.8), rgba(189, 147, 255, 0.6)); 
}

.mobile-header { 
  position: sticky; 
  top: 0; 
  z-index: 100; 
  background: linear-gradient(180deg, rgba(18,0,30,0.95), rgba(10,0,20,0.95)); 
  backdrop-filter: blur(10px); 
  padding: 16px; 
  display: flex; 
  align-items: center; 
  justify-content: space-between; 
  border-bottom: 1px solid rgba(138,43,226,0.3); 
}
.mobile-header h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.2rem; color: #ffffff; display: flex; align-items: center; gap: 8px; }
.back-btn { background: rgba(138,43,226,0.2); border: 1px solid rgba(189,147,255,0.3); color: #ffffff; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

.content-mobile { padding: 16px; padding-bottom: 80px; }

.config-section-mobile { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 20px; margin-bottom: 16px; }
.section-header-mobile { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid rgba(138,43,226,0.3); }
.section-header-mobile i { font-size: 1.5rem; color: #bd93ff; }
.section-header-mobile h2 { margin: 0; font-family: 'Space Grotesk', sans-serif; color: #ffffff; font-size: 1.1rem; }

.config-form { display: flex; flex-direction: column; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group label { color: #f0e1ff; font-size: 0.9rem; font-weight: 600; }
.form-group input, .form-group select { padding: 12px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 10px; color: #ffffff; font-size: 1rem; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: rgba(189,147,255,0.5); background: rgba(255,255,255,0.08); }
.color-input-mobile { height: 50px; cursor: pointer; }

.toggles-list { display: flex; flex-direction: column; gap: 12px; }
.toggle-item-mobile { display: flex; justify-content: space-between; align-items: center; padding: 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(189,147,255,0.15); border-radius: 12px; }
.toggle-info-mobile { flex: 1; padding-right: 12px; }
.toggle-info-mobile strong { display: block; color: #ffffff; font-size: 0.95rem; margin-bottom: 4px; }
.toggle-info-mobile p { margin: 0; color: #a89bb9; font-size: 0.8rem; line-height: 1.3; }

.toggle-switch-mobile { position: relative; display: inline-block; width: 50px; height: 26px; flex-shrink: 0; }
.toggle-switch-mobile input { opacity: 0; width: 0; height: 0; }
.slider-mobile { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.1); border-radius: 26px; transition: .3s; }
.slider-mobile:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: #ffffff; border-radius: 50%; transition: .3s; }
input:checked + .slider-mobile { background: linear-gradient(135deg, #4B0082, #8B5CF6); }
input:checked + .slider-mobile:before { transform: translateX(24px); }

.integrations-list { display: flex; flex-direction: column; gap: 12px; }
.integration-card-mobile { background: rgba(255,255,255,0.02); border: 1px solid rgba(189,147,255,0.25); border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 12px; }
.integration-icon-mobile { width: 50px; height: 50px; background: linear-gradient(135deg, rgba(138,43,226,0.3), rgba(189,147,255,0.2)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #bd93ff; flex-shrink: 0; }
.integration-info-mobile { flex: 1; }
.integration-info-mobile h4 { margin: 0; color: #ffffff; font-size: 0.95rem; margin-bottom: 2px; }
.integration-info-mobile p { margin: 0; color: #a89bb9; font-size: 0.8rem; }
.btn-config-mobile { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; white-space: nowrap; }

.actions-mobile { display: flex; flex-direction: column; gap: 12px; margin-top: 24px; }
.btn-save-mobile, .btn-cancel-mobile { padding: 14px; border: none; border-radius: 12px; font-weight: 600; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-save-mobile { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; box-shadow: 0 8px 24px rgba(138,43,226,0.25); }
.btn-cancel-mobile { background: rgba(255,255,255,0.05); color: #ffffff; border: 1px solid rgba(189,147,255,0.3); }
</style>