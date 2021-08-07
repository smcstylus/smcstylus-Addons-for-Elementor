<?php
namespace SMCstylus_Elementor\Core\Traits;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;
use SMCstylus_Elementor\File_Handler;

/**
* Trait: SMC_Admin_Utils
* Description: Admin functions
* 
* @since 1.0.0
* @package: SMCstylus Addons For Elementor
* 
*/
trait SMC_Admin_Utils{
    /**
    * 
    * Ativation Hook
    * 
    * @since 1.0.0
    * @access public
    * 
    */
    public function activation_hook(){
        // Add widgets / extensions to db
		$this->update_db_addons(SMC_Plugin_Globals::getDB('addons_settings'), ['widgets'=>$this->widgets_registered], ['extensions'=>$this->extensions_registered]);
        
        // Add api settings to db
        $this->install_db_addons_api();
	}
    
    /**
	 * 
	 * Helper function used for flag
	 * 
	 * @since 1.0.0
	 * @access protected
	 * @param function $func
	 * @param boolean $next
	 * @return boolean
	 * 
	 */
    protected function do_next($func, $next = '') {
    // Next must be boolean and true to continue
      if($next !== true){
        return false;
      }
      // Callback must respond with true
      if(call_user_func([$this, $func]) === true){
        return true;
      }
      
      return false;
    }
    
    /**
	 * 
	 * Get API defaults options
	 * 
	 * @since 1.0.0
	 * @access private
	 * @return array
	 * 
	 */
    private function get_addons_api_defaults(){
        $opt_name_api = SMC_Plugin_Globals::getDB('api_settings');
        $dbval_api    = get_option( $opt_name_api );
        
        $dbval_api_defaults = SMC_Addons_Globals::$api_defaults;
        return [
            'db'=>$dbval_api,
            'defaults'=>$dbval_api_defaults,
            'option'=>$opt_name_api,
        ];
    }
    
    /**
	 * 
	 * Set API Setings
	 * 
	 * @since 1.0.0
	 * @access private
	 * 
	 */
	private function install_db_addons_api(){
        $settings = $this->get_addons_api_defaults();
        
        if ( false === $settings['db'] ) {
            add_option( $settings['option'], $settings['defaults'] );
        }else{
            if ( $settings['db'] === $settings['defaults'] ) {
                //new value is same as old
            } else {
            update_option( $settings['option'], array_merge($settings['defaults'], $settings['db']) );
            }
        }   
    }
    
    /**
	 * 
	 * Update API Settings
	 * 
	 * @since 1.0.0
	 * @access private
	 * 
	 */
    private function update_db_addons_api($new=[]){
        $settings = $this->get_addons_api_defaults();
        
        if ( false === $settings['db'] ) {
            $update_default = array_merge($settings['defaults'], $new);
            add_option( $settings['option'], $update_default );
        }else{
            if ( $settings['db'] === $new ) {
                //new value is same as old
            } else {
                update_option( $settings['option'], array_merge($settings['db'], $new) );
            }
        }   
    }
    
    /**
	 * 
	 * Update Addons Settings
	 * 
	 * @since 1.0.0
	 * @access private
	 * 
	 */
	private function update_db_addons($opt_name, $widgets, $extensions){
        $dbval = get_option( $opt_name );
        $defaults = array_merge($widgets, $extensions);
        
        if ( false !== $dbval ) {
            // option exist
            if ( $dbval === $defaults ) {
                //new value is same as old
            } else {
                //new value is different
                update_option( $opt_name, $defaults );
            }
        } else {
            // option not exist
            add_option( $opt_name, $defaults );
        }
      }
    
    /**
	 * 
	 * Chheck Addon Status
	 * 
	 * @since 1.0.0
	 * @access public
     * @param string $addon
     * @param string $list
     * @return boolean
	 * 
	 */
    public function check_addon_status($addon, $list){
        return $list[$addon]['enabled'] === 1 ? true : false;
    }
    
    /**
	 * 
	 * Check Posted Addon
	 * 
	 * @since 1.0.0
	 * @access public
     * @param integer/boolean/string $v
     * @return integer
	 * 
	 */
    public function check_posted_addon_status($v){
        return $v === 1 || $v === true || $v === 'on' ? 1 : 0;
    }
    
    /**
	 * 
	 * Build New Settings
	 * 
	 * @since 1.0.0
	 * @access public
     * @param array $db
     * @param array $posted
	 * 
	 */
    public function build_new_settings($db, $posted) {
        // Set new array equal as the original
        $new = $db;
        
        /** 
         * Unset the keys from original if exist in posted (checked inputs post) and add the rest of sub array at key from db
         */
        foreach($posted as $k =>$v){
            if(array_key_exists($k, $new)){
               $posted[$k] = array_merge($db[$k],['enabled'=>1]);
                unset($new[$k]);
            }
        }
        
        /**  
         * The remained keys are the keys that aren't posted (unchecked inputs)
         * We fill all the new remained keys with zero (same as being uncheked)
         * We merge the posted array with this one in order to recreate the new  array which may contain all the keys.
        */
        $new = array_merge(
            array_map(function($a) { return array_merge($a,['enabled'=>0]); }, $new )
            ,$posted
            ) ;
        
        return $new;
    }
    
    /**
	 * 
	 * Check All Addons Status
	 * 
	 * @since 1.0.0
	 * @access public
     * @return boolean
	 * 
	 */
    public function check_all_addon_status() {
        $addons = array_merge($this->widgets_registered,$this->extensions_registered);
        foreach($addons as $k => $v){
            if($v['enabled'] === 1 )
            return true;
        }
        return false;
    }
    
    /**
     * Check if wp running in background
     *
     * @since 1.0.0
     * @access public
     * @return boolean
     */
    public function is_running_background() {
        if (wp_doing_cron()) {
            return true;
        }

        if (wp_doing_ajax()) {
            return true;
        }
        
        if (!empty($_REQUEST['action']) && !$this->check_background_action($_REQUEST['action'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if elementor edit mode or not
     *
     * @since 1.0.0
     * @access public
     * @return boolean
     */
    public function is_edit_mode() {
        if (isset($_REQUEST['elementor-preview'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if elementor edit mode or not
     *
     * @since 1.0.0
     * @access public
     * @return boolean
     */
    public function is_preview_mode() {
        if (isset($_REQUEST['elementor-preview'])) {
            return false;
        }

        if (!empty($_REQUEST['action']) && !$this->check_background_action($_REQUEST['action'])) {
            return false;
        }

        return true;
    }
     /**
     * Allow to load asset for some pre defined action query param in elementor preview
		 * $allow_action = ['allowed_action'];
     *
     * @since 1.0.0
     * @access public
     * @return boolean
     */
    public function check_background_action($action_name, $allow_action){
        if (in_array($action_name, $allow_action)){
            return true;
        }
        return false;
    }
    
    /**
     * SVG Inline Icons - reduce http requests
     *
     * @since 1.0.0
     * @access private
     * @return string
     */
    private function get_admin_svg_icons($icn){
        switch($icn){
            case'help':
                echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M 12 2 C 6.4889971 2 2 6.4889971 2 12 C 2 17.511003 6.4889971 22 12 22 C 17.511003 22 22 17.511003 22 12 C 22 6.4889971 17.511003 2 12 2 z M 12 4 C 16.430123 4 20 7.5698774 20 12 C 20 16.430123 16.430123 20 12 20 C 7.5698774 20 4 16.430123 4 12 C 4 7.5698774 7.5698774 4 12 4 z M 12 6 C 9.79 6 8 7.79 8 10 L 10 10 C 10 8.9 10.9 8 12 8 C 13.1 8 14 8.9 14 10 C 14 12 11 12.367 11 15 L 13 15 C 13 13.349 16 12.5 16 10 C 16 7.79 14.21 6 12 6 z M 11 16 L 11 18 L 13 18 L 13 16 L 11 16 z"></path></svg>';
            break;
            case'demo':
                echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path d="M 5 4 C 3.895 4 3 4.895 3 6 L 3 20 C 3 21.105 3.895 22 5 22 L 12.75 22 L 12.25 24 L 11 24 A 1.0001 1.0001 0 1 0 11 26 L 19 26 A 1.0001 1.0001 0 1 0 19 24 L 17.75 24 L 17.25 22 L 25 22 C 26.105 22 27 21.105 27 20 L 27 6 C 27 4.895 26.105 4 25 4 L 5 4 z M 5 6 L 25 6 L 25 18 L 5 18 L 5 6 z M 15 19 C 15.552 19 16 19.448 16 20 C 16 20.552 15.552 21 15 21 C 14.448 21 14 20.552 14 20 C 14 19.448 14.448 19 15 19 z"></path></svg>';
            break;
        }  
    }
}