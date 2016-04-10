<?php
/*
 *      class.UserSettings.php
 *      
 *      Copyright 2011 Artem Zhirkov <artemz@artemz-desktop>
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
class UserSettings extends Base {
	public $userid, $parameter, $value;
	public static function properties(){
		return array(
			'required' => array('userid', 'parameter', 'value')
		);
	}
	public function __construct(){
		$this->db = DB::getInstance();
		parent::__construct();
	}
	public function Get($userid, $parameter=''){
		if(!is_numeric($userid)){
			throw new Exception("User ID is not set");
		}
		$retarray = array();
		if(strlen($parameter) < 1){
			$arr = $this->GetButch('', "`userid` = '".$userid."'");
		} else {
			$arr = $this->GetButch('', "`userid` = '".$userid."' AND `parameter` = '".$parameter."'");
		}
		for($i=0;$i<count($arr);$i++){
			$retarray[$arr[$i]['parameter']] = $arr[$i]['value'];
		}
		return $retarray;
	}
	public function Set($userid, $parameter, $value){
		if(!is_numeric($userid)){
			throw new Exception("User ID is not set");
		}
		$id = $this->GetID($userid, 'userid',  "`parameter` = '".$parameter."'");
		if($id){
			$this->Update('value', $value, $id);
		} else {
			return $this->Create(array('userid' => $userid, 'parameter' => $parameter, 'value' => $value));
		}
		return true;
	}
}
?>
