jQuery(document).ready(function(){    

	var testUploadExifBTN = jQuery('#addTestImageExifBTN');	
	testUploadExifBTN.click(function(e){
		e.preventDefault();

              var send_attachment_bkp = wp.media.editor.send.attachment;
                    var frame = wp.media({
                        title: "Select Image",
                        multiple: false,
                        library: { type: 'image' },
                        button : { text : 'add image' }
                    });                   
                    frame.on('close',function() {                        
                        var selection = frame.state().get('selection');
                        selection.each(function(attachment) { 
                        		//attachment.id 
                        		//attachment.attributes.title  
                        		SKExifAjax.init(attachment.id);                             	                                                     
                        });
                         wp.media.editor.send.attachment = send_attachment_bkp;
                    });                                                              
                    frame.open();                    
		return false;
	});
});

var SKExifAjax = (function(){
	return{
		init: function(thumbID){
	        jQuery.post(ajaxurl, {action: 'lunar_action_exif', id: thumbID}, function(data) {
	        	//console.log(data);
	            var response = JSON.parse(data)
	            if(response.status=="FAIL"){
	                alert(response.message);
	                return;
	            }
	            //customExifBoxDemo
	            //console.log(response);
	            if(response.data.length==0){
	            	alert("There is no exif data for the selected image!");
	            	return;
	            }
	            jQuery('.customExifBoxDemo').css('display', 'block');
	            var ta = document.getElementById('customExifBoxDemo');
	            ta.value = response.data;
	        });			
		}
	}
})();