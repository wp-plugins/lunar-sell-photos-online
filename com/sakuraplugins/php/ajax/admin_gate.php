<?php

/**
* 
*/
class AdminGate
{
	private $payload;
	public function setPayload($payload){
		$this->payload = $payload;		
	}

	private $price;
	public function setPrice($price){
		$this->price = $price;		
	}	
	//route
	public function route($route){
		switch ($route) {
			case 'convert':
				$woo_product_data = $this->createDownloadableImageProduct($this->payload);
				$wooProduct = new LunarWooProduct($woo_product_data['post_id'], $this->price, $woo_product_data['exif'], $woo_product_data['content'], $woo_product_data['title']);
				echo json_encode(new AdminGateResponse(AdminGateResponse::STATUS_OK, "", $wooProduct));
				die();
				break;	
			case 'update':				
				$updateResult = $this->updateExistingProduct($this->payload);
				echo json_encode(new AdminGateResponse(AdminGateResponse::STATUS_OK, "", $updateResult['post_id']));
				die();
				break;
			case 'delete':
				$deleteResult = $this->removeWooProduct($this->payload);								
				echo json_encode(new AdminGateResponse(AdminGateResponse::STATUS_OK, "", $deleteResult));
				die();
				break;										
			default:
				return new AdminGateResponse(AdminGateResponse::STATUS_FAIL, "Error, route not found!");
				die();
				break;
		}
	}

	//generic error helper
	public function throwError($msg){		
		echo json_encode(new AdminGateResponse(AdminGateResponse::STATUS_FAIL, $msg));
		die();
	}

	//remove product
	private function removeWooProduct($payloadData){		
		$deleteResult = wp_delete_post($payloadData['productID'], true);
		return array('post_id'=>$payloadData['productID'], 'delete_result'=>(!$deleteResult)?false:true);
	}

	//update existing product
	private function updateExistingProduct($payloadData){
		//update price, description, 
		$woo_post = array(
		    'ID'           => $payloadData['productID'],
		    'post_title' => $payloadData['title'],
		    'post_content' => $payloadData['description']
		);

		// Update the post into the database
	    $post_id = wp_update_post($woo_post, true);	   
		if(is_wp_error($post_id)){
			$this->throwError($post_id->get_error_message());
			return;
		}
		update_post_meta( $payloadData['productID'], '_price', $payloadData['price']);
		update_post_meta( $payloadData['productID'], '_sale_price', $payloadData['price']);
		update_post_meta( $payloadData['productID'], '_regular_price', $payloadData['price']);		
		return array('post_id'=>$post_id);				
	}

	//create downloadable product
	private function createDownloadableImageProduct($payloadData){
	   require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/plugin-options.php');
	   $pluginOptions = LNClass_PluginOptions::getInstance();
	   $labels = $pluginOptions->getDescriptionLabels();
	   $allowedExifKeys = $pluginOptions->getAllowedExifKeys();

	   $thumbID = $payloadData['imageID'];
	   $productTitle = wptexturize($payloadData['productTitle']);
	   $productCaption = wptexturize($payloadData['productCaption']);
	   $productDescription = wptexturize($payloadData['productDescription']);
	   $productFilesize = $payloadData['productFilesize'];
	   $productWidth = $payloadData['productWidth'];
	   $productHeight = $payloadData['productHeight'];	
	   $_wooCatID = (isset($payloadData['_wooCatID']))?$payloadData['_wooCatID']:"none";	

	   $imageFile = get_attached_file($thumbID);
	   $exif = wp_read_image_metadata($imageFile);		

	   $content = '<p><b class="lnWooBold">'.$labels['size'].':</b> '.$productWidth.' x '.$productHeight.' px</p>';
	   $content .= '<p><b class="lnWooBold">'.$labels['file_size'].':</b> ('.$productFilesize.')</p><br /><br />';

	   //description
	   $content .= ($productDescription!="")?$productDescription.'<br /><br />':'';

	   $exifAC = array();
	   //exif
	   if($pluginOptions->isExif()){
		   foreach ($exif as $key => $value) {
		   		if($exif[$key]===0 || !empty($exif[$key])){
		   			array_push($exifAC, array('key'=>$key, 'key_val'=>$exif[$key]));
		   			/*
		   			if($pluginOptions->isAllExifKeys()){
		   					//if display all available exif data
		   					$content .= '<p><b>'.ucfirst($key).':</b> '.$exif[$key].'</p>';
		   			}else{
		   				if($this->isExifKeyAllowed($key, $allowedExifKeys)){
		   					$content .= '<p><b>'.ucfirst($key).':</b> '.$exif[$key].'</p>';	
		   				}
		   			}
		   			*/
		   		}		   			
		   }
	   }

	   if(sizeof($exifAC)!=0){
	   		for ($i=0; $i < sizeof($allowedExifKeys); $i++) { 
	   			$allowedExifKey = $allowedExifKeys[$i];

	   			for ($j=0; $j < sizeof($exifAC); $j++) { 
	   				if($exifAC[$j]['key']==$allowedExifKey){
	   					$content .= '<p><b>'.ucfirst($exifAC[$j]['key']).':</b> '.$exifAC[$j]['key_val'].'</p>';	
	   					break;
	   				}
	   			}
	   		}
	   }

	   //title
	   $title = ($productTitle!="")?$productTitle:$productCaption;
	   $title = ($title=="")? $labels['productTitleDummy'].' '.$thumbID:$title;

	   $post_temp = array(
	     'post_author' => get_current_user_id(),
	     'post_content' => $content,
	     'post_status' => "publish",
	     'post_title' => $title,
	     'post_parent' => '',
	     'post_type' => "product"
	     );	
	   if($_wooCatID!="none")
	   		$post_temp = array_merge($post_temp, array('tax_input'=>array('product_cat'=>$_wooCatID)));	

	   $post_id = wp_insert_post($post_temp, true);
	   if(is_wp_error($post_id)){
	   		$this->throwError($post_id->get_error_message());
	   		return;
	   }

	   	 update_post_meta($post_id, '_thumbnail_id', $thumbID);
	     update_post_meta( $post_id, '_visibility', 'visible');
	     update_post_meta( $post_id, '_stock_status', 'instock');
	     update_post_meta( $post_id, 'total_sales', '0');
	     update_post_meta( $post_id, '_downloadable', 'yes');
	     update_post_meta( $post_id, '_virtual', 'yes');
	     update_post_meta( $post_id, '_regular_price', $this->price);
	     update_post_meta( $post_id, '_sale_price', $this->price);
	     update_post_meta( $post_id, '_purchase_note', "" );
	     update_post_meta( $post_id, '_featured', "no" );
	     update_post_meta( $post_id, '_weight', "" );
	     update_post_meta( $post_id, '_length', "" );
	     update_post_meta( $post_id, '_width', "" );
	     update_post_meta( $post_id, '_height', "" );
	     update_post_meta($post_id, '_sku', "");
	     update_post_meta( $post_id, '_product_attributes', array());
	     update_post_meta( $post_id, '_sale_price_dates_from', "" );
	     update_post_meta( $post_id, '_sale_price_dates_to', "" );
	     update_post_meta( $post_id, '_price', $this->price);
	     update_post_meta( $post_id, '_sold_individually', "" );
	     update_post_meta( $post_id, '_manage_stock', "no" );
	     update_post_meta( $post_id, '_backorders', "no" );
	     update_post_meta( $post_id, '_stock', "" );	

	    $woo_files = array();   
        $file_url = wp_get_attachment_url($thumbID);
        $woo_files[md5( $file_url )] = array(
            'file' => $file_url,
            'name' => basename($file_url)
        );	  
        update_post_meta( $post_id, '_downloadable_files', $woo_files );   
        return array('post_id'=>$post_id, 'exif'=>$exif, 'content'=>$content, 'title'=>$title);
	}

	private function isExifKeyAllowed($key, $keyMap){
		$out = false;
		for ($i=0; $i < sizeof($keyMap); $i++) { 
			if($keyMap[$i]==$key){
				$out = true;
				break;
			}
		}
		return $out;
	}


}

/**
* default gate response
*/
class AdminGateResponse{
	const STATUS_OK = "STATUS_OK";
	const STATUS_FAIL = "STATUS_FAIL";
	public $status;
	public $data;
	public $message;
	function __construct($status, $message, $data=null){
		$this->status = $status;
		$this->data = $data;
		$this->message = $message;
	}
}

class LunarWooProduct{
	public $id;
	public $price;
	public $exif;
	public $content;
	public $title;
	function __construct($id, $price, $exif, $content, $title){
		$this->id = $id;
		$this->price = $price;
		$this->exif = $exif;
		$this->content = $content;
		$this->title = $title;
	}	
}



?>