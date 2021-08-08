<?php
/**
 * Template file: Admin Extensions Page
 * Description: Plugin admin settings extensions tab/page
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */

use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals; 
use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals;
$extensions_group = SMC_Addons_Globals::getGlobal('extensions_groups');

// Extensions grup filter
$extensions_group = apply_filters( 'smcstylus_addons_elementor/extensions', $extensions_group );
$textdomain = SMC_Plugin_Globals::getMain('textdomain');
$tools_settings = get_option(SMC_Plugin_Globals::getDB('tools_settings'));
?>

<div id="extensions" class="smc-addel-settings-tab smc-addel-addons-list">
    <div class="row">
        <div class="col-full"> 
            <?php 
                foreach($extensions_group as $group) : 
				// Create the info only if there are widgets in that group
				if(!empty($group['extensions'])):
                echo !empty($group['title']) ? '<h4>'.__( $group['title'], $textdomain).'</h4>' : '';
            ?> 
                <div class="smc-addel-checkbox-container ">
                    <?php
                        $extensions_list = SMC_Addons_Globals::getGlobal('extensions_list');		
                        foreach($group['extensions'] as $extension_key) {
                            $extension = $extensions_list[$extension_key];
                            $status = checked( 1, $this->check_addon_status($extension['key'],$this->extensions_registered), false );
                            $class = isset($extension['class']) ? ' '.$extension['class'] : '';
                    ?>
                    <div class="smc-addel-checkbox smc-addel-checkbox-addons">
                        <div class="smc-addel-elements-info">
                            <p class="smc-addel-el-title"><?php _e( $extension['title'], $textdomain) ?>
                            </p>
                            
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
                        <input type="checkbox" id="smc_addel_extension_<?php echo esc_attr($extension['key']); ?>" name="smc_addel_extension_<?php echo esc_attr($extension['key']); ?>" <?php echo $status; ?>>
                        <label for="smc_addel_extension_<?php echo esc_attr($extension['key']); ?>" class="<?php echo $label_class; ?>"></label>
                    </div>
                    <?php } ?>
			</div>
			<?php 
            endif;
            endforeach; 
            ?>
            <div class="smc-addel-save-btn-wrap">
                <button type="submit" class="button smc-addel-btn js-smc-addel-settings-save"><?php _e('Save settings', $textdomain); ?></button>
            </div>
        </div>
    </div>
</div>