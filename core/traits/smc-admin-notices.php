<?php
namespace SMCstylus_Elementor\Core\Traits;

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

/**
 * Trait: SMC_Admin_Notices
 * Description: Admin notices
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */

trait SMC_Admin_Notices{
  private $admin_notice = [];
  
	/**
	 * Display Admin Notices
	 *
	 * @since 1.0.0
	 * @access public
	 */
  public function print_admin_notice() {
		$msg  = $this->$admin_notice['msg'];
		$btn  = $this->$admin_notice['btn'];
		$icon = esc_url( $this->$cfgMain['url'] . 'assets/images/smcstylus-addons-elementor.png' );
		$icon_html = '<img src="' . $icon . '" class="smcstylus-addons-elementor_notice_icon" alt="SMCstylus.com" title="SMCstylus" >';
		$class = 'smcstylus-addons-elementor_notice notice-error notice notice-warning is-dismissible';
		
		// Build button
		$button = '';
		if(isset($btn['label']) && strlen($btn['label']) > 0 ){
			$button = '<span><a href="' . $btn['url'] . '" class="button-primary">' . $btn['label'] . '</a></span>';
		}
		
		// Thanks message
		/* translators: %1$s: html tag, %2$s: html tag, %1$s: html tag */ 
		$message = sprintf( esc_html__( '%1$s Thanks for choosing %4$s plugin! %2$s %3$s', $this->$cfgMain['textdomain'] ), '<strong>', '</strong>', '<br/>', $this->$cfgMain['name'] );
		// Error message
		$message .= $msg;
		
		// Print the message
		printf( '<div class="%1$s"> %2$s <p> <span> %3$s </span> %4$s </p> </div>', esc_attr( $class ), $icon_html, $message, $button );
	}
}