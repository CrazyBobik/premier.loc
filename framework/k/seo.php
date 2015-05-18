<?php

class K_SEO{

    public static $title;
    public static $description;
    public static $keywords;
    public static $canonical;
    public static $next;
    public static $prev;

    public static function set($data){

        if(K_Registry::get('lang')=='uk'){

            if(!empty($data['tree_meta_title_uk'])){
                self::$title = $data['tree_meta_title_uk'];
            }
            if(!empty($data['tree_meta_description_uk'])){
                self::$description = $data['tree_meta_description_uk'];
            }
            if(!empty($data['tree_meta_keywords_uk'])){
                self::$keywords = $data['tree_meta_keywords_uk'];
            }
            if(!empty($data['tree_meta_canonical_uk'])){
                self::$canonical = $data['tree_meta_canonical_uk'];
            }

        }else{

            if(!empty($data['tree_meta_title'])){
                self::$title = $data['tree_meta_title'];
            }
            if(!empty($data['tree_meta_description'])){
                self::$description = $data['tree_meta_description'];
            }
            if(!empty($data['tree_meta_keywords'])){
                self::$keywords = $data['tree_meta_keywords'];
            }
            if(!empty($data['tree_meta_canonical'])){
                self::$canonical = $data['tree_meta_canonical'];
            }

        }

    }

    public static function setPrev( $prev){

        self::$prev = $prev;

    }

    public static function setNext($next){

        self::$next = $next;

    }

    public static function getPrev(){

        if(self::$prev){

            return '<link rel="prev" href="'.self::$prev.'" >';

        }

        return '';
    }

    public static function getNext(){

        if(self::$next){

            return '<link rel="next" href="'.self::$next.'" >';

        }

        return '';
    }

    public static function pagePref(){

        if(!empty($_GET['page']) && $_GET['page']>1){

            return " - Страница ".$_GET['page'];

        }

    }

}

?>