<?php


/**
 * scripts manager
 */
class LNClass_ScriptManager {
	
	public static function enqueFluidDivs(){
		//fluid iFrames
		wp_register_script( 'fluidvids', LN_JS.'/external/fluidvids.min.js', array('jquery'), null, TRUE);
		wp_enqueue_script('fluidvids');		
	}
	
	public static function enqueTweenmax($version="1.11.8"){
		//tween js
		wp_register_script('sk_tweenmax', LN_JS.'/external/TweenMax.min.js', array('jquery'), null, TRUE);
		wp_enqueue_script('sk_tweenmax');			
	}	

	public static function enquePackery(){
		//sk_packery
		wp_register_script('sk_packery', LN_JS.'/external/packery1.2.4.js', array('jquery'), null, TRUE);
		wp_enqueue_script('sk_packery');			
	}		
	

	public static function enqueJqueryUI(){
			//jqueryui		
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-widget');
			wp_enqueue_script('jquery-ui-selectable');
			wp_enqueue_script('jquery-ui-button');	
			wp_enqueue_script('jquery-ui-mouse');
			wp_enqueue_script('jquery-ui-spinner');
			wp_enqueue_script('jquery-ui-accordion');
			wp_enqueue_script('jquery-ui-dialog');				
	}

	//load thinkbox 
	public static function enqueueThickbox()
	{
		wp_enqueue_script('thickbox');
		wp_enqueue_script('media-upload');
		wp_enqueue_style('thickbox');		
	}		

	public static function enqueColorPicker(){
			 wp_register_style( 'cpicker_layout', LN_TEMPPATH.'/com/sakuraplugins/js'.'/cpick/colpick.css');		 
		     wp_enqueue_style( 'cpicker_layout');
			 wp_register_script( 'color_picker', LN_TEMPPATH.'/com/sakuraplugins/js'.'/cpick/colpick.js', array('jquery'));
			 wp_enqueue_script('color_picker');		     	 		
	}



}


?>