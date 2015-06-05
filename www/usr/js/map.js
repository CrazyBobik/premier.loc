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


}

$(function(){

    $.get('/poligons.php', function(data){
        mapCouuntries = data;
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


    },'json');
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
    selectCountry(this);
});

$('.city-items').on('click', '.region-item', function(){
    selectRegion(this);
});

$('.reset').on('click', function(){
    resetMap();
});

$('.city-items').on('click', '.city-item', function(){
    selectCity(this);
});

function selectCountry(country){
    $('.country-item').removeClass('active');
    $(country).addClass('active');

    $.each(triangles, function (k, v) {
        v.forEach(function(v1){
            v1.setMap(null);
        });
    });

    triangles[$(country).data("country")].forEach(function(v){
        v.setMap(map);
    });

    $.ajax({
        url: '/ajax/loadmap/map',
        data: {id:$(country).data('id'),
            idobj:$(country).data('idobj')
        },
        dataType: "html",
        success: function (data) {
            $('.city-items').html(data);
        }
    });
    $.ajax({
        url: '/ajax/loadreklamformap/mapReklam',
        data: {idobj:$(country).data('idobj')},
        dataType: "html",
        success: function (data) {
            $('.recommended').html(data);
        }
    });

    var c = mapCouuntries[$(country).data("country")];

    map.setCenter(new google.maps.LatLng(c['lat'], c['len']));
    map.setZoom(c['zoom']);
}

function selectRegion(region){
    $.ajax({
        url: '/ajax/loadmap/map',
        data: {id:$(region).data('id')},
        dataType: "html",
        success: function (data) {
            $('.city-items').html(data);
        }

    });

    var c = mapCouuntries[$(region).data("country")];

    map.setCenter(new google.maps.LatLng(c['lat'], c['len']));
    map.setZoom(c['zoom']);
}

function selectCity(city){
    $.each(triangles, function (k, v) {
        v.forEach(function(v1){
            v1.setMap(null);
        });
    });

    var c = mapCouuntries[$(city).data("country")];

    map.setCenter(new google.maps.LatLng(c['lat'], c['len']));
    map.setZoom(c['zoom']);
}

function resetMap(){
    $.each(triangles, function (k, v) {
        v.forEach(function(v1){
            v1.setMap(null);
        });
    });

    $('.country-item').removeClass('active');
    $('.city-items').html('');
    $('.recommended').html('');

    map.setCenter(new google.maps.LatLng(55, 15));
    map.setZoom(3);
}