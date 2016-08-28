<?php

function flipstring($string) {
	$string = strtolower($string);
	$string = strrev($string);
    $x = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "m", "n", "r", "t", "v", "w", "y", ".", "[", "(", "{", "?", "!", "\'", "<", "_", ";");
    $y = array("\u0250", "q", "\u0254", "p", "\u01DD", "\u025F", "\u0183", "\u0265", "\u0131", "\u027E", "\u029E", "\u026F", "u", "\u0279", "\u0287", "\u028C", "\u028D", "\u028E", "\u02D9", "]", ")", "}", "\u00BF", "\u00A1", ",", ">", "\u203E", "\u061B");
    $string = str_replace($x, $y, $string);
    return $string;
}

function replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}
function unicode_decode($str) {
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
}


?>
