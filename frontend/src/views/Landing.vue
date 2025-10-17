<template>
   <div class="landing-container">
    <!-- Fondo animado -->
     <div class="matrix-bg"></div>

    <!-- Partículas flotantes -->
     <div class="particles-container">
       <div
         v-for="i in 50"
         :key="i"
         class="particle"
         :style="getParticleStyle(i)"
       ></div>
     </div>

     <div class="landing-content">
      <!-- Layout de dos columnas: Modelo a la izquierda, Texto a la derecha -->
      <div class="hero-section animate-fade-in">
        <!-- Panel del Modelo 3D (Izquierda) -->
        <div class="model-panel">
          <div ref="modelCanvas" class="model-canvas"></div>
        </div>

        <!-- Panel de Texto (Derecha) -->
        <div class="text-panel">
          <span class="model-name">{{ currentModelName }}</span>
          <p class="model-description">{{ currentModelDescription }}</p>
          <div class="landing-cta">
            <button class="btn-landing btn-futuristic" @click="changeFrame('auth')">
              Comenzar Ahora
            </button>
            <button class="btn-landing btn-secondary-futuristic" @click="changeFrame('dashboard')">
              Ver Demo
            </button>
          </div>
        </div>
      </div>
     </div>

    <!-- Sección de demostración -->
    <div ref="demoSection" class="demo-section">
      <div class="demo-container">
        <h3 class="demo-title">Características Principales</h3>
        <div class="features-grid">
          <div
            v-for="(feature, index) in features"
            :key="feature.id"
            class="feature-card animate-fade-in"
            :style="{ animationDelay: `${index * 0.2}s` }"
          >
            <div class="feature-icon">
              <i :class="feature.icon"></i>
            </div>
            <h4 class="feature-title">{{ feature.title }}</h4>
            <p class="feature-description">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
 import { ref, onMounted, onUnmounted, computed } from 'vue'
 import { useRouter } from 'vue-router'
 import * as THREE from 'three'
 import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js'

 // Composables
 const router = useRouter()

 // Referencias
 const demoSection = ref(null)
 const modelCanvas = ref(null)

 // Datos reactivos
 const glitchActive = ref(false)
 const particles = ref([])
 const currentModelIndex = ref(0)
 const isTransitioning = ref(false)

 // Modelos 3D procedurales
 const models3D = ref([
   {
     name: 'Análisis Predictivo',
     description: 'Anticípate a la demanda y evita roturas de stock con nuestra IA avanzada de machine learning.',
     geometry: 'predictive',
     color: '#C77DFF', // Morado claro
     font: 'Orbitron',
     emissive: '#3a1a5a'
   },
   {
     name: 'Control Total',
     description: 'Visualiza y gestiona cada artículo de tu inventario en tiempo real con precisión milimétrica.',
     geometry: 'control',
     color: '#8A2BE2', // Morado
     font: 'Orbitron',
     emissive: '#2a1a4a'
   },
   {
     name: 'Automatización Inteligente',
     description: 'Optimiza tus operaciones con algoritmos de IA que aprenden y se adaptan automáticamente.',
     geometry: 'automation',
     color: '#8A2BE2', // Rosa/Coral
     font: 'Orbitron',
     emissive: '#2a1a4a'
   }
 ])

 const currentModelName = computed(() => models3D.value[currentModelIndex.value].name)
 const currentModelDescription = computed(() => models3D.value[currentModelIndex.value].description)

// Características del sistema
const features = ref([
  {
    id: 'ai-inventory',
    title: 'IA Predictiva',
    description: 'Algoritmos inteligentes predicen demandas y optimizan stock automáticamente.',
    icon: 'fas fa-brain'
  },
  {
    id: 'real-time',
    title: 'Tiempo Real',
    description: 'Actualizaciones instantáneas y sincronización multi-dispositivo.',
    icon: 'fas fa-bolt'
  },
  {
    id: 'analytics',
    title: 'Análisis Avanzado',
    description: 'Dashboards interactivos con métricas y reportes detallados.',
    icon: 'fas fa-chart-line'
  },
  {
    id: 'automation',
    title: 'Automatización',
    description: 'Procesos automáticos de reordenamiento y alertas inteligentes.',
    icon: 'fas fa-robot'
  },
  {
    id: 'security',
    title: 'Seguridad Total',
    description: 'Encriptación avanzada y control de accesos granular.',
    icon: 'fas fa-shield-alt'
  },
  {
    id: 'scalability',
    title: 'Escalabilidad',
    description: 'Crece con tu negocio, desde pequeñas tiendas hasta grandes empresas.',
    icon: 'fas fa-expand-arrows-alt'
  }
])

// Variables para Three.js avanzado
let scene, camera, renderer, controls, currentLoadedModel
let lightParticles
let modelParticles = []
let floatingOrbs = []
let clock = new THREE.Clock()

// Métodos avanzados
const changeFrame = (frameId) => {
  // Navegación usando vue-router
  const map = {
    auth: '/auth',
    dashboard: '/dashboard'
  }
  const target = map[frameId] || '/'
  router.push(target)
}

const goToModel = (index) => {
  if (isTransitioning.value || index === currentModelIndex.value) return
  isTransitioning.value = true
  currentModelIndex.value = index
  setTimeout(() => {
    isTransitioning.value = false
  }, 1000)
}

const triggerGlitch = () => {
  glitchActive.value = true
  setTimeout(() => {
    glitchActive.value = false
  }, 2000)
}

const animateParticles = () => {
  particles.value.forEach((particle, index) => {
    // Lógica de animación si es necesaria
  })
}

const getParticleStyle = (index) => {
  const size = Math.random() * 6 + 2
  const left = Math.random() * 100
  const top = Math.random() * 100
  const animationDelay = Math.random() * 20
  const animationDuration = Math.random() * 15 + 20

  return {
    width: `${size}px`,
    height: `${size}px`,
    left: `${left}%`,
    top: `${top}%`,
    animationDelay: `${animationDelay}s`,
    animationDuration: `${animationDuration}s`,
    background: `radial-gradient(circle, rgba(75, 0, 130, ${Math.random() * 0.8 + 0.2}), rgba(138, 43, 226, ${Math.random() * 0.4 + 0.1}))`,
    boxShadow: `0 0 ${size * 2}px rgba(75, 0, 130, ${Math.random() * 0.5 + 0.2})`
  }
}

const nextModel = () => {
  if (isTransitioning.value) return
  const nextIndex = (currentModelIndex.value + 1) % models3D.value.length
  goToModel(nextIndex)
}

// Crear geometría procedural avanzada
const createAdvancedGeometry = (type) => {
  const group = new THREE.Group()
  const currentModel = models3D.value[currentModelIndex.value]

  switch (type) {
    case 'predictive':
      // Cristal de datos flotante
      const crystalGeom = new THREE.IcosahedronGeometry(2, 1)
      const crystalMat = new THREE.MeshPhysicalMaterial({
        color: currentModel.color,
        emissive: currentModel.emissive,
        roughness: 0,
        metalness: 0.5,
        transparent: true,
        opacity: 0.7,
        transmission: 0.9,
        ior: 1.5
      })
      const crystal = new THREE.Mesh(crystalGeom, crystalMat)

      // Red de líneas alrededor del cristal
      const wireframeGeom = new THREE.IcosahedronGeometry(2.5, 1)
      const wireframeMat = new THREE.MeshBasicMaterial({
        color: currentModel.color,
        wireframe: true,
        transparent: true,
        opacity: 0.3
      })
      const wireframe = new THREE.Mesh(wireframeGeom, wireframeMat)

      group.add(crystal)
      group.add(wireframe)
      break

    case 'control':
      // Cubo complejo con geometría interna
      const cubeGroup = new THREE.Group()

      // Cubo principal
      const mainCube = new THREE.Mesh(
        new THREE.BoxGeometry(3, 3, 3),
        new THREE.MeshPhysicalMaterial({
          color: currentModel.color,
          emissive: currentModel.emissive,
          roughness: 0.2,
          metalness: 0.8
        })
      )

      // Cubos internos flotantes
      for (let i = 0; i < 8; i++) {
        const innerCube = new THREE.Mesh(
          new THREE.BoxGeometry(0.5, 0.5, 0.5),
          new THREE.MeshBasicMaterial({
            color: currentModel.color,
            transparent: true,
            opacity: 0.6
          })
        )

        const angle = (i / 8) * Math.PI * 2
        const radius = 2.5
        innerCube.position.set(
          Math.cos(angle) * radius,
          Math.sin(angle) * radius,
          (Math.random() - 0.5) * 2
        )

        cubeGroup.add(innerCube)
      }

      cubeGroup.add(mainCube)
      group.add(cubeGroup)
      break

    case 'automation':
      // Geometría compleja con múltiples formas
      const torusKnot = new THREE.Mesh(
        new THREE.TorusKnotGeometry(1.5, 0.6, 100, 16),
        new THREE.MeshPhysicalMaterial({
          color: currentModel.color,
          emissive: currentModel.emissive,
          roughness: 0.1,
          metalness: 0.9,
          clearcoat: 1.0
        })
      )

      // Partículas alrededor
      const particleGeometry = new THREE.BufferGeometry()
      const particleCount = 50
      const positions = new Float32Array(particleCount * 3)

      for (let i = 0; i < particleCount; i++) {
        const i3 = i * 3
        const radius = 4 + Math.random() * 2
        const theta = Math.random() * Math.PI * 2
        const phi = Math.random() * Math.PI

        positions[i3] = radius * Math.sin(phi) * Math.cos(theta)
        positions[i3 + 1] = radius * Math.sin(phi) * Math.sin(theta)
        positions[i3 + 2] = radius * Math.cos(phi)
      }

      particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3))

      const particleMaterial = new THREE.PointsMaterial({
        color: currentModel.color,
        size: 0.1,
        transparent: true,
        opacity: 0.8
      })

      const particles = new THREE.Points(particleGeometry, particleMaterial)

      group.add(torusKnot)
      group.add(particles)
      break
  }

  return group
}

const loadAdvancedModel = () => {
  if (currentLoadedModel) {
    scene.remove(currentLoadedModel)
  }

  const geometryType = models3D.value[currentModelIndex.value].geometry
  currentLoadedModel = createAdvancedGeometry(geometryType)

  // Posicionar y animar el modelo
  currentLoadedModel.position.set(0, 0, 0)
  currentLoadedModel.rotation.set(0, 0, 0)

  scene.add(currentLoadedModel)
}

const createLightParticles = () => {
  const particleCount = 200
  const positions = new Float32Array(particleCount * 3)
  const colors = new Float32Array(particleCount * 3)
  const purple = new THREE.Color('#8A2BE2')
  const pink = new THREE.Color('#C77DFF')

  for (let i = 0; i < particleCount; i++) {
    const i3 = i * 3
    const radius = 4 + Math.random() * 8
    const theta = Math.random() * Math.PI * 2
    const phi = Math.acos((Math.random() * 2) - 1)

    positions[i3] = radius * Math.sin(phi) * Math.cos(theta)
    positions[i3 + 1] = radius * Math.sin(phi) * Math.sin(theta)
    positions[i3 + 2] = radius * Math.cos(phi)

    const mixedColor = purple.clone().lerp(pink, Math.random())
    mixedColor.toArray(colors, i3)
  }

  const geometry = new THREE.BufferGeometry()
  geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3))
  geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3))

  const material = new THREE.PointsMaterial({
    size: 0.15,
    vertexColors: true,
    blending: THREE.AdditiveBlending,
    transparent: true,
    depthWrite: false,
  })

  lightParticles = new THREE.Points(geometry, material)
  scene.add(lightParticles)
}

const init3DScene = () => {
  scene = new THREE.Scene()
  camera = new THREE.PerspectiveCamera(75, modelCanvas.value.clientWidth / modelCanvas.value.clientHeight, 0.1, 1000)
  camera.position.z = 5

  renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true })
  renderer.setSize(modelCanvas.value.clientWidth, modelCanvas.value.clientHeight)
  renderer.setPixelRatio(window.devicePixelRatio)
  modelCanvas.value.appendChild(renderer.domElement)

  // Luces
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.8)
  scene.add(ambientLight)
  const directionalLight = new THREE.DirectionalLight(0xffffff, 1)
  directionalLight.position.set(5, 10, 7.5)
  scene.add(directionalLight)

  // Controles
  controls = new OrbitControls(camera, renderer.domElement)
  controls.enableDamping = true
  controls.enableZoom = false // Desactivar zoom para mantener el tamaño
  controls.autoRotate = true
  controls.autoRotateSpeed = 1.0

  // Cargar modelo inicial
  loadAdvancedModel()

  // Crear partículas de luz
  createLightParticles()

  // Observador para cambio de modelo
  let lastIndex = currentModelIndex.value
  const animate = () => {
    requestAnimationFrame(animate)
    controls.update()
    if (lastIndex !== currentModelIndex.value) {
      loadAdvancedModel(models3D.value[currentModelIndex.value].geometry)
      lastIndex = currentModelIndex.value
    }

    // Animar partículas de luz
    if (lightParticles) {
      lightParticles.rotation.y += 0.001
      lightParticles.rotation.x += 0.0005
    }
    renderer.render(scene, camera)
  }
  animate()

  // Redimensionar
  window.addEventListener('resize', onWindowResize)
}

const onWindowResize = () => {
  if (modelCanvas.value) {
    camera.aspect = modelCanvas.value.clientWidth / modelCanvas.value.clientHeight
    camera.updateProjectionMatrix()
    renderer.setSize(modelCanvas.value.clientWidth, modelCanvas.value.clientHeight)
  }
}

// Lifecycle
onMounted(() => {
  // Iniciar animación de partículas
  const animationInterval = setInterval(animateParticles, 50)
  
  // Iniciar slider automático
  const sliderInterval = setInterval(nextModel, 5000) // Cambia de modelo cada 5 segundos

  // Iniciar escena 3D
  init3DScene()

  onUnmounted(() => {
    clearInterval(animationInterval)
    clearInterval(sliderInterval)
    window.removeEventListener('resize', onWindowResize)
  })
})
</script>

<style scoped>
/* Contenedor principal de landing */
.landing-container {
  min-height: 100vh;
  width: 100%;
  background: radial-gradient(ellipse at center, rgba(75, 0, 130, 0.1) 0%, transparent 70%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
  padding-top: 10vh; /* Aumentado para mejor centrado vertical */
  z-index: 2;
}

.landing-content {
  width: 100%;
  max-width: 1200px;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 90vh; /* Ajustado para el nuevo layout */
}

.model-carousel-container {
  width: 100%;
  max-width: 600px;
  height: 450px;
  position: relative;
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.hero-section {
  display: flex;
  width: 100%;
  height: 65vh; /* Altura principal */
  max-width: 1400px;
  align-items: center;
  margin-bottom: 15vh; /* Más espacio debajo */
}

.model-panel {
  width: 50%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.text-panel {
  width: 50%;
  padding-left: 4rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start; /* Alinear a la izquierda del panel */
  text-align: left;
}

.model-canvas {
  width: 100%;
  height: 100%;
  cursor: grab;
}

.model-name, .model-description {
  text-align: left;
  max-width: 100%;
}

.model-name {
  color: #ffffff;
  font-weight: 600;
  font-family: 'Orbitron', monospace;
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  font-size: 2rem; /* Más grande */
}

.model-description {
  color: #ffffff;
  font-size: 1rem;
  line-height: 1.5;
  opacity: 0.8;
  margin-bottom: 2rem;
  font-size: 1.1rem; /* Un poco más grande */
}

.particles-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 1;
}

.particle {
  position: absolute;
  border-radius: 50%;
  opacity: 0.6;
  animation: float 15s infinite linear;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) translateX(0px);
  }
  25% {
    transform: translateY(-100px) translateX(50px);
  }
  50% {
    transform: translateY(-50px) translateX(-50px);
  }
  75% {
    transform: translateY(-150px) translateX(25px);
  }
}

.landing-description {
  color: white;
  line-height: 1.6;
  margin-bottom: 2rem;
  position: relative;
}

.landing-cta {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.demo-section {
  padding: 100px 20px;
  background: linear-gradient(180deg, transparent 0%, rgba(75, 0, 130, 0.05) 100%);
}

.demo-container {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.demo-title {
  font-family: 'Orbitron', monospace;
  font-size: 2.5rem;
  color: white;
  margin-bottom: 3rem;
  position: relative;

  &::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 2px;
    background: linear-gradient(90deg, #4B0082, #8A2BE2);
  }
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.feature-card {
  background: rgba(10, 0, 20, 0.5);
  border: 1px solid rgba(138, 43, 226, 0.3);
  border-radius: 20px;
  padding: 2rem;
  text-align: center;
  transition: all 0.3s ease;
  backdrop-filter: blur(5px);

  &:hover {
    transform: translateY(-10px);
    border-color: #8A2BE2;
    box-shadow: 0 0 30px rgba(138, 43, 226, 0.3);
  }
}

.feature-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  background: linear-gradient(135deg, #4B0082, #8A2BE2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: white;
  margin-bottom: 1rem;
}

.feature-title {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: white;
  margin-bottom: 1rem;
}

.feature-description {
  color: white;
  line-height: 1.6;
}

.landing-footer {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 10px 20px;
  border-radius: 25px;
  font-size: 0.875rem;
  z-index: 10;
}

.btn-landing {
   margin: 0 10px;
   font-size: 1rem;
   padding: 0.8rem 1.5rem;

   i {
     margin-right: 10px;
   }
}

@keyframes logoRotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes botPulse {
  0%, 100% {
    filter: drop-shadow(0 0 15px rgba(75, 0, 130, 0.3));
    transform: scale(1);
  }
  50% {
    filter: drop-shadow(0 0 25px rgba(75, 0, 130, 0.5));
    transform: scale(1.05);
  }
}

@media (max-width: 768px) {
  .landing-container {
    min-height: 100vh;
    padding-top: 5vh;
  }

  .landing-content {
    padding: 2rem 1rem;
    min-height: 100vh;
    justify-content: center;
  }

 .hero-section {
    flex-direction: column;
    height: auto;
 }

 .model-panel {
    width: 100%;
    height: 300px;
    margin-bottom: 2rem;
 }

 .text-panel {
    width: 100%;
    margin-left: 0;
    padding: 0 1rem;
    align-items: center;
 }
 .model-name, .model-description {
    text-align: center;
  }

  .landing-cta {
    flex-direction: column;
    width: 90%;
    max-width: 350px;
    align-items: center;
  }

  .features-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .demo-section {
    padding: 50px 20px;
  }
}
</style>
