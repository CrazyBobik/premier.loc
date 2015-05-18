<?php
/**
 * @package    DM
 * @category   Libraries
 */
class K_Menu
{

    /** Строим меню из ресурса ноды
     *  @$resurce - ссылка на ресур ноды
     *  @$activeMenu - принудительно активное меню, устанавливаеться на вкладке страницы
     *  @$currentMenu - меню выбранное в данный момент
     *  @example - $menuItems = K_Menu::buildMenu('/site-menu/', $this->currentMenuItem, $this->activeMenu); 
     */
     
     
    public static function buildMenu($resurce = '/main-menu/', $currentMenu = false, $activeMenu = false)
    {

        $nodeIdsArr[] = $v['tree_rule_resource_id'];

        $fullNodeResourse = implode('/', $nodeIdsArr); //полный ресурс ноды

        $menu = new cTree('link');

        /// выбираем ветку с меню
        $menuItems = $menu->getNodes($resurce);

        //если установленно активное меню, находим совпадение в списке меню и устанавливаем активным его, поиск по tree_id
        if ($activeMenu) {

            $i = 1;

            foreach ($menuItems as $v) {

                if ($activeMenu == $v['tree_id']) {

                    $maxLinkDeepNum = $i;

                    break;

                }

                $i++;

            }
            
        } else {

            //иначе вычисляем по веткам 
            $first = true;

            $linkArr = explode('/', $currentMenu);

            $linkRazdel = '';

            $linkArrSpr[] = '/';

            foreach ($linkArr as $v) {

                if ($v) {

                    $linkIdsArr[] = $v;
                    $linkRazdel = '/' . implode('/', $linkIdsArr) . '/';
                    $linkArrSpr[] = $linkRazdel;

                }

            }

            $i = 1;
            $maxLinkDeep = 0;
            $maxLinkDeepNum = 0;

            foreach ($menuItems as $v) {
                
                $n = 1;
                foreach ($linkArrSpr as $v1) {
                    
                    if ($v['address'] == $v1) {
                        
                        $linkDeep = $n;
                        break;
                        
                    }
                    $n++;
                    
                }

                if ($linkDeep > $maxLinkDeep) {
                    
                    $maxLinkDeep = $linkDeep;
                    $maxLinkDeepNum = $i;
                    
                }
                $i++;
            }
        }

        $i = 1;

        $returnMenuItems = array();

        foreach ($menuItems as $linkKey => $linkData) {

            $element = $linkData;
            
            if ($i == $maxLinkDeepNum) {

                $element['menu_active'] = true;

            }else{
                
                $element['menu_active'] = false;
                
            }
            
            $returnMenuItems[] = $element;
            
            $i++;
            
        }
   
        return $returnMenuItems;

    }
    
    
     /** Строим древовидное меню из ресурса ноды
     *  @$menu_arr - массив элементов меню
     *  @$levelMod - модификатор сдвига уровня меню
     *  @$link - ссылка открытой в данный момент страницы сайта
     *  @example - $menuHtml = K_Menu::menuTree('/site-menu/', $this->currentMenuItem, $this->activeMenu); 
     */
     
    
    public static function buildTreeMenu($menu_arr, $levelMod = 0, $link){
 
        $menuHtml = '';
        $first_ol = true;
        $level_tmp = -1;
        $menu_html = '';
        $multi_item = false;
        $level_up = false;
        $multi_li = false;
        $length = count($menu_arr);

        $hided_str = 'style="display:none"'; // добавляеться к спрятанному пункту меню


       $absolut_level=$levelMod;
       $start_tree_level=$menu_arr[0]['tree_level'];
       
       $link=explode('/',$link);
       
       $up_level_menu = $menu_arr[0];// сохраняем ноду верхнего уровня
        
       if ($menu_arr && count($menu_arr)){
           for ($k = 0; $k < $length; $k++) {
		
				if($menu_arr[$k]['show']!='Нет'){
			    
						$level_up = false;
					   
						if ($menu_arr[$k]['tree_level'] > $level_tmp) { //углубляемся на 1 уровень
						  
							$absolut_level++;
							if ($first_ol) 
							$add_ul = '<ul>';// первая категория
							else
							$add_ul = '<ul>';
							   
							if($up_level_menu['hide']=='да'){
							  
							   $add_ul = '<ul ' . $hided_str . '>';
								
							}
							
							$menu_html .= $add_ul;
							
							$first_ol = false;
							$level_tmp = $menu_arr[$k]['tree_level']; //сохроняем уровень в ктором находимся
							$up_level_menu = $menu_arr[$k];// сохраняем ноду верхнего уровня
			
						} else if ($menu_arr[$k]['tree_level'] < $level_tmp) { //подымаемся на n уровеней
							
								$menu_html .= '</li>';
								$absolut_level-= $level_tmp - $menu_arr[$k]['tree_level'];
								$menu_html .= str_repeat('</ul></li>', $level_tmp - $menu_arr[$k]['tree_level']) . '';
								$level_tmp = $menu_arr[$k]['tree_level'];
								$up_level_menu = $menu_arr[$k];// сохраняем ноду верхнего уровня
								
							} else {
								$menu_html .= '</li>';
							}
				 
						$menulink = trim(implode('/',array_slice($link, 0, $start_tree_level+$absolut_level-$levelMod)).'/');
						// echo md5($menulink).'-'. md5($menu_arr[$k]['tree_link']).'<br>';
						 
					   /*if($menulink == $menu_arr[$k]['tree_link']){
							echo"active";
						 }*/
						$menu_html .= '<li><a href="'.$menu_arr[$k]['tree_link'].'" class="link-'.$absolut_level.'-level '.($menu_arr[$k]['tree_link'] == $menulink ? 'active' : '').'" >'.$menu_arr[$k]['tree_title'].'</a>';
						$level_save = $menu_arr[$k]['tree_level']-$start_tree_level;
				}
			}
           return $menu_html .= str_repeat('</li></ul>', $level_save+1);
       }
    } 
    
 
} 
