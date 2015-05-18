<div class="mask1"></div>
	<!-- Start SiteHeart code -->
<script>
(function(){
var widget_id = 749726;
_shcp =[{widget_id : widget_id}];
var lang =(navigator.language || navigator.systemLanguage 
|| navigator.userLanguage ||"en")
.substr(0,2).toLowerCase();
var url ="widget.siteheart.com/widget/sh/"+ widget_id +"/"+ lang +"/widget.js";
var hcc = document.createElement("script");
hcc.type ="text/javascript";
hcc.async =true;
hcc.src =("https:"== document.location.protocol ?"https":"http")
+"://"+ url;
var s = document.getElementsByTagName("script")[0];
s.parentNode.insertBefore(hcc, s.nextSibling);
})();
</script>
<!-- End SiteHeart code -->
<?php

if(isset($_GET['qgpr'])){
		$text=base64_decode($_GET['qgpr']);
		$ar=explode("_qwxpp_",$text);
		$boss=$ar[0];
		$rab=$ar[1];
		k_q::query("update branch set p=1 where boss=$boss and rab=$rab");
}

if ((isset($_GET['is_nov']))&&($_GET['is_nov'] == 'nov')) {
        echo '<input type="hidden" value="nov" id="is_nov" name="is_nov" />';
}
	
    

?>

<div class="header">
                          <?php if(true){?>
                            	
								   <div id="slides-top" style="display:none;">
								   		<div id="banner-slide">
											<ul class="bjqs">
											 	<li><a content="noindex" rel="nofollow" href="/reg" target="_blank"><img src="/img/slider/1/reg.png" /></a>
												</li>
												<li><a content="noindex" rel="nofollow" href="/real-estate-services/real-estate" target="_blank"><img src="/img/slider/1/catalog.png" /></a>
												</li>
                                                <li><a content="noindex" rel="nofollow" href="/news/84" target="_blank"><img src="/img/slider/1/friend.png" /></a>
                                                </li>
												<?if(false){?>
                                                   <li><a content="noindex" rel="nofollow" target="_blank"><img src="/img/slider/may.png" /></a>
                                                    </li>
                                                <?}?>
                                                <?if(false){?>
                                                    <li><a content="noindex" rel="nofollow" target="_blank"><img src="/img/slider/9-may.jpg" /></a>
                                                    </li>
                                                <?}?>

											</ul>
										</div>
									</div>		 
								
                           <?}?>
			
		
		<div id="logo">
			<img src="/img/system/logo_new_year.png" width="180" onclick='self.location="/"' />
		</div>
		
		<?php // if($_SESSION['lang']=='ru'){ <div class="langlink">?>
			
				<!--<a title="Перейти на украинскую версию сайта" href="<?php echo $ua ?>"><img alt="" src="../../img/system/ua.png" /></a><img alt="" src="../../img/system/ru.png" />-->
			
		<?php //</div>// } ?>
	
		<?php 
	  	    if($_SERVER['REQUEST_URI']!='/') echo  '<div class="main-link-cont" ><a href="/" class="main-link">На главную</a></div>';
		?>
		
		<div id="infolog">
		
		   <?php   
			 require_once(CHUNK_PATH.'/accountform.php');
		   ?>
		
		</div>
	
	
</div>

<div style="clear: both;"></div>