<?php
namespace SMCstylus_Elementor\Addons\Extensions\Custom_JS;

defined('ABSPATH') || exit; // exit if accessed directly

use \Elementor\Controls_Manager;

use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals;

/**
 * Extension: Custom_JS
 * Description: Custom JS
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
class Custom_JS {
    private $extension_version = '1.0.0';
    private $extension_key = 'custom-js';  
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
        
        add_action('elementor/documents/register_controls', [$this, 'register_controls'], 20);
        add_action( 'elementor/element/after_section_end', [ $this, 'register_widgets_controls' ], 20,2 );
    }
    
    
    public function editor_add_scripts() {
        // Styles
		//wp_register_style( $this->slug_prefix . $this->extension_key, $this->extension_url['css'].'extension-'. $this->extension_key . '.css', [], $this->extension_version );
		//wp_enqueue_style( $this->slug_prefix . $this->extension_key );
        
        // JS
        wp_enqueue_script( $this->slug_prefix . $this->extension_key, $this->extension_url['js'].'extension-'. $this->extension_key . '.js', [], $this->extension_version, true );
        wp_localize_script( $this->slug_prefix . $this->extension_key, 'localize', [
            'smc_extension'=>
                ['custom_js_slug' => $this->opt_prefix],
         ] );
    }
    
    public function register_widgets_controls( $element, $section_id ){
        if ( $this->slug_prefix.'custom_css_section' === $section_id
        ||'section_custom_attributes_pro' === $section_id 
        || 'section_custom_attributes' === $section_id ) {
            $this->register_controls($element);
        }
    }
    
    public function register_controls($element){
        $element->start_controls_section(
            $this->opt_prefix . 'custom_js_section',
            [
                'label' => sprintf('<i class="eaicon-logo"></i> %s', __('Custom JS', $this->textdomain)),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );

        $element->add_control(
            $this->opt_prefix . 'custom_js_title',
            [
                'raw' => __('Add your own custom JS here', $this->textdomain),
                'type' => Controls_Manager::RAW_HTML,
            ]
        );

        $element->add_control(
            $this->opt_prefix . 'custom_js_code',
            [
                'type' => Controls_Manager::CODE,
                'label' => __( 'Custom JS', $this->textdomain ),
                'language' => 'javascript',
                'show_label' => false,
            ]
        );
        $element->add_control(
			$this->opt_prefix . 'custom_js_run',
			[
				'label' => __( 'Run JS', $this->textdomain ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'text' => __( 'Run', $this->textdomain ),
				'event' => 'SMC_runJS',
			]
		);
        $element->add_control(
            $this->opt_prefix . 'custom_js_description',
            [
                'raw' => __('Use jQuery selector or Vanilla JS selector. Examples:<br>$(".selector") // for jQuery <br>document.queryselector(".selector") // for vanilla JS<br>Note: in editor mode will not entirely work.', $this->textdomain),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-descriptor',
            ]
        );
        
        $element->add_control(
            $this->opt_prefix . 'custom_js_docs',
            [
                'raw' => sprintf(__( 'For more information, <a href="%1$s" target="_blank">click here</a>', $this->textdomain ), $this->extension_opt['doc_link'] ),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-descriptor',
            ]
        );
        
        $element->end_controls_section();
    }
}
