<template>
  <div class="layout">
    <Sidebar />

    <main class="page">

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
          <div class="card-body chart-placeholder">
            <p>Gráfico (próximamente)</p>
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

        <div class="card span2">
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
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Sidebar from '@/components/Sidebar.vue'

const router = useRouter()

const stats = reactive({ totalProductos: 0, stockBajo: 0, totalEmpresas: 0, valorInventario: '$0' })
const productosStockBajo = ref([])
const recentActivity = ref([
  { icon: 'bi bi-plus-circle', text: 'Producto creado', time: 'Hace 1h' },
  { icon: 'bi bi-pencil-square', text: 'Ajuste de stock', time: 'Hace 3h' },
  { icon: 'bi bi-file-earmark-bar-graph', text: 'Reporte mensual exportado', time: 'Ayer' },
])

const load = () => {
  // TODO: Reemplazar mocks por servicios reales
  stats.totalProductos = 45
  stats.stockBajo = 3
  stats.totalEmpresas = 2
  stats.valorInventario = '$12,450'
  productosStockBajo.value = [
    { id: 1, name: 'Producto A', stock_actual: 2, min_stock: 5 },
    { id: 2, name: 'Producto B', stock_actual: 1, min_stock: 3 },
    { id: 3, name: 'Producto C', stock_actual: 0, min_stock: 2 }
  ]
}

const refresh = () => {
  load()
}

const go = (name) => router.push({ name })

onMounted(() => {
  load()
})
</script>

<style scoped>
.layout { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
.page { position: relative; padding: 26px; }
.page::before { content: ""; position: absolute; inset: 0; pointer-events: none; background:
  radial-gradient(1200px 600px at 10% 10%, rgba(138,43,226,0.10), transparent 60%),
  radial-gradient(900px 500px at 90% 20%, rgba(189,147,255,0.08), transparent 60%),
  linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.00)); }
.page { position: relative; padding: 26px; overflow: hidden; }
.page::before {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    radial-gradient(1200px 600px at 10% 10%, rgba(138,43,226,0.10), transparent 60%),
    radial-gradient(900px 500px at 90% 20%, rgba(189,147,255,0.08), transparent 60%),
    linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.00));
  animation: background-pulse 12s infinite ease-in-out;
}

@keyframes background-pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.02); opacity: 0.85; }
}

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header .title { display: flex; align-items: center; gap: 12px; }
.page-header .title h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.8rem; letter-spacing: 1px; color: #ffffff; text-shadow: 0 0 8px rgba(255,255,255,.35), 0 0 16px rgba(255,255,255,.22); }
.page-header .title h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.8rem; letter-spacing: 1px; color: #cba6ff; text-shadow: 0 0 10px rgba(203, 166, 255, 0.8), 0 0 22px rgba(138, 43, 226, 0.5); }
.page-header .title h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.8rem; letter-spacing: 1px; color: #e6d8ff; text-shadow: 0 0 4px #fff, 0 0 12px #d9baff, 0 0 25px #cba6ff, 0 0 40px #8a2be2; }
.page-header .title h1 { margin: 0; font-family: 'Orbitron', monospace; font-size: 1.8rem; letter-spacing: 1px; color: #f0ebff; text-shadow: 0 0 8px rgba(255,255,255,0.1); }
.page-header .actions { display: flex; gap: 12px; }

.page-header .actions .btn-futuristic { background: linear-gradient(135deg, #4B0082, #6B1FA8); color: #fff; border: 1px solid rgba(138,43,226,0.45); padding: 10px 14px; border-radius: 12px; box-shadow: 0 8px 24px rgba(138,43,226,0.25), inset 0 0 12px rgba(255,255,255,0.06); font-weight: 600; letter-spacing: .2px; }
.page-header .actions .btn-futuristic i { margin-right: 8px; }
.page-header .actions .btn-futuristic:hover { filter: brightness(1.08); box-shadow: 0 12px 30px rgba(138,43,226,0.3), inset 0 0 14px rgba(255,255,255,0.08); }

.page-header .actions .btn-ghost-futuristic { background: rgba(255,255,255,0.04); color: #eae3f7; border: 1px solid rgba(138,43,226,0.35); padding: 10px 14px; border-radius: 12px; box-shadow: inset 0 0 10px rgba(255,255,255,0.04); font-weight: 600; letter-spacing: .2px; }
.page-header .actions .btn-ghost-futuristic i { margin-right: 8px; }
.page-header .actions .btn-ghost-futuristic:hover { background: rgba(255,255,255,0.07); }
.kpis { display: grid; grid-template-columns: repeat(4, minmax(180px, 1fr)); gap: 16px; margin-bottom: 26px; }
.kpi {
  position: relative;
  background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%);
  border: 2px solid transparent;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 10px 28px rgba(138,43,226,0.12), inset 0 0 18px rgba(138,43,226,0.08);
  backdrop-filter: blur(4px);
  overflow: hidden;
  transition: transform .2s ease, box-shadow .2s ease;
}
.kpi::before {
  content: "";
  position: absolute;
  inset: -2px;
  border-radius: 16px;
  pointer-events: none;
  background: conic-gradient(from var(--angle), transparent 0%, #cba6ff 20%, transparent 40%);
  animation: border-spin 4s linear infinite;
  opacity: 0.8;
}
.kpi:hover { transform: scale(1.03); box-shadow: 0 15px 40px rgba(138,43,226,0.25), inset 0 0 24px rgba(138,43,226,0.12); }

.kpis { display: grid; grid-template-columns: repeat(4, minmax(180px, 1fr)); gap: 16px; margin-bottom: 26px; }
.kpi { position: relative; background: radial-gradient(120% 120% at 100% 0%, rgba(138,43,226,0.08), rgba(75,0,130,0.06) 40%, rgba(0,0,0,0.2) 100%); border: 2px solid rgba(138,43,226,0.35); border-radius: 16px; padding: 18px; box-shadow: 0 10px 28px rgba(138,43,226,0.12), inset 0 0 18px rgba(138,43,226,0.08); backdrop-filter: blur(4px); overflow: hidden; }
.kpi::before { content: ""; position: absolute; inset: 0; border-radius: 16px; pointer-events: none; background: linear-gradient(120deg, transparent 0%, rgba(189,147,255,.18) 50%, transparent 100%); opacity: .35; }
.kpi-label { color: #f0e1ff; font-size: 0.86rem; font-weight: 700; letter-spacing: .3px; }
.kpi-value { font-family: 'Orbitron', monospace; font-size: 2rem; color: #ffffff; text-shadow: 0 0 8px rgba(255,255,255,.35), 0 0 16px rgba(255,255,255,.22); }
.kpi-value { font-family: 'Orbitron', monospace; font-size: 2rem; color: #ffffff; text-shadow: 0 0 8px rgba(255,255,255,.35), 0 0 16px rgba(255,255,255,.22); }
.kpi-value.warn { color: #ff8074; }

.grid { display: grid; grid-template-columns: repeat(2, minmax(300px, 1fr)); gap: 18px; }
.card { position: relative; background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 18px; overflow: hidden; box-shadow: 0 15px 40px rgba(138,43,226,0.15), inset 0 0 24px rgba(138,43,226,0.06); transition: transform .25s ease, box-shadow .25s ease; }
.card { position: relative; background: linear-gradient(180deg, rgba(18,0,30,0.85), rgba(10,0,20,0.85)); border: 2px solid rgba(138,43,226,0.35); border-radius: 18px; overflow: hidden; box-shadow: 0 15px 40px rgba(138,43,226,0.15), inset 0 0 24px rgba(138,43,226,0.06); transition: transform .25s ease, box-shadow .25s ease; animation: neon-pulse 8s infinite alternate; }
.card::after { content: ""; position: absolute; inset: 0; pointer-events: none; border-radius: 18px; background: conic-gradient(from 0deg, rgba(138,43,226,.08), rgba(189,147,255,.22), rgba(255,0,255,.18), rgba(189,147,255,.22), rgba(138,43,226,.08)); mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); padding: 2px; opacity: .45; filter: blur(.6px); }
.card:hover { transform: translateY(-4px); box-shadow: 0 25px 55px rgba(138,43,226,0.25), inset 0 0 28px rgba(138,43,226,0.08); }
.card:hover::after { transform: rotate(180deg); }
.card-header { padding: 14px 16px; background: linear-gradient(135deg, rgba(189,147,255,0.10), rgba(138,43,226,0.10)); border-bottom: 1px solid rgba(138,43,226,0.3); }
.card-header h3 { margin: 0; font-family: 'Space Grotesk', sans-serif; color: #ffffff; font-weight: 700; font-size: 1.08rem; display: flex; align-items: center; gap: 8px; text-shadow: 0 0 6px rgba(255,255,255,.25); }
.card-body { padding: 16px; }
.card + .card, .span2 { margin-top: 0; }
.span2 { grid-column: span 2; }

.chart-placeholder { height: 280px; display: grid; place-items: center; color: #cfc3ff; border: 1px dashed rgba(189,147,255,0.35); border-radius: 12px; box-shadow: inset 0 0 20px rgba(138,43,226,0.1); text-shadow: 0 0 6px rgba(189,147,255,.6); }

.list { display: flex; flex-direction: column; gap: 10px; }
.list-item { display: grid; grid-template-columns: 20px 1fr auto; align-items: center; gap: 8px; padding: 12px; border-radius: 12px; border: 1px solid rgba(189,147,255,0.25); background: rgba(255,255,255,0.02); transition: background .2s ease, box-shadow .2s ease, transform .2s ease; }
.list-item:hover { background: rgba(189,147,255,0.06); box-shadow: 0 6px 20px rgba(138,43,226,0.25); transform: translateY(-2px); }
.list-item .name { color: #ffffff; font-weight: 600; }
.list-item .qty, .list-item .time { color: var(--text-secondary); font-size: 0.85rem; }
.list-item .name { color: #ffffff; font-weight: 600; }
.list-item .qty, .list-item .time { color: #b5a8d1; font-size: 0.85rem; }
.empty { color: var(--text-secondary); text-align: center; padding: 20px; }
.list-item i { color: #ffffff; }
.list-item .name { color: #ffffff; font-weight: 500; }
.list-item .qty, .list-item .time { color: #a89bb9; font-size: 0.85rem; }
.empty { color: #a89bb9; text-align: center; padding: 20px; }

@keyframes neonPulse { 0%, 100% { box-shadow: 0 10px 35px rgba(138,43,226,0.15), inset 0 0 20px rgba(138,43,226,0.08) } 50% { box-shadow: 0 12px 45px rgba(138,43,226,0.25), inset 0 0 28px rgba(138,43,226,0.12) } }
/* Animations removed for a clean static neon look */
@property --angle { syntax: '<angle>'; inherits: false; initial-value: 0deg; }
@keyframes border-spin {
  to { --angle: 360deg; }
}

@keyframes neon-pulse {
  from { box-shadow: 0 15px 40px rgba(138,43,226,0.15), inset 0 0 24px rgba(138,43,226,0.06); }
  to { box-shadow: 0 15px 45px rgba(138,43,226,0.22), inset 0 0 28px rgba(138,43,226,0.09); }
}

/* --- Neon white overrides to avoid colored blur --- */
:where(.page-header .title h1,
       .card-header h3,
       .kpi-value,
       .list-item .name,
       .list-item i) {
  color: #ffffff !important;
  text-shadow: 0 0 6px rgba(255,255,255,0.28), 0 0 14px rgba(255,255,255,0.18);
}

@media (max-width: 1100px) {
  .layout { grid-template-columns: 80px 1fr; }
  .kpis { grid-template-columns: repeat(2, 1fr); }
  .grid { grid-template-columns: 1fr; }
  .span2 { grid-column: auto; }
}
</style>
