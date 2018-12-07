<?php
/**
 * plugin name: WC_assembly instruction plugin
 * description: wordpress woocommerce plugin features by adding an assembly instructions tab
 * Author: Taghi Khavari
 * Author URI: http://weblandtk.ir
 * version: 0.1
 */
//check for woocommerce to be active
if( in_array('woocommerce/woocommerce.php',get_option('active_plugins')) )
{
    if(!class_exists('WC_assembly')){
        class WC_assembly{
            public function __construct()
            {
                add_filter('woocommerce_product_data_tabs',array($this,'my_product_data_tab'),20);
                add_action('woocommerce_product_data_panels',array($this,'woocommerce_product_data_panels'));
                add_action('woocommerce_process_product_meta','WC_assembly::save',20,2);
            }
            public function my_product_data_tab($product_data_tabs){
                $product_data_tabs['']= array(
                        'label'    => __( 'Assembly instructions', 'woocommerce' ),
                        'target'   => 'assembly_product_data',
                        'class'    => array(  ),
                        'priority' => 90,
                );
                return $product_data_tabs;
            }
            public function woocommerce_product_data_panels(){
                ?>

                <div id="assembly_product_data" class="panel woocommerce_options_panel">

                    <?php
                    woocommerce_wp_text_input(
                        array(
                            'id'          => '_assembly_instruction',
                            'label'       => __( 'Assembly instruction', 'woocommerce' ),
                            'placeholder' => 'http://',
                            'description' => __( 'Enter the url to the assembly instructions (kites only)', 'woocommerce' ),
                        )
                    );
                    woocommerce_wp_text_input(
                        array(
                            'id'          =>'_kite_price',
                            'label'       => 'Price',
                            'data_type'   => 'price'
                        )
                    );
                    woocommerce_wp_select(
                        array(
                            'id'        => '_select',
                            'label'     => 'My Select Field',
                            'options'   => array(
                                'One'   => 'Option 1',
                                'Two'   => 'Option 2',
                                'Three' => 'Option 3',
                                'Four'  => 'Option 4'
                            )
                        )
                    );
                    ?>
                </div>

                <?php
            }
            
            public static function save($post_id,$post)
            {
                //update post meta
                if (isset($_POST['_assembly_instruction']))
                {
                    update_post_meta($post_id,'_assembly_instruction',wc_clean($_POST['_assembly_instruction']));
                }
                if (isset($_POST['_kite_price']))
                {
                    update_post_meta($post_id,'_kite_price',wc_clean($_POST['_kite_price']));
                }
                if (isset($_POST['_select']))
                {
                    update_post_meta($post_id,'_select',wc_clean($_POST['_select']));
                }
            }
           
        }
        $GLOBALS['WC_assembly'] = new WC_assembly();
    }
}
