<template>
  <aside class="module-sidebar">
    <nav class="module-nav" role="navigation">
      <router-link
        v-for="item in items"
        :key="item.id"
        :to="item.route"
        class="nav-item"
        :class="{ active: isActive(item.route) }"
        :aria-label="item.label"
      >
        <i :class="item.icon" class="icon"></i>
        <span class="label">{{ item.label }}</span>
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'
const props = defineProps({
  items: { type: Array, required: true },
  accent: { type: String, default: '#8A2BE2' }
})
const route = useRoute()
const isActive = (to) => route.path === to || route.path.startsWith(to + '/')
</script>

<style scoped lang="scss">
.module-sidebar {
  width: 260px;
  background: linear-gradient(180deg, rgba(18,0,30,0.9), rgba(10,0,20,0.9));
  border-right: 1px solid rgba(138, 43, 226, 0.25);
  position: sticky;
  top: 0;
  height: 100vh;
  padding: 1rem 0.75rem;
}

.module-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0.9rem;
  border: 1px solid rgba(138, 43, 226, 0.25);
  border-radius: 12px;
  color: rgba(255,255,255,0.9);
  text-decoration: none;
  transition: all .2s ease;
  background: rgba(255,255,255,0.02);
}

.nav-item:hover {
  filter: brightness(1.05);
  box-shadow: 0 8px 18px rgba(138,43,226,0.2);
}

.nav-item.active {
  color: #ffffff;
  border-color: rgba(138, 43, 226, 0.6);
  background: linear-gradient(135deg, rgba(138,43,226,0.20), rgba(75,0,130,0.18));
}

.icon { font-size: 1.1rem; }
.label { font-weight: 600; letter-spacing: .2px; }

@media (max-width: 992px) {
  .module-sidebar { display: none; }
}
</style>
