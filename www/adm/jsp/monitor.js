$(document).ready(function(){
    (function updateMonitor(){
        setTimeout(function(){
            $.getJSON('/monitor/', function(data) {
                var items = '';

                $.each(data, function(key, val) {
                    items += '<li id="' + key + '">' + val + '</li>';
                });

                $('.b-logs ul').html(items);
                
                $('.b-logs').animate({
                    opacity: 0.5
                }, 3000, function(){
                    $('.b-logs').animate({
                        background: 1.0
                    }, 3000)
                });
                
                updateMonitor();
            });
        }, 5000);
    })();
});