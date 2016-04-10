<?php
/*
 *      class.Settings.php
 *      
 *      Copyright 2010 Artem Zhirkov <zhirkow@yahoo.com>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */
?>
<?
class Settings{
	private $db;
	private $raw;
	//public $scope = 'global';
	public static $s_instanse = NULL;
	public static function getInstance(){
		if(self::$s_instanse == NULL){
			$s_instanse = new self();
		}
		return $s_instanse;
	}
	public function __construct(){
		$this->db = DB::getInstance();
	}
	public function Set($parameter, $value, $userid = -1){
		if(is_string($parameter) && is_numeric($userid)){
			$this->raw = $this->db->query_insert('Settings', array('parameter' => $parameter, 'value'=> $value, 'userid' => $userid));
		} else {
			throw new Exception("Problems with data");
		}
		return $this->raw;
	}
	public function RemoveUserSettings($userid){
		if(!is_numeric($userid)){
			throw new Exception("User ID is not set or set incorrectly");
		}
		$this->raw = $this->db->query_first("DELETE FROM `Settings` WHERE `userid`='".$userid."'");
		return $this->raw;
	}
	public function Get($parameter, $userid = -1){
		if(!is_string($parameter)){
			throw new Exception("No parameter given to select value");
		}
		$this->raw = $this->db->query_first("SELECT value FROM `Settings` WHERE `parameter`='".$parameter."' AND `userid`='".$userid."'");
		return @$this->raw['value'];
	}
	public function Update($parameter, $value, $userid = -1){
		if(!is_string($parameter) || !is_numeric($userid)){
			throw new Exception("Problems with data");
		}
		//$this->raw = $this->db->query_update('Settings', array($parameter => $value), '`userid`='.$userid);
		$this->raw = $this->db->query_update('Settings', array('value' => $value), "`parameter`= '".$parameter."'");
		return $this->raw;
	}
	
}
?>
