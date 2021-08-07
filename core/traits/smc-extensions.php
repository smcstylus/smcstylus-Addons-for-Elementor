<?php
namespace SMCstylus_Elementor\Core\Traits;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;
use Elementor\Plugin;
/**
 * Trait: SMC_Extensions
 * Description: Plugin addons functions
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
trait SMC_Extensions {
    /**
     * Register extensions
     *
     * @since v1.0.0
	 * @access public
     * 
     */
    public function register_extensions(){
        $extensions_list = $this->extensions_registered;
        $extension = SMC_Addons_Globals::getGlobal('extensions_list');
        if (empty($extensions_list)) {
            return;
        }
        
        foreach ($extensions_list as $key => $val) {
            if( $this->check_addon_status($key, $extensions_list) ){
                $extension[$key]['__CLASS__']::instance();
            }
        }
    } 
    
    /**
     * Print custom JS
     *
     * @since v1.0.0
	 * @access public
     * @return string
     */
    public function print_custom_js(){
        $post_id = get_the_ID();
        if(!Plugin::$instance->documents->get( $post_id )->is_built_with_elementor())
        {die( '<script></script>');}
        $custom_js_strings='';
        $document = Plugin::$instance->documents->get($post_id);
        $slug = SMC_Plugin_Globals::getMain('opt_prefix').'custom_js_code';
        if ($document->get_settings( $slug)) {
            $custom_js_strings = $document->get_settings($slug);
        }
        
        // Output custom JS code
        echo '<script>' . $custom_js_strings . '</script>';	
    }
}