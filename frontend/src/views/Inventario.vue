<template>
  <component :is="currentComponent" v-if="isMobile" />
  <div v-else class="layout">
    <Sidebar />
    <main class="page">
      <div class="page-header">
        <h1><i class="bi bi-box-seam"></i> Gestión de Inventario</h1>
        <div class="header-actions">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" v-model="searchQuery" placeholder="Buscar producto..." />
          </div>
          <button class="btn-add" @click="openModal()">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
          </button>
        </div>
      </div>

      <section class="kpis">
        <div class="kpi">
          <div class="kpi-label">Total Productos</div>
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
          <div class="kpi-label">Valor Total</div>
          <div class="kpi-value" style="font-size: 1.4rem;">${{ stats.valorTotal }}</div>
        </div>
      </section>

      <section class="table-section">
        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-table"></i> Lista de Inventario</h3>
          </div>
          <div class="card-body">
            <div class="table-container">
              <table class="products-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="filteredInventario.length === 0">
                    <td colspan="8" class="empty">No hay productos en inventario</td>
                  </tr>
                  <tr v-for="item in filteredInventario" :key="item.id">
                    <td>{{ item.id }}</td>
                    <td class="product-name">
                      <i class="bi bi-box"></i>
                      {{ item.nombre }}
                    </td>
                    <td>{{ item.categoria }}</td>
                    <td>
                      <span class="stock" :class="getStockClass(item)">{{ item.stockActual }}</span>
                    </td>
                    <td>{{ item.stockMinimo }}</td>
                    <td class="price">${{ item.precio }}</td>
                    <td>
                      <span class="badge" :class="item.activo ? 'active' : 'inactive'">
                        {{ item.activo ? 'Activo' : 'Inactivo' }}
                      </span>
                    </td>
                    <td class="actions">
                      <button class="btn-icon edit" @click="openModal(item)" title="Editar">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button class="btn-icon delete" @click="confirmDelete(item)" title="Eliminar">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
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
              {{ editMode ? 'Editar Producto' : 'Nuevo Producto' }}
            </h3>
            <button class="btn-close" @click="closeModal"><i class="bi bi-x"></i></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveItem">
              <div class="form-row">
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
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Stock Actual *</label>
                  <input type="number" v-model.number="form.stockActual" required min="0" />
                </div>
                <div class="form-group">
                  <label>Stock Mínimo *</label>
                  <input type="number" v-model.number="form.stockMinimo" required min="0" />
                </div>
              </div>
              <div class="form-row">
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
            <h3><i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación</h3>
          </div>
          <div class="modal-body">
            <p>¿Eliminar <strong>{{ itemToDelete?.nombre }}</strong> del inventario?</p>
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
import { reactive, ref, onMounted, onBeforeUnmount, computed, defineAsyncComponent } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const MobileInventario = defineAsyncComponent(() => import('./mobile/Inventario.vue'))

const isMobile = ref(window.innerWidth <= 992)
const currentComponent = computed(() => isMobile.value ? MobileInventario : null)
const handleResize = () => { isMobile.value = window.innerWidth <= 992 }

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

onMounted(() => { load(); window.addEventListener('resize', handleResize) })
onBeforeUnmount(() => window.removeEventListener('resize', handleResize))
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

.kpis { display: grid; grid-template-columns: repeat(4, minmax(180px, 1fr)); gap: 16px; margin-bottom: 26px; }
.kpi { position: relative; background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 18px; box-shadow: 0 10px 28px rgba(138,43,226,0.12), inset 0 0 18px rgba(138,43,226,0.08); backdrop-filter: blur(4px); overflow: hidden; }
.kpi::before { content: ""; position: absolute; inset: 0; border-radius: 16px; pointer-events: none; background: linear-gradient(120deg, transparent 0%, rgba(189,147,255,.18) 50%, transparent 100%); opacity: .35; }
.kpi-label { color: #f0e1ff; font-size: 0.86rem; font-weight: 700; letter-spacing: .3px; }
.kpi-value { font-family: 'Orbitron', monospace; font-size: 2rem; color: #ffffff; text-shadow: 0 0 8px rgba(255,255,255,.35), 0 0 16px rgba(255,255,255,.22); }
.kpi-value.warn { color: #ff8074; }

.grid { display: grid; grid-template-columns: repeat(2, minmax(300px, 1fr)); gap: 18px; }
.card { position: relative; background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 18px; overflow: hidden; box-shadow: 0 15px 40px rgba(138,43,226,0.15), inset 0 0 24px rgba(138,43,226,0.06); transition: transform .25s ease, box-shadow .25s ease; animation: neon-pulse 8s infinite alternate; }
.card::after { content: ""; position: absolute; inset: 0; pointer-events: none; border-radius: 18px; background: conic-gradient(from 0deg, rgba(138,43,226,.08), rgba(189,147,255,.22), rgba(255,0,255,.18), rgba(189,147,255,.22), rgba(138,43,226,.08)); mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); padding: 2px; opacity: .45; filter: blur(.6px); }
.card:hover { transform: translateY(-4px); box-shadow: 0 25px 55px rgba(138,43,226,0.25), inset 0 0 28px rgba(138,43,226,0.08); }
.card-header { padding: 14px 16px; background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10)); border-bottom: 1px solid rgba(138,43,226,0.3); }
.card-header h3 { margin: 0; font-family: 'Space Grotesk', sans-serif; color: #ffffff; font-weight: 700; font-size: 1.08rem; display: flex; align-items: center; gap: 8px; text-shadow: 0 0 6px rgba(255,255,255,.25); }
.card-body { padding: 16px; }
.span2 { grid-column: span 2; }

.chart-placeholder { height: 280px; display: grid; place-items: center; color: #cfc3ff; border: 1px dashed rgba(189,147,255,0.35); border-radius: 12px; box-shadow: inset 0 0 20px rgba(138,43,226,0.1); text-shadow: 0 0 6px rgba(189,147,255,.6); }

.list { display: flex; flex-direction: column; gap: 10px; }
.list-item { display: grid; grid-template-columns: 20px 1fr auto; align-items: center; gap: 8px; padding: 12px; border-radius: 12px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.02); transition: background .2s ease, box-shadow .2s ease, transform .2s ease; }
.list-item:hover { background: rgba(189,147,255,0.06); box-shadow: 0 6px 20px rgba(138,43,226,0.25); transform: translateY(-2px); }
.list-item .name { color: #ffffff; font-weight: 600; }
.list-item .qty, .list-item .time { color: #a89bb9; font-size: 0.85rem; }
.empty { color: #a89bb9; text-align: center; padding: 20px; }
.list-item i { color: #ffffff; }

@keyframes neon-pulse {
  from { box-shadow: 0 15px 40px rgba(138,43,226,0.15), inset 0 0 24px rgba(138,43,226,0.06); }
  to { box-shadow: 0 15px 45px rgba(138,43,226,0.22), inset 0 0 28px rgba(138,43,226,0.09); }
}

:where(.card-header h3, .kpi-value, .list-item .name, .list-item i) {
  color: #ffffff !important;
  text-shadow: 0 0 6px rgba(255,255,255,0.28), 0 0 14px rgba(255,255,255,0.18);
}

.page-header { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; width: 100%; max-width: 100%; box-sizing: border-box; }
.page-header h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.8rem; color: #ffffff; display: flex; align-items: center; gap: 12px; text-shadow: 0 0 8px rgba(255,255,255,.35); }

.header-actions { display: flex; align-items: center; gap: 12px; }

.btn-add { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 24px rgba(138,43,226,0.25); transition: all .3s ease; }
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(138,43,226,0.35); }

.table-section { position: relative; z-index: 1; margin-top: 20px; width: 100%; max-width: 100%; overflow: hidden; }
.search-box { position: relative; display: flex; align-items: center; }
.search-box i { position: absolute; left: 12px; color: #a89bb9; }
.search-box input { padding: 8px 12px 8px 36px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.9rem; width: 250px; }
.search-box input:focus { outline: none; border-color: rgba(189,147,255,0.5); background: rgba(255,255,255,0.08); }

.table-container { overflow-x: auto; max-width: 100%; }
.products-table { width: 100%; border-collapse: collapse; }
.products-table thead { background: rgba(138,43,226,0.15); }
.products-table th { padding: 14px; text-align: left; color: #f0e1ff; font-weight: 700; font-size: 0.9rem; border-bottom: 2px solid rgba(138,43,226,0.3); }
.products-table tbody tr { border-bottom: 1px solid rgba(189,147,255,0.15); transition: background .2s ease; }
.products-table tbody tr:hover { background: rgba(189,147,255,0.08); }
.products-table td { padding: 14px; color: #ffffff; font-size: 0.9rem; }
.product-name { display: flex; align-items: center; gap: 8px; font-weight: 600; }
.stock { padding: 4px 10px; border-radius: 6px; font-weight: 600; }
.stock.ok { background: rgba(160,255,143,0.15); color: #a0ff8f; }
.stock.low { background: rgba(255,200,87,0.15); color: #ffc857; }
.stock.out { background: rgba(255,128,116,0.15); color: #ff8074; }
.price { color: #a0ff8f; font-weight: 600; }
.badge { padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
.badge.active { background: rgba(160,255,143,0.15); color: #a0ff8f; }
.badge.inactive { background: rgba(255,128,116,0.15); color: #ff8074; }

.actions { display: flex; gap: 8px; }
.btn-icon { width: 32px; height: 32px; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s ease; }
.btn-icon.edit { background: rgba(138,43,226,0.2); color: #bd93ff; }
.btn-icon.edit:hover { background: rgba(138,43,226,0.35); transform: scale(1.1); }
.btn-icon.delete { background: rgba(255,128,116,0.15); color: #ff8074; }
.btn-icon.delete:hover { background: rgba(255,128,116,0.25); transform: scale(1.1); }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.75); display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
.modal { background: linear-gradient(180deg, rgba(18,0,30,0.95), rgba(10,0,20,0.95)); border: 2px solid rgba(138,43,226,0.35); border-radius: 18px; width: 90%; max-width: 600px; max-height: 90vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
.modal-small { max-width: 450px; }
.modal-header { padding: 20px; background: linear-gradient(135deg, rgba(189,147,255,0.15), rgba(138,43,226,0.15)); border-bottom: 1px solid rgba(138,43,226,0.3); display: flex; justify-content: space-between; align-items: center; }
.modal-header.danger { background: linear-gradient(135deg, rgba(255,128,116,0.15), rgba(255,80,60,0.15)); }
.modal-header h3 { margin: 0; color: #ffffff; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
.btn-close { background: none; border: none; color: #ffffff; font-size: 1.5rem; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: background .2s ease; }
.btn-close:hover { background: rgba(255,255,255,0.1); }
.modal-body { padding: 24px; max-height: calc(90vh - 140px); overflow-y: auto; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px; }
.form-group label { color: #f0e1ff; font-size: 0.9rem; font-weight: 600; }
.form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.9rem; font-family: inherit; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: rgba(189,147,255,0.5); background: rgba(255,255,255,0.08); }
.form-group textarea { resize: vertical; }
.checkbox-group { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
.checkbox-group input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }

.modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; }
.btn-cancel { padding: 10px 20px; border: 1px solid rgba(189,147,255,0.35); background: transparent; color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all .2s ease; }
.btn-cancel:hover { background: rgba(189,147,255,0.1); }
.btn-save { padding: 10px 20px; border: none; background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all .2s ease; }
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(138,43,226,0.35); }
.btn-delete { padding: 10px 20px; border: none; background: linear-gradient(135deg, #ff5040, #ff8074); color: #ffffff; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all .2s ease; }
.btn-delete:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(255,80,64,0.35); }
.warning-text { color: #ff8074; font-size: 0.9rem; margin-top: 8px; }

@media (max-width: 1100px) {
  .layout { grid-template-columns: 80px 1fr; }
  .kpis { grid-template-columns: repeat(2, 1fr); }
  .grid { grid-template-columns: 1fr; }
  .span2 { grid-column: auto; }
}
</style>