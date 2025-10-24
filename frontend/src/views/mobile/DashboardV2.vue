<template>
  <div class="mobile-layout">
    <main class="mobile-page">
      <div class="page-header">
        <h1><i class="bi bi-speedometer2"></i> Panel Principal</h1>
      </div>

      <section class="kpis">
        <div class="kpi">
          <div class="kpi-label">Productos</div>
          <div class="kpi-value">{{ stats.totalProductos }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Stock Bajo</div>
          <div class="kpi-value warn">{{ stats.stockBajo }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Empresas</div>
          <div class="kpi-value">{{ stats.totalEmpresas }}</div>
        </div>
        <div class="kpi">
          <div class="kpi-label">Valor Inventario</div>
          <div class="kpi-value">{{ stats.valorInventario }}</div>
        </div>
      </section>

      <section class="grid">
        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-graph-up-arrow"></i> Tendencia de Inventario</h3>
          </div>
          <div class="card-body chart-container">
            <div class="chart-wrapper">
              <canvas ref="valueChart"></canvas>
            </div>
            <div class="chart-wrapper">
              <canvas ref="productsChart"></canvas>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-bell"></i> Stock Bajo</h3>
          </div>
          <div class="card-body list">
            <div v-if="productosStockBajo.length === 0" class="empty">Sin alertas</div>
            <div v-for="p in productosStockBajo.slice(0,6)" :key="p.id" class="list-item">
              <i class="bi bi-exclamation-triangle"></i>
              <span class="name">{{ p.name }}</span>
              <span class="qty">{{ p.stock_actual }} / {{ p.min_stock }}</span>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="bi bi-clock-history"></i> Actividad Reciente</h3>
          </div>
          <div class="card-body list">
            <div v-for="(a, i) in recentActivity" :key="i" class="list-item">
              <i :class="a.icon"></i>
              <span class="name">{{ a.text }}</span>
              <span class="time">{{ a.time }}</span>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onBeforeUnmount, onActivated, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const router = useRouter()

const stats = reactive({ totalProductos: 0, stockBajo: 0, totalEmpresas: 0, valorInventario: '$0' })
const productosStockBajo = ref([])
const valueChart = ref(null)
const productsChart = ref(null)
let valueChartInstance = null
let productsChartInstance = null

const recentActivity = ref([
  { icon: 'bi bi-plus-circle', text: 'Nuevo producto: Laptop Dell Inspiron 15', time: 'Hace 15 min' },
  { icon: 'bi bi-exclamation-triangle', text: 'Alerta: Stock bajo en Teclado Mecánico RGB', time: 'Hace 1h' },
  { icon: 'bi bi-pencil-square', text: 'Actualización de precio: Monitor Samsung 27"', time: 'Hace 2h' },
  { icon: 'bi bi-box-seam', text: 'Pedido recibido: 50 unidades Mouse Logitech', time: 'Hace 3h' },
  { icon: 'bi bi-person-plus', text: 'Nuevo usuario registrado: Carlos López', time: 'Hace 5h' },
  { icon: 'bi bi-file-earmark-bar-graph', text: 'Reporte mensual generado exitosamente', time: 'Hace 8h' },
])

const createValueChart = () => {
  if (!valueChart.value) return
  if (valueChartInstance) valueChartInstance.destroy()

  const ctx = valueChart.value.getContext('2d')
  valueChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      datasets: [{
        label: 'Valor Inventario ($)',
        data: [32500, 35200, 38100, 36800, 39500, 41200, 42800, 43500, 44200, 45100, 45780, 45780],
        borderColor: 'rgba(138, 43, 226, 1)',
        backgroundColor: 'rgba(138, 43, 226, 0.15)',
        borderWidth: 2,
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#8A2BE2',
        pointBorderColor: '#fff',
        pointBorderWidth: 1,
        pointRadius: 3,
        pointHoverRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          align: 'start',
          labels: {
            color: '#ffffff',
            font: { family: 'Space Grotesk', size: 10 },
            padding: 6,
            usePointStyle: true
          }
        },
        tooltip: {
          backgroundColor: 'rgba(18, 0, 30, 0.95)',
          titleColor: '#ffffff',
          bodyColor: '#BD93FF',
          borderColor: 'rgba(138, 43, 226, 0.5)',
          borderWidth: 1,
          padding: 8,
          displayColors: true,
          callbacks: {
            label: (context) => 'Valor: $' + context.parsed.y.toLocaleString()
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(138, 43, 226, 0.1)', drawBorder: false },
          ticks: { color: '#a89bb9', font: { family: 'Space Grotesk', size: 9 } }
        },
        y: {
          grid: { color: 'rgba(138, 43, 226, 0.1)', drawBorder: false },
          ticks: {
            color: '#a89bb9',
            font: { family: 'Space Grotesk', size: 9 },
            callback: (value) => '$' + (value / 1000).toFixed(0) + 'k'
          }
        }
      }
    }
  })
}

const createProductsChart = () => {
  if (!productsChart.value) return
  if (productsChartInstance) productsChartInstance.destroy()

  const ctx = productsChart.value.getContext('2d')
  productsChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      datasets: [{
        label: 'Productos Totales',
        data: [95, 98, 105, 102, 110, 115, 118, 120, 123, 125, 127, 127],
        backgroundColor: 'rgba(189, 147, 255, 0.6)',
        borderColor: 'rgba(189, 147, 255, 1)',
        borderWidth: 1.5,
        borderRadius: 4,
        hoverBackgroundColor: 'rgba(189, 147, 255, 0.8)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          align: 'start',
          labels: {
            color: '#ffffff',
            font: { family: 'Space Grotesk', size: 10 },
            padding: 6,
            usePointStyle: true
          }
        },
        tooltip: {
          backgroundColor: 'rgba(18, 0, 30, 0.95)',
          titleColor: '#ffffff',
          bodyColor: '#BD93FF',
          borderColor: 'rgba(138, 43, 226, 0.5)',
          borderWidth: 1,
          padding: 8,
          displayColors: true,
          callbacks: {
            label: (context) => 'Productos: ' + context.parsed.y
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(138, 43, 226, 0.1)', drawBorder: false },
          ticks: { color: '#a89bb9', font: { family: 'Space Grotesk', size: 9 } }
        },
        y: {
          grid: { color: 'rgba(138, 43, 226, 0.1)', drawBorder: false },
          ticks: {
            color: '#a89bb9',
            font: { family: 'Space Grotesk', size: 9 },
            stepSize: 20
          }
        }
      }
    }
  })
}

const load = () => {
  stats.totalProductos = 127
  stats.stockBajo = 8
  stats.totalEmpresas = 1
  stats.valorInventario = '$45,780'
  productosStockBajo.value = [
    { id: 1, name: 'Laptop Dell Inspiron 15', stock_actual: 2, min_stock: 5 },
    { id: 2, name: 'Mouse Logitech MX Master', stock_actual: 1, min_stock: 10 },
    { id: 3, name: 'Teclado Mecánico RGB', stock_actual: 0, min_stock: 8 },
    { id: 4, name: 'Monitor Samsung 27"', stock_actual: 3, min_stock: 6 },
    { id: 5, name: 'Webcam HD 1080p', stock_actual: 1, min_stock: 4 },
    { id: 6, name: 'Auriculares Bluetooth', stock_actual: 2, min_stock: 12 },
    { id: 7, name: 'Cable HDMI 2m', stock_actual: 4, min_stock: 15 },
    { id: 8, name: 'Hub USB-C 7 puertos', stock_actual: 0, min_stock: 5 }
  ]
  
  setTimeout(() => {
    createValueChart()
    createProductsChart()
  }, 100)
}

onMounted(() => {
  load()
})

onActivated(() => {
  // Recrear gráficas cuando el componente se reactiva
  nextTick(() => {
    setTimeout(() => {
      if (valueChart.value) {
        if (valueChartInstance) {
          valueChartInstance.destroy()
          valueChartInstance = null
        }
        createValueChart()
      }
      if (productsChart.value) {
        if (productsChartInstance) {
          productsChartInstance.destroy()
          productsChartInstance = null
        }
        createProductsChart()
      }
    }, 100)
  })
})

onBeforeUnmount(() => {
  if (valueChartInstance) {
    valueChartInstance.destroy()
    valueChartInstance = null
  }
  if (productsChartInstance) {
    productsChartInstance.destroy()
    productsChartInstance = null
  }
})
</script>

<style scoped>
.mobile-layout { 
  display: flex; 
  flex-direction: column;
  min-height: 100vh;
  height: 100vh;
  width: 100%;
  max-width: 100vw;
  overflow: hidden;
  position: relative;
}

.mobile-page { 
  position: relative; 
  padding: 16px;
  padding-bottom: 100px;
  overflow-y: auto;
  overflow-x: hidden;
  flex: 1;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
  -webkit-overflow-scrolling: touch;
}

.mobile-page::before {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    radial-gradient(800px 400px at 10% 10%, rgba(138,43,226,0.10), transparent 60%),
    radial-gradient(600px 300px at 90% 20%, rgba(189,147,255,0.08), transparent 60%),
    linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.00));
  animation: background-pulse 12s infinite ease-in-out;
}

@keyframes background-pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.02); opacity: 0.85; }
}

.page-header { 
  margin-bottom: 16px; 
}

.page-header h1 { 
  margin: 0 0 12px 0; 
  font-family: 'Orbitron', monospace; 
  font-size: 1.5rem; 
  color: #ffffff; 
  display: flex; 
  align-items: center; 
  gap: 8px; 
  text-shadow: 0 0 8px rgba(255,255,255,.35);
}

.kpis { 
  display: grid; 
  grid-template-columns: repeat(2, 1fr); 
  gap: 12px; 
  margin-bottom: 20px;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
}

.kpi { 
  position: relative; 
  background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%); 
  border: 2px solid rgba(138,43,226,0.35); 
  border-radius: 12px; 
  padding: 14px; 
  box-shadow: 0 8px 20px rgba(138,43,226,0.12), inset 0 0 14px rgba(138,43,226,0.08); 
  backdrop-filter: blur(4px); 
  overflow: hidden; 
}

.kpi::before { 
  content: ""; 
  position: absolute; 
  inset: 0; 
  border-radius: 12px; 
  pointer-events: none; 
  background: linear-gradient(120deg, transparent 0%, rgba(189,147,255,.18) 50%, transparent 100%); 
  opacity: .35; 
}

.kpi-label { 
  color: #f0e1ff; 
  font-size: 0.75rem; 
  font-weight: 700; 
  letter-spacing: .3px; 
  margin-bottom: 4px;
}

.kpi-value { 
  font-family: 'Orbitron', monospace; 
  font-size: 1.5rem; 
  color: #ffffff; 
  text-shadow: 0 0 8px rgba(255,255,255,.35), 0 0 16px rgba(255,255,255,.22); 
}

.kpi-value.warn { color: #ff8074; }

.grid { 
  display: grid; 
  grid-template-columns: 1fr; 
  gap: 14px;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
}

.card { 
  position: relative; 
  background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); 
  border: 2px solid rgba(138,43,226,0.35); 
  border-radius: 14px; 
  overflow: hidden; 
  box-shadow: 0 12px 30px rgba(138,43,226,0.15), inset 0 0 20px rgba(138,43,226,0.06); 
  transition: transform .25s ease, box-shadow .25s ease; 
  animation: neon-pulse 8s infinite alternate; 
}

.card::after { 
  content: ""; 
  position: absolute; 
  inset: 0; 
  pointer-events: none; 
  border-radius: 14px; 
  background: conic-gradient(from 0deg, rgba(138,43,226,.08), rgba(189,147,255,.22), rgba(255,0,255,.18), rgba(189,147,255,.22), rgba(138,43,226,.08)); 
  mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); 
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); 
  padding: 2px; 
  opacity: .45; 
  filter: blur(.6px); 
}

.card-header { 
  padding: 12px 14px; 
  background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10)); 
  border-bottom: 1px solid rgba(138,43,226,0.3); 
}

.card-header h3 { 
  margin: 0; 
  font-family: 'Space Grotesk', sans-serif; 
  color: #ffffff; 
  font-weight: 700; 
  font-size: 0.95rem; 
  display: flex; 
  align-items: center; 
  gap: 6px; 
  text-shadow: 0 0 6px rgba(255,255,255,.25); 
}

.card-body { padding: 14px; }

.card-body.chart-container { 
  display: flex; 
  flex-direction: column; 
  gap: 16px; 
}

.chart-wrapper { 
  height: 150px; 
  width: 100%; 
  position: relative; 
}

.chart-placeholder { 
  height: 200px; 
  display: grid; 
  place-items: center; 
  color: #cfc3ff; 
  border: 1px dashed rgba(189,147,255,0.35); 
  border-radius: 10px; 
  box-shadow: inset 0 0 16px rgba(138,43,226,0.1); 
  text-shadow: 0 0 6px rgba(189,147,255,.6); 
  font-size: 0.9rem;
}

.list { 
  display: flex; 
  flex-direction: column; 
  gap: 8px; 
}

.list-item { 
  display: grid; 
  grid-template-columns: 18px 1fr auto; 
  align-items: center; 
  gap: 8px; 
  padding: 10px; 
  border-radius: 10px; 
  border: 1px solid rgba(189,147,255,0.25); 
  background: rgba(255,255,255,0.02); 
  transition: background .2s ease, box-shadow .2s ease, transform .2s ease; 
}

.list-item:hover { 
  background: rgba(189,147,255,0.06); 
  box-shadow: 0 4px 16px rgba(138,43,226,0.25); 
  transform: translateY(-1px); 
}

.list-item .name { 
  color: #ffffff; 
  font-weight: 600; 
  font-size: 0.85rem;
}

.list-item .qty, .list-item .time { 
  color: #a89bb9; 
  font-size: 0.75rem; 
}

.empty { 
  color: #a89bb9; 
  text-align: center; 
  padding: 16px; 
  font-size: 0.85rem;
}

.list-item i { color: #ffffff; }

@keyframes neon-pulse {
  from { box-shadow: 0 12px 30px rgba(138,43,226,0.15), inset 0 0 20px rgba(138,43,226,0.06); }
  to { box-shadow: 0 12px 35px rgba(138,43,226,0.22), inset 0 0 24px rgba(138,43,226,0.09); }
}

:where(.card-header h3, .kpi-value, .list-item .name, .list-item i) {
  color: #ffffff !important;
  text-shadow: 0 0 6px rgba(255,255,255,0.28), 0 0 14px rgba(255,255,255,0.18);
}
</style>