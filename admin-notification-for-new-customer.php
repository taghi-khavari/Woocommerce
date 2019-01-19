
<?php
/**
 * Send "New User Registration" email to admins when new customer is created on WooCommerce.
 * 
 * @param int $id New customer ID.
 */

add_action( 'woocommerce_created_customer', 'weblandtk_woocommerce_customer_admin_notification' );
function weblandtk_woocommerce_customer_admin_notification( $customer_id ) {
    wp_send_new_user_notifications( $customer_id, 'admin' );
}
