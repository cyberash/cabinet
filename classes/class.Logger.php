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

class Logger {

public $module; // string Code module this action occured in
public $text;  // string Message text
public $user;
static $remoteip;
static $uri;
static $date;

static $errortype = array(
				E_ERROR			=> 'Error',
				E_WARNING		=> 'Warning',
				E_PARSE			=> 'Parsing Error',
				E_NOTICE		=> 'Notice',
				E_CORE_ERROR		=> 'Core Error',
				E_CORE_WARNING		=> 'Core Warning',
				E_COMPILE_ERROR		=> 'Compile Error',
				E_COMPILE_WARNING	=> 'Compile Warning',
				E_USER_ERROR		=> 'User Error',
				E_USER_WARNING		=> 'User Warning',
				E_USER_NOTICE		=> 'User Notice',
				E_STRICT		=> 'Runtime Notice',
				4096			=> 'Unknown');

function __construct() {
	ini_set('error_log', iHOMEDIR.'log/loged_errors.txt');
	Logger::$remoteip = $_SERVER['REMOTE_ADDR'];
	Logger::$uri = empty($_SERVER['REQUEST_URI']) ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	Logger::$date = iNOW_TEXT;
}

// Sets the type of log message: notice, warning, error, critical, or security
/*
public function load( $data ) {
    $this->setID( $data['id'] );
    $this->setType( $data['type'] );
    $this->setModule( $data['module'] );
    $this->setText( $data['text'] );
    $this->setUsername( $data['username'] );
    $this->setRemoteIP( long2ip($data['remoteip']) );
    $this->setDate( $data['date'] );
}
*/

static function listbyaccount($AccountID,$pagenum=1) {
	global $DB, $LANG;
	$page_posts = 25;
	$spos = ($pagenum-1)*$page_posts;
	$result = $DB->query_adv("SELECT SQL_CALC_FOUND_ROWS * FROM `History` LEFT JOIN `Users` USING (`UserID`) WHERE `AccountID`=$AccountID ORDER BY `HistoryID` DESC LIMIT $spos,$page_posts");
	$FOUND_ROWS = $DB->row($DB->query_adv('SELECT FOUND_ROWS()'));
	$count = $FOUND_ROWS['FOUND_ROWS()'];
	$show = beginTable($LANG['History'].' '.$count.' записей');
	$show .= makeTH($LANG['Actions'], $LANG['Description'], $LANG['User'], 'Время', 'IP');
	while($row = $DB->row($result)) $show .= makeTD(
		$row['action'],
		'<pre>'.$row['text'].'</pre>',
		empty($row['username']) ? '---' : $row['username'],
		$row['time'],
		$row['ip']
		);
	$show .= '<tr><td colspan="3" class="nob">';
	$show .= makePageNav($pagenum,$page_posts,$count,iSELF.'?object=account&amp;action=history&amp;AccountID='.$AccountID.'&amp;');
	$show .= '</td></tr>';
	$show .= endTable();
	return $show;
}

}

function log_error($errstr, $errfile = null, $errline = null, $errno = E_ERROR) {
	trigger_error($errstr, E_USER_NOTICE);
}

// Insert Event into database
function log_event( $action, $type='notice', $text='', $AccountID=0, $ResellerID=0, $OrderID=0, $ServiceID=0, $PaymentID=0 ) {
	global $DB;
	$iUSER_ID = iUSER_ID;
	if($action == 'login' && $AccountID<100 && $AccountID>0) { // trick for logging admins logins
		$iUSER_ID = $AccountID;
		$AccountID = 0;
	}
	if( !$DB->make_insert('History',
				array('action' => $action,
						'UserID' => $iUSER_ID,
						'type' => $type,
						'text' => $text,
						'ip' => $_SERVER['REMOTE_ADDR'],
						'ResellerID' => $ResellerID,
						'AccountID' => $AccountID,
						'OrderID' => $OrderID,
						'ServiceID' => $ServiceID,
						'PaymentID' => $PaymentID
					) )) return false;
	return true;
}