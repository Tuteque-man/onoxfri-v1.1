<template>
  <div class="dashboard-container">
    <!-- Contenido principal (Sidebar ahora está en App.vue) -->
    <div class="main-content">
      <div class="dashboard-header">
        <div class="dashboard-title">
          <i class="fas fa-tachometer-alt"></i>
          Dashboard Principal
        </div>
        <div class="dashboard-actions">
          <button class="btn-ghost-futuristic" @click="refreshData">
            <i class="fas fa-sync-alt"></i>
            Actualizar
          </button>
          <button class="btn-futuristic" @click="showNotifications = true">
            <i class="fas fa-bell"></i>
            Notificaciones
          </button>
        </div>
      </div>

      <!-- Tarjetas de estadísticas -->
      <div class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-value">{{ stats.totalProductos || 0 }}</div>
          <div class="stat-label">Productos Totales</div>
        </div>

        <div class="stat-card">
          <div class="stat-value">{{ stats.stockBajo || 0 }}</div>
          <div class="stat-label">Stock Bajo</div>
        </div>

        <div class="stat-card">
          <div class="stat-value">{{ stats.totalEmpresas || 0 }}</div>
          <div class="stat-label">Empresas</div>
        </div>

        <div class="stat-card">
          <div class="stat-value">{{ stats.valorInventario || '$0' }}</div>
          <div class="stat-label">Valor Inventario</div>
        </div>
      </div>

      <!-- Gráficos y widgets -->
      <div class="dashboard-widgets">
        <!-- Gráfico de productos por categoría -->
        <div class="widget-card">
          <div class="widget-header">
            <h3 class="widget-title">
              <i class="fas fa-chart-pie"></i>
              Productos por Categoría
            </h3>
          </div>
          <div class="widget-content">
            <div v-if="categoriaData.length > 0" class="chart-container">
              <canvas ref="categoriaChart"></canvas>
            </div>
            <div v-else class="no-data">
              <i class="fas fa-chart-bar"></i>
              <p>No hay datos para mostrar</p>
            </div>
          </div>
        </div>

        <!-- Productos con stock bajo -->
        <div class="widget-card">
          <div class="widget-header">
            <h3 class="widget-title">
              <i class="fas fa-exclamation-triangle"></i>
              Alertas de Stock
            </h3>
            <router-link to="/productos" class="widget-action">
              Ver Todos
            </router-link>
          </div>
          <div class="widget-content">
            <div v-if="productosStockBajo.length > 0" class="alerts-list">
              <div
                v-for="producto in productosStockBajo.slice(0, 5)"
                :key="producto.id"
                class="alert-item"
              >
                <div class="alert-icon">
                  <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-info">
                  <div class="alert-name">{{ producto.name }}</div>
                  <div class="alert-detail">Stock: {{ producto.stock_actual }} / Mín: {{ producto.min_stock }}</div>
                </div>
              </div>
            </div>
            <div v-else class="no-data">
              <i class="fas fa-check-circle"></i>
              <p>Todos los productos tienen stock adecuado</p>
            </div>
          </div>
        </div>

        <!-- Actividad reciente -->
        <div class="widget-card">
          <div class="widget-header">
            <h3 class="widget-title">
              <i class="fas fa-clock"></i>
              Actividad Reciente
            </h3>
          </div>
          <div class="widget-content">
            <div class="activity-list">
              <div
                v-for="(activity, index) in recentActivity"
                :key="index"
                class="activity-item"
              >
                <div class="activity-icon">
                  <i :class="activity.icon"></i>
                </div>
                <div class="activity-info">
                  <div class="activity-text">{{ activity.text }}</div>
                  <div class="activity-time">{{ activity.time }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de notificaciones -->
    <div v-if="showNotifications" class="modal-overlay" @click="showNotifications = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Notificaciones</h3>
          <button class="modal-close" @click="showNotifications = false">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>No hay notificaciones nuevas</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'

// Estado reactivo
const isLoading = ref(false)
const showNotifications = ref(false)
const stats = reactive({
  totalProductos: 0,
  stockBajo: 0,
  totalEmpresas: 0,
  valorInventario: '$0'
})

const productosStockBajo = ref([])
const categoriaData = ref([])
const recentActivity = ref([
  {
    icon: 'fas fa-plus-circle',
    text: 'Nuevo producto agregado',
    time: 'Hace 2 horas'
  },
  {
    icon: 'fas fa-edit',
    text: 'Producto actualizado',
    time: 'Hace 4 horas'
  },
  {
    icon: 'fas fa-minus-circle',
    text: 'Stock reducido',
    time: 'Hace 6 horas'
  }
])

// Datos simulados para demostración
const loadDashboardData = () => {
  // Datos simulados para demostración
  stats.totalProductos = 45
  stats.stockBajo = 3
  stats.totalEmpresas = 2
  stats.valorInventario = '$12,450'

  productosStockBajo.value = [
    { id: 1, name: 'Producto A', stock_actual: 2, min_stock: 5 },
    { id: 2, name: 'Producto B', stock_actual: 1, min_stock: 3 },
    { id: 3, name: 'Producto C', stock_actual: 0, min_stock: 2 }
  ]

  categoriaData.value = [
    { categoria: 'Electrónicos', cantidad: 25 },
    { categoria: 'Ropa', cantidad: 18 },
    { categoria: 'Libros', cantidad: 12 },
    { categoria: 'Otros', cantidad: 8 }
  ]
}

// Métodos
const refreshData = () => {
  isLoading.value = true
  setTimeout(() => {
    loadDashboardData()
    isLoading.value = false
  }, 1000)
}

// Lifecycle
onMounted(() => {
  loadDashboardData()
})
</script>

<style scoped>
/* =====================================================
   DASHBOARD - ESTILOS CORREGIDOS PARA NUEVO LAYOUT
   ===================================================== */

.dashboard-container {
  width: 100%;
  min-height: 100vh;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1.5rem 0;
  border-bottom: 2px solid rgba(75, 0, 130, 0.1);

  .dashboard-title {
    font-family: 'Orbitron', monospace;
    font-size: 2rem;
    font-weight: 700;
    color: var(--accent-primary);
    display: flex;
    align-items: center;
    margin: 0;

    i {
      margin-right: 1rem;
      font-size: 2.25rem;
    }
  }

  .dashboard-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
}

/* Estadísticas */
.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: var(--bg-secondary);
  border: 2px solid rgba(75, 0, 130, 0.2);
  border-radius: 16px;
  padding: 2rem 1.5rem;
  text-align: center;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4B0082, #6B1FA8, #8A2BE2);
  }

  &:hover {
    transform: translateY(-5px);
    border-color: var(--accent-primary);
    box-shadow: 0 15px 35px rgba(75, 0, 130, 0.15);
  }

  .stat-value {
    font-family: 'Orbitron', monospace;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-primary);
    margin-bottom: 0.5rem;
    line-height: 1;
  }

  .stat-label {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 0.875rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
  }
}

/* Widgets del dashboard */
.dashboard-widgets {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 2rem;
}

.widget-card {
  background: var(--bg-secondary);
  border: 2px solid rgba(75, 0, 130, 0.2);
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s ease;
  position: relative;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4B0082, #6B1FA8);
  }

  &:hover {
    border-color: var(--accent-primary);
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(75, 0, 130, 0.1);
  }
}

.widget-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, rgba(75, 0, 130, 0.05), rgba(107, 31, 168, 0.05));

  .widget-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--accent-primary);
    margin: 0;
    display: flex;
    align-items: center;

    i {
      margin-right: 0.75rem;
      font-size: 1.25rem;
    }
  }

  .widget-action {
    color: var(--accent-primary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 1px solid transparent;

    &:hover {
      background: rgba(75, 0, 130, 0.1);
      border-color: rgba(75, 0, 130, 0.2);
      color: var(--accent-secondary);
    }
  }
}

.widget-content {
  padding: 1.5rem;

  .chart-container {
    height: 300px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .no-data {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-secondary);

    i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: var(--accent-primary);
    }

    p {
      margin: 0;
      font-size: 0.875rem;
    }
  }

  .alerts-list {
    max-height: 300px;
    overflow-y: auto;
  }

  .alert-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid rgba(75, 0, 130, 0.1);
    transition: background-color 0.3s ease;

    &:hover {
      background: rgba(75, 0, 130, 0.05);
    }

    &:last-child {
      border-bottom: none;
    }
  }

  .alert-icon {
    width: 40px;
    height: 40px;
    background: #ffc107;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
  }

  .alert-info {
    flex: 1;

    .alert-name {
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.25rem;
    }

    .alert-detail {
      font-size: 0.875rem;
      color: var(--text-secondary);
    }
  }

  .activity-list {
    .activity-item {
      display: flex;
      align-items: center;
      padding: 1rem;
      border-bottom: 1px solid rgba(75, 0, 130, 0.1);

      &:last-child {
        border-bottom: none;
      }
    }

    .activity-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #4B0082, #6B1FA8);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      margin-right: 1rem;
      flex-shrink: 0;
    }

    .activity-info {
      flex: 1;

      .activity-text {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
      }

      .activity-time {
        font-size: 0.875rem;
        color: var(--text-secondary);
      }
    }
  }
}

/* Modal de notificaciones */
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
  border: 2px solid rgba(75, 0, 130, 0.2);
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
    color: var(--accent-primary);
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.25rem;
  }
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: all 0.3s ease;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;

  &:hover {
    background: rgba(75, 0, 130, 0.1);
    color: var(--accent-primary);
  }
}

.modal-body {
  padding: 1.5rem;
  text-align: center;
  color: var(--text-secondary);
}

/* =====================================================
   RESPONSIVE DESIGN
   ===================================================== */

@media (max-width: 1200px) {
  .dashboard-widgets {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 992px) {
  .dashboard-header {
    flex-direction: column;
    align-items: stretch;
    gap: 1.5rem;
    text-align: center;
  }

  .dashboard-title {
    justify-content: center;
  }

  .dashboard-actions {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: 1rem;
  }

  .dashboard-header {
    margin-bottom: 1.5rem;
    padding: 1rem 0;
  }

  .dashboard-title {
    font-size: 1.75rem;

    i {
      font-size: 2rem;
    }
  }

  .dashboard-stats {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .stat-card {
    padding: 1.5rem 1rem;

    .stat-value {
      font-size: 2rem;
    }
  }

  .dashboard-widgets {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

@media (max-width: 480px) {
  .dashboard-header {
    text-align: center;
  }

  .dashboard-title {
    font-size: 1.5rem;
    flex-direction: column;
    gap: 0.5rem;

    i {
      font-size: 1.75rem;
      margin-right: 0;
    }
  }

  .dashboard-actions {
    flex-direction: column;
    gap: 0.75rem;
  }

  .dashboard-stats {
    grid-template-columns: 1fr;
  }
}
</style>