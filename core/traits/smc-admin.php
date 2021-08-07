<?php
namespace SMCstylus_Elementor\Core\Traits;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit;

use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;
use SMCstylus_Elementor\File_Handler;

use \Elementor\Plugin;
/**
 * Trait: SMC_Admin
 * Description: Admin functions
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
trait SMC_Admin {
    /**
	 * Show the plugin action links on the plugin screen.
	 *
     * 
	 * @since 1.0.0
	 * @access public
     * @param   mixed $links Plugin Action links.
	 * @return  array
     * 
	 */
	public function plugin_action_links( $links  ) {
		// Build and escape the URL.
		$url = esc_url( admin_url( 'options-general.php?page=' . SMC_Plugin_Globals::getMain('slug') ));
		$label = sprintf(esc_attr__( 'View %1$s Settings', SMC_Plugin_Globals::getMain('textdomain') ), SMC_Plugin_Globals::getMain('name') );
		$txt = esc_html__( 'Settings', SMC_Plugin_Globals::getMain('textdomain') );
		
		$settings_links = array(
			'settings' => '<a href="' . $url . '" aria-label="' . $label . '">' . $txt . '</a>',
		);
		return array_merge($links , $settings_links);
	}
    
    /**
     * Create an admin menu.
     *
	 * @since 1.0.0
	 * @access public
     */
    public function admin_menu() {
        add_menu_page(
            __( SMC_Plugin_Globals::getMain('name'), $textdomain ),
            __( SMC_Plugin_Globals::getMain('name'), $textdomain ),
            'manage_options',
            SMC_Plugin_Globals::getMain('slug'),
            [$this, 'admin_settings_page'],
            File_Handler::safe_url( (SMC_Plugin_Globals::getUrl('assets_admin'))['img'] . 'ea-icon-white.svg' ),
            '58.6'
        );
    }
    
    /**
     * Extending plugin row meta
     *
	 * @since 1.0.0
	 * @access public
     * @param array $links
     * @param string $file
     * @return array
     */
    public function insert_plugin_row_meta($links, $file){
        $op = SMC_Plugin_Globals::getMain();
        if ($op['base'] === $file) {
            // docs & faq
            $links[] = sprintf('<a href="%1$s" target="_blank">' . __('Docs & FAQs', $op['textdomain']) . '</a>', esc_attr($op['doc']));
        }
        return $links;
    }
    
    /**
     * Show admin settings page
     * 
	 * @since 1.0.0
	 * @access public
     */
    public function admin_settings_page() {
        $version = self::VERSION;
        $textdomain  = SMC_Plugin_Globals::getMain('textdomain') ;
        $tpl_path    = SMC_Plugin_Globals::getPath('templates') . 'admin/';
        $admin_pages = SMC_Plugin_Globals::getPage();
        $status = checked( 1, $this->check_all_addon_status(), false );
        ?>
        <div class="smc-addel-settings-wrap">
            <form action="" method="POST" id="smc-addel-settings" name="smc-addel-settings">
                <div class="smc-addel-header-bar">
                    <div class="smc-addel-header-left">
                        <div class="smc-addel-admin-logo-inline">
                            <img src="<?php echo $admin_pages['admin-settings']['icon']; ?>"
                                 alt="smcstylus-addons-for-elementor">
                        </div>
                        <h2 class="title">
                        <?php 
                        echo __( $admin_pages['admin-settings']['label'], $textdomain ); 
                        ?>  </h2>
                    </div>
                    <div class="smc-addel-header-right">
                        <button type="submit"
                                class="button smc-addel-btn js-eael-settings-save"><?php echo __( 'Save settings', $textdomain ); ?></button>
                        
                    </div>
                </div>
                <div class="elements-global-control-wrap">
                    <div class="smc-addel-checkbox smc-addel-toogle-all">
                        <i  class="smc-addel-toogle-title">
                        <?php echo __( 'Enable / Disable All addons', $textdomain ); ?> </i>
                        <input type="checkbox" id="toogle_all_addons" name="toogle_all_addons" <?php echo $status; ?>>
                        <label for="toogle_all_addons" class=""></label>
                    </div>
                </div>
                <div class="smc-addel-settings-tabs">
                    <ul class="smc-addel-tabs">
                    <?php
                    // Generate admin page tabs
                    foreach ( $admin_pages as $key => $value){
                        if(strpos($key, 'admin-settings-') !== false){
                            echo sprintf(' 
                            <li>
                                <a href="#%1$s" class="%4$s">
                                    <img src="%2$s" alt="smcstylus-addons-%1$s"><span>%3$s</span>
                                </a>
                            </li>',
                            $value['url'],
                            $value['icon'],
                            __( $value['label'], $textdomain ),
                            ($key==='admin-settings-general')?'active':''
                            );
                        }
                    }
                    ?>
                        
                    </ul>
                    <?php
                    foreach ( $admin_pages as $key => $value){
                        if(strpos($key, 'admin-settings-') !== false){
                            include_once $tpl_path. 'tpl-' . $value['url'] . '.php';
                        }
                    }
                    ?>
                </div>
            </form>
        </div>
        <?php
    }

   /**
     * Load admin scripts
     *
	 * @since 1.0.0
	 * @access public
     */
    public function load_admin_scripts( $hook ) {
        $textdomain = $this->$cfgMain['textdomain'] ;
        $version = $this->VERSION;
        $admin_assets = SMC_Plugin_Globals::getUrl('assets_admin');
        
        wp_enqueue_style( 'smcstylus_addons_elementor-admin-css', $admin_assets['css'] . 'admin.css', false, $version );
       // wp_enqueue_style( 'smcstylus_addons_elementor-smc-icons-css', $admin_assets['css'] . 'smc-icons.css', false, $version );
        wp_enqueue_style( 'sweetalert2-css', $admin_assets['vendor'] . 'sweetalert2/css/sweetalert2.min.css', false, $version );
        
        wp_enqueue_script( 'sweetalert2-js', $admin_assets['vendor'] . 'sweetalert2/js/sweetalert2.min.js', array( 'jquery', 'sweetalert2-core-js' ), $version, true );
        wp_enqueue_script( 'sweetalert2-core-js', $admin_assets['vendor'] . 'sweetalert2/js/core.js', array( 'jquery' ), $version, true );
        wp_register_script( 'smcstylus_addons_elementor-admin-js', $admin_assets['js'] . 'admin.js', array( 'jquery' ), $version, true );

        //Internationalizing JS string translation
        $i18n = [
            'smc_admin' => [
                'all'       => __( 'All', $textdomain ),
                'cancel'    => __( 'Cancel', $textdomain ),
                'save'      => __( 'Save', $textdomain ),
                'save_data' => __( 'Saving Data', $textdomain ),
                'saving_settings' => __( 'Saving Settings', $textdomain ),
                'save_settings'   => __( 'Save Settings', $textdomain ),
                'saved_settings'  => __( 'Settings Saved!', $textdomain ),
                'footer_save_msg' => __( 'Enjoy using SMCstylus Addons for Elementor!', $textdomain ),
                'err_title'       => __( 'Oops...', $textdomain ),
                'err_text'        => __( 'Something went wrong!', $textdomain ),
            ]
        ];

        wp_localize_script( 'smcstylus_addons_elementor-admin-js', 'localize', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'smcstylus-addons-elementor' ),
            'i18n'    => $i18n,
        ) );
        wp_enqueue_script( 'smcstylus_addons_elementor-admin-js' );
    }
  
    /**
     * Saving data with ajax request
     * 
	 * @since 1.0.
	 * @access public
     * @return  array
     */
    public function save_settings() {
        // Security check
        check_ajax_referer( 'smcstylus-addons-elementor', 'smc_nonce' );
        
        // Set some constants/variables
        $textdomain          = $this->$cfgMain['textdomain'] ;
        $opt_name_addons     = SMC_Plugin_Globals::getDB('addons_settings');
        $dbval_addons        = get_option( $opt_name_addons );
        
        $widgets_db          = $dbval_addons['widgets'];
        $extensions_db       = $dbval_addons['extensions'];
        
        $widgets_settings    = [];
        $extensions_settings = [];
        $api_settings        = [];
        
        // Stop execution if user doesn't have permission
        if(!current_user_can('manage_options')){
            wp_send_json_error(__('You are not allowed to do this action!', $textdomain));
            return;
        }
        
        // Stop execution if empy post
        if ( !isset( $_POST[ 'fields' ] ) ) {
            wp_send_json_error(__('Something went wrong!', $textdomain));
            return;
        }

        parse_str( $_POST[ 'fields' ], $settings);
        
        // Build arrays for each category
        foreach($settings as $key => $val){
            
            if(strpos($key, 'smc_addel_widget_') === 0){
                $slug = str_replace('smc_addel_widget_', '', $key);
                $widgets_settings[$slug]['enabled'] = $this->check_posted_addon_status($val);
               
                unset($settings[$key]);
            }
            
            //
            if(strpos($key, 'smc_addel_extension_') === 0){
                $slug = str_replace('smc_addel_extension_', '', $key);
                $extensions_settings[$slug]['enabled'] = $this->check_posted_addon_status($val);
                unset($settings[$key]);
            }
            if(strpos($key, 'smc_addel_api_') === 0){
                $api_settings[str_replace('smc_addel_api_', '', $key)] = sanitize_text_field( $val );
                unset($settings[$key]);
            }
        }
        
        $new_extensions = $this->build_new_settings($extensions_db,$extensions_settings);
        $new_widgets = $this->build_new_settings($widgets_db,$widgets_settings);
        
        // Update each category in DB
        // Widgets & extensions
       $this->update_db_addons($opt_name_addons,['widgets'=>$new_widgets],['extensions'=>$new_extensions]);
      
        // Api
        if(!empty($api_settings)){
            $this->update_db_addons_api($api_settings);
        }
        
        // Reload constants
        $this->load_addons_constants();
        
        // Send message
        wp_send_json( 'ok');
        die();
    }

    
    /**
     * Save default values to db - future
     *
	 * @since 1.0.0
	 * @access public
     * @param integer $post_id
     * @param $editor_data
     */
    public function save_global_values($post_id, $editor_data){
        if (wp_doing_cron()) {
            return;
        }
    }
}