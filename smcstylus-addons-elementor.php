<?php
/**
 * SMCstylus Addons For Elementor
 *
 * @package           SMCstylus Addons For Elementor
 * @author            Mihai Calin Simion
 * @copyright         2021 Mihai Calin Simion
 * @license           GPL-3.0
 *
 * @wordpress-plugin
 * Plugin Name:       SMCstylus Addons For Elementor
 * Plugin URI:        https://wp.smcstylus.com/elementor-addons/
 * Description:       Description of the plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Mihai Calin Simion
 * Author URI:        https://smcstylus.com
 * Text Domain:       smcstylus-addons-for-elementor
 * Domain Path: /languages
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */


/*
Plugin Name: SMCstylus Addons for Elementor
Description: SMCstylus Addons for Elementor plugin includes CSS and JS extensions, and Countdownd widget (for now)
Plugin URI: https://wp.smcstylus.com/elementor-addons/
Version: 1.0.0
Elementor tested up to:  3.2.5
Elementor Pro tested up to: Not tested
Author: Mihai Calin SIMION
Author URI: https://smcstylus.com/
Text Domain: smcstylus-addons-elementor
Domain Path: /languages
License: GNU General Public License v3.0
*/

// No access of directly access.
defined( 'ABSPATH' ) ||	exit; 

// Load plugin constants
define( 'SMC_ADDEL_FILE', __FILE__ );
define( 'SMC_ADDEL_PATH', plugin_dir_path(SMC_ADDEL_FILE) );

// Load Autoloader
require_once SMC_ADDEL_PATH.'file-handler.php';

use SMCstylus_Elementor\File_Handler;
File_Handler::run();

// Load plugin main class
require_once 'smc-init.php';