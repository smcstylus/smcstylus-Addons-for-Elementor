<?php
defined( 'ABSPATH' ) ||	exit; // No access of directly access. 
use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
/**
 *
 * Class: SMC_Control_Points
 * Description: A base control for creating dimension control. Displays input fields for x0,y0, x1, y1 for gradient points.
 *
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */

class SMC_Control_Points extends \Elementor\Control_Base_Units  {
	/**
	 * Get dimensions control type.
	 * Retrieve the control type, in this case `SMC_Control_Points`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 * 
	 */
	public function get_type() {
		return 'SMC_Control_Points';
	}

	/**
	 * Get dimensions control default values.
	 * Retrieve the default value of the dimensions control. Used to return the
	 * default values while initializing the dimensions control.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Control default value.
	 * 
	 */
	public function get_default_value() {
			return [
				'x0' => 0,
				'y0' => 0,
				'x1' => 0,
				'y1' => 0,
				'isLinked' => false,
			];
	}

	/**
	 * Get dimensions control default settings.
	 * Retrieve the default settings of the dimensions control. Used to return the default settings while initializing the dimensions control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array Control default settings.
	 * 
	 */
	protected function get_default_settings() {
			return [
				'label_block' => true,
        'size_units' => ['px'],
				'placeholder' => '',
        'range' => [
          'px' => [
              'min' => 0,
              'max' => 180,
              'step' => 1,
          ],
        ]
				];
	}

	/**
	 * Render dimensions control output in the editor.
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 */
	public function content_template() {
		$textdomain = SMC_Plugin_Globals::getMain('textdomain');
		
		$dimensions = [
			'x0' => __( 'X Start', $textdomain ),
			'y0' => __( 'Y Start', $textdomain ),
			'x1' => __( 'X End', $textdomain ),
			'y1' => __( 'Y End', $textdomain ),
		];
	?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			
			<div class="elementor-control-input-wrapper">
				<ul class="elementor-control-dimensions">
					<?php
					foreach ( $dimensions as $dimension_key => $dimension_title ) :
						$control_uid = $this->get_control_uid( $dimension_key );
						?>
						<li class="elementor-control-dimension">
							<input id="<?php echo $control_uid; ?>" type="number" <?php echo $dimension_key === 'y1' ? 'style="
    border-radius: 0 3px 3px 0;"' : ''; ?> min="{{ data.range['px'].min}}" max="{{ data.range['px'].max}}" step="{{ data.range['px'].step}}"  data-setting="<?php echo esc_attr( $dimension_key ); ?>"
								   placeholder="<#
                  if ( _.isObject( data.placeholder ) ) {
                    if ( ! _.isUndefined( data.placeholder.<?php echo $dimension_key; ?> ) ) {
                       print( data.placeholder.<?php echo $dimension_key; ?> );
                       }
                  } else {
                    print( data.placeholder );
                  } 
                #>">
							<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-dimension-label"><?php echo $dimension_title; ?></label>
						</li>
					<?php endforeach; ?>
				
				</ul>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
<?php
}
}