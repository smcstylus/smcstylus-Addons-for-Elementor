<?php
defined( 'ABSPATH' ) ||	exit; // No access of directly access.
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;

SMC_Addons_Globals::setGlobal('widgets_list', 
  [ 
    'name'=>'countdown', 
    'options'=>[
      'title' => 'Countdown',
      'description' => 'Countdown',
      'icon' => 'eicon-countdown',
      'demo_link' => 'https://wp.smcstylus.com/elementor-addons/',
      'doc_link' => 'https://wp.smcstylus.com/elementor-addons/doc/',
      'keywords' => ['countdown', 'count', 'time', 'timer', 'day', 'hour', 'minute', 'second' , 'effect', 'smcstylus'],
      
      '__CLASS__' => '\SMCstylus_Elementor\Addons\Widgets\Countdown\Countdown',
      'category' => 'basic',
      'key'      => 'countdown',
      'group'    => 'content-elements',
      'enabled'  => true,
      'pro'      => false,
      'class'    => '',
      'css'=>[
        'countdown'=>[
          'file'=>'',
          'ver'=>'',
        ]
      ],
      'js'=>[
        'countdown'=>[
          'deps'=>['jquery'],
          'file'=>'',
          'ver'=>'',
        ],
        'smcstylusCircleTimers'=>[
          'deps'=>['jquery'],
          'file'=>'',
          'ver'=>'1',
        ],
        'jquery.countdown'=>[
          'deps'=>['jquery'],
          'file'=>'',
          'ver'=>'2.2.0',
        ]
      ],
    ] 
  ]
);