<template>
  <component :is="currentComponent" v-if="isMobile" />
  <div v-else class="layout">
    <Sidebar />
    <main class="page">
      <div class="page-header">
        <h1><i class="bi bi-file-earmark-bar-graph"></i> Reportes y Análisis</h1>
        <button class="btn-export" @click="exportReport">
          <i class="bi bi-download"></i> Exportar PDF
        </button>
      </div>

      <!-- Filtros -->
      <section class="filters-section">
        <div class="filters-card">
          <div class="filter-group">
            <label>Tipo de Reporte</label>
            <select v-model="selectedReport">
              <option value="ventas">Ventas</option>
              <option value="inventario">Inventario</option>
              <option value="productos">Productos</option>
              <option value="categorias">Categorías</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Fecha Inicio</label>
            <input type="date" v-model="fechaInicio" :max="maxDate" />
          </div>
          <div class="filter-group">
            <label>Fecha Fin</label>
            <input type="date" v-model="fechaFin" :max="maxDate" />
          </div>
          <button class="btn-filter" @click="generateReport">
            <i class="bi bi-funnel"></i> Generar
          </button>
        </div>
      </section>

      <section class="kpis">
        <div class="kpi">
          <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
          <div class="kpi-info">
            <div class="kpi-label">Ventas Totales</div>
            <div class="kpi-value" style="font-size: 1.4rem; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">${{ stats.ventasTotales }}</div>
          </div>
        </div>
        <div class="kpi">
          <div class="kpi-icon"><i class="bi bi-cart-check"></i></div>
          <div class="kpi-info">
            <div class="kpi-label">Pedidos</div>
            <div class="kpi-value" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ stats.totalPedidos }}</div>
          </div>
        </div>
        <div class="kpi">
          <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
          <div class="kpi-info">
            <div class="kpi-label">Productos</div>
            <div class="kpi-value" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ stats.totalProductos }}</div>
          </div>
        </div>
        <div class="kpi">
          <div class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></div>
          <div class="kpi-info">
            <div class="kpi-label">Crecimiento</div>
            <div class="kpi-value" style="font-size: 1.6rem; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">+{{ stats.crecimiento }}%</div>
          </div>
        </div>
      </section>

      <!-- Gráficos -->
      <section class="charts-section">
        <div class="card chart-card">
          <div class="card-header">
            <h3><i class="bi bi-bar-chart"></i> Ventas Mensuales</h3>
          </div>
          <div class="card-body">
            <canvas ref="ventasChart"></canvas>
          </div>
        </div>

        <div class="card chart-card">
          <div class="card-header">
            <h3><i class="bi bi-pie-chart"></i> Distribución por Categoría</h3>
          </div>
          <div class="card-body">
            <canvas ref="categoriasChart"></canvas>
          </div>
        </div>
      </section>

      <section class="grid">
        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-trophy"></i> Productos Más Vendidos</h3>
          </div>
          <div class="card-body list">
            <div v-if="topProductos.length === 0" class="empty">No hay datos</div>
            <div v-for="(p, index) in topProductos" :key="p.id" class="list-item">
              <span class="rank">#{{ index + 1 }}</span>
              <span class="name">{{ p.nombre }}</span>
              <span class="qty">{{ p.ventas }} ventas</span>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-tags"></i> Categorías Populares</h3>
          </div>
          <div class="card-body list">
            <div v-if="topCategorias.length === 0" class="empty">No hay datos</div>
            <div v-for="c in topCategorias" :key="c.id" class="list-item">
              <i class="bi bi-tag"></i>
              <span class="name">{{ c.nombre }}</span>
              <span class="qty">${{ c.ingresos }}</span>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-calendar-week"></i> Ventas por Día</h3>
          </div>
          <div class="card-body list">
            <div v-if="ventasDiarias.length === 0" class="empty">No hay datos</div>
            <div v-for="v in ventasDiarias" :key="v.fecha" class="list-item">
              <i class="bi bi-calendar3"></i>
              <span class="name">{{ v.fecha }}</span>
              <span class="qty">${{ v.total }}</span>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-exclamation-triangle"></i> Alertas de Stock</h3>
          </div>
          <div class="card-body list">
            <div v-if="alertasStock.length === 0" class="empty">Sin alertas</div>
            <div v-for="a in alertasStock" :key="a.id" class="list-item">
              <i class="bi bi-exclamation-circle" style="color: #ff8074;"></i>
              <span class="name">{{ a.producto }}</span>
              <span class="qty warn">{{ a.stock }} unidades</span>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onBeforeUnmount, computed, defineAsyncComponent, nextTick } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import { Chart, registerables } from 'chart.js'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

Chart.register(...registerables)

const MobileReportes = defineAsyncComponent(() => import('./mobile/Reportes.vue'))

const isMobile = ref(window.innerWidth <= 992)
const currentComponent = computed(() => isMobile.value ? MobileReportes : null)
const handleResize = () => { isMobile.value = window.innerWidth <= 992 }

const selectedReport = ref('ventas')
const fechaInicio = ref('')
const fechaFin = ref('')

// Fecha máxima (hoy)
const maxDate = computed(() => {
  const today = new Date()
  return today.toISOString().split('T')[0]
})

const stats = reactive({ 
  ventasTotales: '0', 
  totalPedidos: 0, 
  totalProductos: 0, 
  crecimiento: 0 
})

const topProductos = ref([])
const topCategorias = ref([])
const ventasDiarias = ref([])
const alertasStock = ref([])

const ventasChart = ref(null)
const categoriasChart = ref(null)
let ventasChartInstance = null
let categoriasChartInstance = null

const load = () => {
  // Configurar fechas por defecto (último mes)
  const hoy = new Date()
  const hace30dias = new Date()
  hace30dias.setDate(hoy.getDate() - 30)
  
  fechaFin.value = hoy.toISOString().split('T')[0]
  fechaInicio.value = hace30dias.toISOString().split('T')[0]
  
  // Cargar datos iniciales
  generateReport()
}

const generateReport = () => {
  // Simular datos de reporte
  stats.ventasTotales = '45,890.50'
  stats.totalPedidos = 127
  stats.totalProductos = 45
  stats.crecimiento = 23
  
  topProductos.value = [
    { id: 1, nombre: 'Laptop HP Pavilion', ventas: 45 },
    { id: 2, nombre: 'Mouse Logitech', ventas: 38 },
    { id: 3, nombre: 'Teclado Mecánico', ventas: 32 },
    { id: 4, nombre: 'Monitor Samsung', ventas: 28 },
    { id: 5, nombre: 'Auriculares Sony', ventas: 25 },
  ]
  
  topCategorias.value = [
    { id: 1, nombre: 'Electrónica', ingresos: '28,450' },
    { id: 2, nombre: 'Ropa', ingresos: '12,340' },
    { id: 3, nombre: 'Deportes', ingresos: '5,100' },
  ]
  
  ventasDiarias.value = [
    { fecha: '2024-01-20', total: '2,450' },
    { fecha: '2024-01-19', total: '1,890' },
    { fecha: '2024-01-18', total: '3,120' },
    { fecha: '2024-01-17', total: '2,780' },
    { fecha: '2024-01-16', total: '1,650' },
  ]
  
  alertasStock.value = [
    { id: 1, producto: 'Mouse Logitech', stock: 3 },
    { id: 2, producto: 'Teclado Mecánico', stock: 0 },
    { id: 3, producto: 'Zapatillas Running', stock: 2 },
  ]
  
  nextTick(() => {
    createCharts()
  })
}

const createCharts = () => {
  // Destruir gráficos existentes
  if (ventasChartInstance) ventasChartInstance.destroy()
  if (categoriasChartInstance) categoriasChartInstance.destroy()
  
  // Gráfico de Ventas Mensuales (Barras)
  if (ventasChart.value) {
    const ctx = ventasChart.value.getContext('2d')
    ventasChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [{
          label: 'Ventas ($)',
          data: [12500, 19800, 15600, 22400, 18900, 25300],
          backgroundColor: 'rgba(138, 43, 226, 0.6)',
          borderColor: 'rgba(189, 147, 255, 1)',
          borderWidth: 2,
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: { color: '#ffffff', font: { size: 12 } }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { color: '#a89bb9' },
            grid: { color: 'rgba(189, 147, 255, 0.1)' }
          },
          x: {
            ticks: { color: '#a89bb9' },
            grid: { color: 'rgba(189, 147, 255, 0.1)' }
          }
        }
      }
    })
  }
  
  // Gráfico de Categorías (Dona)
  if (categoriasChart.value) {
    const ctx = categoriasChart.value.getContext('2d')
    categoriasChartInstance = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: topCategorias.value.map(c => c.nombre),
        datasets: [{
          data: topCategorias.value.map(c => parseFloat(c.ingresos.replace(',', ''))),
          backgroundColor: [
            'rgba(138, 43, 226, 0.8)',
            'rgba(189, 147, 255, 0.8)',
            'rgba(139, 92, 246, 0.8)'
          ],
          borderColor: '#1a0033',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: '#ffffff', font: { size: 12 }, padding: 15 }
          }
        }
      }
    })
  }
}

const exportReport = () => {
  const doc = new jsPDF()
  
  // Título
  doc.setFontSize(18)
  doc.setTextColor(75, 0, 130)
  doc.text('Reporte de Análisis', 14, 20)
  
  // Información del reporte
  doc.setFontSize(10)
  doc.setTextColor(100, 100, 100)
  doc.text(`Tipo: ${selectedReport.value.toUpperCase()}`, 14, 28)
  doc.text(`Período: ${fechaInicio.value} - ${fechaFin.value}`, 14, 34)
  doc.text(`Generado: ${new Date().toLocaleDateString()}`, 14, 40)
  
  let yPos = 50
  
  // Tabla 1: Productos Más Vendidos
  doc.setFontSize(14)
  doc.setTextColor(75, 0, 130)
  doc.text('Productos Más Vendidos', 14, yPos)
  
  doc.autoTable({
    startY: yPos + 5,
    head: [['#', 'Producto', 'Ventas']],
    body: topProductos.value.map((p, i) => [i + 1, p.nombre, p.ventas]),
    theme: 'grid',
    headStyles: { fillColor: [75, 0, 130], textColor: 255 },
    styles: { fontSize: 9 },
    margin: { left: 14, right: 14 }
  })
  
  yPos = doc.lastAutoTable.finalY + 15
  
  // Tabla 2: Categorías Populares
  doc.setFontSize(14)
  doc.setTextColor(75, 0, 130)
  doc.text('Categorías Populares', 14, yPos)
  
  doc.autoTable({
    startY: yPos + 5,
    head: [['Categoría', 'Ingresos ($)']],
    body: topCategorias.value.map(c => [c.nombre, c.ingresos]),
    theme: 'grid',
    headStyles: { fillColor: [75, 0, 130], textColor: 255 },
    styles: { fontSize: 9 },
    margin: { left: 14, right: 14 }
  })
  
  yPos = doc.lastAutoTable.finalY + 15
  
  // Tabla 3: Ventas por Día
  if (yPos > 240) {
    doc.addPage()
    yPos = 20
  }
  
  doc.setFontSize(14)
  doc.setTextColor(75, 0, 130)
  doc.text('Ventas por Día', 14, yPos)
  
  doc.autoTable({
    startY: yPos + 5,
    head: [['Fecha', 'Total ($)']],
    body: ventasDiarias.value.map(v => [v.fecha, v.total]),
    theme: 'grid',
    headStyles: { fillColor: [75, 0, 130], textColor: 255 },
    styles: { fontSize: 9 },
    margin: { left: 14, right: 14 }
  })
  
  yPos = doc.lastAutoTable.finalY + 15
  
  // Tabla 4: Alertas de Stock
  if (yPos > 240) {
    doc.addPage()
    yPos = 20
  }
  
  doc.setFontSize(14)
  doc.setTextColor(255, 80, 74)
  doc.text('Alertas de Stock', 14, yPos)
  
  doc.autoTable({
    startY: yPos + 5,
    head: [['Producto', 'Stock Actual']],
    body: alertasStock.value.map(a => [a.producto, `${a.stock} unidades`]),
    theme: 'grid',
    headStyles: { fillColor: [255, 80, 74], textColor: 255 },
    styles: { fontSize: 9 },
    margin: { left: 14, right: 14 }
  })
  
  // Guardar PDF
  doc.save(`reporte_${selectedReport.value}_${new Date().getTime()}.pdf`)
}

onMounted(() => { 
  load()
  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize)
  if (ventasChartInstance) ventasChartInstance.destroy()
  if (categoriasChartInstance) categoriasChartInstance.destroy()
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

.page-header { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.8rem; color: #ffffff; display: flex; align-items: center; gap: 12px; text-shadow: 0 0 8px rgba(255,255,255,.35); }

.kpis, .grid { position: relative; z-index: 1; }
.btn-export { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 24px rgba(138,43,226,0.25); transition: all .3s ease; }
.btn-export:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(138,43,226,0.35); }

.filters-section { position: relative; z-index: 1; margin-bottom: 24px; }
.filters-card { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 20px; display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap; }
.filter-group { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 180px; }
.filter-group label { color: #f0e1ff; font-size: 0.9rem; font-weight: 600; }
.filter-group input, .filter-group select { padding: 10px 14px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.05); border-radius: 8px; color: #ffffff; font-size: 0.9rem; }
.filter-group input:focus, .filter-group select:focus { outline: none; border-color: rgba(189,147,255,0.5); background: rgba(255,255,255,0.08); }
.btn-filter { padding: 10px 20px; background: linear-gradient(135deg, #6B1FA8, #8B5CF6); color: #fff; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all .2s ease; }
.btn-filter:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(138,43,226,0.3); }

.kpis { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(4, minmax(180px, 1fr)); gap: 16px; margin-bottom: 26px; }
.kpi { position: relative; background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 18px; box-shadow: 0 10px 28px rgba(138,43,226,0.12), inset 0 0 18px rgba(138,43,226,0.08); backdrop-filter: blur(4px); overflow: hidden; display: flex; align-items: center; gap: 16px; }
.kpi::before { content: ""; position: absolute; inset: 0; border-radius: 16px; pointer-events: none; background: linear-gradient(120deg, transparent 0%, rgba(189,147,255,.18) 50%, transparent 100%); opacity: .35; }
.kpi-icon { width: 50px; height: 50px; background: linear-gradient(135deg, rgba(138,43,226,0.3), rgba(189,147,255,0.2)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #bd93ff; }
.kpi-info { flex: 1; }
.kpi-label { color: #f0e1ff; font-size: 0.86rem; font-weight: 700; letter-spacing: .3px; }
.kpi-value { 
  font-family: 'Orbitron', monospace; 
  font-size: 2rem; 
  color: #ffffff; 
  text-shadow: 0 0 8px rgba(255,255,255,.35), 0 0 16px rgba(255,255,255,.22); 
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}

.grid { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(2, minmax(300px, 1fr)); gap: 18px; }
.card { position: relative; background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 18px; overflow: hidden; box-shadow: 0 15px 40px rgba(138,43,226,0.15), inset 0 0 24px rgba(138,43,226,0.06); transition: transform .25s ease, box-shadow .25s ease; }
.card::after { content: ""; position: absolute; inset: 0; pointer-events: none; border-radius: 18px; background: conic-gradient(from 0deg, rgba(138,43,226,.08), rgba(189,147,255,.22), rgba(255,0,255,.18), rgba(189,147,255,.22), rgba(138,43,226,.08)); mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); padding: 2px; opacity: .45; filter: blur(.6px); }
.card:hover { transform: translateY(-4px); box-shadow: 0 25px 55px rgba(138,43,226,0.25), inset 0 0 28px rgba(138,43,226,0.08); }
.card-header { padding: 14px 16px; background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10)); border-bottom: 1px solid rgba(138,43,226,0.3); }
.card-header h3 { margin: 0; font-family: 'Space Grotesk', sans-serif; color: #ffffff; font-weight: 700; font-size: 1.08rem; display: flex; align-items: center; gap: 8px; text-shadow: 0 0 6px rgba(255,255,255,.25); }
.card-body { padding: 16px; }

.list { display: flex; flex-direction: column; gap: 10px; }
.list-item { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 8px; padding: 12px; border-radius: 12px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.02); transition: background .2s ease, box-shadow .2s ease, transform .2s ease; }
.list-item:hover { background: rgba(189,147,255,0.06); box-shadow: 0 6px 20px rgba(138,43,226,0.25); transform: translateY(-2px); }
.list-item .name { color: #ffffff; font-weight: 600; }
.list-item .qty { color: #a89bb9; font-size: 0.85rem; }
.list-item i { color: #ffffff; }
.empty { color: #a89bb9; text-align: center; padding: 20px; }

.rank { width: 32px; height: 32px; background: linear-gradient(135deg, #4B0082, #6B1FA8); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #ffffff; font-size: 0.9rem; }
.qty.warn { color: #ff8074; font-weight: 700; }

@media (max-width: 1100px) {
  .layout { grid-template-columns: 80px 1fr; }
  .kpis { grid-template-columns: repeat(2, 1fr); }
  .grid { grid-template-columns: 1fr; }
  .span2 { grid-column: auto; }
  .page-header { flex-direction: column; gap: 16px; align-items: flex-start; }
  .filters-card { flex-direction: column; }
}
</style>