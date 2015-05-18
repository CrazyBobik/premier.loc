<?php 
class prevnextHelper {
	   
   public function buildArrows($node) {
                
        $prevBro = K_tree::getPrevBro($node['tree_id']);
        $nextBro = K_tree::getNextBro($node['tree_id']);
               
        return '<a href = "javascript:void(0)" '.($prevBro['tree_link']? '' : 'style="display:none"' ).'" id = "arrow-prev" data-link = "'.$prevBro['tree_link'].'" class="arrow-prev-next" >Предыдущий</a>'.
               '<a href = "javascript:void(0)" '.($nextBro['tree_link']? '' : 'style="display:none"' ).'" id = "arrow-next" data-link = "'.$nextBro['tree_link'].'" class="arrow-prev-next" >Следующий</a>';
   }
}

?>