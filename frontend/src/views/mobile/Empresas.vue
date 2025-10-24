<template>
  <div class="mobile-layout">
    <!-- Header Móvil -->
    <header class="mobile-header">
      <h1><i class="bi bi-building"></i> Mi Empresa</h1>
      <button class="edit-btn" @click="editarEmpresa">
        <i class="bi bi-pencil"></i>
      </button>
    </header>

    <!-- Contenido -->
    <main class="mobile-content">
      <!-- Perfil de Empresa -->
      <section class="empresa-card">
        <div class="empresa-logo">
          <img :src="empresa.logo" :alt="empresa.nombre" />
        </div>
        <h2>{{ empresa.nombre }}</h2>
        <p class="slogan">{{ empresa.slogan }}</p>
        <div class="meta-info">
          <div class="meta-item">
            <i class="bi bi-geo-alt"></i>
            <span>{{ empresa.ubicacion }}</span>
          </div>
          <div class="meta-item">
            <i class="bi bi-calendar"></i>
            <span>Fundada en {{ empresa.fundacion }}</span>
          </div>
          <div class="meta-item">
            <i class="bi bi-people"></i>
            <span>{{ empresa.empleados }} empleados</span>
          </div>
        </div>
      </section>

      <!-- Estadísticas -->
      <section class="stats-section">
        <div class="stat-card">
          <i class="bi bi-trophy"></i>
          <h3>{{ empresa.proyectos }}</h3>
          <p>Proyectos</p>
        </div>
        <div class="stat-card">
          <i class="bi bi-star"></i>
          <h3>{{ empresa.clientes }}</h3>
          <p>Clientes</p>
        </div>
        <div class="stat-card">
          <i class="bi bi-award"></i>
          <h3>{{ empresa.premios }}</h3>
          <p>Premios</p>
        </div>
        <div class="stat-card">
          <i class="bi bi-graph-up"></i>
          <h3>{{ empresa.crecimiento }}%</h3>
          <p>Crecimiento</p>
        </div>
      </section>

      <!-- Información General -->
      <section class="info-card">
        <h3><i class="bi bi-info-circle"></i> Información General</h3>
        <div class="info-content">
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
      </section>

      <!-- Descripción -->
      <section class="info-card">
        <h3><i class="bi bi-file-text"></i> Descripción</h3>
        <div class="info-content">
          <p class="descripcion">{{ empresa.descripcion }}</p>
        </div>
      </section>

      <!-- Equipo -->
      <section class="team-section">
        <h3><i class="bi bi-people-fill"></i> Nuestro Equipo</h3>
        <div class="team-grid">
          <div v-for="empleado in empresa.equipo" :key="empleado.id" class="team-card">
            <img :src="empleado.avatar" :alt="empleado.nombre" />
            <div class="team-info">
              <h4>{{ empleado.nombre }}</h4>
              <p>{{ empleado.cargo }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Modal Editar -->
      <div v-if="showModal" class="modal-overlay" @click="closeModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h3><i class="bi bi-pencil"></i> Editar Empresa</h3>
            <button class="btn-close" @click="closeModal">
              <i class="bi bi-x"></i>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="guardarCambios">
              <div class="form-group">
                <label>Nombre *</label>
                <input type="text" v-model="form.nombre" required />
              </div>
              <div class="form-group">
                <label>Slogan</label>
                <input type="text" v-model="form.slogan" />
              </div>
              <div class="form-group">
                <label>RUC/NIT *</label>
                <input type="text" v-model="form.ruc" required />
              </div>
              <div class="form-group">
                <label>Email *</label>
                <input type="email" v-model="form.email" required />
              </div>
              <div class="form-group">
                <label>Teléfono</label>
                <input type="tel" v-model="form.telefono" />
              </div>
              <div class="form-group">
                <label>Sitio Web</label>
                <input type="url" v-model="form.website" />
              </div>
              <div class="form-group">
                <label>Ubicación</label>
                <input type="text" v-model="form.ubicacion" />
              </div>
              <div class="form-group">
                <label>Dirección</label>
                <textarea v-model="form.direccion" rows="2"></textarea>
              </div>
              <div class="form-group">
                <label>Número de Empleados</label>
                <input type="number" v-model.number="form.empleados" min="1" />
              </div>
              <div class="form-group">
                <label>Descripción</label>
                <textarea v-model="form.descripcion" rows="4"></textarea>
              </div>
              <div class="modal-actions">
                <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
                <button type="submit" class="btn-save">
                  <i class="bi bi-check-circle"></i> Guardar
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
import { reactive, ref } from 'vue'

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
    descripcion: form.descripcion
  })
  closeModal()
}
</script>

<style scoped>
.mobile-layout {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0015 0%, #1a0030 50%, #0a0015 100%);
  padding-bottom: 20px;
}

.mobile-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: linear-gradient(135deg, rgba(18,0,30,0.95), rgba(10,0,20,0.95));
  backdrop-filter: blur(10px);
  border-bottom: 2px solid rgba(138,43,226,0.3);
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 4px 20px rgba(138,43,226,0.2);
}

.mobile-header h1 {
  margin: 0;
  font-family: 'Orbitron', monospace;
  font-size: 1.2rem;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}

.edit-btn {
  background: rgba(138,43,226,0.2);
  border: 1px solid rgba(138,43,226,0.4);
  color: #bd93ff;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  cursor: pointer;
  transition: all .3s ease;
}

.edit-btn:active {
  transform: scale(0.95);
  background: rgba(138,43,226,0.3);
}

.mobile-content {
  padding: 16px;
}

.empresa-card {
  background: linear-gradient(180deg, rgba(18,0,30,0.9), rgba(10,0,20,0.9));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 20px;
  padding: 24px;
  text-align: center;
  margin-bottom: 16px;
  box-shadow: 0 8px 24px rgba(138,43,226,0.15);
}

.empresa-logo {
  margin-bottom: 16px;
}

.empresa-logo img {
  width: 100px;
  height: 100px;
  border-radius: 16px;
  border: 3px solid rgba(138,43,226,0.5);
  box-shadow: 0 8px 20px rgba(138,43,226,0.3);
}

.empresa-card h2 {
  margin: 0 0 8px 0;
  font-family: 'Orbitron', monospace;
  font-size: 1.5rem;
  color: #ffffff;
  text-shadow: 0 0 8px rgba(255,255,255,.35);
}

.slogan {
  margin: 0 0 20px 0;
  color: #bd93ff;
  font-size: 1rem;
  font-style: italic;
}

.meta-info {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.meta-item {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #f0e1ff;
  font-size: 0.9rem;
}

.meta-item i {
  color: #bd93ff;
  font-size: 1.1rem;
}

.stats-section {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 16px;
}

.stat-card {
  background: linear-gradient(180deg, rgba(18,0,30,0.9), rgba(10,0,20,0.9));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 16px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 4px 16px rgba(138,43,226,0.1);
}

.stat-card i {
  font-size: 2rem;
  color: #bd93ff;
  margin-bottom: 8px;
}

.stat-card h3 {
  margin: 0 0 4px 0;
  font-family: 'Orbitron', monospace;
  font-size: 1.8rem;
  color: #ffffff;
  text-shadow: 0 0 8px rgba(255,255,255,.35);
}

.stat-card p {
  margin: 0;
  color: #f0e1ff;
  font-size: 0.85rem;
  font-weight: 600;
}

.info-card {
  background: linear-gradient(180deg, rgba(18,0,30,0.9), rgba(10,0,20,0.9));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 4px 16px rgba(138,43,226,0.1);
}

.info-card h3 {
  margin: 0 0 16px 0;
  font-family: 'Space Grotesk', sans-serif;
  color: #ffffff;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  gap: 8px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(138,43,226,0.3);
}

.info-content {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid rgba(189,147,255,0.1);
}

.info-row:last-child {
  border-bottom: none;
}

.info-row .label {
  color: #bd93ff;
  font-weight: 600;
  font-size: 0.85rem;
  flex-shrink: 0;
}

.info-row .value {
  color: #ffffff;
  font-size: 0.85rem;
  text-align: right;
}

.info-row .value a {
  color: #bd93ff;
  text-decoration: none;
  word-break: break-all;
}

.descripcion {
  margin: 0;
  color: #f0e1ff;
  line-height: 1.7;
  font-size: 0.9rem;
}

.team-section {
  margin-bottom: 16px;
}

.team-section h3 {
  margin: 0 0 16px 0;
  font-family: 'Space Grotesk', sans-serif;
  color: #ffffff;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  gap: 8px;
}

.team-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.team-card {
  background: linear-gradient(180deg, rgba(18,0,30,0.9), rgba(10,0,20,0.9));
  border: 2px solid rgba(138,43,226,0.35);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 4px 16px rgba(138,43,226,0.1);
}

.team-card img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid rgba(138,43,226,0.4);
  flex-shrink: 0;
}

.team-info h4 {
  margin: 0 0 4px 0;
  color: #ffffff;
  font-size: 1rem;
  font-weight: 600;
}

.team-info p {
  margin: 0;
  color: #bd93ff;
  font-size: 0.85rem;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.8);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  animation: fadeIn 0.2s ease;
}

.modal {
  background: linear-gradient(180deg, rgba(18,0,30,0.98), rgba(10,0,20,0.98));
  border: 2px solid rgba(138,43,226,0.4);
  border-radius: 20px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(138,43,226,0.3);
  animation: slideUp 0.3s ease;
}

.modal-header {
  padding: 20px;
  background: linear-gradient(135deg, rgba(138,43,226,0.2), rgba(75,0,130,0.2));
  border-bottom: 1px solid rgba(138,43,226,0.3);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-family: 'Space Grotesk', sans-serif;
  color: #ffffff;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-close {
  background: none;
  border: none;
  color: #bd93ff;
  font-size: 1.5rem;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  cursor: pointer;
  transition: background .2s ease;
}

.btn-close:active {
  background: rgba(138,43,226,0.2);
}

.modal-body {
  padding: 20px;
  max-height: calc(90vh - 140px);
  overflow-y: auto;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  color: #bd93ff;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 6px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(138,43,226,0.3);
  border-radius: 10px;
  color: #ffffff;
  font-size: 0.9rem;
  font-family: inherit;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: rgba(138,43,226,0.6);
  background: rgba(255,255,255,0.08);
}

.form-group textarea {
  resize: vertical;
}

.modal-actions {
  display: flex;
  gap: 12px;
  margin-top: 24px;
}

.btn-cancel {
  flex: 1;
  padding: 12px;
  background: transparent;
  border: 1px solid rgba(138,43,226,0.4);
  color: #ffffff;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s ease;
}

.btn-cancel:active {
  background: rgba(138,43,226,0.1);
}

.btn-save {
  flex: 1;
  padding: 12px;
  background: linear-gradient(135deg, #4B0082, #6B1FA8);
  border: none;
  color: #ffffff;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all .2s ease;
}

.btn-save:active {
  transform: scale(0.98);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
