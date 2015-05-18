<?php 

// частные функции используемые в данном сайте 

class K_Func{
   
    
    public static function getPaymentSystem($id){
   
        return AllConfig::$paymentConfig[$id];
           
    }
		
	public static function addBalanc($id, $sum, $paySystemId, $order){
					
		$res = k_q::row("select * from users where id=$id");
				
		$balans = floor($res['balans'] + $sum);
		
		//проверка есть ли такой ордер в базе 
		$res = k_q::one("select id from history where ".k_qh::where(array("order_id"=>$order,'pay_system_id'=>$paySystemId)));
		
		if(!$res){ // если нет то пополняем баланс
		
			k_q::query("update users set balans = $balans where ".k_qh::where(array("id"=>$id)));
		
			k_q::query("insert into history (user, date, sum, code_comment, balans, incoming, pay_system_id, order_id) values (".$id.", NOW(), '$sum', 5, '$balans', 1, '$paySystemId', '$order')");
			
			return true;
		}	
		
		return false;
		
	}
    
   public static function treatStreet($street){
		
		return mb_strtolower(str_replace(AllConfig::$chekAds['removeFromStreet'], '', trim($street)), 'utf8');
		
   } 
    
}

?>