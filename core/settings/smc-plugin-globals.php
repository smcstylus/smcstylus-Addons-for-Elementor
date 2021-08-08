<?php
namespace SMCstylus_Elementor\Core\Settings;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 
use SMCstylus_Elementor\File_Handler;

/**
 * Class: SMC_Plugin_Globals
 * Description: Set/Load plugin globals
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
final class SMC_Plugin_Globals{
     
    private static $base      = '';
    private static $prefix    = '';
    private static $globals = [];

    private static $instance;   
    /**
     * Instance
     *
     * @since 1.0.0
     * @access private
     * @static
     * @return SMC_Plugin_Globals The single instance of the class.
     * 
     */
    public static function instance() {
      if ( is_null( self::$instance ) ) {
        self::$instance = new self();
      }

      return self::$instance;
    }

    private function __construct() { }
    private function __clone() {}
    private function __wakeup() {}
    
    
    
    /**
     * Set constants
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $file
     * @param string $prefix
     * 
     */
    public static function set_base($file='', $prefix=''){
      // Set plugin file and constants prefix in variables
      self::$base = $file;
      self::$prefix = $prefix;
      
      // Define constants
      self::define('main');
      // Load all _constants() in variable
      self::$globals = self::globals(true);
    }
    
    /**
     * Get plugin base file
     *
     * @since 1.0.0
     * @access public
     * @static
     * @return string
     * 
     */
    public static function get_base(){
        return self::$base;
    }

    /**
     * Define constants
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $set
     * @param boolean $read_base
     * 
     */
    public static function define($set, $read_base = false){
        $globals = self::globals($read_base);
        
        foreach($globals["define"][$set] as $key => $val){
            $define = self::$prefix . strtoupper($key);
            defined($define) or define($define, $val);
        }
    }
    
    /**
     * Get defined constant name
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $key
     * @return constant
     * 
     */
    public static function defined($key){
      return constant( self::$prefix . strtoupper($key) );
    }

    /**
     * Get main settings
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $key
     * @return array/string
     * 
     */
    public static function getMain($key=''){
      $main = self::$globals['define']['main'];
      return $key !== '' ? $main[$key] : $main;
    }
    
    /**
     * Get path
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $path
     * @return string
     * 
     */
    public static function getPath($path){
      return self::$globals['define']['path'][$path];
    }
    
    /**
     * Get url
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $url
     * @return string
     * 
     */
    public static function getUrl($url){
      return self::$globals['define']['url'][$url];
    }
    
    /**
     * Get category settings
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $cat
     * @return array
     * 
     */
    public static function getCategory($cat=''){
      $globals = self::$globals["category"];
      return $cat !== '' ? $globals[$cat] : $globals;
    }
    
    /**
     * Get page settings
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $page
     * @return array
     * 
     */
    public static function getPage($page=''){
      $globals = self::$globals["page"];
      return $page !== '' ? $globals[$page] : $globals;
    }
    
    /**
     * Get DB settings
     *
     * @since 1.0.0
     * @access public
     * @static
     * @param string $cat
     * @return array
     * 
     */
    public static function getDB($db=''){
      $globals = self::$globals["db"];
      return $db !== '' ? $globals[$db] : $globals;
    }
    
    
    /**
     * Plugin settings
     *
     * @since 1.0.0
     * @access private
     * @static
     * @param boolean $read_base
     * 
     */
    private static function globals($read_base=false){
      if(self::$base == '') return [];
      
      if($read_base === true){
        $base_path = File_Handler::safe_path( self::defined('path') );
        $base_url  = self::defined('url');
      }
      $ds = DIRECTORY_SEPARATOR;
      return  [
          // Constants
          'define'=>[
              // main
            'main'=>[
              'ver'        => '1.0.0', // plugin version
              'name'       => 'SMCstylus Addons for Elementor',
              'textdomain' => 'smcstylus-addons-for-elementor',
              'slug'       => 'smcstylus-addel',
              'link'       => 'https://wp.smcstylus.com/elementor-addons/',
              'doc'       => 'https://wp.smcstylus.com/elementor-addons/doc/',
            
              'base'       => plugin_basename( self::$base ),
              'path'       => plugin_dir_path( self::$base ),
              'url'        => plugin_dir_url( self::$base ),
              'js_sufix'   => ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min',
              'css_sufix'  => ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min',
              'opt_prefix' => 'smcstylus_addel_',
            ],

            'path'=>[
              // Folders
              'addons'    => $base_path . 'addons' .$ds ,
              'assets'    => $base_path . 'assets' .$ds ,
              'core'      => $base_path . 'core' .$ds ,
              'language'  => $base_path . 'languages' .$ds ,
              'templates' => $base_path . 'templates' .$ds ,
              // Subfolders
              'admin'     => $base_path . 'core' .$ds .'admin' .$ds ,
              'controls'  => $base_path . 'addons' .$ds .'controls' .$ds ,
              'modules'   => $base_path . 'addons' .$ds .'modules' .$ds ,
              'extensions'   => $base_path . 'addons' .$ds .'extensions' .$ds ,
              'widgets'   => $base_path . 'addons' .$ds .'widgets' .$ds ,
              'generated_assets' => wp_upload_dir()['basedir'] . $ds .'smcstylus-addons-for-elementor' . $ds,

            ],
            
            'url' => [
              'addons'    => $base_url . 'addons/' ,
              'assets'    => $base_url . 'assets/' ,
              'core'      => $base_url . 'core/' ,
              'language'  => $base_url . 'languages/' ,
              'templates' => $base_url . 'templates/' ,
              'generated_assets' => wp_upload_dir()['baseurl'] . '/smcstylus-addons-for-elementor/',
              
              'assets_admin'=> [
                'css'    => $base_url . 'assets/admin/css/' ,
                'js'    => $base_url . 'assets/admin/js/' ,
                'img'    => $base_url . 'assets/admin/images/' ,
                'fonts'  => $base_url . 'assets/admin/fonts/' ,
                'vendor' => $base_url . 'assets/admin/vendor/'
                ],

              'assets_front'=> [
                'css'    => $base_url . 'assets/front/css/' ,
                'jss'    => $base_url . 'assets/front/js/' ,
                'img'    => $base_url . 'assets/front/images/' ,
                'fonts'  => $base_url . 'assets/front/fonts/' ,
                'vendor' => $base_url . 'assets/front/vendor/'
                ],
              
              'controls'  => $base_url . 'addons/controls/' ,
              'modules'   => $base_url . 'addons/modules/' ,
              'widgets'   => $base_url . 'addons/widgets/' ,
              'extensions'=> $base_url . 'addons/extensions/' ,
            ]
          ],
          
          // Options constants
          'category' => [
            'options' => [
                'slug' => 'smcstylus-addel-',
                'base' => 'basic',
            ],
            'categories' => [
                'basic' => [
                  'name' => 'SMCstylus basic addons',
                  'icon' => 'fa fa-plug'
                ],
                'header_footer' => [
                  'name' => 'SMCstylus posts addons',
                  'icon' => 'fa fa-plug'
                ],
              ],
            ],
            
          'page' => [
              'admin-menu' => [
                'name'  => 'SMCstylus Addons',
                'label' => 'SMSstylus Addons for elementor',
                'desc'  => '',
                'icon'  => $base_url . 'assets/admin/images/icon-smc-menu.svg'
                
              ],
              'admin-settings' => [
                'name'  => '',
                'label' => 'SMCstylus Addons for Elementor Settings',
                'desc'  => '',
                'icon'  => $base_url . 'assets/admin/images/icon-smc-logo.svg',
                'url'   => ''
              ],
              'admin-settings-general' => [
                'name'  => '',
                'label' => 'Info',
                'desc'  => '',
                'icon'  => $base_url . 'assets/admin/images/icon-documentation.svg',
                'url'   => 'general'
              ],
              'admin-settings-elements' => [
                'name'  => '',
                'label' => 'Widgets',
                'desc'  => '',
                'icon'  => $base_url . 'assets/admin/images/icon-elements.svg',
                'url'   => 'widgets'
              ],
              'admin-settings-extensions' => [
                'name'  => '',
                'label' => 'Extensions',
                'desc'  => '',
                'icon'  => $base_url . 'assets/admin/images/icon-extensions.svg',
                'url'   => 'extensions'
              ],
              'admin-settings_rem_-api' => [
                'name'  => '',
                'label' => 'Api',
                'desc'  => '',
                'icon'  => $base_url . 'assets/admin/images/icon-tools.svg',
                'url'   => 'api'
              ],
          ],
          
          'db' => [
            'general_settings'  => 'smc_addel_general_settings',
            'addons_settings' => 'smc_addel_addons_settings',
            'api_settings' => 'smc_addel_api_settings',
            'tools_settings' => 'smc_addel_tools_settings',
            'elementor_values' => 'smc_addel_elementor_values',
          ],
          
        ];
    }
  }
  SMC_Plugin_Globals::instance();