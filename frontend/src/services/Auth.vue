<template>
  <div class="auth-container">
    <div class="auth-bg-effects">
      <div class="shape" v-for="i in 60" :key="i" :style="getShapeStyle(i)"></div>
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
          <button :class="['tab-btn', { active: isLoginMode }]" type="button" @click="activateLogin()">
            <i class="fas fa-sign-in-alt"></i>
            Iniciar Sesión
          </button>
          <button :class="['tab-btn', { active: !isLoginMode }]" type="button" @click="activateRegister()">
            <i class="fas fa-user-plus"></i>
            Registrarse
          </button>
        </div>

        <!-- Formulario -->
        <form class="auth-form" @submit.prevent="handleSubmit">
          <transition-group name="form-field">
            <div v-if="!isLoginMode" class="form-group" key="empresa_nombre">
              <label for="empresa_nombre">Nombre de la Empresa *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-building"></i></span>
                <input
                  id="empresa_nombre"
                  name="empresa_nombre"
                  v-model="form.empresa_nombre"
                  type="text"
                  class="form-input"
                  :class="{ 'error': errors.empresa_nombre }"
                  placeholder="Ingrese el nombre de su empresa"
                  @input="clearError('empresa_nombre')"
                >
              </div>
              <div v-if="errors.empresa_nombre" class="error-message">{{ errors.empresa_nombre }}</div>
            </div>

            <div v-if="!isLoginMode" class="form-group" key="role">
              <label for="role">Rol *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-user-shield"></i></span>
                <select
                  id="role"
                  name="role"
                  v-model="form.roleId"
                  class="form-input"
                  :class="{ 'error': errors.roleId }"
                  @change="clearError('roleId')"
                >
                  <option value="">Selecciona un rol...</option>
                  <option :value="1">Superusuario</option>
                  <option :value="2">Gerente</option>
                  <option :value="3">Asistente</option>
                </select>
              </div>
              <div v-if="errors.roleId" class="error-message">{{ errors.roleId }}</div>
            </div>

            
            <div v-if="!isLoginMode" class="form-group" key="nombre_real">
              <label for="nombre_real">Nombre *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-id-card"></i></span>
                <input
                  id="nombre_real"
                  name="nombre_real"
                  v-model="form.nombre"
                  type="text"
                  class="form-input"
                  :class="{ 'error': errors.nombre }"
                  placeholder="Nombre"
                  @input="clearError('nombre')"
                >
              </div>
              <div v-if="errors.nombre" class="error-message">{{ errors.nombre }}</div>
            </div>

            <div v-if="!isLoginMode" class="form-group" key="apellido_real">
              <label for="apellido_real">Apellido *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-id-card"></i></span>
                <input
                  id="apellido_real"
                  name="apellido_real"
                  v-model="form.apellido"
                  type="text"
                  class="form-input"
                  :class="{ 'error': errors.apellido }"
                  placeholder="Apellido"
                  @input="clearError('apellido')"
                >
              </div>
              <div v-if="errors.apellido" class="error-message">{{ errors.apellido }}</div>
            </div>

            <div v-if="!isLoginMode" class="form-group" key="nombre_usuario">
              <label for="nombre_usuario">Nombre de usuario *</label>
              <div class="input-group">
                <span class="input-icon"><i class="fas fa-user"></i></span>
                <input
                  id="nombre_usuario"
                  name="nombre_usuario"
                  v-model="form.nombreUsuario"
                  type="text"
                  class="form-input"
                  :class="{ 'error': errors.nombreUsuario }"
                  placeholder="Ej: j.perez"
                  @input="onUsernameManualEdit"
                >
              </div>
              <div v-if="errors.nombreUsuario" class="error-message">{{ errors.nombreUsuario }}</div>
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
import { ref, reactive, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from '@/services'

// Composables
const router = useRouter()

// Estado reactivo
const isLoginMode = ref(true)
const isChangeMode = ref(false)
const isLoading = ref(false)
const successMessage = ref('')
const generalError = ref('')

// Formulario reactivo
const form = reactive({
  empresa_nombre: '',
  nombre: '',
  apellido: '',
  nombreUsuario: '',
  email: '',
  password: '',
  confirmPassword: '',
  rememberMe: false,
  roleId: '',
  currentPassword: '',
  newPassword: '',
  confirmNewPassword: ''
})
const usernameManuallyEdited = ref(false)

// Errores reactivos
const errors = ref({})

// Computed
const isFormValid = computed(() => {
  if (isChangeMode.value) {
    return form.currentPassword && form.newPassword && form.confirmNewPassword && form.newPassword === form.confirmNewPassword && form.newPassword.length >= 6
  } else if (isLoginMode.value) {
    return form.email && form.password
  } else {
    return form.email &&
           form.password &&
           form.confirmPassword &&
           form.empresa_nombre &&
           form.nombre &&
           form.apellido &&
           form.nombreUsuario &&
           form.roleId &&
           form.password === form.confirmPassword
  }
})

// Métodos
const activateLogin = () => {
  isLoginMode.value = true
  isChangeMode.value = false
  clearErrors()
  clearForm()
}

const activateRegister = () => {
  isLoginMode.value = false
  isChangeMode.value = false
  clearErrors()
  clearForm()
}

const activateChange = () => {
  isChangeMode.value = true
  isLoginMode.value = false
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
  form.empresa_nombre = ''
  form.nombre = ''
  form.apellido = ''
  form.nombreUsuario = ''
  form.email = ''
  form.password = ''
  form.confirmPassword = ''
  form.rememberMe = false
  usernameManuallyEdited.value = false
  form.currentPassword = ''
  form.newPassword = ''
  form.confirmNewPassword = ''
}

const validateForm = () => {
  clearErrors()
  const newErrors = {}

  if (!form.email) {
    newErrors.email = 'El correo electrónico es requerido'
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    newErrors.email = 'Ingrese un correo electrónico válido'
  }

  if (isChangeMode.value) {
    if (!form.currentPassword) {
      newErrors.currentPassword = 'Ingrese su contraseña actual'
    }
    if (!form.newPassword) {
      newErrors.newPassword = 'Ingrese la nueva contraseña'
    } else if (form.newPassword.length < 6) {
      newErrors.newPassword = 'La nueva contraseña debe tener al menos 6 caracteres'
    }
    if (!form.confirmNewPassword) {
      newErrors.confirmNewPassword = 'Confirme su nueva contraseña'
    } else if (form.newPassword !== form.confirmNewPassword) {
      newErrors.confirmNewPassword = 'Las contraseñas no coinciden'
    }
  }

  if (!form.password && !isChangeMode.value) {
    newErrors.password = 'La contraseña es requerida'
  } else if (form.password.length < 6) {
    newErrors.password = 'La contraseña debe tener al menos 6 caracteres'
  }

  if (!isLoginMode.value && !isChangeMode.value) {
    if (!form.empresa_nombre || !form.empresa_nombre.trim()) {
      newErrors.empresa_nombre = 'Ingrese el nombre de la empresa'
    }
    if (!form.nombre || !form.nombre.trim()) {
      newErrors.nombre = 'Ingrese su nombre'
    }
    if (!form.apellido || !form.apellido.trim()) {
      newErrors.apellido = 'Ingrese su apellido'
    }
    if (!form.nombreUsuario || !form.nombreUsuario.trim()) {
      newErrors.nombreUsuario = 'Ingrese un nombre de usuario'
    }
    if (!form.roleId) {
      newErrors.roleId = 'Seleccione un rol'
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
    if (isChangeMode.value) {
      const payload = { currentPassword: form.currentPassword, newPassword: form.newPassword }
      response = await authService.changePassword(payload)
    } else if (isLoginMode.value) {
      const authData = { email: form.email, password: form.password }
      response = await authService.login(authData)
    } else {
      const registerData = {
        email: form.email,
        password: form.password,
        roleId: form.roleId,
        nombre: form.nombre,
        apellido: form.apellido,
        nombre_usuario: form.nombreUsuario,
        nombre_empresa: form.empresa_nombre
      }
      response = await authService.register(registerData)
    }

    if (response?.success) {
      successMessage.value = isChangeMode.value
        ? '¡Contraseña actualizada correctamente!'
        : (isLoginMode.value ? '¡Inicio de sesión exitoso!' : '¡Cuenta creada exitosamente!')
      const role = response.user?.role || response.user?.rol || null
      if (!isChangeMode.value) {
        const target = role === 'almacen' || role === 'operador' ? '/inventario' : '/dashboard'
        router.push(target) // Usar la instancia de router definida en el setup
      }
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

const resetPassword = async () => {
  clearErrors()
  // Validar email antes de solicitar reset
  if (!form.email || !/\S+@\S+\.\S+/.test(form.email)) {
    errors.value.email = 'Ingrese un correo válido para enviar el enlace de recuperación'
    return
  }
  try {
    isLoading.value = true
    const resp = await authService.requestPasswordReset({ email: form.email })
    if (resp?.success) {
      successMessage.value = resp?.message || 'Te enviamos un correo con instrucciones para restablecer tu contraseña.'
    } else {
      generalError.value = resp?.message || 'No se pudo iniciar el proceso de recuperación'
    }
  } catch (e) {
    generalError.value = e?.message || 'Error al solicitar recuperación de contraseña'
  } finally {
    isLoading.value = false
  }
}

const getShapeStyle = (i) => {
  const size = Math.random() * 60 + 40; // 40px - 100px, más parecido a partículas
  const corner = Math.floor(Math.random() * 4);
  const r = (min, max) => Math.random() * (max - min) + min;
  const clamp = (v) => Math.max(0, Math.min(100, v));
  let top, left;
  if (corner === 0) { top = clamp(r(0, 8)); left = clamp(r(0, 8)); }
  else if (corner === 1) { top = clamp(r(0, 8)); left = clamp(r(92, 100)); }
  else if (corner === 2) { top = clamp(r(92, 100)); left = clamp(r(0, 8)); }
  else { top = clamp(r(92, 100)); left = clamp(r(92, 100)); }
  const animationDuration = Math.random() * 18 + 14;
  const animationDelay = Math.random() * 8;
  const z = Math.floor(Math.random() * 200) + 400; // 400px - 600px, aún más cerca
  const rx = Math.floor(Math.random() * 40) - 20;
  const ry = Math.floor(Math.random() * 40) - 20;
  const radius = Math.random() < 0.5 ? `${Math.floor(Math.random()*30)}%` : '50%';
  const c1 = Math.random() < 0.5 ? '#4B0082' : '#6B1FA8';
  const c2 = Math.random() < 0.5 ? '#8A2BE2' : '#C77DFF';
  return {
    width: `${size}px`,
    height: `${size}px`,
    top: `${top}%`,
    left: `${left}%`,
    borderRadius: radius,
    background: `radial-gradient(circle at 30% 30%, ${c2}FF 0%, ${c2}99 40%, transparent 70%)`,
    boxShadow: `0 0 ${Math.round(size*0.8)}px ${c2}55, 0 0 ${Math.round(size*0.4)}px ${c1}44`,
    animationDuration: `${animationDuration}s`,
    animationDelay: `${animationDelay}s`,
    '--t': `translateZ(${z}px) rotateX(${rx}deg) rotateY(${ry}deg)`
  };
};

// Username auto-generation
const slug = (s) => (s || '').toString().normalize('NFD').replace(/\p{Diacritic}/gu,'').toLowerCase().replace(/[^a-z0-9]+/g,'').trim()
const generateUsername = (nombre, apellido) => {
  const n = slug(nombre)
  const a = slug(apellido)
  if (!n && !a) return ''
  if (n && a) return `${n.charAt(0)}.${a}`
  return n || a
}
const onUsernameManualEdit = () => {
  usernameManuallyEdited.value = true
  clearError('nombreUsuario')
}
watch(() => form.nombre, (val) => {
  if (!usernameManuallyEdited.value) {
    form.nombreUsuario = generateUsername(val, form.apellido)
  }
})
watch(() => form.apellido, (val) => {
  if (!usernameManuallyEdited.value) {
    form.nombreUsuario = generateUsername(form.nombre, val)
  }
})
</script>

<style lang="scss" scoped>
/* Contenedor principal para centrado y scroll */
.auth-container {
  min-height: 100vh;
  width: 100%; /* Asegura que ocupe todo el ancho del contenedor padre */
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
  z-index: 3; /* por encima del card */
  pointer-events: none; /* no bloquea el formulario */
  perspective: 800px;
  transform-style: preserve-3d;
  overflow: hidden;
}

.shape {
  position: absolute;
  border-radius: 50%;
  filter: blur(0);
  animation: float-bg-3d 22s infinite ease-in-out;
  will-change: transform, opacity;
  transform: var(--t);
  opacity: 0.85;
}

@keyframes float-bg-3d {
  0% {
    transform: var(--t) translateY(0px) translateX(0px) scale(1);
    opacity: 0.85;
  }
  50% {
    transform: var(--t) translateY(-50px) translateX(25px) rotateZ(10deg) scale(1.05);
    opacity: 0.95;
  }
  100% {
    transform: var(--t) translateY(0px) translateX(0px) scale(1);
    opacity: 0.85;
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
  z-index: 2; /* debajo de las figuras, pero visible */
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