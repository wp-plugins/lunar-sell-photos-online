<?php

/**
 *  generic settings
 */
class LNClass_GenericSettingsPage{
	
	private $optionsGroup;
	function __construct($optsGroup) {
		$this->optionsGroup = $optsGroup;
		add_action('admin_init', array($this, 'registerSettingsGroups'));
	}
	
	//register settings group
	public function registerSettingsGroups(){
		register_setting($this->optionsGroup, $this->optionsGroup);
	}	
	
	//get option group
	protected function getOptionGroup(){
		return $this->optionsGroup;
	}	
}


/**
 * RXOptionPage
 */
class LNClass_OptionPage extends LNClass_GenericSettingsPage {
	
	public function settings_page(){
		$options = get_option($this->getOptionGroup());							
		?>
		<div class="spacer10"></div>
		<form method="post" action="options.php">
			<?php settings_fields($this->getOptionGroup()); ?>				
		  
		  <!--options wrapper-->
		  <div id="optionsWrapper">	
		  	<h1 class="optionsMainTitle">Lunar - Options</h1>

	
		  	<!--label open-->
		  	<div class="whiteOptionBox">
		  		<h2 class="optionsSecondTitle">Labels</h2>
		  		<div class="hLineTitle"></div>	  		
		  		<?php
		  			

		  			$size = (isset($options['size']))?$options['size']:'Size';
		  			$file_size = (isset($options['file_size']))?$options['file_size']:'File size';
		  			$productTitleDummy = (isset($options['productTitleDummy']))?$options['productTitleDummy']:'Image';		  			
		  		?>		  				  		

		  		<div class="skRow">
		  			<p><b>Labels used for WooCommerce product's description</b></p>
		  			<p class="sk_notice"><strong>NOTE!</strong> The labels below are used to automatically build WooComerce product's description and title. If the image title is not empty it will be used as title, if caption is not empty it will be used as title, if both image title and caption are empty the "Product dummy title" and image ID will be used as title.</p>
		  			<p class="sk_notice">The image description is made out of image size, image file size, description and exif data.</p>
		  			<div class="skOneThrid">
		  				<p>Size</p>
		  				<input type="text" name="<?php echo $this->getOptionGroup();?>[size]" value="<?php echo $size;?>" />
		  			</div>
		  			<div class="skOneThrid">
		  				<p>File size</p>
		  				<input type="text" name="<?php echo $this->getOptionGroup();?>[file_size]" value="<?php echo $file_size;?>" />
		  			</div>
		  			<div class="skOneThrid">
		  				<p>Product dummy title</p>
		  				<input type="text" name="<?php echo $this->getOptionGroup();?>[productTitleDummy]" value="<?php echo $productTitleDummy;?>" />
		  			</div>

		  			<div class="clear-admin"></div>
		  		</div>


		  	</div>
		  	<!--/label open-->	     

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', LN_PLUGIN_TEXTDOMAIN) ?>" />
	        </p>			  	   
		  	   	  			  	

		  	<!--advertisment-->
		  	<div class="whiteOptionBox whiteOptionBoxTopSpace">
		  		<h2 class="optionsSecondTitle">Advertising Pro Version</h2>
		  		<div class="hLineTitle"></div>
		  		<p class="sk_notice"><strong>NOTE!</strong> If you decide to buy and install the Pro version, please make sure you deactivate the free version first.</p>
		  		<p>There is a pro version of this plugin, it's available at <a target="_blank" href="http://www.sakuraplugins.com/products-list/lunar-sell-photos-online-wordpress-photography-plugin/">SakuraPlugins.com</a></p>
		  		<p>This plugin's main purpose is to automatically convert images to WooCommerce products, you can use the default WooCommerce shop page to display the products, however, for the Pro version I've added functionality that helps display the products in a more fancy way.</p>
		  		<p><b>Main Pro features</b></p>
		  		<ol style="list-style-type: disc;">
		  			<li>Display albums in a fancy way: <a target="_blank" href="http://www.sakuraplugins.com/showcase/lunar/">Preview Lunar View</a> (it doesn't require WooCommerce)</li>
		  			<li>Display WooCommerce product as three columns: <a target="_blank" href="http://www.sakuraplugins.com/showcase/lunar/woocommerce-three-cols/">Preview Woo Three Cols</a></li>
		  			<li>Display WooCommerce product as two columns: <a target="_blank" href="http://www.sakuraplugins.com/showcase/lunar/woocommerce-two-cols/">Preview Woo Two Cols</a></li>
		  			<li>Control allowed EXIF data and the order of EXIF keys: <a target="_blank" href="https://www.youtube.com/watch?v=zJMfZHRu9IA">See admin video</a></li>
		  		</ol>
		  	</div>
		  	<!--/advertisment-->	


			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', LN_PLUGIN_TEXTDOMAIN) ?>" />
	        </p>	        		  		        	  	

		</form>		
		
		<?php
	}





}


?>
