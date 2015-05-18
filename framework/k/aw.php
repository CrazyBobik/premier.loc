<?php

/**
 * Библиотека переделанных функций для array_walk
 * 
 * @package    DM
 * * @category   Helpers
 */
class K_Aw {
 
 /** 
 * обрезает строку с по границе слова.
 * @package    DM/* Connecting, selecting database // <!-- phpDesigner :: Timestamp [02.08.2012 11:39:54] -->
 * @category   Helpers
 */
 
    public static function trunc_ws($string, $len, $wordsafe = false, $dots = false,$enc='utf-8') {
        $slen = mb_strlen($string,$enc);
        if ($slen <= $len) {
            return $string;
        }
        if ($wordsafe) {
            $end = $len;
            while (($string[--$len] != ' ') && ($len > 0)) {
            };            ;
            if ($len == 0) {
                $len = $end;
            }
        }
        if ((ord($string[$len]) < 0x80) || (ord($string[$len]) >= 0xC0)) {
            return mb_substr($string, 0, $len, $enc) . ($dots ? '..' : '');
        }
        while (--$len >= 0 && ord($string[$len]) >= 0x80 && ord($string[$len]) < 0xC0) {
        };
        return mb_substr($string, 0, $len, $enc) . ($dots ? '..' : '');
    }
}
