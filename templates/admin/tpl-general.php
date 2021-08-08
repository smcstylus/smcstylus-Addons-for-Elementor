<?php
/**
 * Template file: Admin General Page
 * Description: Plugin admin settings widgets tab/page
 * 
 * @since 1.0.0
 * @package: SMCstylus Addons For Elementor
 * 
 */
use SMCstylus_Elementor\Core\Settings\SMC_Addons_Globals; 
use SMCstylus_Elementor\Core\Settings\SMC_Plugin_Globals; 

$plugin_details = SMC_Plugin_Globals::getMain();
$admin_assets = SMC_Plugin_Globals::getUrl('assets_admin');
?>
<div id="general" class="smc-addel-settings-tab active">
    <div class="row smc-addel-admin-general-wrapper">
        <div class="smc-addel-admin-general-inner">
            <div class="smc-addel-admin-block-wrapper">
            
                    <div class="smc-addel-admin-block smc-addel-admin-block-banner" >
                        <a href="<?php esc_attr_e($plugin_details['doc']); ?>" target="_blank">
                            <img class="smcstylus-addel-banner" src="<?php echo $admin_assets['img'] . 'smcstylus-addons-banner.jpg'; ?>" alt="<?php _e('Banner', $plugin_details['textdomain']); ?>">
                        </a>
                    </div><!--preview image end-->
                
                    <div class="smc-addel-admin-block smc-addel-admin-block-docs">
                        <header class="smc-addel-admin-block-header">
                            <div class="smc-addel-admin-block-header-icon">
                                <img src="<?php echo $admin_assets['img'] . 'icon-documentation.svg'; ?>" alt="<?php _e('Documentation', $plugin_details['textdomain']); ?>">
                            </div>
                            <h4 class="smc-addel-admin-title"><?php _e('Documentation', $plugin_details['textdomain']); ?></h4>
                        </header>
                        <div class="smc-addel-admin-block-content">
                            <p><?php echo sprintf(esc_html__('Get familiar with %1$s version %2$s and build awesome websites for you or your clients with ease.', $plugin_details['textdomain']) ,$plugin_details['name'], self::VERSION); ?></p>
                            <a href="<?php esc_attr_e($plugin_details['doc']); ?>" class="site-button" target="_blank"><?php _e('Documentation', $plugin_details['textdomain']); ?></a>
                        </div>
                    </div>
                    <div class="smc-addel-admin-block smc-addel-admin-block-contribution">
                        <header class="smc-addel-admin-block-header">
                            <div class="smc-addel-admin-block-header-icon">
                                <img src="<?php echo $admin_assets['img'] . 'icon-github.svg'; ?>" alt="">
                            </div>
                            <h4 class="smc-addel-admin-title"><?php _e('See my other projects on GitHub ', $plugin_details['textdomain']); ?></h4>
                        </header>
                        <div class="smc-addel-admin-block-content">
                            <p><?php _e('If you like my plugin, go to my Github page and see what else you can find usefull for your projects. Have fun !!!', $plugin_details['textdomain']); ?> </p>
                            <a href="https://smcstylus.github.io" class="site-button" target="_blank"><?php _e('GitHub', $plugin_details['textdomain']); ?></a>
                        </div>
                    </div>
                    
                    
            </div><!--admin block-wrapper end-->
        </div>
           
    </div><!--Row end-->
</div>
