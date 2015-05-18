<?php 

class paginatorHelper {
      
    // Вспомогательный метод - используеться перед запросом
    public function prepear($page, $onPage)
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
	public function simple( $page, $cntPages, $url, $class = null ) {
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
        
        
		$html = '<div id="paginator"><p>стр.</p>';

//		if ( $page > 1 ) {
//			$link = str_replace('{#}', ($page-1), $url);
//			$html .= '<p class="news-left">« <a href="'.$link.'" > Назад</a></p>';
//		} else {
//			$html .= '';
//		}
//
        $html .= '<ul>';
		for ( $i=0; $i<$per; ++$i ) {
			$index = $page - 2 + $i;
			if ( $index >= 1 && $index <= $cntPages ) {
			 
				$link = str_replace('{#}', ($index), $url);
                
                if($index==$page){
                  
                     $html .= '<li><a class="active" href="'.$link.'">'.($index).'</a></li>';
                    
                }else{
                   
                     $html .= '<li><a href="'.$link.'">'.($index).'</a>';
                    
                }
      		
			}
		}
        $html .= '</ul>';
//          if ( $page > 0 && $page < $cntPages ) {
//			$link = str_replace('{#}', ($page+1), $url);
//			$html .= '<p class="news-right"><a href="'.$link.'" >Вперед </a> »</p>';
//		} else {
//			$html .= '';
//		}
                
     	$html .= '<div class="clear"></div></div>';
		return $html;
	}

	public function mini( $page, $cntPages, $url, $class = null ) {
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