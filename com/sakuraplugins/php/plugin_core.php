<?php

require_once(LN_CLASS_PATH.'com/sakuraplugins/php/customposts/SkCPT.php');
require_once(LN_CLASS_PATH.'com/sakuraplugins/php/customposts/utils/CPTHelper.php');
require_once(LN_CLASS_PATH.'com/sakuraplugins/php/script_manager/ScriptManager.php');


require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/libs/resize_helper.php');
require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/plugin-options.php');
require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/admin_pages/admin-option-page.php');
require_once(LN_CLASS_PATH.'com/sakuraplugins/php/libs/sk_utils.php');
require_once(LN_CLASS_PATH.'com/sakuraplugins/php/libs/mobile_detect.php');


/**
 * base class
 */
class LNClass_PluginBase {

	/************* BASE PLUGIN EVENTS ***********/
	//init handler
	public function initializeHandler(){
		$this->addCPT();
	}
		

	//admin init handler
	public function adminInitHandler(){
		$this->rxCPT->addMetaBox(__('Photos', LN_PLUGIN_TEXTDOMAIN), 'meta_box_gallery_023648', 'meta_box_gallery');
		

	}	

	/**
	 * SAVE POST EXTRA DATA
	 */
	 public function savePostHandler(){
		global $post;						
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
			return $post_id;
		}
		if(!current_user_can('edit_posts') || !current_user_can('publish_posts')){
			return;
		}
			//save portfolio data
		if(isset($this->rxCPT) && isset($_POST['post_type'])){
			if($this->rxCPT->getPostSlug() == $_POST['post_type']){									
				if(current_user_can( 'edit_posts', $post->ID ) && isset($_POST[$this->rxCPT->getPostCustomMeta()])){							
					update_post_meta($post->ID, $this->rxCPT->getPostCustomMeta(), $_POST[$this->rxCPT->getPostCustomMeta()]);
				}		 
			}						
		}																
	 }	
	
	//admin enqueue scripts handler
	public function adminEnqueueScriptsHandler(){
		if(!isset($this->rxCPT)){
			return;
		}
		$current_screen = get_current_screen();
		if($current_screen->post_type==$this->rxCPT->getPostSlug()){
			//default styles
			$this->enqueAdminCommonStyles();			
			//default JS
			wp_enqueue_script('jquery');
			
			LNClass_ScriptManager::enqueJqueryUI();
			//LNClass_ScriptManager::enqueTweenmax();
			LNClass_ScriptManager::enqueueThickbox();
			LNClass_ScriptManager::enqueColorPicker();
			wp_enqueue_script('iris');
			wp_enqueue_media();			
			wp_register_script('ln_admin_js', LN_TEMPPATH.'/com/sakuraplugins/js/admin.js', array('jquery'));
			wp_enqueue_script('ln_admin_js');				
		}
		$screenID = $current_screen->id;
		if($screenID==LN_PORTFOLIO_SLUG.'_page_ct_portfolio_sett'){
			$this->enqueAdminCommonStyles();
			wp_register_script('ln_admin_js_options', LN_TEMPPATH.'/com/sakuraplugins/js/admin_options.js', array('jquery'));
			wp_enqueue_script('ln_admin_js_options');			
		}					
	}	
	private function enqueAdminCommonStyles(){
			wp_register_style('skgrid_admin_style', LN_TEMPPATH.'/com/sakuraplugins/css/admin.css');
			wp_enqueue_style('skgrid_admin_style');	
			wp_register_style('skgrid_admin_style_opts', LN_TEMPPATH.'/com/sakuraplugins/css/admin_options.css');
			wp_enqueue_style('skgrid_admin_style_opts');
	}	

	//admin menu handler
	public function adminMenuHandler(){
		$sk_options_page = new LNClass_OptionPage(LN_PORTFOLIO_OPTION_GROUP);
		add_submenu_page('edit.php?post_type='.LN_PORTFOLIO_SLUG, 'Options', 'Options', 'manage_options', 'ct_portfolio_sett', array($sk_options_page, 'settings_page'));		
	}
					
	
	//remove support
	public function removeSupport($postTypeSlug, $val){
		//remove_post_type_support($postTypeSlug, $val);
	}
	
	//add thumb size/support
	private function addThumbSize($postTypes=NULL){
		if($postTypes==NULL)
			return;
		if(function_exists('add_theme_support')){
			add_theme_support('post-thumbnails');			
		}
	}

	//theme setup event
	public function after_theme_setup(){
		$this->addThumbSize(array(LN_PORTFOLIO_SLUG));
	}	
	
	//handle lunar ajax requests - admin
	public function lunarAjaxHandler(){
		require_once(LN_CLASS_PATH.'com/sakuraplugins/php/ajax/admin_gate.php');
		$gate = new AdminGate();
		if(!current_user_can('edit_posts') || !current_user_can('publish_posts')){
			$gate->throwError("Current user cannot edit posts!");			
			return;
		}
		$payload = (isset($_POST['payload'])?$_POST['payload']:false);
		$gate->setPayload($payload);
		$price = (isset($_POST['price'])?$_POST['price']:false);
		$gate->setPrice($price);
				
		$route = (isset($_POST['route'])?$_POST['route']:false);
		if(!$route){
			$gate->throwError("No route found!");
			return;
		}
		$gate->route($route);
		die();
	}

	//get image exif data
	public function lunarAjaxExif(){
		if(!current_user_can('edit_posts') || !current_user_can('publish_posts')){
			echo json_encode(array('status'=>'FAIL', 'data'=>'', 'message'=>'You are not suppose to edit this!'));
			die();
		}
		$id = (isset($_POST['id'])?$_POST['id']:false);
		if(!$id){
			echo json_encode(array('status'=>'FAIL', 'data'=>'', 'message'=>'ID not set!'));
			die();
		}
		$imageFile = get_attached_file($id);
		$exif = wp_read_image_metadata($imageFile);		
		$exifDTA = '';
		foreach($exif as $key => $value){
			$exifDTA .= $key.', ';
		}	
		echo json_encode(array('status'=>'OK', 'data'=> $exifDTA, 'message'=>''));	
		die();
	}


	//init listeners
	public function start($opts){
		add_action('init', array($this, 'initializeHandler'));
		add_action('admin_init', array($this, 'adminInitHandler'));
		add_action('save_post', array($this, 'savePostHandler'));		
		add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScriptsHandler'));				
		add_action('admin_menu', array($this, 'adminMenuHandler'));		
		add_action("wp_enqueue_scripts", array($this, 'WPEnqueueScriptsHandler'), 999999);	
		add_action("wp_ajax_get_metro_thumb", array($this, 'get_metro_thumb'));	
		add_action('after_setup_theme', array($this, 'after_theme_setup'));	
		register_deactivation_hook($opts['PLUGIN_FILE'], array($this, 'plugin_deactivate'));
		add_action("wp_before_admin_bar_render", array($this, 'adminBarCustom'));
		add_action('wp_ajax_lunar_action', array($this, 'lunarAjaxHandler'));
		add_action('wp_ajax_lunar_action_exif', array($this, 'lunarAjaxExif'));
	}



	//admin bar custom
	public function adminBarCustom(){
		if(function_exists('get_current_screen')){
			$current_screen = get_current_screen();		
			if($current_screen->post_type==LN_PORTFOLIO_SLUG){			
				require_once(LN_CLASS_PATH.'com/sakuraplugins/php/admin_pages/banner.php');
			}
		}
	}	

	//plugin deactivate
	public function plugin_deactivate()
	{
		update_option('ct_is_rewrite', false);
		flush_rewrite_rules();		
	}	


	//ajax callback - admin thumbs
	public function get_metro_thumb(){
		$metroThumbID = (isset($_POST['metroThumbID'])?$_POST['metroThumbID']:'0');
		$response = new StdClass;
		$res = wp_get_attachment_image_src($metroThumbID, 'thumbnail');
		$iconUrl = ($res[0])?$res[0]:"http://placehold.it/100x100";
		$response->url = $iconUrl;		
		echo json_encode($response);
		die();
	}	

	/************* END BASE PLUGIN EVENTS ***********/	

	private $rxCPT;
	/*
	 * create youtube CPT
	 */
	public function addCPT(){
		$pluginOptions = LNClass_PluginOptions::getInstance();		
		$settings = array('post_custom_meta_data'=>LN_POST_CUSTOM_META, 'post_type' => LN_PORTFOLIO_SLUG, 'name' => __('Lunar', LN_PLUGIN_TEXTDOMAIN), 'menu_icon' => LN_TEMPPATH.'/com/sakuraplugins/images/icons/images-flickr.png',
		'singular_name' => __('Lunar', LN_PLUGIN_TEXTDOMAIN), 'rewrite' => $pluginOptions->getReWriteSlug(), 'add_new' => __('New Album', LN_PLUGIN_TEXTDOMAIN),
		'edit_item' => __('Edit', LN_PLUGIN_TEXTDOMAIN), 'new_item' => __('New Album', LN_PLUGIN_TEXTDOMAIN), 'view_item' => __('View item', LN_PLUGIN_TEXTDOMAIN), 'search_items' => __('Search items', LN_PLUGIN_TEXTDOMAIN),
		'not_found' => __('No item found', LN_PLUGIN_TEXTDOMAIN), 'not_found_in_trash' => __('Item not found in trash', LN_PLUGIN_TEXTDOMAIN), 
		'supports' => array('title'));
		
		$cptHelper = new LNClass_CPTHelper($settings);
		$this->rxCPT = new LNClass_CPT();
		$this->rxCPT->create($cptHelper, $settings);

		//re-write once
		$isReWrite = get_option('ct_is_rewrite');		
		if($isReWrite!=true){
			flush_rewrite_rules();			
			update_option('ct_is_rewrite', true);					
		}					
	}






}


?>