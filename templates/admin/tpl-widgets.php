<?php
/**
 * Template file: Admin widgets page
 * Class version: 1.0.0
 * Description: Plugin admin settings widgets tab/page
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */


use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals; 
use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals; 
$widgets_group = SMC_Addons_Globals::getGlobal('widgets_groups');
$widgets_group = apply_filters( 'smcstylus_addons_elementor/widgets', $widgets_group );
$textdomain = SMC_Plugin_Globals::getMain('textdomain');

?>
<div id="widgets" class="smc-addel-settings-tab smc-addel-addons-list">
	<div class="row">
		<div class="col-full">
            
			<?php foreach($widgets_group as $group) : 
				// Create the info only if there are widgets in that group
				if(!empty($group['widgets'])):
				echo (!empty($group['title'])) ? '<h4>'.__( $group['title'], $textdomain).'</h4>' : ''; 
			?>

			<div class="smc-addel-checkbox-container ">
			<?php 
			$widgets_list = SMC_Addons_Globals::getGlobal('widgets_list');		
			foreach($group['widgets'] as $widget_key) {
				$widget = $widgets_list[$widget_key];
				 
				$status = checked( 1, $this->check_addon_status($widget['key'],$this->widgets_registered), false );
				$class = isset($widget['class']) ? ' '.$widget['class'] : '';
			?>
				<div class="smc-addel-checkbox smc-addel-checkbox-addons <?php echo $class; ?>">
					<div class="smc-addel-elements-info">
						<p class="smc-addel-el-title"><?php _e( $widget['title'], $textdomain) ?></p>
						<?php if (!empty( $widget['demo_link'])) { ?>
							<a class="smc-addel-element-info-link" href="<?php echo esc_attr( esc_url( $widget['demo_link'] ) );?>" target="_blank">
								<span class="smc-addel-element-demo"><?php $this->get_admin_svg_icons('demo');?></span>
								<span class="smc-addel-info-tooltip"><?php esc_html_e('Demo', $textdomain); ?></span>
							</a>
						<?php } ?>
						<?php if (!empty( $widget['doc_link'])) { ?>
							<a class="smc-addel-element-info-link" href="<?php echo esc_attr( esc_url( $widget['doc_link'] ) );?>" target="_blank">
								<span class="smc-addel-element-help"><?php $this->get_admin_svg_icons('help');?></span>
								<span class="smc-addel-info-tooltip"><?php esc_html_e('Documentation', $textdomain); ?></span>
							</a>
						<?php } ?>
					</div>
					<input type="checkbox" id="smc_addel_widget_<?php echo esc_attr($widget['key']); ?>" name="smc_addel_widget_<?php echo esc_attr($widget['key']); ?>" <?php echo esc_html( $status ); ?>>
					<label for="smc_addel_widget_<?php echo esc_attr($widget['key']); ?>" class="<?php echo esc_attr( $label_class ); ?>"></label>
				</div>
			<?php } ?>
			</div>
			<?php endif;endforeach; ?>

			<div class="smc-addel-save-btn-wrap">
				<button type="submit" class="button smc-addel-btn js-smc-addel-settings-save"><?php esc_html_e('Save settings', $textdomain); ?></button>
			</div>
    </div>
	</div>
</div>
