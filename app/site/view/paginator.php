<?php
	/****************************ÏÎÑÒĞÀÍÈ×ÍÎÅ ĞÀÇÄÅËÅÍÈÅ**************************************/
	class Paginator extends Db{
		function select_num($table,$sql_pag){
			$sql ="SELECT * FROM $table $sql_pag";
			$res = $this->sql($sql);
			return $res;
		}
		function select_page_limit($table,$start,$limit,$sql_pag){
			$sql ="select * from $table $sql_pag LIMIT $start, $limit";
			$res = $this->sql($sql);
			return $res;
		}
		function page($table,$limit,$sql_pag){
			$res_pages_num = $this->select_num($table,$sql_pag);
			while($row_pages_num = mysql_fetch_array($res_pages_num)){ 
				if((isset($_GET['ñch2']))&&($_GET['ñch2']!=0)){
					$r=mysql_query("select id from gallery where id_add=".$row_pages_num['id_add']."");
					$r=mysql_fetch_array($r);
					if(empty($r['id'])) continue;
				}
				$num++; 
			}
			$total_pages = $num;
			if($total_pages > $limit){
				$page = mysql_escape_string($_GET['page']);
				if($page){
					$start = ($page - 1) * $limit; 
				}else{
					$start = 0;	
				}
				$res_list = $this->select_page_limit($table,$start,$limit,$sql_pag);
			}else $res_list = $this->select_page_limit($table,0,$limit,$sql_pag);
			return $res_list;
		}
		function pageindex($table,$limit,$link,$sql_pag,$user){
			$res_pages_num = $this->select_num($table,$sql_pag);
			while($row_pages_num = mysql_fetch_array($res_pages_num)){ 
				if((isset($_GET['ñch2']))&&($_GET['ñch2']!=0)){
					$r=mysql_query("select id from gallery where id_add=".$row_pages_num['id_add']."");
					$r=mysql_fetch_array($r);
					if(empty($r['id'])) continue;
				}
				$num++; 
			}
			$total_pages = $num;
            
            K_Registry::write('pages', $total_pages);
            
			$page = mysql_escape_string($_GET['page']);
			$lastpage = ceil($total_pages/$limit);
			$LastPagem1 = $lastpage - 1;
			$stages = 3;
			$prev = 1;			
			$next = $lastpage;	
            
            if ($user == '') { $user = 'undefined'; } else { $user = $user; }
            
			if($total_pages > $limit){
				if($lastpage > 1){	
					$paginate .= "<div id='paginate'>";
                    
                    if($page!=1){
                    	$paginate.= "<a href='#top' onclick='setPagination(1,".$user.")'>".ln('« Ïğåäûäóùàÿ','« Ïîïåğåäíÿ')."</a>";
                    }
					if ($lastpage < 7 + ($stages * 2)){	
						for ($counter = 1; $counter <= $lastpage; $counter++){
							if ($counter == $page){
								$paginate.= "<span class='current'>$counter</span>";
							}else{
								$paginate.= "<a href='#top' onclick='setPagination(".$counter.",".$user.")'>$counter</a>";
							}					
						}
					}
					elseif($lastpage > 5 + ($stages * 2))
					{
						if($page < 1 + ($stages * 2)){
							for ($counter = 1; $counter < 4 + ($stages * 2); $counter++){
								if ($counter == $page){
									$paginate.= "<span class='current'>$counter</span>";
								}else{
									$paginate.= "<a href='#top' onclick='setPagination(".$counter.",".$user.")'>$counter</a>";
								}					
							}
							$paginate.= "...";
							$paginate.= "<a href='#top' onclick='setPagination(".$LastPagem1.",".$user.")'>$LastPagem1</a>";
							$paginate.= "<a href='#top' onclick='setPagination(".$lastpage.",".$user.")'>$lastpage</a>";		
						}
						elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)){
							$paginate.= "<a href='#top' onclick='setPagination(1,".$user.")'>1</a>";
							$paginate.= "<a href='#top' onclick='setPagination(2,".$user.")'>2</a>";
							$paginate.= "...";
							for ($counter = $page - $stages; $counter <= $page + $stages; $counter++){
								if ($counter == $page){
									$paginate.= "<span class='current'>$counter</span>";
								}else{
									$paginate.= "<a href='#top' onclick='setPagination(".$counter.",".$user.")'>$counter</a>";
								}					
							}
							$paginate.= "...";
							$paginate.= "<a href='#top' onclick='setPagination(".$LastPagem1.",".$user.")'>$LastPagem1</a>";
							$paginate.= "<a href='#top' onclick='setPagination(".$lastpage.",".$user.")'>$lastpage</a>";		
						}
						else{
							$paginate.= "<a href='#top' onclick='setPagination(1)'>1</a>";
							$paginate.= "<a href='#top' onclick='setPagination(2)'>2</a>";
							$paginate.= "...";
							for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++){
								if ($counter == $page){
									$paginate.= "<span class='current'>$counter</span>";
								}else{
									$paginate.= "<a href='#top' onclick='setPagination(".$counter.",".$user.")'>$counter</a>";
								}					
							}
						}
					}
                    if($page!=$lastpage){
                    	$paginate.= "<a href='#top' onclick='setPagination(".$lastpage.",".$user.")'>".ln('Ñëåäóşùàÿ »','Íàñòóïíà »')."</a>";
                    }     
                    
					$paginate.= "</div>";
				}
			}
			return $paginate;
		}
	}
	$page = new Paginator();
	/*--------------------------------------------------------------------------------------------*/
?>