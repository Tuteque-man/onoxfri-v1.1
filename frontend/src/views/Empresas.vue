<template>
  <component :is="currentComponent" v-if="isMobile" />
  <div v-else class="layout">
    <Sidebar />
    <main class="page">
      <div class="page-header">
        <h1><i class="bi bi-building"></i> Mi Empresa</h1>
        <button class="btn-edit" @click="editarEmpresa()">
          <i class="bi bi-pencil"></i> Editar Información
        </button>
      </div>

      <!-- Perfil de Empresa -->
      <section class="empresa-profile">
        <div class="empresa-header">
          <div class="empresa-logo">
            <img :src="empresa.logo" :alt="empresa.nombre" />
          </div>
          <div class="empresa-info">
            <h2>{{ empresa.nombre }}</h2>
            <p class="empresa-slogan">{{ empresa.slogan }}</p>
            <div class="empresa-meta">
              <span><i class="bi bi-geo-alt"></i> {{ empresa.ubicacion }}</span>
              <span><i class="bi bi-calendar"></i> Fundada en {{ empresa.fundacion }}</span>
              <span><i class="bi bi-people"></i> {{ empresa.empleados }} empleados</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Banner de Empleados -->
      <section class="empleados-banner">
        <h3><i class="bi bi-people-fill"></i> Nuestro Equipo</h3>
        <div class="empleados-grid">
          <div v-for="empleado in empresa.equipo" :key="empleado.id" class="empleado-card">
            <div class="empleado-avatar">
              <img :src="empleado.avatar" :alt="empleado.nombre" />
            </div>
            <div class="empleado-info">
              <h4>{{ empleado.nombre }}</h4>
              <p>{{ empleado.cargo }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Información de la Empresa -->
      <section class="info-section">
        <div class="info-grid">
          <div class="info-card">
            <div class="card-header">
              <h3><i class="bi bi-info-circle"></i> Información General</h3>
            </div>
            <div class="card-body">
              <div class="info-row">
                <span class="label">RUC/NIT:</span>
                <span class="value">{{ empresa.ruc }}</span>
              </div>
              <div class="info-row">
                <span class="label">Email:</span>
                <span class="value">{{ empresa.email }}</span>
              </div>
              <div class="info-row">
                <span class="label">Teléfono:</span>
                <span class="value">{{ empresa.telefono }}</span>
              </div>
              <div class="info-row">
                <span class="label">Sitio Web:</span>
                <span class="value"><a :href="empresa.website" target="_blank">{{ empresa.website }}</a></span>
              </div>
              <div class="info-row">
                <span class="label">Dirección:</span>
                <span class="value">{{ empresa.direccion }}</span>
              </div>
            </div>
          </div>

          <div class="info-card">
            <div class="card-header">
              <h3><i class="bi bi-file-text"></i> Descripción</h3>
            </div>
            <div class="card-body">
              <p class="descripcion">{{ empresa.descripcion }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Estadísticas -->
      <section class="stats-section">
        <div class="stats-grid">
          <div class="stat-card">
            <i class="bi bi-trophy"></i>
            <h4>{{ empresa.proyectos }}</h4>
            <p>Proyectos Completados</p>
          </div>
          <div class="stat-card">
            <i class="bi bi-star"></i>
            <h4>{{ empresa.clientes }}</h4>
            <p>Clientes Satisfechos</p>
          </div>
          <div class="stat-card">
            <i class="bi bi-award"></i>
            <h4>{{ empresa.premios }}</h4>
            <p>Premios Ganados</p>
          </div>
          <div class="stat-card">
            <i class="bi bi-graph-up"></i>
            <h4>{{ empresa.crecimiento }}%</h4>
            <p>Crecimiento Anual</p>
          </div>
        </div>
      </section>

      <!-- Modal Editar -->
      <div v-if="showModal" class="modal-overlay" @click="closeModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h3><i class="bi bi-pencil"></i> Editar Información de la Empresa</h3>
            <button class="btn-close" @click="closeModal">
              <i class="bi bi-x"></i>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="guardarCambios">
              <div class="form-row">
                <div class="form-group">
                  <label>Nombre *</label>
                  <input type="text" v-model="form.nombre" required />
                </div>
                <div class="form-group">
                  <label>RUC/NIT *</label>
                  <input type="text" v-model="form.ruc" required />
                </div>
              </div>
              <div class="form-group">
                <label>Slogan</label>
                <input type="text" v-model="form.slogan" />
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Email *</label>
                  <input type="email" v-model="form.email" required />
                </div>
                <div class="form-group">
                  <label>Teléfono</label>
                  <input type="tel" v-model="form.telefono" />
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Sitio Web</label>
                  <input type="url" v-model="form.website" />
                </div>
                <div class="form-group">
                  <label>Ubicación</label>
                  <input type="text" v-model="form.ubicacion" />
                </div>
              </div>
              <div class="form-group">
                <label>Dirección</label>
                <textarea v-model="form.direccion" rows="2"></textarea>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Número de Empleados</label>
                  <input type="number" v-model.number="form.empleados" min="1" />
                </div>
                <div class="form-group">
                  <label>Año de Fundación</label>
                  <input type="text" v-model="form.fundacion" />
                </div>
              </div>
              <div class="form-group">
                <label>Descripción</label>
                <textarea v-model="form.descripcion" rows="4"></textarea>
              </div>
              <div class="modal-actions">
                <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
                <button type="submit" class="btn-save">
                  <i class="bi bi-check-circle"></i> Guardar Cambios
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onBeforeUnmount, computed, defineAsyncComponent } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const MobileEmpresas = defineAsyncComponent(() => import('./mobile/Empresas.vue'))

const isMobile = ref(window.innerWidth <= 992)
const currentComponent = computed(() => isMobile.value ? MobileEmpresas : null)
const handleResize = () => { isMobile.value = window.innerWidth <= 992 }

const showModal = ref(false)

const empresa = reactive({
  nombre: 'Tech Solutions SAC',
  slogan: 'Innovación y Tecnología para tu Negocio',
  logo: 'https://ui-avatars.com/api/?name=Tech+Solutions&size=200&background=4B0082&color=fff&bold=true',
  ubicacion: 'Lima, Perú',
  fundacion: '2015',
  empleados: 50,
  ruc: '20123456789',
  email: 'contacto@techsolutions.com',
  telefono: '+51 987 654 321',
  website: 'https://techsolutions.com',
  direccion: 'Av. Principal 123, San Isidro, Lima',
  descripcion: 'Somos una empresa líder en soluciones tecnológicas, especializada en desarrollo de software, consultoría IT y transformación digital. Con más de 8 años de experiencia, ayudamos a empresas de todos los tamaños a alcanzar sus objetivos mediante la implementación de tecnologías innovadoras y soluciones personalizadas.',
  proyectos: 150,
  clientes: 85,
  premios: 12,
  crecimiento: 35,
  equipo: [
    { id: 1, nombre: 'Carlos Mendoza', cargo: 'CEO & Fundador', avatar: 'https://ui-avatars.com/api/?name=Carlos+Mendoza&size=100&background=6B1FA8&color=fff' },
    { id: 2, nombre: 'Ana García', cargo: 'CTO', avatar: 'https://ui-avatars.com/api/?name=Ana+Garcia&size=100&background=8B5CF6&color=fff' },
    { id: 3, nombre: 'Luis Torres', cargo: 'Director de Proyectos', avatar: 'https://ui-avatars.com/api/?name=Luis+Torres&size=100&background=A78BFA&color=fff' },
    { id: 4, nombre: 'María Rodríguez', cargo: 'Gerente de Ventas', avatar: 'https://ui-avatars.com/api/?name=Maria+Rodriguez&size=100&background=C4B5FD&color=000' },
    { id: 5, nombre: 'Pedro Sánchez', cargo: 'Líder de Desarrollo', avatar: 'https://ui-avatars.com/api/?name=Pedro+Sanchez&size=100&background=DDD6FE&color=000' },
    { id: 6, nombre: 'Laura Fernández', cargo: 'Diseñadora UX/UI', avatar: 'https://ui-avatars.com/api/?name=Laura+Fernandez&size=100&background=EDE9FE&color=000' },
  ]
})

const form = reactive({
  nombre: '',
  slogan: '',
  ruc: '',
  email: '',
  telefono: '',
  website: '',
  ubicacion: '',
  direccion: '',
  empleados: 0,
  fundacion: '',
  descripcion: ''
})

const editarEmpresa = () => {
  // Copiar datos actuales al formulario
  Object.assign(form, {
    nombre: empresa.nombre,
    slogan: empresa.slogan,
    ruc: empresa.ruc,
    email: empresa.email,
    telefono: empresa.telefono,
    website: empresa.website,
    ubicacion: empresa.ubicacion,
    direccion: empresa.direccion,
    empleados: empresa.empleados,
    fundacion: empresa.fundacion,
    descripcion: empresa.descripcion
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const guardarCambios = () => {
  // Actualizar datos de la empresa
  Object.assign(empresa, {
    nombre: form.nombre,
    slogan: form.slogan,
    ruc: form.ruc,
    email: form.email,
    telefono: form.telefono,
    website: form.website,
    ubicacion: form.ubicacion,
    direccion: form.direccion,
    empleados: form.empleados,
    fundacion: form.fundacion,
    descripcion: form.descripcion
  })
  closeModal()
}

onMounted(() => window.addEventListener('resize', handleResize))
onBeforeUnmount(() => window.removeEventListener('resize', handleResize))
</script>

<style scoped>
.layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  height: 100vh;
  background: linear-gradient(135deg, #0a0015 0%, #1a0030 50%, #0a0015 100%);
  position: relative;
  overflow: hidden;
}

.layout::before {
  content: "";
  position: fixed;
  inset: 0;
  background: radial-gradient(circle at 20% 50%, rgba(138,43,226,0.15) 0%, transparent 50%),
              radial-gradient(circle at 80% 80%, rgba(75,0,130,0.15) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
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

.page-header {
  position: relative;
  z-index: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.stats-grid, .empresas-grid { position: relative; z-index: 1; }

.page-header h1 {
  margin: 0;
  font-family: 'Orbitron', monospace;
  font-size: 1.8rem;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 12px;
  text-shadow: 0 0 8px rgba(255,255,255,.35);
}

.btn-edit {
  background: linear-gradient(135deg, #4B0082, #6B1FA8);
  color: #fff;
  border: none;
  padding: 12px 20px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 8px 24px rgba(138,43,226,0.25);
  transition: all .3s ease;
}

.btn-edit:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(138,43,226,0.35);
}

/* Perfil de Empresa */
.empresa-profile {
  position: relative;
  z-index: 1;
  margin-bottom: 24px;
}

.empresa-header {
  background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 18px;
  padding: 32px;
  display: flex;
  gap: 24px;
  align-items: center;
  box-shadow: 0 15px 40px rgba(138,43,226,0.15);
}

.empresa-logo {
  flex-shrink: 0;
}

.empresa-logo img {
  width: 120px;
  height: 120px;
  border-radius: 16px;
  border: 3px solid rgba(138,43,226,0.5);
  box-shadow: 0 8px 24px rgba(138,43,226,0.3);
}

.empresa-info h2 {
  margin: 0 0 8px 0;
  font-family: 'Orbitron', monospace;
  font-size: 2rem;
  color: #ffffff;
  text-shadow: 0 0 8px rgba(255,255,255,.35);
}

.empresa-slogan {
  margin: 0 0 16px 0;
  color: #bd93ff;
  font-size: 1.1rem;
  font-style: italic;
}

.empresa-meta {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
}

.empresa-meta span {
  color: #f0e1ff;
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.95rem;
}

.empresa-meta i {
  color: #bd93ff;
}

/* Banner de Empleados */
.empleados-banner {
  position: relative;
  z-index: 1;
  margin-bottom: 24px;
}

.empleados-banner h3 {
  margin: 0 0 16px 0;
  font-family: 'Space Grotesk', sans-serif;
  color: #ffffff;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.empleados-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}

.empleado-card {
  background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  transition: all .3s ease;
}

.empleado-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(138,43,226,0.25);
  border-color: rgba(138,43,226,0.5);
}

.empleado-avatar img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid rgba(138,43,226,0.4);
}

.empleado-info h4 {
  margin: 0 0 4px 0;
  color: #ffffff;
  font-size: 1rem;
  font-weight: 600;
}

.empleado-info p {
  margin: 0;
  color: #bd93ff;
  font-size: 0.85rem;
}

/* Información */
.info-section {
  position: relative;
  z-index: 1;
  margin-bottom: 24px;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.info-card {
  background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 15px 40px rgba(138,43,226,0.15);
}

.card-header {
  padding: 16px 20px;
  background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10));
  border-bottom: 1px solid rgba(138,43,226,0.3);
}

.card-header h3 {
  margin: 0;
  font-family: 'Space Grotesk', sans-serif;
  color: #ffffff;
  font-weight: 700;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  gap: 8px;
}

.card-body {
  padding: 20px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid rgba(189,147,255,0.1);
}

.info-row:last-child {
  border-bottom: none;
}

.info-row .label {
  color: #bd93ff;
  font-weight: 600;
  font-size: 0.9rem;
}

.info-row .value {
  color: #ffffff;
  font-size: 0.9rem;
}

.info-row .value a {
  color: #bd93ff;
  text-decoration: none;
  transition: color .2s ease;
}

.info-row .value a:hover {
  color: #ffffff;
}

.descripcion {
  margin: 0;
  color: #f0e1ff;
  line-height: 1.7;
  font-size: 0.95rem;
}

/* Estadísticas */
.stats-section {
  position: relative;
  z-index: 1;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.stat-card {
  background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  transition: all .3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(138,43,226,0.25);
  border-color: rgba(138,43,226,0.5);
}

.stat-card i {
  font-size: 2.5rem;
  color: #bd93ff;
  margin-bottom: 12px;
}

.stat-card h4 {
  margin: 0 0 8px 0;
  font-family: 'Orbitron', monospace;
  font-size: 2rem;
  color: #ffffff;
  text-shadow: 0 0 8px rgba(255,255,255,.35);
}

.stat-card p {
  margin: 0;
  color: #f0e1ff;
  font-size: 0.9rem;
  font-weight: 600;
}

@media (max-width: 1100px) {
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .empresa-header {
    flex-direction: column;
    text-align: center;
  }
  
  .empresa-meta {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .empleados-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.75);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
}

.modal {
  background: linear-gradient(180deg, rgba(18,0,30,0.95), rgba(10,0,20,0.95));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 18px;
  width: 90%;
  max-width: 700px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}

.modal-header {
  padding: 20px;
  background: linear-gradient(135deg, rgba(189,147,255,0.15), rgba(138,43,226,0.15));
  border-bottom: 1px solid rgba(138,43,226,0.3);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  color: #ffffff;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-close {
  background: none;
  border: none;
  color: #ffffff;
  font-size: 1.5rem;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: background .2s ease;
}

.btn-close:hover {
  background: rgba(255,255,255,0.1);
}

.modal-body {
  padding: 24px;
  max-height: calc(90vh - 140px);
  overflow-y: auto;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  margin-bottom: 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  color: #f0e1ff;
  font-size: 0.9rem;
  font-weight: 600;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 10px 14px;
  border: 1px solid rgba(189,147,255,0.25);
  background: rgba(255,255,255,0.05);
  border-radius: 8px;
  color: #ffffff;
  font-size: 0.9rem;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: rgba(189,147,255,0.5);
  background: rgba(255,255,255,0.08);
}

.form-group textarea {
  resize: vertical;
}

.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 24px;
}

.btn-cancel {
  padding: 10px 20px;
  border: 1px solid rgba(189,147,255,0.35);
  background: transparent;
  color: #ffffff;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all .2s ease;
}

.btn-cancel:hover {
  background: rgba(189,147,255,0.1);
}

.btn-save {
  padding: 10px 20px;
  border: none;
  background: linear-gradient(135deg, #4B0082, #6B1FA8);
  color: #ffffff;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all .2s ease;
}

.btn-save:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(138,43,226,0.35);
}
</style>