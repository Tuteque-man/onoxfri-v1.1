# Documentación Técnica: Esquema de Base de Datos (`scheme.sql`)

## Descripción General

El archivo `scheme.sql` define la estructura de la base de datos para el sistema de inventario digital **Onoxfri**. El diseño está alineado con los requerimientos funcionales y no funcionales del sistema, garantizando seguridad, escalabilidad, trazabilidad y facilidad de uso.

La base de datos está organizada en cinco módulos principales:

1. **Autenticación y Seguridad**
2. **Inventario Core**
3. **Precios y Promociones**
4. **Importación y Exportación**
5. **Reportes y Métricas (Dashboard)**

---

## 1. Autenticación y Seguridad

- **users**: Almacena los usuarios del sistema, con contraseñas cifradas.
- **roles**: Define los roles principales (Superusuario, Gerente, Asistente).
- **user_roles**: Relaciona usuarios con roles.
- **permissions**: Permisos granulares por funcionalidad.
- **role_permissions**: Relaciona roles con permisos.
- **user_sessions**: Historial de sesiones de usuario (inicio/cierre de sesión, IP, agente).
- **audit_logs**: Registro de acciones críticas para auditoría.

---

## 2. Inventario Core

- **products**: Productos del inventario, con campos clave como SKU, nombre, descripción, código de barras, categoría, precio, costo, stock mínimo y estado.
- **categories**: Categorías de productos.
- **stock_levels**: Niveles de stock por producto (cantidad disponible y reservada).
- **inventory_movements**: Movimientos de inventario (entradas, salidas, ajustes), con triggers para actualizar stock y generar alertas de stock bajo.
- **suppliers**: Proveedores asociados a los productos.
- **customers**: Clientes registrados.

---

## 3. Precios y Promociones

- **price_history**: Historial de cambios de precios por producto, con usuario responsable y motivo.
- **promotions**: Promociones aplicables a productos o categorías, con tipo de descuento, vigencia, prioridad y usuario creador.

---

## 4. Importación y Exportación

- **import_jobs**: Registro de importaciones/exportaciones masivas, con tipo, archivo, estado y usuario responsable.
- **custom_transactions**: Transacciones personalizadas para operaciones especiales.

---

## 5. Reportes y Métricas (Dashboard)

- **Vistas**:
  - `v_inventory_value`: Valor total del inventario por producto.
  - `v_low_stock`: Productos con stock bajo.
  - `v_inventory_value_by_category`: Valor total del inventario agrupado por categoría.
  - `v_stock_movements_by_period`: Movimientos de stock agrupados por periodo.

---

## Seguridad y Respaldo

- **backups**: Registro de respaldos automáticos de la base de datos.

---

## Índices y Rendimiento

- Índices en campos clave para búsquedas rápidas (SKU, nombre, código de barras, tipo de movimiento, fechas, etc.).
- Uso de claves foráneas para integridad referencial.
- Soporte para escalabilidad y concurrencia.

---

## Notas Técnicas

- Todas las contraseñas se almacenan cifradas.
- El sistema soporta auditoría de acciones y sesiones.
- El diseño permite la importación/exportación eficiente de datos en formato Excel.
- El esquema está preparado para soportar al menos 10,000 productos activos y 10 usuarios concurrentes.
- Cumple con los principios DRY y KISS para facilitar el mantenimiento.

---

## Recomendaciones de Uso

- Realizar respaldos periódicos utilizando la tabla `backups`.
- Mantener actualizados los permisos y roles según la evolución de las funcionalidades.
- Utilizar las vistas para alimentar dashboards y reportes sin afectar el rendimiento de las tablas principales.

---

## Actualizaciones

Para modificar la estructura, se recomienda seguir las convenciones y agregar comentarios descriptivos en el SQL para facilitar futuras migraciones.

# Esquemas de Base de Datos - Sistema de Inventario Onoxfri

Este directorio contiene diferentes versiones del esquema de base de datos para facilitar la importación en phpMyAdmin.

## 📁 Archivos Disponibles

### 1. `scheme.sql` (Original)
- **Archivo original** con comentarios y metadatos de phpMyAdmin
- Incluye todos los elementos: tablas, vistas, triggers, índices, claves foráneas
- **No recomendado** para pegar directamente en phpMyAdmin debido a comentarios

### 2. `scheme_clean.sql` (Limpio)
- Versión sin comentarios pero mantiene toda la funcionalidad
- Incluye triggers, vistas y procedimientos almacenados
- **Compatible** con phpMyAdmin pero puede requerir ejecución por partes

### 3. `scheme_phpmyadmin.sql` (Optimizado para phpMyAdmin)
- Versión limpia sin comentarios ni elementos problemáticos
- Mantiene vistas y funcionalidades avanzadas
- **Recomendado** para importar en phpMyAdmin

### 4. `scheme_basic.sql` (Básico)
- **Solo tablas esenciales** sin triggers ni vistas complejas
- Excluye elementos que pueden causar problemas en phpMyAdmin
- **Más seguro** para entornos con restricciones

## 🚀 Recomendaciones de Uso

### Para phpMyAdmin (Recomendado):
```sql
-- Usa scheme_phpmyadmin.sql
-- Copia y pega todo el contenido directamente
```

### Para MySQL Workbench o línea de comandos:
```sql
-- Usa scheme.sql (original)
mysql -u usuario -p onoxfri < scheme.sql
```

### Para entornos con restricciones:
```sql
-- Usa scheme_basic.sql
-- Solo tablas básicas sin elementos avanzados
```

## ⚠️ Notas Importantes

1. **Cambia las contraseñas**: Los usuarios demo tienen contraseñas hash que debes reemplazar
2. **Configura permisos**: Asegúrate de que el usuario de MySQL tenga permisos adecuados
3. **Backup primero**: Siempre realiza un backup antes de importar esquemas

## 🔧 Solución de Problemas

### Si phpMyAdmin muestra errores:
- Usa `scheme_basic.sql` (más compatible)
- Ejecuta las instrucciones por secciones
- Verifica permisos del usuario MySQL

### Si faltan funcionalidades:
- Usa `scheme_phpmyadmin.sql` para funcionalidades completas
- Agrega manualmente triggers y vistas si es necesario

## 📋 Funcionalidades Incluidas

### Todas las versiones incluyen:
- ✅ Tablas básicas (users, products, categories, etc.)
- ✅ Índices y claves foráneas
- ✅ Datos de demostración
- ✅ Relaciones entre tablas

### Versiones avanzadas agregan:
- ✅ Triggers automáticos de inventario
- ✅ Vistas para reportes
- ✅ Procedimientos almacenados
- ✅ Sistema de permisos granular

## 🎯 Versión Recomendada

Para la mayoría de usuarios, recomiendo usar **`scheme_phpmyadmin.sql`** ya que:
- Está optimizado para phpMyAdmin
- Incluye todas las funcionalidades
- No tiene comentarios problemáticos
- Es fácil de copiar y pegar

## 🔐 Usuarios por Defecto

Después de importar, tendrás estos usuarios:
- **admin@example.com** - Superusuario (todos los permisos)
- **gerente@example.com** - Gerente (permisos de gestión)
- **asistente@example.com** - Asistente (permisos básicos)

**Nota**: Cambia las contraseñas después del primer inicio de sesión.