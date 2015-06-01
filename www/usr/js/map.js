function initialize() {
    //получаем наш div куда будем карту добавлять
    var mapCanvas = document.getElementById('map');
    // задаем параметры карты
    var mapOptions = {
        //Это центр куда спозиционируется наша карта при загрузке
        center: new google.maps.LatLng(55, 15),
        //увеличение под которым будет карта, от 0 до 18
        // 0 - минимальное увеличение - карта мира
        // 18 - максимально детальный масштаб
        zoom: 3,
        //Тип карты - обычная дорожная карта
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    //Инициализируем карту
    map = new google.maps.Map(mapCanvas, mapOptions);

    //  var triangles = {};
    // var iterator = 0;

    $.each(mapCouuntries,function(k, v){

        var i = 1;
        triangles[k] = [];

        while (typeof v['latlng_'+i] != 'undefined') {

            var triangleCoords = [];

            $.each(v['latlng_'+i], function (k, v) {

                triangleCoords.push(new google.maps.LatLng(k, v));

            });

            triangles[k].push(new google.maps.Polygon({
                paths: triangleCoords,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: v['color'],
                fillOpacity: 0.1
            }));

            i++;
        }
        //triangles.setMap(map);

        //google.maps.event.addListener(triangles, 'click');
    });
}

$(function() {

    //Когда документ загружен полностью - запускаем инициализацию карты.
    google.maps.event.addDomListener(window, 'load', initialize);
});

$('.country-item').on({
    mouseenter: function () {
        triangles[$(this).data("country")].forEach(function(v){
            v.setMap(map);
        });
    },
    mouseleave: function () {
        var isActive = true;

        var classList =$(this).attr('class').split(/\s+/);

        classList.forEach(function(item){
            if (item === 'active') {
                isActive = false;
            }
        });

        if (isActive) {
            triangles[$(this).data("country")].forEach(function (v) {
                v.setMap(null);
            });
        }
    }
});

$('.country-items').on('click', '.country-item', function(){
    $('.country-item').removeClass('active');
    $(this).addClass('active');

    $.each(triangles, function (k, v) {
        v.forEach(function(v1){
            v1.setMap(null);
        });
    });

    triangles[$(this).data("country")].forEach(function(v){
        v.setMap(map);
    });

    $.ajax({
        url: '/ajax/loadmap/map',
        data: {id:$(this).data('id'),
            idobj:$(this).data('idobj')
        },
        dataType: "html",
        success: function (data) {
            $('.city-items').html(data);
        }
    });
    $.ajax({
        url: '/ajax/loadreklamformap/mapReklam',
        data: {idobj:$(this).data('idobj')},
        dataType: "html",
        success: function (data) {
            $('.recommended').html(data);
        }
    });

    var c = mapCouuntries[$(this).data("country")];

    map.setCenter(new google.maps.LatLng(c['lat'], c['len']));
    map.setZoom(c['zoom']);
});

$('.city-items').on('click', '.region-item', function(){
    $.ajax({
        url: '/ajax/loadmap/map',
        data: {id:$(this).data('id')},
        dataType: "html",
        success: function (data) {
            $('.city-items').html(data);
        }

    });
});