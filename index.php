<?php
defined ('ABSPATH') or die ('¡No HACKS Man!');
/*
 * Plugin Name: Wordpress Upgrade!
 * Description: Mejora WordPress en seguridad y velocidad de sitio. 
 * Version: 0.1
 * Author: Miguel Ángel
*/
/* SEGURIDAD */

// Actualizar automaticamente plugins y temas
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );
//==========================================

// Mantener conectado la sesion on 365 días
function conectado_365_dias( $expirein ){
  return 31556926; //1 año en segundos
}
// ========================================

// Remover la versión de wordpress en el head
add_filter('the_generator', 'killVersion');
function killVersion(){ return ''; }
remove_action('wp_head', 'wp_generator');
// ==========================================

// Impedir comentarios HTML
function plc_comment_post( $incoming_comment ) { // Esto es lo que pasa cuando se publica un comentario
  // convierte todo lo que haya en un comentario y lo muestra literalmente
  $incoming_comment['comment_content'] = htmlspecialchars($incoming_comment['comment_content']);
  // la única excepción son las citas sencillas, que no pueden ser #039; ya que WordPress las marca como spam
  $incoming_comment['comment_content'] = str_replace( "'", '&apos;', $incoming_comment['comment_content'] );
  return( $incoming_comment );
  }
  // Esto es lo que pasará antes de mostrar un comentario
  function plc_comment_display( $comment_to_display ) {
  // Vuelve a habilitar las citas sencillas
  $comment_to_display = str_replace( '&apos;', "'", $comment_to_display );
  return $comment_to_display;
  }
  add_filter( 'preprocess_comment', 'plc_comment_post', '', 1);
  add_filter( 'comment_text', 'plc_comment_display', '', 1);
  add_filter( 'comment_text_rss', 'plc_comment_display', '', 1);
  add_filter( 'comment_excerpt', 'plc_comment_display', '', 1);
// ========================

// Eliminar mensaje de error
add_filter('login_errors', create_function('$a',"return null;"));
// ========================

// Inhabilitar cambio de claves
  if ( is_admin() ) add_action( 'init', 'disable_password_fields', 10 );
  function disable_password_fields() {
    if ( ! current_user_can( 'administrator' ) ) $show_password_fields = add_filter( 'show_password_fields', '__return_false' );
  }
// ============================

// Prohibir WP-Admin de suscriptores
function restrict_access_admin_panel(){
  global $current_user;
  get_currentuserinfo();
  if ($current_user->user_level <  4) {
    wp_redirect( get_bloginfo('url') );
    exit;
  }
}
add_action('admin_init', 'restrict_access_admin_panel', 1);
// =================================


// Desactivar XML - RCP
add_filter( 'xmlrpc_methods', function( $methods ) {
  unset( $methods['pingback.ping'] );
  return $methods;
} );
// ====================

// Restringir nombres de usuarios comunes
function sozot_validate_username($valid, $username) {
  $forbidden = array('admin', 'webmaster', 'Editor', 'Administrador', 'Dios', 'SuperAdmin', 'foro', 'foros', 'moderador', 'Super Administrador', 'Autor', 'Ayuda WordPress');
  $pages = get_pages();
  foreach ($pages as $page) {
      $forbidden[] = $page->post_name;
  }
  if(!$valid || is_user_logged_in() && current_user_can('create_users') ) return $valid;
  $username = strtolower($username);
  if ($valid && strpos( $username, ' ' ) !== false) $valid=false;
  if ($valid && in_array( $username, $forbidden )) $valid=false;
  if ($valid && strlen($username) < 5) $valid=false;
  return $valid;
}
add_filter('validate_username', 'sozot_validate_username', 10, 2);

function sozot_registration_errors($errors) {
  if ( isset( $errors->errors['invalid_username'] ) )
      $errors->errors['invalid_username'][0] = __( 'ERROR: Nombre de usuario no válido, elige otro.', 'sozot' );
  return $errors;
}
add_filter('registration_errors', 'sozot_registration_errors');
// ======================================



// Limpieza del head
/**
 * Limpieza de wp_head()
 *
 * Elimina enlaces innecesarios
 * Elimina el CSS utilizado por el widget de comentarios recientes
 * Elimina el  CSS utilizado en las galerías
 * Elimina el cierre automático de etiquetas y cambia de ''s a "'s en rel_canonical()
 */
function nowp_head_cleanup() {
  // Eliminamos lo que sobra de la cabecera
  remove_action('wp_head', 'rsd_link');
  remove_action('wp_head', 'wp_generator');
  remove_action('wp_head', 'feed_links', 2);
  remove_action('wp_head', 'index_rel_link');
  remove_action('wp_head', 'wlwmanifest_link');
  remove_action('wp_head', 'feed_links_extra', 3);
  remove_action('wp_head', 'start_post_rel_link', 10, 0);
  remove_action('wp_head', 'parent_post_rel_link', 10, 0);
  remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
  remove_action('wp_head', 'feed_links', 2);
  remove_action('wp_head', 'feed_links_extra', 3);
  
  global $wp_widget_factory;
  remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  
  if (!class_exists('WPSEO_Frontend')) {
    remove_action('wp_head', 'rel_canonical');
    add_action('wp_head', 'nowp_rel_canonical');
  }
}
function nowp_rel_canonical() {
  global $wp_the_query;
  
  if (!is_singular()) {
    return;
  }
  
  if (!$id = $wp_the_query->get_queried_object_id()) {
    return;
  }
  
  $link = get_permalink($id);
  echo "\t<link rel=\"canonical\" href=\"$link\">\n";
}
add_action('init', 'nowp_head_cleanup');

/**
 * Eliminamos la versión de WordPress
 */
add_filter('the_generator', '__return_false');


/**
 * Limpieza de los language_attributes() usados en la etiqueta <html>
 *
 * Cambia lang="es-ES" a lang="es"
 * Elimina dir="ltr"
 */
function nowp_language_attributes() {
  $attributes = array();
  $output = '';
  
  if (function_exists('is_rtl')) {
    if (is_rtl() == 'rtl') {
      $attributes[] = 'dir="rtl"';
    }
  }
  
  $lang = get_bloginfo('language');
  
  if ($lang && $lang !== 'es-ES') {
    $attributes[] = "lang=\"$lang\"";
  } else {
    $attributes[] = 'lang="es"';
  }
  
  $output = implode(' ', $attributes);
  $output = apply_filters('nowp_language_attributes', $output);
  
  return $output;
}
add_filter('language_attributes', 'nowp_language_attributes');
// =================


// Cambiar URL de autor a id
function change_author_permalinks() {
  global $wp_rewrite;
   // Primero cambiamos el valor de la base de author a lo que queramos
   $WP_rewrite->author_base = 'perfil';
  $wp_rewrite->flush_rules();
}
 
add_action('init','change_author_permalinks');
 
add_filter('query_vars', 'users_query_vars');
function users_query_vars($vars) {
    // ahora añadimos el ID de usuario a la lista de variables válidas
    $new_vars = array('perfil');
    $vars = $new_vars + $vars;
    return $vars;
}
 
//Y finalmente generamos la regla de escritura de la URL con los valores anteriores 
function user_rewrite_rules( $wp_rewrite ) {
  $newrules = array();
  $new_rules['perfil/(\d*)$'] = 'index.php?author=$matches[1]';
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
add_filter('generate_rewrite_rules','user_rewrite_rules');
// =========================

/* ========= */ 