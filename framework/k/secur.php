<?php

/**
 * @package    DM
 * @category   Helpers
 */
class K_Secur{

//Добовляет случайные волны к картинке  
   public static function imagewave($im){
    $sx = imagesx($im);
    $sy = imagesy($im);
    $dx = mt_rand(0,$sx/2);
    $xf = mt_rand(-100,100)/20;
    for($x = 0; $x < $sx; $x++){
        $yd = floor(sin(deg2rad($dx+$x)*$xf)*2);
        $l = array();
        for($y = 0; $y < $sy; $y++)
            $l[$y] = imagecolorat($im,$x,$y);
        if($yd>0) for($y = 0; $y < $yd; $y++) array_push($l,array_shift($l));
        elseif($yd<0) for($y = 0; $y > $yd; $y--) array_unshift($l,array_pop($l));
        for($y = 0; $y < $sy; $y++)
            imagesetpixel($im,$x,$y,$l[$y]);
    }
    $dy = mt_rand(0,$sy/2);
    $yf = mt_rand(-100,100)/20;
    for($y = 0; $y < $sy; $y++){
        $xd = floor(sin(deg2rad($dy+$y)*$yf)*2);
        $l = array();
        for($x = 0; $x < $sx; $x++)
            $l[$x] = imagecolorat($im,$x,$y);
        if($xd>0) for($x = 0; $x < $xd; $x++) array_push($l,array_shift($l));
        elseif($xd<0) for($x = 0; $x > $xd; $x--) array_unshift($l,array_pop($l));
        for($x = 0; $x < $sx; $x++)
            imagesetpixel($im,$x,$y,$l[$x]);
    }
 }
// генерирует слово капчи
  public static function genCapchaText(){
    $text = "";
    for($i = 0; $i < 5; $i++){
       // switch(mt_rand(1,3)){
          //  case(1): {$c=chr(rand(ord('1'),ord('0')));break;}
           // case(2): {$c=chr(rand(ord('A'),ord('Z')));break;}
          //  case(3): {$c=rand(0,9);break;}
        //}
		
		$c=rand(0,9);
        $text.=$c;
    }
    return $text;
  }
  
// генерирует слово капчи
  public static function genPassword($max=7){

    $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    
    // Определяем количество символов в $chars
    
    $size=StrLen($chars)-1;
    
    // Определяем пустую переменную, в которую и будем записывать символы.
    
    $password=null;
    
    // Создаём пароль.
    
        while($max--)
        $password.=$chars[rand(0,$size)];
    return $password;
  }
  
  
  
// генерирует капчу
  public static function genCapcha($text, $fileName = false){
     
    $im = imageCreateTrueColor(200,80);
    
    $white = imageColorAllocate($im, mt_rand(170,255),mt_rand(170,255),mt_rand(170,255));
   // $black = imageColorAllocate($im, mt_rand(0,140),mt_rand(0,140),mt_rand(0,140));
    imagefilledrectangle($im,0,0,200,80,$white);
    
    $a = mt_rand(-5,5);
    $s = 40;

    $f = CONFIGS_PATH.'/captcha_fonts/captcha_font'.mt_rand(1,1).".ttf";

    do{
        $s--;
        $b = imagettfbbox ($s, $a, $f, $text);
        $x = $b[2]-$b[0];
        $y = $b[1]-$b[7];
    }while(($x>=200)||($y>=60));
    $black = imageColorAllocate($im, mt_rand(0,140),mt_rand(0,140),mt_rand(0,140));
    imagettftext($im, $s, $a, 100-$x/2, 30+$y/2, $black, $f, $text);
    //self::imagewave($im);
    
    $yd = mt_rand(-30,30);
    
    $black = imageColorAllocate($im, mt_rand(0,140),mt_rand(0,140),mt_rand(0,140));
    imageline($im,0,$yd,200,30+$yd,$black);
  //  self::imagewave($im);
    $black = imageColorAllocate($im, mt_rand(0,140),mt_rand(0,140),mt_rand(0,140));
    imageline($im,0,30+$yd,200,60+$yd,$black);
  //  self::imagewave($im);
    
    
    if($fileName){
        
    }else{
        ob_start();
        imagepng($im);
        $b64 = base64_encode(ob_get_contents());
        ob_end_clean();
        imageDestroy($im);
        return $b64; 
    } 
  } 
  
}
