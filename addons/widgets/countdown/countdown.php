<?php
namespace SMCstylus_Elementor\Addons\Widgets\Countdown;
defined('ABSPATH') || exit; // exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager; 

use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;
/**
 * Widget: Coundown
 * Description: C
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 * Mihai Calin Simion
 * https://smcstylus.com
 * 
 */
class Countdown extends Widget_Base {
  private $widget_version = '1.3.0';
  private $widget_key = 'countdown';
  private $widget_url = '';
  private $widget_opt = '';
  private $slug_prefix = '';
  private $opt_prefix = '';
  private $textdomain = '';
  private $css_dependencies = [];
  private $js_dependencies = [];
  
  public function __construct($data = array(), $args = null){
    parent::__construct($data, $args);
    // Set constants
    $this->widget_url  = SMC_Plugin_Globals::getUrl('widgets') ;
    $this->widget_opt  = SMC_Addons_Globals::getGlobal('widgets_list', $this->widget_key);
    $this->slug_prefix = SMC_Plugin_Globals::getCategory('options')['slug'];
    $this->opt_prefix  = SMC_Plugin_Globals::getMain('opt_prefix') . $widget_key . '__';
    $this->textdomain  = SMC_Plugin_Globals::getMain('textdomain');
    
    $this->register_assets();
  }
  
  private function register_assets(){
    // CSS
    foreach($this->widget_opt['css'] as $key => $op){
        $file = ($op['file_url'] === '') ? $this->widget_url . $this->widget_key .'/'.$op['file_name'].'.css' : $op['file_url'];
        $ver = $op['version'] === '' ? $this->widget_version : $op['version'];
        array_push($this->css_dependencies, $key);
        wp_register_style($key, $file, $ver);
    }
    
    // JS
    foreach($this->widget_opt['js'] as $key => $op){
        $file = ($op['file_url'] === '') ? $this->widget_url . $this->widget_key .'/'.$op['file_name'].'.js' : $op['file_url'];
        $dep = $op['deps'];
        $ver = $op['version'] === '' ? $this->widget_version : $op['version'];
        $footer = $op['load_in_footer'] === false ? false : true;
        
        array_push($this->js_dependencies, $key);
        
        wp_register_script($key, $file, $dep, $ver, $footer);
    }
  }
  
  public function get_name(){
      return $this->widget_key;
  }
  
  public function get_title(){
      return __($this->widget_opt['title'], $this->textdomain);
  }
  
  public function get_icon(){
      return $this->widget_opt['icon'];
  }
  
  public function get_categories(){
    $slug = $this->slug_prefix . $this->widget_opt['category'];
    return [$slug];
  }
  
  public function get_keywords(){
      return $this->widget_opt['keywords'];  
  }
  
  public function get_custom_help_url(){
      return $this->widget_opt['doc_link'];
  }

  public function get_style_depends() { 
    return  $this->css_dependencies;
  }

    public function get_script_depends() {
        return  $this->js_dependencies;
    }

       
    /********************   CONTROLS   **********************/ 
    /** HELPERS **/
    private function setDefaultSettingsOption($op, $def) {
        return !empty( $op ) ? $op : $def;
    }
    
    private function controlsPoints($title , $condition=[]){
        $this->add_control(
            'countdown__circleUI_'.$title.'_points_popover_toggle',
            [
                'label' => __( 'Gradient Points', $this->textdomain  ),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'Default', $this->textdomain ),
                'label_on' => __( 'Custom', $this->textdomain ),
                'return_value' => 'yes',
                'condition' =>$condition,
            ]
        );
        
        $this->start_popover();
        $this->add_control(
            'countdown__circleUI_'.$title.'_gradient_point_x1',
            [
                'label' => esc_html__( 'X Start', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 180,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_'.$title.'_gradient_point_y1',
            [
                'label' => esc_html__( 'Y Start', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 180,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 180,
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_'.$title.'_gradient_point_x2',
            [
                'label' => esc_html__( 'X End', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 180,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_'.$title.'_gradient_point_y2',
            [
                'label' => esc_html__( 'Y End', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 180,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
            ]
        );
        $this->end_popover();
    }
    
    private function controlsShadows($title , $condition=[]){
        $this->add_control(
            'countdown__circleUI_'.$title.'_popover_toggle',
            [
                'label' => __( 'Shadow', $this->textdomain  ),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'Default', $this->textdomain ),
                'label_on' => __( 'Custom', $this->textdomain ),
                'return_value' => 'yes',
                'condition' =>$condition,
            ]
        );
        
        $this->start_popover();
        $this->add_control(
            'countdown__circleUI_'.$title.'_shadows_heading',
            [
                'label' => esc_html__( 'Shadows', $this->textdomain ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 180,
                        'step' => 1,
                    ],
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_'.$title.'_shadows_blur',
            [
                'label' => esc_html__( 'Blur Size', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 13,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_'.$title.'_shadows_color',
            [
                'label' => esc_html__( 'Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
            ]
        );
        
        $this->add_control(
            'countdown__circleUI_'.$title.'_shadows_x',
            [
                'label' => esc_html__( 'X Axe', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 13,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_'.$title.'_shadows_y',
            [
                'label' => esc_html__( 'Y Axe', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 13,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
            ]
        );
        $this->end_popover();
    }

    private function controlsColors($heading, $title, $shadows=false, $gradpoints=false, $condition=[]){
        $this->add_control(
            'countdown__circleUI_'.$title.'_colors_heading',
            [
                'label' => esc_html__( $heading, $this->textdomain ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'   =>  $condition,
            ]
        );
        
        $this->add_control(
            'countdown__circleUI_'.$title.'_lineGradient',
            [
                'label'        => esc_html__( 'Enable Gradient', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'   =>  $condition,
            ]
        );
        $this->add_control(
            'countdown__circleUI_'.$title.'_lineColor',
            [
                'label' => esc_html__( 'Time Line Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'condition'   =>  $condition,
            ]
        );
        $this->add_control(
            'countdown__circleUI_'.$title.'_lineColor_end',
            [
                'label' => esc_html__( 'Time Line Second Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'condition'   => array_merge( $condition,[
                    'countdown__circleUI_'.$title.'_lineGradient' => 'yes',
                ]),
            ]
        );
        if($gradpoints=== true){
            $this->controlsPoints($title, ['countdown__circleUI_'.$title.'_lineGradient' => 'yes']);
            //$this->add_control(
            //    'countdown__circleUI_'.$title.'_gradient_points',
            //    [
            //        'label' => esc_html__( 'Gradient Points', $this->textdomain ),
            //        'type' =>'SMC_Control_Points',
            //        'classes' => 'elementor-control-type-dimensions',
            //        'condition'   => array_merge( $condition,[
            //            'countdown__circleUI_'.$title.'_lineGradient' => 'yes',
            //        ]),
            //    ]
            //);
        }
        
        if($shadows === true){
            $this->controlsShadows($title,['countdown__circleUI_'.$title.'_lines_show'=>'yes']);
        }
    }
    
    private function calcGradient($condition, $firstColor, $secondColor, $def1, $def2){
        $settings = $this->get_settings_for_display();
        return ('yes' === $settings[$condition]) 
            ? [
                $this->setDefaultSettingsOption( $settings[$firstColor] , $def1), $this->setDefaultSettingsOption( $settings[$secondColor] , $def2) 
              ] 
            : [
                $this->setDefaultSettingsOption( $settings[$firstColor] , $def1)
              ]
            ;
    }
    
    /** GENERAL - CIRCLE **/
    private function controlsGeneralTabCircle(){
        /* Circle - General Options Section */
        $this->start_controls_section(
            'countdown__circle_general_section',
            [
                'label' => esc_html__( 'Circle Style -  General Options', $this->textdomain ),
                'condition'=>[
                    'countdown__display_style'=>'circle',
                ],
            ]
        );
        
        $this->add_control(
            'countdown__circleUI_lines_heading',
            [
                'label' => esc_html__( 'Lines Width Settings', $this->textdomain ),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'countdown__circleUI_lines_freeWheel',
            [
                'label'        => esc_html__( 'Enable free wheel', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );
        // Default
        $this->add_control(
            'countdown__circleUI_remaining_lineWidth',
            [
                'label' => esc_html__( 'Remaining Time Line width', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'condition'   => [
                    'countdown__circleUI_lines_freeWheel!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'countdown__circleUI_past_lineWidth',
            [
                'label' => esc_html__( 'Expired Time Line Width', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 80,
                ],
                'condition'   => [
                    'countdown__circleUI_lines_freeWheel!' => 'yes',
                ],
            ]
        );
        // Free wheel 
        $this->add_control(
            'countdown__circleUI_remaining_lineWidth_freeWheel',
            [
                'label' => esc_html__( 'Remaining Time Line width', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'condition'   => [
                    'countdown__circleUI_lines_freeWheel' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'countdown__circleUI_past_lineWidth_freeWheel',
            [
                'label' => esc_html__( 'Expired Time Line Width', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
                'condition'   => [
                    'countdown__circleUI_lines_freeWheel' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();

        
        /* Circle past time - section */
        $this->start_controls_section(
            'countdown__circle_past_time_section',
            [
                'label' => esc_html__( 'Circle Style -  Past Time Options', $this->textdomain ),
                'condition'=>[
                    'countdown__display_style'=>'circle',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_past_lines_show',
            [
                'label'        => esc_html__( 'Show Expired Time Line', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->controlsColors('Colors', 'past', true, true, ['countdown__circleUI_past_lines_show'=>'yes']);
        
        $this->end_controls_section();

        
        /* Circles Remaining Time Section */
        $this->start_controls_section(
            'countdown__circle_remaining_time_section',
            [
                'label' => esc_html__( 'Circle Style - Left Time Options', $this->textdomain ),
                'condition'=>[
                    'countdown__display_style'=>'circle',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_remaining_general_heading',
            [
                'label' => esc_html__( 'General', $this->textdomain ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        // Animation style  
        $this->add_control(
            'countdown__circleUI_animationStyle',
            [
                'label'   => esc_html__( 'Animation', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'smooth',
                'options' => [
                    'smooth' => esc_html__( 'Smooth', $this->textdomain ),
                    'ticks'  => esc_html__( 'Ticks', $this->textdomain ),
                ],
                'label_block'=>true,
            ]
        );
        // Animation direction
        $this->add_control(
            'countdown__circleUI_animationDirection',
            [
                'label'   => esc_html__( 'Animation Direction', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'Clockwise',
                'options' => [
                    'Clockwise' => esc_html__( 'Clockwise', $this->textdomain ),
                    'Counter-clockwise'  => esc_html__( 'Counter clockwise', $this->textdomain ),
                    'Both'  => esc_html__( 'Both', $this->textdomain ),
                ],
                'label_block'=>true,
            ]
        );
        // Lines cap
        $this->add_control(
            'countdown__circleUI_remaining_linesCap',
            [
                'label'   => esc_html__( 'Time Line Cap Style', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'round',
                'options' => [
                    'round' => esc_html__( 'Round', $this->textdomain ),
                    'butt'  => esc_html__( 'Butt', $this->textdomain ),
                    'square'  => esc_html__( 'Square', $this->textdomain ),
                ],
                'label_block'=>true,
            ]
        );
        $this->add_control(
            'countdown__circleUI_remaining_uiEfect',
            [
                'label'   => esc_html__( 'Time Line UI Effect', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => '0',
                'options' => [
                    '0' => esc_html__( 'No effect', $this->textdomain ),
                    '1'  => esc_html__( '...', $this->textdomain ),
                    '2'  => esc_html__( '. - .', $this->textdomain ),
                    '3'  => esc_html__( '- . -', $this->textdomain ),
                    '4'  => esc_html__( '. - -', $this->textdomain ),
                    '5'  => esc_html__( '- -', $this->textdomain ),
                    '6'  => esc_html__( '- _ -', $this->textdomain ),
                ],
                'label_block'=>true,
            ]
        );
        
        
        $this->controlsShadows('remaining');
        $this->add_control(
            'countdown__circleUI_remaining_lines_eachOne',
            [
                'label'        => esc_html__( 'Set colors individual', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );
        $this->add_control(
            'countdown__circleUI_remaining_lineGradient',
            [
                'label'        => esc_html__( 'Enable Gradient', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition' => [
                    'countdown__circleUI_remaining_lines_eachOne!'=>'yes',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_remaining_lineColor',
            [
                'label' => esc_html__( 'Time Line Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'countdown__circleUI_remaining_lines_eachOne!'=>'yes',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_remaining_lineColor_end',
            [
                'label' => esc_html__( 'Time Line Second Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'condition'   => [
                    'countdown__circleUI_remaining_lineGradient' => 'yes',
                    'countdown__circleUI_remaining_lines_eachOne!'=>'yes',
                ],
            ]
        );
        
        
        //$this->add_control(
        //    'countdown__circleUI_remaining_gradient_points',
        //    [
        //        'label' => esc_html__( 'Gradient Points', $this->textdomain ),
        //        'type' =>'SMC_Control_Points',
        //        'classes' => 'elementor-control-type-dimensions',
        //        'condition'   => [
        //            'countdown__circleUI_remaining_lineGradient' => 'yes',
        //            'countdown__circleUI_remaining_lines_eachOne!'=>'yes',
        //        ],
        //    ]
        //);

        $this->controlsColors('Days', 'remaining_days', false, false, ['countdown__circleUI_remaining_lines_eachOne'=>'yes']);
        $this->controlsColors('Hours', 'remaining_hours', false, false, ['countdown__circleUI_remaining_lines_eachOne'=>'yes']);
        $this->controlsColors('Minutes', 'remaining_minutes', false, false, ['countdown__circleUI_remaining_lines_eachOne'=>'yes']);
        $this->controlsColors('Seconds', 'remaining_seconds', false, false, ['countdown__circleUI_remaining_lines_eachOne'=>'yes']);

        $this->end_controls_section();

        /* Circle Inner Section */
        $this->start_controls_section(
            'countdown__circleUI_inner_section',
            [
                'label' => esc_html__( 'Circle Style - Inner Options', $this->textdomain ),
                'condition'=>[
                    'countdown__display_style'=>'circle',
                ],
            ]
        );
        
        $this->add_control(
            'countdown__circleUI_inner_show',
            [
                'label'        => esc_html__( 'Colorize Inner Circle', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );
        $this->add_control(
            'countdown__circleUI_inner_gradient',
            [
                'label'        => esc_html__( 'Enable Gradient', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition' => [
                    'countdown__circleUI_inner_show'=>'yes',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_inner_color',
            [
                'label' => esc_html__( 'Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'countdown__circleUI_inner_show'=>'yes',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_inner_color_end',
            [
                'label' => esc_html__( 'Second Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'condition'   => [
                    'countdown__circleUI_inner_gradient' => 'yes',
                    'countdown__circleUI_inner_show'=>'yes',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_inner_size',
            [
                'label' => esc_html__( 'Inner color size', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'condition'   => [
                    'countdown__circleUI_inner_show' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_inner_shadowBlur',
            [
                'label' => esc_html__( 'Shadow Blur', $this->textdomain ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'condition'   => [
                    'countdown__circleUI_inner_show' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'countdown__circleUI_inner_shadowColor',
            [
                'label' => esc_html__( 'Shadow Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'condition'   => [
                    'countdown__circleUI_inner_show'=>'yes',
                ],
            ]
        );
 
        $this->end_controls_section();
    }
    /** GENERAL - CIRCLE **/
    private function controlsGeneralTabFlipper(){
        /* Circle - General Options Section */
        $this->start_controls_section(
            'countdown__flipper_general_section',
            [
                'label' => esc_html__( 'Flipper Style -  General Options', $this->textdomain ),
                'condition'=>[
                    'countdown__display_style'=>'flipper',
                ],
            ]
        );
        
        $this->add_control(
			'countdown__flipper_note',
			[
				'label' => __( 'Note', $this->textdomain ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Select "test" option will set the countdown for X secconds. Usefull if you want to see the action.', $this->textdomain ),
			]
		);
        
        $this->add_control(
            'countdown__flipper_timers_type',
            [
                'label'   => esc_html__( 'Environement', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'dueDate',
                'options' => [
                    'dueDate'   => esc_html( 'Countdown', $this->textdomain ),
                    'clock'   => esc_html__( 'Clock', $this->textdomain ),
                    'test5'   => esc_html__( 'Test 5 seconds', $this->textdomain ),
                    'test15'   => esc_html__( 'Test 15 seconds', $this->textdomain ),
                    'test70'   => esc_html__( 'Test 70 seconds', $this->textdomain ),
                ],
            ]
        );

        $this->add_control(
            'countdown__flipper_time_template',
            [
                'label'   => esc_html__( 'Date & Time Templates', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    //'0'   => esc_html( 'd:H:i:s'),
                    '1'   => esc_html( 'dd:HH:ii:ss'),
                    '2'   => esc_html__( 'ddd:HH:ii:ss' ),
                ],
            ]
        );
        $this->end_controls_section();
    }
    
    /** GENERAL - REGULAR **/
    private function controlsGeneralTabRegular(){
        /* Regular style section */
        $this->start_controls_section(
            'countdown__regular_style_section',
            [
                'label' => esc_html__( 'Flat Style Options', $this->textdomain ),
                'condition'=>[
                    'countdown__display_style'=>'flat',
                ],
            ]
        );
            
            $this->add_responsive_control(
                'countdown__regularUI_columnWidth',
                [
                    'label'   => esc_html__( 'Column Width', $this->textdomain ),
                    'type'    => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 2000,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'size_units' => [ '%', 'px' ],
                    'selectors'  => [
                        '{{WRAPPER}} .smc-addel-countdown--timer' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'countdown__regularUI_columnHeight',
                [
                    'label'   => esc_html__( 'Column Height', $this->textdomain ),
                    'type'    => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 2000,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'size_units' => [ '%', 'px' ],
                    'selectors'  => [
                        '{{WRAPPER}} .smc-addel-countdown--timer' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'countdown__regularUI_columnSpacing',
                [
                    'label' => esc_html__( 'Column Spacing', $this->textdomain ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .smc-addel-countdown--timer + .smc-addel-countdown--timer' => 'margin-left:{{SIZE}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();
    }
    
    /** STYLE - CIRCLE **/
    private function controlsStyleTabCircle(){
        // Number style tab start
        $this->start_controls_section(
            'countdown__circleUI_numbers_section',
            [
                'label'     => esc_html__( 'Time Style', $this->textdomain ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'countdown__display_style'=>'circle',
                ],
            ]
        );
            
        $this->add_control(
            'countdown__circleUI_numbers_color',
            [
                'label' => esc_html__( 'Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .smcstylusCircleTimers .smcstylusCircleTimers--number' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
			'countdown__circleUI_numbers_fontFamily',
			[
				'label' => esc_html__( 'Font Family', $this->textdomain  ),
				'type' => \Elementor\Controls_Manager::FONT,
				'default' => "'Open Sans', sans-serif",
                'selectors' => [
                    '{{WRAPPER}} .smcstylusCircleTimers   .smcstylusCircleTimers--number'=>"font-family:{{VALUE}};",
                ],
				
			]
		);
        $this->add_control(
            'countdown__circleUI_numbers_fontSize',
            [
                'label' => esc_html__( 'Size', $this->textdomain  ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [  ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 70,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 28,
                ],
            ]
        );
        $this->add_control(
			'countdown__circleUI_numbers_fontStyle',
			[
                'label' => esc_html__( 'Style', $this->textdomain  ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'Default', $this->textdomain  ),
                    'normal' => esc_html__( 'Normal', $this->textdomain  ),
                    'italic' => esc_html__( 'Italic', $this->textdomain  ),
                    'oblique' => esc_html__( 'Oblique', $this->textdomain  ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .smcstylusCircleTimers   .smcstylusCircleTimers--number'=>'font-style:{{VALUE}};',
                ],
			]
		);
        $this->add_control(
			'countdown__circleUI_numbers_fontWeight',
			[
                'label' => esc_html__( 'Weight', $this->textdomain  ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'Initial', $this->textdomain  ),
                    'inherit' => esc_html__( 'Inherit', $this->textdomain  ),
                    'normal' => esc_html__( 'Normal', $this->textdomain  ),
                    'bold' => esc_html__( 'Bold', $this->textdomain  ),
                    'bolder' => esc_html__( 'Bolder', $this->textdomain  ),
                    'lighter' => esc_html__( 'Lighter', $this->textdomain  ),
                    '100' => '100' ,
                    '200' => '200'  ,
                    '300' => '300' ,
                    '400' => '400' ,
                    '500' => '500'  ,
                    '600' => '600' ,
                    '700' => '700'  ,
                    '800' => '800' ,
                    '900' => '900' ,
                ],
                'selectors' => [
                    '{{WRAPPER}} .smcstylusCircleTimers   .smcstylusCircleTimers--number'=>'font-weight:{{VALUE}};',
                ],
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'countdown__circleUI_numbers_text_shadow',
                'label' => esc_html__( 'Text Shadow', $this->textdomain ),
                'selector' => '{{WRAPPER}}  .smcstylusCircleTimers .smcstylusCircleTimers--number',
            ]
        );

        $this->add_responsive_control(
            'countdown__circleUI_numbers_margin',
            [
                'label' => esc_html__( 'Margin', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .smcstylusCircleTimers .smcstylusCircleTimers--number' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );

        $this->end_controls_section(); // Timer style tab end
        
         // Label Style tab section
         $this->start_controls_section(
            'countdown__circleUI_labels_section',
            [
                'label' => esc_html__( 'Labels', $this->textdomain ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'   => [
                    'countdown__hide_labels!' => 'yes',
                    'countdown__display_style'=>'circle',
                ],
            ]
        );
            $this->add_control(
                'countdown__circleUI_labels_color',
                [
                    'label' => esc_html__( 'Color', $this->textdomain ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .smcstylusCircleTimers .smcstylusCircleTimers--label' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'countdown__circleUI_labels_fontSize',
                [
                    'label' => esc_html__( 'Size', $this->textdomain  ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px'],
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 70,
                            'step' => 0.1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 10,
                    ],
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'countdown__circleUI_labels_typography',
                    'label' => esc_html__( 'Typography', $this->textdomain ),
                    'selector' => '{{WRAPPER}} .smcstylusCircleTimers .smcstylusCircleTimers--label',
                    'exclude' => [ 'font_size', 'line-height' ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'countdown__circleUI_labels_text_shadow',
                    'label' => esc_html__( 'Text Shadow', $this->textdomain ),
                    'selector' => '{{WRAPPER}} .smcstylusCircleTimers .smcstylusCircleTimers--label',
                ]
            );


            $this->add_responsive_control(
                'countdown__circleUI_labels_margin',
                [
                    'label' => esc_html__( 'Margin', $this->textdomain ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .smcstylusCircleTimers .smcstylusCircleTimers--label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

        $this->end_controls_section(); // Label style tab end
    }
    
    /** STYLE - REGULAR **/
    private function controlsStyleTabRegular(){
        /* Regular style Item Style tab section */
        $this->start_controls_section(
            'countdown__regularUI_timers_section',
            [
                'label' => esc_html__( 'Timers', $this->textdomain ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'countdown__display_style'=>'flat',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'countdown__regularUI_timers_background',
                'label' => esc_html__( 'Background', $this->textdomain ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .smc-addel-countdown--timer',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'countdown__regularUI_timers_boxShadow',
                'label' => esc_html__( 'Box Shadow', $this->textdomain ),
                'selector' => '{{WRAPPER}} .smc-addel-countdown--timer',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'countdown__regularUI_timers_borders',
                'label' => esc_html__( 'Border', $this->textdomain ),
                'selector' => '{{WRAPPER}} .smc-addel-countdown--timer',
            ]
        );

        $this->add_responsive_control(
            'countdown__regularUI_timers_borderRadius',
            [
                'label' => esc_html__( 'Border Radius', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown--timer' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_responsive_control(
            'countdown__regularUI_timers_padding',
            [
                'label' => esc_html__( 'Padding', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown--timer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_responsive_control(
            'countdown__regularUI_timers_margin',
            [
                'label' => esc_html__( 'Margin', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown--timer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_responsive_control(
            'countdown__regularUI_timers_contentAlign',
            [
                'label' => esc_html__( 'Content Alignment', $this->textdomain ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', $this->textdomain ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', $this->textdomain ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', $this->textdomain ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown--timer' => 'text-align: {{VALUE}};',
                ],
                'prefix_class' => 'smc-addel-countdown--timer-content-align%s-',
            ]
        );
        $this->end_controls_section(); 
        
        
        
        // Number style tab start
        $this->start_controls_section(
            'countdown__regularUI_numbers',
            [
                'label'     => esc_html__( 'Time Style', $this->textdomain ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    
                    'countdown__display_style'=>'flat',
                ]
            ]
        );
            
        $this->add_control(
            'countdown__regularUI_numbers_color',
            [
                'label' => esc_html__( 'Color', $this->textdomain ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-time' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'countdown__regularUI_numbers_typography',
                'label'    => __( 'Typography', $this->textdomain ),
                'selector' => '{{WRAPPER}}  .smc-addel-countdown--wrapper .smc-addel-countdown--timer-time',
                
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'countdown__regularUI_numbers_textShadow',
                'label' => esc_html__( 'Text Shadow', $this->textdomain ),
                'selector' => '{{WRAPPER}}  .smc-addel-countdown--wrapper .smc-addel-countdown--timer-time',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'countdown__regularUI_numbers_background',
                'label' => esc_html__( 'Background', $this->textdomain ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-time',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'countdown__regularUI_numbers_border',
                'label' => esc_html__( 'Border', $this->textdomain ),
                'selector' => '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-time',
            ]
        );

        $this->add_responsive_control(
            'countdown__regularUI_numbers_borderRadius',
            [
                'label' => esc_html__( 'Border Radius', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-time' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_responsive_control(
            'countdown__regularUI_numbers_padding',
            [
                'label' => esc_html__( 'Padding', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-time' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_responsive_control(
            'countdown__regularUI_numbers_margin',
            [
                'label' => esc_html__( 'Margin', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-time' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );

        $this->end_controls_section(); // Timer style tab end

        // Label Style tab section
        $this->start_controls_section(
            'countdown__regularUI_labels_section',
            [
                'label' => esc_html__( 'Labels', $this->textdomain ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'   => [
                    'countdown__hide_labels!' => 'yes',
                    'countdown__display_style'=>'flat',
                ],
            ]
        );
            $this->add_control(
                'countdown__regularUI_labels_color',
                [
                    'label' => esc_html__( 'Color', $this->textdomain ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-label' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'countdown__regularUI_labels_typography',
                    'selector' => '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-label',
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'countdown__regularUI_labels_textShadow',
                    'label' => esc_html__( 'Text Shadow', $this->textdomain ),
                    'selector' => '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-label',
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'countdown__regularUI_labels_background',
                    'label' => esc_html__( 'Background', $this->textdomain ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-label',
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'countdown__regularUI_labels_border',
                    'label' => esc_html__( 'Border', $this->textdomain ),
                    'selector' => '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-label',
                ]
            );

            $this->add_responsive_control(
                'countdown__regularUI_labels_borderRadius',
                [
                    'label' => esc_html__( 'Border Radius', $this->textdomain ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-label' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'countdown__regularUI_labels_padding',
                [
                    'label' => esc_html__( 'Padding', $this->textdomain ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'countdown__regularUI_labels_margin',
                [
                    'label' => esc_html__( 'Margin', $this->textdomain ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .smc-addel-countdown--wrapper .smc-addel-countdown--timer-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

        $this->end_controls_section(); // Label style tab end
    }
    
    /** REGISTER CONTROLS **/
    protected function _register_controls() {
        /* Countdown section */
        $this->start_controls_section(
            'countdown__section',
            [
                'label' => esc_html__( 'Countdown', $this->textdomain ),
            ]
        );
        // Countdown style    
        $this->add_control(
            'countdown__display_style',
            [
                'label'   => esc_html__( 'Countdown Style', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'circle',
                'options' => [
                    'circle' => esc_html__( 'Circle', $this->textdomain ),
                    'flipper'  => esc_html__( 'Flipper', $this->textdomain ),
                    'flat'  => esc_html__( 'Flat', $this->textdomain ),
                ],
                'label_block'=>true,
            ]
        );
        // Circle template
        $this->add_control(
            'countdown__template_circle',
            [
                'label'   => esc_html__( 'Circle Templates', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'one',
                'options' => [
                    'one'   => esc_html__( 'Simple', $this->textdomain ),
                    'two'   => esc_html__( 'Simple, no point', $this->textdomain ),
                ],
                'condition'   => [
                    'countdown__display_style' => 'circle',
                ],
            ]
        );
        // Flipper template
        $this->add_control(
            'countdown__template_flipper',
            [
                'label'   => esc_html__( 'Flipper Templates', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'dark',
                'options' => [
                    'light'   => esc_html__( 'Light', $this->textdomain ),
                    'dark'   => esc_html__( 'Dark', $this->textdomain ),
                    'dark-gradient'   => esc_html__( 'Dark Gradient', $this->textdomain ),
                ],
                'condition'   => [
                    'countdown__display_style' => 'flipper',
                ],
            ]
        );
        // Regular template
        $this->add_control(
            'countdown__template_flat',
            [
                'label'   => esc_html__( 'Flat Templates', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'one',
                'options' => [
                    'one'   => esc_html__( 'Dark', $this->textdomain ),
                    'two'   => esc_html__( 'Calendar', $this->textdomain ),
                    'three' => esc_html__( 'Flip Secconds', $this->textdomain ),
                    'four'  => esc_html__( 'Tick', $this->textdomain ),
                    'five'  => esc_html__( 'Suspended', $this->textdomain ),
                ],
                'condition'   => [
                    'countdown__display_style' => 'flat',
                ],
            ]
        );
        // Due Date
        $this->add_control(
            'countdown__due_date',
            [
                'label'          => esc_html__( 'Due Date', $this->textdomain ),
                'type'           => Controls_Manager::DATE_TIME,
                'picker_options' => array( 'dateFormat' => "Y-m-d  H:i",'allowInput' =>true, 'time_24hr' => true, 'enableTime' =>true),
                'default'        => date( 'Y-m-d H:i',  ( strtotime('3 days') * HOUR_IN_SECONDS ) ),
            ]
        );
        // Countdown style    
        $this->add_control(
            'countdown__due_date_action',
            [
                'label'   => esc_html__( 'Action at completion', $this->textdomain ),
                'type'    => Controls_Manager::SELECT,
                'nothing' => 'circle',
                'options'      => [
                'donothing'    => esc_html__( 'Do nothing', $this->textdomain ),
                'hide'         => esc_html__( 'Only hide timers', $this->textdomain ),
                'redirect'     => esc_html__( 'Redirect', $this->textdomain ),
                'message_keep' => esc_html__( 'Message + keep timers', $this->textdomain ),
                'message_hide' => esc_html__( 'Message + hide timers', $this->textdomain ),
                ],
                'label_block' => true,
                'default'     => 'donothing',
            ]
        );
        // Redirect
        $this->add_control(
            'countdown__due_date_redirect',
            [
                'label'          => esc_html__( 'Redirect at completion', $this->textdomain ),
                'type'           => Controls_Manager::URL,
                'condition'   => [
                    'countdown__due_date_action' => 'redirect',
                ],
            ]
        );
        // Message
        $this->add_control(
            'countdown__due_date_message',
            [
                'label'          => esc_html__( 'Message at completion', $this->textdomain ),
                'type'           => Controls_Manager::WYSIWYG,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name'     => 'countdown__due_date_action',
                            'operator' => '===',
                            'value'    => 'message_keep'
                        ],
                        [
                            'name'     => 'countdown__due_date_action',
                            'operator' => '===',
                            'value'    => 'message_hide'
                        ]
                    ]
                ]
            ]
        );
        $this->end_controls_section();

        
        /* Timers Settings Section */
        $this->start_controls_section(
            'countdown__timers_section',
            [
                'label' => esc_html__( 'Timers Settings', $this->textdomain ),
            ]
        );
        
        // Show / Hide timers
        $this->add_control(
            'countdown__show_timers_heading',
            [
                'label' => esc_html__( 'Show Timers', $this->textdomain ),
                'type'  => Controls_Manager::HEADING,
            ]
        );
        
        $this->add_control(
            'countdown__show_days',
            [
                'label'        => esc_html__( 'Days', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'countdown__show_hours',
            [
                'label'        => esc_html__( 'Hours', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'countdown__show_minutes',
            [
                'label'        => esc_html__( 'Minutes', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'countdown__show_seconds',
            [
                'label'        => esc_html__( 'Seconds', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );
        // Show / Hide labels    
        $this->add_control(
            'countdown__hide_labels_heading',
            [
                'label' => esc_html__( 'Labels Settings', $this->textdomain ),
                'type' => Controls_Manager::HEADING,
            ]
        );
        
        $this->add_control(
            'countdown__hide_labels',
            [
                'label'        => esc_html__( 'Hide Labels', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );
        // Custom labels
        $this->add_control(
            'countdown__custom_labels',
            [
                'label'        => esc_html__( 'Custom Labels', $this->textdomain ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'condition'    => [
                    'countdown__hide_labels!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'countdown__custom_label_days',
            [
                'label'       => esc_html__( 'Days', $this->textdomain ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Days', $this->textdomain ),
                'condition'   => [
                    'countdown__custom_labels!' => '',
                    'countdown__hide_labels!' => 'yes',
                    'countdown__show_days'    => 'yes',
                ],
            ]
        );

        $this->add_control(
            'countdown__custom_label_hours',
            [
                'label'       => esc_html__( 'Hours', $this->textdomain ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Hours', $this->textdomain ),
                'condition'   => [
                    'countdown__custom_labels!'     => '',
                    'countdown__hide_labels!' => 'yes',
                    'countdown__show_hours'   => 'yes',
                ],
            ]
        );

        $this->add_control(
            'countdown__custom_label_minutes',
            [
                'label'       => esc_html__( 'Minutes', $this->textdomain ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Minutes', $this->textdomain ),
                'condition'   => [
                    'countdown__custom_labels!'     => '',
                    'countdown__hide_labels!' => 'yes',
                    'countdown__show_minutes' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'countdown__custom_label_seconds',
            [
                'label'       => esc_html__( 'Seconds', $this->textdomain ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Seconds', $this->textdomain ),
                'condition'   => [
                    'countdown__custom_labels!'     => '',
                    'countdown__hide_labels!' => 'yes',
                    'countdown__show_seconds'  => 'yes',
                ],
            ]
        );
        $this->end_controls_section();

        // Circle general tab
        $this->controlsGeneralTabCircle();
        
        // Regular general tab
        $this->controlsGeneralTabRegular();
        
        // Flipper general tab
        $this->controlsGeneralTabFlipper();
        
        
        /*****************     STYLE TAB    ****************/
        // Style Counter Wraper
        $this->start_controls_section(
            'countdown__style_wrapper_section',
            [
                'label' => esc_html__( 'Wrapper', $this->textdomain ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'countdown__style_wrapper_background',
                'label' => esc_html__( 'Background', $this->textdomain ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .smc-addel-countdown',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'countdown__style_wrapper_boxshadow',
                'label' => esc_html__( 'Box Shadow', $this->textdomain ),
                'selector' => '{{WRAPPER}} .smc-addel-countdown',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'countdown__style_wrapper_border',
                'label' => esc_html__( 'Border', $this->textdomain ),
                'selector' => '{{WRAPPER}} .smc-addel-countdown',
            ]
        );

        $this->add_responsive_control(
            'countdown__style_wrapper_border_radius',
            [
                'label' => esc_html__( 'Border Radius', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_responsive_control(
            'countdown__style_wrapper_padding',
            [
                'label' => esc_html__( 'Padding', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_responsive_control(
            'countdown__style_wrapper_margin',
            [
                'label' => esc_html__( 'Margin', $this->textdomain ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_responsive_control(
            'countdown__style_wrapper_timers_align',
            [
                'label' => esc_html__( 'Timers Alignment', $this->textdomain ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__( 'Left', $this->textdomain ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', $this->textdomain ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'end' => [
                        'title' => esc_html__( 'Right', $this->textdomain ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .smc-addel-countdown' => 'justify-content: {{VALUE}};',
                ],
                'prefix_class' => 'smc-addel-countdown--timer-align%s-',
            ]
        );
        
        $this->end_controls_section();

        // Circular style item
        $this->controlsStyleTabCircle();
        // Regular style item
        $this->controlsStyleTabRegular();
        // Flipper 
    }

    protected function render( $instance = [] ) {
        $settings = $this->get_settings_for_display();
        $def_date = date( 'Y-m-d H:i',  ( strtotime('3 days')  * HOUR_IN_SECONDS ) );
        $date       = isset( $settings['countdown__due_date'] ) ? $settings['countdown__due_date'] : $def_date;
        $data_options = [
            'due_date' => [
                'date'     => $date,
                'action'   => $this->setDefaultSettingsOption($settings['countdown__due_date_action'], 'donothing' ),
                'redirect' => $this->setDefaultSettingsOption($settings['countdown__due_date_redirect'],'' ),
                'message'  => $this->setDefaultSettingsOption($settings['countdown__due_date_message'], '' ),
            ],
            'status' => [
                'hideLabels'  => ( 'yes' === $settings['countdown__hide_labels'] ),
                'showDays'    => ( 'yes' === $settings['countdown__show_days'] ),
                'showHours'   => ( 'yes' === $settings['countdown__show_hours'] ),
                'showMinutes' => ( 'yes' === $settings['countdown__show_minutes'] ),
                'showSeconds' => ( 'yes' === $settings['countdown__show_seconds'] ),
            ],
            'style' => $settings['countdown__display_style'] ,
            'customlabels' => [
                'days'    => $this->setDefaultSettingsOption( $settings['countdown__custom_label_days'] , esc_html__( 'Days',$this->textdomain )),
                'hours'   => $this->setDefaultSettingsOption( $settings['countdown__custom_label_hours'] , esc_html__( 'Hours',$this->textdomain )),
                'minutes' => $this->setDefaultSettingsOption($settings['countdown__custom_label_minutes'] , esc_html__( 'Minutes',$this->textdomain )),
                'seconds' => $this->setDefaultSettingsOption($settings['countdown__custom_label_seconds'] , esc_html__( 'Seconds',$this->textdomain )),
            ],
        ];
        
        // Circular countdown
        
        switch($settings['countdown__display_style']){
            case"circle":
            $free_wheel = $settings['countdown__circleUI_lines_freeWheel'];
            
            $past_line  = ($free_wheel === 'yes' ? $settings['countdown__circleUI_past_lineWidth_freeWheel']['size']: $settings['countdown__circleUI_past_lineWidth']['size']);
            
            $remaining_line   = ($free_wheel === 'yes' ? $settings['countdown__circleUI_remaining_lineWidth_freeWheel']['size']: $settings['countdown__circleUI_remaining_lineWidth']['size']);
            
            $inner_colors     = $this->calcGradient('countdown__circleUI_inner_lineGradient', 'countdown__circleUI_inner_color', 'countdown__circleUI_inner_color_end', '#ffcc00', '#00000000');
            
            $past_line_colors = $this->calcGradient('countdown__circleUI_past_lineGradient', 'countdown__circleUI_past_lineColor', 'countdown__circleUI_past_lineColor_end', 'rgba(81, 203, 238, 1)', '#f6008b');
            
            $remaining_line_colors = $this->calcGradient('countdown__circleUI_remaining_lineGradient', 'countdown__circleUI_remaining_lineColor', 'countdown__circleUI_remaining_lineColor_end', 'rgba(81, 203, 238, 1)', '#f6008b');
            
            $remaining_line_colors_days    = $this->calcGradient('countdown__circleUI_remaining_days_lineGradient', 'countdown__circleUI_remaining_days_lineColor', 'countdown__circleUI_remaining_days_lineColor_end', '#f6008b', "rgba(256, 256, 256, 0.7)");
            $remaining_line_colors_hours   = $this->calcGradient('countdown__circleUI_remaining_hours_lineGradient', 'countdown__circleUI_remaining_hours_lineColor', 'countdown__circleUI_remaining_hours_lineColor_end', '#f6008b', "rgba(256, 256, 256, 0.7)");
            $remaining_line_colors_minutes = $this->calcGradient('countdown__circleUI_remaining_minutes_lineGradient', 'countdown__circleUI_remaining_minutes_lineColor', 'countdown__circleUI_remaining_minutes_lineColor_end', '#f6008b', "rgba(256, 256, 256, 0.7)");
            $remaining_line_colors_seconds = $this->calcGradient('countdown__circleUI_remaining_seconds_lineGradient', 'countdown__circleUI_remaining_seconds_lineColor', 'countdown__circleUI_remaining_seconds_lineColor_end', '#f6008b', "rgba(256, 256, 256, 0.7)");
            
            
            $data_options = array_merge($data_options, [
                'animation' => [
                    'style'     => $settings['countdown__circleUI_animationStyle'],
                    'direction' => $settings['countdown__circleUI_animationDirection']
                ],

                'labelSize' => (isset($settings['countdown__circleUI_labels_fontSize']['size'])) ? $settings['countdown__circleUI_labels_fontSize']['size'] : 10,
                
                'numberSize' => isset($settings['countdown__circleUI_numbers_fontSize']['size']) ? $settings['countdown__circleUI_numbers_fontSize']['size'] : 28,
                
                'inner' => [
                    'fill'        => ( 'yes' === $this->setDefaultSettingsOption( $settings['countdown__circleUI_inner_show'], 'no')),
                    'useGradient' => ( 'yes' === $this->setDefaultSettingsOption( $settings['countdown__circleUI_inner_gradient'], 'no')),
                    'colors'      => $inner_colors,
                    'centerSize'  => $this->setDefaultSettingsOption( $settings['countdown__circleUI_inner_size']['size'], 5),
                    'shadowBlur'  => $this->setDefaultSettingsOption( $settings['countdown__circleUI_inner_shadowBlur']['size'], 0),
                    'shadowColor' => $this->setDefaultSettingsOption( $settings['countdown__circleUI_inner_shadowColor'], "#00000000"),
                ],
                
                'pastTime' => [
                    'showLines'         => ( 'yes' === $settings['countdown__circleUI_past_lines_show'] ),
                    'lineWidth'         => !empty($past_line) ? ( absint( $past_line ) / 100 ) : 0.6,
                    'useGradient'        => ('yes' === $settings['countdown__circleUI_past_lineGradient']),
                    'lineColors'        => $past_line_colors,
                    'gradientPoints'    => 
                    [
                        $this->setDefaultSettingsOption( $settings['countdown__circleUI_past_gradient_point_x1']['size'], 60),
                        $this->setDefaultSettingsOption( $settings['countdown__circleUI_past_gradient_point_y1']['size'], 180),
                        $this->setDefaultSettingsOption( $settings['countdown__circleUI_past_gradient_point_x2']['size'], 0),
                        $this->setDefaultSettingsOption( $settings['countdown__circleUI_past_gradient_point_y2']['size'], 0),  
                    ],  //"60", "180", "0", "0"],
                    'shadowBlur'        => $this->setDefaultSettingsOption( $settings['countdown__circleUI_past_shadows_blur']['size'], 0),
                    'shadowColor'       => $this->setDefaultSettingsOption( $settings['countdown__circleUI_past_shadows_color'], "rgba(0,0,0, 0)"),
                    'shadowCoordinates' => [$this->setDefaultSettingsOption( $settings['countdown__circleUI_past_shadows_x']['size'], 0), $this->setDefaultSettingsOption( $settings['countdown__circleUI_past_shadows_y']['size'], 0)],
                ],
                
                'leftTime' => [
                    'lineWidth'         => !empty( $remaining_line) ? ( absint( $remaining_line ) / 100 ) : 0.03,
                    'linesCap'           => $this->setDefaultSettingsOption( $settings['countdown__circleUI_remaining_linesCap'] , "round"),
                    'lineColors'        => $remaining_line_colors,
                    'useGradient'        => ('yes' === $settings['countdown__circleUI_remaining_lineGradient']),
                    'useEachLineColor' => ( 'yes' ===  $settings['countdown__circleUI_remaining_lines_eachOne']),
                    'daysColors'        => $remaining_line_colors_days,
                    'hoursColors'       => $remaining_line_colors_hours,
                    'minutesColors'     => $remaining_line_colors_minutes,
                    'secondsColors'     => $remaining_line_colors_seconds,
                    'daysGrads'         => ( 'yes' ===  $settings['countdown__circleUI_remaining_days_lineGradient']),
                    'hoursGrads'         => ( 'yes' ===  $settings['countdown__circleUI_remaining_hours_lineGradient']),
                    'minutesGrads'         => ( 'yes' ===  $settings['countdown__circleUI_remaining_minutes_lineGradient']),
                    'secondsGrads'         => ( 'yes' ===  $settings['countdown__circleUI_remaining_seconds_lineGradient']),
                    'shadowBlur'        => $this->setDefaultSettingsOption( $settings['countdown__circleUI_remaining_shadows_blur']['size'], 0), //0-13
                    'shadowColor'       => $this->setDefaultSettingsOption( $settings['countdown__circleUI_remaining_shadows_color'], "rgba(0,0,0, 0)"),
                    'shadowCoordinates' => [$this->setDefaultSettingsOption( $settings['countdown__circleUI_remaining_shadows_x']['size'], 0), $this->setDefaultSettingsOption( $settings['countdown__circleUI_remaining_shadows_y']['size'], 0)],//0-7
                    'UIeffect'=>$this->setDefaultSettingsOption( $settings['countdown__circleUI_remaining_uiEfect'], 0),
                ],
            ]);
            
            
            $this->add_render_attribute( 'area_attr', 'class', 'smc-addel-countdown smc-addel-countdown--style-circle smc-addel-countdown--style-circle-'.$settings['countdown__template_circle'] );
            
            break;
            
            // Regular countdown
            case"flat":
                $this->add_render_attribute( 'area_attr', 'class', 'smc-addel-countdown smc-addel-countdown--wrapper smc-addel-countdown--style-flat-'.$settings['countdown__template_flat'] );
            break;
            
            case"flipper":
                $flipper_date_tpl = [
                   // ['d','H','i','s'],
                    ['dd','HH','ii','ss'],
                    ['ddd','HH','ii','ss'],
                ];
                
                $flipper_date_tpl_selected = $this->setDefaultSettingsOption( $settings['countdown__flipper_time_template'], 1);
                
                $data_options = array_merge($data_options, [
                    'timerType' => $settings['countdown__flipper_timers_type'],
                    'cssTemplate' => $settings['countdown__template_flipper'],
                    'timeTemplate' => [
                        'days' => $flipper_date_tpl[$flipper_date_tpl_selected][0], //dd
                        'hours' => $flipper_date_tpl[$flipper_date_tpl_selected][1],
                        'minutes' => $flipper_date_tpl[$flipper_date_tpl_selected][2],
                        'seconds' => $flipper_date_tpl[$flipper_date_tpl_selected][3],
                    ],
                ]);
    
                
                
                $this->add_render_attribute( 'area_attr', 'class', 'smc-addel-countdown smc-addel-countdown--wrapper smc-addel-countdown--style-flipper-'.$settings['countdown__template_flipper'] );
            break;
        }
        
        $this->add_render_attribute( 'area_attr', 'data-date', $date.':00' );
        $this->add_render_attribute( 'area_attr', 'data-countdown', wp_json_encode( $data_options ) );

        echo sprintf('<div %1$s></div>', $this->get_render_attribute_string( 'area_attr' ) );
    } 
}