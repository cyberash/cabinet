<?php
/*
 *      class.ServerModule.php
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
class ServerModule extends Base {
	public $id;
	public $name, $modulename, $status;
	public static $derivatives = array();
	public $db;
	public $raw = NULL;
	public $data = NULL;
	public function __construct(){
		parent::__construct();
		$this->db = DB::getInstance();
	}
	public static function properties(){
		return array(
			'required' => array('modulename'),
			'values' => array('status' => '1')
			
		);
	}
	public function CreateService($operate_array, $create_data, $client_options){
		if(!is_array($operate_array)){
			throw new Exception("Operate array is not set!");
		} elseif(!is_array($create_data)){
			throw new Exception("Create array is not set!");
		} elseif(!is_array($client_options)){
			throw new Exception("Client options is not set!");
		}
		if(!$this->name){
			$this->name = $this->GetName();
		}
		$module = call_user_func(array($this->name."ServerModule",'getInstance'));
		if(!($module->Create($operate_array, $create_data, $client_options))){
			return false;
		} else {
			return true;
		}
	}
	public function SuspendService($operate_array, $create_data){
		if(!is_array($operate_array)){
			throw new Exception("Operate array is not set!");
		} elseif(!is_array($create_data)){
			throw new Exception("Create array is not set!");
		}
		if(!$this->name){
			$this->name = $this->GetName();
		}
		$modulename = $this->name."ServerModule";
		if(!class_exists($modulename)){
			throw new Exception("Server module not found with name ".$modulename);
		} elseif($module->Suspend($operate_array, $create_data)){
			return true;
		} else {
			return false;
		}
	}
	public function UnsuspendService($operate_array, $create_data){
		if(!is_array($operate_array)){
			throw new Exception("Operate array is not set!");
		} elseif(!is_array($create_data)){
			throw new Exception("Create array is not set!");
		}
		if(!$this->name){
			$this->name = $this->GetName();
		}
		$modulename = $this->name."ServerModule";
		if(!class_exists($modulename)){
			throw new Exception("Server module not found with name ".$modulename);
		} elseif($module->Unsuspend($operate_array, $create_data)){
			return true;
		} else {
			return false;
		}
	}
	public function DeleteService($operate_array, $create_data){
		if(!is_array($operate_array)){
			throw new Exception("Operate array is not set!");
		} elseif(!is_array($create_data)){
			throw new Exception("Create array is not set!");
		}
		if(!$this->name){
			$this->name = $this->GetName();
		}
		$modulename = $this->name."ServerModule";
		if(!class_exists($modulename)){
			throw new Exception("Server module not found with name ".$modulename);
		} elseif($modulename::getInstance()->Delete($operate_array, $create_data)){
			return true;
		} else {
			return false;
		}
	}
	public function UpdateService($operate_array, $create_data, $client_options){
		if(!is_array($operate_array)){
			throw new Exception("Operate array is not set!");
		} elseif(!is_array($create_data)){
			throw new Exception("Create array is not set!");
		}
		if(!$this->name){
			$this->name = $this->GetName();
		}
		$modulename = $this->name."ServerModule";
		if(!class_exists($modulename)){
			throw new Exception("Server module not found with name ".$modulename);
		} elseif($module->Create($operate_array, $create_data, $client_options)){
			return true;
		} else {
			return false;
		}
	}
	public function RetriveAllModules(){
		$dir = SYSTEM_PATH.'/servermodules';
		if($handle = opendir($dir)){
			while(false != ($file = readdir($handle))){
				if(strstr($file, 'php') && sizeof(explode('.', $file)) < 3){
					$name = explode('.', $file);
					$data[] = $name[0];
				}
			}
		return $data;
		} else {
			throw new Exception("Unable to open directory with server modules: ".$dir);
		}
	}
	public function GetName($id=''){
		if(!is_numeric($id) && !is_numeric($this->id)){
			throw new Exception("Problems with module id");
		} elseif(!is_numeric($id)){
			$id = $this->id;
		}
		$this->raw = $this->db->query_first("SELECT modulename FROM ServerModule WHERE `id`='".$id."'");
		return $this->raw['modulename'];
	}
	public function UpdateStatus($status){
		if($this->id && is_binary($status)){
			$this->raw = $this->db->query_update("ServerModule", array('status' => $status));
			if($this->raw){
				return true;
			} else {
				return false;
			}
		} else {
			throw new Exception("Problems with module id or with status");
		}
	}
	public function getOperateArray(){
		
		if(isset($this->id)){
			
			$name = $this->GetName().'ServerModule';
			if(!class_exists($name)){
				throw new Exception("Unable to found server module with name: ".$name);
			}
			
			if(!is_object($module = $name::getInstance())){
				throw new Exception("Unable to get instance of server module with name: ".$name);
			}
			
			if(is_array($module->OperateRequirements())){
				return $module->OperateRequirements();
			} else {
				throw new Exception("Problems with server module");
			}
		} else {
			throw new Exception("Module ID is not set");
		}
	}
	/*
	 * $aname must be a type of array returned by server module
	*/
	public function getArray($aname){
		if(isset($this->id)){
			$this->name = $this->GetName();
			//5.3
			$module = call_user_func(array($this->name."ServerModule",'getInstance'));
			//should be changed to direct call later (?)
			switch($aname){
				case 'Operate':
					return $module->OperateRequirements();
				break;
				case 'Create':
					return $module->CreateOptions();
				break;
				case 'Client':
					return $module->ClientOptions();
				break;
				default:
					throw new Exception("Unknown server module method");
			}
		}
	}
}
?>
