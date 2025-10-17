Este documento detalla los requerimientos funcionales y no funcionales (RF y RNF) para el desarrollo del nuevo sistema de inventario digital, basado en los objetivos de mantener la familiaridad de Excel, mejorar la seguridad y optimizar la gestión operativa.

I. Módulos del Sistema  
El sistema se estructura en cinco módulos principales para facilitar el desarrollo, mantenimiento y la asignación clara de funcionalidades:  
- Módulo de Autenticación y Seguridad: Gestión de usuarios, roles y acceso.  
- Módulo de Inventario Core: Gestión principal de productos y stock.  
- Módulo de Precios y Promociones: Control avanzado sobre la fijación de precios y ofertas.  
- Módulo de Importación y Exportación: Manejo de datos masivos en formato Excel.  
- Módulo de Reportes y Métricas (Panel Principal): Visualización de rendimiento e informes clave.

II. Requerimientos Funcionales (RF)  
Los requerimientos funcionales definen las capacidades específicas que el sistema debe ejecutar.

**Módulo 1: Autenticación y Seguridad**  
El sistema debe permitir el inicio de sesión seguro mediante credenciales únicas. Es fundamental la gestión de roles (Gerente, Asistente, Superusuario) con asignación de permisos específicos a nivel de funcionalidad. Al iniciar, el sistema debe mostrar una página principal personalizada según el rol del usuario (ejemplo: Panel Principal con métricas para Gerente). El Superusuario debe tener la capacidad de crear, modificar y desactivar cuentas de usuario. Además, el sistema registrará un historial de accesos y acciones críticas para fines de auditoría.

**Perfil de Usuario**  
Cada usuario contará con un perfil editable donde podrá visualizar y modificar su información personal, incluyendo su nombre y el nombre de la empresa. El perfil mostrará el rol que está utilizando actualmente y su estado de conexión (en línea o no). Además, el sistema permitirá ver la lista de usuarios conectados en tiempo real, indicando claramente quiénes están activos en el sistema y quiénes no.

**Módulo 2: Inventario Core**  
El sistema debe presentar una Tabla de Inventario editable en línea, imitando la interfaz y la simplicidad de Excel. Esta tabla debe permitir la creación, visualización, edición y eliminación (CRUD) de productos, incluyendo campos como Nombre, SKU, Categoría y Stock Actual. Para facilitar la navegación, la tabla debe incluir filtros dinámicos, capacidad de ordenamiento por cualquier columna y una barra de búsqueda global. Una funcionalidad crítica es la generación de alertas de stock bajo cuando las cantidades caigan por debajo de un umbral predefinido. Finalmente, el usuario solo podrá visualizar la información básica de los productos y seleccionar ítems para el proceso de pago.

**Módulo 3: Precios y Promociones**  
Este módulo se centra en el control financiero. El sistema debe permitir la modificación del precio de venta y mantener un historial de cambios de precios por producto, registrando fecha, precio anterior, nuevo precio y el usuario responsable. Además, debe facilitar la aplicación de promociones (porcentuales o monto fijo) a productos o categorías específicas por un tiempo limitado.

**Módulo 4: Importación y Exportación**  
Para garantizar una transición suave y facilitar las operaciones masivas, el sistema debe permitir la exportación completa de la Tabla de Inventario a un archivo Excel (.xlsx). También debe soportar la importación masiva de productos y datos de stock desde un archivo Excel, incluyendo una validación de formato. Durante la importación, el sistema debe ofrecer la opción de crear nuevos productos o actualizar los existentes, utilizando el SKU como identificador.

**Módulo 5: Reportes y Métricas (Panel Principal)**  
El sistema debe ofrecer visualizaciones clave. En el Panel Principal del Gerente, se mostrarán métricas clave como el valor total del inventario. Se deben generar reportes detallados del movimiento de stock (entradas y salidas) por periodo. Asimismo, se generará un reporte de inventario que muestre la cantidad, el costo unitario y el valor total de los productos agrupados por categoría.  
**Adicionalmente, el sistema generará reportes y estadísticas sobre los resultados de la gestión de inventario, y proporcionará recomendaciones automáticas al usuario sobre cómo mejorar la eficiencia y el control del inventario, basándose en los datos históricos y tendencias detectadas.**

III. Requerimientos No Funcionales (RNF)  
Los requerimientos no funcionales definen los atributos de calidad, rendimiento y restricciones tecnológicas del sistema.

**Usabilidad y Experiencia de Usuario (UX)**  
La interfaz debe ser intuitiva y simple, manteniendo la familiaridad de una hoja de cálculo para minimizar la curva de aprendizaje del personal. El tiempo de respuesta de las tablas y reportes no debe exceder los 3 segundos. Es obligatorio que el diseño sea responsivo, garantizando la funcionalidad completa tanto en PC de escritorio como en dispositivos móviles.

**Rendimiento y Escalabilidad**  
El sistema debe ser escalable, soportando una base de datos de al menos 10,000 productos activos y un historial transaccional de 5 años. En términos de concurrencia, el sistema debe soportar un mínimo de 10 usuarios concurrentes realizando operaciones sin que se degrade el rendimiento.

**Seguridad y Control**  
El control de acceso debe ser estricto y basado en roles. Se requiere una política de respaldo diario automatizado de la base de datos. Todas las contraseñas de usuario deben ser almacenadas de forma cifrada (hashing) para cumplir con los estándares de seguridad.

**Mantenibilidad y Tecnología**  
El desarrollo debe seguir metodologías ágiles y principios de calidad de código como DRY (No Repetir Código) y KISS (Mantenerlo Simple) para asegurar la mantenibilidad.  
La selección tecnológica debe seguir las recomendaciones (React/Vue, Node.js/Python/PHP, MySQL/PostgreSQL) para garantizar la escalabilidad y el soporte a largo plazo.  
Finalmente, se debe entregar la documentación técnica completa del código y la arquitectura del sistema.  
**(En este proyecto se trabajará con Vue, JavaScript y PHP)**