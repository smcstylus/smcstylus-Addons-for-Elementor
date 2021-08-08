<?php
defined( 'ABSPATH' ) ||	exit; // No access of directly access.
use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;

/**
 * Extensions settings
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
SMC_Addons_Globals::setGlobal('extensions_groups', 
  [ 
    [
      'name'=>'developer-extensions' , 
      'options'=>[
        'title' => __( 'Developer extensions', SMC_Plugin_Globals::getMain('textdomain')),
        'extensions'  => [
          'custom-css',
          'custom-js',
        ]
      ]
    ],
    [
      'name'=>'content-extensions'  , 
      'options'=> [
        'title' => __( 'Content Extensions', SMC_Plugin_Globals::getMain('textdomain')),
        'extensions'  => []
      ],
    ]
  ]
);