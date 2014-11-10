<?php
/*
Plugin Name: Lunar - WordPress photography
Plugin URI: http://sakuraplugins.com/
Description: Lunar, a WordPress photography plugin!
Author: SakuraPlugins
Version: 1.3.0
Author URI: http://sakuraplugins.com/
*/
define('LN_TEMPPATH', plugins_url('', __FILE__));
define('LN_JS_ADMIN', LN_TEMPPATH.'/com/sakuraplugins/js');
define('LN_JS', LN_TEMPPATH.'/js');
define('LN_CLASS_PATH', plugin_dir_path(__FILE__));
define('LN_PLUGIN_TEXTDOMAIN', 'ln_portfolio');
define('LN_PORTFOLIO_SLUG', 'ln_grid');
define('LN_POST_CUSTOM_META', 'ln_portfolio_post_options');
define('LN_PORTFOLIO_OPTION_GROUP', 'ln_portfolio_option_group');
define('LN_PORTFOLIO_REWRITE', 'sk_luna');
define('LN_FILE', __FILE__);
define('LN_PORTFOLIO_CATEGORIES', 'ln_portfolio_categories');


require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/plugin_core.php');
$gr_plugin_core = new LNClass_PluginBase();
$gr_plugin_core->start(array('PLUGIN_FILE'=>__FILE__));

?>
