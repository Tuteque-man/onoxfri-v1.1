<template>
  <div class="not-found-container">
    <div class="matrix-bg"></div>

    <div class="main-content">
      <div class="not-found-content">
        <div class="error-code" data-text="404">404</div>
        <h1 class="error-title">Página No Encontrada</h1>
        <p class="error-description">
          La página que buscas no existe o ha sido movida a otra ubicación.
        </p>

        <div class="error-actions">
          <button class="btn-futuristic" @click="goHome">
            <i class="fas fa-home"></i>
            Ir al Inicio
          </button>
          <button class="btn-secondary-futuristic" @click="goBack">
            <i class="fas fa-arrow-left"></i>
            Volver Atrás
          </button>
        </div>

        <!-- Elementos decorativos -->
        <div class="floating-elements">
          <div
            v-for="i in 8"
            :key="i"
            class="floating-element"
            :style="getFloatingStyle(i)"
          >
            <i :class="getRandomIcon()"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

// Composables
const router = useRouter()

// Datos
const icons = [
  'fas fa-cog', 'fas fa-database', 'fas fa-server',
  'fas fa-code', 'fas fa-terminal', 'fas fa-sitemap',
  'fas fa-project-diagram', 'fas fa-network-wired'
]

// Métodos
const goHome = () => {
  router.push('/')
}

const goBack = () => {
  router.back()
}

const getRandomIcon = () => {
  return icons[Math.floor(Math.random() * icons.length)]
}

const getFloatingStyle = (index) => {
  const size = Math.random() * 40 + 20
  const left = Math.random() * 100
  const top = Math.random() * 100
  const animationDelay = Math.random() * 5
  const animationDuration = Math.random() * 10 + 10

  return {
    fontSize: `${size}px`,
    left: `${left}%`,
    top: `${top}%`,
    animationDelay: `${animationDelay}s`,
    animationDuration: `${animationDuration}s`
  }
}
</script>

<style scoped>
.not-found-container {
  width: 100%;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.not-found-content {
  text-align: center;
  z-index: 2;
  max-width: 600px;
  padding: 2rem;
}

.error-code {
  font-family: 'Orbitron', monospace;
  font-size: 8rem;
  font-weight: 900;
  color: #4B0082;
  margin-bottom: 1rem;
  text-shadow: 0 0 30px rgba(75, 0, 130, 0.5);
  animation: glitch 2s infinite;
}

.error-title {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 2.5rem;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.error-description {
  font-size: 1.125rem;
  color: #666;
  margin-bottom: 3rem;
  line-height: 1.6;
}

.error-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 3rem;
}

.floating-elements {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.floating-element {
  position: absolute;
  color: rgba(75, 0, 130, 0.3);
  animation: float 15s infinite linear;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  33% {
    transform: translateY(-50px) rotate(120deg);
  }
  66% {
    transform: translateY(-100px) rotate(240deg);
  }
}

@keyframes glitch {
  0%, 100% { transform: translate(0); }
  20% { transform: translate(-2px, 2px); }
  40% { transform: translate(-2px, -2px); }
  60% { transform: translate(2px, 2px); }
  80% { transform: translate(2px, -2px); }
}

@media (max-width: 768px) {
  .error-code {
    font-size: 6rem;
  }

  .error-title {
    font-size: 2rem;
  }

  .error-actions {
    flex-direction: column;
    align-items: center;
  }
}
</style>