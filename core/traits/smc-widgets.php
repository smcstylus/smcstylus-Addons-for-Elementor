<?php
namespace SMCstylus_Elementor\Core\Traits;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

use \Elementor\Plugin;
use \Elementor\Controls_Manager;

use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;
use SMCstylus_Elementor\File_Handler;
/**
 * Trait: SMC_Widgets
 * Description: Plugin addons functions
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
trait SMC_Widgets {
    /**
     * Add category.
     *
     * Register custom widget category in Elementor's editor
     *
     * @since 1.0.0
     * @access public
     */
    
    public function register_widgets_categories($elements_manager) {
        $textdomain = SMC_Plugin_Globals::getMain('textdomain');
        $cats = SMC_Plugin_Globals::getCategory();
        $prefix = $cats['options']['slug'];
        
        foreach($cats['categories'] as $key => $val){
            $elements_manager->add_category(
            $prefix . $key,
            [
                'title' => __( $val['name'], $textdomain ),
                'icon' => $val['icon'],
            ]
        );
        }
    }
    
    /**
     * Register widgets
     *
     * @since v1.0.0
	 * @access public
     * 
     */
    public function register_widgets($widgets_manager){
        $widgets_list = $this->widgets_registered;
        $widget = SMC_Addons_Globals::getGlobal('widgets_list');
        
        if (empty($widgets_list)) {
            return;
        }
        asort($widgets_list);
        foreach ($widgets_list as $key => $val) {
            if( $this->check_addon_status($key, $widgets_list) ){
                // Load widget class
                $widgets_manager->register_widget_type(new  $widget[$key]['__CLASS__']);
            }
        }
       
    }
    
    /**
     * Register custom controls
     *
     * @since v1.0.0
	 * @access public
     * 
     */
    public function register_controls($controls_manager){
        $controls = SMC_Addons_Globals::getGlobal('controls');
        if (empty($controls)) {
            return;
        }
        asort($controls);
        foreach ($controls as $key => $val) {
            $file = $val['path'] . DIRECTORY_SEPARATOR . strtolower(preg_replace(['/_/'], ['-'], $val['__CLASS__'])) . '.php';
			File_Handler::loadFile(SMC_Plugin_Globals::getPath('controls') . $file);
            $controls_manager->register_control($key, new $val['__CLASS__']);
        }
    }
}