<template>
  <div class="empresas-container">
    <!-- Contenido principal (Sidebar ahora está en App.vue) -->
    <div class="main-content">
      <div class="page-header">
        <div class="page-title">
          <i class="fas fa-building"></i>
          Gestión de Empresas
        </div>
        <div class="page-actions">
          <button class="btn-secondary-futuristic" @click="showCreateModal = true">
            <i class="fas fa-plus"></i>
            Nueva Empresa
          </button>
        </div>
      </div>

      <!-- Estadísticas rápidas -->
      <div class="stats-section">
        <div class="stat-card">
          <div class="stat-value">{{ empresas.length }}</div>
          <div class="stat-label">Total Empresas</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ empresasActivas.length }}</div>
          <div class="stat-label">Empresas Activas</div>
        </div>
      </div>

      <!-- Filtros y búsqueda -->
      <div class="filters-section">
        <div class="search-box">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar empresas..."
            class="form-input"
          >
          <i class="fas fa-search"></i>
        </div>
      </div>

      <!-- Lista de empresas -->
      <div class="empresas-grid">
        <div
          v-for="empresa in filteredEmpresas"
          :key="empresa.id"
          class="empresa-card"
          @click="selectEmpresa(empresa)"
        >
          <div class="empresa-header">
            <h3 class="empresa-name">{{ empresa.nombre_empresa }}</h3>
            <div class="empresa-actions">
              <button
                class="btn-icon"
                @click.stop="editEmpresa(empresa)"
                :title="'Editar ' + empresa.nombre_empresa"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="btn-icon delete"
                @click.stop="confirmDeleteEmpresa(empresa)"
                :title="'Eliminar ' + empresa.nombre_empresa"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>

          <div class="empresa-body">
            <p v-if="empresa.descripcion" class="empresa-description">
              {{ empresa.descripcion }}
            </p>
            <p v-else class="no-description">Sin descripción</p>
          </div>

          <div class="empresa-footer">
            <div class="empresa-meta">
              <span class="meta-item">
                <i class="fas fa-user"></i>
                Usuario ID: {{ empresa.usuario_id }}
              </span>
              <span class="meta-item">
                <i class="fas fa-calendar"></i>
                {{ formatDate(empresa.fecha_creacion) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Estado vacío -->
      <div v-if="filteredEmpresas.length === 0" class="empty-state">
        <i class="fas fa-building"></i>
        <h3>No se encontraron empresas</h3>
        <p>{{ searchQuery ? 'No hay empresas que coincidan con tu búsqueda' : 'Aún no tienes empresas registradas' }}</p>
        <button v-if="!searchQuery" class="btn-futuristic" @click="showCreateModal = true">
          <i class="fas fa-plus"></i>
          Crear Primera Empresa
        </button>
      </div>
    </div>

    <!-- Modal crear/editar empresa -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click="closeModals">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Editar Empresa' : 'Nueva Empresa' }}</h3>
          <button class="modal-close" @click="closeModals">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form class="modal-body" @submit.prevent="saveEmpresa">
          <div class="form-group">
            <label for="empresa_nombre">Nombre de la Empresa *</label>
            <input
              id="empresa_nombre"
              v-model="empresaForm.nombre_empresa"
              type="text"
              class="form-input"
              :class="{ 'error': errors.nombre_empresa }"
              placeholder="Ingrese el nombre de la empresa"
              required
            >
            <div v-if="errors.nombre_empresa" class="error-message">
              {{ errors.nombre_empresa }}
            </div>
          </div>

          <div class="form-group">
            <label for="empresa_descripcion">Descripción</label>
            <textarea
              id="empresa_descripcion"
              v-model="empresaForm.descripcion"
              class="form-input"
              rows="3"
              placeholder="Descripción opcional de la empresa"
            ></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-ghost-futuristic" @click="closeModals">
              Cancelar
            </button>
            <button type="submit" class="btn-futuristic" :disabled="isSaving">
              <span v-if="isSaving" class="loading-futuristic"></span>
              {{ isSaving ? 'Guardando...' : (showEditModal ? 'Actualizar' : 'Crear') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="showDeleteModal = false">
      <div class="modal-content delete-modal" @click.stop>
        <div class="modal-header">
          <h3>Confirmar Eliminación</h3>
          <button class="modal-close" @click="showDeleteModal = false">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="modal-body">
          <p>¿Estás seguro de que deseas eliminar la empresa <strong>{{ empresaToDelete?.nombre_empresa }}</strong>?</p>
          <p class="warning-text">
            <i class="fas fa-exclamation-triangle"></i>
            Esta acción no se puede deshacer.
          </p>
        </div>

        <div class="modal-footer">
          <button class="btn-ghost-futuristic" @click="showDeleteModal = false">
            Cancelar
          </button>
          <button class="btn-futuristic delete" @click="deleteEmpresa" :disabled="isDeleting">
            <span v-if="isDeleting" class="loading-futuristic"></span>
            {{ isDeleting ? 'Eliminando...' : 'Eliminar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { empresaService } from '../services/api.js'

// Estado reactivo
const empresas = ref([])
const searchQuery = ref('')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const empresaToDelete = ref(null)
const isLoading = ref(false)
const isSaving = ref(false)
const isDeleting = ref(false)

// Formulario para crear/editar empresa
const empresaForm = reactive({
  nombre_empresa: '',
  descripcion: ''
})

// Errores de validación
const errors = ref({})

// Empresa seleccionada para editar
const editingEmpresa = ref(null)

// Computed
const empresasActivas = computed(() => {
  return empresas.value.filter(empresa => empresa.is_active !== false)
})

const filteredEmpresas = computed(() => {
  if (!searchQuery.value) return empresas.value

  const query = searchQuery.value.toLowerCase()
  return empresas.value.filter(empresa =>
    empresa.nombre_empresa.toLowerCase().includes(query) ||
    (empresa.descripcion && empresa.descripcion.toLowerCase().includes(query))
  )
})

// Métodos
const loadEmpresas = async () => {
  isLoading.value = true
  try {
    const empresasData = await empresaService.getMyEmpresas()
    empresas.value = empresasData || []
  } catch (error) {
    console.error('Error al cargar empresas:', error)
    // Mostrar error al usuario
  } finally {
    isLoading.value = false
  }
}

const validateForm = () => {
  errors.value = {}
  let isValid = true

  if (!empresaForm.nombre_empresa.trim()) {
    errors.value.nombre_empresa = 'El nombre de la empresa es requerido'
    isValid = false
  }

  return isValid
}

const saveEmpresa = async () => {
  if (!validateForm()) return

  isSaving.value = true
  try {
    if (showEditModal.value) {
      await empresaService.updateEmpresa(editingEmpresa.value.id, empresaForm)
    } else {
      await empresaService.createEmpresa(empresaForm)
    }

    closeModals()
    loadEmpresas()
  } catch (error) {
    console.error('Error al guardar empresa:', error)
    errors.value.general = error.message
  } finally {
    isSaving.value = false
  }
}

const editEmpresa = (empresa) => {
  editingEmpresa.value = empresa
  empresaForm.nombre_empresa = empresa.nombre_empresa
  empresaForm.descripcion = empresa.descripcion || ''
  showEditModal.value = true
}

const confirmDeleteEmpresa = (empresa) => {
  empresaToDelete.value = empresa
  showDeleteModal.value = true
}

const deleteEmpresa = async () => {
  if (!empresaToDelete.value) return

  isDeleting.value = true
  try {
    await empresaService.deleteEmpresa(empresaToDelete.value.id)
    showDeleteModal.value = false
    empresaToDelete.value = null
    loadEmpresas()
  } catch (error) {
    console.error('Error al eliminar empresa:', error)
  } finally {
    isDeleting.value = false
  }
}

const selectEmpresa = (empresa) => {
  // Aquí puedes implementar navegación a detalles de empresa
  console.log('Empresa seleccionada:', empresa)
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingEmpresa.value = null
  empresaForm.nombre_empresa = ''
  empresaForm.descripcion = ''
  errors.value = {}
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Lifecycle
onMounted(() => {
  loadEmpresas()
})
</script>

<style scoped>
.empresas-container {
  min-height: 100vh;
}

.main-content {
  width: 100%;
  padding: 2rem;
  box-sizing: border-box;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1.5rem 0;
  border-bottom: 2px solid rgba(75, 0, 130, 0.1);

  .page-title {
    font-family: 'Orbitron', monospace;
    font-size: 2rem;
    color: #4B0082;
    display: flex;
    align-items: center;
    margin: 0;

    i {
      margin-right: 1rem;
    }
  }
}

.stats-section {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--bg-secondary);
  border: 2px solid rgba(75, 0, 130, 0.2);
  border-radius: 12px;
  padding: 1.5rem;
  text-align: center;
  flex: 1;

  .stat-value {
    font-family: 'Orbitron', monospace;
    font-size: 2rem;
    font-weight: 700;
    color: #4B0082;
    margin-bottom: 0.5rem;
  }

  .stat-label {
    font-size: 0.875rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
}

.filters-section {
  margin-bottom: 2rem;

  .search-box {
    position: relative;
    max-width: 400px;

    input {
      padding-right: 3rem;
    }

    i {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #666;
    }
  }
}

.empresas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.empresa-card {
  background: var(--bg-secondary);
  border: 2px solid rgba(75, 0, 130, 0.2);
  border-radius: 16px;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;

  &:hover {
    border-color: #4B0082;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(75, 0, 130, 0.2);
  }
}

.empresa-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;

  .empresa-name {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.25rem;
    font-weight: 600;
    color: #4B0082;
    margin: 0;
    flex: 1;
  }

  .empresa-actions {
    display: flex;
    gap: 0.5rem;
    margin-left: 1rem;
  }
}

.btn-icon {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 8px;
  background: rgba(75, 0, 130, 0.1);
  color: #4B0082;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;

  &:hover {
    background: #4B0082;
    color: white;
  }

  &.delete {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;

    &:hover {
      background: #dc3545;
      color: white;
    }
  }
}

.empresa-body {
  margin-bottom: 1rem;

  .empresa-description {
    color: #666;
    line-height: 1.5;
    margin: 0;
  }

  .no-description {
    color: #999;
    font-style: italic;
    margin: 0;
  }
}

.empresa-footer {
  .empresa-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;

    .meta-item {
      font-size: 0.875rem;
      color: #666;
      display: flex;
      align-items: center;

      i {
        margin-right: 0.5rem;
        width: 16px;
      }
    }
  }
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;

  i {
    font-size: 4rem;
    color: #4B0082;
    margin-bottom: 1rem;
  }

  h3 {
    margin-bottom: 1rem;
    color: #4B0082;
  }

  p {
    margin-bottom: 2rem;
  }
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-content {
  background: var(--bg-primary);
  border-radius: 20px;
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid rgba(75, 0, 130, 0.2);

  h3 {
    margin: 0;
    color: #4B0082;
    font-family: 'Space Grotesk', sans-serif;
  }
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #666;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: all 0.3s ease;

  &:hover {
    background: rgba(75, 0, 130, 0.1);
    color: #4B0082;
  }
}

.modal-body {
  padding: 1.5rem;

  .form-group {
    margin-bottom: 1.5rem;

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .form-input {
      width: 100%;
      padding: 0.75rem 1rem;
      background: var(--bg-secondary);
      border: 2px solid rgba(75, 0, 130, 0.3);
      border-radius: 8px;
      color: var(--text-primary);
      font-family: inherit;
      transition: all 0.3s ease;

      &:focus {
        outline: none;
        border-color: #4B0082;
        box-shadow: 0 0 0 3px rgba(75, 0, 130, 0.1);
      }

      &.error {
        border-color: #dc3545;
      }
    }

    textarea.form-input {
      resize: vertical;
      min-height: 80px;
    }
  }

  .error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
  }
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.modal-footer {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding: 1.5rem;
  border-top: 1px solid rgba(75, 0, 130, 0.2);
}

.warning-text {
  color: #dc3545;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 1rem 0;

  i {
    margin-right: 0.5rem;
  }
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .empresas-grid {
    grid-template-columns: 1fr;
  }

  .form-actions,
  .modal-footer {
    flex-direction: column;
  }
}
</style>