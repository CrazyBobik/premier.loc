$(function(){
    $('.ajax-form').on('submit', function()
    {

        var form = $(this);

        var hideForm = function()
        {
            showHide(form, 'hide');
        }

        var cleanForm = function()
        {
            form.trigger( 'reset' );
        }

        $(form).ajaxSubmit(
            {
                semantic: true,
                dataType: 'json',
                success: function(data)
                {

                    if(data.msgid !== undefined ){

                        var msgId = data.msgid;

                    }else{

                        var msgId = '#flash-msg-note';

                    }

                    if (data.error == true)
                    {
                        if (data.errormsg !== undefined){

                            // вывод набора ошибок
                            showValidateErrors(data.errormsg, msgId);

                        }else{

                            var msgclass = 'validation';

                            if(data.msgclass !== undefined ){

                                var msgclass = data.msgclass;
                            }


                            // вывод простого сообщения если нет набора ошибок
                            nNoteShow(msgId, msgclass , data.msg);

                        }

                        if (typeof(data.callback) == "string"){
                            eval(data.callback);
                            callback();
                        }

                    }
                    else
                    {
                        if (data.clean !== undefined){
                            if(data.clean == true)
                                cleanForm();
                        }else {
                            hideForm();
                        }

                        var msgclass = 'success';

                        if(data.msgclass !== undefined ){

                            var msgclass = data.msgclass;
                        }

                        nNoteShow(msgId, msgclass, data.msg);

                        if (typeof(data.callback) == "string"){
                            eval(data.callback);
                            callback();
                        }

                        if (data.redirect !== undefined){
                            setTimeout(function(){location.href = data.redirect}, 2000);
                        }
                    }
                }
            }, "json");
        return false;
    });

    $( ".product-title" ).hover(
	  function() {
		$( this ).find('span').css('color','orange');
		
	  }, function() {
		$( this ).find('.product-title-longtitle').css('color','#00BED2');
		$( this ).find('.product-title-name').css('color','black');
	  }
	);
	
	$( ".top-menu-title" ).hover(
	  function() {
		$( this ).find('span').css('color','orange');
		
	  }, function() {
		$( this ).find('.top-menu-title-longtitle').css('color','#00BED2');
		$( this ).find('.top-menu-title-name').css('color','black');
	  }
	);
	
	if($('#silder-cont').length>0){   
			
			$('#silder-cont').bjqs({
					height : 550,
					width  : 750,
					responsive : true,
					animspeed : 4000,
					animtype : 'slide', // accepts 'fade' or 'slide'
					showcontrols  : false,
					centercontrols : false,   
					showmarkers  : false, 
			});
					
	};
	var other;
	
	$('#otherlangs').on('change',	
			function () {
				$(this).find('option:first-child').show();
				
				window.location.href = $(this).val();
				
			}
	);
	
	$('#otherlangs').hover(
	
			 function () {
			    //other = $(this).find('option:first-child');
			 
				$(this).find('option:first-child').hide();
			 
				var element = $("#otherlangs")[0], worked = false;
				if (document.createEvent) { // all browsers
					var e = document.createEvent("MouseEvents");
					e.initMouseEvent("mousedown", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
					worked = element.dispatchEvent(e);
				} else if (element.fireEvent) { // ie
					worked = element.fireEvent("onmousedown");
        }
			//   $('#otherlangs').trigger('mousedown')
			   
			 }, 
			 function () {
			 
			 
			   
			 }
	);
    // слайдер в шапке на главной
    if($('#top-slider').length>0){
        $.ajax({
            url:'/ajax/loadslider/topslider',
            dataType: "html",
            success: function(data){
                $('#top-slider').html(data);
                setTimeout(function(){

                    var slideWidth=900;
                    var sliderTimer;

                    $(function(){

                        $('.slidewrapper').width($('.slidewrapper').children().size()*slideWidth);
                        sliderTimer = setInterval(nextSlide, 3000);

                        $('.slider').hover(function(){
                            clearInterval(sliderTimer);
                        },function(){
                            sliderTimer = setInterval(nextSlide, 3000);
                        });

                        $('.next_slide').on('click', function(){
                            nextSlide();
                        });

                        $('.prev_slide').on('click', function(){
                            prevSlide();
                        });
                    });

                    var nextSlide = function(){
                        var currentSlide=parseInt($('.slidewrapper').data('current'));
                        currentSlide++;
                        if(currentSlide>=$('.slidewrapper').children().size()){
                            currentSlide=0;
                        }
                        $('.slidewrapper').animate({left: -currentSlide*slideWidth},900).data('current',currentSlide);
                    };
                    var prevSlide = function (){
                        var currentSlide=parseInt($('.slidewrapper').data('current'));
                        currentSlide--;
                        if(currentSlide<0){
                            currentSlide=$('.slidewrapper').children().size()-1;
                        }
                        $('.slidewrapper').animate({left: -currentSlide*slideWidth},900).data('current',currentSlide);
                    };
                },500);
            }
        });
    }


});