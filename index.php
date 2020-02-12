<?php
defined ('ABSPATH') or die ('¡No HACKS Man!');
/*
 * Plugin Name: Wordpress Upgrarde!
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

/* ========= */

