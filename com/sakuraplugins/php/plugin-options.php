<?php

/**
* plugin options
*/

class LNClass_PluginOptions
{

	private $options;
	function __construct()
	{
		$this->options = get_option(LN_PORTFOLIO_OPTION_GROUP);
	}

	static private $instance;
	static public function getInstance(){
		if(!isset(self::$instance)){
			self::$instance = new LNClass_PluginOptions();
		}
		return self::$instance;
	}

	//get re-write slug
	public function getReWriteSlug(){
		return (isset($this->options['mmReWriteSlug']))?$this->options['mmReWriteSlug']:LN_PORTFOLIO_REWRITE;	
	}

	//hover col for thumb background
	public function getThumbBackgroundHoverCol(){
		return (isset($this->options['thumbs_hover_background']))?$this->options['thumbs_hover_background']:'3fd9cd';		
	}	

	//hover col for thumb text
	public function getThumbTextHoverCol(){
		return (isset($this->options['thumbs_hover_text_color']))?$this->options['thumbs_hover_text_color']:'FFFFFF';		
	}

	//return nav hover color
	public function getNavHoverColor(){
		return (isset($this->options['ctp_nav_hover']))?$this->options['ctp_nav_hover']:'3fd9cd';		
	}


	//custom CSS
	public function getCustomCSS(){	
		return (isset($this->options['ctCustomCSS']))?$this->options['ctCustomCSS']:'';
	}

	//*********WOO RELATED**********

	//is exif
	public function isExif(){
		return true;
	}

	//allow all exif keys
	public function isAllExifKeys(){
		return true;
	}

	
	//allowed exif keys	
	public function getAllowedExifKeys(){
		$defaultExifKeys = 'aperture, credit, camera, caption, created_timestamp, copyright, focal_length, iso, shutter_speed, title, orientation';		  					
		$lnCustomEXIF = (isset($this->options['lnCustomEXIF']))?$this->options['lnCustomEXIF']:$defaultExifKeys;
		return array_map('trim',explode(",",$lnCustomEXIF));
		/*
		return array('aperture', 'credit', 'camera', 'caption', 'created_timestamp', 'copyright', 'focal_length', 
			'iso', 'shutter_speed', 'title', 'orientation');
		*/
	}
	//labels
	public function getDescriptionLabels(){
		$size = (isset($this->options['size']))?$this->options['size']:'Size';
		$file_size = (isset($this->options['file_size']))?$this->options['file_size']:'File size';
		$productTitleDummy = (isset($this->options['productTitleDummy']))?$this->options['productTitleDummy']:'Image';		  					
		return array('size'=>$size, 'file_size'=>$file_size, 'productTitleDummy'=>$productTitleDummy);
	}

	public function getWooFrontendLabels(){
		$quantity = (isset($this->options['quantity']))?$this->options['quantity']:'Quantity';
		$add_to_cart = (isset($this->options['add_to_cart']))?$this->options['add_to_cart']:'Add to cart';
		$view_cart = (isset($this->options['view_cart']))?$this->options['view_cart']:'View cart';			
		return array('quantity'=>$quantity, 'add_to_cart'=>$add_to_cart, 'view_cart'=>$view_cart);
	}

	public function getWooMaxImageWidth(){
		$max_image_size = (isset($this->options['max_image_size']))?$this->options['max_image_size']:'800';
		return $max_image_size;
	}


}

?>