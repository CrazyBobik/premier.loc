   
    /*
*
* jQuery AjaxLeaf plugin
* Copyright (c) 2012 Div-studio, Inc.
* Author: Alex Erkoff aka IniPod.
*
* Version 0.1 (10/07/2012)
* Requires jQuery 1.7.2
*
* Dual licensed under the MIT and GPL licenses:
*   http://www.opensource.org/licenses/mit-license.php
*   http://www.gnu.org/licenses/gpl.html
*
*/
	(function($) {
	   	   
		$.fn.ajaxLeaf = function(opt) {
			var opt = $.extend({
				loadUrl: '',
                collectInfo:function(wrap) {
                     var addInfo = '';
                     return addInfo;
				},
                dropFiltres:function() {
              	},   
                dropFiltresButtonInd:'.drop_filtres',
                srchButtonInd: '.srch',
				srchInputInd: '.srch-input',
				navButtonInd: '.page-button',
				countSelectorInd: '.on-page-count',
				itemsContainer: '#items',
				xInfoHolder: '.info-holder',
				numPageLinksToDisplay: 5,
				navOrder: ["first", "prev", "num", "next", "last"],
				navTmplFirst: '<span class="toFirst ui-corner-tl ui-corner-bl fg-button ui-button page-button" page="1">Первая</span>',
				navTmplPrev: '<span class="toFirst ui-corner-tl ui-corner-bl fg-button ui-button page-button" page="{num}">Предыдущая</span>',
				navTmplNext: '<span class="toFirst ui-corner-tl ui-corner-bl fg-button ui-button page-button" page="{num}">Следующая</span>',
				navTmplLast: '<span class="toFirst ui-corner-tl ui-corner-bl fg-button ui-button page-button" page="{num}">Последняя</span>',
				navTmplNum: '<span class="toFirst ui-corner-tl ui-corner-bl fg-button ui-button page-button {disable}" page="{num}">{num}</span>',
				jquery_ui: false,
				jquery_ui_active: "ui-state-highlight",
				jquery_ui_default: "ui-state-default",
				jquery_ui_disabled: "ui-state-disabled",
				"renderItems": function(items) {
					var itemsHtml = [];
					$.each(items, function(id, user) {
						itemsHtml.push('<tr id="user-row-' + id + '" class="user-row"> <td>' + user.name + '</td><td>' + user.login + '</td><td>' + user.email + '</td><td>' + user.roles + '</td><td><img class="table-btn" src="/adm/img/userRemove.png" onclick="delUser(\'' + id + '\')" title="Удалить" alt="Удалить" /><img src="/adm/img/formbuilder/edit.png" class="table-btn" onclick="editUser(\'' + id + '\')" title="Редактировать" alt="Редактировать" /></td></tr>');
					});
					return itemsHtml;
				},
                 "renderPagination": function(count,onPage,page,opt) {
                    	numPages = Math.ceil(count / onPage);
				     	var more = '<span class="ellipse more">...</span>';
			         	var less = '<span class="ellipse less">...</span>';
			         	var first = !opt.show_first_last ? '' : '<span class="toFirst ui-corner-tl ui-corner-bl fg-button ui-button page-button">' + opt.nav_label_first + '</span>';
			         	var last = !opt.show_first_last ? '' : '<span class="last ui-corner-tr ui-corner-br fg-button ui-button page-button">' + opt.nav_label_last + '</span>';
		            	var navigationHtml = "";
             			for (var i = 0; i < opt.navOrder.length; i++) {
							switch (opt.navOrder[i]) {
							case "first":
								if (page > 1) {
									navigationHtml += opt.navTmplFirst;
								}
								break;
							case "last":
								if (page < numPages) {
									navigationHtml += opt.navTmplLast.replace("{num}", numPages);
								}
								break;
							case "next":
								if (page < numPages) {
									navigationHtml += opt.navTmplNext.replace("{num}", page - 1 + 2);
								}
								break;
							case "prev":
								if (page > 1) {
									navigationHtml += opt.navTmplPrev.replace("{num}", page - 1);
								}
								break;
							case "num":
								navigationHtml += less;
								var currentLink = 1,
									disable;
								while (numPages >= currentLink) {
									disable = currentLink == page ? 'ui-state-disabled' : '';
									navigationHtml += opt.navTmplNum.replace("{num}", currentLink).replace("{num}", currentLink).replace("{disable}", disable);
									currentLink++;
								}
								navigationHtml += more;
								break;
							default:
								break;
							}
						}
       				return navigationHtml;
				}
                
                
                
			}, opt);
			var jquery_ui_default_class = opt.jquery_ui ? opt.jquery_ui_default : '';
			var jquery_ui_active_class = opt.jquery_ui ? opt.jquery_ui_active : '';
			var jquery_ui_disabled_class = opt.jquery_ui ? opt.jquery_ui_disabled : '';
			$(opt.xInfoHolder).hide();
            
		
  			return $(this).each(function() {
  		    
				var wrapper = $(this);
   	            var nowPage = 1 ;
            
				wrapper.off('click', opt.navButtonInd);
				wrapper.off('keyup', opt.srchInputInd);
				wrapper.off('change', opt.countSelectorInd);
				wrapper.on('click', opt.navButtonInd, function() {
					loadRows(wrapper.find(opt.srchInputInd).val(), $(this).attr('page'), wrapper.find(opt.countSelectorInd).val());
                     return false;
				});
                
                wrapper.on('click', opt.srchButtonInd, function() {
					loadRows(wrapper.find(opt.srchInputInd).val(), 1, wrapper.find(opt.countSelectorInd).val());
                    return false;
				});
                
                
                wrapper.on('click', opt.dropFiltresButtonInd, function() {
                    opt.dropFiltres(wrapper);
                   	loadRows('', 1, wrapper.find(opt.countSelectorInd).val());
                     return false;
				});
                
       			wrapper.on('keyup', opt.srchInputInd, function() {
					loadRows(wrapper.find(opt.srchInputInd).val(), 1, wrapper.find(opt.countSelectorInd).val());
				});
                
				wrapper.on('change', opt.countSelectorInd, function() {
					loadRows(wrapper.find(opt.srchInputInd).val(), 1, wrapper.find(opt.countSelectorInd).val());
				});
                
                
                
				var more = '<span class="ellipse more">...</span>';
				var less = '<span class="ellipse less">...</span>';
				var first = !opt.show_first_last ? '' : '<span class="toFirst ui-corner-tl ui-corner-bl fg-button ui-button page-button">' + opt.nav_label_first + '</span>';
				var last = !opt.show_first_last ? '' : '<span class="last ui-corner-tr ui-corner-br fg-button ui-button page-button">' + opt.nav_label_last + '</span>';
   	            
                var restart = function() {
                     opt.dropFiltres(wrapper);
			     	loadRows('', 1, 10)
    			};
                
               	var reload = function() {
    				loadRows('', nowPage,  wrapper.find(opt.countSelectorInd).val())
    			};
                      
            
            	// Загружает строки таблицы и перестраивает пагинацию
				var loadRows = function(filter, page, onPage) {
				    
	                nowPage = page;
                       
		         	$.post(opt.loadUrl, 'filter=' + filter + '&page=' + page + '&onPage=' + onPage + '&' + $(opt.xInfoHolder).text()+'&'+opt.collectInfo(wrapper), function(data) {
						var items = opt.renderItems(data.items);
						wrapper.find(opt.itemsContainer + " tbody").empty().prepend(items.join(''));
						//строим пагинацию
				        var navigationHtml = opt.renderPagination(data.countItems,onPage,page,opt);
						$nav_panels = wrapper.find('#acl-users_paginate');
						$nav_panels.html(navigationHtml).each(function() {});
						$nav_panels.children('.ellipse').hide();
						$nav_panels.find('.previous_link').next().next().addClass('active_page ' + jquery_ui_active_class);
					
                        wrapper.trigger('loaded');
                    }, "json");
				}
				loadRows('', 1, 10);
			});
		};
     
	})(jQuery);                    