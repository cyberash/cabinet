<?php
/*
 *      class.GatewayModule.php
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
class GatewayModule{
	public $id;
	public static $gm_instance = NULL;
	private $db;
	private $raw;
	public $name;
	public function __construct(){
		$this->db = DB::getInstance();
	}
	public static function getInstance(){
		if(self::$gm_instance == NULL){
			$gm_instance = new self();
		}
		return $gm_instance;
	}
	public function GetID($name=NULL){
		if(!is_string($name) && !is_string($this->name)){
			throw new Exception("Gateway name is not set or set incorrectly");
		} elseif(!is_string($name)){
			$name = $this->name;
		}
		$this->raw = $this->db->query_first("SELECT id FROM GatewayModules WHERE `modulename`='".$name."'");
		return $this->raw['id'];
	}
	public function GetName($id=NULL){
		if(!is_numeric($id) && !is_numeric($this->id)){
			throw new Exception("Gateway id is not set or set incorrectly");
		} elseif(!is_numeric($id)){
			$id = $this->id;
		}
		$this->raw = $this->db->query_first("SELECT `modulename` FROM GatewayModules WHERE `id`='".$id."'");
		return $this->raw['modulename'];
	}
	public function GetButch($num=''){
		if(is_numeric($num)){
			$this->raw = $this->db->fetch_all_array('SELECT * FROM GatewayModules ORDER BY id DESC LIMIT 0,'.$num);
		} else {
			$this->raw = $this->db->fetch_all_array('SELECT * FROM GatewayModules ORDER BY id DESC');
		}
		return $this->raw;
	}
	public function RetriveAllModules(){
		$dir = SYSTEM_PATH.'/paymodules';
		if($handle = opendir($dir)){
			while(false != ($file = readdir($handle))){
				if(strstr($file, 'php') && sizeof(explode('.', $file)) < 3){
					$name = explode('.', $file);
					$this->data[] = $name[0];
				}
			}
		return $this->data;
		} else {
			throw new Exception("Unable to open directory with gateway modules: ".$dir);
		}
	}
	public function FetchData($id=NULL){
		if(!is_numeric($id) && !is_numeric($this->id)){
			throw new Exception("No gateway module id specified");
		} elseif(!is_numeric($id)){
			$id = $this->id;
		}
		$this->raw = $this->db->query_first("SELECT * FROM GatewayModules WHERE `id`='".$id."'");
		return $this->raw;
	}
	public function Create($name=NULL){
		$currency = NULL;
		if(!is_string($name) && !is_string($this->name)){
			throw new Exception("Gateway name is not set or set incorrectly");
		} elseif(!is_string($name)){
			$name = $this->name;
		}
		if(!class_exists($name."PaymentGateway")){
			throw new Exception("Unable to find gateway madule with name: ".$name."PaymentGateway");
		}
		$curr = Currency::getInstance();
		$currs = $curr->GetButch();
		$defcurr = $curr->GetDefaultCurrency();
		$prov = call_user_func(array($name."PaymentGateway",'getInstance'));
		$provcurr = $prov->Currency();
		for($i=0;$i<count($provcurr);$i++){
			if($provcurr[$i] == $defcurr['name'] && $currency == NULL){
				$currency = $provcurr[$i];
			} else {
				for($n=0;$n<count($currs);$n++){
					if($provcurr[$i] == $currs[$n]['name'] && $currency == NULL){
						$currency = $provcurr[$i];
					}
				}
			}
			
		}
		if(!is_string($currency)){
			throw new Exception("This module does not support any system currency");
		} else {
			$this->raw = $this->db->query_insert('GatewayModules', array('modulename' => $name, 'currency' => $currency, 'data' => serialize($prov->OperateRequirements())));
			return $this->raw;
		}
	}
	/*
	 * @paramter - modulename, currency, data
	 */
	public function Update($parameter, $value, $id){
		if(!is_string($parameter) || !is_numeric($id)){
			throw new Exception("No parameter or gateway id to update specified");
		}
		$this->raw = $this->db->query_update('GatewayModules', array($parameter => $value), '`id`='.$id);
		return $this->raw;
	}
	public function Delete($id=''){
		if(!is_numeric($id) && !is_numeric($this->id)){
			throw new Exception("Gateway ID is not set or set incorrectly");
		} elseif(!is_numeric($id)){
			$id = $this->id;
		}
		$this->raw = $this->db->query_first("DELETE FROM `GatewayModules` WHERE `GatewayModules`.`id` = '".$id."'");
		return $this->raw;
	}
	
	//for filling up submitted data
	public function generateOperateArray($array){
		if(is_string($this->name)){
			$module = call_user_func(array($this->name."PaymentGateway",'getInstance'));
			$oparray = $module->OperateRequirements();
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
			throw new Exception("Module name is not set correctly");
		}
	}
}
?>
