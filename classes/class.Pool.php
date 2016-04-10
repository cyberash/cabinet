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

class Pool {

public $id;
public $description;
public $request;
public $statusinfo;
public $status;
public $ServerID;
public $AccountID;
public $UserID;
public $lastrenty;

static $LANG = array(
'Pool'=>'Задача'
);

function __construct( $id = '') {
	global $DB;
	$id = intval($id);
	if($id != null) {
		$result = $DB->make_select('Pool','*','`PoolID`='.$id);
		$row = $DB->row($result);
		if(!$row) {
			$this->id = 0;
		}else{
			$this->load($row);
		}
	}else{
		$this->id = 0;
	}
}

public function load($data){
	$this->description = $data['description'];
	$this->request = $data['request'];
	$this->statusinfo = $data['statusinfo'];
	$this->status = $data['status'];
	$this->ServerID = $data['ServerID'];
	$this->AccountID = $data['AccountID'];
	$this->UserID = $data['UserID'];
	$this->lastrenty = $data['lastrenty'];
}

public function show() {
	$show = beginTable(Pool::$LANG['Pool'].' ID '.$this->id, '100%');
	$show .= StaticField('action', $this->description);
	$show .= StaticField('time', $this->lastrenty);
	$show .= StaticField('status', $this->status);
	$show .= StaticField('statusinfo', '<pre>'.htmlentities($this->statusinfo).'</pre>');
	$show .= StaticField('request', '<pre>.'.$this->request.'</pre>');
	$show .= StaticField('ServerID', $this->ServerID);
	$show .= StaticField('AccountID', '<a href="?object=account&action=show&AccountID='.$this->AccountID.'">'.$this->AccountID.'</a>');
	$show .= StaticField('UserID', '<a href="?object=user&action=show&UserID='.$this->UserID.'">'.$this->UserID.'</a>');
	$show .= endTable();
	return $show;
}

static function listbyaccount($AccountID,$pagenum=1) {
	global $DB, $LANG;
	$result = $DB->make_select('Pool', 'SQL_CALC_FOUND_ROWS *', '`AccountID`='.$AccountID, 'PoolID', 'DESC');
	$FOUND_ROWS = $DB->row($DB->query_adv('SELECT FOUND_ROWS()'));
	$count = $FOUND_ROWS['FOUND_ROWS()'];
	$show = beginTable($LANG['History'].' задач '.$count.' записей');
	$show .= makeTH('info', 'status', 'ServerID', 'time');
	while( $data = $DB->row($result) ){
		$show .= makeTD(
	'<a href="?object=pool&amp;action=show&amp;PoolID='.$data['PoolID'].'">'.$data['description'].'</a>',
	//$data['statusinfo'],
	$data['status'],
	$data['ServerID'],
	$data['lastrenty']
		);
	}
	$show .= endTable();
	return $show;
}

}
