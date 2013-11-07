<?php
/*
Plugin Name: WP-Dropstream
Plugin URI: http://getdropstream.com/merchants
Description: A brief description of the Plugin.
Version: 0.6.1
Author: Dropstream
Author URI: http://getdropstream.com
License: http://getdropstream.com/terms
*/
error_reporting(0);

function _log( $message ) {
  if( WP_DEBUG === true ){
    if( is_array( $message ) || is_object( $message ) ){
      error_log( print_r( $message, true ) );
    } else {
      error_log( $message );
    }
  }
}

class Dropstream {
  private static $instance = NULL;
  private function __construct() {
    add_filter( 'xmlrpc_methods', array($this, 'dropstream_add_xmlrpc_methods') );
  }
  
  public static function get_instance() {
    if(!isset(self::$instance)) {
      self::$instance = new Dropstream();
    }
    return self::$instance;
  }
  
  public function dropstream_ping($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }

    return '0.6.0';
  }
  
  public function dropstream_getOrders($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }

    $status = $args['params']['status'];
    
    $created_after = NULL;
    if(array_key_exists('created_after', $args['params'])) {
      $created_after = $args['params']['created_after'];
    }
    
    return $this->get_adapter_instance()->getOrders($status, $created_after);
  }

  public function dropstream_updateOrderStatus($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }
    
    $id = $args['params']['id'];
    $status = $args['params']['status'];
    return $this->get_adapter_instance()->updateOrderStatus($id, $status);
  }

  public function dropstream_createOrderTracking($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }
    
    $id = $args['params']['id'];
    $tracking = $args['params']['tracking'];
    $carrier = $args['params']['carrier'];
    return $this->get_adapter_instance()->createOrderTracking($id, $tracking, $carrier);
  }

  public function dropstream_updateProductInventory($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $username = $args['credentials']['username'];
    $password = $args['credentials']['password'];

    if ( ! $wp_xmlrpc_server->login( $username, $password ) ) {
      return $wp_xmlrpc_server->error;
    }
    
    $product_id = $args['params']['product_id'];
    $sku = $args['params']['sku'];
    $quantity = $args['params']['quantity'];
    return $this->get_adapter_instance()->updateProductInventory($product_id, $sku, $quantity);
  }
  
  public function dropstream_add_xmlrpc_methods( $methods ) {
    $methods['dropstream.ping'] = array($this, 'dropstream_ping');
    $methods['dropstream.getOrders'] = array($this, 'dropstream_getOrders');
    $methods['dropstream.updateOrderStatus'] = array($this, 'dropstream_updateOrderStatus');
    $methods['dropstream.createOrderTracking'] = array($this, 'dropstream_createOrderTracking');
    $methods['dropstream.updateProductInventory'] = array($this, 'dropstream_updateProductInventory');
    return $methods;
  }

  private function get_adapter_instance() {
    global $wp_xmlrpc_server;
    
    if(is_plugin_active('wp-e-commerce/wp-shopping-cart.php')) {
      
      require_once('inc/adapters/wp_e_commerce.php');
      return new WPECommerceAdapter();
    } elseif(is_plugin_active('woocommerce/woocommerce.php')) {
      
      require_once('inc/adapters/woocommerce.php');
      return new WoocommerceAdapter();
    }
    return $wp_xmlrpc_server->error; // TODO return a helpful message
  }
  
}

function init_dropstream() {
  $dropstream =  Dropstream::get_instance();
}
add_action( 'plugins_loaded' , 'init_dropstream');

?>
