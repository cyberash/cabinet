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

class Preset extends Base {

	public static $derivatives = array();
	public $groupid, $presetid, $name, $paramsdata, $status;
	public function __construct(){
		parent::__construct();
	}
	public static function properties(){
		return array(
			'required' => array('groupid', 'name', 'paramsdata', 'status'),
			'values' => array('status' => '1')
			
		);
	}
	public function getOptions($id = ''){
		if(!is_numeric($id) && !is_numeric($this->presetid)){
			throw new Exception("Preset ID is not set or set incorrectly");
		} elseif(!is_numeric($id)) {
			$id = $this->presetid;
		}
		$this->raw = $this->FetchData($id);
		return unserialize($this->raw['paramsdata']);
	}
	public function generateOperateArray($array){
		if(is_numeric($this->groupid)){
			$group = ServerGroups::getInstance()->FetchData($this->groupid);
			$sm = ServerModule::getInstance();
			$sm->id = $group['moduleid'];
			$ret = array();
			foreach($sm->getArray('Client') as $k => $v){
				if(isset($array[$v['name']])){
					$ret[$v['name']] = $array[$v['name']];
				} else {
					return false;
				}
			}
			return $ret;
		} else {
			throw new Exception("Group ID is incorrect or is not set");
		}
	}
}

?>
