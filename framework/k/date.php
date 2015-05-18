<?php

 
class K_Date {


   protected static $instance = null;
   protected $date = null;
   protected $dateArray = null;

   //@array Русские названия месяцев именительный падеж
   public static $ruMonthI = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
   
   //@array Русские названия месяцев родительный падеж
   public static $ruMonthR = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
   
   //@array Украинские названия месяцев именительный падеж
   public static $ukrMonthI = array('січень', 'лютий', 'березень', 'квiтень', 'травень', 'червень', 'липень', 'серпень ', 'вересень', 'жовтень', 'листопад', 'грудень');
  
   //@array Украинские названия месяцев родительный падеж
   public static $ukrMonthR = array('січеню', 'лютому', 'березеню', 'квiтеню', 'травеню', 'червеню', 'липню', 'серпень ', 'вересень', 'жовтень', 'листопад', 'грудень');
  
   protected function __construct($date = flase){
	   // require_once (WWW_PATH . '/ExtProce/debug/xhprof/xhprof_lib/utils/xhprof_lib.php');
       // require_once (WWW_PATH . '/ExtProce/debug/xhprof/xhprof_lib/utils/xhprof_runs.php');  
       //  xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);  
    
        if(!$date){
                    
          $this->dateArray = self::dateParse(time());
            
        }else{
            
          $this->dateArray = self::dateParse($date);
            
        }
  
        self::$instance = $this;
  }
       
/**
 * разбивает дату в разных форматах на составляющие, год должен быть записан полностью 
 * @return array('ts'=>mktime(0,0,0,$m[3], $m[1], $m[5]),
                 'm'=>$m[3],
                 'd'=>$m[1],
                 'y'=>$m[5])
*/
  
   public static function dateParse($str){
      
       //если дата таймштамп
    
       if (is_int($str)){ // проверяем строку 
       
          return array(
                      'ts'=>$str,
                      'm'=>date('m', $str),
                      'd'=>date('d', $str),
                      'y'=>date('Y', $str),
                      'h'=>date('H', $str),
                      'i'=>date('i', $str),
                      's'=>date('s', $str),
                   ); 
                   
       } else if (preg_match("/(\d{2})(\/|\-|\.)(\d{2})(\/|\-|\.)(\d{4})/", $str, $m)){   // проверяем строку на соответствие формату даты, с указанием подмасок   09.07.2008
         
          return array(
                      'ts'=>mktime(0,0,0,$m[3], $m[1], $m[5]),
                      'm'=>$m[3],
                      'd'=>$m[1],
                      'y'=>$m[5]
                   ); 
       }
    
       else if (preg_match("/(\d{4})(\/|\-|\.)(\d{2})(\/|\-|\.)(\d{2})/", $str, $m)){ /// 2008/09/07
          
            return array(
                      'ts'=>mktime(0,0,0,$m[3], $m[5], $m[1]),
                      'm'=>$m[3],
                      'd'=>$m[5],
                      'y'=>$m[1]
                   );
                    
       }elseif (($timestamp = strtotime($str)) !== false) {
        
          return array(
                      'ts'=>$timestamp,
                      'm'=>date('m', $str),
                      'd'=>date('d', $str),
                      'y'=>date('Y', $str)
                   );
      } 
       else{ 
        
           return FALSE;
           
       }
   }
   
   
   public static function midDay($date = false){
         
        if(!$date){
            
            return  date('Y')."-".date('m')."-".date('d').' 12:00:00'; 
            
        }else{
         
           $dateArry = self::dateParse($date);
         
           return  $dateArry['y']."-".$dateArry['m']."-".$dateArry['d'].' 12:00:00'; 
         
        }
        
   }
   
     /**
     *Приводит дату к формату который понимает Mysql
     * 
     */
     
    public static function dateMysql($str){
        
      if($date = self::dateParse($str)){
       
         return $date['y'].'-'.$date['m'].'-'.$date['d'];
     
      }
      else{ 
        
         return FALSE;
         
      }
      
    }
    
     /**
     *Приводит дату к формату который понимает sql
     * 
     */
    
    public function format($format){
            
          if($format=='sql'){
         
            return $this->dateArray['y']."-".$this->dateArray['m']."-".$this->dateArray['d'].' '.$this->dateArray['h'].':'.$this->dateArray['i'].':'.$this->dateArray['s']; 
            
          }elseif($format=='dotted'){
            
            return $this->dateArray['y'].".".$this->dateArray['m'].".".$this->dateArray['d'].' '.$this->dateArray['h'].':'.$this->dateArray['i'].':'.$this->dateArray['s']; 
            
          }else{
             
            return date($format, $this->dateArray['ts']);
            
          }
    }    
    
    /**
     *Приводит дату к формату записи через точку 09.07.2008
     * 
     */
     
    public static function dateDotted($str){
      if($date = self::dateParse($str)){
        return $date['d'].'.'.$date['m'].'.'.$date['y'];
      }
      else{   
         return FALSE;
      }
   }
      
    
    
     /**
     * массив месяцев для ui датапикера
     * monthNames:['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря']
     *  
     */
    //возвращяет номер месяца
    
    public static function russianDateNumered($date) {
        $monthArr = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');

        foreach ($monthArr as $k => $v) {
            $dateNumered = str_replace($v, $k + 1, $date);
        }

        return $dateNumered;
    }

    public static function ruMonthNum($month) {
        $monthArr = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        $num = false;
        $i = 1;
        foreach ($monthArr as $v) {
            if ($month == $v) {
                $num = $i;
                break;
            }

            $i++;
        }

        return $num;
    }
    
  
    public static function uaMonthNum($month) {
        $monthArr = array('січень', 'лютий', 'березень', 'квiтень', 'травень', 'червень', 'липень', 'серпень ', 'вересень', 'жовтень', 'листопад', 'грудень');
        $num = false;
        $i = 1;
        foreach ($monthArr as $v) {
            if ($month == $v) {
                $num = $i;
                break;
            }

            $i++;
        }

        return $num;
    }
    
    
    // Переводит порядковый номер дня недели в название дня недели, от 0 (воскресенье) до 6 (суббота)
     public static function ruWeekWord($weekNum) {
        $weekArr = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');
        return $weekArr[$weekNum];
    }
    
    // Переводит порядковый номер месяца в название месяца, от 0 (января) до 11 (декабря)
     public static function ruMonthWord($monthNum) {
       $monthArr = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        return $monthArr[intval($monthNum)];
    }
    
     // Переводит порядковый номер месяца в название месяца, от 0 (января) до 11 (декабря)
     public static function uaMonthWord($monthNum) {
      $monthArr = array('','січнеь', 'лютий', 'березень', 'квiтень', 'травень', 'червень', 'липень', 'серпень ', 'вересень', 'жовтень', 'листопад', 'грудень');
        return $monthArr[intval($monthNum)];
    }

    public static function russianMonthName($date) {
        $translation = array(
            "January" => "Январь",
            "Jan" => "Январь",
            "February" => "Февраль",
            "Feb" => "Февраль",
            "March" => "Март",
            "Mar" => "Март",
            "April" => "Апрель",
            "Apr" => "Апрель",
            "May" => "Май",
            "May" => "Май",
            "June" => "Июнь",
            "Jun" => "Июнь",
            "July" => "Июль",
            "Jul" => "Июль",
            "August" => "Август",
            "Aug" => "Август",
            "September" => "Сентябрь",
            "Sep" => "Сентябрь",
            "October" => "Октябрь",
            "Oct" => "Октябрь",
            "November" => "Ноябрь",
            "Nov" => "Ноябрь",
            "December" => "Декабрь",
            "Dec" => "Декабрь",
        );

        return strtr($date, $translation);
    }

    //Перверодит англискую дату и время на русский
    public static function russianDateI8($date) {
        $translation = array(
            "am" => "дп",
            "pm" => "пп",
            "AM" => "ДП",
            "PM" => "ПП",
            "Monday" => "Понедельник",
            "Mon" => "Пн",
            "Tuesday" => "Вторник",
            "Tue" => "Вт",
            "Wednesday" => "Среда",
            "Wed" => "Ср",
            "Thursday" => "Четверг",
            "Thu" => "Чт",
            "Friday" => "Пятница",
            "Fri" => "Пт",
            "Saturday" => "Суббота",
            "Sat" => "Сб",
            "Sunday" => "Воскресенье",
            "Sun" => "Вс",
            "January" => "января",
            "Jan" => "января",
            "February" => "февраля",
            "Feb" => "февраля",
            "March" => "марта",
            "Mar" => "марта",
            "April" => "апреля",
            "Apr" => "апреля",
            "May" => "мая",
            "May" => "мая",
            "June" => "июня",
            "Jun" => "июня",
            "July" => "июля",
            "Jul" => "июля",
            "August" => "августа",
            "Aug" => "августа",
            "September" => "сентября",
            "Sep" => "сентября",
            "October" => "октября",
            "Oct" => "октября",
            "November" => "ноября",
            "Nov" => "ноября",
            "December" => "декабря",
            "Dec" => "декабря",
            "st" => "ое",
            "nd" => "ое",
            "rd" => "е",
            "th" => "ое");

        return strtr($date, $translation);
    }

    /**
     * 
     * @todo Почиcтить или cделать что-бы ввыводил таблицу
     * 
     * 
     * 
     */
    public static function calendarMonth($year, $month) {
        $skip = date("w", mktime(0, 0, 0, $month, 1, $year)) - 1; // узнаем номер дня недели
        if ($skip < 0) {
            $skip = 6;
        }
        $daysInMonth = date("t", mktime(0, 0, 0, $month, 1, $year));   // узнаем число дней в месяце

        $calendarDays = array();    // обнуляем calendar boday
        $day = 1;       // для цикла далее будем увеличивать значение

        for ($i = 0; $i < 6; $i++) { // Внешний цикл для недель 6 с неполыми строками
            $calendar_body .= '<tr>';       // открываем тэг строки
            for ($j = 0; $j < 7; $j++) {      // Внутренний цикл для дней недели
                if (($skip > 0) or ($day > $daysInMonth)) { // выводим пустые ячейки до 1 го дня ип после полного количства дней
                    $calendarDays[] = null;
                    $calendar_body .= '<td class="none"> </td>';
                    $skip--;
                } else {

                    $calendarDays[] = $day;

                    if ($j == 0)     // если воскресенье то омечаем выходной
                        $calendar_body .= '<td class="holiday">' . $day . '</td>';
                    else {   // в противном случае просто выводим день в ячейке
                        if ((date(j) == $day) && (date(m) == $month) && (date(Y) == $year)) {//проверяем на текущий день
                            $calendar_body .= '<td class="today">' . $day . '</td>';
                        } else {
                            $calendar_body .= '<td class="day">' . $day . '</td>';
                        }
                    }
                    $day++; // увеличиваем $day
                }
            }
            $calendar_body .= '</tr>'; // закрываем тэг строки
        }
        return $calendarDays;
    }
    
   	// Get K_Date instance
    
	public static function load($date) {
	   
			return new K_Date($date);
      
	} 
        
}
