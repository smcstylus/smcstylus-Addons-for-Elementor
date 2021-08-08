<?php
defined( 'ABSPATH' ) ||	exit; // No access of directly access.
use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;

/**
 * Widgets settings
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
SMC_Addons_Globals::setGlobal('widgets_groups', 
  [ 
    [
      'name'=>'content-elements' , 
      'options'=>[
        'title' => __( 'Timers Elements', SMC_Plugin_Globals::getMain('textdomain')),
        'widgets'  => [
          'countdown',
        ]
      ]
    ],
    [
      'name'=>'content-elements2'  , 
      'options'=> [
        'title' => __( 'Content Elements2', SMC_Plugin_Globals::getMain('textdomain')),
        'widgets'  => []
      ],
    ]
  ]
);