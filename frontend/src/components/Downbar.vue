<template>
  <nav class="downbar" role="navigation" aria-label="Navegación inferior">
    <router-link
      v-for="item in items"
      :key="item.id"
      :to="item.route"
      class="down-item"
      :class="{ active: isActive(item.route) }"
    >
      <i :class="item.icon"></i>
    </router-link>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isActive = (to) => route.path === to || route.path.startsWith(to + '/')

const items = computed(() => [
  { id: 'dashboard', label: 'Inicio', route: '/dashboard', icon: 'bi bi-grid-1x2-fill' },
  { id: 'productos', label: 'Productos', route: '/productos', icon: 'bi bi-box-seam' },
  { id: 'categorias', label: 'Categorías', route: '/categorias', icon: 'bi bi-tags' },
  { id: 'inventario', label: 'Inventario', route: '/inventario', icon: 'bi bi-clipboard-check' },
  { id: 'reportes', label: 'Reportes', route: '/reportes', icon: 'bi bi-graph-up' },
  { id: 'usuarios', label: 'Usuarios', route: '/usuarios', icon: 'bi bi-people' },
])
</script>

<style scoped lang="scss">
.downbar {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  height: calc(60px + env(safe-area-inset-bottom));
  padding-bottom: env(safe-area-inset-bottom);
  background: linear-gradient(135deg, rgba(75,0,130,0.95), rgba(138,43,226,0.95));
  border-top: 1px solid rgba(138, 43, 226, 0.35);
  display: none; /* hidden by default, enabled on mobile */
  z-index: 1050;
}

.down-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, 0.9);
  text-decoration: none;
  gap: 2px;

  i { font-size: 1.25rem; }
  .label { display: none; }
}

.down-item.active {
  color: #ffffff;
}

@media (max-width: 992px) {
  .downbar { 
    display: grid; 
    grid-template-columns: repeat(6, 1fr);
  }
}
</style>
