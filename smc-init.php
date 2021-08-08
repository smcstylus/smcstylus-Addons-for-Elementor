<?php
namespace SMCstylus_Elementor;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

// Load dependencies
use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals as SMC_Const;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;
use SMCstylus_Elementor\File_Handler;

use SMCstylus_Elementor\Core\Traits\SMC_Admin_Utils;
use SMCstylus_Elementor\Core\Traits\SMC_Compatibility;
use SMCstylus_Elementor\Core\Traits\SMC_Admin_Notices;
use SMCstylus_Elementor\Core\Traits\SMC_Admin;
use SMCstylus_Elementor\Core\Traits\SMC_Extensions;
use SMCstylus_Elementor\Core\Traits\SMC_Widgets;

/**
 * SMCstylus Addons For Elementor - Main Class
 * Description: The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 * @package SMCstylus Addons For Elementor
 */
final class SMC_Elementor_Init {
	// Load traits
	use SMC_Admin_Utils;
	use SMC_Compatibility;
	use SMC_Admin_Notices;
	use SMC_Admin;
	use SMC_Extensions;
	use SMC_Widgets;
	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum WordPress Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum WordPress version required to run the plugin.
	 */
	const MIN_WP_VER = '4.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MIN_ELEMENTOR_VER = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MIN_PHP_VER = '7.0';
	
	private   $cfgMain =[];
	protected $extensions_registered;
	protected $extensions_groups;
	protected $widgets_registered;
	protected $widgets_groups;
	protected $pro_enabled;
	
	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var SMC_Elementor_Init The single instance of the class.
	 */
	private static $_instance = null;
	/**
	 * Instance
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return SMCstylus_addons_elementor An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			// Add action before init
			do_action( 'smcstylus_addons_elementor/before_init' );
			 
			self::$_instance = new self();
			// Add action after init
			do_action( 'smcstylus_addons_elementor/after_init' ); 
		}
		
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function __construct() {
		// Declare constants
		SMC_Const::set_base(SMC_ADDEL_FILE, 'SMC_ADDEL_');
		$this->cfgMain = SMC_Const::getMain();
		
		// Check for Pro version: maybe for future use. keep  for now
		$this->pro_enabled = apply_filters('smcstylus_addons_elementor/pro_enabled', false);
		
		// Exclude templates from index
		add_action('wp_head', [$this, 'noindex_templates']);
		
		// Check for compatibility and load main core
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );

	}
	
	// Protect singleton
	protected function __clone() {}
	public function __wakeup() {throw new Exception("Cannot unserialize singleton");}

	/**
	 * On Plugins Loaded
	 *
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function on_plugins_loaded() {
		// Load language
		$this->i18n();
		
		// Check if the domain PHP version is compatible with the plugin
		$next = true;
    $next = $this->do_next('check_php_version', $next);
		
		// Check if the Elementor plugin is installated
    $next = $this->do_next('check_elementor_plugin', $next);
		
		// Init loader if all the up conditions are satisfated
		if ( $next ) {
			// Load widgets categories
			File_Handler::loadFile(SMC_Const::getPath('widgets') . 'widgets.php');
			// Load extensions categories
			File_Handler::loadFile(SMC_Const::getPath('extensions') . 'extensions.php');
			// Load controls list
			File_Handler::loadFile(SMC_Const::getPath('controls') . 'controls.php');
			// Load extensions and widgets settings
			File_Handler::include_filtered_file(__DIR__ . '/addons');
			
			// Load addons constants in instance
			$this->load_addons_constants();
			$this->install_db_addons_api();
			
			// Load addons
			add_action( 'elementor/init', [ $this, 'init_addons' ] );
			
			// Admin actions
			$this->admin_init();
			
		}else{
			return false;
		}
	}
	
	/**
	 * Load Addons Constants
	 *
	 * Load extensions and widgets constants.
	 *
	 * @since 1.0.0
	 * @access public
	 */ 
	public function load_addons_constants(){
    $opt_name = SMC_Const::getDB('addons_settings');
    $dbval    = get_option( $opt_name );
    $widget_list     = [];
    $extension_list  = [];
    $widget_group    = SMC_Addons_Globals::getGlobal('widgets_groups');
    $extension_group = SMC_Addons_Globals::getGlobal('extensions_groups');
		
		// If not set in db
		if ( false === $dbval ) {
			// Default widgets list
      $widgets    = SMC_Addons_Globals::getGlobal('widgets_list');
			
			// Default extensions list
      $extensions = SMC_Addons_Globals::getGlobal('extensions_list');
			
			// Create widgets list for DB
			foreach($widgets as $key => $val){
				$widget_list[$key] = ['enabled' => ($val['enabled']===true) ? 1 : 0, 'group' => $val['group']];
			}
			
			// Create extensions list for DB
			foreach($extensions as $key => $val){
				$extension_list[$key] = ['enabled' => ($val['enabled']===true) ? 1 : 0, 'group' => $val['group']];
			}
			
			// Add to DB
			$this->update_db_addons($opt_name, ['widgets'=>$widget_list], ['extensions'=>$extension_list]);
		}else{
      $widget_list    = $dbval['widgets'];
      $extension_list = $dbval['extensions'];
		}
		
    $this->widgets_registered    = $widget_list;
    $this->widgets_groups        = $widget_group;
    $this->extensions_registered = $extension_list;
    $this->extensions_groups     = $extension_group;		
	}
		
	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_init() {
		// Admin
		if (is_admin()) {
			// Load settings link in plugin tab
			add_filter( 'plugin_action_links_' . SMC_Const::getMain('base'), [ $this, 'plugin_action_links' ] );
			add_filter('plugin_row_meta', [$this, 'insert_plugin_row_meta'], 10, 2);
		
			// Add admin settings page
			add_action( 'admin_menu', [$this, 'admin_menu'] );
			// Scripts
			add_action('admin_enqueue_scripts', [$this, 'load_admin_scripts']);
			// Ajax
			add_action('wp_ajax_save_settings_with_ajax', [$this, 'save_settings']);
		}
	}
	
	/**
	 * Init addons
	 *
	 * Load extensions, widgets, controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init_addons() {
		add_filter( 'enable_wp_debug_mode_checks', '__return_false' );
		do_action( 'smcstylus_addons_elementor/init_addons' );
		
		//add_action('elementor/editor/after_save', [$this, 'save_global_values'], 10, 2); -future
		
		// Load extensions
		$this->register_extensions();
		
		// Register Widgets Categories
		add_action( 'elementor/elements/categories_registered', [$this, 'register_widgets_categories']);
		
		// Load Controls
		add_action('elementor/controls/controls_registered', array($this, 'register_controls'));
		
		// Load Widgets
		add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
		
		// Print custom JS (custom js extension)
		add_action('wp_print_footer_scripts', [$this, 'print_custom_js']);
	}
	
	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @param string $lang_path The languages directory path.
	 * @access public
	 */
	public function i18n() {
			// Filter the languages directory path and load textdomain
			load_plugin_textdomain( $this->$cfgMain['textdomain'], false, apply_filters( 'smcstylus_addons_elementor/textdomain_path', SMC_Const::getPath('language') ) );
	}
	
	/**
     * Excluding main templates and megamenu content from search engine.
     * See - https://wordpress.org/support/topic/google-is-indexing-elementskit-content-as-separate-pages/
     * --- for future use ----
     * @since 1.0.0
     * @access public
     */
		public function noindex_templates(){
			$tpl = apply_filters('smcstylus_addons_elementor/noindex', 
			[
				'smcstylus_elementor_widget', 
				'smcstylus_elementor_template', 
				'smcstylus_elementor_content'
			]);
			
			if ( in_array( get_post_type(), $tpl) ){
					echo '<meta name="robots" content="noindex, nofollow" />', "\n";
			}
		}
}
// Init class
SMC_Elementor_Init::instance();