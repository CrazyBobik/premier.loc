<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Access {

    private static $acl = null;
    private static $aclTree = null;
    private static $lastKnowResourse = null;
  
    public static $test = 0;
    

    protected static $_isInit = false;
    protected static $_isInitTree = false;

    protected static function init() {
        if (self::$_isInit) return;
        self::load(false);
        self::$_isInit = true;
    }
   

    protected static function initTree() {
        if (self::$_isInitTree) return;
        self::loadAclTree(false);
        self::$_isInitTree = true;
    }

    public static function load($reload = false) {
        //create Acl Obj
        self::$acl = new K_Acl();

        if (!$reload) {
         
            // зугрузка сразу всего ACL 
            $unlimCache = K_Cache_Manager::get('unlim');
            if($unlimCache->test('ACL')) {
            self::$acl = $unlimCache->load('ACL');
            return;
            }
           
             /*// если во время раздельной загрузки возникли проблемы то пересоздадим обьект ACL и продолжим загрузку обычно 
             if(self::loadAclArrays('ACL',self::$acl))
              {
                return;
              }else{
                self::$acl = new K_Acl(); 
              }*/
        }

        $query = new K_Db_Query();
        $sql = "SELECT DISTINCT r.role_acl_key AS role, p.role_acl_key AS parent FROM role AS r LEFT JOIN role AS p ON r.role_status = 1 AND p.role_status = 1 AND r.role_parent_id = p.role_id ORDER BY r.role_level";
        $allroles = $query->q($sql);

        foreach ($allroles as $v) {
            if ($v['parent'] == null) {

                self::$acl->addRole(new K_Acl_Role($v['role']));

            } else {

                self::$acl->addRole(new K_Acl_Role($v['role']), $v['parent']);

            }

        }

       $sql = "SELECT DISTINCT r.resource_name as resource, r.resource_deny_action as deny_action, p.resource_name as parent FROM resource as r LEFT JOIN resource as p ON r.resource_parent_id = p.resource_id AND r.resource_status = 1 and p.resource_status = 1 WHERE r.resource_status = 1  ORDER BY r.resource_level";

        $allResources = $query->q($sql);
        foreach ($allResources as $v) {
            if ($v['parent'] == null) {

                self::$acl->add(new K_Acl_Resource($v['resource']), null, $v['deny_action']);

            } else {

                self::$acl->add(new K_Acl_Resource($v['resource']), $v['parent'], $v['deny_action']);

            }
        }

        //add rules
       $sql = "SELECT DISTINCT rule.rule_id as ruleId, rule.rule_type as ruleType, role.role_acl_key as role, resource.resource_name as resource, privilege.privilege_name as privilege 
            FROM rule, role, resource, privilege 
            WHERE rule.rule_status = 1 
                  and role.role_status = 1 
                  and resource.resource_status = 1 
                  and privilege.privilege_status = 1 
                  and rule.rule_role_id = role.role_id 
                  and rule.rule_resource_id = resource.resource_id 
                  and rule.rule_privilege_id = privilege.privilege_id 
                  ORDER BY rule.rule_order";

        $allRules = $query->q($sql);

        foreach ($allRules as $v) {

            switch ($v['ruleType']) {

                case 'allow':

                    self::$acl->allow($v['role'], $v['resource'], $v['privilege']);

                    break;

                case 'deny':

                    self::$acl->deny($v['role'], $v['resource'], $v['privilege']);

                    break;
            }
        }

        K_Cache_Manager::get('unlim')->save('ACL', self::$acl);
        
        //self::saveAclArrays('ACL',self::$acl);
    }
    
    private static function loadAclArrays($pref,&$aclObj) {
        $unlimCache=K_Cache_Manager::get('unlim');
          
        if (!$aclObj->putResources($unlimCache->load($pref."RE"))) {
             return false;
        }
        
        if (!$aclObj->putRoles($unlimCache->load($pref."RO"))) {
             return false;
        }
        if (!$aclObj->putRules($unlimCache->load($pref."RU"))) {
             return false;
        }
        return true;
    }
    
    private static function saveAclArrays($pref,&$aclObj) {
        
        $grabResources= $aclObj->grabResources();
        $grabRoles= $aclObj->grabRoles();
        $grabRules= $aclObj->grabRules(); 
        
      K_Cache_Manager::get('unlim')->save($pref."RE", $grabResources);
      
      K_Cache_Manager::get('unlim')->save($pref."RO",$grabRoles);
      
      K_Cache_Manager::get('unlim')->save($pref."RU", $grabRules);
    }
    

    public static function loadAclTree($reload = false) {
        //create Acl Obj
        self::$aclTree = new K_Acl();

          if (!$reload) {
         
            /*// зугрузка сразу всего ACL 
            $unlimCache = K_Cache_Manager::get('unlim');
            if($unlimCache->test('ATR')) {
            self::$aclTree = $unlimCache->load('ATR');
            return;
            }*/
           
             // если во время раздельной загрузки возникли проблемы то пересоздадим обьект ACL и продолжим загрузку обычно 
             if(self::loadAclArrays('ATR',self::$aclTree))
              {
                return;
                
              }else{
                self::$aclTree = new K_Acl(); 
              }
        }

        $query = new K_Db_Query;
        $sql = "SELECT DISTINCT r.role_acl_key AS role, p.role_acl_key AS parent FROM role AS r LEFT JOIN role AS p ON r.role_status = 1 AND p.role_status = 1 AND r.role_parent_id = p.role_id ORDER BY r.role_level";
        $allroles = $query->q($sql);
        foreach ($allroles as $v) {
            if ($v['parent'] == null) {

                self::$aclTree->addRole(new K_Acl_Role($v['role']));

            } else {

                self::$aclTree->addRole(new K_Acl_Role($v['role']), $v['parent']);
            }
        }
        
        //add rules
        $sql = "SELECT DISTINCT tree_rule.tree_rule_id as tree_ruleId, tree_rule.tree_rule_resource_id, tree_rule.tree_rule_type as tree_ruleType, role.role_acl_key as role, privilege.privilege_name as privilege 
            FROM tree_rule, role, tree, privilege 
            WHERE tree_rule.tree_rule_status = 1 
                  and role.role_status = 1 
                  and privilege.privilege_status = 1 
                  and tree_rule.tree_rule_role_id = role.role_id 
                  and tree_rule.tree_rule_resource_id = tree.tree_id 
                  and tree_rule.tree_rule_privilege_id = privilege.privilege_id 
                  ORDER BY tree_rule.tree_rule_order";

           $allRules = $query->q($sql);
           self::aclTreeloadRules($allRules);
        
    }  
    
    public static function aclTreeReloadBrunch($allRules){
            if (!self::$_isInitTree)return false;
            K_Access::aclTreeLoadRules($allRules); 
    }
    
    // загружает правила и только необходимые ресурсы 
    private static function aclTreeLoadRules($allRules){
         foreach ($allRules as $v) {

            $nodeIds = K_Tree::getParents($v['tree_rule_resource_id']);

            $nodeIdsArr = $nodeIds;

            $nodeIdsArr[] = $v['tree_rule_resource_id'];

            $fullNodeResourse = implode('/', $nodeIdsArr); //полный ресурс ноды

            if (! self::$aclTree->has($fullNodeResourse)) { // грузим ресурсы только если нода не загруженна.
                // проверяем есть ли у ноды предки
                if (count($nodeIds) > 0) {
                    $nodeIds[] = $v['tree_rule_resource_id'];

                    $tmpIds = $nodeIds;
                    //проверяем на сколько загруженна ветка ресурса
                    $i = count($nodeIds) - 1;
                    foreach ($nodeIds as $k => $n) {
                        $i--;
                        array_pop($tmpIds);
                        $rosourceParent = implode('/', $tmpIds); // ресурс предка ноды
                        if (self::$aclTree->has($rosourceParent)) {
                            break;
                        }
                    }

                    // echo "*******\n";

                    $resourseArr = $tmpIds; // стартовая позиция с которой загружены ресурсы
                    // догружаем ресурсы ветки ноды вместе с нодой
                    for ($m = $i + 1; $m < count($nodeIds); $m++) {

                        $resourseArr[] = $nodeIds[$m];
                        $resourse = strtolower(implode('/', $resourseArr));

                        //  echo $rosourceParent."-parent\n";
                        //  echo $resourse."-node\n";


                        self::$aclTree->add(new K_Acl_Resource($resourse), $rosourceParent == '' ? null : $rosourceParent, null);
                        $rosourceParent = $resourse;
                    }
                } else { //нет предков сразу грузим ресурс без предка, если он не загружен
                    if (! self::$aclTree->has($v['tree_rule_resource_id'])) self::$aclTree->add(new K_Acl_Resource($v['tree_rule_resource_id']), null, null);
                }
            }
            //  echo $v['role']."    ".$fullNodeResourse.'  '.$v['privilege']." ". $v['tree_ruleType'] ;
            //устанавливамем правило
            switch ($v['tree_ruleType']) {
                case 'allow':
                    self::$aclTree->allow($v['role'], $fullNodeResourse, $v['privilege']);
                    break;

                case 'deny':
                    self::$aclTree->deny($v['role'], $fullNodeResourse, $v['privilege']);
                    break;
            }
        }  
    
      //K_Cache_Manager::get('unlim')->save('ATR', self::$aclTree);
    
      self::saveAclArrays('ATR',self::$aclTree); 
         
    }
    
    
    // проверка на доступ к дереву
    public static function accessTree($nodeId,$privilege = null,$addNodes = false) {
        
        self::initTree();
        $nodeArr = K_tree::getParents($nodeId); // выбираем всех родителей ноды
        $nodeArr[] = $nodeId; // добовляем id самой ноды


        $allowTrigger = false;
        $access = false; // по умолчанию доступ запрещён
        //проверяем на доступ ноду со всеми её родителями к которой запрашиваються потомки
        // проверка идёт с верху вниз
        /**
         *@todo протестировать проверку с низу-вверх, как быстрей ?
         */
        // c низу вверх(должно работать быстрей);
        
        
        //проверка не самой ноды а сразу ноды родителя(необходимо для привелегий add addremove) 
        if( $addNodes){
              array_pop($nodeArr);   
        }
		
         $i=count($nodeArr);
         for($i;$i>0;$i--){
            $resourse = implode('/', $nodeArr);     
            array_pop($nodeArr);   
            $access=K_Access::aclTree()->isAllowed(K_Auth::getRoles(), $resourse, $privilege);
        
            if(K_Access::aclTree()->lastResource){
             break;  
            }
         }         
         
       /*  // проверка с верху ввниз         
        foreach ($nodeArr as $v) {
            $resourseArr[] = $v;

            $resourse = implode('/', $resourseArr);
            $access = K_Access::aclTree()->isAllowed(K_Auth::getRoles(), $resourse, null);

            if (K_Access::aclTree()->lastResource == null) {
                $access = $allowTrigger;
            } else {
                $lastKnowResourse = K_Access::aclTree()->lastResource;
            }
            
            $allowTrigger = $access;
        }*/
        return $access;
    }
  
    public static function accessSiteCheck($res, $privilege ='view') {
        self::init();
        
        self::$lastKnowResourse = null;
        
		
		
        if (is_string($res)){
        $res=explode('/',$res);  
        }
        
        $allowTrigger = false;
        $access = false;
        
        $roles = K_Auth::getRoles();
		// var_dump(   $roles);
		//  var_dump(  $resourse);
		//var_dump( $privilege);  
        foreach ($res as $v) {
            if (is_string($v)) {
                $resourseArr[] = $v;
                $resourse = strtolower(implode('/', $resourseArr));
			//	 var_dump( $resourse);
				
                $access = K_Access::acl()->isAllowed($roles, $resourse, $privilege);
               
 //var_dump(   $access);
				
			   if (K_Access::acl()->lastResource == false) {
                    $access = $allowTrigger;
                } else {
                     self::$lastKnowResourse = K_Access::acl()->lastResource;
                }
                $allowTrigger = $access;
            }
        }
		//exit();
       return  $access;
    }
        
    public static function accessSite($res, $privilege ='view') {
	
       $access = self::accessSiteCheck($res, $privilege);
	   
        if (! $access) {
            if (isset(self::$lastKnowResourse)) {
                $denyAction = K_Access::acl()->getDeneyAction(self::$lastKnowResourse);
                if ($denyAction) {
                    if ($resourse != $denyAction) K_Request::redirect($denyAction);

                } else {
                    if ($resourse != 'default/index/index') K_Request::redirect('/');
                }
            } else {
                if ($resourse != 'default/index/index') K_Request::redirect('/');
            }
        }
    }

    public static function acl() {
        self::init();
        return self::$acl;
    }

    public static function aclTree() {
        self::initTree();
        return self::$aclTree;
    }


}
