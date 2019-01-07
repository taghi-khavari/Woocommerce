<?php
/**
 * Plugin name: Weblandtk Premium Product
 * Plugin URI: http://weblandtk.ir
 * Description: This is a simple plugin to add a custom role in wordpress for showing them the price of some Premium product that we choose
 * Author: Taghi Khavari
 * Author Uri: http://weblandtk.ir
 * version: 1.0
 * License: GPL2
 */


function weblandtk_customer_role()
{
//add_role( string $role, string $display_name, array $capabilities = array() )
    add_role(
        'weblandtk_customer',
        'Weblandtk Customer',
        [
            //Subscriber
            'read'                     => true,
            'see-premium-product'      => true
        ]
    );
}
// add the customer_role
add_action('init', 'weblandtk_customer_role');



add_action( 'add_meta_boxes', 'weblandtk_add_wt_premium_ch_meta_box' );

function weblandtk_add_wt_premium_ch_meta_box() {
    add_meta_box(
        'weblandtk_fields_meta_box', // $id
        'Is This Product Premium?', // $title
        'weblandtk_show_wt_premium_ch_meta_box', // $callback
        'product', // $screen
        'side', // $context
        'high' // $priority
    );
}


function weblandtk_show_wt_premium_ch_meta_box() {
    global $post;
    $meta = get_post_meta( $post->ID, 'wt_premium_ch', true ); ?>

    <input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">
    <p>
        <label for="wt_premium_ch">Premium
            <input type="checkbox" name="wt_premium_ch" value="premium" <?php if ( $meta === 'premium' ) echo 'checked'; ?>>
        </label>
    </p>


<?php }
function weblandtk_save_wt_premium_ch_meta( $post_id ) {
    // verify nonce
    if ( isset($_POST['your_meta_box_nonce'])
        && !wp_verify_nonce( $_POST['your_meta_box_nonce'], basename(__FILE__) ) ) {
        return $post_id;
    }
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    // check permissions
    if (isset($_POST['post_type'])) { //Fix 2
        if ( 'page' === $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            } elseif ( !current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
    }
    $new = $_POST['wt_premium_ch'];
    update_post_meta( $post_id, 'wt_premium_ch', $new );
}
add_action( 'save_post', 'weblandtk_save_wt_premium_ch_meta' );


function weblandtk_remove_price(){
    remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10);
}


function weblandtk_add_new_price(){
    add_action('woocommerce_single_product_summary','weblandtk_is_premium_product',10);
}


function weblandtk_is_premium_product(){
    global $post;
    $premium_product = get_post_meta($post->ID,'wt_premium_ch', true);
    if($premium_product=='premium') {
        echo 'Use the qoute button!';

    }
}

add_action('woocommerce_single_product_summary','weblandtk_check_premium_product',5);
function weblandtk_check_premium_product(){
    global $post;
    $premium_product = get_post_meta($post->ID,'wt_premium_ch', true);
    if(($premium_product=='premium')&& !current_user_can('see-premium-product')) {
        weblandtk_remove_price();
        weblandtk_add_new_price();
    }
}


add_filter( 'manage_edit-product_columns', 'weblandtk_change_columns_filter',10, 1 );
function weblandtk_change_columns_filter( $columns ) {
    unset($columns['product_tag']);
    unset($columns['sku']);
    unset($columns['featured']);
    unset($columns['product_type']);
    unset($columns['date']);
    unset($columns['author']);
    $columns['premium'] = __( 'Premium Product');
    return $columns;
}

add_action( 'manage_product_posts_custom_column', 'weblandtk_product_column_offercode', 10, 2 );

function weblandtk_product_column_offercode( $column, $postid ) {
    if ( $column == 'premium' ) {
        $premium_product = get_post_meta($postid,'wt_premium_ch', true);
        if($premium_product=='premium') {
            echo 'PREMIUM';
        }else{
            echo '-';
        }
    }
}



add_filter( 'bulk_actions-edit-product', 'weblandtk_register_bulk_actions_add' );

function weblandtk_register_bulk_actions_add($bulk_actions) {
    $bulk_actions['premium_products'] = __( 'Add to Premium Product');
    return $bulk_actions;
}
//Handling the form submission
add_filter( 'handle_bulk_actions-edit-product', 'weblandtk_bulk_action_add_handler', 10, 3 );

function weblandtk_bulk_action_add_handler( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction !== 'premium_products' ) {
        return $redirect_to;
    }
    foreach ( $post_ids as $post_id ) {
        // Perform action for each post.
        update_post_meta( $post_id, 'wt_premium_ch', 'premium' );
    }
    $redirect_to = add_query_arg( 'bulk_premium_product', count( $post_ids ), $redirect_to );
    return $redirect_to;
}


add_filter( 'bulk_actions-edit-product', 'weblandtk_register_bulk_actions_remove' );

function weblandtk_register_bulk_actions_remove($bulk_actions) {
    $bulk_actions['rm_premium_products'] = __( 'Remove from Premium Product');
    return $bulk_actions;
}
//Handling the form submission
add_filter( 'handle_bulk_actions-edit-product', 'weblandtk_bulk_action_remove_handler', 10, 3 );

function weblandtk_bulk_action_remove_handler( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction !== 'rm_premium_products' ) {
        return $redirect_to;
    }
    foreach ( $post_ids as $post_id ) {
        // Perform action for each post.
        delete_post_meta( $post_id, 'wt_premium_ch' );
    }
    $redirect_to = add_query_arg( 'bulk_premium_product', count( $post_ids ), $redirect_to );
    return $redirect_to;
}
//Showing notices
add_action( 'admin_notices', 'weblandtk_bulk_action_admin_notice' );

function weblandtk_bulk_action_admin_notice() {
    if ( ! empty( $_REQUEST['bulk_premium_product'] ) ) {
        $product_count = intval( $_REQUEST['bulk_premium_product'] );
        printf( '<div id="message" class="updated fade">' .
            _n( '%s product changed ',
                '%s products changed ',
                $product_count,
                'weblandtk.ir'
            ) . '</div>', $product_count );
    }
}
