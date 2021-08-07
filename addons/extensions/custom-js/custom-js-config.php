<?php
defined( 'ABSPATH' ) ||	exit; // No access of directly access.
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;

SMC_Addons_Globals::setGlobal('extensions_list', 
  [ 
    'name'=>'custom-js', 
    'options'=>[
      'title' => 'Custom JS',
      'description' => 'Add custom JS to any widget for Elementor ',
      'icon' => 'fa fa-css3',
      'demo_link' => 'https://wp.smcstylus.com/elementor-addons/',
      'doc_link' => 'https://wp.smcstylus.com/elementor-addons/doc/',
      'keywords' => '',
      
      '__CLASS__' => '\SMCstylus_Elementor\Addons\Extensions\Custom_JS\Custom_JS',
      'category' => '',
      'key' => 'custom-js',
      'group' =>'developer-extensions',
      'enabled' => true,
      'pro'=> false,
      'class'=>'',
      'css'=>[],
      'js'=>[],
    ] 
  ]
);