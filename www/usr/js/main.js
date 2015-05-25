$(function(){
    $('.ajax-form').on('submit', function()
    {

        var form = $(this);

        var hideForm = function(){
            showHide(form, 'hide');
        };

        var cleanForm = function(){
            form.trigger( 'reset' );
        };

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

    $( '#mi-slider' ).catslider();
    $( '#mi-slider2' ).catslider();

    $("#slider-doc").slides({
        responsive: true
    });

    $(".show-form").click(function(){$(".form").slideToggle(1000)});

    function selectLoad(a, selId, params, checkOpt) {
        var items = [];
        $(selId).empty();
        if ($(a).find(':selected').attr('treeid')) {
            $.post('/ajax/forms/loadChilds', 'field=' + params + '&treeid=' + $(a).find(':selected').attr('treeid'), function (data) {
                items.push('<option value="">Все</option>');
                $.each(data, function (k, v) {
                    var cheked = '';
                    if (v.value == checkOpt) {
                        cheked = 'selected=\"selected\"';
                    }
                    items.push('<option treeid=\"' + k + '\" value=\"' + v.value + '\" ' + cheked + ' > ' + v.title + '</option>');
                });
                $(selId).html(items.join(''));
                $(selId).removeAttr('disabled');
            }, 'json');
        } else {
            items.push('<option value="">Все</option>');
            $(selId).html(items.join(''));
            $(selId).attr('disabled','disabled');
            if (!$('#country').find(':selected').attr('treeid')) {
                $('#city').html(items.join('')).attr('disabled','disabled');
            }
        }
    }


    $('#country').on('change',function(){selectLoad(this,'#region','id','')});
    $('#region').on('change',function(){selectLoad(this,'#city','id','')});

});