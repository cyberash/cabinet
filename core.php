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
include("config.php");
define('iVersion', '1.6.0alpha5'); # Program Version
define('iDEBUG', true);
define('BR', "<br />");
$begin_time = microtime(1);

$_SERVER['PHP_SELF'] = htmlentities(strip_tags($_SERVER['PHP_SELF']));
define('iSELF', $_SERVER['PHP_SELF']);

// Runtime settings
ini_set('register_globals', '0');
//ini_set('error_log', 'loged_errors'); // Name of the file where script errors should be logged
$_ENV = array();
if(iDEBUG){
error_reporting(E_ALL);
} else {
error_reporting(0);
}

// Detect Windows os Unix server type
if(strtoupper(substr(PHP_OS, 0,3)) == 'WIN' ) {
	define('iUNIX', false);
	define('iPATH_SLASH', '\\');
}else{
	define('iUNIX', true);
	define('iPATH_SLASH', '/');
}

// Get absolute path
//$folder = dirname(__FILE__); // __DIR__ equivalent, compatible with php <5.3
//while (!file_exists($folder.iPATH_SLASH.'lib') && $folder)
 //      $folder = substr($folder,0,strrpos($folder, iPATH_SLASH));
//define('iHOMEDIR', $folder.iPATH_SLASH);
//$folder_level = ''; $folder = 0;
//while (!file_exists($folder_level.'lib') && $folder<256) {
//	$folder_level .= '../'; $folder++;
//}
//define('iHOMEURL', substr($folder_level,3));
//unset($folder,$folder_level);

//legacy support of PHP < 5.3
$phpversion = explode('.', phpversion());
if($phpversion[0] < 5){
	echo 'Dont even think about running this script on PHP version less than 5';
	exit;
} elseif($phpversion[1] < 3){
//loading legacy code
///	require_once(iHOMEDIR.'lib/hooks/legacy.php');
}

// Check if installed
if(!IS_INSTALLED && !isset($_GET['install']) && !isset($_POST['install'])){
		echo("Looks like program is not installed.  <a href='install/install.php?install=1'>Click here</a> to start installation script");
		exit;
	}
if(empty($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = 'localhost';
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	define('iCONSOLE', true);
}else define('iCONSOLE', false);

($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || iDEBUG ) ? ini_set('display_errors', '1') : ini_set('display_errors', '0');
define('iDAY', 86400);
define('iMON', 365.25/12*iDAY);
define('iYAR', 365.25*iDAY);
define('iSLASH',get_magic_quotes_gpc());
//date_default_timezone_set($_CONF['timezone']);
define('iNOW_TEXT', date('Y-m-d H:i:s'));
define('iNOW_UNIX', strtotime(iNOW_TEXT));

$db = DB::getInstance();
if($db->errno != 0){
	die("Database connection problem. Please, check your connection settings. Error #".$db->errno.": ".$db->error);
}

function __autoload($class_name) {
	if(preg_match('/^(\w+)ServerModule$/', $class_name, $servs)){
		if($servs[1] == ''){
			$libm = iHOMEDIR.'lib/api/class.'.$class_name.'.php';
		} else {
			$libm = SYSTEM_PATH.'/servermodules/'.$servs[1].'.php';
		}
	} elseif(preg_match('/^(\w+)notifymodule/', $class_name, $servs)){
		$libm = SYSTEM_PATH.'/notifymodules/'.$servs[1].'.php';
	} elseif(preg_match('/^(\w+)curprovider/', $class_name, $servs)) {
		$libm = SYSTEM_PATH.'/curmodules/'.$servs[1].'.php';
	} elseif(preg_match('/^(\w+)PaymentGateway/', $class_name, $servs)){
		$libm = SYSTEM_PATH.'/paymodules/'.$servs[1].'.php';
	} else {
		$libm = SYSTEM_PATH.'/classes/class.'.$class_name.'.php';
	}
	if(!file_exists($libm)){
		trigger_error("can't find $libm", E_USER_ERROR);
	}
	
	require($libm);
}

//Loading system language
$setting = Settings::getInstance();
$lang = Lang::getInstance();
$langcode = $setting->Get('system.lang.default');

if(!$langcode || strlen($langcode) < 2){
	$langcode = 'en';
}
require_once($lang->GetLangPath($langcode));
if(!function_exists('langArray')){
	//trying to load default lang
	if(!require_once($lang->GetLangPath('en'))){
		die('Unable to load language file');
	}
}
Lang::$langarray = langArray();


$object = isset($_REQUEST['object']) ? $_REQUEST['object'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$pagenum = isset($_REQUEST['pagenum']) ? intval($_REQUEST['pagenum']) : 1;
if($pagenum<1) $pagenum = 1;
$_REQUEST = array_merge($_REQUEST, $_COOKIE, $_POST, $_GET);
$NAVIG = array();




//if(!checkrights('A')) die('<h1>Сервис временно недоступен</h1><br />Доступ будет открыт в течении 15 мин.<br /><br />');

/**  :TODO:
 * защита от повторного нажатия кнопок
 * тока в стадии продумывания и тестирования
 * пока пожет не дать никакого эфекта если браузер не дождался ответа от преведущего поста
*/
// block rePOST data
if(isset($_POST['fc']) && preg_match('/^[a-z0-9]{8}$/',$_POST['fc'])) {
	if(isset($_COOKIE['fc']) && $_COOKIE['fc']==$_POST['fc']) {
		if($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
			log_error('Resend POST '.$object.':'.$action);
			pdata('Вы пытаетесь отправить те же данные повторно');
		}
	}else setcookie('fc', $_POST['fc'], time() + 3600*24, '/', '', '0');
}

function show_err_trace($trace_array, $errno, $errstr) {
	$i = 0;
	if(isset($trace_array[1]['function']) && $trace_array[1]['function'] == 'trigger_error') $i=2;
	elseif(!isset($trace_array[0]['file'])) {
	    $trace_array[0]['file'] = $trace_array[0]['args'][2];
	    $trace_array[0]['line'] = $trace_array[0]['args'][3];
	}
	echo '<div style="font-family:Verdana;font-size:11px;background-color:#FFC0A0;border:1px solid #606060;"><b>'. Logger::$errortype[$errno] .'</b>: '. $errstr .' in <b>'. $trace_array[$i]['file'] .'</b> on line <b>'. $trace_array[$i]['line'] .'</b>'.
	'<div style="font-family:Verdana;font-size:10px;padding:0px 0px 0px 20px;">';
	$i++;
	for(; $i < count($trace_array); $i++) {
		if(isset($trace_array[$i]['args'])) {
			$arg = '(';
			foreach($trace_array[$i]['args'] as $v) {
				if(is_object($v)) $v = 'CLASS '.get_class($v);
				//elseif(is_resource($arg)) ?? not tested = not needed :)
				elseif(is_array($v)) $v = 'ARRAY';
				//elseif(is_string($v)) $v = '\''.$v.'\'';
				$arg .= $arg=='(' ? "$v" : ", $v";
			}
			$arg .= ')';
		}else $arg = '';
		echo 'file: <b>'. $trace_array[$i]['file'] .'</b> line: <b>'. $trace_array[$i]['line'] .'</b> function: <b>'. $trace_array[$i]['function'] .$arg.'</b><br />';
	}
	echo "</div></div>";
}

// Handler for standard error messages.
//Disabled for a while
//function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
//	global $_CONF;
//	if (error_reporting() == 0) return;
//
//	if(!defined('iUSER_ID')) $iUSER_ID = 0; else $iUSER_ID = iUSER_ID;
//	
//	if($_CONF['ErrorLogging']) error_log(Logger::$errortype[$errno].': '.$errstr.' in '.$errfile.' on line '.$errline.' , requested by url "'.Logger::$uri.'" from '.Logger::$remoteip.' with UserID='.$iUSER_ID);
//
//	if ($_CONF['ErrorDisplay'] && function_exists('debug_backtrace')) {
//		$trace_array = debug_backtrace();
//		show_err_trace($trace_array, $errno, $errstr);
//		/* debug
//		foreach($trace_array as $funct) {
//				$a = $funct; unset($a['args']);
//				raw($a, "stet out");
//		}
//		*/
//	}elseif($_CONF['ErrorDisplay']) {
//		if(!iCONSOLE) echo '<br /><b>'. Logger::$errortype[$errno] .'</b>: '. $errstr .' in <b>'. $errfile .'</b> on line <b>'. $errline .'</b><br />';
//		else echo Logger::$errortype[$errno] .': '. $errstr .' in '. $errfile .' on line '. $errline ."\n";
//	}
//
//	if ($errno % 255 == E_ERROR || $errno % 255 == E_USER_ERROR) exit(-1);
//}
/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
 * array containing the HTTP server response header fields and content.
 */
function get_web_page( $url )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "Multicabinet Billing System", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}

//hook for windows
if(!function_exists('money_format')){
/**
 * Formata um numero em notacao de moeda, assim como a funcao money_format do PHP
 * @author Rubens Takiguti Ribeiro
 * @see http://php.net/manual/en/function.money-format.php
 * @param string $formato Formato aceito por money_format
 * @param float $valor Valor monetario
 * @return string Valor formatado
 */
function money_format($formato, $valor) {

    // Se nenhuma localidade foi definida, formatar com number_format
    if (setlocale(LC_MONETARY, 0) == 'C') {
        return number_format($valor, 2);
    }

    // Obter dados da localidade
    $locale = localeconv();

    // Extraindo opcoes do formato
    $regex = '/^'.             // Inicio da Expressao
             '%'.              // Caractere %
             '(?:'.            // Inicio das Flags opcionais
             '\=([\w\040])'.   // Flag =f
             '|'.
             '([\^])'.         // Flag ^
             '|'.
             '(\+|\()'.        // Flag + ou (
             '|'.
             '(!)'.            // Flag !
             '|'.
             '(-)'.            // Flag -
             ')*'.             // Fim das flags opcionais
             '(?:([\d]+)?)'.   // W  Largura de campos
             '(?:#([\d]+))?'.  // #n Precisao esquerda
             '(?:\.([\d]+))?'. // .p Precisao direita
             '([in%])'.        // Caractere de conversao
             '$/';             // Fim da Expressao

    if (!preg_match($regex, $formato, $matches)) {
        trigger_error('Formato invalido: '.$formato, E_USER_WARNING);
        return $valor;
    }

    // Recolhendo opcoes do formato
    $opcoes = array(
        'preenchimento'   => ($matches[1] !== '') ? $matches[1] : ' ',
        'nao_agrupar'     => ($matches[2] == '^'),
        'usar_sinal'      => ($matches[3] == '+'),
        'usar_parenteses' => ($matches[3] == '('),
        'ignorar_simbolo' => ($matches[4] == '!'),
        'alinhamento_esq' => ($matches[5] == '-'),
        'largura_campo'   => ($matches[6] !== '') ? (int)$matches[6] : 0,
        'precisao_esq'    => ($matches[7] !== '') ? (int)$matches[7] : false,
        'precisao_dir'    => ($matches[8] !== '') ? (int)$matches[8] : $locale['int_frac_digits'],
        'conversao'       => $matches[9]
    );

    // Sobrescrever $locale
    if ($opcoes['usar_sinal'] && $locale['n_sign_posn'] == 0) {
        $locale['n_sign_posn'] = 1;
    } elseif ($opcoes['usar_parenteses']) {
        $locale['n_sign_posn'] = 0;
    }
    if ($opcoes['precisao_dir']) {
        $locale['frac_digits'] = $opcoes['precisao_dir'];
    }
    if ($opcoes['nao_agrupar']) {
        $locale['mon_thousands_sep'] = '';
    }

    // Processar formatacao
    $tipo_sinal = $valor >= 0 ? 'p' : 'n';
    if ($opcoes['ignorar_simbolo']) {
        $simbolo = '';
    } else {
        $simbolo = $opcoes['conversao'] == 'n' ? $locale['currency_symbol']
                                               : $locale['int_curr_symbol'];
    }
    $numero = number_format(abs($valor), $locale['frac_digits'], $locale['mon_decimal_point'], $locale['mon_thousands_sep']);

/*
//TODO: dar suporte a todas as flags
    list($inteiro, $fracao) = explode($locale['mon_decimal_point'], $numero);
    $tam_inteiro = strlen($inteiro);
    if ($opcoes['precisao_esq'] && $tam_inteiro < $opcoes['precisao_esq']) {
        $alinhamento = $opcoes['alinhamento_esq'] ? STR_PAD_RIGHT : STR_PAD_LEFT;
        $numero = str_pad($inteiro, $opcoes['precisao_esq'] - $tam_inteiro, $opcoes['preenchimento'], $alinhamento).
                  $locale['mon_decimal_point'].
                  $fracao;
    }
*/

    $sinal = $valor >= 0 ? $locale['positive_sign'] : $locale['negative_sign'];
    $simbolo_antes = $locale[$tipo_sinal.'_cs_precedes'];

    // Espaco entre o simbolo e o numero
    $espaco1 = $locale[$tipo_sinal.'_sep_by_space'] == 1 ? ' ' : '';

    // Espaco entre o simbolo e o sinal
    $espaco2 = $locale[$tipo_sinal.'_sep_by_space'] == 2 ? ' ' : '';

    $formatado = '';
    switch ($locale[$tipo_sinal.'_sign_posn']) {
    case 0:
        if ($simbolo_antes) {
            $formatado = '('.$simbolo.$espaco1.$numero.')';
        } else {
            $formatado = '('.$numero.$espaco1.$simbolo.')';
        }
        break;
    case 1:
        if ($simbolo_antes) {
            $formatado = $sinal.$espaco2.$simbolo.$espaco1.$numero;
        } else {
            $formatado = $sinal.$numero.$espaco1.$simbolo;
        }
        break;
    case 2:
        if ($simbolo_antes) {
            $formatado = $simbolo.$espaco1.$numero.$sinal;
        } else {
            $formatado = $numero.$espaco1.$simbolo.$espaco2.$sinal;
        }
        break;
    case 3:
        if ($simbolo_antes) {
            $formatado = $sinal.$espaco2.$simbolo.$espaco1.$numero;
        } else {
            $formatado = $numero.$espaco1.$sinal.$espaco2.$simbolo;
        }
        break;
    case 4:
        if ($simbolo_antes) {
            $formatado = $simbolo.$espaco2.$sinal.$espaco1.$numero;
        } else {
            $formatado = $numero.$espaco1.$simbolo.$espaco2.$sinal;
        }
        break;
    }

    // Se a string nao tem o tamanho minimo
    if ($opcoes['largura_campo'] > 0 && strlen($formatado) < $opcoes['largura_campo']) {
        $alinhamento = $opcoes['alinhamento_esq'] ? STR_PAD_RIGHT : STR_PAD_LEFT;
        $formatado = str_pad($formatado, $opcoes['largura_campo'], $opcoes['preenchimento'], $alinhamento);
    }

    return $formatado;
}
}
?>
