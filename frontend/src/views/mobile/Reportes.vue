<template>
  <div class="mobile-page">
    <header class="mobile-header">
      <button class="back-btn" @click="$router.push('/dashboard')">
        <i class="bi bi-arrow-left"></i>
      </button>
      <h1><i class="bi bi-file-earmark-bar-graph"></i> Reportes</h1>
      <button class="export-btn" @click="exportReport">
        <i class="bi bi-download"></i>
      </button>
    </header>

    <!-- Filtros -->
    <section class="filters-section">
      <div class="filter-row">
        <select v-model="selectedReport" class="filter-select">
          <option value="ventas">Ventas</option>
          <option value="inventario">Inventario</option>
          <option value="productos">Productos</option>
          <option value="categorias">Categorías</option>
        </select>
      </div>
      <div class="filter-row">
        <input type="date" v-model="fechaInicio" class="filter-input" />
        <input type="date" v-model="fechaFin" class="filter-input" />
      </div>
      <button class="btn-generate" @click="generateReport">
        <i class="bi bi-funnel"></i> Generar Reporte
      </button>
    </section>

    <!-- KPIs -->
    <section class="kpis-section">
      <div class="kpi-card">
        <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="kpi-info">
          <div class="kpi-label">Ventas Totales</div>
          <div class="kpi-value">${{ stats.ventasTotales }}</div>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon"><i class="bi bi-cart-check"></i></div>
        <div class="kpi-info">
          <div class="kpi-label">Pedidos</div>
          <div class="kpi-value">{{ stats.totalPedidos }}</div>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
        <div class="kpi-info">
          <div class="kpi-label">Productos</div>
          <div class="kpi-value">{{ stats.totalProductos }}</div>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></div>
        <div class="kpi-info">
          <div class="kpi-label">Crecimiento</div>
          <div class="kpi-value">+{{ stats.crecimiento }}%</div>
        </div>
      </div>
    </section>

    <!-- Gráficos -->
    <section class="charts-section">
      <div class="chart-card">
        <div class="chart-header">
          <h3><i class="bi bi-bar-chart"></i> Ventas Mensuales</h3>
        </div>
        <div class="chart-body">
          <canvas ref="ventasChart"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-header">
          <h3><i class="bi bi-pie-chart"></i> Distribución por Categoría</h3>
        </div>
        <div class="chart-body">
          <canvas ref="categoriasChart"></canvas>
        </div>
      </div>
    </section>

    <!-- Reportes -->
    <section class="reports-section">
      <div class="report-card">
        <div class="card-header">
          <h3><i class="bi bi-trophy"></i> Top Productos</h3>
        </div>
        <div class="card-body">
          <div v-if="topProductos.length === 0" class="empty">No hay datos</div>
          <div v-for="(p, index) in topProductos" :key="p.id" class="list-item">
            <span class="rank">#{{ index + 1 }}</span>
            <div class="item-info">
              <span class="item-name">{{ p.nombre }}</span>
              <span class="item-detail">{{ p.ventas }} ventas</span>
            </div>
          </div>
        </div>
      </div>

      <div class="report-card">
        <div class="card-header">
          <h3><i class="bi bi-tags"></i> Categorías Populares</h3>
        </div>
        <div class="card-body">
          <div v-if="topCategorias.length === 0" class="empty">No hay datos</div>
          <div v-for="c in topCategorias" :key="c.id" class="list-item">
            <i class="bi bi-tag"></i>
            <div class="item-info">
              <span class="item-name">{{ c.nombre }}</span>
              <span class="item-detail">${{ c.ingresos }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="report-card">
        <div class="card-header">
          <h3><i class="bi bi-calendar-week"></i> Ventas por Día</h3>
        </div>
        <div class="card-body">
          <div v-if="ventasDiarias.length === 0" class="empty">No hay datos</div>
          <div v-for="v in ventasDiarias" :key="v.fecha" class="list-item">
            <i class="bi bi-calendar3"></i>
            <div class="item-info">
              <span class="item-name">{{ v.fecha }}</span>
              <span class="item-detail">${{ v.total }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="report-card">
        <div class="card-header">
          <h3><i class="bi bi-exclamation-triangle"></i> Alertas de Stock</h3>
        </div>
        <div class="card-body">
          <div v-if="alertasStock.length === 0" class="empty">Sin alertas</div>
          <div v-for="a in alertasStock" :key="a.id" class="list-item">
            <i class="bi bi-exclamation-circle" style="color: #ff8074;"></i>
            <div class="item-info">
              <span class="item-name">{{ a.producto }}</span>
              <span class="item-detail warn">{{ a.stock }} unidades</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { Chart, registerables } from 'chart.js'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

Chart.register(...registerables)

const router = useRouter()

const selectedReport = ref('ventas')
const fechaInicio = ref('')
const fechaFin = ref('')

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
  const hoy = new Date()
  const hace30dias = new Date()
  hace30dias.setDate(hoy.getDate() - 30)
  
  fechaFin.value = hoy.toISOString().split('T')[0]
  fechaInicio.value = hace30dias.toISOString().split('T')[0]
  
  generateReport()
}

const generateReport = () => {
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
  if (ventasChartInstance) ventasChartInstance.destroy()
  if (categoriasChartInstance) categoriasChartInstance.destroy()
  
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
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: { color: '#ffffff', font: { size: 10 } }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { color: '#a89bb9', font: { size: 9 } },
            grid: { color: 'rgba(189, 147, 255, 0.1)' }
          },
          x: {
            ticks: { color: '#a89bb9', font: { size: 9 } },
            grid: { color: 'rgba(189, 147, 255, 0.1)' }
          }
        }
      }
    })
  }
  
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
            labels: { color: '#ffffff', font: { size: 10 }, padding: 10 }
          }
        }
      }
    })
  }
}

const exportReport = () => {
  const doc = new jsPDF()
  
  doc.setFontSize(18)
  doc.setTextColor(75, 0, 130)
  doc.text('Reporte de Análisis', 14, 20)
  
  doc.setFontSize(10)
  doc.setTextColor(100, 100, 100)
  doc.text(`Tipo: ${selectedReport.value.toUpperCase()}`, 14, 28)
  doc.text(`Período: ${fechaInicio.value} - ${fechaFin.value}`, 14, 34)
  doc.text(`Generado: ${new Date().toLocaleDateString()}`, 14, 40)
  
  let yPos = 50
  
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
  
  doc.save(`reporte_${selectedReport.value}_${new Date().getTime()}.pdf`)
}

onMounted(() => load())

onBeforeUnmount(() => {
  if (ventasChartInstance) ventasChartInstance.destroy()
  if (categoriasChartInstance) categoriasChartInstance.destroy()
})
</script>

<style scoped>
.mobile-page { min-height: 100vh; padding-bottom: 80px; }

.mobile-header { position: sticky; top: 0; z-index: 100; background: linear-gradient(135deg, #1a0033 0%, #0a001a 100%); padding: 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid rgba(138,43,226,0.3); box-shadow: 0 4px 20px rgba(138,43,226,0.2); }
.mobile-header h1 { margin: 0; font-size: 1.3rem; color: #ffffff; font-family: 'Orbitron', monospace; display: flex; align-items: center; gap: 8px; }
.back-btn, .export-btn { background: rgba(138,43,226,0.2); border: 1px solid rgba(189,147,255,0.3); color: #ffffff; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; }
.back-btn:active, .export-btn:active { background: rgba(138,43,226,0.4); }

.filters-section { padding: 16px; background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; margin: 16px; }
.filter-row { display: flex; gap: 10px; margin-bottom: 10px; }
.filter-select, .filter-input { flex: 1; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(189,147,255,0.25); border-radius: 10px; color: #ffffff; font-size: 0.95rem; }
.filter-select:focus, .filter-input:focus { outline: none; border-color: rgba(189,147,255,0.5); background: rgba(255,255,255,0.08); }
.btn-generate { width: 100%; padding: 12px; background: linear-gradient(135deg, #6B1FA8, #8B5CF6); color: #fff; border: none; border-radius: 10px; font-weight: 600; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; }
.btn-generate:active { transform: scale(0.98); }

.kpis-section { display: grid; grid-template-columns: 1fr; gap: 12px; padding: 0 16px; margin-bottom: 16px; }
.kpi-card { background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%); border: 2px solid rgba(138,43,226,0.35); border-radius: 14px; padding: 14px; display: flex; align-items: center; gap: 12px; box-shadow: 0 8px 20px rgba(138,43,226,0.12); }
.kpi-icon { width: 45px; height: 45px; background: linear-gradient(135deg, rgba(138,43,226,0.3), rgba(189,147,255,0.2)); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #bd93ff; }
.kpi-info { flex: 1; }
.kpi-label { color: #f0e1ff; font-size: 0.75rem; font-weight: 600; margin-bottom: 4px; }
.kpi-value { font-family: 'Orbitron', monospace; font-size: 1.4rem; color: #ffffff; text-shadow: 0 0 6px rgba(255,255,255,.3); }

.charts-section { padding: 0 16px; display: flex; flex-direction: column; gap: 16px; margin-bottom: 16px; }
.chart-card { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(138,43,226,0.15); }
.chart-header { padding: 12px 14px; background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10)); border-bottom: 1px solid rgba(138,43,226,0.3); }
.chart-header h3 { margin: 0; font-size: 1rem; color: #ffffff; font-weight: 700; display: flex; align-items: center; gap: 8px; }
.chart-body { padding: 16px; height: 250px; }

.reports-section { padding: 0 16px; display: flex; flex-direction: column; gap: 16px; }
.report-card { background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(138,43,226,0.15); }
.card-header { padding: 12px 14px; background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10)); border-bottom: 1px solid rgba(138,43,226,0.3); }
.card-header h3 { margin: 0; font-size: 1rem; color: #ffffff; font-weight: 700; display: flex; align-items: center; gap: 8px; }
.card-body { padding: 12px; }

.list-item { display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 10px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.02); margin-bottom: 8px; }
.list-item:last-child { margin-bottom: 0; }
.list-item i { color: #ffffff; font-size: 1.1rem; }
.item-info { flex: 1; display: flex; flex-direction: column; gap: 4px; }
.item-name { color: #ffffff; font-weight: 600; font-size: 0.95rem; }
.item-detail { color: #a89bb9; font-size: 0.85rem; }
.item-detail.warn { color: #ff8074; font-weight: 700; }
.empty { color: #a89bb9; text-align: center; padding: 20px; font-size: 0.9rem; }

.rank { width: 32px; height: 32px; background: linear-gradient(135deg, #4B0082, #6B1FA8); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #ffffff; font-size: 0.9rem; flex-shrink: 0; }
</style>