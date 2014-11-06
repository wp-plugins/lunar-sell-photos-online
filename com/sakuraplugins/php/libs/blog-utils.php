<?php


/**
 * used for blog page pagination
 */
class LNClass_BlogPagination{
	
	//page pagination 
	/**
	 * requires pagination css
	 */
	public function kriesi_pagination($pages = '', $range = 2){  
	     $showitems = ($range * 2)+1;  
	
	     global $paged;
	     if(empty($paged)) $paged = 1;
	
	     if($pages == '')
	     {
	         global $wp_query;
	         $pages = $wp_query->max_num_pages;
	         if(!$pages)
	         {
	             $pages = 1;
	         }
	     }   
		
		$out = '';
	     if(1 != $pages)
	     {
	         $out .= "<div class='paginationln'>";
	         if($paged > 2 && $paged > $range+1 && $showitems < $pages) $out .= "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
	         if($paged > 1 && $showitems < $pages) $out .= "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";
	
	         for ($i=1; $i <= $pages; $i++)
	         {
	             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
	             {
	                 $out .= ($paged == $i)? "<span class='current currentPage'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
	             }
	         }
	
	         if ($paged < $pages && $showitems < $pages) $out .= "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
	         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) $out .= "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
	         $out .= "</div>";
	     }
		 return $out;
	}
	
}


?>