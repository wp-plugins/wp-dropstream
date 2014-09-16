<?php

require('adapter.php');

class WoocommerceAdapter implements iAdapter {
  
  public function getOrders($status, $updated_after) {

    $orders = array('orders' => array());

    $order_query = NULL;
    if(is_pangolin()) {
      $order_query = new WP_Query(array('post_type' => 'shop_order', 'post_status' => array('wc-'.$status), 'posts_per_page' => '-1',
      'date_query' => array(
      		array(
            'column'    => 'post_modified_gmt',
      			'after'     => $updated_after,
      			'inclusive' => true,
      		),
      	)));
    } else {
      $order_query = new WP_Query(array('post_type' => 'shop_order', 'posts_per_page' => '-1',
        'date_query' => array(
            array(
              'column'    => 'post_modified_gmt',
              'after'     => $updated_after,
              'inclusive' => true,
            ),
          ),
        'tax_query' => array(
                array(
                    'taxonomy' => 'shop_order_status',
                    'field' => 'slug',
                    'terms' =>  array($status),
                    'operator' => 'AND' )
            )));
    }
    _log('Executing query. '.$order_query->request);

    if( $order_query->have_posts() ) {

      while ($order_query->have_posts()) : $order_query->the_post(); 

      $wc_order = self::format_order(new WC_Order(get_the_ID()));

      $orders['orders'][] = $wc_order;
      endwhile;
    }

    _log('Completed getOrders method');
    return $orders;
   }
  
  public function updateOrderStatus($id, $status) {
    $wc_order = new WC_Order($id);
    
    if(!$wc_order->get_order($id)) {
      return new IXR_Error( 404, __( 'Selected order could not be found' ) );
    }

    if($status == 'awaiting-fulfillment' && is_pangolin()) {
      $status = 'wc-awaiting-shipment';
    }
    return $wc_order->update_status($status);
  }
  
  public function createOrderTracking($order_id, $tracking_number, $carrier, $send_email = true) {
    $wc_order = new WC_Order($order_id);
    
    if(!$wc_order->get_order($order_id)) {
      return new IXR_Error( 404, __( 'Selected order could not be found' ) );
    }
    
    if(!isset($tracking_number)) {
      return new IXR_Error( 500, __( 'Invalid Tracking Number' ) );
    }
    
    update_post_meta( $order_id, '_tracking_provider', strtolower($carrier) );
		update_post_meta( $order_id, '_tracking_number', $tracking_number );
		update_post_meta( $order_id, '_date_shipped', strtotime(date('Y-m-d')) ); // YYYY-MM-DD
    
  }

  public function updateProductInventory($product_id, $sku, $quantity) {
    $product = self::get_product_by_sku($sku);

    if(!isset($product)) {
      return new IXR_Error( 404, __( 'Product SKU ['.$sku.'] not found.' ) );
    }
    $product->set_stock($quantity);
    
    return true;
  }
  
  
  private static function get_product_by_sku( $sku ) {
    global $wpdb;

    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

    if ( $product_id ) return get_product( $product_id );

    return null;
  }
  
  private static function get_product_by_id( $id ) {
    return get_product( $id );
  }
  
  private static function format_order($_order) {
  	$order = new stdClass;
  	$order->id = $_order->id;
  	if(is_plugin_active('woocommerce-sequential-order-numbers-pro/woocommerce-sequential-order-numbers.php')) {
  	  $order->display_id = ltrim( $_order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) );
	  }
    _log('Preparing to format order. order_id='.$_order->id);

  	$order->status = $_order->status;
  	$order->email = $_order->billing_email;
  	$order->date = $_order->order_date;
  	$order->notes = $_order->customer_note;
  	$order->shipping_method = $_order->get_shipping_method();
    $order->shipping_amount = $_order->get_total_shipping();
    $order->shipping_tax = $_order->get_shipping_tax();
    $order->tax_amount = $_order->get_total_tax();
    $order->discount_amount = $_order->get_total_discount();
    $order->coupons = $_order->get_used_coupons();
  	
  	// billing address
  	$billing_address = new stdClass;
  	$billing_address->first_name = $_order->billing_first_name;
  	$billing_address->last_name = $_order->billing_last_name;
  	$billing_address->phone = $_order->billing_phone;
  	$billing_address->company = $_order->billing_company;
  	$billing_address->street = $_order->billing_address_1."\r\n".$_order->billing_address_2;
  	$billing_address->city = $_order->billing_city;
  	$billing_address->state = $_order->billing_state;
  	$billing_address->zip = $_order->billing_postcode;
  	$billing_address->country = $_order->billing_country;
  	$order->billing_address = $billing_address;

  	// shipping address
  	$shipping_address = new stdClass;
  	$shipping_address->first_name = $_order->shipping_first_name;
  	$shipping_address->last_name = $_order->shipping_last_name;
  	$shipping_address->company = $_order->shipping_company;
  	$shipping_address->street = $_order->shipping_address_1."\r\n".$_order->shipping_address_2;
  	$shipping_address->city = $_order->shipping_city;
  	$shipping_address->state = $_order->shipping_state;
  	$shipping_address->zip = $_order->shipping_postcode;
  	$shipping_address->country = $_order->shipping_country;
  	$order->shipping_address = $shipping_address;

  	$order->order_items = array();
  	foreach($_order->get_items() as $_item) {
  	  $product = self::get_product_by_id($_item['product_id']);
      
      if(is_null($product)) {
        _log('  WARNING: Could not find product. order_id='.$_order->id.',product_id='.$_item['product_id']);
        continue;
      }

  		$item = new stdClass;
  		$item->order_product_id = $_item['product_id'];
  		$item->name = $_item['name'];
  		$item->sku = self::format_order_item_sku($_item);
  		$item->quantity = $_item['qty'];
  		$item->price = $product->get_price();
  		$order->order_items[] = $item;

      _log('  Found order item. order_id='.$_order->id.',sku='.$item->sku);
  	}

    _log('Formatted order. order_id='.$_order->id);
  	return $order;
  }
  
  private static function format_order_item_sku($item) {
    $product = self::get_product_by_id($item['product_id']);
    if($product->product_type == 'simple' || $product->product_type == 'subscription') {
      return $product->get_sku();
    } else if($product->product_type == 'variable' || $product->product_type == 'variable-subscription') {
      $variation = new WC_Product_variation($item['variation_id']);
      
      return $product->get_sku()."".$variation->get_sku();
    }
    // group products can only contain simple products and will be caught above
    return NULL;
  }
  
}