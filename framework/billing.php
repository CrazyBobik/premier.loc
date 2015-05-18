<?php
http://mpr.div-studio.com.ua/api/getcontainer/
/**
 * @package    DM
 * @category   Helpers
 */
class Billing {
    
    protected static $_adminCost = null;

	protected static $_userCost = null;
	
	protected static $_isInit = false;

  protected static function init() {
		if ( self::$_isInit ) return;
	       
            $prices=K_TreeQuery::gOne('/payment/accountscost/' ,'prices');
            self::$_adminCost = $prices['admin_cost'];
            self::$_userCost = $prices['user_cost'];
    
		self::$_isInit = true;
	 } 
     
   public static function getAdminCost(){
     self::init();   
     return self::$_adminCost;     
        
   }  
    
   public static function getUserCost(){
     self::init();    
     return self::$_userCost;  
        
   }    

   public static function daysLeft(){
       self::init();
    
       $costPerMounth = self::costPerMounth();
       $costPerDay = round($costPerMounth/30, 2);
            
       K_Auth::setUserKey('cost_per_mounth',$costPerMounth); 
       K_Auth::setUserKey('cost_per_day',$costPerDay);              
                        
       if (K_Auth::getUserInfo('org_balance')>0){
          $daysLeft = floor(K_Auth::getUserInfo('org_balance')/$costPerDay);
       }else{
         $daysLeft = 0;
       }  
                            
       K_Auth::setUserKey('days_left',$daysLeft);
   }
   
   
    //Стоимость оплаты одного пользователя за один день
   public static function userPerDay(){
      self::init();
      return round(self::$_userCost/30,2);
   }
   
    //Стоимость оплаты одного админа за один день
   public static function adminPerDay(){
      self::init();
      
      return round(self::$_adminCost/30,2);
   }
   
   public static function costPerMounth(){
       self::init();
       
       $client = new Admin_Model_Client;
       $orgClientsCountResult = $client->fetchAll(K_Db_Select::create('client_level')->where(array('client_organization' =>K_Auth::getUserInfo('organization'))));
                                   
       $orgAdminsCount=0;
       $orgUsersCount=0;
                         
       foreach($orgClientsCountResult as $v){
                                    
           if($v['client_level']=='admin'){
               $orgAdminsCount++;
                                           
           }else{
               $orgUsersCount++;
           }
       }
       return $orgAdminsCount*self::$_adminCost+$orgUsersCount*self::$_userCost;
   }
    
   public static function balanceRemove($count){
          self::init();
          //снимаем деньги со счёта
          $organizationModel = new Admin_Model_Organization;
          $organizationModel->update(array('organization_pay_balance'=>K_Db_Quote::quote(K_Auth::getUserInfo('org_balance')-$count)),
                                                   'organization_id='.K_Auth::getUserInfo('organization'));
          $journalData['p_org']    = K_Auth::getUserInfo('organization');                                      
          $journalData['p_client'] = K_Auth::getUserInfo('id');                     
          $journalData['p_balance'] = K_Auth::getUserInfo('org_balance');   
          $journalData['p_count']  = $count;   
          $journalData['p_admins'] = 0;   
          $journalData['p_users']  = 1;                                   
          $journalData['p_acost']  = self::$_adminCost;     
                                              
          $journal = new Admin_Model_PayJournal;
          $journal->save($journalData);
          
          K_Auth::setUserKey('org_balance', K_Auth::getUserInfo('org_balance')-$count);
  }
 
}
