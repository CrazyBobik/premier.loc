<?php

class K_Tree{

    static $table = 'tree';
    
    
    public static function getAllTree()
    {
        
     $treeTable = new K_Tree_Model();
     return $result = $treeTable->fetchAll(K_Db_Select::create()->order(array('tree_lkey'))); 
    }
 
    public static function add($pid, $type, $name, $title, $show = 1, $ord = 0)
    {
		$treeTable = new K_Tree_Model();
		
        if (preg_match('/[^a-z0-9-]/i', $name))
        {
            die('node name not match'.$name );
        }

		// Получаем родительский элемент
		$result = self::getNode($pid);
        $pid=$result['tree_id'];
		
		if (count($result) == 0)
		{
            die('no such parent node');
        }
		
        list($lKey, $rKey, $level, $url) = array($result['tree_lkey'], $result['tree_rkey'], $result['tree_level'], $result['tree_link'] );

        // Проверяем, есть ли элемент с таким же именем
		$cnt = $treeTable->count(
			K_Db_Select::create()
				->where('`tree_pid` = '. (int)$pid .' AND `tree_name` = '. K_Db_Quote::quoteKey($name))
		);
		
        // Если есть, варьируем число в конце
        if ($cnt > 0)
		{
			$result = $treeTable->select()->where('`tree_pid`='.(int)$pid)->fetchArray();

            $eNames = array();
            while (list($k, $f) = each($result))
			{
                $eNames[] = $f['tree_name'];
            }
			
            if (preg_match('@(\d+)$@', $name, $subp))
			{
                // Имя заканчивается числом
                $newNum = intval($subp[1]); $newPart = substr($name, 0, strlen($name) - strlen($newNum));
            }
			else
			{
                // Имя не заканчивается числом, берем его целиком за основу
                $newNum = 1; $newPart = $name;
            }
            while (in_array($newPart . $newNum, $eNames)) {
                $newNum++;
            }
            $name = $newPart . $newNum;
        }
		
        // Формируем ссылку для вставляемого элемента
        $url = ($url{0} == '.') ? ( (($url{1} != '1') ? $url : '') . '/' . $name . '/' ) : ( $url . $name . '/' );
			
        // Подготовка к сдвигу
        if ($ord == 0)
		{
            // По умолчанию вставляем в конец родительского элемента
			$result = $treeTable->select('MAX(`tree_rkey`) as `mkey`')->where('`tree_pid`='.(int)$pid)->fetchArray();
			
			$mKey = $result[0]['mkey'];
        }
		else
		{
            // Вставляем в определенную позицию
			$result = $treeTable->select('`tree_lkey`-1 as `tree_lkey`')->where('`tree_pid`='.(int)$pid)->order('`tree_lkey`')->fetchAssoc('tree_name');

            $childs = array();
            while (list($k, $f) = each($r)) {
                $childs[] = $f['tree_lkey'];
            }
            if ($ord > count($childs)) {
                $mKey = $rKey - 1;
            } else {
                $mKey = $childs[$ord - 1];
            }
        }
		
        if ($mKey == 0) {
            $newLeft = $rKey; $newRight = $rKey + 1; // Это первый элемент
        } else {
            $newLeft = $mKey + 1; $newRight = $mKey + 2; // Нормальная вставка
        }
		
		$treeTable->update(array(
				'tree_rkey' => new K_Db_Expr('IF(`tree_rkey` >= '.$newLeft.', `tree_rkey` + 2, `tree_rkey`)'),
				'tree_lkey' => new K_Db_Expr('IF(`tree_lkey` >= '.$newLeft.', `tree_lkey` + 2, `tree_lkey`) '),
			), '`tree_rkey` >= '.$newLeft);
		
		
		$time = time();
		
		$insertIntoTreeData = array(
			'tree_lkey'     => $newLeft,
			'tree_rkey'     => $newRight,
			'tree_level'    => $level + 1,
			'tree_pid'      => $pid,
			'tree_type'     => $type,
			'tree_name'     => $name,
			'tree_link'     => $url,
			'tree_title'    => $title,
			'tree_added'    => $time,
			'tree_modified' => $time,
		);
		
		$insertId = $treeTable->save($insertIntoTreeData);

		$typeModelName = 'Type_Model_'.ucfirst($type);
		$typeTable = new $typeModelName();
		
		$typeTable->save(array('type_'.$type.'_id' => $insertId));
		
        // Возвращает ID созданного элемента.
        return $insertId;
    }
    
    public static function delete($id)
    {
		$treeTable = new K_Tree_Model();
		
        // Проверяем существование элемента с заданным ID.
        $result = $treeTable->select()->where('`tree_id`='.(int)$id)->fetchArray();
        
        if (count($result) == 0)
		{ 
            return false;
        }
		
        list($lKey, $rKey) = array($result[0]['tree_lkey'], $result[0]['tree_rkey']);
		
        // Удаляем выбранный элемент и все вложенные.
		$treeTable->select()->where('`tree_lkey` >= '.$lKey.' AND `tree_rkey` <= '.$rKey)->remove();
		
		$shift = $rKey - $lKey + 1;
		
		$treeTable->update(array(
				'tree_lkey' => new K_Db_Expr('IF(`tree_lkey` > '.$lKey.', `tree_lkey` - '.$shift.', `tree_lkey`)'),
				'tree_rkey' => new K_Db_Expr('`tree_rkey` - '.$shift),
			), '`tree_rkey` > '.$rKey);
        
        return true;
    }
 	
    /**
     * @throws Exception
     * @param $base
     * @param $position
     * @return array|bool
     */
    private static function _getPositionParent($base, $position)
    {
        switch ($position)
        {
            case 'top':
            case 'bottom':
            case 'inside':
                // Базовый элемент является родительским для вставляемого
                return $base;
            break;
            case 'before':
            case 'after':
                // Базовый элемент и вставляемый имеют общего родителя
                return self::getNode($base['tree_pid']);
            break;
            default:
                throw new Exception('Положение вставки задано неправильно: '.$position);
            break;
        }
    }

    /**
     * @throws Exception
     * @param $base
     * @param $parent
     * @param $position
     * @return
     */
    private static function _getPositionLeftKey($base, $parent, $position)
    {
        switch ($position)
        {
            case 'top':
                return $parent['tree_lkey'] + 1;
            break;
            case 'bottom':
            case 'inside':
                return $parent['tree_rkey'];
            break;
            case 'before':
                return $base['tree_lkey'];
            break;
            case 'after':
                return $base['tree_rkey'] + 1;
            break;
            default:
                throw new Exception('Положение вставки задано неправильно: '.$position);
            break;
        }
    }
	
    /**
     * Получить информацию об элементе дерева.
     *
     * @param integer|string $key ID или Link элемента
     * @param bool $get_tree_data Нужны ли данные из дерева
     * @return array|bool Массив данных об элементе / FALSE, если элемент не найден
     */
    public static function getNode($key)
    {
		$treeTable = new K_Tree_Model();
		
        // Определяем поле ключа
        $key_field = is_numeric($key) ? 'tree_id' : 'tree_link';

		if ($key)
		{
			// Выполняем запрос
			$result = $treeTable->select()->where(array($key_field => $key))->fetchRow();
			
			if ($result)
			{
				$result = $result->toArray();
			}
			else
			{
				return false;
			}
		
        	// Элемент не найден
			if (count($result) == 0)
				$result = false;
		}
		else
		{
			$result = false;
		}
		
        return $result;
    }
	
    
     /**
     * Выбрать всех родителей для ноды
     *
     * @param int $node_id ID элемента
     * @full bool вернуть полные данные о ноде
     * @return array Массив родотельских элементов
     * 
     * @todo Optimizirovat сделать условие для полей
     */

    public static function getParents( $id , $full=false) {
        $treeTable = new K_Tree_Model();

        $table_left = 'tree_lkey';
        $table_right = 'tree_rkey';
        $table_id = 'tree_id';

        $query = 'SELECT A.*, CASE WHEN A.' . $table_left . ' + 1 < A.' . $table_right . ' 
        THEN 1 ELSE 0 END AS nflag FROM tree A, tree B WHERE B.' . $table_id . ' = ' . ( int )$id . ' AND B.' . $table_left . ' BETWEEN A.' . $table_left . ' AND A.' . $table_right . ' ORDER BY A.' . $table_left;

        $node_parents_arr = $treeTable->fetchArray( $query );
        
        $node_parents=array();
        
        foreach ( $node_parents_arr as $v ) {
    
          if ( $v["nflag"] == 1 && $v["tree_id"]!=$id ){
          
                 if($full){
                    
                       $node_parents[] = $v;
                 }else{
                    
                       $node_parents[] = $v["tree_id"];
                   
                 }
           }
        
        }
        return $node_parents;
    }
    
    /**
     * Выбирает ветку начиная с ноды  
     *
     * @param int id ноды с которой начать выборку ветки 
     * @return возвращяет ветку запрашиваемого дерева.
     * @removeRootNode убират ноду с кторой начинаеться выборка
     */
        
   public static function getTreeBranch($id=NULL, $removeRootNode = false, $typeFilter = false)
	{
	   if($typeFilter){
	        if(is_string($typeFilter)){
                $typeFilter=array($typeFilter);
            }
            
            foreach($typeFilter as $v){
               $typeFilterArr[]=K_Db_Quote::quote($v);
            }
            
           $typeFilterWhere ="  AND tree_type IN(".implode(',',$typeFilterArr).') ';
       }
      
       if ($id)
		{
			$node = self::getNode($id);
        	$query =
				'SELECT *
				FROM tree
				WHERE tree_lkey >= '.(int)$node['tree_lkey'].' AND tree_rkey <= '.(int)$node['tree_rkey'].$typeFilterWhere.'
     			ORDER BY tree_lkey';
 		}
		else
		{
			$query =
				'SELECT *
				FROM tree
				ORDER BY tree_lkey';
		}
       
        $treeTable = new K_Tree_Model();
        $branch = $treeTable->fetchArray( $query );
	     
        //удаляем родительскую ноду
        if ($removeRootNode){
            foreach($branch as $k=>&$v){
                         
                if($node['tree_id']==$v['tree_id']){
                     unset($branch[$k]);
                }
            }
           
        }       
        
        return $branch;
	} 
    
    
    
    public static function getChilds($key)
    {
        
      	$treeTable = new K_Tree_Model();
		
        // Определяем поле ключа
        if (is_numeric($key)){
           $pid=$key;
        }
        else{
          $node=self::getNode($key);  
          $pid=$node['tree_id'];
        }
      $query=('SELECT *
				FROM tree where tree_pid='.$pid.'
				ORDER BY tree_lkey');

      $childs = $treeTable->fetchArray( $query );  
      
      return $childs;  
    }
    
    
    public static function getNextBro($key, $node = false)
    {
        
         $treeTable = new K_Tree_Model();
          
          // Определяем поле ключа
          if(!$node){
            
              if (is_numeric($key)){
                
                   $pid = $key;
                   $node = self::getNode($key); 
                   
              }
              else{
                
                  $node = self::getNode($key);  
                  $pid = $node['tree_id'];
                  
              }
          
          }
            
          $query = ('SELECT *
    				FROM tree where tree_pid = '.$node['tree_pid'].' AND tree_lkey > ' .$node['tree_lkey'].'
    			    ORDER BY tree_lkey limit 1');  
                    
          $treeR  = $treeTable->fetchArray( $query );  
          $nextBro = $treeR[0];  
          
          return $nextBro;  
          
    }
        
    // Проверить ключь ноды          
    public static function checkNodeKey($key)
    {
          if (is_numeric($key)){
            
               $pid = $key;
               
          }
          else{
            
              $node = self::getNode($key);  
              $pid = $node['tree_id'];
              
          }
          
         return $pid;
    }
    
    public static function getPrevBro($key, $node = false)
    {        
       
          $treeTable = new K_Tree_Model();
            
          // Определяем поле ключа
          if(!$node){
            
              if (is_numeric($key)){
                
                   $pid = $key;
                   $node = self::getNode($key); 
                   
              }
              else{
                
                  $node = self::getNode($key);  
                  $pid = $node['tree_id'];
                  
              }
          
          }
            
          $query = ('SELECT *
    				FROM tree where tree_pid = '.$node['tree_pid'].' AND tree_lkey < ' .$node['tree_lkey'].'
    			    ORDER BY tree_lkey DESC limit 1');  
                             
          $treeR  = $treeTable->fetchArray( $query );  
          $prevBro = $treeR[0];     
                  
          return $prevBro;  
    }
    

   /**
     * Подобрать свободное имя сегмента URL
     *
     * @param int $parent_id ID родительского элемента
     * @param string $old_name Старое имя сегмента
     * @return string Новое (свободное) имя сегмента
     */
    private static function getAvailableName($parent_id, $old_name)
    {
		$treeTable = new K_Tree_Model();
	
        // Список занятых сегментов
        $occupied_names = array();
		$request = $treeTable->select('tree_name')->where('`tree_pid`='.$parent_id)->fetchArray();
		
		
        foreach ($request as $f)
        {
            $occupied_names[] = $f['tree_name'];
        }

        // Разбираем первоначальное имя на основу + числовой суффикс
        if (preg_match('/(\d+)$/', $old_name, $matches))
        {
            // Предлагаемое имя содержит числовой суффикс
            $name_suffix = (int)$matches[1];
            $name_base = substr($old_name, 0, strlen($old_name) - strlen($name_suffix));
        }
        else
        {
            // Имя сегмента не содержит числового суффикса, добавляем единичку
            $name_suffix = 1;
            $name_base = $old_name;
        }

        while (in_array($name_base.$name_suffix, $occupied_names))
            $name_suffix++;

        return $name_base.$name_suffix;
    }
	
    /**
     * Сформировать URL элемента по URL родителя $parent_url + имени сегмента элемента $name
     *
     * @param string $parent_url URL родителя
     * @param string $name Имя сегмента элемента
     * @return string URL элемента
     */
    private static function _makeUrl($parent_url, $name)
    {
        if ($parent_url == '/')
        {
            return '/'.$name.'/';
        }
        else
        {
            return $parent_url.$name.'/';
        }
    }
	
    /**
     * @throws Exception
     * @param array|int|string $node Объект элемента или ключ
     * @param string|bool $parent_url Ссылка родительского элемента
     * @return bool
     */
    private static function _fixUrls($node, $parent_url = FALSE)
    {
		$treeTable = new K_Tree_Model();
	
        if ( ! is_array($node))
        {
            $node = self::getNode($node);
            if ($node === FALSE)
                throw new Exception('Элемент с ID '.func_get_arg(0).' не найден');
        }

        if ($node['tree_level'] == 0)
            throw new Exception('Элемент нулевого уровня');

        if ($parent_url === FALSE)
        {
            $parent_url = Arr::get(self::getNode($node['tree_pid']), 'url');
        }

        $urls = array($node['tree_pid'] => $parent_url);

		$result = $treeTable->select(array('tree_id', 'tree_name', 'tree_pid'))->where('`tree_lkey` BETWEEN '.$node['tree_lkey'].' AND '.$node['tree_rkey'])->order('tree_lkey')->fetchArray();
				
        foreach ($result as $f)
        {
            $urls[$f['tree_id']] = self::_makeUrl($urls[$f['tree_pid']], $f['tree_name']);
        }
		
        foreach ($urls as $node_id => $node_url)
        {
			$treeTable->update(array(
					'tree_link' => $node_url,
				), '`tree_id`='.$node_id);
        }
		
        return true;
    }
    
    /*
    * Возвращает количество детей в ноде
    */
    public static function countChilds($pid)
    {
       $treeTable = new K_Tree_Model(); 
       return $treeTable->count(K_Db_Select::create()->where(array('tree_pid'=>$pid)));
    }
	
    /**
     * Перемещает элемент в новое место дерева. Позиция определяется относительно опорного элемента
     *
     * @throws Exception
     * @param integer $key Ключ (ID или URL) перемещаемого элемента
     * @param integer $base_key Ключ (ID или URL) опорного элемента
     * @param string $position Положение относительно опорного элемента. Возможные значения: bottom, top, before, after
     * @return bool Произошло ли перемещение
     */
     
    public static function move($key, $base_key, $position = 'top')
    {
		$treeTable = new K_Tree_Model();
	
        // Получаем информацию о перемещаемом элементе
        $node = self::getNode($key);
		
        if ($node === FALSE)
            throw new Exception('Перемещаемого элемента ('.$key.') не существует');
        if ($node['tree_level'] == 0)
            throw new Exception('Невозможно переместить элемент нулевого уровня');
        $node_diff = $node['tree_rkey'] - $node['tree_lkey'] + 1;

        // Получаем опорный элемент
        $base = self::getNode($base_key);
        if ($base === FALSE)
            throw new Exception('Заданного опорного элемента ('.$base_key.') не существует');

        // Получаем родительский элемент
        $parent = self::_getPositionParent($base, $position);

        // Не пытаемся ли мы переместить элемент в один из вложенных?
		if ($node['tree_lkey'] < $parent['tree_lkey'] AND $node['right_key'] > $parent['right_key'])
            throw new Exception('Невозможно переместить элемент в один из вложенных');

        // Новые значения элемента
        $move = array(
            'tree_lkey' => self::_getPositionLeftKey($base, $parent, $position),
            'tree_level'    => $parent['tree_level'] + 1,
            'tree_name'     => $node['tree_name'],
            'tree_pid'      => $parent['tree_id'],
        );

        if ($node['tree_pid'] == $parent['tree_id'])
        {
            // Смена порядка внутри одного родителя. Упрощенный алгоритм для ускорения операций

            if ($node['tree_lkey'] == $move['tree_lkey'])
            {
                // Элемент уже находится в нужной позиции
                return TRUE;
            }

            if ($move['tree_lkey'] > $node['tree_lkey'])
            {
				// Перемещаем элемент вперед. Все промежуточные сдвигаем назад
				$shift = ($move['tree_lkey'] - 1) - $node['tree_rkey'];
				
				$treeTable->update(array(
						'tree_rkey' => new K_Db_Expr('IF(`tree_lkey` < '.(int)$node['tree_rkey'].', `tree_rkey` + '.$shift.', `tree_rkey` - '.(int)$node_diff.')'),
						'tree_lkey' => new K_Db_Expr('IF(`tree_lkey` < '.(int)$node['tree_rkey'].', `tree_lkey` + '.$shift.',  `tree_lkey` - '.(int)$node_diff.')'),
					), '`tree_lkey` BETWEEN '.(int)$node['tree_lkey'].' AND '.((int)$move['tree_lkey'] - 1));
			}
            else
            {
				// Перемещаем элемент назад. Все промежуточные сдвигаем вперед
				$shift = $node['tree_lkey'] - $move['tree_lkey'];
				
				$treeTable->update(array(
						'tree_rkey' => new K_Db_Expr('IF(`tree_lkey` < '.(int)$node['tree_lkey'].', `tree_rkey` + '.(int)$node_diff.', `tree_rkey` - '.$shift.')'),
						'tree_lkey' => new K_Db_Expr('IF(`tree_lkey` < '.(int)$node['tree_lkey'].', `tree_lkey` + '.(int)$node_diff.',  `tree_lkey` - '.$shift.')'),
					), '`tree_lkey` BETWEEN '.(int)$move['tree_lkey'].' AND '.(int)$node['tree_rkey']);
			}
        }
        else
        {
            // Перемещение в другой родительский элемент. $move - место элемента, как если бы он вставлялся

            // Проверяем, чтобы URL не повторялся
			$name_occupied = $treeTable->select()
                  ->where('`tree_pid` = '. (int)$parent['tree_id'] .' AND `tree_name` = '. K_Db_Quote::quoteKey($move['tree_name']))
                  ->fetchArray();

            // Если предлагаемое имя сегмента занято, используем свободное имя
            //var_dump($name_occupied);
            
               if (count($name_occupied)){
               // $move['tree_name'] = self::get_available_name($parent['tree_id'], $move['tree_name']);
                     $move['tree_name']="_".base_convert(time()+rand(1,999),10,36);
                     $move['tree_title']="_K";   
                 }
            if ($move['tree_lkey'] > $node['tree_lkey'])
            {
				// Перемещаем вперед. Ориентируемся на правый ключ. Область перемещения: $node['tree_lkey'], $move['tree_rkey']
                $move['tree_lkey'] -= $node_diff;
                $move['tree_rkey'] = $move['tree_lkey'] + $node_diff - 1;

				$shift = $move['tree_rkey'] - $node['tree_rkey'];
				
				$treeTable->update(array(
						'tree_lkey' => new K_Db_Expr('IF(`tree_lkey` < '.(int)$node['tree_rkey'].', IF(`tree_lkey` < '.(int)$node['tree_lkey'].', `tree_lkey`, `tree_lkey` + '.$shift.'), `tree_lkey` - '.(int)$node_diff.')'),
						'tree_rkey' => new K_Db_Expr('IF(`tree_rkey` <= '.(int)$node['tree_rkey'].', `tree_rkey` + '.$shift.', IF(`tree_rkey` <= '.(int)$move['tree_rkey'].', `tree_rkey` - '.(int)$node_diff.', `tree_rkey`))'),
					), '`tree_lkey` BETWEEN '.(int)$node['tree_lkey'].' AND '.(int)$move['tree_rkey'].' OR `tree_rkey` BETWEEN '.(int)$node['tree_lkey'].' AND '.(int)$move['tree_rkey']);
			}
            else
            {
				// Перемещаем назад. Ориентируемся на левый ключ.
                $move['tree_rkey'] = $move['tree_lkey'] + $node_diff - 1;
				
				$shift = $node['tree_lkey'] - $move['tree_lkey'];
				
				$treeTable->update(array(
						'tree_lkey' => new K_Db_Expr('IF(`tree_lkey` < '.(int)$node['tree_lkey'].', IF(`tree_lkey` < '.(int)$move['tree_lkey'].', `tree_lkey`, `tree_lkey` + '.(int)$node_diff.'), `tree_lkey` - '.$shift.')'),
						'tree_rkey' => new K_Db_Expr('IF(`tree_rkey` <= '.(int)$node['tree_lkey'].', `tree_rkey` + '.(int)$node_diff.', IF(`tree_rkey` <= '.(int)$node['tree_rkey'].', `tree_rkey` - '.$shift.', `tree_rkey`))'),
					), '`tree_lkey` BETWEEN '.(int)$move['tree_lkey'].' AND '.(int)$node['tree_rkey'].' OR `tree_rkey` BETWEEN '.(int)$move['tree_lkey'].' AND '.(int)$node['tree_rkey']);
			}

            if ($move['tree_level'] != $node['tree_level'])
            {
                $level_shift = $node['tree_level'] - $move['tree_level'];
				
				$treeTable->update(array(
						'tree_level' => new K_Db_Expr('`tree_level` - ('.$level_shift.')'),
					), '`tree_lkey` BETWEEN '.(int)$move['tree_lkey'].' AND '.(int)$move['tree_rkey']);
            }
			
			$treeTable->update($move, '`tree_id`='.$node['tree_id']);

            $move = array_merge($node, $move);
            self::_fixUrls($move, $parent['tree_link']);
        }
       return true;
    }

    public static function fixPids($startId)
    {
        $treeTable = new K_Tree_Model();
		
		$result = $treeTable->select(array('tree_lkey', 'tree_rkey', 'tree_level'))->where('`tree_id`='.(int)$startId)->fetchArray();
        
        if (count($result) == 0) { 
            throw new Exception('db_err_fixpid db_err_norootd '. $startId);
        }
        
        list($lKey, $rKey, $level) = array($result[0]['tree_lkey'], $result[0]['tree_rkey'], $result[0]['tree_level']);
        
        if (($rKey - $lKey) == 1) return; // Вложенных элементов нет.
        
		$result = $treeTable->select(array('tree_id', 'tree_lkey', 'tree_rkey', 'tree_level'))->where('`tree_lkey` BETWEEN '.$lKey.' AND '.$rKey.' AND `tree_rkey`-`tree_lkey` > 1')->order('tree_level')->fetchArray();
        
        $upds = array(); 
        while (list($k, $f) = each($result)) {
            $upds[] = $f;
        }
		
        // Перебираем все элементы, у которых есть дочерние элементы.
        foreach ($upds as $cUpd) {
			$treeTable->update(array(
					'tree_pid' => $cUpd['tree_id']
				), '`tree_lkey`='.$cUpd['tree_lkey'].' AND `tree_rkey` < '.$cUpd['tree_rkey'].' AND `tree_level`='.($cUpd['tree_level'] + 1));
        }
		
        return true;
    }
    
        
    public static function reStoreTreeKeys($freeKey=0,$pid=0)
    {
        
      $query = new K_Db_Query; 
      if(!is_numeric($pid)||!is_numeric($freeKey)) return false;
      $r=$query->q("select tree_id from tree where tree_pid=".$pid);
       
      if (count($r)>0)   
      foreach($r as $f)
      {
        $id=$f['tree_id'];
      
        $rightKey=self::reStoreTreeKeys($freeKey+1,$id);
        
        if($rightKey===false) 
        {
             return false;
        }
      
       $qr="UPDATE tree SET tree_lkey=".$freeKey.", tree_rkey=".$rightKey."  WHERE tree_id=".$id;
       $query->q($qr);
      
       $freeKey=$rightKey+1;
      }
      return $freeKey;
    }

   

}
?>