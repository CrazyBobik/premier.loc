<?php 

class paginatorHelper {

	/**
	 * Функция возвращает html для простой постраничной навигации (вперед-назад)
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
		$html = '<div class="paginator'.(!empty($class)?' '.$class:'').'">';

		if ( $page > 1 ) {
			$link = str_replace('{#}', ($page-1), $url);
			$html .= '<a href="'.$link.'"><span class="zoom-arrow">&laquo;</span>Назад</a>';
		} else {
			$html .= '';
		}

		if ( $page > 0 && $page < $cntPages ) {
			$link = str_replace('{#}', ($page+1), $url);			
			$html .= '<a href="'.$link.'">Вперед<span class="zoom-arrow">&raquo;</span></a>';
		} else {
			$html .= '';
		}

		$html .= '<div class="pages">';

                $html .= '<span class="mini">Страницы:</span>&nbsp;';
                
                if ( $page > 3 ) {
                    $html .= '<a class="page-link" href="'.str_replace('{#}', 1, $url).'">1</a>...&nbsp;';
                }
                
		for ( $i=0; $i<$per; ++$i ) {
			$index = $page - 2 + $i;
			if ( $index >= 1 && $index <= $cntPages ) {
				$link = str_replace('{#}', ($index), $url);
				$html .= '<a class="page-link'.($index==$page?' active':'').'" href="'.$link.'">'.($index).'</a>';
			}
		}
                
                if ( $page + $per - 2 <= $cntPages ) {
                    $lastIndex = $cntPages;
                    $html .= '&nbsp;...&nbsp;<a class="page-link" href="'.str_replace('{#}', ($lastIndex), $url).'">'.$lastIndex.'</a>';
                }

		$html .= '</div><div class="cl"></div></div>';
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