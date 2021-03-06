<?php
class K_Paginator{
    
     // Вспомогательный метод - используеться перед запросом
    public static function prepear($page, $onPage)
    {

        if (!$onPage) {

            $onPage = 20;

        }

        if ($page) {

            $start = $page * $onPage - $onPage;

        } else {

            $start = 0;
            $page = 1;

        }

        return array(
            'start' => $start,
            'page' => $page,
            'onPage' => $onPage
        );
    }

    /**
     * Метод возвращает html для простой постраничной навигации (вперед-назад)
     *
     * @param Int $page			page
     * @param Int $cntPages		total pages
     * @param string $url		url for str_replace -> {#} - number of the page
     * @return string
     */
    public static function simple( $page, $cntPages, $url, $class = null ) {
        /*
        <div class="paginator">
        <a href="#"><span class="zoom-arrow">&larr;</span> назад</a>
        <a href="#">далее <span class="zoom-arrow">&rarr;</span></a>
        <br/><br/>
        <span class="mini">Страницы:</span>
                <a href="#">1</a>
            <a href="#">2</a>
            <a class="active" href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
        </div>
        */

        $per = 7;

        if ($cntPages <= 1) return '';

        $min = 1;


        $html = '<div class="paginator">';

        $html .= '<ul>';
		if ( $page > 1 ) {
            if ($page == 2){
                $link = preg_replace('/[&|?]page={#}/', '', $url);
            } else {
                $link = str_replace('{#}', ($page-1), $url);
            }
            $html .= '<a href="'.$link.'" ><li>Назад</li></a>';
            K_SEO::setPrev($link);
        } else {
            $html .= '';
        }


        for ( $i=0; $i<$per; ++$i ) {
            $index = $page - 2 + $i;
            if ( $index >= 1 && $index <= $cntPages ) {

                if ($index == 1){
                    $link = preg_replace('/[&|?]page={#}/', '', $url);
                } else {
                    $link = str_replace('{#}', ($index), $url);
                }
                if($index==$page){

                    $html .= '<a href="'.$link.'"><li class="active">'.($index).'</li></a>';

                }else{

                    $html .= '<a href="'.$link.'"><li>'.($index).'</li></a>';

                }

            }
        }

          if ( $page > 0 && $page < $cntPages ) {
			$link = str_replace('{#}', ($page+1), $url);
			$html .= '<li><a href="'.$link.'" >Вперед</a></li>';
              K_SEO::setNext($link);
		} else {
			$html .= '';
		}
        $html .= '</ul>';

        $html .= '<div class="clear"></div></div>';
        return $html;
    }


    public static function mini( $page, $cntPages, $url, $class = null ) {
      /*
      <div class="paginator">
      <a href="#"><span class="zoom-arrow">&larr;</span> назад</a>
      <a href="#">далее <span class="zoom-arrow">&rarr;</span></a>
      <br/><br/>
      <span class="mini">Страницы:</span>
        <a href="#">1</a>
       <a href="#">2</a>
       <a class="active" href="#">3</a>
       <a href="#">4</a>
       <a href="#">5</a>
      </div>
      */
    
      if ($cntPages <= 1) return '';
    
      $min = 1;
      $html = '<div class="paginator'.(!empty($class)?' '.$class:'').'" style="text-align:center">';
    
      if ( $page > 1 ) {
       $link = str_replace('{#}', ($page-1), $url);
       $html .= '<a href="'.$link.'"><span class="zoom-arrow">&larr;</span> назад</a>';
      } else {
       $html .= '';
      }
    
      if ( $page > 0 && $page < $cntPages ) {
       $link = str_replace('{#}', ($page+1), $url);
       $html .= '<a href="'.$link.'">вперед <span class="zoom-arrow">&rarr;</span></a>';
      } else {
       $html .= '';
      }
    
      $html .= '</div>';
      return $html;
      
     } 
     
}


?>