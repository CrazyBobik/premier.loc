$(document).ready(function(){
    function removeCookie(name){
        document.cookie=name+"="+"; expires=Thu, 01 Jan 1970 00:00:01 GMT";
    }

    function addHTMLCode(){
        var item = getCoockieArray('favoritearray');
        var itemcount = item.length;
        $('#count-fav').html(itemcount);

        if (itemcount > 0) {
            $('.favorites-img').addClass('favorites-img-active');
        } else {
            $('.favorites-img').removeClass('favorites-img-active');
        }
    }

    function addClazz(id){
        var item = $('.favorit-btn[data-id="'+id+'"]');

        item.removeClass('favorit-btn-remove');
        item.addClass('favorit-btn-add');
        item.html('В избраное');

        return item;
    }

    function removeClazz(id){
        var item = $('.favorit-btn[data-id="'+id+'"]');

        item.removeClass('favorit-btn-add');
        item.addClass('favorit-btn-remove');
        item.html('Убрать из избраного');

        return item;
    }

    $('.obj-list').on('click', '.favorit-btn-add', function(){
        var id = $(this).attr('data-id');
        addCoockieArray('favoritearray', id);

        removeClazz(id);

        addHTMLCode();
    });

    $('.obj-list').on('click', '.favorit-btn-remove', function(){
        var id = $(this).attr('data-id');
        removeCoockieArray('favoritearray', id);

        addClazz(id);

        addHTMLCode();
    });

    $('.clear-fav').on('click', function(){
        if (confirm('Вы действительно хотите очистить избранное')){
            removeCookie('favoritearray');
            addHTMLCode();
            location.reload();
        }
    });

    $(function(){
        addHTMLCode();

        var items = getCoockieArray('favoritearray');

        items.forEach(function (entry) {
            removeClazz(entry);
        });

    })
});