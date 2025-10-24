<template>
  <div class="mobile-layout">
    <main class="mobile-page">
      <div class="page-header">
        <h1><i class="bi bi-box-seam"></i> Inventario</h1>
        <button class="btn-add" @click="openModal()">
          <i class="bi bi-plus-circle"></i> Nuevo
        </button>
      </div>

      <section class="kpis">
        <div class="kpi">
          <div class="kpi-label">Total</div>
          <div class="kpi-value">{{ stats.totalProductos }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Stock Bajo</div>
          <div class="kpi-value warn">{{ stats.stockBajo }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Sin Stock</div>
          <div class="kpi-value warn">{{ stats.sinStock }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Valor</div>
          <div class="kpi-value" style="font-size: 1rem;">${{ stats.valorTotal }}</div>
        </div>
      </section>

      <section class="table-section">
        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-box"></i> Productos</h3>
            <div class="search-box">
              <i class="bi bi-search"></i>
              <input type="text" v-model="searchQuery" placeholder="Buscar..." />
            </div>
          </div>
          <div class="card-body">
            <div class="mobile-list">
              <div v-if="filteredInventario.length === 0" class="empty">No hay productos</div>
              <div v-for="item in filteredInventario" :key="item.id" class="list-item">
                <div class="item-header">
                  <div class="item-title">
                    <i class="bi bi-box"></i>
                    <span>{{ item.nombre }}</span>
                  </div>
                  <span class="badge" :class="item.activo ? 'active' : 'inactive'">
                    {{ item.activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
                <div class="item-info">
                  <div class="info-row">
                    <span class="label">Categoría:</span>
                    <span class="value">{{ item.categoria }}</span>
                  </div>
                  <div class="info-row">
                    <span class="label">Stock:</span>
                    <span class="stock" :class="getStockClass(item)">{{ item.stockActual }} / {{ item.stockMinimo }}</span>
                  </div>
                  <div class="info-row">
                    <span class="label">Precio:</span>
                    <span class="price">${{ item.precio }}</span>
                  </div>
                </div>
                <div class="item-actions">
                  <button class="btn-icon edit" @click="openModal(item)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-icon delete" @click="confirmDelete(item)">
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
              {{ editMode ? 'Editar' : 'Nuevo' }}
            </h3>
            <button class="btn-close" @click="closeModal"><i class="bi bi-x"></i></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveItem">
              <div class="form-group">
                <label>Nombre del Producto *</label>
                <input type="text" v-model="form.nombre" required />
              </div>
              <div class="form-group">
                <label>Categoría *</label>
                <select v-model="form.categoria" required>
                  <option value="">Seleccionar...</option>
                  <option value="Electrónica">Electrónica</option>
                  <option value="Ropa">Ropa</option>
                  <option value="Alimentos">Alimentos</option>
                  <option value="Hogar">Hogar</option>
                  <option value="Deportes">Deportes</option>
                </select>
              </div>
              <div class="form-group">
                <label>Stock Actual *</label>
                <input type="number" v-model.number="form.stockActual" required min="0" />
              </div>
              <div class="form-group">
                <label>Stock Mínimo *</label>
                <input type="number" v-model.number="form.stockMinimo" required min="0" />
              </div>
              <div class="form-group">
                <label>Precio *</label>
                <input type="number" v-model.number="form.precio" required min="0" step="0.01" />
              </div>
              <div class="form-group">
                <div class="checkbox-group">
                  <input type="checkbox" id="activo" v-model="form.activo" />
                  <label for="activo">Producto Activo</label>
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
            <p>¿Eliminar <strong>{{ itemToDelete?.nombre }}</strong>?</p>
            <p class="warning-text">Esta acción no se puede deshacer.</p>
            <div class="modal-actions">
              <button class="btn-cancel" @click="showDeleteModal = false">Cancelar</button>
              <button class="btn-delete" @click="deleteItem">
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
import { reactive, ref, onMounted, computed } from 'vue'

const stats = reactive({ totalProductos: 0, stockBajo: 0, sinStock: 0, valorTotal: '0' })
const inventario = ref([])
const searchQuery = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editMode = ref(false)
const itemToDelete = ref(null)
const form = reactive({ id: null, nombre: '', categoria: '', stockActual: 0, stockMinimo: 0, precio: 0, activo: true })

const filteredInventario = computed(() => {
  if (!searchQuery.value) return inventario.value
  return inventario.value.filter(item => 
    item.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    item.categoria.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const getStockClass = (item) => {
  if (item.stockActual === 0) return 'out'
  if (item.stockActual <= item.stockMinimo) return 'low'
  return 'ok'
}

const load = () => {
  inventario.value = [
    { id: 1, nombre: 'Laptop HP Pavilion', categoria: 'Electrónica', stockActual: 15, stockMinimo: 5, precio: 899.99, activo: true },
    { id: 2, nombre: 'Mouse Logitech', categoria: 'Electrónica', stockActual: 3, stockMinimo: 10, precio: 29.99, activo: true },
    { id: 3, nombre: 'Teclado Mecánico', categoria: 'Electrónica', stockActual: 0, stockMinimo: 5, precio: 79.99, activo: true },
    { id: 4, nombre: 'Monitor Samsung 24"', categoria: 'Electrónica', stockActual: 8, stockMinimo: 3, precio: 199.99, activo: true },
    { id: 5, nombre: 'Camiseta Deportiva', categoria: 'Ropa', stockActual: 50, stockMinimo: 20, precio: 24.99, activo: true },
    { id: 6, nombre: 'Zapatillas Running', categoria: 'Deportes', stockActual: 2, stockMinimo: 8, precio: 89.99, activo: true },
  ]
  updateStats()
}

const updateStats = () => {
  stats.totalProductos = inventario.value.length
  stats.stockBajo = inventario.value.filter(i => i.stockActual > 0 && i.stockActual <= i.stockMinimo).length
  stats.sinStock = inventario.value.filter(i => i.stockActual === 0).length
  stats.valorTotal = inventario.value.reduce((sum, i) => sum + (i.stockActual * i.precio), 0).toFixed(2)
}

const openModal = (item = null) => {
  if (item) {
    editMode.value = true
    Object.assign(form, { ...item })
  } else {
    editMode.value = false
    Object.assign(form, { id: null, nombre: '', categoria: '', stockActual: 0, stockMinimo: 0, precio: 0, activo: true })
  }
  showModal.value = true
}

const closeModal = () => { showModal.value = false }

const saveItem = () => {
  if (editMode.value) {
    const index = inventario.value.findIndex(i => i.id === form.id)
    if (index !== -1) {
      inventario.value[index] = { ...form }
    }
  } else {
    inventario.value.push({ ...form, id: Math.max(...inventario.value.map(i => i.id), 0) + 1 })
  }
  updateStats()
  closeModal()
}

const confirmDelete = (item) => {
  itemToDelete.value = item
  showDeleteModal.value = true
}

const deleteItem = () => {
  inventario.value = inventario.value.filter(i => i.id !== itemToDelete.value.id)
  updateStats()
  showDeleteModal.value = false
}

onMounted(() => load())
</script>

<style scoped>
.mobile-layout { min-height: 100vh; background: linear-gradient(135deg, #0a0015 0%, #1a0030 50%, #0a0015 100%); padding-bottom: 80px; }
.mobile-page { padding: 16px; }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.page-header h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.4rem; color: #ffffff; display: flex; align-items: center; gap: 8px; }
.btn-add { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: none; padding: 10px 16px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 0.9rem; }

.kpis { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px; }
.kpi { background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%); border: 2px solid rgba(138,43,226,0.35); border-radius: 12px; padding: 14px; }
.kpi-label { color: #f0e1ff; font-size: 0.8rem; font-weight: 700; }
.kpi-value { font-family: 'Orbitron', monospace; font-size: 1.6rem; color: #ffffff; margin-top: 4px; }
.kpi-value.warn { color: #ff8074; }

.table-section { margin-top: 20px; }
.card { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; overflow: hidden; }
.card-header { padding: 14px; background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10)); border-bottom: 1px solid rgba(138,43,226,0.3); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
.card-header h3 { margin: 0; color: #ffffff; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; }
.card-body { padding: 14px; }

.search-box { position: relative; display: flex; align-items: center; flex: 1; min-width: 150px; }
.search-box i { position: absolute; left: 10px; color: #a89bb9; font-size: 0.9rem; }
.search-box input { width: 100%; padding: 8px 10px 8px 32px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.85rem; }
.search-box input:focus { outline: none; border-color: rgba(189,147,255,0.5); }

.mobile-list { display: flex; flex-direction: column; gap: 12px; }
.list-item { background: rgba(255,255,255,0.02); border: 1px solid rgba(189,147,255,0.25); border-radius: 12px; padding: 14px; }
.item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.item-title { display: flex; align-items: center; gap: 8px; color: #ffffff; font-weight: 600; font-size: 1rem; }
.item-title i { color: #bd93ff; }
.badge { padding: 4px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 600; }
.badge.active { background: rgba(160,255,143,0.15); color: #a0ff8f; }
.badge.inactive { background: rgba(255,128,116,0.15); color: #ff8074; }

.item-info { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
.info-row { display: flex; justify-content: space-between; align-items: center; }
.info-row .label { color: #bd93ff; font-size: 0.85rem; font-weight: 600; }
.info-row .value { color: #ffffff; font-size: 0.85rem; }
.stock { padding: 3px 8px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; }
.stock.ok { background: rgba(160,255,143,0.15); color: #a0ff8f; }
.stock.low { background: rgba(255,200,87,0.15); color: #ffc857; }
.stock.out { background: rgba(255,128,116,0.15); color: #ff8074; }
.price { color: #a0ff8f; font-weight: 600; font-size: 0.9rem; }

.item-actions { display: flex; gap: 8px; justify-content: flex-end; }
.btn-icon { width: 36px; height: 36px; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.btn-icon.edit { background: rgba(138,43,226,0.2); color: #bd93ff; }
.btn-icon.delete { background: rgba(255,128,116,0.15); color: #ff8074; }

.empty { color: #a89bb9; text-align: center; padding: 30px; font-size: 0.9rem; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px; }
.modal { background: linear-gradient(180deg, rgba(18,0,30,0.98), rgba(10,0,20,0.98)); border: 2px solid rgba(138,43,226,0.4); border-radius: 16px; width: 100%; max-width: 500px; max-height: 90vh; overflow: hidden; }
.modal-small { max-width: 400px; }
.modal-header { padding: 16px; background: linear-gradient(135deg, rgba(189,147,255,0.15), rgba(138,43,226,0.15)); border-bottom: 1px solid rgba(138,43,226,0.3); display: flex; justify-content: space-between; align-items: center; }
.modal-header.danger { background: linear-gradient(135deg, rgba(255,128,116,0.15), rgba(255,80,60,0.15)); }
.modal-header h3 { margin: 0; color: #ffffff; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; }
.btn-close { background: none; border: none; color: #ffffff; font-size: 1.3rem; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
.modal-body { padding: 16px; max-height: calc(90vh - 120px); overflow-y: auto; }

.form-group { margin-bottom: 14px; }
.form-group label { display: block; color: #f0e1ff; font-size: 0.9rem; font-weight: 600; margin-bottom: 6px; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.9rem; box-sizing: border-box; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: rgba(189,147,255,0.5); }
.checkbox-group { display: flex; align-items: center; gap: 8px; }
.checkbox-group input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }

.modal-actions { display: flex; gap: 10px; margin-top: 16px; }
.btn-cancel { flex: 1; padding: 10px; border: 1px solid rgba(189,147,255,0.35); background: transparent; color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; }
.btn-save { flex: 1; padding: 10px; border: none; background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; }
.btn-delete { flex: 1; padding: 10px; border: none; background: linear-gradient(135deg, #ff5040, #ff8074); color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; }
.warning-text { color: #ff8074; font-size: 0.85rem; margin-top: 8px; }
</style>
