<?php
/**
    MultiCabinet - billing system for WHM panels.
    Copyright (c) 2008, Vladimir M. Andreev. All rights reserved.

    This file is part of MultiCabinet billing system.

    MultiCabinet is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    MultiCabinet is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
**/

if (!defined('iSELF')) { header('Location: index.php'); exit; }

class Core {

function __construct() {
	// Save some memory.. (since we don't use these anyway.)
	unset($GLOBALS['HTTP_COOKIE_VARS'], $GLOBALS['HTTP_ENV_VARS'], $GLOBALS['HTTP_GET_VARS']);
	unset($GLOBALS['HTTP_POST_VARS'], $GLOBALS['HTTP_SERVER_VARS'], $GLOBALS['HTTP_POST_FILES']);
	unset($GLOBALS['_FILES'], $GLOBALS['_ENV']);

	// Filter. No any html/js/non_uft code
	foreach($_COOKIE as $numop => $valo) $_COOKIE[$numop] = stripinput($valo);
	foreach($_POST as $numop => $valo) $_POST[$numop] = stripinput($valo);
	foreach($_GET as $numop => $valo) $_GET[$numop] = stripinput($valo);
	foreach($_REQUEST as $numop => $valo) $_REQUEST[$numop] = stripinput($valo);

	// Prevent any possible XSS attacks via $_GET.
	foreach ($_GET as $check_url) {
		if ((eregi("<[^>]*script*\"?[^>]*>", $check_url)) || (eregi("<[^>]*object*\"?[^>]*>", $check_url)) ||
			(eregi("<[^>]*iframe*\"?[^>]*>", $check_url)) || (eregi("<[^>]*applet*\"?[^>]*>", $check_url)) ||
			(eregi("<[^>]*meta*\"?[^>]*>", $check_url)) || (eregi("<[^>]*style*\"?[^>]*>", $check_url)) ||
			(eregi("<[^>]*form*\"?[^>]*>", $check_url)) || (eregi("\([^>]*\"?[^)]*\)", $check_url)) ||
			(eregi("\"", $check_url))) {
			//die ('XSS attack filtred');
			die();
		}
	}
	
	// Some PHP defaults. For stable work on different systems.
	ini_set('precision', 14);
	ini_set('serialize_precision', 14);
	
/*
	if(!iCONSOLE) {
		$_SERVER['HTTP_ACCEPT_ENCODING'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
		// enable Gzip compression
		if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start(array('ob_gzhandler',9));
		// no caching hint
		//header("Expires: Mon, 01 Jan 2000 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");// HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");// HTTP/1.0
	}
*/



}

}

# далее идут только функции

function load_theme($name='') {
	if(!preg_match('/^[-0-9A-Za-z_]{3,50}$/', $name)) $name = 'System';
	global $Theme;
	if(!empty($name) && file_exists(iHOMEDIR.'www/themes/'.$name.'/theme.php')) {
		require_once(iHOMEDIR.'www/themes/'.$name.'/theme.php');
		$theme_name = 'Theme_'.$name;
		$Theme = new $theme_name();
	}elseif(get_class($Theme)!='Theme') $Theme = new Theme();
}

function redirect($location, $type='header') {
//echo "Redirect to $location\n"; return;
	$host  = $_SERVER['HTTP_HOST'];
	$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$uri = "http://$host$uri/$location"; // Use full url fo HTTP/1.1
	if ($type == 'header') {
		header('Location: '.$uri);
	}else echo "<script type='text/javascript'>document.location.href='".html_entity_decode($uri,ENT_QUOTES,'UTF-8')."'</script>\n";
	exit;
}

// рисует окошко что всё ок и редиректит
function redirect_onok($location) {
	$res = beginDocument('Operation COMPLETE');
	$res .= '<script type="text/javascript">
function timeoutgo() {
	document.location.href="'.html_entity_decode($location,ENT_QUOTES,'UTF-8').'";
}
window.setTimeout("timeoutgo();", 500);
</script>';

	$res .= '<div align="center">'.beginTable();
	$res .= '<tr><td><b>Operation COMPLETE</b></td></tr>';
	$res .= endTable().'</div>';
	$res .= endDocument();
	return $res;
}

// if you need to replace localised symbols, use this
/*
function utf8_str_replace($s,$r,$str){
	if(!is_array($s)) $s = '!'.preg_quote($s,'!').'!u';
	else foreach ($s as $k => $v) $s[$k] = '!'.preg_quote($v).'!u';
	return preg_replace($s,$r,$str);
}
*/

/*
In general, given that you’re switching to UTF-8, you no longer need to use HTML entities other than the “special five” which could cause a parser problems, because the characters can be represented directly in UTF-8. The “special five”, which could trip an HTML / XML parser are;
& (ampersand) entity: &amp;
" (double quote) entity: &quot;
' (single quote) entity: &apos;
< (less than) entity: &lt;
> (greater than) entity: &gt;
*/
// Strip Input Function, prevents HTML in unwanted places
// To prevent SQL inject, use ONLY ' qoute for data in SQL queryes. ' quote is replaced to " in REQUEST data.
// (т.е. недопускать наличи одинарной кавычки в данных. заменять на двойную)
// Сделать функцию
function stripinput($text) {
//echo bin2hex($text)."<br>\n";
	if(is_array($text)) {
        	//ob_start();
        	//var_dump($text);
        	//$output = ob_get_clean();
		//log_error('ARRAY in Input data: '.$output);
		die();
	}
	if(iSLASH) $text = stripslashes($text);
	if(!preg_match('/^.{1}/us',$text)) $text = @iconv('CP1251','UTF-8//IGNORE',$text); // Covert to UFT if input is cp1251
#todo: decode with html_entity_decode, filter all but alpha_num_znaki, encode slases and quotes to entities
	//if(is_array($text)) $text = implode('',$text);
	// Kill hexadecimal characters completely
//	$text = preg_replace('#(&\#x)([0-9A-F]+);*#si', '', $text); // or use html_entity_decode() to decode it
	$text = html_entity_decode($text,ENT_QUOTES,'UTF-8');
// А нах нам двойные кавычки фильтровать
	//$search = array('&nbsp;', '&', '#', '\"', "\'", '"', "'", "\\", '<', '>', '/', "`", '%', '»', '«', '“', '“');
	//$replace = array(' ', '&amp;', '&#35;', '&quot;', '&#39;', '&quot;', '&#39;', '&#92;', '&lt;', '&gt;', '&#47;', '&#39;', '&#25;', '&quot;', '&quot;', '&quot;', '&quot;');
	$search = array('<', '>', "`", "'",'»', '«', '\\', '–', '“', '”', '‘', '’');
	$replace = array('&lt;', '&gt;', '"', '"', '"', '"', '/', '-', '"', '"', '"', '"');
	$text = trim(str_replace($search, $replace, $text));
//echo bin2hex($text)."<br>\n";
	return $text;
}

// This function sanitises submissions
// Allow some HTML stuff in our comment fiels, but prevent XSS
function descript($text,$striptags=true) {
	// the following is based on code from bitflux (http://blog.bitflux.ch/wiki/)
}

function translit($text) {
	$cyr = array(
	'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ы','Ъ','Э','Ю','Я',
        'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я');

	$lat = array(
	'A','B','V','G','D','E','E','Zh','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','Kh','Ts','Ch','Sh','Shch',"",'Y','','E','Yu','Ya',
	'a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch','','y','','e','yu','ya');

	// е,ё = ey после гласных, после ь,ъ и в начале слова
	$text = preg_replace('/([AаЕеЁёИиОоУуЬьЫыЪъЭэЮюЯя]{1})[её]{1}/u','$1ey',$text);
	$text = preg_replace('/([AаЕеЁёИиОоУуЬьЫыЪъЭэЮюЯя]{1})[ЕЁ]{1}/u','$1Ey',$text);

	// сочитание ья отображается как ia
	$text = str_replace('ья','ia',$text);

	// остальное прямая замена по табличке
	$text = str_replace($cyr, $lat, $text);
	return $text;
/*
ОАО = OJSC или JSC
ЗАО = CJSC или JSC
ООО = LLC, Ltd или SRL
фонд = fond
союз = union
ГОУ школа = SEE School
МОУ школа = MEE School
*/
}

function translit_fio($text) {
	$text = translit($text);
	$words = explode(' ', $text);
	$text = '';
	if(isset($words[1])) $text = $words[1].' ';
	if(isset($words[2])) $text .= $words[2][0].' ';
	if(isset($words[0])) $text .= $words[0];
	return $text;
}

function mail_send($to,$from,$subject,$body){
	$body = "\n\n". @iconv('UTF-8','CP1251',$body)."\n";
	$subject = @iconv('UTF-8','CP1251',$subject);
	$Mail = new PHPMailer();
	$Mail->Priority = 3;
	$Mail->Encoding = '8bit';
	$Mail->CharSet = 'windows-1251';
	$Mail->From = $from;
	$Mail->FromName = $from;
	$Mail->Sender = $from;
	$Mail->Subject = $subject;
	$Mail->Body = $body;
	$Mail->AltBody = '';
	$Mail->WordWrap = strlen($body);;
	$Mail->Mailer = 'mail';
	//$Mail->AddReplyTo($from, $from);
	//$Mail->AddAttachment("/temp/11-10-00.zip", "new_name.zip");
	$Mail->AddAddress($to, $to);
	if(!iDEMO) $Mail->Send();
	if(iDEBUG) {
		echo "<pre>Send mail \"$subject\" to $to from $from ...\n";
		echo "Body: $body\n</pre>";
		echo $Mail->ErrorInfo;
	}
}

/*
function seril($key,$val){
	$s = '';
	if(is_int($val)) $s = 's:'.strlen($key).':"'.$key.'";i:'.$val.';';
	else $s = 's:'.strlen($key).':"'.$key.'";s:'.strlen($val).':"'.$val.'";';
	return $s;
}
*/

function zone($domain) {
	return substr($domain, strpos($domain, '.'));
}

function datetostr($timestamp = null) {
	if(!is_null($timestamp)) return date('Y-m-d H:i:s',$timestamp); else return date('Y-m-d H:i:s');
}

function pdata($inm, $log = '') {
global $DB;
//$back = debug_backtrace();
//print_r($back);
	echo "<html><head><title>Ошибка</title>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head>
<body><table bgcolor='#E0E0FF' border='1' cellpadding='5' cellspacing='0' ><tr><td><b>!</b> При обработке данных возникла следующая проблема: <b>$inm</b><p><a href='javascript:history.back()' onClick='history.back()'>вернутся назад</a></p></td></tr></table></body></html>";
	if(!empty($log)) {
		$post  = serialize($_POST);
		$DB->make_insert('WrongOrders',
			array(	'error' => $inm."\n".$log,
					'post' => $post
					) );
	}
	exit();
}

/**
 * xml2array() will convert the given XML text to an array in the XML structure.
 * Arguments : $contents - The XML text
 *  $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 * Return: The parsed XML in an array form.
 */
function xml2array($contents, $get_attributes=true) {
    if(empty($contents)) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }
    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create();
    xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
    xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
    xml_parse_into_struct( $parser, $contents, $xml_values );
    xml_parser_free( $parser );
    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array;

    //Go through the tags.
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = '';
        if($get_attributes) {//The second argument of the function decides this.
            $result = array();
            if(isset($value)) $result['value'] = $value;

            //Set the attributes too.
            if(isset($attributes)) {
                foreach($attributes as $attr => $val) {
                    if($get_attributes == 1) $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                    /**  :TODO: should we change the key name to '_attr'? Someone may use the tagname 'attr'. Same goes for 'value' too */
                }
            }
        }elseif(isset($value)) $result = $value;

        //See tag status and do the needed.
        if($type == 'open') {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;

            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                $current = &$current[$tag];

            } else { //There was another element with the same tag name
                if(isset($current[$tag][0])) {
                    array_push($current[$tag], $result);
                } else {
                    $current[$tag] = array($current[$tag],$result);
                }
                $last = count($current[$tag]) - 1;
                $current = &$current[$tag][$last];
            }

        } elseif($type == 'complete') { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;

            } else { //If taken, put all things inside a list(array)
                if((is_array($current[$tag]) and $get_attributes == 0)//If it is already an array...
                        or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
                    array_push($current[$tag],$result); // ...push the new element into that array.
                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }

    return($xml_array);
}

function array2xml($array, $name) {
	$xml = new XmlWrite();
	$xml->push($name);
	$xml->xwalk($array, $xml);
	$xml->pop();
	return $xml->getXml();
}

require_once(iHOMEDIR.'lib/api/class.Template.php');

class ArrayDiff {
static $as_str;
static function array_diff_assoc_recursive($one, $two, $r=NULL){
	$diff = array();
	//self::$as_str;
	$d1=array_diff_assoc($one, $two);
	if(!empty($d1)) {
		$diff=$d1;
		foreach($d1 as $key=>$val) self::$as_str .= "$key: '$one[$key]'->'$two[$key]'; \n";
	}
	$same=array_intersect_assoc($one, $two);
	foreach($same as $rr=>$data){
		if(is_array($one[$rr]) && is_array($two[$rr])){
			$d2 = self::array_diff_assoc_recursive($one[$rr], $two[$rr], $rr);
			if(!empty($d2)) $diff = array_merge($diff,$d2);
		}
	}
	if($r==null || empty($diff)) return $diff; else return array($r=>$diff);
}
}

function raw($var, $label=null) {
        // format the label
        $label = ($label===null) ? '' : rtrim($label) . ' ';
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (PHP_SAPI == 'cli') $output = PHP_EOL . $label . PHP_EOL . $output . PHP_EOL;
        else $output = '<h3>raw_dump:</h3><pre>'.$label.htmlspecialchars($output, ENT_QUOTES).'</pre>';
	//if(checkrights('A') || checkrights('D')) echo($output);
	//if(checkrights('A')) echo($output);
	echo($output);
}

function raw_backtrace() {
	$back = debug_backtrace();
	unset($back[0]);
	raw($back,'backtrace');
}
