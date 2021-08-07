<?php
defined( 'ABSPATH' ) ||	exit; // No access of directly access.
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;

SMC_Addons_Globals::setGlobal('extensions_list', 
  [ 
    'name'=>'custom-css', 
    'options'=>[
      'title' => 'Custom CSS',
      'description' => 'Add custom CSS to any widget for Elementor Free',
      'icon' => 'fa fa-css3',
      'demo_link' => 'https://wp.smcstylus.com/elementor-addons/',
      'doc_link' => 'https://wp.smcstylus.com/elementor-addons/doc/',
      'keywords' => '',
      
      '__CLASS__' => '\SMCstylus_Elementor\Addons\Extensions\Custom_CSS\Custom_CSS',
      'category' => '',
      'key' => 'custom-css',
      'group' =>'developer-extensions',
      'enabled' => true,
      'pro'=> false,
      'class'=>'',
      'css'=>[],
      'js'=>[],
    ] 
  ]
);