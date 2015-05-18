<?php

/**

 * crumbs

 */

class K_Crumbs

{
	/**

	 * crumbs

	 * 

	 * (default value: array())

	 * 

	 * @var array

	 * @access private

	 * @static

	 */

	private static $crumbs = array();
	

	/**

	 * clear function.

	 * 

	 * @access public

	 * @static

	 * @return void

	 */

	public static function clear()

	{

		self::$crumbs = array();

	}

	

	/**

	 * get function.

	 * 

	 * @access public

	 * @static

	 * @return array crumbs

	 */

	public static function get()

	{

		return self::$crumbs;

	}

	/**

	 * add function.

	 * 

	 * @access public

	 * @static

	 * @param array array({title}, {url})

	 * @return boolean TRUE | exception Breadcrumb_Exception

	 */

	public static function add($title ,$url)
	{
	
		$array = array($title ,$url);
				
        if(is_array($array) && count($array) == 2){

    		array_push(self::$crumbs,array('title'=>$array[0],
                                           'url'=>$array[1]
                                           ));

    		return TRUE;

        } else {

            throw new Exception("Input to crumbs:add must be an array of 2 elements (array(title, url))!");
    
        }
	}    
    
    public static function fromNodes($nodes, $sliceLink, $exclude=array())
	{
	   // $link = trim($link,'/');
      
       // $linkFragments = explode('/', $link);
        
       //  var_dump($nodes);
        
        foreach($nodes as $v){
            
            if(!in_array($v['tree_link'],$exclude) ){
                
                   self::add(array( $v['tree_title'], str_replace($sliceLink,'',$v['tree_link'])));
                   
            }
            
        }
      
      
        return $result;
	}
    
    
    
    public static function fromNodeLink($link)
	{
	   
        $link = trim($link,'/');
      
        $linkFragments = explode('/', $link);
        
        foreach($linkFragments as $v){
            
                    
            self::add(array(
            'title'=>
            'url'
            ));
            
        }
      
      
        return $result;
	}
    
    
    
    
    
   	/**

	 * add function.

	 * 

	 * @access public

	 * @static

	 * @param array array({title}, {url})

	 * @return boolean TRUE | exception Breadcrumb_Exception

	 */

	public static function Render()
	{	
	    $crumbs = self::$crumbs;
				    
        //var_dump($crumbs);
                
        ob_start();
      
        include (APP_PATH . '/blocks/_simple/crumbs.phtml');

        $result = ob_get_contents();
        
        ob_end_clean();
        
        return $result;
	}
}

