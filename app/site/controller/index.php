<?php

class Site_Controller_Index extends Controller {
    /* {public} */

    public $helpers = array(
        'paginator',
        'call',
        'error',
        'form',
        'include',
        'ru',
        'prevnext');
   
    public  $layout = 'layout';
    private $configs = array();
    private $staticPage;
    
    private $nodeItem = array(); // информация о единичной ноде к которой идёт запрос
    private $parentNodeItem = array(); // информация о родителе единичной ноды к которой идёт запрос

    public function onInit() {
  	
    }
	
    public static $userregionid = '';

    public function pageAction(){

        $link = '/pages';

        $this->paramLink = $_SERVER['REQUEST_URI'];
      						
        //убираем гет строку
        $qpos = strpos($this->paramLink, '?');
		
        if ($qpos) {
		
            $this->prezentGetParams = true;
			
            $this->paramLink = substr($this->paramLink, 0, strpos($this->paramLink, '?'));
			
        }
		
        $this->view->expuri = $expuri = explode('/', $this->paramLink);
						
        $this->view->langs = K_TreeQuery::crt('/langs/')->type(array('contentlang'))->go();
		
        $this->view->realUrl =  $expuri[1];
		
        //определение языка
        foreach($this->view->langs as $lang){
			
            $this->view->langsTok[] = $lang['tree_name'];
			
            if($lang['tree_name'] == $expuri[1]){
				
                $contentLang = $lang['tree_name'];
				
                $this->view->realUrl =  $expuri[2];
				
                break;
				
            }

        }

		
		
        if(!$contentLang){
			
            $contentLang = AllConfig::$defoultLang;
            $this->view->realUrl =  $expuri[1];
        }else{
		
            unset($expuri[0]);
            unset($expuri[1]);
								
            $this->paramLink = implode('/', $expuri);
			
            //$this->realSiteLink =
        }
			
        // если язык русский то и интерфейс русский
        if(	$contentLang == 'ru'){
		
            $siteLang = 'ru';
		
			 
        }else{
		
            $siteLang = 'en';
	
        }


        AllConfig::$contentLang = $contentLang;
        AllConfig::$siteLang = $siteLang;
        $this->view->settings = K_TreeQuery::gOne('/settings/'.$contentLang.'/', 'sitesettings');

        K_Registry::write('settings', $this->view->settings);


        K_Crumbs::add(t('Главная','Company'), '/');//($contentLang == AllConfig::$defoultLang ? '/' :'/'.$contentLang
			
					
        $this->view->paramLink =  $this->paramLink;
               
        $this->paramLink = trim( $this->paramLink, '/');
	
        // прописать в техническом роутере самого движка роуты на сайтомап и апи и другие технические разделы, ajax например

        if($this->paramLink == "sitemap.xml"){
        
            require_once(PLATFORM_PATH.'/sitemap.php');
            
        }
       	
        // если страница не найдена добавляем в хедер 404 ошибку
        if(empty($this->paramLink)||$this->paramLink=="/"){
     
            $pageIndex = 'index';
            $this->view->onMain = true;
        }    
	   	    
        //
        if($linkRouter = $this->preRouter("/".$contentLang.'/'.$this->paramLink."/")){
          
            $this->view->page = $page = $this->treePage($linkRouter);
			
        }
        else{   //спец функция
                       
            $this->view->page = $page = $this->treePage('/pages/'.$contentLang.'/'.$this->paramLink.$pageIndex.'/'); 
        
        }
   
        // строим страницу через treePage, если страница не найдена выводим 404 ошибку
        if(!$this->view->page){

            header("HTTP/1.0 404 Not Found");
            $this->treePage("/pages/".$contentLang."/404/");

        }
	   

		




	 
    }
    
    /** treePage  строит страницу из дерева типов или вовзвращяет false если страница на найденна
     *
     *
     */
	
    private function treePage($link){
	
        $nodes = K_TreeQuery::crt($link)->go(array('aliases' => true));
     	
        //если нет сессии - проверка на залогиненость

        $metaParams['pageArgs'] = $this->pageArgs;
         
        $metaParams['link'] = $this->paramLink;
      
        // var_dump($link); Ссылка поиска страницы к дереву типов
        
        $this->meta = $metaParams;
        
        // echo $link;
        // echo '<br/>';
    
        $this->staticPage = $nodes[0];
          
        // проверка на страницу к кторой идёт запрос 
        //  var_dump($nodes);
        if($nodes[0]){ // кастомная страница
     
            K_Seo::set($nodes[0]);
			
            //$this->view->currentMenuItem = $this->paramLink;
            
            //строим блоки и кастомную страницу 
            $this->view->content = $this->getBlocks($nodes);
            //		var_dump($this->view->content);
            // K_debug::get()->addMessage('node  '.$nodes[0]['tree_type']);
            // K_debug::get()->dump($nodes);
       
        } else{
	   
            return false;

        }
       
        $this->view->title = K_Seo::$title;
                
        $this->view->mkeys = K_Seo::$keywords;
                
        $this->view->mdesc = K_Seo::$description;
	   
        $this->view->canonical = K_Seo::$canonical;
		   
        return trim($link,'/');

    }
   
    
    /*
    private function getItemBlocks($node, $paramNode,$howShow,$nodeMeta,$typeP) {
         //$typeBlocks=cTree::getTypeBlocks($nodeItem[0]['tree_type']);

        $this->nodeItem = $paramNode[0];
        $blocks = $this->getBlocks($node);
        
        var_dump($this->nodeItem);
      
        $blocks['middle'][] = K_Request::call(array(
                                  'module'     => 'typebloks',
                                  'controller' => $typeP,
                                  'action'     => $howShow == 'list' ? 'list' : 'item',
                                  'params'     => array('node' =>$paramNode)
                                  )
                                );
        
        // $this->bd_simpleTypeBlock($paramNode[0]);

        return $blocks;
    }
*/


/** listRouter  обработчик списка вывода и пагинации
 *
 *
 */

    private function listRouter($paramLink){
				
        $paramArr = explode('.',$paramLink);
               
                          
        $paramLink = $paramArr[0];
               
        // на всякий случай определимся как мы будем показывать тип
        $this->howShow = $paramArr[1] == 'list' ? 'list' :'item';
               
        if ((int)$paramArr[1]){//если вторым элементом идёт число, то сразу можно определить список
            $this->howShow = 'list';
            $argnum = 1;
        }else{
            $argnum = 2;
        }
                
        $this->pageArgs['num'] = $paramArr[$argnum] ? $paramArr[$argnum] : 1;
                    
        if(!empty($paramArr[$argnum+1]) && (int)($paramArr[$argnum+1])){
            $this->pageArgs['count'] = $paramArr[$argnum+1] > 100 ? 100 : $paramArr[$argnum+1];//должно браться из настроек
        }else $this->pageArgs['count'] = 10;
              
              
        return $paramLink;
    }

    /** preRouter дополнительный роутинг который проверяет ссылки первого уровня 
     */

    private function preRouter($link){
        //var_dump(allconfig::$contentLang.'/searche/');
								

        //********************  Статья отдельно.
//			$articleNode = K_TreeQuery::gOne('/articles'.$link, 'articles');
//
//			if(!empty($articleNode)){
//
//				// если есть статья с такой ссылкой переходим на неё
//				K_Registry::write('articles', $articleNode);
//				K_Seo::set($articleNode);
//
//				return $link = '/system-pages/articles/';
//
//			}
//
        //********************  Новость отдельно.
        $newsNode = K_TreeQuery::gOne('/news/news-out'.$link, 'news');

        if(!empty($newsNode)){
            // если есть новость с такой ссылкой переходим на неё
            K_Registry::write('news', $newsNode);
            K_Seo::set($newsNode);

            return $link = '/system-pages/oneNews/';
        }
        $newsNode = K_TreeQuery::gOne('/news/news-compain'.$link, 'news');

        if(!empty($newsNode)){
            // если есть новость с такой ссылкой переходим на неё
            K_Registry::write('news', $newsNode);
            K_Seo::set($newsNode);

            return $link = '/system-pages/oneNews/';
        }

        //********************  Все новости.
        if ($link === "/ru/news-out/"){
            $news = "/news/news-out/";
            K_Registry::write('news', $news);

            return $link = '/pages/ru/news/ru/';
        } elseif ($link === "/ru/news-in/"){
            $news = "/news/news-compain/ru/";
            K_Registry::write('news', $news);

            return $link = '/pages/ru/news/';
        }


        //******************* Новострой отдельно.
        $novostroyNode = K_TreeQuery::gOne('/jk'.$link, 'novostoy');

        if (!empty($novostroyNode)){
            //если есть новострой с такой же ссылкой переходим на него
            K_Registry::write('novostroy', $novostroyNode);
            K_SEO::set($novostroyNode);

            return $link = '/system-pages/oneNovostroy/';
        }

        //******************* Все новострои.
//            if ($link === "/ru/objects/"){
//                return $link = '/system-pages/objects/';
//            }

        /*
			//********************  Новости списком. 
			// парсим внешнию строку запроса 
			if( preg_match('/^\/category\/novosti\/page\/(\d+)\//is', $link, $mathcs)){
					
				$_GET['page'] = $mathcs[1];                 
			
				return '/system-pages/newslist/'; 
			}
			
			//********************  Новости списком с пагинацией. 
			if( preg_match('/^\/category\/novosti\//is', $link, $mathcs)){
					
				$_GET['page'] = $mathcs[1];                
			
				return '/system-pages/newslist/'; 
			}
		  */
        return false;
    }

    /** getBlocks загрузка и формирование блоков
     *
     * @param $nodes
     * @return $rBlocks
     *
     */
 
    private function getBlocks($nodes){
        // include_once('start.php');
        $this->userregionid = $defoult_loc['reg_id'];
        //vd1($defoult_loc['reg_id']);
         
        $parentNode = $nodes[0];
        $blocks = array();
        $rBlocks = array();

        $lastBlock = null;
        $lastBlockName = '';
        
        
        // загрузка базовых блоков
        $doNotLoadBaseBlock = array(
        
            'footer' => false
        );

        foreach ($nodes as $nodeKey => $node) {
            if ($node['tree_pid'] == $parentNode['tree_id'] && ($node['tree_type'] == 'column' || $node['tree_type'] == 'list' )) {
                if ($node['tree_name'] == 'left') {
                    $lastBlock = $node['tree_id'];
                    $lastBlockName = 'left';
                    $doNotLoadBaseBlock['left'] = true;
                }
                elseif ($node['tree_name'] == 'right') {
                    $lastBlock = $node['tree_id'];
                    $lastBlockName = 'right';
                    $doNotLoadBaseBlock['right'] = true;
                }
                elseif ($node['tree_name'] == 'middle') {
                    $lastBlock = $node['tree_id'];
                    $lastBlockName = 'middle';
                    $doNotLoadBaseBlock['middle'] = true;
                }
                elseif ($node['tree_name'] == 'widgets') {
                    $lastBlock = $node['tree_id'];
                    $lastBlockName = 'widgets';
                    $doNotLoadBaseBlock['widgets'] = true;
                }

                elseif ($node['tree_name'] == 'content') {

                    $lastBlock = $node['tree_id'];
                    $lastBlockName = 'content';
                    // $doNotLoadBaseBlock['content'] = true;
                }

                elseif ($node['tree_name'] == 'footer') {
                    $lastBlock = $node['tree_id'];
                    $lastBlockName = 'footer';
                    $doNotLoadBaseBlock['footer'] = true;
                }
                else{
                    $lastBlock = $node['tree_id'];
                    $lastBlockName = 'middle';
                    $doNotLoadBaseBlock['middle'] = true;
                    $blocks[$lastBlockName][] = $node;
                }
            } else{

                if ($lastBlock && $lastBlockName && $node['tree_pid'] == $lastBlock) {

                    $blocks[$lastBlockName][] = $node;
                }
            }
        }


        $links = array();

        foreach ($doNotLoadBaseBlock as $blockType => $dontLoadBlocks) {
            if (!$dontLoadBlocks) {
                $links[] = $blockType;
            }
        }

        if (sizeof($links) > 0) {
            $baseNodes = K_TreeQuery::crt('/system-pages/baseblocks/')->go(array('aliases'=>true));

            foreach ($baseNodes as $nodeKey => $node) {
                if ($node['tree_pid'] == $baseNodes[0]['tree_id'] && ($node['tree_type'] == 'column' || $node['tree_type'] == 'list' )) {
                    if (in_array($node['tree_name'], $links)) {

                        if ($node['tree_name'] == 'left') {
                            $lastBlock = $node['tree_id'];
                            $lastBlockName = 'left';
                            $doNotLoadBaseBlock['left'] = false;
                        }

                        if ($node['tree_name'] == 'right') {
                            $lastBlock = $node['tree_id'];
                            $lastBlockName = 'right';
                            $doNotLoadBaseBlock['right'] = false;
                        }

                        if ($node['tree_name'] == 'middle') {
                            $lastBlock = $node['tree_id'];
                            $lastBlockName = 'middle';
                            $doNotLoadBaseBlock['middle'] = false;
                        }

                        if ($node['tree_name'] == 'widgets') {
                            $lastBlock = $node['tree_id'];
                            $lastBlockName = 'widgets';
                            $doNotLoadBaseBlock['widgets'] = false;
                        }

                        if ($node['tree_name'] == 'content') {
                            $lastBlock = $node['tree_id'];
                            $lastBlockName = 'content';
                            $doNotLoadBaseBlock['content'] = false;
                        }

                        if ($node['tree_name'] == 'footer') {
                            $lastBlock = $node['tree_id'];
                            $lastBlockName = 'footer';
                            $doNotLoadBaseBlock['footer'] = false;
                        }
                    }
                } elseif ($lastBlock && $lastBlockName && !$doNotLoadBaseBlock[$lastBlockName] && $node['tree_pid'] == $lastBlock) {
                    $blocks[$lastBlockName][] = $node;
                }
            }
        }

        $noLoadBlockTypes = array('folder', 'page', 'list','column');

        ///Загрузка блоков по модулям

        foreach ($blocks as $blockType => $typeBlocks){

            $countElements = sizeof($typeBlocks);

            for ($i = 0; $i < $countElements; $i++) {

                if(!in_array($typeBlocks[$i]['tree_type'], $noLoadBlockTypes)){

                    if ($typeBlocks[$i]['tree_type'] != 'block') {

                        $block = $this->HMVCblock($typeBlocks[$i], $typeBlocks[$i]['tree_type']);

                    } elseif (isset($typeBlocks[$i]['content']) && $typeBlocks[$i]['content']) {


                        if( $typeBlocks[$i]['template'] == 'Без шаблона' ){/// @todo когда сделаю селекты, переделать на нормальный латинский токен

                            $block = $this->loadBlock( $typeBlocks[$i], false );

                        }elseif(!$typeBlocks[$i]['template'] || $typeBlocks[$i]['template'] == 'Стандартный шаблон старшего модуля'){

                            $block = $this->loadBlock($typeBlocks[$i], $blockType);


                        }else{

                            $block = $this->loadBlock($typeBlocks[$i], $typeBlocks[$i]['template']);

                        }

                    } elseif (!empty($typeBlocks[$i]['action'])) {

                        $block = $this->HMVCblock($this->nodeItem, $typeBlocks[$i]['action']);

                    }

                    // $this->{'b_' . self::blockActionToMethod($typeBlocks[$i]['action'])}($typeBlocks[$i]);
                    $rBlocks[$blockType][$i] = $block;

                }
            }
        }
        ///Дозагрузка блоков в контент

        foreach ($nodes as $nodeKey => $node){

            if($node['tree_pid']==$parentNode['tree_id'] && $node['tree_type']=='block') {

                if ($node['tree_type'] == 'block') {

                    if (!empty($node['action'])) {

                        $block = $this->HMVCblock($this->nodeItem, $node['action']);

                    } elseif ($node['template'] == 'Без шаблона') {/// @todo когда сделаю селекты, переделать на нормальный латинский токен

                        $block = $this->loadBlock($node, false);

                    } elseif (!$node['template'] || $node['template'] == 'Стандартный шаблон старшего модуля') {

                        $block = $this->loadBlock($node, $blockType);

                    } else {

                        $block = $this->loadBlock($node, $node['template']);

                    }

                }

                //$this->{'b_' . self::blockActionToMethod($node['action'])}($node);
                $rBlocks['middle'][] = $block;
            }
        }

        return $rBlocks;
    }
    
    
    /** Загружает HMVCblock у которого есть свой шаблон и контроллер
     *
     * @param $node нода блока
     * @param $template шаблон блока который надо подключить
     * @return
     *
     */

    private function HMVCblock($node, $block = false){
		
        //exit();
	   
        return K_Request::call(array(
        
            'module' => 'blocks',
            'controller' => $block ? $block : $node['action'],
            'action' => 'index',
            'params' => array('node'=>$node, 'own'=>$this->nodeItem, 'meta' => $this->meta)));
                                    
    }
     
    /** Загружает блоки и цепляет шаблоны для них
     *
     * @param $node надо блока
     * @param $template шаблон блока который надо подключить
     * @return поключенный отрендеренный шаблон блока
     *
     * Конвенции $node['content'] - сюда рендериться пользователький шаблон
     *
     */
     
    private function loadBlock($node, $template = false){
	

        // шаблонизатор в шаблоне, параметры беруться из страницы
      
        if(isset($node['templater_on']) && $node['templater_on'] == "Да"){ ///@todo отрефакторить, когда сделаю нормальные селекты на on/off
            
            /// шаблонизатор http://webew.ru/articles/3609.webew
                          
            $templater = new Templater; ///@todo phalcon integration: сделать возможность менять шаблонизатор на phalcon volt
                           
            foreach($this->staticPage as $k => $v){
                            
                /// выбираем все поля с префиксом templater_
                if(preg_match('/templater_/', $k)){
                                    
                    $data[str_replace('templater_', '', $k)] = $v;
                                      
                }
                            
            }
                     
            $node['content'] = $templater->parse($data, $node['content'], false, false);
        };
     
        if(!$template){
            
            $result = $node['content'];
            
        }else{
     	
		
            if($template=="Стандартный шаблон блока контента(block-content)"){
			
			
                $template = 'block-content';
			
            }elseif($template=='Вывод только содержимого как ест( notemplate )'){
			
                $template = 'notemplate';
			
            }
			
            $result = '';
      
            ob_start();
   
              
            include (APP_PATH . '/blocks/_simple/' . $template . '.phtml');
                
         
    
            $result = ob_get_contents();
            
            ob_end_clean();
            
        }

        return $result;
    } 
    
    /** Методы блоков
     *  подключает html шаблоны блоков 
     *  
     * 
     */
     
    private function bd_simpleTypeBlock($node){
        
        $result = '';

        if (is_dir(APP_PATH . '/blocks/' . $node['tree_type'])) {
            include (APP_PATH . '/blocks/' . $node['tree_type'] . '/item.phtml');
        }
        
        return K_Request::call(array(
                'module'     => 'typebloks',
                'controller' => $node['tree_type'],
                'action'     => 'item',
                'params'     => array('node' =>$node)
            )
        );
    }

    /** Згружает блок с клиентской формой 
     * 
     */
    private function blockForm($params = array()) {
        $result = '';

        $this->view->type = $_GET;
        $this->view->loadFormTemplate(
            array(
                'formStart' => '<div class="mainForm"><div style="display: none;" class="nNote nSuccess hideit" id="flash-msg-nNote"><p></p></div>',
                'formEnd' => '</div>',
                'row' => '<div class="rowElem noborder user-form-row"><label>{{label}}:</label><div class="formRight">{{element}}</div><div class="fix"></div></div>',
                'row_submit' => '{{element}}',
                'row_reset' => '{{element}}',
                'row_file' => '<div class="rowElem noborder user-form-row"><label>{{label}}:</label><div class="formRight" >{{element}}</div><div class="fix"></div></div>',
                'row_select' => '<div class="rowElem noborder user-form-row"><label>{{label}}:</label><div class="formRight" >{{element}}</div><div class="fix"></div></div>',
                'checkbox' => '<div class="rowElem noborder user-form-row"><label>{{label}}:</label><div class="formRight" >{{element}}</div><div class="fix"></div></div>',
                'radio' => '<div class="rowElem noborder user-form-row"><label>{{label}}:</label><div class="formRight" >{{element}}</div><div class="fix"></div></div>',
                'row_formbuilder' => '{{element}}'
            )
        );

        ob_start();

        include (APP_PATH . '/blocks/client_form.phtml');

        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
  
    /// шаблонизатор рендеринга блока  
    private function templateRender($data, $template){
        
        /// шаблонизатор http://webew.ru/articles/3609.webew
        $templater = new Templater;
                      
        return $templater->parse($data, $template, false, false);
    }
    
    private function genLeftMenu(){
        
        $menu_arr =K_CupTree::rootPath(K_TreeQuery::crt('/leftmenu/')->types(array('menulink'))->go(),'/leftmenu/');
       
       
        return K_Menu::buildTreeMenu($menu_arr,0,$this->meta['link']);
      
    }

}
