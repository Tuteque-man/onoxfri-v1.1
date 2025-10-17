# 🚀 ONOXFRI - Frontend Vue 3

## Plataforma de Inventario Inteligente con Diseño Futurista

### 📋 Descripción

Frontend moderno desarrollado con Vue 3, Vite y diseño tecnológico futurista para la plataforma de inventario inteligente ONOXFRI. Incluye integración completa con el backend PHP y características avanzadas de UX/UI.

## ✨ Características Principales

### 🎨 Diseño Tecnológico
- **Tema morado futurista** con colores principales #4B0082
- **Sidebar responsive** que se convierte en downbar en móviles
- **Logo ONOXFRI** con fuente Orbitron y efectos glitch
- **Animaciones sutiles** y efectos visuales avanzados
- **Tema dual** (claro/oscuro) con cambio dinámico

### 🛠️ Tecnologías Utilizadas
- **Vue 3** - Framework progresivo JavaScript
- **Vue Router** - Sistema de rutas SPA
- **Pinia** - Gestión de estado moderna
- **Vite** - Build tool ultra-rápido
- **SCSS** - Preprocesador CSS avanzado
- **Axios** - Cliente HTTP para integración API
- **Font Awesome** - Iconografía tecnológica
- **Animate.css** - Animaciones CSS

### 📱 Características Responsive
- **Sidebar izquierdo** en pantallas grandes (>992px)
- **Downbar inferior** en móviles y tablets
- **Navegación táctil** optimizada
- **Diseño mobile-first**

## 🚀 Instalación y Uso

### Prerrequisitos
- Node.js 16+
- npm o yarn
- Backend PHP ONOXFRI corriendo en puerto 80

### Instalación

```bash
# 1. Instalar dependencias
npm install

# 2. Iniciar servidor de desarrollo
npm run dev

# 3. Construir para producción
npm run build

# 4. Preview de producción
npm run preview
```

### Configuración del Backend
Asegúrate de que el backend PHP esté corriendo en:
```
http://localhost/onoxfri/backend/php-8.2.12/
```

## 📁 Estructura del Proyecto

```
frontend/
├── public/                 # Archivos estáticos
├── src/
│   ├── assets/            # Recursos
│   │   └── styles/        # Estilos SCSS
│   │       ├── variables.scss  # Variables de tema
│   │       └── main.scss       # Estilos principales
│   ├── components/        # Componentes Vue
│   │   └── Sidebar.vue    # Sidebar/Downbar responsive
│   ├── services/          # Servicios API
│   │   └── api.js         # Cliente HTTP y servicios
│   ├── views/             # Vistas/páginas
│   │   ├── Landing.vue    # Página de inicio
│   │   ├── Auth.vue       # Login/Registro
│   │   ├── Dashboard.vue  # Dashboard principal
│   │   ├── Empresas.vue   # Gestión de empresas
│   │   └── ...            # Otros módulos
│   ├── App.vue            # Componente raíz
│   ├── main.js            # Punto de entrada
│   └── style.css          # Estilos globales
├── index.html             # HTML principal
└── package.json           # Dependencias
```

## 🎯 Funcionalidades Implementadas

### ✅ Completadas
- [x] **Página de Landing** con efectos visuales y partículas
- [x] **Sistema de Autenticación** (login/registro integrado)
- [x] **Dashboard Principal** con estadísticas y gráficos
- [x] **Gestión de Empresas** con CRUD completo
- [x] **Sidebar Responsive** con navegación intuitiva
- [x] **Integración API** con backend PHP
- [x] **Tema Morado Futurista** con efectos visuales
- [x] **Animaciones Avanzadas** y transiciones suaves

### 🔄 Módulos Placeholder
- [ ] **Productos** - Gestión completa de inventario
- [ ] **Categorías** - Organización de productos
- [ ] **Inventario** - Control avanzado de stock
- [ ] **Reportes** - Dashboards y métricas
- [ ] **Usuarios** - Administración de usuarios
- [ ] **Configuración** - Opciones del sistema

## 🎨 Esquema de Colores

### Colores Principales
- **Morado Principal**: `#4B0082` (30% intensidad)
- **Morado Claro**: `#6B1FA8` (efectos y hover)
- **Morado Oscuro**: `#2D004D` (sombras profundas)

### Colores de Fondo
- **Blanco Puro**: `#FFFFFF`
- **Negro Puro**: `#000000`
- **Blanco 60%**: `rgba(255, 255, 255, 0.6)`
- **Negro 60%**: `rgba(0, 0, 0, 0.6)`

### Colores de Texto
- **Texto Principal**: `#000000` / `#FFFFFF`
- **Texto Secundario**: `rgba(0, 0, 0, 0.1)` / `rgba(255, 255, 255, 0.1)`

## 🔧 Configuración API

### Variables de Entorno
Crear archivo `.env` en la raíz:
```env
VITE_API_BASE_URL=http://localhost/onoxfri/backend/php-8.2.12
VITE_APP_NAME=ONOXFRI
```

### Endpoints Configurados
- `GET /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registrar usuario
- `GET /api/empresas` - Listar empresas
- `POST /api/empresas/create` - Crear empresa
- `PUT /api/empresas/{id}` - Actualizar empresa
- `DELETE /api/empresas/{id}` - Eliminar empresa

## 📱 Responsive Design

### Breakpoints
- **Mobile**: < 576px
- **Tablet**: 576px - 992px
- **Desktop**: > 992px

### Componentes Responsive
- **Sidebar**: Se convierte en downbar en móviles
- **Dashboard**: Se adapta a diferentes tamaños
- **Formularios**: Se optimizan para touch

## 🚀 Uso Básico

### 1. Página de Inicio
```javascript
// Navegar a landing
router.push('/')
```

### 2. Autenticación
```javascript
// Iniciar sesión
await authService.login({
  email: 'usuario@empresa.com',
  password: 'contraseña',
  empresa_id: 1
})
```

### 3. Gestión de Empresas
```javascript
// Crear empresa
await empresaService.createEmpresa({
  nombre_empresa: 'Mi Empresa S.A.',
  descripcion: 'Descripción de la empresa'
})
```

## 🎭 Efectos Visuales

### Animaciones Incluidas
- **Glitch Effect**: En el logo ONOXFRI
- **Partículas Flotantes**: En página de landing
- **Transiciones Fade**: Entre páginas
- **Hover Effects**: En botones y tarjetas
- **Neon Glow**: Sombras luminosas

### Fuentes Utilizadas
- **Orbitron**: Para logo y títulos principales
- **Space Grotesk**: Para texto principal
- **Rajdhani**: Para texto secundario

## 🔒 Seguridad

### Características de Seguridad
- **Autenticación JWT** con tokens seguros
- **Validación de formularios** en frontend y backend
- **Sanitización de datos** automática
- **Control de permisos** por roles de usuario
- **Logging de acciones** en audit_logs

## 📊 Estado del Proyecto

### ✅ Completado (90%)
- [x] Arquitectura base Vue 3 + Vite
- [x] Sistema de rutas completo
- [x] Diseño futurista implementado
- [x] Autenticación integrada
- [x] Gestión de empresas funcional
- [x] Dashboard con estadísticas
- [x] Responsive design completo
- [x] Integración API backend
- [x] Sistema de temas dinámico

### 🔄 Pendiente (10%)
- [ ] Módulos avanzados de inventario
- [ ] Reportes interactivos
- [ ] Notificaciones push
- [ ] Tests automatizados

## 🛠️ Desarrollo

### Comandos Útiles
```bash
# Desarrollo
npm run dev

# Producción
npm run build

# Preview producción
npm run preview

# Análisis de bundle
npm run build -- --analyze
```

### Estructura de Componentes
- **Views**: Páginas principales de la aplicación
- **Components**: Componentes reutilizables
- **Services**: Lógica de integración con APIs
- **Assets**: Estilos, imágenes y recursos

## 📞 Soporte

Para soporte técnico o consultas sobre el desarrollo:
1. Revisa la documentación en `README_EMPRESAS.md`
2. Consulta los ejemplos en `backend/php-8.2.12/examples_empresas.php`
3. Verifica la configuración del backend PHP

## 🎉 ¡Listo para Usar!

El frontend ONOXFRI está completamente configurado y listo para:
- ✅ Desarrollar módulos adicionales
- ✅ Personalizar el diseño según necesidades
- ✅ Integrar con sistemas externos
- ✅ Desplegar en producción

¡Disfruta de tu plataforma de inventario inteligente con diseño futurista! 🚀✨
