jQuery(document).ready(function(){    
    var lnAdmin = new LunaAdmin();
    lnAdmin.init();
});
var SKLuna = {};
function LunaAdmin(){
	var post_meta = jQuery('#featured_images_rx_portfolio').attr('data-post_meta');
	this.init = function(){
		initSortableFeaturedImages();
		handleImagesUpload();
    SKLuna.Woo.InfoUI.LunarUpdateWindow.init();
	}


   function initSortableFeaturedImages(){
       jQuery("#featuredImagesUI").sortable({
            placeholder: "ui-state-highlight",
            stop: function( event, ui ){     
            }
        });
        jQuery("#featuredImagesUI").disableSelection(); 
        jQuery("#featuredImagesUI").children().each(function(indx){
            thumbsHoverAction(jQuery(this).find('.featuredThumbOverlay'));
            jQuery(this).find('.thumbOverlayRemove').click(function(e){
                e.preventDefault();

                var iconUI = jQuery(this).parent().parent().parent();
                var isProduct = (iconUI.find('.isProduct').val()=="true")?true:false;
                if(!isProduct){
                  if(confirm('Are you sure you want to remove this image?')){
                      iconUI.remove();                    
                      checkChildrens();  
                  } 
                }else{
                  if(confirm('Are you sure you want to remove this image? The linked WooCommerce product will also be deleted!')){
                      iconUI.remove();                    
                      checkChildrens();
                      var list = [];
                      list.push({thumbUI: iconUI});
                      SKLuna.Woo.remove(list); 
                  }                     
                }                             
            });

            jQuery(this).find('.thumbOverlayEdit').click(function(e){
                e.preventDefault();
           		   var iconUI = jQuery(this).parent().parent().parent();
           		   //openEditBox(iconUI, iconUI.find('.imgSizeUI').val());
                 //openEditBox(iconUI);  
                 SKLuna.Woo.InfoUI.LunarUpdateWindow.show(iconUI);
                 SKLuna.Woo.InfoUI.LunarUpdateWindow.setCallbacks(function(updateData){                    
                    SKLuna.Woo.update(updateData, iconUI);
                 }, function(){});

            });            

        });       
   }	

   function checkChildrens(){       
       var dummyData = '<input id="dummyFeaturedImagesDATA" type="hidden" name="'+post_meta+'[featuredImages]" value="" />';
       var children = jQuery("#featuredImagesUI").children();   
       if(children.length==0){
           removeDummyFeaturedData();
           jQuery(dummyData).appendTo(jQuery('#featuresThumbsContainer'));
       }else{
           removeDummyFeaturedData();
       }
   } 
   
   function removeDummyFeaturedData(){
       try{
           jQuery('#dummyFeaturedImagesDATA').remove();
       }catch(e){}
   }    

	function handleImagesUpload(){
      //convert to products
       jQuery('#convertToProductBTN').click(function(e){
           e.preventDefault();
           var children = jQuery("#featuredImagesUI").children();               
           if(children.length==0){
               alert('There are no images to convert!');
               return;
           }
           var toBeconvertedList = [];
           var count = 0;
           jQuery("#featuredImagesUI .thumbBoxImage").each(function(indx){
                count++;
                var thumbBoxImageUI = jQuery(this);
                if(thumbBoxImageUI.find('.isProduct').val()=="false"){
                    toBeconvertedList.push({thumbBoxImageUI: thumbBoxImageUI});
                }
           });
           if(count==0){
              alert('There are no images to convert!');
              return;
           }            
           if(toBeconvertedList.length==0){
              alert('All images are already converted as products!');
              return;
           }            
           if(SKLuna.Woo.getIsBusy()){
              alert('Currently processing products. Please wait!');
              return;              
           }
           var beforeConvertUI = new SKLuna.Woo.InfoUI.BeforeConvert();
           beforeConvertUI.modaltitle = beforeConvertUI.modaltitle+" ::: ready to convert "+toBeconvertedList.length+" images";           
            beforeConvertUI.setOKCallback(function(obj){                
                SKLuna.Woo.InfoUI.dismiss();
                SKLuna.Woo.convert(toBeconvertedList, obj.getUI().find('.pGenericPrice').val(), obj.getUI().find('.pGenericCategory').val());                       
            });
            SKLuna.Woo.InfoUI.showInfo(beforeConvertUI);
            beforeConvertUI.listenCategoryChange();                                 
       });
      //remove images
       jQuery('#removeAllFeaturedImagesBTN').click(function(e){
           e.preventDefault();
           try{
               var children = jQuery("#featuredImagesUI").children();               
               if(children.length==0){
                   alert('There are no featured images');
                   return;
               }
                                                           
               var listToDelete = [];
               var thereAreProducts = false;
               for (var i = 0; i < children.length; i++) {
                  var child = jQuery(children[i]);
                  if(child.find('.isProduct').val()=="true"){
                      thereAreProducts = true;
                      listToDelete.push({thumbUI: child.find('.thumbBoxImage')});
                  }                 
               };


                if(!thereAreProducts){
                  if(confirm('Are you sure you want to remove all images?')){
                     jQuery("#featuredImagesUI").empty();
                     checkChildrens(); 
                  } 
                }else{
                  if(confirm('Are you sure you want to remove all images? The linked WooCommerce products will also be deleted!')){
                      jQuery("#featuredImagesUI").empty();
                      checkChildrens();
                      SKLuna.Woo.remove(listToDelete); 
                  }                     
                } 

           }catch(e){}
       });

       //add images
       jQuery('#addFeaturedImagesBTN').click(function(e){
              e.preventDefault();
              var send_attachment_bkp = wp.media.editor.send.attachment;
                    var frame = wp.media({
                        title: "Select Images",
                        multiple: true,
                        library: { type: 'image' },
                        button : { text : 'add image' }
                    });
                    
                    frame.on('close',function() {                        
                        var selection = frame.state().get('selection');
                        selection.each(function(attachment) {  
                              //console.log(attachment);
                              /*
                              console.log(attachment.attributes.title);
                              console.log(attachment.attributes.caption);
                              console.log(attachment.attributes.description);
                              console.log(attachment.attributes.filesizeHumanReadable);
                              console.log(attachment.attributes.width);
                              console.log(attachment.attributes.height);  
                              */                                                          
                              /*                              
                              console.log(attachment.attributes.authorName);
                              console.log(attachment.attributes.filename);
                              */


                               var iconUrl = 'http://placehold.it/150x150';
                               if(attachment.attributes.sizes.thumbnail!=undefined){
                                   iconUrl = (attachment.attributes.sizes.thumbnail.url!='')?attachment.attributes.sizes.thumbnail.url:iconUrl;
                               }                              
                               featuredImageHTML = '<li class="ui-state-default"><div class="thumbBoxImage">';
                               featuredImageHTML += '<div class="featuredThumb"><img src="'+iconUrl+'" /></div>';
                                 featuredImageHTML += '<input class="lunarThumbID" type="hidden" name="'+post_meta+'[featuredImages][]" value="'+attachment.id+'" />';
                                 featuredImageHTML += '<input class="imgIsVideo" type="hidden" name="'+post_meta+'[imgIsVideo][]" value="false" />';
                                 featuredImageHTML += '<textarea class="imgVideoCode" name="'+post_meta+'[imgVideoCode][]"></textarea>';

                                 featuredImageHTML += '<input class="isProduct" type="hidden" name="'+post_meta+'[isProduct][]" value="false" />';
                                 featuredImageHTML += '<input class="productID" type="hidden" name="'+post_meta+'[productID][]" value="" />';
                                 featuredImageHTML += '<input class="productPrice" type="hidden" name="'+post_meta+'[productPrice][]" value="" />';

                                 featuredImageHTML += '<input class="productTitle" type="hidden" name="'+post_meta+'[productTitle][]" value="'+attachment.attributes.title+'" />';
                                 featuredImageHTML += '<input class="productCaption" type="hidden" name="'+post_meta+'[productCaption][]" value="'+attachment.attributes.caption+'" />';
                                 featuredImageHTML += '<textarea class="productDescription" name="'+post_meta+'[productDescription][]">'+attachment.attributes.description+'</textarea>';
                                 featuredImageHTML += '<input class="productFilesize" type="hidden" name="'+post_meta+'[productFilesize][]" value="'+attachment.attributes.filesizeHumanReadable+'" />';
                                 featuredImageHTML += '<input class="productWidth" type="hidden" name="'+post_meta+'[productWidth][]" value="'+attachment.attributes.width+'" />';
                                 featuredImageHTML += '<input class="productHeight" type="hidden" name="'+post_meta+'[productHeight][]" value="'+attachment.attributes.height+'" />';




                                 featuredImageHTML += '<div class="featuredThumblogoUI">';
                                     featuredImageHTML += '<div class="wooLogo"></div>';
                                 featuredImageHTML += '</div>';
                                 
                                 featuredImageHTML += '<div class="featuredThumbOverlay">';
                                   featuredImageHTML +='<div class="thumbOverlayMove"></div>';
                                   featuredImageHTML +='<div class="thumbOverlayRemove"></div>';
                                   featuredImageHTML +='<div class="thumbOverlayEdit"></div>';                                                                                        
                                 featuredImageHTML +='</div>';
                               featuredImageHTML += '</div></li>';
                               var featuredImageJq = jQuery(featuredImageHTML);
                               featuredImageJq.appendTo("#featuredImagesUI");
                               jQuery("#featuredImagesUI").sortable("refresh"); 
                               
                               checkChildrens(); 
                               
                               thumbsHoverAction(featuredImageJq.find('.featuredThumbOverlay'));
                               featuredImageJq.find('.thumbOverlayRemove').click(function(e){
                                    e.preventDefault();
                                    var iconUI = jQuery(this).parent().parent().parent();
                                    var isProduct = (iconUI.find('.isProduct').val()=="true")?true:false;
                                    if(!isProduct){
                                      if(confirm('Are you sure you want to remove this image?')){
                                          iconUI.remove();                    
                                          checkChildrens();  
                                      } 
                                    }else{
                                      if(confirm('Are you sure you want to remove this image? The linked WooCommerce product will also be deleted!')){
                                          iconUI.remove();                    
                                          checkChildrens();
                                          var list = [];
                                          list.push({thumbUI: iconUI});
                                          SKLuna.Woo.remove(list); 
                                      }                     
                                    } 
                               });
                               featuredImageJq.find('.thumbOverlayEdit').click(function(e){
                                    e.preventDefault();
                                    var iconUI = jQuery(this).parent().parent().parent();

                                    SKLuna.Woo.InfoUI.LunarUpdateWindow.show(iconUI);
                                    SKLuna.Woo.InfoUI.LunarUpdateWindow.setCallbacks(function(updateData){                    
                                      SKLuna.Woo.update(updateData, iconUI);
                                    }, function(){});                                     
                               });                                                                
                        });
                         wp.media.editor.send.attachment = send_attachment_bkp;
                    });                                  
                            
                    frame.open();
             return false;            
       });		
	}

   function thumbsHoverAction(el){
       el.css('opacity', 0);       
       el.hover(function(e){
           jQuery(this).animate({
            opacity: 1
           }, 200);
       }, function(e){
           jQuery(this).animate({
            opacity: 0
           }, 200);           
       });
   }

   initFeaturedImageSettings();
   //featured image size
   function initFeaturedImageSettings(){
      var featuredSizesCB = jQuery('#luna_featuredSizeUI');      
      jQuery('#luna_featuredSizeUI option').each(function(indx){
        if(jQuery(this).val()==jQuery('#ln_featuredValUI').val()){
          jQuery(this).attr('selected', 'selected');
        }
      });
      featuredSizesCB.change(function(e){        
        jQuery('#ln_featuredValUI').val(jQuery('#luna_featuredSizeUI option:selected').val());
      }); 

      //video code
      jQuery('#ln_useVideoCB').change(function(){
        if(this.checked){          
          jQuery('#redirect_video_content_ui').removeClass('sk_modal_hide');          
        }else{
          jQuery('#redirect_video_content_ui').addClass('sk_modal_hide');
        }
      });
      //MP4
      jQuery("#addPreviewVideoBTN").click(function(e){
          e.preventDefault();
          SkVideoUpload.init(function(id, filename){
              jQuery('#previewVideoID').val(id);
              jQuery('#previewVideoFilename').val(filename);
              jQuery('#videoUploadInfo').html(filename);              
          });
      });
      //OGG
      jQuery("#addPreviewVideoOGGBTN").click(function(e){
          e.preventDefault();
          SkVideoUpload.init(function(id, filename){
              jQuery('#oggVideoID').val(id);
              jQuery('#oggVideoFilename').val(filename);
              jQuery('#oggVideoInfo').html(filename);              
          });
      });      

      //special project
      
      jQuery('#isSpecialProjectCB').change(function(){
        if(this.checked){          
          jQuery('#specialProjectUI').removeClass('sk_modal_hide');          
        }else{
          jQuery('#specialProjectUI').addClass('sk_modal_hide');
        }
      });     
      

      //use redirect
      jQuery('#useRedirectCB').change(function(){
        if(this.checked){
          jQuery('#redirect_content_ui').removeClass('sk_modal_hide');
        }else{
          jQuery('#redirect_content_ui').addClass('sk_modal_hide');
        }
      });    

   }

}

//video upload
var LnVideoUpload = function(){};
LnVideoUpload.prototype = {
    init: function(callback){
              var send_attachment_bkp = wp.media.editor.send.attachment;
                    var frame = wp.media({
                        title: "Select Video",
                        multiple: false,
                        library: { type: 'video' },
                        button : { text : 'add video' }
                    });  
                    frame.on('close',function() {   
                        var count = 0;                     
                        var selection = frame.state().get('selection');
                        selection.each(function(attachment){
                          count++;
                          if(count==1){
                            callback(attachment.id, attachment.attributes.filename);                            
                              //console.log(attachment.id);
                              //console.log(attachment.attributes.filename);
                          }
                        });                        

                        wp.media.editor.send.attachment = send_attachment_bkp;
                    });    
              frame.open();                
    }
}
SkVideoUpload = new LnVideoUpload();





//WOOCOMMERCE proxy
SKLuna.Woo = (function(){

  var _listToConvert;
  var _defaultPrice;
  var _isBusy = false;
  var _wooCatID = "none";

  //init convert
  var countConverted = 0;
  function initConvert(){
      countConverted = 0;
      var cinfo = new SKLuna.Woo.InfoUI.ConvertInfo();
      cinfo.setOKCallback(function(){
        SKLuna.Woo.InfoUI.dismiss();        
      })
      SKLuna.Woo.InfoUI.showInfo(cinfo);      

      createWooproduct();
  }
  //create woo product
  function createWooproduct(){
      var thumbUI = _listToConvert[countConverted].thumbBoxImageUI;
      var imageID = thumbUI.find('.lunarThumbID').val();
      SKLuna.Woo.InfoUI.realTimeInfo(countConverted+1, _listToConvert.length);

      var payload = {};
      payload['imageID'] = imageID;      
      payload['productTitle'] = thumbUI.find('.productTitle').val();
      payload['productCaption'] = thumbUI.find('.productCaption').val();
      payload['productDescription'] = thumbUI.find('.productDescription').html();
      payload['productFilesize'] = thumbUI.find('.productFilesize').val();
      payload['productWidth'] = thumbUI.find('.productWidth').val();
      payload['productHeight'] = thumbUI.find('.productHeight').val();
      payload['_wooCatID'] = _wooCatID;
      

      
      SKLuna.Woo.AjaxProxy.init(payload, 'convert', function(data){ 
          //implement converted
          //console.log(data) //id, price, exif
          _listToConvert[countConverted].thumbBoxImageUI.find('.isProduct').val("true");
          _listToConvert[countConverted].thumbBoxImageUI.find('.productID').val(data.id);    
          _listToConvert[countConverted].thumbBoxImageUI.find('.productPrice').val(data.price);
          _listToConvert[countConverted].thumbBoxImageUI.find('.productDescription').text(data.content);
          _listToConvert[countConverted].thumbBoxImageUI.find('.productTitle').val(data.title);

          _listToConvert[countConverted].thumbBoxImageUI.find('.featuredThumblogoUI').css('display', 'block');  
          _listToConvert[countConverted].thumbBoxImageUI.find('.thumbOverlayEdit').css('display', 'block');  
                            

          //console.log('count:'+countConverted);
          countConverted++;          
          if(countConverted < _listToConvert.length){
              //continue
              createWooproduct();
          }else{ 
              _isBusy = false;             
              SKLuna.Woo.InfoUI.dismiss();
              var ac = new SKLuna.Woo.InfoUI.AfterConvert()
              ac.setOKCallback(function(){
                SKLuna.Woo.InfoUI.dismiss();        
              })
              SKLuna.Woo.InfoUI.showInfo(ac);               
          }
      }, _defaultPrice);      
  }


  //update product
  function updateWooProduct(updateData, updatedUI){
      var updateInfo = new SKLuna.Woo.InfoUI.UpdateInfo(updateData.title);
      SKLuna.Woo.InfoUI.showInfo(updateInfo);    
      SKLuna.Woo.AjaxProxy.init(updateData, 'update', function(data){          

          updatedUI.find('.productPrice').val(updateData.price);
          updatedUI.find('.productTitle').val(updateData.title);
          updatedUI.find('.productDescription').text(updateData.description);
          _isBusy = false;             
          SKLuna.Woo.InfoUI.dismiss();
          var updateModal = new SKLuna.Woo.InfoUI.AfterUpdate(updateData.title)
          updateModal.setOKCallback(function(){
              SKLuna.Woo.InfoUI.dismiss();
          });
          SKLuna.Woo.InfoUI.showInfo(updateModal);
      });
  }

  var _listToDelete;
  var countToDelete = 0;
  //delete woo product
  function removeWooProduct(){
      countToDelete = 0;
      var removeModal = new SKLuna.Woo.InfoUI.DeleteInfo();
      SKLuna.Woo.InfoUI.showInfo(removeModal);
      removeProduct();
  }
  function removeProduct(){
      SKLuna.Woo.InfoUI.realTimeInfo(countToDelete+1, _listToDelete.length);
      var thumbUI = _listToDelete[countToDelete].thumbUI;
      var productID = thumbUI.find('.productID').val();
      var payload = {};
      payload['productID'] = productID;
      SKLuna.Woo.AjaxProxy.initSilent(payload, 'delete', function(response){
          countToDelete++;
          if(countToDelete < _listToDelete.length){
              //continue
              removeProduct();
          }else{ 
              _isBusy = false;             
              SKLuna.Woo.InfoUI.dismiss();              
              var ad = new SKLuna.Woo.InfoUI.AfterDelete()
              ad.setOKCallback(function(){
                SKLuna.Woo.InfoUI.dismiss();        
              })
              SKLuna.Woo.InfoUI.showInfo(ad); 
          }
      });
  }
  

  return {
      getIsBusy: function(){
        return _isBusy;
      },
      setBusyStatus: function(val){
        _isBusy = val;
      },
      convert: function(list, price, wooCatID){
          _isBusy = true;
          _defaultPrice = price;
          _listToConvert = list;
          _wooCatID = wooCatID;
          initConvert();
      },
      remove: function(list){
          if(_isBusy){
            alert('Currently processing products. Please wait!');
            return;
          }        
          _isBusy = true;
          _listToDelete = list;
          removeWooProduct();
      },
      update: function(updateData, updatedUI){
          if(_isBusy){
            alert('Currently processing products. Please wait!');
            return;
          }
          _isBusy = true;
          updateWooProduct(updateData, updatedUI);
      }
  }
})();
SKLuna.Woo.lunar_action = "lunar_action";
SKLuna.Woo.AjaxProxy = (function(){

    return{
      init: function(payload, route, callback, price){
        jQuery.post(ajaxurl, {action: SKLuna.Woo.lunar_action, payload: payload, route: route, price: price}, function(data) {
            var response = JSON.parse(data)
            if(response.status=="STATUS_FAIL"){
                //implement error
                SKLuna.Woo.InfoUI.dismiss();
                alert(response.message);
                SKLuna.Woo.setBusyStatus(false);
                return;
            }
            callback(response.data);
        });           
      },

      initSilent: function(payload, route, callback){
        jQuery.post(ajaxurl, {action: SKLuna.Woo.lunar_action, payload: payload, route: route}, function(data) {
            var response = JSON.parse(data);
            if(response.status=="STATUS_FAIL"){
                //implement error
                SKLuna.Woo.InfoUI.dismiss();
                alert(response.message);
                SKLuna.Woo.setBusyStatus(false);
                return;
            }            
            callback(response.data);
        }); 
      }

    }
})();




//INFO HELPER
SKLuna.Woo.InfoUI = (function(){
  var html = [
    '<div id="genericInfo"></div>'
  ].join('');

  var infoUI;
  var contentObjUI;
  function show(contentObj){
      infoUI = jQuery(html);
      jQuery('body').append(infoUI);
      contentObjUI = contentObj;
      infoUI.append(contentObj.getUI());
  }

  function dismiss(){
      infoUI.remove();
  }

  function showProgress(val, total){
      if(contentObjUI.infoCall!=undefined){
          contentObjUI.infoCall(val, total);
      }
  }

  return{
    showInfo: function(contentObj){
        show(contentObj);
    },
    dismiss: function(){
        dismiss();
    },
    realTimeInfo: function(val, total){
        showProgress(val, total);
    }
  }
})();


//generic modal
SKLuna.Woo.InfoUI.GenericModal = function(){

    this.modaltitle = "";
    this.modalcontent = "";
    this.isConfirm = false;
    this.isCancel = false;
    function buildHTML(title, content, isConfirm, isCancel){
      
      var bottom = "";
      if(isConfirm||isCancel){
          bottom += '<div class="modalBottomLine"></div>';
          if(isConfirm)
            bottom += '<a class="modalButton modalButtonOK" href="#">OK</a>';
          if(isCancel)
            bottom += '<a class="modalButton modalButtonCancel" href="#">Cancel</a>';          
          bottom += '<div class="modal-clear"></div>';
      }
      var html = [
          '<div class="genericLunarModal">',
              '<div class="lunarModalTitle">'+title+'</div>',
              '<div class="lunarModalContent">'+content+bottom+'</div>',               
          '</div>'
      ].join('');
      return html;      
    }

    this.initEvents = function(){    
      //console.log('init events');
      var _self = this;  
      this.jUI.find('.modalButtonOK').click(function(e){
        e.preventDefault();  
        if(_self.okCall!=undefined)
          _self.okCall(_self);                
      });
      this.jUI.find('.modalButtonCancel').click(function(e){
        e.preventDefault();
        if(_self.cancelCall==undefined){
            SKLuna.Woo.InfoUI.dismiss();
            return;
        }     
        if(_self.cancelCall!=undefined)
          _self.cancelCall(_self);   
      });      
    }
    this.removeEvents = function(){
      //tbd
    }

    this.cancelCall;
    this.setCancelCallback = function(cb){
        this.cancelCall = cb;
    }
    this.okCall;
    this.setOKCallback = function(cb){
        this.okCall = cb;
    }    

    this.jUI;
    this.getUI = function(){      
        if(this.jUI==undefined){
            //console.log('building interface');
            this.jUI = jQuery(buildHTML(this.modaltitle, this.modalcontent, this.isConfirm, this.isCancel));
        }            
        if(this.isConfirm||this.isCancel)
          this.initEvents();
        return this.jUI;
    }
}
SKLuna.Woo.InfoUI.GenericModal.prototype.show = function(){
}
SKLuna.Woo.InfoUI.GenericModal.prototype.dismiss = function(){      
}


//before convert
SKLuna.Woo.InfoUI.BeforeConvert = function(){
    SKLuna.Woo.InfoUI.GenericModal.call(this);

    var wooCatedRaw = jQuery('#wooCategoriesTA').text();
    var wooCategHtml = '';
    if(wooCatedRaw!=''){
        var wooCategObj = JSON.parse(wooCatedRaw);
        var options = '';
        options += ('<option value="none">None</option>');
        for (var i = 0; i < wooCategObj.length; i++) {            
            options += ('<option value="'+wooCategObj[i].cat_ID+'">'+wooCategObj[i].name+'</option>');
        };        
        wooCategHtml = [
          '<div class="modalContentRow">',
            '<p>Choose a WooCommerce category</p>',
            '<select class="wooCatSelectUI">',
              options,
            '</select>',
            '<input style="margin-top: 10px; display: none;" class="pGenericCategory" type="text" value="none" />',        
          '</div>',             
        ].join(''); 
    }

    this.modaltitle = "Settings";
    this.modalcontent = [
      '<div class="modalContentRow">',
        '<div class="modalNoticeInfo"><b>NOTE! </b>Same price will be applied for all products, you can edit the price, title and description for each image later.</div>',
      '</div>',
      '<div class="modalContentRow">',
        '<label><input style="margin-top: 10px;" class="pGenericPrice" type="text" value="3" />Price</label>',        
      '</div>',
      wooCategHtml       
    ].join('');  
    this.isConfirm = true;
    this.isCancel = true; 

    this.listenCategoryChange = function(){
      var ui = this.jUI;
      ui.find('.wooCatSelectUI').change(function() {
          var option = jQuery(this).find('option:selected');
          ui.find('.pGenericCategory').val(option.val());
      });      
    } 
}
SKLuna.Woo.InfoUI.BeforeConvert.prototype = new SKLuna.Woo.InfoUI.GenericModal();
SKLuna.Woo.InfoUI.BeforeConvert.prototype.constructor = SKLuna.Woo.InfoUI.BeforeConvert;



//convert info
SKLuna.Woo.InfoUI.ConvertInfo = function(){
    SKLuna.Woo.InfoUI.GenericModal.call(this);
    this.modaltitle = "Converting images to products";
    this.modalcontent = [
      '<div class="modalContentRow">',
        '<div>Please wait, converting image <span class="infoProgress"></span> of <span class="infoProgressTotal"></span> to products.</div>',
        '<div class="modalPreloader"></div>',
      '</div>',
    ].join('');  

    this.isConfirm = false;
    this.isCancel = false;  
    this.infoCall = function(val, total){
        //console.log(val);
        if(this.jUI!=undefined){
            var infoProgress = this.jUI.find('.infoProgress');
            infoProgress.html(val);
            var infoProgressTotal = this.jUI.find('.infoProgressTotal');
            infoProgressTotal.html(total);
        }
    }    
}
SKLuna.Woo.InfoUI.ConvertInfo.prototype = new SKLuna.Woo.InfoUI.GenericModal();
SKLuna.Woo.InfoUI.ConvertInfo.prototype.constructor = SKLuna.Woo.InfoUI.ConvertInfo;


//delete info
SKLuna.Woo.InfoUI.DeleteInfo = function(){
    SKLuna.Woo.InfoUI.GenericModal.call(this);
    this.modaltitle = "Removing linked WooCommerce products";
    this.modalcontent = [
      '<div class="modalContentRow">',
        '<div>Please wait, removing product <span class="infoProgress"></span> of <span class="infoProgressTotal"></span>.</div>',
        '<div class="modalPreloader"></div>',
      '</div>',
    ].join('');    
}
SKLuna.Woo.InfoUI.DeleteInfo.prototype = new SKLuna.Woo.InfoUI.ConvertInfo();
SKLuna.Woo.InfoUI.DeleteInfo.prototype.constructor = SKLuna.Woo.InfoUI.DeleteInfo;



//update info - product
SKLuna.Woo.InfoUI.UpdateInfo = function(prodTitle){
    SKLuna.Woo.InfoUI.GenericModal.call(this);
    this.modaltitle = "Updating: "+prodTitle;
    this.modalcontent = [
      '<div class="modalContentRow">',
        '<div>Please wait...</div>',
        '<div class="modalPreloader"></div>',
      '</div>',
    ].join('');  

    this.isConfirm = false;
    this.isCancel = false;    
}
SKLuna.Woo.InfoUI.UpdateInfo.prototype = new SKLuna.Woo.InfoUI.GenericModal();
SKLuna.Woo.InfoUI.UpdateInfo.prototype.constructor = SKLuna.Woo.InfoUI.UpdateInfo;



//after convert
SKLuna.Woo.InfoUI.AfterConvert = function(){
    SKLuna.Woo.InfoUI.GenericModal.call(this);
    this.modaltitle = "Done!";
    this.modalcontent = [
      '<div class="modalContentRow">',
        '<p>Images have been converted to WooCommerce products. In order to modify the price, title and description, hover over the thumbs and click Edit. You can also edit each product by going to Admin > Products.</p>',        
      '</div>',
      '<div class="modalContentRow">',
        '<div class="modalNoticeInfo"><b>NOTE! </b>Do not forget to save/update the post.</div>',
      '</div>',      
    ].join('');  
    this.isConfirm = true;
    this.isCancel = false;  
}
SKLuna.Woo.InfoUI.AfterConvert.prototype = new SKLuna.Woo.InfoUI.GenericModal();
SKLuna.Woo.InfoUI.AfterConvert.prototype.constructor = SKLuna.Woo.InfoUI.AfterConvert;

//after delete
SKLuna.Woo.InfoUI.AfterDelete = function(){
    SKLuna.Woo.InfoUI.GenericModal.call(this);
    this.modaltitle = "Done!";
    this.modalcontent = [
      '<div class="modalContentRow">',
        '<p>Linked WooCommerce products have been removed.</p>',        
      '</div>',
      '<div class="modalContentRow">',
        '<div class="modalNoticeInfo"><b>NOTE! </b>Do not forget to save/update the post.</div>',
      '</div>',      
    ].join('');  
    this.isConfirm = true;
    this.isCancel = false;  
}
SKLuna.Woo.InfoUI.AfterDelete.prototype = new SKLuna.Woo.InfoUI.GenericModal();
SKLuna.Woo.InfoUI.AfterDelete.prototype.constructor = SKLuna.Woo.InfoUI.AfterDelete;


//after update
SKLuna.Woo.InfoUI.AfterUpdate = function(prductTitle){
    SKLuna.Woo.InfoUI.GenericModal.call(this);
    this.modaltitle = "Done!";
    this.modalcontent = [
      '<div class="modalContentRow">',
        '<p>'+prductTitle+' has been successfully updated.</p>',        
      '</div>'      
    ].join('');  
    this.isConfirm = true;
    this.isCancel = false;  
}
SKLuna.Woo.InfoUI.AfterUpdate.prototype = new SKLuna.Woo.InfoUI.GenericModal();
SKLuna.Woo.InfoUI.AfterUpdate.prototype.constructor = SKLuna.Woo.InfoUI.AfterUpdate;



SKLuna.Woo.InfoUI.LunarEditor = (function(){
    var editorHTML;
    function retriveEditor(){
        if(editorHTML==undefined){
            editorHTML = jQuery('#lunar_editor_wrap').html();
            jQuery('#lunar_editor_wrap').remove();
        }
        return editorHTML;
    }
    return{
        getEditorHTML: function(){
            return retriveEditor();
        }
    }
})();

//lunar update window - special
SKLuna.Woo.InfoUI.LunarUpdateWindow = (function(){

    var modalUI;
    function init(){
        if(modalUI==undefined){
            modalUI = jQuery('#lunnar_update_window');
            var _self = this;
            modalUI.find('.modalButtonOK').click(function(e){
              e.preventDefault();
              hide();
              if(okF)
                okF({productID: productUI.find('.productID').val(), title: modalUI.find('.prodTitle').val(), price: modalUI.find('.pGenericPrice').val(), description: tinymce.get('lunar_editor').getContent()});                            
            });
            modalUI.find('.modalButtonCancel').click(function(e){
              e.preventDefault();
              if(cancelF)
                cancelF();
              hide();
            });            
            //modalUI.remove();
        }
    }
    var productUI;
    function initProduct(product){
        productUI = product;
        if(modalUI)
          modalUI.css('display', 'block');
        modalUI.find('.prodTitle').val(productUI.find('.productTitle').val());
        modalUI.find('.pGenericPrice').val(productUI.find('.productPrice').val()); 
        
        if(tinymce){
            tinymce.get('lunar_editor').setContent(productUI.find('.productDescription').text());
        }else{
          alert('Something went wrong! could not find Tinymace.');
        }

        //productUI.find('.productDescription').html()               
    }
    var okF, cancelF;    
    function initEventsListeners(ok, cancel){
        okF = ok;
        cancelF = cancel;
    }

    function hide(){
        if(modalUI)
          modalUI.css('display', 'none');
    }

    return{
        init: function(){          
            init();
        },
        show: function(productUI){
            initProduct(productUI);
        },
        dismiss: function(){

        },
        setCallbacks: function(okF, cancelF){
            initEventsListeners(okF, cancelF);
        }
    }
})();







