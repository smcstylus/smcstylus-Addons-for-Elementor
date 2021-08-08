<?php
namespace SMCstylus_Elementor\Core\Helpers;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Class: SMC_3rdParty
 * Description: Helper - 3rd plugin are activated
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
class SMC_3rdParty{
  public static $VER = '1.0.0';
	
	 /**
		* 
	 * Check if Elementor plugin is installed
	 * 
	 * @since 1.0.0
   * @access public
   * @static
	 * @return boolean
	 * 
	 */	
	public static function is_elementor_installed(){
		$plugins = get_plugins();
		return isset( $plugins['elementor/elementor.php'] ) ? true : false;
	}
	
		/**
	 * 
	 * Check if Elementor plugin is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return boolean
	 * 
	 */
	public static function is_elementor_active(){
		if ( ! did_action( 'elementor/loaded' ) ) {
			return false;
		}
		return true;
	}
	
		/**
	 * 
	 * Check if Elementor plugin is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param integer $min
	 * @return boolean
	 * 
	 */
	public static function is_elementor_version_compatible($min) {
		// Check for required Elementor version
		if(! defined('ELEMENTOR_VERSION')) return false;
		if ( ! version_compare( ELEMENTOR_VERSION, $min, '>=' ) ) {
			return false;
		}
		return true;
	}
	
  /**
	 * Check if Elementor free plugin is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return boolean
	 * 
	 */
    public static function is_elementorFree_active() {
        return function_exists('elementor_load_plugin_textdomain') ? true : false;
    }
		
    /**
	 * Check if Elementor Pro plugin is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return boolean
	 * 
	 */
    public static function is_elementorPro_active() {
        return function_exists('elementor_pro_load_plugin') ? true : false;
    }
		
		  /**
	 * Check wich distribution of Elementor (Pro/Free) is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return string
	 * 
	 */
		public static function wichElementorIsActive(){
			if(function_exists('elementor_pro_load_plugin')){
				return 'pro';
			}
			if(function_exists('elementor_load_plugin_textdomain')){
				return 'free';
			}
			return '';
		}
	
  /**
	 * 
	 * Check if WPML plugin is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return boolean
	 */
    public static function is_wpml_active() {
        return class_exists('SitePress') ? true : false;
    }
	
  /**
	 * 
	 * Check if WooCommerce plugin is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return boolean
	 * 
	 */
    public static function is_woo_active() {
        return class_exists('WooCommerce') ? true : false;
    }
	
	/**
	 * 
	 * Check if Contact Form 7 plugin is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return boolean
	 * 
	 */
	public static function is_cf7_active() {
        return class_exists('WPCF7') ? true : false;
    }
	
	/**
	 * Check if Revslider plugin is active
	 * 
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return boolean
	 * 
	 */
	public static function is_revSlider_active() {
        return class_exists('RevSliderBase') ? true : false;
  }
	
		/**
		 * 
    * Check if a plugin is installed
    *
    * @since 1.0.0
	  * @access public
	  * @static
		* @param string $basename
	  * @return boolean
		*
    */
    public static function is_plugin_installed($basename) {
        $installed_plugins = get_plugins();
        return isset($installed_plugins[$basename]);
    }
		
    /**
    * Get plugin data
    *
    * @since 1.0.0
	  * @access public
	  * @static
		* @param string $basename
	  * @return array
     */
    public static function get_local_plugin_data( $basename = '' ) {
        if ( empty( $basename ) ) {
            return false;
        }

        $plugins = get_plugins();

        if ( !isset( $plugins[ $basename ] ) ) {
            return false;
        }

        return $plugins[ $basename ];
    }
}