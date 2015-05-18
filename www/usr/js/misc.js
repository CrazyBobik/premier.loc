	function showHide(element, type) {
	   var el=$(element);
		if (type == 'show') {
			el.animate({
				opacity: 'show',
				height: 'show'
			}, 'slow');
		} else if (type == 'hide') {
			el.animate({
				opacity: 'hide',
				height: 'hide'
			}, 'slow');
		}
	}

   // Показывет флеш сообщение и убирает его через 20 секунд
   function nNoteShow(nNId, nNClass, msg) {
   
		var nNote = $(nNId);
		nNote.hide();
		nNote.removeClass('error');
		nNote.removeClass('warning');
		nNote.removeClass('info');
		nNote.removeClass('success');
		nNote.removeClass('validation');
		nNote.addClass(nNClass);
		nNote.empty();
		nNote.html(msg);
		nNote.fadeIn();
		if (typeof nNoteHideTimeout != "undefined") {
			clearTimeout(nNoteHideTimeout);
		}
		nNoteHideTimeout = setTimeout(function(n) {
			nNote.fadeOut("slow");
		}, 20000);
		
	}

   	
	 // Выводит ошибки валидации 
	function showValidateErrors(errors, noteId){ 
	    //alert(noteId);
		var items = [];
		
		$.each(errors, function(id, msg){
		
			items.push('<strong>"' + msg.label + '"</strong> - ' + msg.error)
		
		});
		
		if( noteId !== undefined )

			nNoteShow(noteId, 'validation', items.join('<br />'));

		else 
							
			nNoteShow('#flash-msg-note', 'validation', items.join('<br />'));						
		
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

// плагин для подсветки поиска
jQuery.fn.highlight = function(pat,wrap) {
 function innerHighlight(node, pat) {
  var skip = 0;
  if (node.nodeType == 3) {
   var pos = node.data.toUpperCase().indexOf(pat);
   if (pos >= 0) {
    var spannode = document.createElement(wrap);
    spannode.className = 'highlight';
    var middlebit = node.splitText(pos);
    var endbit = middlebit.splitText(pat.length);
    var middleclone = middlebit.cloneNode(true);
    spannode.appendChild(middleclone);
    middlebit.parentNode.replaceChild(spannode, middlebit);
    skip = 1;
   }
  }
  else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
   for (var i = 0; i < node.childNodes.length; ++i) {
    i += innerHighlight(node.childNodes[i], pat);
   }
  }
  return skip;
 }
 return this.each(function() {
  innerHighlight(this, pat.toUpperCase());
 });
};

jQuery.fn.removeHighlight = function() {
 return this.find(".highlight").each(function() {
  this.parentNode.firstChild.nodeName;
  with (this.parentNode) {
   replaceChild(this.firstChild, this);
   normalize();
  }
 }).end();
};

$(function(){

	$('body').on('click','.jlink',function(){ window.location.href = $(this).data('link');
																					  return false;
																					});
});

function setCookie( name, value, expires, path, domain, secure ){ 
    var today = new Date(); 
    today.setTime( today.getTime() ); 
    
    if ( expires !== undefined ) 
    { 
     var expires = expires * 1000 * 60 * 60 * 24; 
    } 
    else{
     var expires = 1000 * 60 * 60 * 24;     
    }
    
    var expiresDate = new Date( today.getTime() + (expires) ); 
    document.cookie = name + "=" +escape( value ) + 
        ( ( expires ) ? ";expires=" + expiresDate.toGMTString() : "" ) +  
        ( ( path ) ? ";path=" + path : "" ) +  
        ( ( domain ) ? ";domain=" + domain : "" ) + 
        ( ( secure ) ? ";secure" : "" ); 
} 
 
function getCookie( name ) { 
    var start = document.cookie.indexOf( name + "=" ); 
    var len = start + name.length + 1; 
    if ( ( !start ) && 
    ( name != document.cookie.substring( 0, name.length ) ) ) 
    {return null;} 
    if ( start == -1 ) return null; 
    var end = document.cookie.indexOf( ";", len ); 
    if ( end == -1 ) end = document.cookie.length; 
    return unescape( document.cookie.substring( len, end ) ); 
}

function getCoockieArray(name){
    var arrStr = ''+getCookie(name);
    var arr = arrStr.split(",");
    var arrRes = [];
    
    arr.forEach(function(entry){
        if( entry!='null' && entry!='' ){
                arrRes.push(entry);
        }
    });
    
    return  arrRes;
} 

function removeValue(arr, value){
    var arrRes = [];

    arr.forEach(function(entry){
        
        if(value!=entry){
            arrRes.push(entry);
        }
        
    });

    return arrRes;
}

function addCoockieArray(name, id){
    var arrRes = getCoockieArray(name);
   
    arrRes.push(id);
    
    setCookie(name, arrRes.join(','), 1000, '/');
    
    return arrRes;
}

function removeCoockieArray(name, id){
 
    var arr = getCoockieArray(name);
    var arrRes = removeValue(arr, id);
    
    setCookie(name, arrRes.join(','), 1000, '/');
    
    return arrRes;
}
