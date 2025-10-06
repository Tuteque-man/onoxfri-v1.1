# Sistema de Inventario Onoxfri - Backend PHP

Backend básico pero funcional desarrollado en PHP puro para el sistema de inventario onoxfri.

## Estructura del Proyecto

```
backend/php-8.2.12/
├── config/
│   └── database.php          # Configuración de conexión a base de datos
├── middleware/
│   └── AuthMiddleware.php    # Middleware de autenticación y autorización
├── models/
│   ├── BaseModel.php         # Modelo base con operaciones CRUD básicas
│   ├── User.php             # Modelo de usuarios
│   ├── Product.php          # Modelo de productos
│   ├── Category.php         # Modelo de categorías
│   └── Customer.php         # Modelo de clientes
├── controller/
│   ├── BaseController.php   # Controlador base con utilidades comunes
│   ├── AuthController.php   # Controlador de autenticación
│   ├── ProductController.php # Controlador de productos
│   └── CategoryController.php # Controlador de categorías
```

## Características

### Base de Datos
- Conexión PDO a MySQL/MariaDB
- Base de datos: `onoxfri`
- Configuración flexible de conexión
- Manejo de transacciones

### Autenticación y Autorización
- Sistema de login/logout
- Gestión de sesiones
- Control de permisos basado en roles
- Middleware de protección de rutas

### Modelos
- Patrón Active Record básico
- Operaciones CRUD completas
- Relaciones entre entidades
- Validaciones básicas

### Controladores
- Arquitectura RESTful
- Respuestas JSON estructuradas
- Manejo de errores consistente
- Logging de acciones

## Uso Básico

### Configuración de Base de Datos

1. Crear la base de datos `onoxfri` en MySQL/MariaDB
2. Ejecutar el archivo `database-scheme/scheme.sql` para crear las tablas
3. Configurar las credenciales en `config/database.php` si es necesario

### Inicio Rápido

```php
<?php
// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'middleware/AuthMiddleware.php';
require_once 'models/Product.php';

// Crear instancia de producto
$productModel = new Product();

// Obtener productos activos
$products = $productModel->getActiveWithStock();

// Crear nuevo producto
$newProduct = $productModel->create([
    'sku' => 'PROD-001',
    'name' => 'Producto de ejemplo',
    'price' => 100.00,
    'cost' => 60.00,
    'min_stock' => 5,
    'is_active' => 1
]);
?>
```

### Autenticación

```php
<?php
require_once 'controller/AuthController.php';

$authController = new AuthController();

// Login
$authController->handleRequest(); // POST /api/auth/login
// Body: {"email": "admin@example.com", "password": "password"}

// Logout
$authController->handleRequest(); // DELETE /api/auth/logout
?>
```

### API Endpoints

#### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registrar usuario
- `DELETE /api/auth/logout` - Cerrar sesión

#### Productos
- `GET /api/products` - Listar productos
- `GET /api/products/active` - Productos activos con stock
- `GET /api/products/{id}` - Obtener producto específico
- `GET /api/products/search?q=termino` - Buscar productos
- `GET /api/products/low-stock` - Productos con stock bajo
- `POST /api/products/create` - Crear producto
- `PUT /api/products/{id}` - Actualizar producto
- `DELETE /api/products/{id}` - Eliminar producto

#### Categorías
- `GET /api/categories` - Listar categorías
- `GET /api/categories/with-count` - Categorías con conteo de productos
- `POST /api/categories/create` - Crear categoría
- `PUT /api/categories/{id}` - Actualizar categoría
- `DELETE /api/categories/{id}` - Eliminar categoría

## Seguridad

- Todas las rutas requieren autenticación
- Sanitización de datos de entrada
- Validación de permisos por roles
- Logging de acciones del usuario
- Protección contra ataques comunes

## Desarrollo

### Agregar Nuevo Modelo

1. Crear archivo en `models/NombreModelo.php`
2. Extender `BaseModel`
3. Definir propiedades: `$table`, `$primaryKey`, `$fillable`

### Agregar Nuevo Controlador

1. Crear archivo en `controller/NombreController.php`
2. Extender `BaseController`
3. Implementar métodos HTTP según necesidades

## Notas

- Código PHP puro sin frameworks externos
- Compatible con PHP 8.2+
- Uso de PDO para seguridad en consultas
- Arquitectura modular y extensible
- Listo para integración con frontend Vue.js

## Próximos Pasos

- Implementar más controladores (Customer, Supplier, Reports)
- Agregar validaciones más avanzadas
- Implementar sistema de caché
- Agregar tests unitarios
- Documentación de API completa
# Sistema de Inventario Onoxfri - Backend PHP

Backend básico pero funcional desarrollado en PHP puro para el sistema de inventario onoxfri.

## ✅ Estructura Creada y Corregida

### Directorios y archivos principales:
- **config/database.php** - Configuración completa de conexión PDO a MySQL/MariaDB
- **middleware/AuthMiddleware.php** - Sistema de autenticación y autorización con sesiones
- **models/** - Modelos basados en tu esquema de base de datos:
  - BaseModel.php (clase base con operaciones CRUD mejoradas)
  - User.php (gestión de usuarios y roles - **Completamente corregido**)
  - Product.php (productos con gestión de stock e inventario)
  - Category.php (categorías con conteo de productos)
  - Customer.php (gestión de clientes)
  - Role.php (gestión de roles y permisos)
- **controller/** - Controladores con arquitectura RESTful:
  - BaseController.php (funcionalidades comunes)
  - AuthController.php (login/logout/registro)
  - ProductController.php (operaciones CRUD completas de productos)
  - CategoryController.php (gestión de categorías)
- **README.md** - Esta documentación

## 🔧 Correcciones Implementadas

### Modelo User.php - Problemas Solucionados:
- ✅ **Propiedad $user definida**: Ahora usa `$currentUser` con métodos apropiados
- ✅ **Validaciones completas**: Email único, formato válido, campos requeridos
- ✅ **Manejo de errores**: Try-catch en todos los métodos críticos
- ✅ **Múltiples roles**: Soporte completo para usuarios con múltiples roles
- ✅ **Métodos seguros**: `verifyPassword()` y `getUserRoles()` ahora funcionan correctamente

### BaseModel.php - Mejoras:
- ✅ **Manejo de errores mejorado**: Excepciones descriptivas
- ✅ **Validación de datos**: Respeta campos `$fillable`
- ✅ **Transacciones**: Soporte completo para operaciones atómicas

### AuthMiddleware.php - Actualizaciones:
- ✅ **Múltiples roles**: Compatible con `GROUP_CONCAT` de roles
- ✅ **Método hasRole()**: Funciona con arrays de roles

## 🚀 Características Implementadas

### Base de Datos
- Conexión PDO segura a la base de datos `onoxfri`
- Manejo de transacciones
- Configuración flexible

### Autenticación y Autorización
- Sistema de login/logout con sesiones
- Control de permisos basado en roles
- Soporte para múltiples roles por usuario
- Middleware de protección de rutas

### Modelos
- Patrón Active Record básico
- Operaciones CRUD completas
- Relaciones entre entidades
- Validaciones completas
- Manejo robusto de errores

### API RESTful
- Endpoints JSON estructurados
- Manejo de errores consistente
- Logging de acciones
- Sanitización de datos

## 📋 Endpoints Disponibles

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registrar usuario
- `DELETE /api/auth/logout` - Cerrar sesión

### Productos
- `GET /api/products` - Listar productos
- `GET /api/products/active` - Productos activos con stock
- `GET /api/products/{id}` - Obtener producto específico
- `GET /api/products/search?q=termino` - Buscar productos
- `GET /api/products/low-stock` - Productos con stock bajo
- `POST /api/products/create` - Crear producto
- `PUT /api/products/{id}` - Actualizar producto
- `DELETE /api/products/{id}` - Eliminar producto

### Categorías
- `GET /api/categories` - Listar categorías
- `GET /api/categories/with-count` - Categorías con conteo de productos
- `POST /api/categories/create` - Crear categoría
- `PUT /api/categories/{id}` - Actualizar categoría
- `DELETE /api/categories/{id}` - Eliminar categoría

## 🔐 Seguridad

- Todas las rutas requieren autenticación
- Sanitización de datos de entrada
- Validación de permisos por roles
- Logging de acciones del usuario
- Protección contra ataques comunes
- Hash seguro de contraseñas (bcrypt)

## 🛠️ Uso Básico

### Configuración de Base de Datos

1. Crear la base de datos `onoxfri` en MySQL/MariaDB
2. Ejecutar el archivo `database-scheme/scheme.sql` para crear las tablas
3. Configurar las credenciales en `config/database.php` si es necesario

### Ejemplo de Uso

```php
<?php
// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Product.php';

// Crear usuario
$userModel = new User();
try {
    $user = $userModel->createUser([
        'name' => 'Juan Pérez',
        'email' => 'juan@example.com',
        'password' => 'contraseña123'
    ]);

    // Asignar múltiples roles
    $userModel->assignRole($user['id'], 1); // Superusuario
    $userModel->assignRole($user['id'], 2); // Gerente

    echo "Usuario creado exitosamente";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

## 📚 Desarrollo

### Agregar Nuevo Modelo

1. Crear archivo en `models/NombreModelo.php`
2. Extender `BaseModel`
3. Definir propiedades: `$table`, `$primaryKey`, `$fillable`

### Agregar Nuevo Controlador

1. Crear archivo en `controller/NombreController.php`
2. Extender `BaseController`
3. Implementar métodos HTTP según necesidades

## ✨ Características Avanzadas

- **Múltiples roles por usuario**: Un usuario puede tener varios roles simultáneamente
- **Gestión granular de permisos**: Sistema de permisos detallado
- **Transacciones de base de datos**: Para operaciones atómicas
- **Validaciones robustas**: En todos los niveles
- **Logging completo**: Todas las acciones quedan registradas
- **Arquitectura escalable**: Fácil de extender y mantener

## 🚀 Próximos Pasos Sugeridos

- Implementar más controladores (Customer, Supplier, Reports)
- Agregar sistema de caché para mejorar rendimiento
- Implementar tests unitarios
- Agregar documentación de API con Swagger/OpenAPI
- Implementar rate limiting para la API
- Agregar sistema de backups automáticos

## 💡 Notas

- Código PHP puro sin frameworks externos
- Compatible con PHP 8.2+
- Uso de PDO para seguridad en consultas
- Arquitectura modular y extensible
- Listo para integración con frontend Vue.js
- Manejo robusto de errores en toda la aplicación