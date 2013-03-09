<?php

require('adapter.php');
require_once( WP_PLUGIN_DIR."/wp-e-commerce/wpsc-core/wpsc-constants.php" );
class WPECommerceAdapter implements iAdapter {
  
  public function getOrders($status) {
    require_once( WP_PLUGIN_DIR."/wp-e-commerce/wpsc-includes/purchaselogs.class.php" );
    require_once( WP_PLUGIN_DIR."/wp-e-commerce/wpsc-includes/meta.functions.php" );
    global $wpdb;

    $orders = array('orders' => array());
    $sql = $wpdb->prepare( "SELECT * FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `processed`=%s", $status);
    $purchase_logs = $wpdb->get_results( $sql );

    foreach ( $purchase_logs as $purchase_log ) {
      $purchaseLogObj = new WPSC_Purchase_Log($purchase_log->id);
      $checkoutForm = new WPSC_Checkout_Form_Data($purchase_log->id);
      $order_items = array();

      // Load sku into order items
      foreach($purchaseLogObj->get_cart_contents() as $cart_item) {
        $cart_item->sku = get_product_meta($cart_item->prodid, 'sku', true );
        $order_items[] = $cart_item;
      }
      
      $addresses = $checkoutForm->get_gateway_data();
      $order = array(
        'id' => $purchase_log->id,
        'date' => date('Y-m-d h:i:s A', $purchase_log->date),
        'shipping_method' => $purchase_log->shipping_method,
        'shipping_option' => $purchase_log->shipping_option,
        'status' => $purchase_log->processed,
        'notes' => $purchase_log->notes,
        'plugin_version' => $purchase_log->plugin_version,
        
        'billing_address' => $addresses['billing_address'],
        'shipping_address' => $addresses['shipping_address'],
        'order_items' => $order_items
      );
      $orders['orders'][] = $order;
    }

    return $orders;
  }
  
  public function updateOrderStatus($id, $status) {
    require_once( WP_PLUGIN_DIR."/wp-e-commerce/wpsc-includes/purchase-log.class.php" );
    
    $purchaseLog = new WPSC_Purchase_Log($id);
    if(!$purchaseLog->exists()) {
      return new IXR_Error( 404, __( 'Selected PurchaseLog could not be found' ) );
    }
    
    $purchaseLog->set('processed', $status);
    return $purchaseLog->save();
  }
  
  public function createOrderTracking($order_id, $tracking_number, $send_email = true) {
    require_once( WP_PLUGIN_DIR."/wp-e-commerce/wpsc-includes/purchase-log.class.php" );
    
    $purchaseLog = new WPSC_Purchase_Log($order_id);
    if(!$purchaseLog->exists()) {
      return new IXR_Error( 404, __( 'Selected PurchaseLog could not be found' ) );
    }
    
    global $wpdb;
    $update = (int) $wpdb->update(
				WPSC_TABLE_PURCHASE_LOGS,
				array(
					'track_id' => $tracking_number
				),
				array(
					'id'       => $order_id
				),
				'%s',
				'%d'
			);
			
    if(!$update) {
      return new IXR_Error( 500, __( 'Could not update tracking number for order' ) );
    }
    
		if ( $update && $send_email)
			self::_send_tracking_email( $order_id, $tracking_number );
  }

  public function updateProductInventory($product_id, $sku, $quantity) {
    $product = get_post( $product_id);
    if(!$product) {
      return new IXR_Error( 404, __( 'Selected PurchaseLog could not be found' ) );
    }
    
    require_once( WP_PLUGIN_DIR."/wp-e-commerce/wpsc-includes/meta.functions.php" );
    $update = update_product_meta($product_id, 'stock', $quantity);
    
    if(!$update) {
      return new IXR_Error( 500, __( 'Could not update tracking number for order' ) );
    }
  }
  
  /**
  * Copied from wpec shipwire)fuctions
  */
  private static function _send_tracking_email( $order_id, $tracking_number = '' ) {
		global $wpdb;

		$id = absint( $order_id );

		$tracking_numbers = array();

		foreach ( $tracking_number as $tn => $array ) {
			$tracking_numbers[] = '<a href="' .  esc_url( $array['link'] ) . '">' . esc_html( $tn ) . '</a>';
		}

		$tracking_numbers = implode( '<br />', $tracking_numbers );
		$site_name        = get_option( 'blogname' );

		$message = nl2br( get_option( 'wpsc_trackingid_message' ) );
		$message = str_replace( '%trackid%', $tracking_numbers, $message );
		$message = str_replace( '%shop_name%', $site_name, $message );

		$email_form_field = $wpdb->get_var( "SELECT `id` FROM `" . WPSC_TABLE_CHECKOUT_FORMS . "` WHERE `type` IN ('email') AND `active` = '1' ORDER BY `checkout_order` ASC LIMIT 1" );
		$email            = $wpdb->get_var( $wpdb->prepare( "SELECT `value` FROM `" . WPSC_TABLE_SUBMITED_FORM_DATA . "` WHERE `log_id` = %d AND `form_id` = %d LIMIT 1", $id, $email_form_field ) );

		$subject = get_option( 'wpsc_trackingid_subject' );
		$subject = str_replace( '%shop_name%', $site_name, $subject );

		add_filter( 'wp_mail_from',         'wpsc_replace_reply_address', 0 );
		add_filter( 'wp_mail_from_name',    'wpsc_replace_reply_name', 0 );
		add_filter( 'wp_mail_content_type', array( __CLASS__, 'tracking_email_html' ) );

		$send = wp_mail( $email, $subject, $message );

		remove_filter( 'wp_mail_from',         'wpsc_replace_reply_address', 0 );
		remove_filter( 'wp_mail_from_name',    'wpsc_replace_reply_name', 0 );
		remove_filter( 'wp_mail_content_type', array( __CLASS__, 'tracking_email_html' ) );

		return $send;
	}
  
}

?>