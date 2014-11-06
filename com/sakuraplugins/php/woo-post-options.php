<?php
require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/libs/resize_helper.php');
require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/plugin-options.php');

class LNClass_WooPostOptions
{
	private $ID;
	function __construct($ID)
	{
		$this->ID = $ID;		
	}

	//get product thumb
	public function getProductThumb($w, $h=null){
		//calculate h (4:3) - portrait
		$h = (75*$w)/100;//remove this line for custom H		
		$imageURL = "http://placehold.it/".$w."x".$h."/e8117f/FFFFFF/&text=Could not resize the image!";		
		$res = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), 'full');
		if($res){
			$thumb_temp_url = LNClass_ResizeHelper::resize($res[0], $w, $h, true);			
			$imageURL = ($thumb_temp_url)?$thumb_temp_url:$imageURL;
		}
		return $imageURL;
	}

	//get product thumb
	public function getFeaturedImage(){
		$pluginOptions = LNClass_PluginOptions::getInstance();
		$w = $pluginOptions->getWooMaxImageWidth();
		$imageURL = "http://placehold.it/".$w."x".'300'."/e8117f/FFFFFF/&text=Could not resize the image!";		
		$res = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), 'full');
		if($res){
			$thumb_temp_url = LNClass_ResizeHelper::resize($res[0], $w);			
			$imageURL = ($thumb_temp_url)?$thumb_temp_url:$imageURL;
		}
		return $imageURL;
	}	

	//get product price
	public function getProductSalePrice(){
		return get_post_meta($this->ID, '_sale_price', true);
	}


}

?>