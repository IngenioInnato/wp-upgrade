<?php
  #Definir las rutas de tu sitio
  define('WP_HOME', 'https://misitio.es'); // blog url
  define('WP_SITEURL', 'https://misitio.es'); // site url
  #=============================
#Definir la ruta a tu tema
define('TEMPLATEPATH', '/ruta/absoluta/a/wp-content/themes/nombredeltema');
#=========================

#Especificar el tema por defecto
define('WP_DEFAULT_THEME', 'nombre-de-carpeta-del-tema' );
#===============================

#Definir la ruta de la hoja de estilos
define('STYLESHEETPATH', '/ruta/absoluta/a/wp-content/themes/nombredeltema');
#=====================================

#Controlar las revisiones de entrada
define('WP_POST_REVISIONS', 3);
#===================================

#Cambiar el periodo de vaciado de la papelera
define('EMPTY_TRASH_DAYS', 7 );
#============================================


#Actualizaciones automáticas también a versiones mayores
define('WP_AUTO_UPDATE_CORE', true );
#=======================================================


#Forzar ssl
define('FORCE_SSL_LOGIN', true);
define('FORCE_SSL_ADMIN', true);
#==========

#Aumentar limite de memoria
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '256M' );
#==========================

#Desactivar XML - RPC
add_filter('xmlrpc_enabled', '__return_false');
#====================

#Cambia el intervalo del guardado automático
define('AUTOSAVE_INTERVAL', 300);
#===========================================


#Activar la caché de objetos de WordPress
define('WP_CACHE', true );
#========================================

#Comprimir los contenidos
define('COMPRESS_CSS', true );
define('COMPRESS_SCRIPTS', true );
define('CONCATENATE_SCRIPTS', true );
define('ENFORCE_GZIP', true );
#=========================

#Desactivar o controlar el cron de WordPress
define('DISABLE_WP_CRON', true );
#===========================================

#Depuración de scripts
define('SCRIPT_DEBUG', true );
#=====================

#Desactivar el editor interno de WordPress de plugins y temas
define('DISALLOW_FILE_EDIT', true );
#============================================================