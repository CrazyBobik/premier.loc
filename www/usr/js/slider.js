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