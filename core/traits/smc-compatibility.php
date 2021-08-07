<?php
namespace SMCstylus_Elementor\Core\Traits;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

use SMCstylus_Elementor\Core\Settings\SMC_Constants as SMC_Const;
use SMCstylus_Elementor\Core\Settings\SMC_Globals;
use SMCstylus_Elementor\Core\Helpers\SMC_3rdParty as SMC_3rdparty;

/**
* Trait: SMC_Compatibility
* Description: Admin helper functions
* 
* @since 1.0.0
* @package: SMCstylus Addons For Elementor
* 
*/
trait SMC_Compatibility{
  /**
	 * 
	 * Check PHP version
	 * 
	 * @since 1.0.0
	 * @access public
	 * @return boolean
	 * 
	 */
  public function check_php_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MIN_PHP_VER, '<' ) ) {
			$message = sprintf(
				/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
				esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', $this->$cfgMain['textdomain'] ),
				'<strong>' . esc_html__( $this->$cfgMain['name'], $this->$cfgMain['textdomain'] ) . '</strong>',
				'<strong>' . esc_html__( 'PHP', $this->$cfgMain['textdomain'] ) . '</strong>',
				 self::MIN_PHP_VER
			); 
			
			$this->$admin_notice = [
				'msg' => $message, 
				'btn' => [
					'url' => $btn['url'],
					'label' => $btn['label'],
				]
			];
			
			add_action( 'admin_notices', [ $this, 'print_admin_notice' ] );
			return false;
		}
		return true;
  }
	
  /**
	 * Check for Elementor plugin
	 *
	 * Warning when the site doesn't have Elementor installed, activated or the min version.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return boolean
	 * 
	 */
	public function check_elementor_plugin() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		
		$elementor = 'elementor/elementor.php';
		$name_html = '<strong>' . esc_html__( $this->$cfgMain['name'], $this->$cfgMain['textdomain'] ) . '</strong>';
		$elementor_html = '<strong>' . esc_html__( 'Elementor', $this->$cfgMain['textdomain'] ) . '</strong>';
		$msg = '';
		$btn = [];
		$next = true;
		$print = false;
		
		// Check if Elementor is installed	
		if(SMC_3rdparty::is_elementor_installed() !== true){
			// Request to install elementor
			$btn['url']   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
			$btn['label'] = esc_html__( 'Install Elementor', $this->$cfgMain['textdomain'] );
				
			$message = sprintf(
				/* translators: 1: Plugin name 2: Elementor */
				esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', $this->$cfgMain['textdomain'] ),
				$name_html,
				$elementor_html
			);
			
			// Don't check next but print error
			$next = false;
			$print = true;
		}
		
		// Check if Elementor is activated
		if($next === true && !SMC_3rdparty::is_elementor_active() ){
			$btn['url']   = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $elementor . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $elementor );
			
			$btn['label'] = esc_html__( 'Activate Elementor Now', $this->$cfgMain['textdomain'] );	
				
			$message = sprintf(
				/* translators: 1: Plugin name 2: Elementor */
				esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', $this->$cfgMain['textdomain'] ),
				$name_html,
				$elementor_html
			);
			
			// Don't check next but print error
			$next = false;
			$print = true;
		}
		
		// Check if Elementor is installed and min. version is running	
		if($next === true &&  !SMC_3rdparty::is_elementor_version_compatible(self::MIN_ELEMENTOR_VER) ){
			$message = sprintf(
				/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
				esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', $this->$cfgMain['textdomain'] ),
				$name_html,
				$elementor_html,
				self::MIN_ELEMENTOR_VER
			);
			
			// Don't check next but print error
			$next = false;
			$print = true;
		}
		
		// Print error and stop
		if($print){
			$this->$admin_notice = [
				'msg' => $message, 
				'btn' => [
					'url' => $btn['url'],
					'label' => $btn['label'],
				]
			];
			
			add_action( 'admin_notices', [ $this, 'print_admin_notice' ] );
			return false;
		}
		
		// Continue
		return true;
	}
}