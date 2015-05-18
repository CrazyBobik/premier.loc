<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_Gui_Blog extends Admin_Controller_Gui {

	
				
	public function __construct($nodeData)
	{
		
		parent::__construct($this->_options);

		
					
		$this->nodeData = $nodeData;
	
	}
    
    protected function commentsGUI()
 	{
	   
       	$this->tabs['comments'] = 'Комментарии';
        
         return <<< HTML
         <script type="text/javascript">
                   $('#commetns-table-wrapper').ajaxLeaf(
                  { 
                    loadUrl:'/admin/comments/load/', 
                    srchButtonInd: '.srchbutton',
                    itemsContainer:'#comments-forms', 
                    collectInfo:function(wrap) {
                    var addInfo = '&blogid='+wrap.find('#blogid').val();
                        addInfo+= '&comments-status='+wrap.find('#comments-status').val();
                    return addInfo;
        				},
                    "renderItems": function(items) {
				    var itemsHtml = [];
			    	$.each(items, function(id, comment) {
					itemsHtml.push('<tr id="comment-row-' + comment.id + '"><td>' + comment.date + '</td><td>' + comment.name + '</td><td>' + comment.email + '</td><td>' + comment.content + '</td><td class="com-status" data-uid="' + comment.id + '"><span class="trval">' + comment.status + '</span><img alt="Изменить" title="Изменить" class="edit" src="/adm/img/formbuilder/edit.png"></td><td><img src="/adm/img/formbuilder/edit.png" class="table-btn" onclick="editComment(\'' + comment.id  + '\')" title="Редактировать" alt="Редактировать"/><img src="/adm/img/formbuilder/delete.png" class="table-btn" onclick="removeComment(\'' + comment.id  + '\',\'' + comment.name + '\')" title="Удалить" alt="Удалить" /></td></tr>');
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
       	</script>
<div style="margin-top: 8px;" class="table" id="commetns-table-wrapper">
											<div class="head">
												<h5 class="iFrames">
													Таблица комментариев
												</h5>
											</div>
											<div class="dataTables_wrapper" id="acl-users_wrapper">
                                              	<div class="">
                                                    <div id="acl-users_filter" class="dataTables_filter">
                                                         Статус
                                                        <select id="comments-status" style="width:110px">
                                                        <option value="">Любой</option>
                                                        <option value="опубликован">Опубликован</option>
                                                        <option value="ожидает публикации">Ожидает публикации</option>
                                                        </select>
                                                            Поиск:
                                                            <input class="srch-input" type="text" style="width:110px">
                                                            <span class="srchbutton b-button greyishBtn add_node_button"> Поиск </span>
                                                      </div>
                                                </div>
												<table cellspacing="0" cellpadding="0" border="0" class="display" id="comments-forms">
													<thead>
														<tr>
                                                          <th>Дата</th>
													      <th>Имя</th>
                                                          <th>E-mail</th>
                                                          <th>Комментарий</th>
                                                          <th>Статус</th>
                                                          <th>Управление</th>
                                     					</tr>
													</thead>
													<tbody>
													</tbody>
												</table>
												<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix">
													<div id="acl-users_length" class="dataTables_length">
														<label>
															Элементов на страницу:
															<select name="acl-users_length" class="on-page-count" size="1">
                                                                <option value="5" selected="selected">
																	5
																</option>
																<option value="10" selected="selected">
																	10
																</option>
																<option value="25">
																	25
																</option>
																<option value="50">
																	50
																</option>
																<option value="100">
																	100
																</option>
															</select>
														</label>
													</div>
													<div class="dataTables_paginate fg-buttonset ui-buttonset fg-buttonset-multi ui-buttonset-multi paging_full_numbers" id="acl-users_paginate">
											    	</div>
												</div>
											</div>
                                            <input type="hidden" id="blogid" value="{$this->nodeData['tree_id']}" />
										</div>         
HTML;
	}
    
    protected function tagsGUI()
	{
       	$this->tabs['tags'] = 'Теги';
        
        $tagsOptions = '';
        $tagsInputs = '';
        
        $tags = K_TreeQuery::crt('/blogtags/')->type('blogtag')->go();
        
        
        $blogTagsMode = new Admin_Model_BlogTag; 
        
        $blogTags = $blogTagsMode->fetchAssoc('bt_tag_id',K_Db_Select::create()->where(array('bt_blog_id'=>$this->nodeData['tree_id'])));
        
        $blogTagsIds = array_keys($blogTags);
        
        
        foreach($tags as $v):
         
             $tagsOptions.='<option value="'.$v['tree_id'].'" >'.($v['name']?  htmlspecialchars($v['name']) : 'Нет названия').'</option>';
          
             if(in_array($v['tree_id'], $blogTagsIds)){
               $tagsInputs.= '<input style="display:none" name="tags[]"  value="'.$v['tree_id'].'">';
               $tagsSelOptions.='<option value="'.$v['tree_id'].'" >'.($v['name']?  htmlspecialchars($v['name']) : 'Нет названия').'</option>';
             }
             
        endforeach;
         return <<< HTML
         
         
             <div class="b-padded mainForm"> 
                   <div id="flash-msg-nNote" class="nNote hideit" style="display: none;"><p></p></div>
            
              
             <form action="/admin/blogs/settags/" class="ajax-form" method="post">
                 <div class="rowElem noborder admin-form-row">
                                  <label>
                                    Добавить новый тег:
                                  </label>
                                  <div class="formRight">
                                    <input type="text" name="new-teg" id="add-new-tag-name" />
                                  </div>
                                  <div class="fix"></div>
                                  <input type="button" value="Добавить" id="add-new-tag" class="b-button greyishBtn submitForm">
                                  
                </div>
                  <div class="fix"></div>
                 <table class="sel-tags-table" >
                     <tr>
                          <td>
                              <select  class="blog-tags" id="sel-tags-start" multiple="on">
                                $tagsOptions
                              </select>
                          </td>
                          <td>
                              <select name="tags[]" id="sel-tags" class="tags-select" multiple="on">
                                $tagsSelOptions
                              </select>
                          </td>
                      <tr>
                  </table>
                 <div id="sel-tags-int">
                  $tagsInputs
                 </div>   
                 <input type="hidden" name="this_key"  value="{$this->nodeData['tree_id']}" />
                 <input type="submit" value="Сохранить теги" id="save_button" class="b-button greyishBtn submitForm">
             </form>
             </div>
             
HTML;
	}
    
}
            
            
            
            