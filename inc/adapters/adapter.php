<?php
interface iAdapter {
  public function getOrders($status);
  public function updateOrderStatus($id, $status);
  public function createOrderTracking($id, $tracking_number, $send_email = true);
  public function updateProductInventory($product_id, $sku, $quantity);
}

?>