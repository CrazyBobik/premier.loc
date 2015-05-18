$(function() {
 	   $('#commetns-table-wrapper').ajaxLeaf(
                  { 
                    loadUrl:'/admin/comments/load/', 
                    srchButtonInd: '.srchbutton',
                    itemsContainer:'#comments-forms', 
                    "collectInfo":function(wrap) {
                                var addInfo ='&comments-status='+wrap.find('#comments-status').val();
                                    addInfo+='&date-start='+wrap.find('#date-start').val();
                                    addInfo+='&date-stop='+wrap.find('#date-stop').val();
                                return addInfo;
        				},
                      dropFiltres:function(wrap) {
                               wrap.find('#date-start').val('');
                               wrap.find('#date-stop').val('');
                    	},   
                    "renderItems": function(items) {
                				    var itemsHtml = [];
                			    	$.each(items, function(id, comment) {
                					itemsHtml.push('<tr id="comment-row-' + comment.id + '"><td>' + comment.date + '</td><td>' + comment.name + '</td><td>' + comment.ip + '</td><td>' + comment.content + '</td><td class="com-status" data-uid="' + comment.id + '"><span class="trval">' + comment.status + '</span><img alt="Изменить" title="Изменить" class="edit" src="/adm/img/formbuilder/edit.png"></td><td><img src="/adm/img/formbuilder/edit.png" class="table-btn" onclick="editComment(\'' + comment.id  + '\')" title="Редактировать" alt="Редактировать"/><img src="/adm/img/formbuilder/delete.png" class="table-btn" onclick="removeComment(\'' + comment.id  + '\',\'' + comment.name + '\')" title="Удалить" alt="Удалить" /></td></tr>');
                			    	});
                             
                			    	return itemsHtml;
			         }
                  }
      ).bind('loaded', function(){
           $(".com-status").tableCellEdit({
                    saveUrl: '/admin/comments/changestatus/id/',
                    selectVariants: {'опубликован':'опубликован','ожидает публикации':'ожидает публикации'},
                    valueContInd: '.trval',
                    dataAttr:'data-uid'
                });
		});  

});

function editComment(commentId){
    $.get('/admin/comments/edit','commentid='+commentId,function(data){
          flyboxOpen(800+44,600,'Редактирование комментария','<div style="padding:0 10px;width:800px;height:600px;overflow-x:auto">'+data+'</div>');
   },'html').done(function(){
    
    
    
    
         
            });
}

function removeComment(commentId,name){
   if (window.confirm('Вы уверенны что хотите удалить комментарий пользователя "'+name+'" ?')){
        $.post('/admin/comments/remove',{'commentid':commentId},function(data){
            if(data.error == true){
               alert(data.msg);
            }else{
               $('#comment-row-'+commentId).remove();  
            }
         
        },'json');
   }
}

//Плагин редактирования ячейки таблицы
(function($)
{
    $.fn.tableCellEdit = function(opt)
    {
       var opt = $.extend(
        {
            saveUrl: '/admin/clients/changetatus/id/',
            saveButtonInd: '.save',
            exitButtonInd: '.exit',
            editButtonInd: '.edit',
            saveRemoveBuutonsHtml:'<a rel="block" class="type_save save" href="javascript:void(0);" title="Сохранить"></a><a rel="block" class="type_cancel exit" href="javascript:void(0);" title="Отмена"></a>',
            selectVariants: {'wait-active':'wait-active','active':'active','banned':'banned'},
            input:false,
            valueContInd: '.trval',
            dataAttr:'data-uid'
        }, opt);
        
        if (opt.input !== undefined && opt.input==true){
             var selectElem = "<input id='selector' class='selector'/>";
        }else{
            var selectElem = "<select id='selector' class='selector'>"; 
            $.each(opt.selectVariants,function(k,v){
                selectElem+= '<option data-text="'+v+'" value="'+k+'">'+v+'</option>';
             })
             selectElem+= "</select>";
        }
        
        return $(this).each(function()
        {
            var wrapper = $(this);
    
            wrapper.find(opt.editButtonInd).live('click',function(){
              
              var rollback = wrapper.html();
                $(this).replaceWith(opt.saveRemoveBuutonsHtml);
                var trvalue = wrapper.find(opt.valueContInd).text();
                
                wrapper.find(opt.valueContInd).replaceWith(selectElem);
                wrapper.find('.selector [data-text="' +trvalue+ '"]').attr('selected', true);
                
                wrapper.find(opt.exitButtonInd).bind('click',function(){
                  wrapper.html(rollback);
                });
                
                wrapper.find(opt.saveButtonInd).bind('click',function(){
                  var newVal = wrapper.find('.selector :selected').text();
                    
                      $.post(opt.saveUrl+wrapper.attr('data-uid'),'param='+newVal,function(data){
                        if(data=='OK'){
                          wrapper.html(rollback);
                          wrapper.find(opt.valueContInd).html(newVal);
                        }else{
                          alert('Неизвестная ошибка, презагрузите страницу и попробуйте снова')
                        }
                      
                  },'text');  
                });;     
             });
        })
    }
})(jQuery);  

$(function() {
    // вор тегов и мультиселектов
     $('#sel-tags-start').die().live('dblclick',function(){
            $('#sel-tags').append('<option value="'+ $(this).find(':selected').attr('value')+'">'+$(this).find(':selected').text()+'</option>');
            $('#sel-tags-int').append('<input style="display:none" name="tags[]"  value= "'+ $(this).find(':selected').attr('value')+'"/>'); 
     });
              
     $('#sel-tags').die().live('dblclick',function(){
            $('#sel-tags-int [value="'+ $(this).find(':selected').attr('value')+'"]').remove();
            $(this).find(':selected').remove();
     });
     
     // создаём новый тег
     
     $('#add-new-tag').live('click',function(){
        var name = $('#add-new-tag-name').val();
        $.post('/admin/blogs/addtag/',{'tagname':name},function(data){
           
            if(data.error == true){
                
               showValidateErrors(data.msg);
               
            }else{
                
               nNoteShow('#flash-msg-nNote', 'nSuccess', data.msg);
               
               $('#sel-tags-start').prepend('<option value="'+data.id+'" >'+name+'</option>');
               
               //  $('#comment-row-'+commentId).remove();  
               
            }
         
        },'json');
        
     });
});
