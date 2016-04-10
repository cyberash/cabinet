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

class Server {
	public $id;
	public $moduleid;
	public $servergroupid;
	public $maxclients = NULL;
	public $accessdata;
	public $servername;
	public static $se_instance = NULL;
	private $db = NULL;
	private $raw = NULL;
	private $data = NULL;
public static function getInstance(){
	if(self::$se_instance == NULL){
		self::$se_instance = new self();
	}
	return self::$se_instance;
}
public function __construct($servername = '', $id = ''){
	$this->db = DB::getInstance();
	$this->servername = $servername;
	$this->id = $id;
}
public function getAccess($id=''){
	if(!is_numeric($id) && !is_numeric($this->id)){
		throw new Exception("Server ID is not set or set incorrectly");
	} elseif(!is_numeric($id) && is_numeric($this->id)){
		$id = $this->id;
	}
	$this->raw = $this->db->query_first("SELECT `accessdata` FROM `Servers` WHERE `ServerID`='".id."'");
	return unserialize($this->raw);
}
public function Delete($id = ''){
	if(!is_numeric($this->id) && !is_numeric($id)){
		throw new Exception("Server ID is not specified");
	} elseif(!is_numeric($this->id)){
		$this->id = $id;
	}
	//probably `id` should be changed to `ServerID`
	$this->raw = $this->db->query_first("DELETE FROM `Servers` WHERE `Servers`.`ServerID` = '".$this->id."'");
	if(!$this->raw){
		return false;
	} else {
		return true;
	}
}
public function FetchData($id=''){
	if($id == '' && is_numeric($this->id)){
		$this->raw = $this->db->query_first("SELECT * FROM `Servers` WHERE `ServerID`='".$this->id."'");
	} elseif(is_numeric($id)){
		$this->raw = $this->db->query_first("SELECT * FROM `Servers` WHERE `ServerID`='".$id."'");
	} else {
		throw new Exception("Problems with server id");
	}
	return $this->raw;
}
public function GetButch($num = NULL){
	if(is_numeric($num)){
		$this->raw = $this->db->fetch_all_array('SELECT ServerID, servergroupid, servername, status FROM Servers ORDER BY ServerID DESC LIMIT 0,'.$num);
	} else {
		$this->raw = $this->db->fetch_all_array('SELECT * FROM Servers ORDER BY ServerID DESC');
	}
	return $this->raw;
}
public function GetActiveByGroup($servergroupid=''){
	if(!is_numeric($servergroupid) && !is_numeric($this->servergroupid)){
		throw new Exception("Server group ID is not set or set incorrectly");
	} elseif(!is_numeric($servergroupid)){
		$servergroupid = $this->servergroupid;
	}
	$this->raw = $this->db->fetch_all_array("SELECT * FROM `Servers` WHERE `servergroupid`='".$servergroupid."' AND `status`='1'");
	return $this->raw;
}
public function Create($servername = '', $servergroupid ='', $maxclients = NULL, $autofull="1", $accessdata  = NULL){
	if($servername == '') {
		$servername = $this->servername;
	}
	if(!is_array($accessdata)){
		$accessdata = $this->accessdata;
	}
	if($servergroupid == ''){
		$servergroupid = $this->servergroupid;
	}
	if($servername == '' || $servergroupid == '' || $maxclients == '' || !is_array($accessdata)){
		throw new Exception("Problems with fields");
	}
	$accessdata = serialize($accessdata);
	$this->data = array('servergroupid' => $servergroupid, 'servername' => $servername, 'maxclients' => $maxclients, 'autofill' => $autofull, 'accessdata' => $accessdata);
	if(!$this->CheckData()){
		return $this->CheckData();
	}
	$this->raw = $this->db->query_insert('Servers', $this->data);
	if(!$this->raw){
		var_dump($this->data);
		throw new Exception("Unable to perform DB query");
	} else {
		return true;
	}
}
private function CheckData(){
if(!$this->data || $this->data == NULL || !is_array($this->data)){
		throw new Exception("Problems with data");
	} elseif(strlen($this->data['servername']) < 2 || !is_numeric($this->data['servergroupid']) || !is_numeric($this->data['maxclients'])){
			throw new Exception("Problems with data in fields"); 
	} else {
		return true;
	}
}
public function Update($parameter, $value, $id=''){
	if(!is_numeric($id) && !is_numeric($this->id)){
		throw new Exception("Server ID is not set or set incorrectly");
	} elseif(!is_numeric($id)){
		$id = $this->id;
	}
	$this->raw = $this->db->query_update('Servers', array($parameter => $value), 'ServerID ='.$id);
	return $this->raw;
}
public function FindModuleID($serverid = ''){
	if(!is_numeric($serverid) && !is_numeric($this->id)){
		throw new Exception("Server ID is not or set incorrectly");
	} elseif(!is_numeric($this->id)){
		$this->id = $serverid;
	}
	$sg = ServerGroups::getInstance();
	$serverdata = $this->FetchData();
	$sgdata = $sg->FetchData($serverdata['servergroupid']);
	return $sgdata['moduleid'];
}
public function generateOperateArray($array){
	$this->moduleid = $this->FindModuleID();
	if(is_numeric($this->moduleid)){
		$sm = ServerModule::getInstance();
		$sm->id = $this->moduleid;
		$oparray = $sm->getOperateArray();
		$ret = array();
		foreach($oparray as $k => $v){
			if(isset($array[$v['name']])){
				$ret[$v['name']] = $array[$v['name']];
			} else {
				return false;
			}
		}
		return $ret;
	} else {
		throw new Exception("Module ID is not set correctly");
	}
}
}
