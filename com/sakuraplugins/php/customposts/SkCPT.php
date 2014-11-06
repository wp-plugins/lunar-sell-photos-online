<?php
require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/customposts/GenericPostType.php');
require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/libs/resize_helper.php');
require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/plugin-options.php');

/**
 * Rx CPT
 */
class LNClass_CPT extends LNClass_GenericPostType {



	public function meta_box_gallery(){
		global $post;
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post_id;
															
		?>	
			<a id="removeAllFeaturedImagesBTN"class='sk-admin-button sk-button-right' href="#">Remove all</a>
			<a id="addFeaturedImagesBTN"class='sk-admin-button sk-button-right' href="#">Add images</a>

			<?php
			/**
			 * Check if WooCommerce is active
			 **/
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			    echo '<a id="convertToProductBTN"class="sk-admin-button sk-button-right" href="#">Convert to products</a>';
			}else{
				echo '<p class="lunarReqNotice">WooCommerce is not activated!</p>';
			}
			?>			
			<div class="sk_clear_fx"></div>
			<div id="featured_images_rx_portfolio" class="optionBox" data-post_meta="<?php echo LN_POST_CUSTOM_META;?>">
				<?php
					$customPostOptions = get_post_meta($post->ID, $this->getPostCustomMeta(), false);
					$featuredImagesAC = (isset($customPostOptions[0]['featuredImages']))?$customPostOptions[0]['featuredImages']:array();
					$imgIsVideoAC = (isset($customPostOptions[0]['imgIsVideo']))?$customPostOptions[0]['imgIsVideo']:array();
					$imgVideoCodeAC = (isset($customPostOptions[0]['imgVideoCode']))?$customPostOptions[0]['imgVideoCode']:array();

					$isProductAC = (isset($customPostOptions[0]['isProduct']))?$customPostOptions[0]['isProduct']:array();	
					$productIDAC = (isset($customPostOptions[0]['productID']))?$customPostOptions[0]['productID']:array();	
					$productPriceAC = (isset($customPostOptions[0]['productPrice']))?$customPostOptions[0]['productPrice']:array();

					$productTitleAC = (isset($customPostOptions[0]['productTitle']))?$customPostOptions[0]['productTitle']:array();
					$productCaptionAC = (isset($customPostOptions[0]['productCaption']))?$customPostOptions[0]['productCaption']:array();
					$productDescriptionAC = (isset($customPostOptions[0]['productDescription']))?$customPostOptions[0]['productDescription']:array();
					$productFilesizeAC = (isset($customPostOptions[0]['productFilesize']))?$customPostOptions[0]['productFilesize']:array();
					$productWidthAC = (isset($customPostOptions[0]['productWidth']))?$customPostOptions[0]['productWidth']:array();
					$productHeightAC = (isset($customPostOptions[0]['productHeight']))?$customPostOptions[0]['productHeight']:array();	

					function adjustHandler($array, $index, $isProductData=false){
						$out = '';
						if(sizeof($array)!=0){
							$out = $array[$index];
						}
						if($isProductData && sizeof($array)==0){
							$out = 'false';
						}
						return $out;
					}
							
				?>
				<div id="featuresThumbsContainer">
					<ul class="sortableThumbs" id="featuredImagesUI">
						<?php if(!empty($featuredImagesAC)):?>
									<?php								
									for ($i=0; $i < sizeof($featuredImagesAC); $i++) {
										$res = wp_get_attachment_image_src($featuredImagesAC[$i], 'medium');
										$iconUrl = 'http://placehold.it/150x150';
										if($res){
											$resizeRes = LNClass_ResizeHelper::resize($res[0], 150, 150, true);
											$iconUrl = ($resizeRes)?$resizeRes:$iconUrl;
										}
										$iconHTML = '<li class="ui-state-default"><div class="thumbBoxImage">';
	                               		$iconHTML .= '<div class="featuredThumb"><img src="'.$iconUrl.'" /></div>';
	                               		$iconHTML .= '<input class="lunarThumbID" type="hidden" name="'.$this->getPostCustomMeta().'[featuredImages][]" value="'.$featuredImagesAC[$i].'" />';	                               			                               		
                                 		$iconHTML .= '<input class="imgIsVideo" type="hidden" name="'.$this->getPostCustomMeta().'[imgIsVideo][]" value="'.$imgIsVideoAC[$i].'" />';
                                 		$iconHTML .= '<textarea class="imgVideoCode" name="'.$this->getPostCustomMeta().'[imgVideoCode][]">'.$imgVideoCodeAC[$i].'</textarea>';
                                 		
                                 		$iconHTML .= '<input class="isProduct" type="hidden" name="'.$this->getPostCustomMeta().'[isProduct][]" value="'.adjustHandler($isProductAC, $i, true).'" />';
                                 		$iconHTML .= '<input class="productID" type="hidden" name="'.$this->getPostCustomMeta().'[productID][]" value="'.adjustHandler($productIDAC, $i).'" />';
                                 		$iconHTML .= '<input class="productPrice" type="hidden" name="'.$this->getPostCustomMeta().'[productPrice][]" value="'.adjustHandler($productPriceAC, $i).'" />';

		                                 $iconHTML .= '<input class="productTitle" type="hidden" name="'.$this->getPostCustomMeta().'[productTitle][]" value="'.adjustHandler($productTitleAC, $i).'" />';
		                                 $iconHTML .='<input class="productCaption" type="hidden" name="'.$this->getPostCustomMeta().'[productCaption][]" value="'.adjustHandler($productCaptionAC, $i).'" />';
		                                 $iconHTML .= '<textarea class="productDescription" name="'.$this->getPostCustomMeta().'[productDescription][]">'.adjustHandler($productDescriptionAC, $i).'</textarea>';
		                                 $iconHTML .= '<input class="productFilesize" type="hidden" name="'.$this->getPostCustomMeta().'[productFilesize][]" value="'.adjustHandler($productFilesizeAC, $i).'" />';
		                                 $iconHTML .= '<input class="productWidth" type="hidden" name="'.$this->getPostCustomMeta().'[productWidth][]" value="'.adjustHandler($productWidthAC, $i).'" />';
		                                 $iconHTML .= '<input class="productHeight" type="hidden" name="'.$this->getPostCustomMeta().'[productHeight][]" value="'.adjustHandler($productHeightAC, $i).'" />';

		                                $wooLogoCSS = '';
			                            if(sizeof($isProductAC)!=0){
			                            	$wooLogoCSS = ($isProductAC[$i]=="true")?'display: block;':'';
			                            } 			                               			                               			                               		
                                 		
		                                 $iconHTML .= '<div class="featuredThumblogoUI" style="'.$wooLogoCSS.'">';
		                                     $iconHTML .= '<div class="wooLogo"></div>';
		                                 $iconHTML .= '</div>';	                               			                               			                               			                               		

										$iconHTML .= '<div class="featuredThumbOverlay">
										<div class="thumbOverlayMove"></div>
										<div class="thumbOverlayRemove"></div>	
										<div style="'.$wooLogoCSS.'" class="thumbOverlayEdit"></div>																		
										</div>';
										$iconHTML .= '</div></li>';
										echo $iconHTML;
									}
									?>
						<?php endif;?>					
					</ul>
					<div class="sk_clear_fx"></div>	
				</div>				
			</div>	

			<div id="wooCategories">
				<?php
					$args = array(
						'type'                     => 'product',
						'orderby'                  => 'name',
						'order'                    => 'ASC',
						'hide_empty'               => false,
						'hierarchical'             => false,
						'taxonomy'                 => 'product_cat',
						'pad_counts'               => false 
					);					
					$categories = get_categories($args);
					$categData = (!empty($categories))? json_encode($categories):'';
				?>
				<textarea id="wooCategoriesTA"><?php echo $categData;?></textarea>
			</div>

			<!--lunar update window-->
			<div id="lunnar_update_window">
	          <div class="genericLunarModal lunarUpdateModal">
	              <div class="lunarModalTitle">Edit product</div>
	              <div class="lunarModalContent">

			      <div class="modalContentRow">
			        <label>Title: <input style="margin-top: 10px;" class="prodTitle prodInputEdit" type="text" value="" /></label>        
			        <p></p>
			        <label>Price:<input style="margin-top: 10px;" class="pGenericPrice prodInputEdit" type="text" value="" /></label>
			        <p>Description:</p>
					<div id="lunar_editor_wrap">
						<?php wp_editor('', 'lunar_editor', array('wpautop'=>true, 'media_buttons'=>false, 'editor_class'=>'lunar_edit_textarea', 'textarea_rows'=>5)); ?> 		
					</div>			        
			      </div>

	              	<div class="modalBottomLine"></div>
	              	<a class="modalButton modalButtonOK" href="#">OK</a>
	              	<a class="modalButton modalButtonCancel" href="#">Cancel</a>
	              	<div class="modal-clear"></div>
	              </div>               
	          </div>
			</div>
			<!--/lunar update window-->

			<p class="sk_notice"><b>NOTE! </b>You can change the image's order by drag and drop. You can convert photos to WooCommerce products by clicking the "CONVERT TO PRODUCTS" button. Once images have been converted to Woo products, you can quick edit the product by hovering over the thumbs and click the edit button. </p>

		
		<?php
	}
		
		
}


?>