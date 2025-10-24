<template>
  <div class="mobile-layout">
    <main class="mobile-page">
      <div class="page-header">
        <h1><i class="bi bi-box-seam"></i> Productos</h1>
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" v-model="searchQuery" placeholder="Buscar..." />
        </div>
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
          <div class="kpi-label">Categorías</div>
          <div class="kpi-value">{{ stats.totalCategorias }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Valor</div>
          <div class="kpi-value">{{ stats.valorTotal }}</div>
        </div>
      </section>

      <section class="products-list">
        <div v-if="filteredProducts.length === 0" class="empty">
          <i class="bi bi-inbox"></i>
          <p>No hay productos</p>
        </div>
        <div v-for="producto in filteredProducts" :key="producto.id" class="product-card">
          <div class="product-header">
            <div class="product-info">
              <h3>{{ producto.nombre }}</h3>
              <span class="category">{{ producto.categoria }}</span>
            </div>
            <span class="badge" :class="producto.activo ? 'active' : 'inactive'">
              {{ producto.activo ? 'Activo' : 'Inactivo' }}
            </span>
          </div>
          <div class="product-body">
            <div class="info-row">
              <span class="label">Precio:</span>
              <span class="price">${{ producto.precio.toFixed(2) }}</span>
            </div>
            <div class="info-row">
              <span class="label">Stock:</span>
              <span class="stock" :class="{ 'low': producto.stock < producto.stock_minimo }">
                {{ producto.stock }} unidades
              </span>
            </div>
          </div>
          <div class="product-actions">
            <button class="btn-edit" @click="openModal(producto)">
              <i class="bi bi-pencil"></i> Editar
            </button>
            <button class="btn-delete" @click="confirmDelete(producto)">
              <i class="bi bi-trash"></i> Eliminar
            </button>
          </div>
        </div>
      </section>

      <button class="btn-fab" @click="openModal()">
        <i class="bi bi-plus"></i>
      </button>

      <!-- Modal Crear/Editar -->
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal">
          <div class="modal-header">
            <h3>{{ editMode ? 'Editar Producto' : 'Nuevo Producto' }}</h3>
            <button class="btn-close" @click="closeModal"><i class="bi bi-x"></i></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveProduct">
              <div class="form-group">
                <label>Nombre *</label>
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
                </select>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Precio *</label>
                  <input type="number" step="0.01" v-model.number="form.precio" required />
                </div>
                <div class="form-group">
                  <label>Stock *</label>
                  <input type="number" v-model.number="form.stock" required />
                </div>
              </div>
              <div class="form-group">
                <label>Stock Mínimo *</label>
                <input type="number" v-model.number="form.stock_minimo" required />
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
                  {{ editMode ? 'Actualizar' : 'Guardar' }}
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
            <h3>Confirmar Eliminación</h3>
          </div>
          <div class="modal-body">
            <p>¿Eliminar <strong>{{ productToDelete?.nombre }}</strong>?</p>
            <p class="warning-text">Esta acción no se puede deshacer.</p>
            <div class="modal-actions">
              <button class="btn-cancel" @click="showDeleteModal = false">Cancelar</button>
              <button class="btn-delete-confirm" @click="deleteProduct">Eliminar</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed } from 'vue'

const stats = reactive({ totalProductos: 0, stockBajo: 0, totalCategorias: 0, valorTotal: '$0' })
const productos = ref([])
const searchQuery = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editMode = ref(false)
const productToDelete = ref(null)
const form = reactive({ id: null, nombre: '', categoria: '', precio: 0, stock: 0, stock_minimo: 5, activo: true })

const filteredProducts = computed(() => {
  if (!searchQuery.value) return productos.value
  return productos.value.filter(p => 
    p.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    p.categoria.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const load = () => {
  productos.value = [
    { id: 1, nombre: 'Laptop HP', categoria: 'Electrónica', precio: 850, stock: 15, stock_minimo: 5, activo: true },
    { id: 2, nombre: 'Mouse Logitech', categoria: 'Electrónica', precio: 25.5, stock: 3, stock_minimo: 10, activo: true },
    { id: 3, nombre: 'Teclado Mecánico', categoria: 'Electrónica', precio: 120, stock: 8, stock_minimo: 5, activo: true },
  ]
  updateStats()
}

const updateStats = () => {
  stats.totalProductos = productos.value.length
  stats.stockBajo = productos.value.filter(p => p.stock < p.stock_minimo).length
  stats.totalCategorias = new Set(productos.value.map(p => p.categoria)).size
  stats.valorTotal = '$' + productos.value.reduce((sum, p) => sum + (p.precio * p.stock), 0).toFixed(2)
}

const openModal = (producto = null) => {
  if (producto) {
    editMode.value = true
    Object.assign(form, producto)
  } else {
    editMode.value = false
    Object.assign(form, { id: null, nombre: '', categoria: '', precio: 0, stock: 0, stock_minimo: 5, activo: true })
  }
  showModal.value = true
}

const closeModal = () => { showModal.value = false }

const saveProduct = () => {
  if (editMode.value) {
    const index = productos.value.findIndex(p => p.id === form.id)
    if (index !== -1) productos.value[index] = { ...form }
  } else {
    productos.value.push({ ...form, id: Math.max(...productos.value.map(p => p.id), 0) + 1 })
  }
  updateStats()
  closeModal()
}

const confirmDelete = (producto) => {
  productToDelete.value = producto
  showDeleteModal.value = true
}

const deleteProduct = () => {
  productos.value = productos.value.filter(p => p.id !== productToDelete.value.id)
  updateStats()
  showDeleteModal.value = false
}

onMounted(() => load())
</script>

<style scoped>
.mobile-layout { height: 100vh; position: relative; overflow: hidden; }
.mobile-page { height: 100%; padding: 16px; padding-bottom: 100px; overflow-y: auto; -webkit-overflow-scrolling: touch; }

.page-header { margin-bottom: 16px; }
.page-header h1 { margin: 0 0 12px 0; font-family: 'Orbitron', monospace; font-size: 1.5rem; color: #ffffff; display: flex; align-items: center; gap: 8px; }
.search-box { position: relative; }
.search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #a89bb9; }
.search-box input { width: 100%; padding: 10px 12px 10px 36px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.9rem; }

.kpis { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px; }
.kpi { background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%); border: 2px solid rgba(138,43,226,0.35); border-radius: 12px; padding: 12px; }
.kpi-label { color: #f0e1ff; font-size: 0.7rem; font-weight: 700; margin-bottom: 4px; }
.kpi-value { font-family: 'Orbitron', monospace; font-size: 1.3rem; color: #ffffff; }
.kpi-value.warn { color: #ff8074; }

.products-list { display: flex; flex-direction: column; gap: 12px; }
.product-card { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 14px; padding: 14px; }
.product-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.product-info h3 { margin: 0 0 4px 0; color: #ffffff; font-size: 1rem; }
.category { color: #a89bb9; font-size: 0.8rem; }
.badge { padding: 4px 10px; border-radius: 10px; font-size: 0.7rem; font-weight: 600; }
.badge.active { background: rgba(160,255,143,0.15); color: #a0ff8f; }
.badge.inactive { background: rgba(255,128,116,0.15); color: #ff8074; }

.product-body { margin-bottom: 12px; }
.info-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
.label { color: #a89bb9; font-size: 0.85rem; }
.price { color: #a0ff8f; font-weight: 600; }
.stock { color: #a0ff8f; font-weight: 600; }
.stock.low { color: #ff8074; }

.product-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.btn-edit, .btn-delete { padding: 8px; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; }
.btn-edit { background: rgba(138,43,226,0.2); color: #bd93ff; }
.btn-delete { background: rgba(255,128,116,0.15); color: #ff8074; }

.btn-fab { position: fixed; bottom: 90px; right: 20px; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: none; font-size: 1.5rem; box-shadow: 0 8px 24px rgba(138,43,226,0.35); cursor: pointer; z-index: 100; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.85); display: flex; align-items: flex-end; z-index: 1000; }
.modal { background: linear-gradient(180deg, rgba(18,0,30,0.98), rgba(10,0,20,0.98)); border-radius: 20px 20px 0 0; width: 100%; max-height: 85vh; overflow: hidden; animation: slideUp 0.3s ease; }
.modal-small { max-height: 50vh; align-self: center; border-radius: 20px; margin: 20px; width: calc(100% - 40px); }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }

.modal-header { padding: 18px; border-bottom: 1px solid rgba(138,43,226,0.3); display: flex; justify-content: space-between; align-items: center; }
.modal-header.danger { background: linear-gradient(135deg, rgba(255,128,116,0.15), rgba(255,80,60,0.15)); }
.modal-header h3 { margin: 0; color: #ffffff; font-size: 1.1rem; }
.btn-close { background: none; border: none; color: #ffffff; font-size: 1.8rem; cursor: pointer; }
.modal-body { padding: 20px; max-height: calc(85vh - 80px); overflow-y: auto; }

.form-group { margin-bottom: 16px; }
.form-group label { display: block; color: #f0e1ff; font-size: 0.9rem; font-weight: 600; margin-bottom: 6px; }
.form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.95rem; box-sizing: border-box; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.checkbox-group { display: flex; align-items: center; gap: 8px; }
.checkbox-group input[type="checkbox"] { width: 20px; height: 20px; }

.modal-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px; }
.btn-cancel { padding: 12px; border: 1px solid rgba(189,147,255,0.35); background: transparent; color: #ffffff; border-radius: 8px; font-weight: 600; }
.btn-save { padding: 12px; border: none; background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #ffffff; border-radius: 8px; font-weight: 600; }
.btn-delete-confirm { padding: 12px; border: none; background: linear-gradient(135deg, #ff5040, #ff8074); color: #ffffff; border-radius: 8px; font-weight: 600; }
.warning-text { color: #ff8074; font-size: 0.85rem; margin-top: 8px; }
.empty { text-align: center; padding: 40px 20px; color: #a89bb9; }
.empty i { font-size: 3rem; margin-bottom: 12px; display: block; }
</style>