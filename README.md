# JetEngine Query Cache

**JetEngine Query Cache** es un plugin de WordPress diseñado para mejorar el rendimiento de tu sitio cacheando las consultas realizadas por **JetEngine**. Este plugin almacena en caché consultas relacionadas con tipos de publicaciones personalizadas, taxonomías y listings, reduciendo así el tiempo de carga y optimizando el uso de recursos del servidor.

## 🚀 Características

- **Cacheo de Consultas JetEngine**: Almacena en caché consultas de tipos de publicaciones personalizadas, taxonomías y listings.
- **Interfaz de Administración**: Gestiona la caché directamente desde el panel de administración de WordPress.
- **Estadísticas Detalladas**: Monitorea estadísticas como:
  - Total de consultas interceptadas.
  - Hits y misses de caché.
  - Porcentaje de consultas cacheadas.
- **Registro de Consultas No Cacheadas**: Diagnostica consultas no cacheadas para optimizar tu sitio.
- **Limpieza Manual de Caché**: Vacía la caché directamente desde la interfaz de administración.
- **Configuración Flexible**: Selecciona qué tipos de publicaciones, taxonomías y listings deseas cachear según tus necesidades.

## 📋 Requisitos

- **WordPress**: 5.0 o superior.
- **PHP**: 7.0 o superior.
- **JetEngine Plugin**: Instalado y activo.

## 🛠 Instalación

1. **Descargar el Plugin**  
   Descarga el archivo ZIP desde el repositorio de GitHub o desde la fuente proporcionada.

2. **Subir a WordPress**  
   - Ve a **Panel de administración** > **Plugins** > **Añadir nuevo**.
   - Haz clic en **Subir plugin** y selecciona el archivo ZIP descargado.
   - Pulsa **Instalar ahora**.

3. **Activar el Plugin**  
   - Tras la instalación, haz clic en **Activar plugin**.

## ⚙ Configuración

1. **Acceder a la Configuración**  
   Ve a **Herramientas** > **JetEngine Cache** en el panel de administración.

2. **Seleccionar Elementos a Cachear**  
   - **Post Types**: Selecciona los tipos de publicaciones personalizadas que deseas cachear.
   - **Taxonomías**: Selecciona las taxonomías personalizadas que deseas cachear.
   - **Listings**: Selecciona los listings personalizados que deseas cachear.

3. **Guardar Configuraciones**  
   Haz clic en **Guardar cambios** para activar la caché en los elementos seleccionados.

## 📊 Uso

### 1. **Panel de Configuración**
Desde la configuración del plugin, selecciona los elementos deseados para cachear y guarda los cambios.

### 2. **Estadísticas de la Caché**
Monitorea el rendimiento del plugin en la sección de estadísticas:
- **Total de Consultas**: Número total de consultas interceptadas.
- **Hits de Caché**: Consultas servidas desde la caché.
- **Misses de Caché**: Consultas no servidas desde la caché.
- **Porcentaje de Consultas Cacheadas**: Proporción de consultas atendidas desde la caché.
- **Última Limpieza de Caché**: Fecha y hora de la última limpieza.

### 3. **Gráfico de Uso**
Un gráfico de dona muestra el porcentaje de consultas cacheadas frente a las no cacheadas para facilitar la comprensión.

### 4. **Consultas No Cacheadas**
Accede al registro de consultas no cacheadas, donde se detalla:
- **Fecha y hora**.
- **Consulta SQL**.
- **Variables de consulta**.

### 5. **Gestión de Caché**
- **Vaciar Caché**: Limpia toda la caché manualmente desde el panel.
- **Notificaciones**: Recibe un mensaje de éxito tras vaciar la caché.

---

¡Optimiza tu rendimiento con **JetEngine Query Cache** y acelera tu sitio de WordPress! 🚀
