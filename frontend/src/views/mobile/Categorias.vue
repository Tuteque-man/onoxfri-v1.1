<template>
  <div class="mobile-layout">
    <main class="mobile-page">
      <div class="page-header">
        <h1><i class="bi bi-tags"></i> Gestión de Categorías</h1>
        <button class="btn-add" @click="openModal()">
          <i class="bi bi-plus-circle"></i> Nueva
        </button>
      </div>

      <section class="kpis">
        <div class="kpi">
          <div class="kpi-label">Total Categorías</div>
          <div class="kpi-value">{{ stats.totalCategorias }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Activas</div>
          <div class="kpi-value">{{ stats.categoriasActivas }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Productos</div>
          <div class="kpi-value">{{ stats.totalProductos }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Más Usada</div>
          <div class="kpi-value" style="font-size: 1rem;">{{ stats.masUsada }}</div>
        </div>
      </section>

      <section class="table-section">
        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-table"></i> Categorías</h3>
            <div class="search-box">
              <i class="bi bi-search"></i>
              <input type="text" v-model="searchQuery" placeholder="Buscar..." />
            </div>
          </div>
          <div class="card-body">
            <div class="mobile-list">
              <div v-if="filteredCategorias.length === 0" class="empty">No hay categorías</div>
              <div v-for="categoria in filteredCategorias" :key="categoria.id" class="list-item">
                <div class="item-header">
                  <div class="item-title">
                    <i class="bi bi-tag"></i>
                    <span>{{ categoria.nombre }}</span>
                  </div>
                  <span class="badge" :class="categoria.activo ? 'active' : 'inactive'">
                    {{ categoria.activo ? 'Activa' : 'Inactiva' }}
                  </span>
                </div>
                <div class="item-info">
                  <div class="info-row">
                    <span class="label">Descripción:</span>
                    <span class="value">{{ categoria.descripcion }}</span>
                  </div>
                  <div class="info-row">
                    <span class="label">Productos:</span>
                    <span class="value">{{ categoria.productos }}</span>
                  </div>
                </div>
                <div class="item-actions">
                  <button class="btn-icon edit" @click="openModal(categoria)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-icon delete" @click="confirmDelete(categoria)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Modal Crear/Editar -->
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal">
          <div class="modal-header">
            <h3>
              <i :class="editMode ? 'bi bi-pencil' : 'bi bi-plus-circle'"></i>
              {{ editMode ? 'Editar' : 'Nueva' }}
            </h3>
            <button class="btn-close" @click="closeModal"><i class="bi bi-x"></i></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCategoria">
              <div class="form-group">
                <label>Nombre *</label>
                <input type="text" v-model="form.nombre" required />
              </div>
              <div class="form-group">
                <label>Descripción</label>
                <textarea v-model="form.descripcion" rows="3" placeholder="Descripción..."></textarea>
              </div>
              <div class="form-group">
                <div class="checkbox-group">
                  <input type="checkbox" id="activo" v-model="form.activo" />
                  <label for="activo">Activa</label>
                </div>
              </div>
              <div class="modal-actions">
                <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
                <button type="submit" class="btn-save">
                  <i class="bi bi-check-circle"></i> {{ editMode ? 'Actualizar' : 'Guardar' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal Eliminar -->
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal modal-small">
          <div class="modal-header danger">
            <h3><i class="bi bi-exclamation-triangle"></i> Confirmar</h3>
          </div>
          <div class="modal-body">
            <p>¿Eliminar <strong>{{ categoriaToDelete?.nombre }}</strong>?</p>
            <p class="warning-text">No se puede deshacer.</p>
            <div class="modal-actions">
              <button class="btn-cancel" @click="showDeleteModal = false">Cancelar</button>
              <button class="btn-delete" @click="deleteCategoria">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'

const stats = reactive({ totalCategorias: 0, categoriasActivas: 0, totalProductos: 0, masUsada: '-' })
const categorias = ref([])
const searchQuery = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editMode = ref(false)
const categoriaToDelete = ref(null)
const form = reactive({ id: null, nombre: '', descripcion: '', activo: true })

const filteredCategorias = computed(() => {
  if (!searchQuery.value) return categorias.value
  return categorias.value.filter(c => 
    c.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    c.descripcion.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const load = () => {
  categorias.value = [
    { id: 1, nombre: 'Electrónica', descripcion: 'Productos electrónicos y tecnología', productos: 25, activo: true },
    { id: 2, nombre: 'Ropa', descripcion: 'Prendas de vestir y accesorios', productos: 18, activo: true },
    { id: 3, nombre: 'Alimentos', descripcion: 'Productos alimenticios', productos: 12, activo: true },
    { id: 4, nombre: 'Hogar', descripcion: 'Artículos para el hogar', productos: 8, activo: false },
  ]
  updateStats()
}

const updateStats = () => {
  stats.totalCategorias = categorias.value.length
  stats.categoriasActivas = categorias.value.filter(c => c.activo).length
  stats.totalProductos = categorias.value.reduce((sum, c) => sum + c.productos, 0)
  const masUsadaCat = categorias.value.reduce((max, c) => c.productos > max.productos ? c : max, categorias.value[0])
  stats.masUsada = masUsadaCat ? masUsadaCat.nombre : '-'
}

const openModal = (categoria = null) => {
  if (categoria) {
    editMode.value = true
    Object.assign(form, { ...categoria })
  } else {
    editMode.value = false
    Object.assign(form, { id: null, nombre: '', descripcion: '', activo: true })
  }
  showModal.value = true
}

const closeModal = () => { showModal.value = false }

const saveCategoria = () => {
  if (editMode.value) {
    const index = categorias.value.findIndex(c => c.id === form.id)
    if (index !== -1) {
      categorias.value[index] = { ...form, productos: categorias.value[index].productos }
    }
  } else {
    categorias.value.push({ ...form, id: Math.max(...categorias.value.map(c => c.id), 0) + 1, productos: 0 })
  }
  updateStats()
  closeModal()
}

const confirmDelete = (categoria) => {
  categoriaToDelete.value = categoria
  showDeleteModal.value = true
}

const deleteCategoria = () => {
  categorias.value = categorias.value.filter(c => c.id !== categoriaToDelete.value.id)
  updateStats()
  showDeleteModal.value = false
}

load()
</script>

<style scoped>
.mobile-layout { display: flex; flex-direction: column; min-height: 100vh; height: 100vh; width: 100%; max-width: 100vw; overflow: hidden; position: relative; }
.mobile-page { position: relative; padding: 16px; padding-bottom: 100px; overflow-y: auto; overflow-x: hidden; flex: 1; width: 100%; max-width: 100%; box-sizing: border-box; -webkit-overflow-scrolling: touch; }
.mobile-page::before { content: ""; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(800px 400px at 10% 10%, rgba(138,43,226,0.10), transparent 60%), radial-gradient(600px 300px at 90% 20%, rgba(189,147,255,0.08), transparent 60%), linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.00)); animation: background-pulse 12s infinite ease-in-out; z-index: 0; }
@keyframes background-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.85; } }

.page-header { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.page-header h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.3rem; color: #ffffff; display: flex; align-items: center; gap: 8px; text-shadow: 0 0 8px rgba(255,255,255,.35); }
.btn-add { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: none; padding: 10px 16px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 6px 20px rgba(138,43,226,0.25); transition: all .3s ease; font-size: 0.9rem; }
.btn-add:active { transform: scale(0.95); }

.kpis { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px; width: 100%; max-width: 100%; box-sizing: border-box; }
.kpi { position: relative; background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%); border: 2px solid rgba(138,43,226,0.35); border-radius: 12px; padding: 14px; box-shadow: 0 8px 20px rgba(138,43,226,0.12), inset 0 0 14px rgba(138,43,226,0.08); backdrop-filter: blur(4px); overflow: hidden; }
.kpi::before { content: ""; position: absolute; inset: 0; border-radius: 12px; pointer-events: none; background: linear-gradient(120deg, transparent 0%, rgba(189,147,255,.18) 50%, transparent 100%); opacity: .35; }
.kpi-label { color: #f0e1ff; font-size: 0.75rem; font-weight: 700; letter-spacing: .3px; margin-bottom: 4px; }
.kpi-value { font-family: 'Orbitron', monospace; font-size: 1.5rem; color: #ffffff; text-shadow: 0 0 8px rgba(255,255,255,.35), 0 0 16px rgba(255,255,255,.22); }

.table-section { position: relative; z-index: 1; margin-top: 12px; width: 100%; max-width: 100%; overflow: hidden; }
.card { position: relative; background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 14px; overflow: hidden; box-shadow: 0 12px 30px rgba(138,43,226,0.15), inset 0 0 20px rgba(138,43,226,0.06); }
.card-header { padding: 12px 14px; background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10)); border-bottom: 1px solid rgba(138,43,226,0.3); }
.card-header h3 { margin: 0 0 10px 0; font-family: 'Space Grotesk', sans-serif; color: #ffffff; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 6px; }
.card-body { padding: 14px; }

.search-box { position: relative; display: flex; align-items: center; }
.search-box i { position: absolute; left: 10px; color: #a89bb9; font-size: 0.85rem; }
.search-box input { padding: 8px 10px 8px 32px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.85rem; width: 100%; }
.search-box input:focus { outline: none; border-color: rgba(189,147,255,0.5); background: rgba(255,255,255,0.08); }

.mobile-list { display: flex; flex-direction: column; gap: 12px; }
.list-item { background: rgba(255,255,255,0.02); border: 1px solid rgba(189,147,255,0.25); border-radius: 12px; padding: 12px; transition: all .2s ease; }
.list-item:active { background: rgba(189,147,255,0.06); transform: scale(0.98); }

.item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.item-title { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #ffffff; font-size: 0.95rem; }
.item-title i { color: #bd93ff; }

.badge { padding: 4px 10px; border-radius: 10px; font-size: 0.7rem; font-weight: 600; }
.badge.active { background: rgba(160,255,143,0.15); color: #a0ff8f; }
.badge.inactive { background: rgba(255,128,116,0.15); color: #ff8074; }

.item-info { display: flex; flex-direction: column; gap: 6px; margin-bottom: 10px; }
.info-row { display: flex; justify-content: space-between; font-size: 0.85rem; }
.info-row .label { color: #a89bb9; }
.info-row .value { color: #ffffff; font-weight: 500; }

.item-actions { display: flex; gap: 8px; justify-content: flex-end; }
.btn-icon { width: 36px; height: 36px; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s ease; }
.btn-icon.edit { background: rgba(138,43,226,0.2); color: #bd93ff; }
.btn-icon.edit:active { background: rgba(138,43,226,0.35); transform: scale(0.9); }
.btn-icon.delete { background: rgba(255,128,116,0.15); color: #ff8074; }
.btn-icon.delete:active { background: rgba(255,128,116,0.25); transform: scale(0.9); }

.empty { color: #a89bb9; text-align: center; padding: 20px; font-size: 0.9rem; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); padding: 16px; }
.modal { background: linear-gradient(180deg, rgba(18,0,30,0.95), rgba(10,0,20,0.95)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; width: 100%; max-width: 500px; max-height: 90vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
.modal-small { max-width: 380px; }
.modal-header { padding: 16px; background: linear-gradient(135deg, rgba(189,147,255,0.15), rgba(138,43,226,0.15)); border-bottom: 1px solid rgba(138,43,226,0.3); display: flex; justify-content: space-between; align-items: center; }
.modal-header.danger { background: linear-gradient(135deg, rgba(255,128,116,0.15), rgba(255,80,60,0.15)); }
.modal-header h3 { margin: 0; color: #ffffff; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; }
.btn-close { background: none; border: none; color: #ffffff; font-size: 1.5rem; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: background .2s ease; }
.btn-close:active { background: rgba(255,255,255,0.1); }
.modal-body { padding: 20px; max-height: calc(90vh - 120px); overflow-y: auto; }

.form-group { margin-bottom: 14px; display: flex; flex-direction: column; gap: 6px; }
.form-group label { color: #f0e1ff; font-size: 0.85rem; font-weight: 600; }
.form-group input, .form-group select, .form-group textarea { padding: 10px 12px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.9rem; font-family: inherit; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: rgba(189,147,255,0.5); background: rgba(255,255,255,0.08); }
.form-group textarea { resize: vertical; }
.checkbox-group { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
.checkbox-group input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }

.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
.btn-cancel { padding: 10px 18px; border: 1px solid rgba(189,147,255,0.35); background: transparent; color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all .2s ease; font-size: 0.9rem; }
.btn-cancel:active { background: rgba(189,147,255,0.1); }
.btn-save { padding: 10px 18px; border: none; background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: all .2s ease; font-size: 0.9rem; }
.btn-save:active { transform: scale(0.95); }
.btn-delete { padding: 10px 18px; border: none; background: linear-gradient(135deg, #ff5040, #ff8074); color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: all .2s ease; font-size: 0.9rem; }
.btn-delete:active { transform: scale(0.95); }
.warning-text { color: #ff8074; font-size: 0.85rem; margin-top: 8px; }
</style>