	function showHide(element, type) {
		if (type == 'show') {
			$(element).animate({
				opacity: 'show',
				height: 'show'
			}, 'slow');
		} else {
			$(element).animate({
				opacity: 'hide',
				height: 'hide'
			}, 'slow');
		}
	}

     // Показывет флеш сообщение и убирает его через 20 секунд
   function nNoteShow(nNId, nNClass, msg) {
		var nNote = $(nNId);
		
		nNote.hide();
		nNote.removeClass('nFailure');
		nNote.removeClass('nWarning');
		nNote.removeClass('nInformation');
		nNote.removeClass('nSuccess');
		nNote.addClass(nNClass);
    	nNote.empty();
		nNote.html('<p>'+msg+'</p>');
		nNote.fadeIn();
		if (typeof nNoteHideTimeout != "undefined") {
			clearTimeout(nNoteHideTimeout);
		}
		var nNoteHideTimeout = setTimeout(function(n) {
			nNote.fadeOut("slow");
		}, 20000);
	}
    
    
    // Выводит ошибки валидации 
	function showValidateErrors(errors, noteId) {
	
		var items = [];
		$.each(errors, function(id, msg) {
			items.push('<strong>Ошибка:</strong><em>"' + msg.label + '"</em> - ' + msg.error)
		});
		
		if(noteId !== undefined )
       
	    nNoteShow(noteId, 'nFailure', items.join('<br />'));

		else 
							
		nNoteShow('#flash-msg-nNote', 'nFailure', items.join('<br />'));						
		
	}
    
var flyboxOpened = false;
var flyboxReloadPage = false;
function flyboxOpen(width, height, title, html)
{
    var pageY = window.pageYOffset || document.documentElement.scrollTop;
    if( flyboxOpened ) { return false; }
    flyboxOpened = true;
    var outer = document.getElementById("flybox_container");
    var box = document.getElementById("flybox_box");
    var ttl = document.getElementById("flybox_title");
    var cnt = document.getElementById("flybox_main");
    if( !outer || !box || !cnt ) { return false; }
    if( ! width ) { width = 600; }
    if( ! height ) { height = 500; }
    if( ! title ) { title = ""; }
    if( ! html ) { html = ""; }
    var page_size = get_screen_preview_size();
    box.style.width = width + "px";
    box.style.height = height + "px";
    var left = Math.round((page_size[0] - width) / 2);
    var top = Math.round((page_size[1] - height) / 2);
    left = Math.max(left, 10);
    top = Math.max(top, 10)+pageY;
    box.style.left = left + "px";
    box.style.top = top + "px";
    ttl.innerHTML = title;
    setTimeout( function() { outer.style.display = "block"; }, 1 );
    setTimeout( function() { cnt.innerHTML = html; }, 1 );
    //setTimeout( function() { if(msgbox_close) { msgbox_close(); } }, 50 );
    //setTimeout( function() { if(postform_topmsg_close) { postform_topmsg_close(); } }, 50 );
} 

function flyboxClose(reload)
{
    if (flyboxReloadPage || reload){
      window.location.reload(true);
	}
	flyboxOpened	= false;
	document.getElementById("flybox_container").style.display	= "none";
	setTimeout( function(){ document.getElementById("flybox_main").innerHTML = ""; }, 1 );
}

function get_screen_preview_size()
{
	var w=0, h=0;
	if( typeof( window.innerWidth ) == 'number' ) {
		w	= window.innerWidth;
		h	= window.innerHeight;
	}
	else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		w	= document.documentElement.clientWidth;
		h	= document.documentElement.clientHeight;
	}
	else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		w	= document.body.clientWidth;
		h	= document.body.clientHeight;
	}
	return [w, h];
}    




//Плагин мультизагрузки файлов
(function($)
{
    $.fn.multifile = function()
    {
        
        return $(this).each(function()
        {
            var fielsCont = $(this);
            
            fielsCont.append('<div class="file_field_add"><a class="btnIconLeft mr10 mt5 mb10" title="" href="javascript:;"><img class="icon" title="Добавить файл" src="/adm/img/icons/dark/add.png"><span>Добавить</span></a></div>');
            
             $('.file_field_add').die('click').live('click',function(){
			      var MyCont=$(this).parent();
			      var count = MyCont.find('input[type="file"]').length;
                
                 $(this).before('<div  style="margin:15px 0"  id="'+MyCont.attr('id')+'_a_'+count+'" ><input type="text" style="width:150px;margin-right:5px" name="'+MyCont.attr('data-filename')+'_t_'+count+'" id="'+MyCont.attr('id')+'_t_'+count+'"  /><input style="width:220px" type="file" name="'+MyCont.attr('data-filename')+'_f_'+count+'" id="'+MyCont.attr('id')+'_f_'+count+'"><a data-fileid="'+count+'" class="file_field_delete" href="javascript:void(0);" title="Убрать лишний"></a></div>');   
             })
             
             $('.file_field_delete').die('click').live('click',function(){
              
                 $('#'+fielsCont.attr('id')+'_f_'+$(this).attr('data-fileid')).remove();
                 $('#'+fielsCont.attr('id')+'_t_'+$(this).attr('data-fileid')).remove();
                 $('#'+fielsCont.attr('id')+'_a_'+$(this).attr('data-fileid')).remove();
               
                 $(this).remove();
             
             })
        })
    }
})(jQuery);    


function disableTree(){
     $('#tree-disable-div').css('z-index','100').show();
   
}


function enableTree(){
   
     $('#tree-disable-div').css('z-index','0').hide();
   
}
// 
var findClosest = function()
            {
               var nNote = form.siblings(".nNote");
                
               if(nNote.length > 0 ){
                
                  return nNote;
                
               }else{
                
                  return form.closest(".nNote");
               }
            }



function reloadFlyForm(link){
         
      $.get(link,function(data){
         
         $('#flybox_main').html('<div style="padding:0 10px;width:900px;height:700px;overflow-x:auto">'+data+'</div>');
         
      },'html')
      
   }

