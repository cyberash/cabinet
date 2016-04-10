<?php
/*
 *      class.NotificationModule.php
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
class NotificationModule extends Base{
	public $name, $shortname;
	public static $derivatives = array('NotifyModuleData' => 'moduleid');
	public static function properties(){
		$time = Time::getInstance();
		return array(
			'required' => array('name', 'shortname')
		);
	}
	public function __construct(){
		$this->db = DB::getInstance();
		parent::__construct();
	}
	
	public function GetOpReqs($id){
		if(!is_numeric($id)){
			throw new Exception("Module ID is not set");
		}
		$name = $this->FetchData($id);
		$name = $name['name'].'notifymodule';
		$module = $name::getInstance();
		return $module->OperateRequirements();
	}
	public function Send($moduleid, $to, $subject, $message){
		//$user = User::getInstance();
		$nmd = NotifyModuleData::getInstance();
		$moduledata = $this->FetchData($moduleid);
		if(!$moduledata){
			throw new Exception("Notification module not found with id #".$moduleid);
		}
		//$userdata = $user->FetchData($userid);
		$reqs = array();
		$reqs_array = $nmd->GetButch('',"`moduleid` = '".$moduleid."'");
		for($i=0;$i<count($reqs_array);$i++){
			$reqs[$reqs_array[$i]['name']] = $reqs_array[$i]['value'];
		}
		$classname = $moduledata['name'].'notifymodule';
		if(!class_exists($classname)){
			throw new Exception("Class not found with name: ".$classname);
		}
		//var_dump($classname);
		$module = $classname::getInstance();
		return $module->Send($reqs,$to,$subject,$message);
	}
	public function RetriveAllModules(){
		$dir = SYSTEM_PATH.'/notifymodules';
		if($handle = opendir($dir)){
			while(false != ($file = readdir($handle))){
				if(strstr($file, 'php') && sizeof(explode('.', $file)) < 3){
					$name = explode('.', $file);
					$this->data[] = $name[0];
				}
			}
		return $this->data;
		} else {
			throw new Exception("Unable to open directory with notification modules: ".$dir);
		}
	}
}
?>
