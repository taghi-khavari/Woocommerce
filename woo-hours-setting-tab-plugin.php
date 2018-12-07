<?php
/**
 * Plugin name: Woocommerce closed hours
 * Plugin Uri: http://Weblandtk.ir
 * Description: A simple plugin to show how to add a new tab to the woocommerce setting tab
 * Version: 0.1
 * Author: Taghi Khavari
 * Author URI: http://Weblandtk.ir
 */

//check for woocommerce to be active
if( in_array('woocommerce/woocommerce.php',get_option('active_plugins')) )
{
    if(!class_exists('WC_Hours')){
        class WC_Hours{
            public function __construct()
            {
                add_filter('woocommerce_settings_tabs_array',array($this,'add_settings_tab') ,50);
                add_action('woocommerce_settings_opening_hours', array($this,'add_settings'),50);
                add_action('woocommerce_update_options_opening_hours', array($this,'update_settings'),50);
                add_action('wp', array($this,'maybe_disable_checkout'),50);
            }

            public function add_settings_tab($settings_tab){

                $settings_tab['opening_hours'] = 'Opening Hours';

                return $settings_tab;
            }
            public function add_settings(){
                woocommerce_admin_fields(self::get_settings());
            }

            public function update_settings(){
                woocommerce_update_options(self::get_settings());
            }

            public function maybe_disable_checkout(){
                $disable_checkout = get_option('wc_settings_closed_fridays') == 'yes' ? true : false;

                //check if the setting is checked and it's friday
                //TODO actually check that it's friday

                if($disable_checkout && TRUE && is_checkout()){
                    //redirect to a specific post
                    wp_safe_redirect(get_permalink(1));
                }

            }

            public function get_settings(){
                $settings = array(
                    'section_title' => array(
                        'name'    => 'Opening Hours',
                        'type'    => 'title',
                        'desc'    => '',
                        'id'      => ''
                    ),
                    'closed_fridays' => array(
                        'name'    => 'Closed on fridays',
                        'type'    => 'checkbox',
                        'desc'    => 'Disable the checkout on fridays',
                        'id'      => 'wc_settings_closed_fridays'
                    ),
                    'section_end' => array(
                        'type'    => 'sectionend',
                        'id'      => 'wc_settings_section_end'
                    )
                );
                return $settings;
            }
        }

        $GLOBALS['wc_hours'] = new WC_Hours();
    }
}
