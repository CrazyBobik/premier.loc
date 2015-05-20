$(document).ready(function(){
    function openLeftMenu(id){

        var menuControl = $('.menu-arr[rel="'+id+'"]');
        menuControl.removeClass('hide-control-open');
        menuControl.addClass('hide-control-close');

        menuControl.closest('.menu-list').find('.sub-menu').show('600');
        return menuControl;
    }

    function closeLeftMenu(id){

        var menuControl = $('.menu-arr[rel="'+id+'"]');
        menuControl.removeClass('hide-control-close');
        menuControl.addClass('hide-control-open');

        menuControl.closest('.menu-list').find('.sub-menu').hide('600');
        return menuControl;


    }

    $('.main-menu').on('click','.hide-control-open', function(){

        var id = $(this).attr('rel');
        openLeftMenu(id);
        addCoockieArray('leftmenuopen', id);

    });

    $('.main-menu').on('click','.hide-control-close', function(){
        var id = $(this).attr('rel');

        closeLeftMenu(id);
        removeCoockieArray('leftmenuopen', id);
        if(id==getCookie('leftmenuopenlink')){
            removeCookie('leftmenuopenlink');
        }

    });

    $(function() {

        // openLeftMenu
        var openMenus = getCoockieArray('leftmenuopen');

        openMenus.forEach(function (entry) {
            openLeftMenu(entry);
        });

        openLeftMenu(getCookie('leftmenuopenlink'));

        $('body').on('click', '.hidden-link,.jlinkn', function () {
            window.open($(this).data('link'));
            return false;
        });

        $('body').on('click', '.hidden-link-this', function () {
            window.location.href = $(this).data('link');
            return false;
        });

        $('body').on('click', '.hidden-link-toplink,.hidden-link-blink,.jlink', function () {
            window.location.href = $(this).data('link');
            return false;
        });
    });
});