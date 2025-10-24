<template>
  <nav class="module-downbar" role="navigation" aria-label="Navegación inferior módulo">
    <router-link
      v-for="item in items"
      :key="item.id"
      :to="item.route"
      class="down-item"
      :class="{ active: isActive(item.route) }"
      :aria-label="item.label"
    >
      <i :class="item.icon"></i>
      <span class="label" v-if="showLabels">{{ item.label }}</span>
    </router-link>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  items: { type: Array, required: true },
  accent: { type: String, default: '#8A2BE2' },
  showLabels: { type: Boolean, default: false }
})

const route = useRoute()
const isActive = (to) => route.path === to || route.path.startsWith(to + '/')
</script>

<style scoped lang="scss">
.module-downbar {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  height: calc(60px + env(safe-area-inset-bottom));
  padding-bottom: env(safe-area-inset-bottom);
  background: linear-gradient(135deg, rgba(75,0,130,0.95), rgba(138,43,226,0.95));
  border-top: 1px solid rgba(138, 43, 226, 0.35);
  display: none;
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

.down-item.active { color: #fff; }

@media (max-width: 992px) {
  .module-downbar {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
  }
}
</style>
