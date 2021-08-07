<?php
namespace SMCstylus_Elementor\Core\Settings;
// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

/**
 * Class: SMC_Addons_Globals
 * Description: Set/Load plugin addons globals
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
final class SMC_Addons_Globals{
  
    private static $globals = [
      'controls'          => [],
      'widgets_list'      => [],
      'widgets_groups'    => [],
      'extensions_list'   => [],
      'extensions_groups' => [],
    ];
    
    public static $api_defaults =[
      'recaptcha_key'       => '',
      'recaptcha_secret'    => '',
      'google_id'           => '',
      'google_map_key'      => '',
      'facebook_app_id'     => '',
      'facebook_app_secret' => '',
      'mailchimp_api'       => '',
    ];
    
    private static $tools_defaults = [
      'duplicate_post_type' => '',
    ];

    private static $instance;
    
  /**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 * @return SMC_Addons_Globals The single instance of the class.
   * 
	 */
    public static function instance() {
      if ( is_null( self::$instance ) ) {
        self::$instance = new self();
      }

      return self::$instance;
    }
    
  /**
	 * 
	 * Get all settings
	 * 
	 * @since 1.0.0
	 * @access public
   * @static
	 * @return array
	 * 
	 */
    public static function getGlobals(){
      return self::$globals;
    }
    
    /**
     * Get global settings
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $cat
     * @param string $row
     * @return array/string
     * 
     */
    public static function getGlobal($cat='', $row=''){
      if(empty($cat))
      return self::$globals;
      
      if(empty($row))
      return self::$globals[$cat];
      
      return self::$globals[$cat][$row];
    }
    
    /**
     * Set global settings
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $cat
     * @param string $row
     * 
     */
    public static function setGlobal($cat, $row){
      if(empty($cat) || empty($row))
      return;
      // single
      if(isset($row['name'])){
        self::$globals[$cat][$row['name']] = $row['options'];
      // multi
      }else{
        foreach($row as $entry){
          self::$globals[$cat][$entry['name']] = $entry['options'];
        }
      }
      
    }
    
    /**
     * Set widget
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $row
     * 
     */
    public static function setGlobalWidget($row){
      self::$globals['widgets_list'][$row['name']] = $row['options'];
    }
    
    /**
     * Set widgets category
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param array $arr
     * 
     */
    public static function setGlobalWidgetCategory($arr){
      self::$globals['widgets_category'] = $arr;
    }
    
    /**
     * Set controls
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $row
     * 
     */
    public static function setGlobalControl($row){
      if(empty($row))
      return;
      
      self::$globals['controls'][$row['name']] = $row['options'];
    }
    
    /**
     * Set extension
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $row
     * 
     */
    public static function setGlobalExtension($row){
      if(empty($row))
      return;
      
      self::$globals['extensions'][$row['name']] = $row['options'];
    }
    
    private function __construct() { }
    private function __clone() {}
    private function __wakeup() {}
    
}
SMC_Addons_Globals::instance();