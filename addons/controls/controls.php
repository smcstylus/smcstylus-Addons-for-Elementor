<?php
defined( 'ABSPATH' ) ||	exit; // No access of directly access.
use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;

/**
 * Controls settings
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
SMC_Addons_Globals::setGlobal('controls', 
  [ 
    [
      'name' => 'SMC_Control_Points' ,
      'options'      => [
        'title'      => __( 'Control Points', SMC_Plugin_Globals::getMain('textdomain')),
        '__CLASS__'  => 'SMC_Control_Points',
        'path' => 'points',
        'custom-css' => '',
        'custom-js'  => '',
      ],
    ]
  ]
);