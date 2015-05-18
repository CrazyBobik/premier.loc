<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_jForm {
    private static $adminka = false;

    public static function generate($structure, $data, $formData, $forma, $template, $extraFields = null,$adminka=true) {
       
        echo $template['formStart'];
       
        self::$adminka = $adminka;
       
        $form = new K_jformHelper;
        
        $form->begin($formData[0], $formData[1], array_merge(array('id' => 'x_form_' . $formData[2], 'enctype' => 'multipart/form-data'), isset($formData[4]) ? $formData[4] : array()));

        $structure = json_decode($structure);

        if (is_array($structure) && $structure) {
            
            foreach ($structure as $elementKey => $element) {
                
                if (self::convertTypes($element->type)) {
                    
                    $type = self::convertTypes($element->type);
                    
                    ob_start();
                   
                      self::addField($form, $type, $element, (isset($data[$element->values->name]) ? $data[$element->values->name] : ''), $formData);
                   
                      $fieldCode = ob_get_contents();
                  
                    ob_end_clean();
              
                    echo self::template((isset($template['row_' . $type]) ? $template['row_' . $type] : $template['row']), array('label' => $element->values->label, 'class'=>$element->values->class ,'element' => $fieldCode));
                }
            }

            if (isset($extraFields) && $extraFields)
                foreach ($extraFields as $k => $v) {
                    $element = new stdClass();
                    $element->values->name = $k;
                    $element->values->value = $v;
                    $element->values->class = '';
                    $element->values->id = '';
                    self::addField($form, "hidden", $element, (isset($data[$element->values->name]) ? $data[$element->values->name] : ''), $formData);
                }
                
        } else {
            echo '
				<div class="nNote nFailure hideit">
					<p><strong>Ошибка: </strong>Форма не найдена!</p>
				</div>
			';
        }

        $form->end();
        echo self::template($template['formEnd'], array('formid' => $formData[2]));
       
    if($adminka)
        echo '<script type="text/javascript">$("#x_form_' . $formData[2] . '").ajaxForm({beforeSubmit:function(){disableTree();},success: function(responseText, statusText, xhr, $form){ $("#x_formsuccess_' . $formData[2] . '").css("display", "block").animate({"opacity": 1.0}, 2000).html("<p>"+responseText+"</p>"); }});</script>';
    }

    public static function template($template, $data = array()) {
        
        // var_dump($template);
        
        foreach ($data as $templateVar => $templateVal) {
            
            $template = str_replace('{{' . $templateVar . '}}', $templateVal, $template);
            
        }

        return $template;
    }

    public static function addField($form, $type, $element, $elementData, $formData) {
        switch ($type) {
            case 'text':
                $form->$type($element->values->name, (! empty($elementData) ? $elementData : (! empty($element->values->default) ? $element->values->default : '')), array(
                    'class' => $element->values->class,
                    'id' => $element->values->id,
                    'placeholder' => $element->values->placeholder));
                    
                break;
            case 'textarea':
                $form->$type($element->values->name, (! empty($elementData) ? $elementData : (! empty($element->values->default) ? $element->values->default : '')), array(
                    'class' => $element->values->class,
                    'id' => $element->values->id,
                    'placeholder' => $element->values->placeholder));
                break;
            case 'wysiwyg':
                $form->textarea($element->values->name, (! empty($elementData) ? $elementData : (! empty($element->values->default) ? $element->values->default : '')), array('class' => $element->values->class . ' wysiwyg-redactor', 'id' => $element->values->id));
                echo '
				<script type="text/javascript">
            
                  $(function() {
                 	$(".wysiwyg-redactor").ckeditor({
                   	                allowedContent: true,
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
                                                   CKEDITOR.replace( "content_id", {
                                                        allowedContent: true})
                                                   config.protectedSource.push(/<(style)[^>]*>.*<\/style>/ig);
                                                   return config;
                                                   
                                                }
                   	});
                     
                     
                     
                  });
                  
                 </script>'; 
            
            /*
				$(".wysiwyg-redactor").redactor({
				        
				        imageUpload: "/admin/api/uploadImage",
				        autoformat: true,
                        cleanUp: true,
					    convertDivs: false,
                        removeClasses: true,
                        removeStyles: true,
                        convertLinks: false,
                        buttons: ["html", "|", "formatting", "|", "bold", "italic" , "deleted","|","norderedlist", "orderedlist", "outdent", "indent", "|","image","video","table", "link", "|","fontcolor", "backcolor", "|","alignleft", "aligncenter", "alignright", "justify", "|","horizontalrule", "fullscreen"] 
				});
            */
            
                 break;
            case 'formbuilder':
                K_Loader::load('formbuilder', APP_PATH . '/plugins');
                
                $form_builder = new Formbuilder(unserialize($elementData));
                echo '
				<div id="my-form-builder"></div>
				<input type="hidden" name="' . $element->values->name . '" id="f_' . $element->values->name . '"></div>
				
				<script type="text/javascript">		
					$("#my-form-builder").formbuilder({
						"save_url": false,
						"load_url": false,
						"preview_url": false,
						"saveInDom": true,
						' . (! empty($elementData) ? '"load_source": ' . $form_builder->render_json(false) . ',' : '') . '
						"domElementId": "f_' . $element->values->name . '",
						"useJson" : true 
					});
				</script>
				';
                break;
            case 'file':
                $form->$type($element->values->name, array('class' => $element->values->class, 'id' => $element->values->id));
                     if(self::$adminka){
                        echo ' <input type="checkbox" name="'.$element->values->name.'_delete">Удалить файл</input>';
                     }
                   
                     if($elementData)
                     echo '<div>' .(preg_match('/^.*\.(jpg|png|jpeg|gif|bmp|ico)$/im', $elementData) ? '<br /><img  src="/upload/'.$elementData.'?h=120"  height="120" style="margin-top:5px" /></div>' : '<span class="file_address_text"><a href="/upload/'.htmlentities(urlencode( $elementData )).'">' . $elementData . '</a></span></div>');
              
                break;
                
           case 'multifile':
           
                   echo '<div id = '.$element->values->id.' data-filename = '.$element->values->name.' class = "multifile">';
                   $i=0;
           
                   if($elementData){
                       $fileArr = unserialize($elementData);
                                 
                       if(count($fileArr))
                       foreach($fileArr as $v){
                        
                         echo  '<div style="margin:15px 0"  id="'.$element->values->id.'_a_'.$i.'">'; 
                         
                         echo  '<input type="text" value="'.$v['t'].'" style="width:150px;margin-right:5px" name="'.$element->values->name.'_t_'.$i.'" id="'.$element->values->id.'_t_'.$i.'"/>';
                         
                           $form->file($element->values->name.'_f_'.$i, array('class' => $element->values->class,'id' => $element->values->id.'_f_'.$i));
                           
                           if(self::$adminka){
                              echo '<br/> <input type="checkbox" name="'.$element->values->name.'_delete_'.$i.'">Удалить файл</input>';
                           }
                          
                           echo '<div>' .(preg_match('/^.*\.(jpg|png|jpeg|gif|bmp|ico)$/im', $v['f']) ? '<br /><img  height="120" src="/iploads/'.$v['f'].'?w=150&h=150&zc=C" style="margin-top:5px" /></div><br/>' : '<span class="file_address_text"><a href="/upload/'.htmlentities(urlencode($v['f'])) .'">' . ($v['t']?$v['t']:$v['f']) . '</a></span></div><br/>');
     
                          $i++;
                          
                         echo  '</div>'; 
                          
                        }
                     }
                     
                     if(!$i) {
                        
                          echo  '<div style="margin:15px 0"  id="'.$element->values->id.'_a_0">'; 
                         
                          echo  '<input type="text"  style="width:150px;margin-right:5px" name="'.$element->values->name.'_t_0" id="'.$element->values->id.'_t_0"/>';
                         
                           $form->file($element->values->name.'_f_0', array('style'=>'width:220px' ,'class' => $element->values->class,'id' => $element->values->id.'_f_0'));
                    
                          echo '<a data-fileid="'.$element->values->id.'_0'.'" class="file_field_delete" href="javascript:void(0);" title="Убрать лишний"></a>';
                          echo  '</div>'; 
                       
                        }
                   echo '</div>'; 
                   echo "<script type=\"text/javascript\"> 
                            $(function(){
                                 $('#" . $element->values->id."').multifile(); 
                            });
                         </script>"; 
                 break;
            case 'select':
            
                $listOptions = self::getListOptions($element);
                $listAttr = array('class' => $element->values->class, 'id' => $element->values->id);

                if ($element->multiple == "checked") {
                    $listAttr['multiple'] = 'multiple';
                }

                //  var_dump($element);
                $treeIds = array();

                if ($element->values->method) {
                     
                    $params = json_decode($element->values->method,true);
               
                    // var_dump($params);
                    // $params = explode(':',$element->values->method);
                    
                    $source = trim($params['node']);
                   
                    $field ='tree_'.$params['field'];
                   
                    if(strpos($params['field'],'type')!==false){
                    
                       $field = str_replace('type_','',$params['field']);
                    
                    }
                    
                    if (preg_match('/^\/.*\/$/', $source)) {
                 
						if(empty($params['go']) || !is_array($params['go'])){
							
							$params['go']=array();						
						
						}		
				 
                        $goParams = array_merge(array(), array('orderby' => $params['order'][1]));
                   						  
						//var_dump( $goParams); 		
						   
                        $nodeChilds = K_TreeQuery::crt($source)->types($params['filter'])->limit($params['limit'])->order($params['order'][0])->go($goParams);
             					
                        // var_dump($nodeChilds);  
                        // var_dump($nodeChilds);  
                        // $start=$nodeChilds[1]['tree_level'];
                        // var_dump($treeBrach);
                        // str_repeat('&nbsp;&nbsp;',$v['tree_level']).$v['tree_title']
						
                        $firstNode = true;
                        
                        foreach ($nodeChilds as $v) {
                            
                               if($firstNode == true){
                                
                                     $startLevel = $v['tree_level'];
                                     
                               }
                            
                               $firstNode = false;
                               
                               $title = $v[$field];
                               $treeIds[$title] = $v['tree_id'];
                            
                               // str_repeat('&nbsp;&nbsp;',$v['tree_level']-$start).
                                
                               if($params['title'] && count($params['title'])){
                                    
                                    $optTitle=array();
                                   
                                    foreach($params['title'] as $tile){  
                                   
                                      $optTitle[]=$v[$tile];
                                   
                                    }
                                 
                                    $listOptions[0][$title] = str_repeat('&nbsp;&nbsp;', $v['tree_level']-$startLevel).implode(' - ', $optTitle);
                                }
                                else{
                                    
                                    $listOptions[0][$title] = str_repeat('&nbsp;&nbsp;', $v['tree_level']-$startLevel).$v['tree_title'];
                                    
                                }
                        }
                        
                        //var_dump( $listOptions[0] );
                        
                    } else { 
                       $node = K_Tree::getNode($elementData);
                       $nodesBro = K_Tree::getChilds($node['tree_pid']);
                        foreach($nodesBro as $v){     
                            $title = $v[$field];
                            $treeIds[$title] = $v['tree_id'];
                            $listOptions[0][$title] = $v['tree_title'];
                        }
                        
                        echo "<script type=\"text/javascript\"> 
                           $('#" . $source . "').off('.formsel'); 
                           $('#" . $source . "').on('change.formsel',function(){selectLoad(this,'#{$listAttr['id']}','{$params['0']}','')});
                           </script>";
                        /*
                       if(! empty($elementData)){
                                $checkJs="if(v.value=='$elementData'){cheked='selected=\"selected\"}";
                          //  $this->loadSelect[$source]=array('select'=>$listAttr['id'],'field'=>$params['0'],'opt'=>$elementData);
                             
                            echo "selectLoad('#".$source ."','#{$listAttr['id']}','{$params['0']}','$elementData',);";  
                            } 
                          */  
                      }
                }
       
               $form->$type($element->values->name . (isset($listAttr['multiple']) && $listAttr['multiple'] ? '[]' : ''), $listOptions[0],  (! empty($elementData) ? $elementData : $listOptions[0]), $listAttr, $treeIds);
       
            break;
            
            case 'checkbox':
            
                $listOptions = self::getListOptions($element);
                
                $listAttr = array('class' => $element->values->class, 'id' => $element->values->id);

                if (count($listOptions[0]) > 0) {
                    
                    $i = 1;
                   
                    foreach ($listOptions[0] as $key => $value){
               
                        echo '<label>';
               
                        $form->$type($element->values->name . (count($listOptions[0]) > 1 ? '[]' : ''), ($listOptions[1] == $i || (count($listOptions[0]) == 1 && $elementData == 'on') ? true : false), $key, $listAttr);
                    
                        echo  $key . ' &nbsp;'.'</label>';

                        $i++;
                        
                    }
                    
                }

            break;
            
            case 'radio':
            
                $listOptions = self::getListOptions($element);
                
                $listAttr = array('class' => $element->values->class, 'id' => $element->values->id);
                
                if (count($listOptions[0]) > 0) {
                  
                    $i = 1;
                    
                    foreach ($listOptions[0] as $key => $value) {
                        
                        echo '<label>';
                        
                        $form->$type($element->values->name . (count($listOptions[0]) > 1 ? '[]' : ''), ($listOptions[1] == $i || (count($listOptions[0]) == 1 && $elementData == 'on') ? true : false), $key, $listAttr);
                          
                        echo  $key . ' &nbsp;'.'</label>';

                        $i++;
                        
                    }
                }
               
               //  var_dump($listOptions[0]);
               // $form->$type($element->values->name . (''), $listOptions[0], $listOptions[1], $listAttr);
            
            break;

            case 'submit':
                $form->$type($element->values->label, array('class' => $element->values->class, 'id' => $element->values->id));
                break;
            case 'reset':
                $form->$type($element->values->label, array('class' => $element->values->class, 'id' => $element->values->id));
                break;

            case 'hidden':
                $form->$type($element->values->name, $element->values->value, array('class' => $element->values->class, 'id' => $element->values->id));
                break;

        }
    }

    public static function getFromWWWPath($path) {
        $sid = strpos($path, 'www');
        return (strpos($path, '/images/') === 0 ? $path : '/' . str_replace('\\', '/', substr($path, ($sid + 4))));
    }

    public static function convertTypes($type) {
        switch ($type) {
            case 'input_text':
                return 'text';
            case 'wysiwyg':
                return 'wysiwyg';
            case 'textarea':
                return 'textarea';
            case 'formbuilder':
                return 'formbuilder';
            case 'file':
                return 'file';
            case 'multifile':
                return 'multifile';
            case 'select':
                return 'select';
            case 'checkbox':
                return 'checkbox';
            case 'radio':
                return 'radio';
            case 'submit':
                return 'submit';
            case 'reset':
                return 'reset';
        }

        return false;
    }

    public static function getListOptions($element) {
        $resultOptions = array();
        $checkedOption = '';

        foreach ((array )$element->options as $key => $value) {
            $resultOptions[$value->value] = $value->value;

            if ($value->baseline == 'checked') {
                $checkedOption = $key;
            }
        }

        return array($resultOptions, $checkedOption);
    }
}
