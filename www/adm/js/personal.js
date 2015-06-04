function changeType(typeId){

    $('.object-types-button').removeClass('object-types-button-selected');

    $('.object-types-button[data-type="'+typeId+'"]').addClass('object-types-button-selected');



    $('#objects_type option[value="'+typeId+'"]').attr('selected',true);


    $('.srchbutton').click();

}

function editObject(objectId){
    $.get('/admin/objects/edit','objectid='+objectId,function(data){
        flyboxOpen(900+44,700, 'Управление', '<div style="padding:0 10px;width:900px;height:700px;overflow-x:auto">'+data+'</div>');
    },'html').done(function(){
        setTimeout(setObjectForm,500);
    });
}


function setObjectForm(){
    /*

     $(".wysiwyg-redactor").redactor({
     imageUpload: "/admin/api/uploadImage",
     autoformat: true,
     cleanUp: false,
     convertDivs: false,
     removeClasses: false,
     removeStyles: false,
     convertLinks: false,
     buttons: ["html", "|", "formatting", "|", "bold", "italic" , "deleted","|","norderedlist", "orderedlist", "outdent", "indent", "|","image","video","table", "link", "|","fontcolor", "backcolor", "|","alignleft", "aligncenter", "alignright", "justify", "|","horizontalrule", "fullscreen"]
     });

     */

    //обнуление эветнов
    $('.update-image').die();
    $('.add-image').on();
    $('.remove-image').die();
    $('.setplan').die();


    // установить картинку планом
    $('.setplan').live('change', function()
    {


        var objectId =  $(this).attr('data-object-id');
        var imgId =  $(this).attr('data-image-id');
        var setDo = ($(this).attr("checked")=="checked" ? 'add':'remove');



        $.post('/admin/objects/setplan',{'objectId':objectId,
            'imgId':imgId,
            'setDo':setDo
        },function(data){


        },'json');



        return false;
    });




    // обнавление картинки
    $('.update-image').live('click', function()
    {


        var form = $(this).closest('form');
        var upimg = $(form).find('.rounded');

        var updateObjectImg = function ()
        {

            $(upimg).attr("src", $(upimg).attr('src')+"?"+Math.random());

        };

        var cleanForm = function()
        {
            form.trigger( 'reset' );
        };

        $(form).ajaxSubmit(
            {
                semantic: true,
                dataType: 'html',
                success: function(data)
                {

                    if(data.indexOf('ERROR123')==-1){

                        updateObjectImg();
                        cleanForm();

                    }else{

                        jAlert(data.replace('ERROR123:',''));

                    }

                }
            }, "html");

        return false;
    });

    // добавление картинки
    $('.add-image').on('submit', function()
    {

        var form = $(this);

        $(form).ajaxSubmit(
            {
                semantic: true,
                dataType: 'html',
                success: function(data)
                {

                    if(data.indexOf('ERROR123')==-1){

                        $('#object-images-conteiner').append(data);
                        $('.object_images').trigger( 'reset' );

                    }else{

                        jAlert(data.replace('ERROR123:',''));

                    }

                }
            }, "html");

        return false;
    });

    // Удаление картинки
    $('.remove-image').live('click', function()
    {

        if (window.confirm('Вы уверены что хотите удалить картинку ?')){

            var image = $(this).attr('data-image');
            var id = $(this).attr('data-id');

            $.post('/admin/objects/imageremove/', {'image':image,
                'id':id,
            }, function(data){
                if(data.error == true){

                    jAlert(data.msg, 'Ошибка');
                }else{

                    $('#img_'+data.image).fadeOut('slow').remove();

                }
            },'json');
        }

        return false;
    });

    $("#description").ckeditor({

        toolbar: "Standard",
        removePlugins : "resize,about,save",
        filebrowserBrowseUrl : "/adm/plugins/ckfinder/ckfinder.html",
        filebrowserImageBrowseUrl : "/adm/plugins/ckfinder/ckfinder.html?Type=Images",
        filebrowserFlashBrowseUrl : "/adm/plugins/ckfinder/ckfinder.html?Type=Flash",
        filebrowserUploadUrl : "/adm/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files",
        filebrowserImageUploadUrl : "/adm/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images",
        filebrowserFlashUploadUrl : "/adm/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash",
        editorConfig : function( config )
        {
            config.protectedSource.push(/<(style)[^>]*>.*<\/style>/ig);
            return config;

        }
    });


    /*
     CKEDITOR.replace( 'wysiwyg-redactor' ,{
     toolbar: 'Standard',
     filebrowserUploadUrl: '/admin/api/uploadImageCk'
     });*/
}