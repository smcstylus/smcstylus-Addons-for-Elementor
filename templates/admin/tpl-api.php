<?php
/**
 * Template file: Admin API page
 * Description: Plugin admin settings API tab/page
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
$api_settings = get_option(SMC_Plugin_Globals::getDB('api_settings'));
?>
<div id="api" class="smc-addel-settings-tab smc-addel-elements-list">
	<div class="row">
		<div class="col-full">
		
		<div class="smc-addel-checkbox-container">
			<!-- start cb -->
				<div class="smc-addel-checkbox<?php echo $class; ?>">
					<div class="smc-addel-elements-info">
						<p class="smc-addel-el-title">
						<?php 
						_e( 'Google Client ID', $textdomain) ;	
						echo '<br><input type="text" name="smc_addel_api_google_id" id="google-client-id" class="google-client-id"  placeholder="Set API Key" value="' . $api_settings['google_id'] . '">';
						?>
						</p>
					</div>
				</div>
			<!-- end cb -->
			<!-- start cb -->
				<div class="smc-addel-checkbox<?php echo $class; ?>">
					<div class="smc-addel-elements-info">
						<p class="smc-addel-el-title">
						<?php 
							_e( 'Google Map API Key', $textdomain) ;	
							echo '<br><input type="text" name="smc_addel_api_google_map_key" id="google-map-key" class="google-map-key" placeholder="Set API Key"  value="' . $api_settings['google_map_key'] . '">';
						?>
						</p>
					</div>
				</div>
			<!-- end cb -->
			<!-- start cb -->
				<div class="smc-addel-checkbox<?php echo $class; ?>">
					<div class="smc-addel-elements-info">
						<p class="smc-addel-el-title">
						<?php 
						_e( 'Facebook APP ID', $textdomain) ;
						echo '<br><input type="text" name="smc_addel_api_facebook_app_id" id="facebook-app-id" class="facebook-app-id" placeholder="Set API Key"  value="' . $api_settings['facebook_app_id'] . '"><br><br>';
						
						_e( 'Facebook APP Secret', $textdomain) ;
						echo '<br><input type="text" name="smc_addel_api_facebook_app_secret" id="facebook-app-secret" class="facebook-app-secret" placeholder="Set API Key"  value="' . $api_settings['facebook_app_secret'] . '">';
						?>
						</p>
					</div>
				</div>
			<!-- end cb -->
			<!-- start cb -->
				<div class="smc-addel-checkbox<?php echo $class; ?>">
					<div class="smc-addel-elements-info">
						<p class="smc-addel-el-title">
						<?php 
					_e( 'Recaptcha Key', $textdomain) ;
					echo '<br><input type="text" name="smc_addel_api_recaptcha_key" id="recaptcha-api-key" class="recaptcha-api-key" placeholder="Set API Key"  value="' . $api_settings['recaptcha_key'] . '"><br><br>';
					
					_e( 'Recaptcha Secret', $textdomain) ;
					echo '<br><input type="text" name="smc_addel_api_recaptcha_secret" id="recaptcha-api-secret" class="recaptcha-api-secret" placeholder="Set API Key"  value="' . $api_settings['recaptcha_secret'] . '">';
						?>
						</p>
					</div>
				</div>
			<!-- end cb -->
			<!-- start cb -->
				<div class="smc-addel-checkbox<?php echo $class; ?>">
					<div class="smc-addel-elements-info">
						<p class="smc-addel-el-title">
						<?php 
						_e( 'Mailchimp API Key', $textdomain) ;
						echo '<br><input type="text" name="smc_addel_api_mailchimp_key" id="mailchimp-key" class="mailchimp-api" placeholder="Set API Key" value="' . $api_settings['mailchimp_key'] . '">';
						?>
						</p>
					</div>
				</div>
			</div>
    </div>
		<div class="smc-addel-save-btn-wrap">
			<button type="submit" class="button smc-addel-btn js-smc-addel-settings-save"><?php esc_html_e('Save settings', $textdomain); ?></button>
		</div>
	</div>
</div>