/**
 * jQuery Form Builder Plugin
 * Copyright (c) 2009 Mike Botsko, Botsko.net LLC (http://www.botsko.net)
 * http://www.botsko.net/blog/2009/04/jquery-form-builder-plugin/
 * Originally designed for AspenMSM, a CMS product from Trellis Development
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 */
(function ($) {
	$.fn.formbuilder = function (options) {
		// Extend the configuration options with user-provided
		var defaults = {
			saveInDom: false,
			domElementId: false,
			save_url: false,
			load_url: false,
			load_source: false,
            preview_url: false,
			control_box_target: false,
            xform:false,
          	serialize_prefix: 'frmb',
            
            validates: ["notEmpty","email","int","float","string","alphanumeric","ealphanumeric","length255","length40","minlength5","date","time","datetime"],
            itemfields:{
                text : ["placeholder",
                    	"label",
                    	"id",
                    	"class",
                    	"name",
                    	"table",
                    	"field",
                    	"default",
                    	"filter"
                ],
            textarea : ["placeholder",
                    	"label",
                    	"id",
                    	"class",
                    	"name",
                    	"table",
                    	"field",
                    	"default",
                    	"filter"
            ],
            file : [   	"label",
                    	"id",
                    	"class",
                    	"name",
                    	"table",
                    	"field",
                    	"default",
                    	"filter",
                    ],  
           multifile : ["label",
                    	"id",
                    	"class",
                    	"name",
                    	"table",
                    	"field",
                    	"default",
                    	"filter",
                        "multiupload"
            ],             
                       
            hidden  : [ "id",
                    	"class",
                    	"name",
                    	"table",
                    	"field",
                    	"method"
            ],
             password :["label", 
                        "id",
                    	"class",
                    	"name",
                    	"table",
                    	"field",
                    	"default",
                    	"filter"
            ],  
            submit  : [ "label",
                    	"id",
                    	"class",
                    	"name"
                    	
            ],
            reset  : [ "label",
                    	"id",
                    	"class",
                    	"name"
             ],
             select   : [ "label",
                    	"id",
                    	"class",
                    	"name",
						"method"
             ],
             checkbox   : [ "label",
                    	"id",
                    	"class",
                    	"name"
             ],
             radio   : [ "label",
                         "id",
                    	"class",
                    	"name"
             ],
             formbuilder: [ "label",
                    	"name"
             ],
             wysiwyg: [ "label",
                        "id",
                    	"class",
                    	"name"
             ]             			 
 
            },
			messages: {
    	      	save				: "Сохранить",
				add_new_field		: "Добавить...",
				text				: "Текстовое поле",
				title				: "Заголовок",
				paragraph			: "Мультистрочное поле",
				checkboxes			: "Чекбоксы",
				radio				: "Радиобоксы",
				select				: "Список",
                hidden     			: "Скрытое поле",
                password			: "Пароль",
				submit				: "Кнопка отправки",
				reset				: "Кнопка очистить",
                file    			: "Файл", 
                multifile           : "Несколько файлов",
				text_field			: "Текстовое поле",
				label				: "label",
                placeholder         : "placeholder",
                multiupload         : "Мультизагрузка",
				paragraph_field		: "Мультистрочное поле",
				select_options		: "Вариант",
				add					: "+",
				checkbox_group		: "Группа чекбоксов",
				remove_message		: "Вы действительно хотите удалить данный элемент?",
				remove				: "Удалить",
				radio_group			: "Группа радиобоксов",
				selections_message	: "Разрешить множественный выбор",
				hide				: "Скрыть",
				required			: "Требуемое",
				show				: "Показать",
                preview				: "Посмотреть",
                validate            : "Выберите метод валидации",
                id               	: "id",
                "class"             : "class",
                name                : "name",
                table               : "Таблица",
                field               : "Поле",
                "default"           : "По-умолчанию",
                filter              : "Фильтр",
				"method"            : "Источник",
				"formbuilder"       : "Генератор форм",
				"wysiwyg"           : "Rich-text редактор",
                  
                validateAll: {
                   notEmpty  : "Требуемое",
                    email    : "Электронная почта",
					"int"    : "Целое число",
    	            "float"  : "Вещественное число",
                    "string" : "Строка",
              "alphanumeric" : "Только бувы и цифры",
             "ealphanumeric" : "Только латинские бувы и цифры",
                   length40  : "Ограничение длины 40 символов", 
                   length255 : "Ограничение длины 255 символов",
                 minlength5  : "Минимальная длина 5 символов",
                	date     : "Дата",
                time         : "Время",
                datetime     : "Дата - Время"
              }                            
	       }
		};
		var opts = $.extend(defaults, options);
		var frmb_id = 'frmb-' + $('ul[id^=frmb-]').length++;
		return this.each(function () {
		    var formBuiderWrapper=$(this);
			var ul_obj = $(this).prepend('<ul id="' + frmb_id + '" class="frmb"></ul>').find('ul');
			var field_type = '', last_id = 1, help, form_db_id;
			// Add a unique class to the current element
			$(ul_obj).addClass(frmb_id);
			// load existing form data
			if (opts.load_url) {
				$.getJSON(opts.load_url, function(json) {
					form_db_id = json.form_id;
					fromJson(json.form_structure);
				});
			}
			// Create form control select box and add into the editor
			var controlBox = function (target) {
					var select = '';
					var box_content = '';
					var save_button = '';
					var box_id = frmb_id + '-control-box';
					var save_id = frmb_id + '-save-button';
                    var preview_id = frmb_id + '-preview-button';
					// Add the available options
					select += '<option value="0">' + opts.messages.add_new_field + '</option>';
					select += '<option value="input_text">' + opts.messages.text + '</option>';
					select += '<option value="formbuilder">' + opts.messages.formbuilder + '</option>';
					select += '<option value="textarea">' + opts.messages.paragraph + '</option>';
					select += '<option value="wysiwyg">' + opts.messages.wysiwyg + '</option>';
					select += '<option value="checkbox">' + opts.messages.checkboxes + '</option>';
					select += '<option value="radio">' + opts.messages.radio + '</option>';
					select += '<option value="select">' + opts.messages.select + '</option>';
                    select += '<option value="file">' + opts.messages.file + '</option>';
                    select += '<option value="multifile">' + opts.messages.multifile + '</option>';
                    select += '<option value="hidden">' + opts.messages.hidden + '</option>';
					select += '<option value="password">' + opts.messages.password + '</option>';
					select += '<option value="submit">' + opts.messages.submit + '</option>';
                    select += '<option value="reset">' + opts.messages.reset + '</option>';
                    
					// Build the control box and save button content
					result_message = '<div id="result-message"></div>';
					box_content = '<select id="' + box_id + '" class="frmb-control">' + select + '</select>';
					save_button = '<input type="submit" id="' + save_id + '" class="frmb-submit" value="' + opts.messages.save + '"/>' + result_message;
					preview_button = '<input type="submit" id="' + preview_id + '" class="frmb-submit" value="' + opts.messages.preview + '"/>';
		          	// Insert the control box into page
					if (!target) {
						$(ul_obj).before(box_content);
					} else {
						$(target).append(box_content);
					}
					// Insert the save button
					if (!opts.saveInDom)
					{
						$(formBuiderWrapper).append(save_button);
					}
                    //$(ul_obj).after(preview_button);
					
					
					var saveMomently = function(){
						
						$('#'+opts.domElementId).val($(ul_obj).serializeFormList({
							prepend: opts.serialize_prefix
						})+ "&form_id=0");
					
						timeoutId = setTimeout(function(){
							saveMomently();
						}, 500);
					};
					
					saveMomently();
					
					
					// Set the form save action
					$('#' + save_id).click(function () {
						save();
						return false;
					});
                    $('#' + preview_id).click(function () {
						preview();
						return false;
					});
                    
					// Add a callback to the select element
					$('#' + box_id).change(function () {
						appendNewField($(this).val());
						$(this).val(0).blur();
						// This solves the scrollTo dependency
						$('html, body').animate({
							scrollTop: $('#frm-' + (last_id - 1) + '-item').offset().top
						}, 500);
						return false;
					});
				}(opts.control_box_target);
			// Json parser to build the form builder
			var fromJson = function (json) {
					var options = false;
					// Parse json
					$(json).each(function () {
					   
			         // Xform type - не добавление нового поля а данные дополнительно подключаемой статической формы.       
                            if (this.type === 'xform') { 
                    	     restoreXform(this.values);
                                 }
                             else{
                                        // checkbox type
                						if (this.type === 'checkbox') {
                							options = [];
                							$.each(this.options, function () {
                								options.push([this.value, this.baseline]);
                							});
                						}
                						// radio type
                						else if (this.type === 'radio') {
                							options = [];
                							$.each(this.options, function () {
                								options.push([this.value, this.baseline]);
                							});
                						}
                						// select type
                						else if (this.type === 'select') {
                							
                							options = [];
                							$.each(this.options, function () {
                								options.push([this.value, this.baseline]);
                							});
                						}
                                     	appendNewField(this.type, this.values,this.vlds,options);
                                 }  
					});
				};
			// Wrapper for adding a new field
			var appendNewField = function (type, values,vlds, options) {
			
					field_type = type;
					if (typeof (values) === 'undefined') {
						values = '';
					}
					switch (type) {
					case 'input_text':
						appendTextInput(values,vlds);
						break;
                        
			        case 'textarea':
						appendTextarea(values,vlds);
						break;
						
			        case 'wysiwyg':
						appendWysiwyg(values,vlds);
						break;
					
			        case 'formbuilder':
						appendFormBuilder(values,vlds);
						break;
                        
                    case 'file':
						appendFileInput(values,vlds);
						break;
                        
                    case 'multifile':
						appendMultiFileInput(values,vlds);
						break;     
					
                    case 'hidden':
						appendHiddenInput(values,vlds);
						break;
                        
					case 'password':
						appendPasswordInput(values,vlds);
						break;
                        
                    case 'submit':
						appendSubmit(values,vlds);
						break;
                        
                    case 'reset':
						appendReset (values,vlds);
						break;    
				                    
                    case 'checkbox':
						appendCheckboxGroup(values,vlds, options);
						break;
					case 'radio':
						appendRadioGroup(values,vlds, options);
						break;
					case 'select':
						appendSelectList(values,vlds, options);
						break;
					}
    	            
				};
            //constructor for elemet atributs    
           var fieldsConstructor=function(fields,values){
                var fields_html='';
                var val='';
                       	for (i = 0; i < fields.length; i++) {
                       	    if (values[fields[i]]=== undefined){
                       	        val='';
                             }
                            else{
                                val=values[fields[i]];
                            }
                            
                   	        fields_html += '<label>' + opts.messages[fields[i]] + ':</label>'; 
                            fields_html += '<input class="fld-'+fields[i]+'" id="frm-item-'+fields[i]+'-'+last_id +'" name="'+fields[i]+'" type="text" value="' +htmlSpecialChars(val) + '" />';
       					} ;
                return fields_html;
           };  
           
           
         var htmlSpecialChars = function(string, reverse)
	{

		// specialChars это список символов и их сущностей
		// specialChars["<"] = "&lt;";
		// x — простая переменная, используемая в циклах
		var specialChars = {
				"&": "&amp;",
				"<": "&lt;",
				">": "&gt;",
				'"': "&quot;"
			}, x;

		// Если мы отменяем перевод
		if (typeof(reverse) != "undefined")
		{

			// Нужно создать временный массив
			reverse = [];

			// Помещаем каждый специальный символ в массив
			for (x in specialChars)
				reverse.push(x);

			// Создаем обратный массив
			// ["<", ">"] становится [">", "<"]
			reverse.reverse();

			// Для каждого специального символа:
			for (x = 0; x < reverse.length; x++)

				// Заменяем все экземпляры (g) сущности оригиналом
				// если x = 1, то
				// reverse[x] = reverse[1] = ">";
				// specialChars[reverse[x]] = specialChars[">"] = "&gt;";
				string = string.replace(
					new RegExp(specialChars[reverse[x]], "g"),
					reverse[x]
				);

			// Получаем оригинальную строку
			return string;
		}

		// Если нам нужно не получать оригинал, а перевести строку в сущности
		// Для каждого специального символа:
		for (x in specialChars)

			// Заменяем все экземпляры специального символа его сущностью
			// Запомните, в отличие от обратного алгоритма, где x была числом
			// здесь х это необходимый символ (&, <, > или ")
			string = string.replace(new RegExp(x, "g"), specialChars[x]);

		// Получаем переведенную строку.
		return string;
	}   
                
			// single line input type="text"
			var appendTextInput = function (values,vlds) {
		           	help = '';
                	appendFieldLi(opts.messages.text, fieldsConstructor(opts.itemfields.text,values) ,vlds, help,values.label);
				};
   	       
                
			// multi-line textarea
			var appendTextarea = function (values,vlds) {
			   		help = '';
					appendFieldLi(opts.messages.paragraph_field, fieldsConstructor(opts.itemfields.textarea ,values), vlds, help,values.label);
				};
				
			// wysiwyg
			var appendWysiwyg = function (values,vlds) {
			   		help = '';
					appendFieldLi(opts.messages.wysiwyg, fieldsConstructor(opts.itemfields.wysiwyg ,values), vlds, help,values.label);
				};
				
			// formbuilder
			var appendFormBuilder = function (values,vlds) {
			   		help = '';
					appendFieldLi(opts.messages.formbuilder, fieldsConstructor(opts.itemfields.formbuilder ,values), vlds, help,values.label);
				};
                
               // single line input type="file"
			var appendFileInput = function (values,vlds) {
			     	help = '';
					appendFieldLi(opts.messages.file, fieldsConstructor(opts.itemfields.file ,values),vlds, help,values.label);
				};   
                // single line input type="file"
             var   appendMultiFileInput = function (values,vlds) {
			     	help = '';
					appendFieldLi(opts.messages.multifile, fieldsConstructor(opts.itemfields.multifile ,values),vlds, help,values.label);
				};  
                
              // single line input type="password"
			var appendPasswordInput = function (values,vlds) {
			     	help = '';
					appendFieldLi(opts.messages.password, fieldsConstructor(opts.itemfields.password ,values),vlds, help,values.label);
				}; 
              // single line input type="submit "
			var appendSubmit = function (values,vlds) {
			     	help = '';
					appendFieldLi(opts.messages.submit, fieldsConstructor(opts.itemfields.submit ,values),vlds, help,values.label);
				}; 
              // single line input type="reset"
			var appendReset = function (values,vlds) {
			     	help = '';
					appendFieldLi(opts.messages.reset, fieldsConstructor(opts.itemfields.reset ,values),vlds, help,values.label);
				};        
             // single line input type="reset"
			var appendHiddenInput = function (values,vlds) {
			     	help = '';
					appendFieldLi(opts.messages.hidden, fieldsConstructor(opts.itemfields.hidden ,values),vlds, help);
				};                
                
                
             // adds a checkbox element
			var appendCheckboxGroup = function (values,vlds, options) {
					var title = '';
                    var field = '';
					if (typeof (values) === 'object') {
						title = values.label;
					}
					field += '<div class="chk_group">';
			        field += fieldsConstructor(opts.itemfields.checkbox  ,values);
					field += '<div class="false-label">' + opts.messages.select_options + '</div>';
					field += '<div class="fields">';
					if (typeof (options) === 'object') {
						for (i = 0; i < options.length; i++) {
							field += checkboxFieldHtml(options[i]);
						}
					}
					else {
						field += checkboxFieldHtml('');
					}
					field += '<div class="add-area"><a href="#" class="add add_ck">' + opts.messages.add + '</a></div>';
					field += '</div>';
					field += '</div>';
					help = '';
					appendFieldLi(opts.messages.checkbox_group, field, vlds, help,values.label);
				};
			// Checkbox field html, since there may be multiple
			var checkboxFieldHtml = function (options) {
					var checked = false;
					var value = '';
                    var field = '';
					if (typeof (options) === 'object') {
						value = options[0];
						checked = ( options[1] === 'false' || options[1] === 'undefined' ) ? false : true;
					}
					field = '';
					field += '<div>';
					field += '<input type="checkbox"' + (checked ? ' checked="checked"' : '') + ' />';
					field += '<input type="text" value="' + value + '" />';
					field += '<a href="#" class="remove" title="' + opts.messages.remove_message + '">' + opts.messages.remove + '</a>';
					field += '</div>';
					return field;
				};
			// adds a radio element
			var appendRadioGroup = function (values, vlds, options) {
			 		var title = '';
                    var field = '';
					if (typeof (values) === 'object') {
						title = values.label;
					}
					field += '<div class="rd_group">';
				    field += fieldsConstructor(opts.itemfields.radio ,values);
					field += '<div class="false-label">' + opts.messages.select_options + '</div>';
					field += '<div class="fields">';
					if (typeof (options) === 'object') {
					 	for (i = 0; i < options.length; i++) {
							field += radioFieldHtml(options[i], 'frm-' + last_id + '-fld');
						}
					}
					else {
						field += radioFieldHtml('', 'frm-' + last_id + '-fld');
					}
					field += '<div class="add-area"><a href="#" class="add add_rd">' + opts.messages.add + '</a></div>';
					field += '</div>';
					field += '</div>';
					help = '';
					appendFieldLi(opts.messages.radio_group, field,vlds, help,values.label);
				};
			// Radio field html, since there may be multiple
			var radioFieldHtml = function (options, name) {
					var checked = false;
					var value = '';
                    var field = '';
					if (typeof (options) === 'object') {
						value = options[0];
						checked = ( options[1] === 'false' || options[1] === 'undefined' ) ? false : true;
					}
					field = '';
					field += '<div>';
					field += '<input type="radio"' + (checked ? ' checked="checked"' : '') + ' name="radio_' + name + '" />';
					field += '<input type="text" value="' + value + '" />';
					field += '<a href="#" class="remove" title="' + opts.messages.remove_message + '">' + opts.messages.remove + '</a>';
					field += '</div>';
					return field;
				};
			// adds a select/option element
			var appendSelectList = function (values,vlds, options) {
					var multiple = false;
					var title = '';
                    var field = '';
					if (typeof (values) === 'object') {
						title = values.label;
						multiple = values.multiple === 'true' ? true : false;
					}
					field += '<div class="opt_group">';
                    field += fieldsConstructor(opts.itemfields.select,values);
					field += '<div class="false-label">' + opts.messages.select_options + '</div>';
					field += '<div class="fields">';
					field += '<input type="checkbox" name="multiple"' + (multiple ? 'checked="checked"' : '') + '>';
					field += '<label class="auto">' + opts.messages.selections_message + '</label>';
					if (typeof (options) === 'object' && options.length > 0) {
						for (i = 0; i < options.length; i++) {
							field += selectFieldHtml(options[i], multiple);
						}
					}
					else {
						field += selectFieldHtml('', multiple);
					}
					field += '<div class="add-area"><a href="#" class="add add_opt">' + opts.messages.add + '</a></div>';
					field += '</div>';
					field += '</div>';
					help = '';
					appendFieldLi(opts.messages.select, field,vlds, help,values.label);
				};
			// Select field html, since there may be multiple
			var selectFieldHtml = function (options, multiple) {
					if (multiple) {
						return checkboxFieldHtml(options);
					}
					else {
						return radioFieldHtml(options);
					}
				};
                
            var validateOptions = function (validates){
                 var validate_options='<option value="0">'+opts.messages.validate+'</option>';
                      	for (i = 0; i < opts.validates.length; i++) {
                   	      if($.inArray(opts.validates[i], validates)==-1){
					        validate_options+='<option value="'+opts.validates[i]+'">'+opts.messages.validateAll[opts.validates[i]]+'</option>';
                           }
						};
                 return validate_options;
               };    
                
			// Appends the new field markup to the editor
			var appendFieldLi = function (title, field_html, validates, help,labletext) {
			 
			        var validates_html='';
                      if (validates !=undefined)
                        for (i = 0; i < validates.length; i++) {
                   	       validates_html+= addvalidate(validates[i],'vld_'+last_id);
  						};
                        
  					var li = '';
					li += '<li id="frm-' + last_id + '-item" class="' + field_type + '">';
					li += '<div class="legend">';
					li += '<a id="frm-' + last_id + '" class="toggle-form closed" href="#"></a> ';
					li += '<a id="del_' + last_id + '" class="del-button delete-confirm" href="#" title="' + opts.messages.remove_message + '"></a>';
					li += '<a id="up_' + last_id + '" class="up-button" href="javascript:void(0)"></a>';
					li += '<a id="dn_' + last_id + '" class="down-button" href="javascript:void(0)"></a>';
					li += '<strong id="txt-title-' + last_id + '">' + title + '</strong><span id="txt-lable-' + last_id + '" > ['+labletext+']</span></div>';
					li += '<div id="frm-' + last_id + '-fld" class="frm-holder">';
					li += '<div class="fld-values">';
                    li += validates_html;
					li += '<div class="frm-fld">';
                    li += '<select class="validate-select" id="vld_'+last_id+'">';
                    li += validateOptions(validates);
                    li += '</select></div>';
					li += field_html;
					li += '</div>';
					li += '</div>';
					li += '</li>';
					$(ul_obj).append(li);
					$('#frm-' + last_id + '-item').hide();
					$('#frm-' + last_id + '-item').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					last_id++;
				};
                
             var addvalidate = function(validate,id){            
                var box='<div vld="del-'+id+'" title="'+validate+'" class="validatebox">';
                    box+=opts.messages.validateAll[validate]+'<a onfocus="this.blur();" class="validate-del" title="Удалить" href="javascript:;"></a>';
				    box+='</div>';
                 return box;   
             }

			// Удаляем все события
			$('.remove').die();
			$('.toggle-form').die();
			$('.delete-confirm').die();
			$('.validate-select').die();
			$('.validate-del').die();
			$('.add_ck').die();
			$('.add_opt').die();
			$('.add_rd').die();
			$('.up-button').die();
			$('.down-button').die();
  	        $('.fld-label').die();
			
			if (opts.load_source) {
				form_db_id = 0;
				fromJson(opts.load_source.form_structure);
			}
               
			// handle field delete links
			$('.remove').live('click', function () {
				$(this).parent('div').animate({
					opacity: 'hide',
					height: 'hide',
					marginBottom: '0px'
				}, 'fast', function () {
					$(this).remove();
				});
				return false;
			});
			// handle field display/hide
			$('.toggle-form').live('click', function () {
				var target = $(this).attr("id");
				if ($(this).hasClass("open")) {
					$(this).removeClass('open').addClass('closed');
					$('#' + target + '-fld').animate({
						opacity: 'hide',
						height: 'hide'
					}, 'slow');
					return false;
				}
				if ($(this).hasClass("closed")) {
					$(this).removeClass('closed').addClass('open');
					$('#' + target + '-fld').animate({
						opacity: 'show',
						height: 'show'
					}, 'slow');
					return false;
				}
				return false;
			});
			// handle delete confirmation
			$('.delete-confirm').live('click', function () {
				var delete_id = $(this).attr("id").replace(/del_/, '');
				if (confirm($(this).attr('title'))) {
					$('#frm-' + delete_id + '-item').animate({
						opacity: 'hide',
						height: 'hide',
						marginBottom: '0px'
					}, 'slow', function () {
						$(this).remove();
					});
				}
				return false;
			});
            
  	        // handle add validate
			$('.validate-select').live('change', function () {
                var validate = $(this).find(':selected').val();
                if(validate!='0'){
                     $(this).find(':selected').remove();
    				 $(this).parent().before(addvalidate(validate,$(this).attr('id')));
                }
     			return false;
			});
            
              // handle dell validate
			$('.validate-del').live('click', function () {
			    var parent=$(this).parent();
			 	var validate_select_id = parent.attr('vld').replace(/del-vld_/, '');
               
                $('#vld_'+validate_select_id).append('<option value="'+parent.attr('title')+'">'+opts.messages.validateAll[parent.attr('title')]+'</option>');
                parent.remove();
     			return false;
			});
            
			// Attach a callback to add new checkboxes
			$('.add_ck').live('click', function (){
				$(this).parent().before(checkboxFieldHtml());
				return false;
			});
			// Attach a callback to add new options
			$('.add_opt').live('click', function (){
				$(this).parent().before(selectFieldHtml('', false));
				return false;
			});
			// Attach a callback to add new radio fields
			$('.add_rd').live('click', function (){
				$(this).parent().before(radioFieldHtml(false, $(this).parents('.frm-holder').attr('id')));
				return false;
			});
			// Changing position of list elements
			$('.up-button').live('click', function(){
				var moveElement = $(this).parent().parent();
				moveUp(moveElement);
			});
			$('.down-button').live('click', function(){
				var moveElement = $(this).parent().parent();
				moveDown(moveElement);
			});
            //Add lable text to title 
  	        $('.fld-label').live('keyup',function(){
      	         var id=$(this).attr('id').replace(/frm-item-label-/,'');
                // alert('txt-title-'+id);
    			 $('#txt-lable-'+id).text(' ['+$(this).val()+']');
			});
            
            
			
			// saves the serialized data to the server 
			var save = function () {
        			if (opts.save_url) {
						$.ajax({
							type: "POST",
							url:opts.save_url,
							data: $(ul_obj).serializeFormList({
								prepend: opts.serialize_prefix
							}) + "&form_id=" + form_db_id+serializeXForms(),
							success: function (){
								$('#result-message').html('Форма сохранена!').css('display', 'block').animate({
									opacity: 1.0
								}, 3000);
								
								setTimeout(function(){
									$('#result-message').animate({
										opacity: 0.0
									}, 3000, function(){
										$('#result-message').css('display', 'none');
									});
								}, 2000);
							}
						});
					};
				};
             //востанавливаем дополнительную форму   
             var restoreXform = function(values){
              $.each(values, function(key, value) { 
                 var elem=$('#'+ opts.xform+' [name^="'+key+'"]');
                    if (elem.length){
                        if(elem.attr('type')=='checkbox'){
                             if(value)                
                             elem.attr('checked',true);
                        }
                        else{
                          elem.val(value);  
                        }
                   }
                })
              }    

           //серелизует дополнительне формы которые можно настроить в xform
            var serializeXForms = function(){
              var params='';
              if (opts.xform!=false)
               params="&"+$('#'+ opts.xform).serialize();
                  /*       for (i = 0; i < opts.add_to_serialaze.length; i++) {
                            alert(opts.add_to_serialaze[i]);
                   	        params+="&"+$('#'+opts.add_to_serialaze[i]).serialize();
              			};*/
                
                  /*     opts.add_to_serialaze.each(function(form){
                    params+="&"+$('#'+form).serialize(); })*/
              return params;
           }
                
                
          	var preview = function () {
        			if (opts.preview_url) {
						$.ajax({
							type: "POST",
							url:opts.preview_url,
							data: $(ul_obj).serializeFormList({
								prepend: opts.serialize_prefix
							}) + "&form_id=" + form_db_id,
							success: function (result){
							 $('#my-form-builder-preview').html(result);                             
							}
						});
					};
				};      
                
		});
	};
})(jQuery);

function moveUp($item) {
    $before = $item.prev();
    $item.insertBefore($before);
}

function moveDown($item) {
    $after = $item.next();
    $item.insertAfter($after);
}

/**
 * jQuery Form Builder List Serialization Plugin
 * Copyright (c) 2009 Mike Botsko, Botsko.net LLC (http://www.botsko.net)
 * Originally designed for AspenMSM, a CMS product from Trellis Development
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 * Modified from the serialize list plugin
 * http://www.botsko.net/blog/2009/01/jquery_serialize_list_plugin/
 */
(function ($) {
    var fieldtypes=["placeholder",
                    	"label",
                    	"id",
                    	"class",
                    	"name",
                    	"table",
                    	"field",
                    	"default",
                    	"filter",
						"method",
                        "multiupload"
                        ];
    var vldtypes=["requred","integer"];
	$.fn.serializeFormList = function (options) {
		// Extend the configuration options with user-provided
		var defaults = {
			prepend: 'ul',
			is_child: false,
			attributes: ['class']
		};
		var opts = $.extend(defaults, options);
		if (!opts.is_child) {
			opts.prepend = '&' + opts.prepend;
		}
         
		var serialStr = '';
		// Begin the core plugin
		this.each(function () {
			var ul_obj = this;
			var li_count = 0;
			var c = 1;
            
            var serializeFields=function(id){
                var fieldsStr='';
             	 $('#' + $(id).attr('id') + ' .fld-values input[type=text]').each(function () {
           	    var this_field = $(this).attr('name');
                if ($.inArray(this_field, fieldtypes) != -1) {
     	             fieldsStr += opts.prepend + '[' + li_count + '][values]['+this_field+']=' + encodeURIComponent($(this).val());
     	          };
                })
                return fieldsStr;
            };           
            
             var serializeVld=function(id){
                var vldStr='';
                var v=0;
             	 $('#' + $(id).attr('id') + ' .validatebox').each(function () {
           	       var this_vld = $(this).attr('title');
                  //if ($.inArray(this_vld, vldtypes)>0) {
                          vldStr += opts.prepend + '[' + li_count + '][vlds]['+v+']='+this_vld;
                          v++;
     	          //};
                })
                return vldStr;
            };    
            
			$(this).children().each(function () {
				for (att = 0; att < opts.attributes.length; att++) {
					var key = (opts.attributes[att] === 'class' ? 'type' : opts.attributes[att]);
					serialStr += opts.prepend + '[' + li_count + '][' + key + ']=' + encodeURIComponent($(this).attr(opts.attributes[att]));
					// append the form field values
					if (opts.attributes[att] === 'class') {
					   serialStr +=serializeFields(this);
                       serialStr +=serializeVld(this);
						switch ($(this).attr(opts.attributes[att])) {
						case 'checkbox':
                      		c = 1;
							$('#' + $(this).attr('id') + ' .fields input[type=text]').each(function () {
									serialStr += opts.prepend + '[' + li_count + '][options][' + c + '][value]=' + encodeURIComponent($(this).val());
									serialStr += opts.prepend + '[' + li_count + '][options][' + c + '][baseline]=' + $(this).prev().attr('checked');
								c++;
							});
							break;
						case 'radio':
							c = 1;
							$('#' + $(this).attr('id') + ' .fields input[type=text]').each(function () {
									serialStr += opts.prepend + '[' + li_count + '][options][' + c + '][value]=' + encodeURIComponent($(this).val());
									serialStr += opts.prepend + '[' + li_count + '][options][' + c + '][baseline]=' + $(this).prev().attr('checked');
							
								c++;
							});
							break;
						case 'select':
							c = 1;
							serialStr += opts.prepend + '[' + li_count + '][multiple]=' + $('#' + $(this).attr('id') + ' input[name=multiple]').attr('checked');
							$('#' + $(this).attr('id') + ' .fields input[type=text]').each(function () {
									serialStr += opts.prepend + '[' + li_count + '][options][' + c + '][value]=' + encodeURIComponent($(this).val());
									serialStr += opts.prepend + '[' + li_count + '][options][' + c + '][baseline]=' + $(this).prev().attr('checked');
							
								c++;
							});
							break;
						}
					}
				}
				li_count++;
			});
		});
		return (serialStr);
	};
})(jQuery);