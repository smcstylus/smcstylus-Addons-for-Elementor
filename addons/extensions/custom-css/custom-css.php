<?php
namespace SMCstylus_Elementor\Addons\Extensions\Custom_CSS;

defined('ABSPATH') || exit; // exit if accessed directly

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Element_Base;
use Elementor\Core\Files\CSS;

use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;

/**
 * Extension: Custom_CSS
 * Description: Custom CSS for Elementor free version
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
class Custom_CSS {
    private $extension_version = '1.0.0';
    private $extension_key = 'custom-css';  
    private $extension_url = '';
    private $extension_opt = '';
    private $slug_prefix = '';
    private $opt_prefix = '';
    private $textdomain = '';
  private static $_instance = null;
    public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    public function __construct() {
        $this->extension_url = SMC_Plugin_Globals::getUrl('assets_admin') ;
        $this->extension_opt = SMC_Addons_Globals::getGlobal('extensions_list', $this->extension_key);
        $this->slug_prefix = SMC_Plugin_Globals::getCategory('options')['slug'];
        $this->opt_prefix = SMC_Plugin_Globals::getMain('opt_prefix');
        $this->textdomain = SMC_Plugin_Globals::getMain('textdomain');
        
        $this->on_elementor_init();
    }

    public function on_elementor_init(){
        
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'editor_add_scripts' ] );
        
        add_action( 'elementor/element/after_section_end', [ $this, 'register_controls' ], 10, 2 );
        add_action( 'elementor/element/parse_css', [ $this, 'save_custom_css' ], 10, 2 );
        add_action( 'elementor/css-file/post/parse', [ $this, 'post_settings' ] );

        
    }

    public function editor_add_scripts() {
        wp_enqueue_script( $this->slug_prefix . $this->extension_key, $this->extension_url['js'].'extension-'. $this->extension_key . '.js', [], $this->extension_version, true );
        wp_localize_script( $this->slug_prefix . $this->extension_key, 'localize', [
            'smc_extension'=>
                ['custom_css_slug' => $this->opt_prefix],
         ] );
    }

    /**
     * @param $element Controls_Stack
     * @param $section_id string
     */
    public function register_controls( Controls_Stack $element, $section_id ) {
        // Remove Custom CSS Advertise from free version
        if ( 'section_custom_css_pro' !== $section_id ) {
            return;
        }

        $old_section = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $element->get_unique_name(), 'section_custom_css_pro' );
        $controls_to_remove = [ 'section_custom_css_pro', 'custom_css_pro' ];

        \Elementor\Plugin::instance()->controls_manager->remove_control_from_stack( $element->get_unique_name(), $controls_to_remove );
        
        // Add the SMCstylus Custom CSS section for free version
        $element->start_controls_section(
            $this->opt_prefix . 'custom_css_section',
            [
                'label' => __( 'Custom CSS', $this->textdomain ),
                'tab' => $old_section['tab'],
            ]
        );

        $element->add_control(
            $this->opt_prefix . 'custom_css_title',
            [
                'raw' => __( 'Add your own custom CSS here', $this->textdomain ),
                'type' => Controls_Manager::RAW_HTML,
            ]
        );

        $element->add_control(
            $this->opt_prefix . 'custom_css_code',
            [
                'type' => Controls_Manager::CODE,
                'label' => __( 'Custom CSS', $this->textdomain ),
                'language' => 'css',
                'render_type' => 'ui',
                'show_label' => false,
                'separator' => 'none',
            ]
        );

        $element->add_control(
            $this->opt_prefix . 'custom_css_description',
            [
                'raw' => wp_kses_post(__( 'Use "widget" to target wrapper element. Examples:<br>widget {height: auto;} // For main wrapper<br>element .widget-child {margin: 10px;} // For wrapper child<br>.my-class {text-align: center;} // Use any custom selector', $this->textdomain )),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $element->end_controls_section();
    }


    /**
     * @param $post_css CSS\Post
     * @param $element Element_Base
     */
    public function save_custom_css( $post_css, $element ) {
        $element_settings = $element->get_settings();

        $css = trim( $element_settings[$this->opt_prefix . 'custom_css_code'] );
        if ( empty( $css ) ) {
            return;
        }

        $css = str_replace( 'widget', $post_css->get_element_unique_selector( $element ), $css );

        // Add a css comment
        $css = sprintf( 
            '/* Start SMCstylus Addons for Elementor - custom CSS for %s, class: %s */', 
            $element->get_name(), 
            $element->get_unique_selector() ) 
            . $css 
            . '/* End  SMCstylus Addons for Elementor - custom CSS */';

        $post_css->get_stylesheet()->add_raw_css( $css );
    }
    
    /**
     * @param $post_css CSS\Post
     */
    public function post_settings( $post_css ) {
        $document = \Elementor\Plugin::instance()->documents->get( $post_css->get_post_id() );
        $custom_css = $document->get_settings( $this->opt_prefix . 'custom_css_code' );

        $custom_css = trim( $custom_css );

        if ( empty( $custom_css ) ) {
            return;
        }

        $custom_css = str_replace( 'widget', 'body.elementor-page-' . $post_css->get_post_id(), $custom_css );

        // Add a css comment
        $custom_css = "
            /* Start SMCstylus Addons for Elementor - custom CSS */ 
            {$custom_css} 
            /* End SMCstylus Addons for Elementor - custom CSS */
            ";

        $post_css->get_stylesheet()->add_raw_css( $custom_css );
    }
}
