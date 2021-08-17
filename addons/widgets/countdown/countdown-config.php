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
        'smcstylus-addel-countdown'=>[
          'file_name'=>'countdown',
          'file_url'=>'',
          'version'=>'',
        ]
      ],
      'js'=>[
        'fix-timers'=>[
          'file_name'=>'fix-timers',
          'file_url'=>'',
          'deps'=>[],
          'version'=>'1.0.0',
          'load_in_footer'=>true,
        ],
        'jquery-smcstylusCircleTimers'=>[
          'file_name'=>'jquery.smcstylusCircleTimers',
          'file_url'=>'',
          'deps'=>['jquery'],
          'version'=>'1.0.0',
          'load_in_footer'=>true,
        ],
        'jquery-flipper-timers'=>[
          'file_name'=>'jquery.smcstylusFlipperTimers',
          'file_url'=>'',
          'deps'=>['jquery'],
          'version'=>'1.0.0',
          'load_in_footer'=>true,
        ],
        'jquery-countdown'=>[
          'file_name'=>'jquery.countdown',
          'file_url'=>'',
          'deps'=>['jquery'],
          'version'=>'2.2.0',
          'load_in_footer'=>true,
        ],
        'smcstylus-addel-countdown'=>[
          'file_name'=>'countdown',
          'file_url'=>'',
          'deps'=>['jquery'],
          'version'=>'1.3.0',
          'load_in_footer'=>true,
        ],
      ],
    ] 
  ]
);