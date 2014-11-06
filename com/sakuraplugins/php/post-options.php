<?php
require_once(LN_CLASS_PATH.'/com/sakuraplugins/php/libs/resize_helper.php');

class LNClass_PostOptions
{
	private $customPostOptions;
	private $ID;
	function __construct($ID)
	{
		$this->ID = $ID;
		$this->customPostOptions = get_post_meta($ID, LN_POST_CUSTOM_META, false);
	}

	//get featured image thumb
	public function getFeaturedImageThumb(){
		$imageURL = false;
		$res = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), 'thumbnail');
		if($res){			
			$imageURL = $res[0];
		}
		return $imageURL;
	}

	//get featured image featured
	public function getFeaturedImage($w, $h=null){		
		$imageURL = "";
		$res = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), 'full');
		if($res){
			$imageURL = $res[0];
			$resizedImage = LNClass_ResizeHelper::resize($imageURL, $w);	
			if($resizedImage){
				$imageURL = $resizedImage;
			}								
		}
		return $imageURL;		
	}	

	//get cover image size			
	public function getCoverImageSize(){
		return (isset($this->customPostOptions[0]['featuredSize']))?$this->customPostOptions[0]['featuredSize']:'25';
	}

	//album covers urls
	public function getAlbumCovers($size){		
		$out = false;
		switch ($size) {
			case '25':
				$out = array('albumCoverLarge'=>$this->getFeaturedImage(500), 'albumCoverMedium'=>$this->getFeaturedImage(300), 'albumCoverSmall'=>$this->getFeaturedImage(180));
				break;
			case '30':
				$out = array('albumCoverLarge'=>$this->getFeaturedImage(700), 'albumCoverMedium'=>$this->getFeaturedImage(450), 'albumCoverSmall'=>$this->getFeaturedImage(300));
				break;
			case '33':
				$out = array('albumCoverLarge'=>$this->getFeaturedImage(700), 'albumCoverMedium'=>$this->getFeaturedImage(450), 'albumCoverSmall'=>$this->getFeaturedImage(300));
				break;
			case '50':
				$out = array('albumCoverLarge'=>$this->getFeaturedImage(900), 'albumCoverMedium'=>$this->getFeaturedImage(500), 'albumCoverSmall'=>$this->getFeaturedImage(350));
				break;
			case '70':
				$out = array('albumCoverLarge'=>$this->getFeaturedImage(1000), 'albumCoverMedium'=>$this->getFeaturedImage(600), 'albumCoverSmall'=>$this->getFeaturedImage(400));
				break;
			case '100':
				$out = array('albumCoverLarge'=>$this->getFeaturedImage(1200), 'albumCoverMedium'=>$this->getFeaturedImage(700), 'albumCoverSmall'=>$this->getFeaturedImage(500));
				break;																				
			
			default:
				$out = array('albumCoverLarge'=>$this->getFeaturedImage(1200), 'albumCoverMedium'=>$this->getFeaturedImage(700), 'albumCoverSmall'=>$this->getFeaturedImage(500));
				break;
		}
		return $out;
	}


	//get featured images
	public function getFeaturedImages(){
		$featuredImagesAC = (isset($this->customPostOptions[0]['featuredImages']))?$this->customPostOptions[0]['featuredImages']:array();
		return (sizeof($featuredImagesAC)!=0)?$featuredImagesAC:false;
	}

	//get subtitle
	public function getSubtitle(){
		return (isset($this->customPostOptions[0]['subtitle']))?$this->customPostOptions[0]['subtitle']:'';
	}

	//get custom URL
	public function getCustomURL(){
		$useRedirect = (isset($this->customPostOptions[0]['useRedirect']))?$this->customPostOptions[0]['useRedirect']:'';
		if($useRedirect!="ON"){
			return false;
		}
		return (isset($this->customPostOptions[0]['redirect_url']))?$this->customPostOptions[0]['redirect_url']:'';		
	}

	//get preview video
	public function getPreviewVideo(){
		$useVideoCB = (isset($this->customPostOptions[0]['useVideoCB']))?$this->customPostOptions[0]['useVideoCB']:'';
		if($useVideoCB!="ON"){
			return false;
		}	
		$previewVideoID = (isset($this->customPostOptions[0]['previewVideoID']))?$this->customPostOptions[0]['previewVideoID']:'';
		if($previewVideoID==""){
			return false;
		}
		$videoURL = wp_get_attachment_url($previewVideoID);
		if(!$videoURL){
			return false;
		}
		$oggVideoID = (isset($this->customPostOptions[0]['oggVideoID']))?$this->customPostOptions[0]['oggVideoID']:'';
		$previewVideoWebmURL = wp_get_attachment_url($oggVideoID);

		$videoAllTileCB = (isset($this->customPostOptions[0]['videoAllTileCB']))?$this->customPostOptions[0]['videoAllTileCB']:'';
		$videoAllTile = ($videoAllTileCB=="ON")?true:false;
		return array('videoURL'=>$videoURL, 'videoAllTime'=>$videoAllTile, 'previewVideoWebmURL'=>$previewVideoWebmURL);
	}

	//get special album
	public function isSpecialAlbum(){
		$isSpecialProjectCB = (isset($this->customPostOptions[0]['isSpecialProjectCB']))?$this->customPostOptions[0]['isSpecialProjectCB']:'';
		$isSpecial = ($isSpecialProjectCB=="ON")?true:false;
		if(!$isSpecial)
			return false;
		$isSpecialProjectImageLeftCB = (isset($this->customPostOptions[0]['isSpecialProjectImageLeftCB']))?$this->customPostOptions[0]['isSpecialProjectImageLeftCB']:'';
		$side = ($isSpecialProjectImageLeftCB=="ON")?'left':'right';
		return array('isSpecial'=>$isSpecial, 'specialSide'=>$side);
	}

	//get album gallery
	public function getAlbumGallery(){
		$featuredImagesAC = (isset($this->customPostOptions[0]['featuredImages']))?$this->customPostOptions[0]['featuredImages']:array();
		$imgIsVideoAC = (isset($this->customPostOptions[0]['imgIsVideo']))?$this->customPostOptions[0]['imgIsVideo']:array();
		$imgVideoCodeAC = (isset($this->customPostOptions[0]['imgVideoCode']))?$this->customPostOptions[0]['imgVideoCode']:array();
		if(empty($featuredImagesAC))
			return false;
		$gallery = array();
		for ($i=0; $i < sizeof($featuredImagesAC); $i++) { 			
			$imageSizes = array('large'=>$this->getGalleryImage($featuredImagesAC[$i], 500), 'medium'=>$this->getGalleryImage($featuredImagesAC[$i], 300), 'small'=>$this->getGalleryImage($featuredImagesAC[$i], 180), 'fullMobile'=>$this->getGalleyImageFull($featuredImagesAC[$i]), 'largeMobile'=>$this->getGalleyImageLarge($featuredImagesAC[$i]), 'mediumMobile'=>$this->getGalleyImageMedium($featuredImagesAC[$i]));
			$tempOject = array('isVideo'=>$imgIsVideoAC[$i], 'videoCodeAC' => base64_encode($imgVideoCodeAC[$i]), 'imageSizes'=>$imageSizes);
			array_push($gallery, $tempOject);
		}
		return $gallery;
	}

	//get featured image featured
	private function getGalleryImage($id, $w, $h=null){		
		$imageURL = "";
		$res = wp_get_attachment_image_src($id, 'full');
		if($res){
			$imageURL = $res[0];
			$resizedImage = LNClass_ResizeHelper::resize($imageURL, $w);	
			if($resizedImage){
				$imageURL = $resizedImage;
			}								
		}
		return $imageURL;		
	}	
	private function getGalleyImageFull($id){
		$imageURL = "";
		$res = wp_get_attachment_image_src($id, 'full');
		if($res){
			$imageURL = $res[0];
		}		
		return $imageURL;	
	}

	private function getGalleyImageLarge($id){
		$imageURL = "";
		$res = wp_get_attachment_image_src($id, 'large');
		if($res){
			$imageURL = $res[0];
		}		
		return $imageURL;	
	}

	private function getGalleyImageMedium($id){
		$imageURL = "";
		$res = wp_get_attachment_image_src($id, 'medium');
		if($res){
			$imageURL = $res[0];
		}		
		return $imageURL;	
	}		


}

?>