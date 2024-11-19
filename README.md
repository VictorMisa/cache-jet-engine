JetEngine Query Cache es un plugin de WordPress diseñado para mejorar el rendimiento de tu sitio cacheando las consultas realizadas por JetEngine. Este plugin almacena en caché consultas relacionadas con tipos de publicaciones personalizadas, taxonomías y listings, reduciendo así el tiempo de carga y optimizando el uso de recursos del servidor.

Además, ofrece una interfaz intuitiva en el backend para gestionar la caché y visualizar estadísticas detalladas, incluyendo consultas que no han sido cacheadas.

Características
Cacheo de Consultas JetEngine: Almacena en caché consultas de tipos de publicaciones personalizadas, taxonomías y listings de JetEngine.
Interfaz de Administración: Gestiona la caché directamente desde el panel de administración de WordPress.
Estadísticas Detalladas: Visualiza estadísticas como el total de consultas, hits de caché, misses y porcentaje de consultas cacheadas.
Registro de Consultas No Cacheadas: Mantiene un registro de las consultas que no se han cacheado para facilitar el diagnóstico y optimización.
Limpieza Manual de Caché: Permite vaciar la caché manualmente desde la interfaz de administración.
Configuración Flexible: Selecciona qué tipos de publicaciones, taxonomías y listings deseas cachear según tus necesidades.
Requisitos
WordPress: 5.0 o superior.
PHP: 7.0 o superior.
JetEngine Plugin: Instalado y activo.
Instalación
Descargar el Plugin:

Descarga el archivo ZIP del plugin desde el repositorio de GitHub o desde la fuente donde esté alojado.
Subir a WordPress:

Ve al panel de administración de WordPress.
Navega a Plugins > Añadir nuevo.
Haz clic en Subir plugin y selecciona el archivo ZIP descargado.
Haz clic en Instalar ahora.
Activar el Plugin:

Después de la instalación, haz clic en Activar plugin.
Configuración
Acceder a la Página de Configuración:

En el panel de administración de WordPress, ve a Herramientas > JetEngine Cache.
Seleccionar Elementos a Cachear:

Post Types: Selecciona los tipos de publicaciones personalizadas que deseas cachear.
Taxonomías: Selecciona las taxonomías personalizadas que deseas cachear.
Listings: Selecciona los listings personalizados que deseas cachear.
Guardar Configuraciones:

Después de seleccionar los elementos deseados, haz clic en Guardar cambios.
Uso
1. Panel de Configuración
En la página de configuración del plugin, podrás seleccionar qué tipos de publicaciones, taxonomías y listings deseas cachear. Marca las casillas correspondientes y guarda los cambios para activar la caché en los elementos seleccionados.

2. Estadísticas de la Caché
El plugin proporciona una sección de estadísticas donde puedes monitorear el rendimiento de la caché:

Total de Consultas: Número total de consultas interceptadas por el plugin.
Hits de Caché: Número de consultas servidas desde la caché.
Misses de Caché: Número de consultas que no se sirvieron desde la caché.
Porcentaje de Consultas Cacheadas: Proporción de consultas que se sirvieron desde la caché.
Última Limpieza de Caché: Fecha y hora de la última vez que se vació la caché.
3. Gráfico de Uso de la Caché
Un gráfico de dona muestra visualmente el porcentaje de consultas cacheadas frente a las no cacheadas, facilitando la comprensión del rendimiento de la caché.

4. Consultas No Cacheadas
El plugin registra y muestra una lista de las consultas que no han sido cacheadas, incluyendo la fecha y hora, la consulta SQL y las variables de la consulta. Esto es útil para identificar áreas que podrían beneficiarse de una optimización adicional.

5. Acciones de Gestión de Caché
Vaciar Caché: Desde la sección de acciones, puedes vaciar toda la caché manualmente. Esto eliminará todas las transients relacionadas con la caché de JetEngine y reiniciará los contadores de consultas.
Notificaciones: Después de vaciar la caché, se mostrará una notificación de éxito para confirmar que la acción se ha realizado correctamente.
