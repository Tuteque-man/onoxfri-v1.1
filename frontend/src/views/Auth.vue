<template>
  <div class="auth-container">
    <div class="auth-bg-effects">
      <div class="shape" v-for="i in 10" :key="i" :style="getShapeStyle(i)"></div>
    </div>
    <div class="auth-card animate-fade-in">
      <div class="auth-header">
        <div class="auth-logo" v-if="!isLoading">
          <img src="@/assets/img/l1.png" alt="ONOXFRI Logo" class="auth-logo-img" />
        </div>
          <p class="auth-subtitle">
            {{ isLoginMode ? 'Portal de Acceso' : 'Únete a la revolución del inventario' }}
          </p>
        </div>

        <!-- Tabs Login / Registro -->
        <div class="auth-tabs">
          <button :class="['tab-btn', { active: isLoginMode }]" type="button" @click="setLoginMode(true)">
            <i class="fas fa-sign-in-alt"></i>
            Iniciar Sesión
          </button>
          <button :class="['tab-btn', { active: !isLoginMode }]" type="button" @click="setLoginMode(false)">
            <i class="fas fa-user-plus"></i>
            Registrarse
          </button>
        </div>

        <!-- Formulario -->
        <form class="auth-form" @submit.prevent="handleSubmit">
          <transition-group name="form-field">
            <!-- Empresa (solo login) -->
            <div v-if="isLoginMode" class="form-group" key="empresa_login">
              <label for="empresa_login">Empresa *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-building"></i></span>
                <input
                  id="empresa_login"
                  name="empresa_login"
                  v-model="form.empresa_nombre"
                  type="text"
                  class="form-input"
                  :placeholder="'Nombre de la empresa'"
                >
              </div>
            </div>
            <!-- Campo de empresa (solo en registro) -->
            <div v-if="!isLoginMode" class="form-group" key="empresa">
              <label for="empresa">Nombre de la Empresa *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-building"></i></span>
                <select
                  id="empresa"
                  name="empresa"
                  v-model="form.empresa_id"
                  class="form-input"
                  :class="{ 'error': errors.empresa_id }"
                  @change="clearError('empresa_id')"
                >
                  <option value="">Seleccionar empresa existente...</option>
                  <option v-for="empresa in empresas" :key="empresa.id" :value="empresa.id">
                    {{ empresa.nombre_empresa }}
                  </option>
                  <option value="new">+ Crear nueva empresa</option>
                </select>
              </div>
              <div v-if="errors.empresa_id" class="error-message">{{ errors.empresa_id }}</div>
            </div>

            <!-- Campo para nueva empresa -->
            <div v-if="!isLoginMode && form.empresa_id === 'new'" class="form-group" key="nueva_empresa">
              <label for="nueva_empresa">Nombre de la Nueva Empresa *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-plus-circle"></i></span>
                <input
                  id="nueva_empresa"
                  name="nueva_empresa"
                  v-model="form.nueva_empresa"
                  type="text"
                  class="form-input"
                  :class="{ 'error': errors.nueva_empresa }"
                  placeholder="Ingrese el nombre de su empresa"
                  @input="clearError('nueva_empresa')"
                >
              </div>
              <div v-if="errors.nueva_empresa" class="error-message">{{ errors.nueva_empresa }}</div>
            </div>
          </transition-group>

          <!-- Campo de email -->
          <div class="form-group">
            <label :for="isLoginMode ? 'login_email' : 'register_email'">
              Correo Electrónico *
            </label>
            <div class="input-group">
              <span class="input-icon"><i class="fas fa-envelope"></i></span>
              <input
                :id="isLoginMode ? 'login_email' : 'register_email'"
                :name="isLoginMode ? 'login_email' : 'register_email'"
                v-model="form.email"
                type="email"
                class="form-input"
                :class="{ 'error': errors.email }"
                :placeholder="isLoginMode ? 'Ingrese su email' : 'ejemplo@empresa.com'"
                @input="clearError('email')"
              >
            </div>
            <div v-if="errors.email" class="error-message">
              {{ errors.email }}
            </div>
          </div>

          <!-- Campo de contraseña -->
          <div class="form-group">
            <label :for="isLoginMode ? 'login_password' : 'register_password'">
              Contraseña *
            </label>
            <div class="input-group">
              <span class="input-icon"><i class="fas fa-lock"></i></span>
              <input
                :id="isLoginMode ? 'login_password' : 'register_password'"
                :name="isLoginMode ? 'login_password' : 'register_password'"
                v-model="form.password"
                type="password"
                class="form-input"
                :class="{ 'error': errors.password }"
                :placeholder="isLoginMode ? 'Ingrese su contraseña' : 'Mínimo 6 caracteres'"
                @input="clearError('password')"
              >
            </div>
            <div v-if="errors.password" class="error-message">
              {{ errors.password }}
            </div>
          </div>

          <!-- Confirmar contraseña (solo registro) -->
          <transition name="form-field">
            <div v-if="!isLoginMode" class="form-group" key="confirm_password">
              <label for="confirm_password">Confirmar Contraseña *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-check-double"></i></span>
                <input
                  id="confirm_password"
                  name="confirm_password"
                  v-model="form.confirmPassword"
                  type="password"
                  class="form-input"
                  :class="{ 'error': errors.confirmPassword }"
                  placeholder="Repita su contraseña"
                  @input="clearError('confirmPassword')"
                >
              </div>
              <div v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</div>
            </div>
          </transition>

          

          <!-- Botones de acción -->
          <div class="form-actions">
            <div class="main-action-group">
              <button
                type="submit"
                class="btn-futuristic"
                :disabled="isLoading"
              >
                <span v-if="isLoading" class="loading-futuristic"></span>
                <i v-if="!isLoading" :class="isLoginMode ? 'fas fa-sign-in-alt' : 'fas fa-user-plus'"></i>
                {{ isLoading ? 'Procesando...' : (isLoginMode ? 'Iniciar Sesión' : 'Crear Cuenta') }}
              </button>
              <button v-if="isLoginMode" type="button" class="btn-ghost-futuristic" @click="resetPassword" :disabled="isLoading">
                ¿Olvidaste tu contraseña?
              </button>
            </div>
          </div>

          <!-- Mensaje de éxito -->
          <div v-if="successMessage" class="success-message animate-fade-in">
            <i class="fas fa-check-circle"></i>
            {{ successMessage }}
          </div>

          <!-- Mensaje de error general -->
          <div v-if="generalError" class="error-message animate-fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            {{ generalError }}
          </div>
        </form>
      </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { authService, empresaService } from '@/services/api.js'

// Composables
const router = useRouter()

// Estado reactivo
const isLoginMode = ref(true)
const isLoading = ref(false)
const empresas = ref([])
const successMessage = ref('')
const generalError = ref('')

// Formulario reactivo
const form = reactive({
  empresa_id: '',
  nueva_empresa: '',
  empresa_nombre: '',
  email: '',
  password: '',
  confirmPassword: '',
  rememberMe: false
})

// Errores reactivos
const errors = ref({})

// Computed
const isFormValid = computed(() => {
  if (isLoginMode.value) {
    return form.email && form.password
  } else {
    return form.email &&
           form.password &&
           form.confirmPassword &&
           (form.empresa_id || form.nueva_empresa) &&
           form.password === form.confirmPassword
  }
})

// Métodos
const toggleMode = () => {
  isLoginMode.value = !isLoginMode.value
  clearErrors()
  clearForm()
}

const setLoginMode = (login) => {
  isLoginMode.value = login
  clearErrors()
  clearForm()
}

const clearErrors = () => {
  errors.value = {}
  generalError.value = ''
  successMessage.value = ''
}

const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
}

const clearForm = () => {
  form.empresa_id = ''
  form.nueva_empresa = ''
  form.empresa_nombre = ''
  form.email = ''
  form.password = ''
  form.confirmPassword = ''
  form.rememberMe = false
}

const validateForm = () => {
  clearErrors()
  const newErrors = {}

  if (!form.email) {
    newErrors.email = 'El correo electrónico es requerido'
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    newErrors.email = 'Ingrese un correo electrónico válido'
  }

  if (!form.password) {
    newErrors.password = 'La contraseña es requerida'
  } else if (form.password.length < 6) {
    newErrors.password = 'La contraseña debe tener al menos 6 caracteres'
  }

  if (!isLoginMode.value) {
    if (!form.empresa_id) {
      newErrors.empresa_id = 'Seleccione o cree una empresa'
    }
    if (form.empresa_id === 'new' && !form.nueva_empresa.trim()) {
      newErrors.nueva_empresa = 'Ingrese el nombre de la nueva empresa'
    }
    if (!form.confirmPassword) {
      newErrors.confirmPassword = 'Confirme su contraseña'
    } else if (form.password !== form.confirmPassword) {
      newErrors.confirmPassword = 'Las contraseñas no coinciden'
    }
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) return

  isLoading.value = true
  generalError.value = ''
  successMessage.value = ''

  try {
    let response
    if (isLoginMode.value) {
      const authData = { email: form.email, password: form.password }
      response = await authService.login(authData)
    } else {
      // Registro: crear empresa si corresponde y luego registrar
      let empresaId = form.empresa_id
      if (empresaId === 'new') {
        const empresaPayload = {
          nombre_empresa: form.nueva_empresa.trim(),
          descripcion: `Empresa creada durante registro de ${form.email}`
        }
        const empresaResp = await empresaService.createEmpresa(empresaPayload)
        if (!empresaResp?.success) throw new Error('Error al crear la empresa')
        empresaId = empresaResp.data?.id
      }
      const registerData = { email: form.email, password: form.password, empresa_id: empresaId }
      response = await authService.register(registerData)
    }

    if (response?.success) {
      successMessage.value = isLoginMode.value ? '¡Inicio de sesión exitoso!' : '¡Cuenta creada exitosamente!'
      const role = response.user?.role || response.user?.rol || null
      const target = role === 'almacen' || role === 'operador' ? '/inventario' : '/dashboard'
      const router = useRouter()
      router.push(target)
    } else {
      generalError.value = response?.message || 'Error en la operación'
    }
  } catch (error) {
    console.error('[Auth] exception:', error)
    generalError.value = error.message || 'Error inesperado'
  } finally {
    isLoading.value = false
  }
}

const resetPassword = () => {
  // Implementar funcionalidad de recuperación de contraseña
  alert('Funcionalidad de recuperación de contraseña próximamente')
}

const loadEmpresas = async () => {
  try {
    const token = localStorage.getItem('onoxfri_token')
    const userStr = localStorage.getItem('onoxfri_user')
    const user = userStr ? JSON.parse(userStr) : null

    // Si no está autenticado, no llamar al backend para evitar 401
    if (!token) {
      empresas.value = []
      // En modo registro, forzar por defecto crear nueva empresa
      if (!isLoginMode.value && !form.empresa_id) {
        form.empresa_id = 'new'
      }
      return
    }

    const role = user?.role || user?.rol || ''
    let empresasData = []

    if (role === 'Superusuario' || role === 'Gerente') {
      empresasData = await empresaService.getAllEmpresas()
    } else {
      empresasData = await empresaService.getMyEmpresas()
    }

    empresas.value = empresasData || []
  } catch (error) {
    console.error('Error al cargar empresas:', error)
  }
}

const getShapeStyle = (i) => {
  const size = Math.random() * 150 + 50;
  const top = Math.random() * 100;
  const left = Math.random() * 100;
  const animationDuration = Math.random() * 20 + 15;
  const animationDelay = Math.random() * 10;

  return {
    width: `${size}px`,
    height: `${size}px`,
    top: `${top}%`,
    left: `${left}%`,
    animationDuration: `${animationDuration}s`,
    animationDelay: `${animationDelay}s`,
  };
};

// Lifecycle
onMounted(() => {
  loadEmpresas()
})
</script>

<style lang="scss" scoped>
/* Contenedor principal para centrado y scroll */
.auth-container {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem; /* tighter */
  box-sizing: border-box;
  position: relative;
  overflow: hidden;
}

.auth-bg-effects {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

.shape {
  position: absolute;
  background: rgba(75, 0, 130, 0.06); /* softer */
  border-radius: 50%;
  animation: float-bg 20s infinite ease-in-out;
  filter: blur(70px); /* slightly reduced */
}

@keyframes float-bg {
  0% {
    transform: translateY(0px) translateX(0px) scale(1);
  }
  50% {
    transform: translateY(-40px) translateX(20px) scale(1.1);
  }
  100% {
    transform: translateY(0px) translateX(0px) scale(1);
  }
}

.auth-card {
  background: rgba(10, 0, 20, 0.27);
  border-radius: 16px;
  padding: 2rem 1.5rem; /* tighter */
  width: 100%;
  max-width: 420px;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(15px);
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  border: 1px solid transparent;
  background-clip: padding-box;
  z-index: 1;
  animation: neon-pulse 6s infinite alternate; /* calmer */
}

.auth-card::before {
  content: '';
  position: absolute;
  top: 0; right: 0; bottom: 0; left: 0;
  z-index: -1;
  margin: -1px;
  border-radius: inherit;
  background: linear-gradient(135deg, #8A2BE2, #4B0082);
}

@keyframes neon-pulse {
  from { box-shadow: 0 0 20px rgba(138, 43, 226, 0.2), 0 15px 50px rgba(0, 0, 0, 0.4); }
  to { box-shadow: 0 0 35px rgba(138, 43, 226, 0.4), 0 15px 50px rgba(0, 0, 0, 0.4); }
}

.auth-header {
  text-align: center;
  margin-bottom: 1rem; /* tighter */
}

/* Tabs Login/Registro */
.auth-tabs {
  width: 100%;
  display: flex;
  gap: 0.5rem;
  margin: 0 0 1rem 0;
}

.tab-btn {
  flex: 1;
  padding: 0.6rem 0.9rem;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(138, 43, 226, 0.25);
  color: #ffffff;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab-btn:hover { filter: brightness(1.05); }

.tab-btn.active {
  background: linear-gradient(135deg, #4B0082, #6B1FA8);
  border-color: transparent;
  box-shadow: 0 0 18px rgba(75, 0, 130, 0.25);
}

.auth-logo-img {
  width: 80px;
  height: 80px;
  object-fit: contain;
  margin: 0 auto 0.75rem; /* tighter */
  display: block;
  filter: drop-shadow(0 0 15px rgba(75, 0, 130, 0.3));
  animation: logoPulse 3.5s ease-in-out infinite; /* calmer */
}

@keyframes logoPulse {
  0%, 100% {
    filter: drop-shadow(0 0 15px rgba(75, 0, 130, 0.3));
    transform: scale(1);
  }
  50% {
    filter: drop-shadow(0 0 25px rgba(75, 0, 130, 0.5));
    transform: scale(1.05);
  }
}

.auth-subtitle {
  color: rgb(255, 255, 255) !important; /* enforce white */
  opacity: 0.95;
  font-size: 1rem;
  font-family: 'Orbitron', monospace;
  margin-bottom: 0;
}

/* Estilo de Switch moderno */
.auth-mode-selector {
  width: 100%;
  margin-bottom: 2rem;
}

.switch {
  position: relative;
  display: inline-block;
  width: 100%;
  height: 50px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.07);
  transition: .4s;
  border-radius: 12px;
  display: flex;
  align-items: center;
}

.slider:before {
  position: absolute;
  content: "";
  height: calc(100% - 8px);
  width: 50%;
  left: 4px;
  bottom: 4px;
  background: linear-gradient(135deg, #4B0082, #6B1FA8);
  transition: .4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
  border-radius: 8px;
  box-shadow: 0 0 20px rgba(75, 0, 130, 0.3);
}

input:checked + .slider:before {
  transform: translateX(calc(100% - 8px));
}

.slider-text {
  width: 50%;
  text-align: center;
  color: #ccc;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  z-index: 1;
  transition: color 0.4s ease;
}

input:checked ~ .slider .login-text,
input:not(:checked) ~ .slider .register-text {
  color: #ccc;
}

input:not(:checked) ~ .slider .login-text,
input:checked ~ .slider .register-text {
  color: white;
}

.auth-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.8rem; /* tighter */
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.form-group > label {
  color: #ffffff !important;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 0.92rem;
}

.input-wrapper {
  position: relative;
}

/* New input-group with left icon inline */
.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: static;
  left: auto;
  top: auto;
  transform: none;
  color: rgba(138, 43, 226, 0.85);
  pointer-events: none;
  margin-right: 0.5rem; /* 8px spacing between icon and input */
}

.input-wrapper i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(138, 43, 226, 0.75);
  transition: color 0.3s ease;
}

.form-input {
  padding: 0.65rem 1rem;
  padding-left: 2.8rem;
  border-radius: 8px;
  border: 1px solid rgba(138, 43, 226, 0.35); /* stronger */
  background: rgba(0,0,0,0.35); /* darker */
  color: #ffffff;
  font-size: 0.975rem;
  outline: none;
  transition: all 0.3s ease;
  width: 100%;
  box-sizing: border-box;
}

/* When using input-group (flex icon), remove extra left padding */
.input-group > .form-input {
  padding-left: 1rem;
}

/* Placeholders en morado (acento) */
.form-input::placeholder { color: var(--accent-secondary) !important; opacity: 1; }
.form-input::-webkit-input-placeholder { color: var(--accent-secondary) !important; opacity: 1; }
.form-input::-moz-placeholder { color: var(--accent-secondary) !important; opacity: 1; }
.form-input:-ms-input-placeholder { color: var(--accent-secondary) !important; opacity: 1; }
.form-input::-ms-input-placeholder { color: var(--accent-secondary) !important; opacity: 1; }

/* Extra specificity inside auth card */
.auth-card .form-group > label { color: #ffffff !important; }
.auth-card .form-input::placeholder { color: var(--accent-secondary) !important; }
.auth-card .form-input { color: #ffffff; }

.form-input:focus {
  border-color: #6B1FA8;
  background: rgba(0,0,0,0.45);
  box-shadow: 0 0 12px rgba(138, 43, 226, 0.25), 0 0 0 1px #8A2BE2;
}

.input-wrapper:focus-within i {
  color: #C77DFF;
}

.form-input.error {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
}

.checkbox-group {
  display: flex;
  align-items: center;
  margin-top: -0.5rem; /* Reduce el espacio superior */
}

.checkbox-label {
  position: relative;
  display: inline-flex;
  align-items: center;
  padding-left: 35px;
  cursor: pointer;
  font-size: 1rem;
  color: white;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.checkbox-label input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 24px;
  width: 24px;
  border: 2px solid #4B0082;
  border-radius: 6px;
  background: transparent;
  transition: all 0.3s ease;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
  background: linear-gradient(135deg, #4B0082, #6B1FA8);
  box-shadow: 0 0 15px rgba(138, 43, 226, 0.4);
}

.checkmark::after {
  content: '';
  position: absolute;
  left: 7px;
  top: 2px;
  width: 6px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg) scale(0);
  transition: transform 0.3s;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
  transform: rotate(45deg) scale(1);
}

.form-actions {
  display: flex;
  flex-direction: column;
  margin-top: 0.25rem; /* tighter */
  width: 100%;
}

.main-action-group {
  display: flex;
  flex-direction: column;
  width: 100%;
  gap: 0.6rem;
}

.btn-futuristic {
  flex: 1;
  padding: 0.7rem 1.1rem;
  border: none;
  border-radius: 8px;
  background: linear-gradient(135deg, #4B0082, #6B1FA8);
  color: #fff;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 0.98rem;
  cursor: pointer;
  box-shadow: 0 0 16px rgba(75, 0, 130, 0.18);
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-futuristic:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 0 30px rgba(138, 43, 226, 0.4);
}

.btn-futuristic:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-ghost-futuristic {
  background: transparent;
  color: #ffffff !important; /* link '¿Olvidaste tu contraseña?' en blanco */
  border: none;
  font-size: 0.9rem;
  cursor: pointer;
  text-decoration: none;
  padding: 0.25rem;
  transition: color 0.2s;
}

.btn-ghost-futuristic:hover {
  color: #ffffff !important;
}

.loading-futuristic {
  width: 22px;
  height: 22px;
  border: 3px solid rgba(75, 0, 130, 0.2);
  border-top: 3px solid #4B0082;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  display: inline-block;
}

@keyframes spin {
  0% { transform: rotate(0deg);}
  100% { transform: rotate(360deg);}
}

.error-message {
  color: #ff667a; /* clearer on dark */
  font-size: 0.9rem;
  margin-top: 0.1rem;
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.success-message {
  color: #3bd16f;
  font-size: 0.9rem;
  margin-top: 0.75rem;
  padding: 0.6rem;
  background: rgba(40, 167, 69, 0.08);
  border: 1px solid rgba(40, 167, 69, 0.2);
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Transiciones de formulario */
.form-field-enter-active,
.form-field-leave-active {
  transition: all 0.4s ease;
  max-height: 100px;
}
.form-field-enter-from,
.form-field-leave-to {
  opacity: 0;
  transform: translateY(-10px);
  max-height: 0;
  margin-top: 0;
  margin-bottom: 0;
  padding-top: 0;
  padding-bottom: 0;
}

/* Responsive */
@media (max-width: 600px) {
  .auth-container {
    padding: 0.85rem; /* tighter */
  }

  .auth-card {
    padding: 1.5rem 1.25rem; /* tighter */
    max-width: 100%;
  }

  .auth-logo-img {
    width: 60px;
    height: 60px;
  }

  .auth-subtitle {
    font-size: 0.95rem;
  }

  .switch {
    height: 45px;
  }

  .form-input {
    font-size: 0.95rem;
  }

  .form-actions {
    flex-direction: column;
    gap: 0.6rem;
  }
}

</style>