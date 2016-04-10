<?php
/*
 *      class.Base.php
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
abstract class Base {
	private $db,$raw;
	private static $aoInstance = array();
	private static $class_vars = array();
	public static $error = array();
	public function __construct(){
		$this->db = DB::getInstance();
	}
	final public static function getInstance(){
		$calledClassName = get_called_class();
		self::$class_vars = get_class_vars($calledClassName);
		if (!isset(self::$aoInstance[$calledClassName])){

			self::$aoInstance[$calledClassName] = new $calledClassName();
		}
		return self::$aoInstance[$calledClassName];
	}
	final private function getCleanCreateArray($array){
		$calledClassName = get_called_class();
		if(count(self::$class_vars) < 1){
			throw new Exception("Called object has no properties");
		} else {
			$retarray = array();
			$class_vars = get_class_vars($calledClassName);
			$prop = call_user_func(array($calledClassName, 'properties'));
			foreach($class_vars as $key => $value){
				//if($key == 'moduleid') echo 1;
				if(array_key_exists($key,$array) && strlen($array[$key]) > 0){
					$retarray[$key] = $array[$key];
				} elseif(array_key_exists($key,$prop['required'])){
					self::$error[] = 'Required property not found';
				} elseif(array_key_exists($key,$prop['values'])){
					$retarray[$key] = $prop['values'][$key];
					
				
				}
			}
			//var_dump($retarray);
			return $retarray;
		}
	}
	public function GetButch($num='', $where=1, $orderby='id', $order = 'DESC', $limit = 0){
		$calledClassName = get_called_class();
		if(strlen($num) > 0 && !is_numeric($num)){
			throw new Exception("number to fetch in wrong format");
		}
		if(!preg_match('/^(ASC|DESC)$/i', $order)){
			throw new Exception("order in wrong format");
		}
		if(!is_numeric($limit)){
			throw new Exception("limit in wrong format");
		}
		
		if(is_numeric($num)){
			$limitstr = ' LIMIT '.$limit.','.$num;
		} else {
			$limitstr = '';
		}
		
		$orderby = '`'.$orderby.'` '.$order;
		$this->raw = $this->db->fetch_all_array('SELECT * FROM `'.$calledClassName.'` WHERE '.$where.' ORDER BY '.$orderby.' '.$limitstr);
		return $this->raw;
	}
	public function Calculate($where=1){
		$calledClassName = get_called_class();
		$this->raw = $this->db->query_first('SELECT COUNT(*) FROM `'.$calledClassName.'` WHERE '.$where);
		return $this->raw['COUNT(*)'];
	}
	public function Update($that,$bythat,$id=''){
		$calledClassName = get_called_class();
		if(!is_numeric($id) && !is_numeric($this->id)){
			throw new Exception("id is not set");
		} elseif(!is_numeric($id)){
			$id = $this->id;
		}
		$this->raw = $this->db->query_update($calledClassName, array($that => $bythat), 'id = '.$id);
		return $this->raw;
	}
	public function BatchUpdate($array, $id){
		$calledClassName = get_called_class();
		if(count($array) < 1){
			throw new Exception('Data array is empty');
		}
		if(!is_numeric($id) && !is_numeric($this->id)){
			throw new Exception("id is not set");
		} elseif(!is_numeric($id)){
			$id = $this->id;
		}
		$array = $this->getCleanCreateArray($array);
		$this->raw = $this->db->query_update($calledClassName, $array, 'id = '.$id);
	}
	public function Create($array){
		$calledClassName = get_called_class();
		if(count($array) < 1){
			throw new Exception('Data array is empty');
		}
		
		$array = $this->getCleanCreateArray($array);
		if(count(self::$error) > 0){
			return false;
		}
		return $this->db->query_insert($calledClassName,$array);
	}
	public function FetchData($id, $andwhere=''){
		$calledClassName = get_called_class();
		if(!is_numeric($id)){
			throw new Exception("ID is not set");
		}
		if(strlen($andwhere) > 0){
			$where = " WHERE `id`='".$id."' AND ".$andwhere;
		} else {
			$where = " WHERE `id`='".$id."'";
		}
		return $this->db->query_first("SELECT * FROM `".$calledClassName."`".$where);
	}
	public function GetID($byid, $by, $andwhere=''){
		$calledClassName = get_called_class();
		if(!is_string($by)){
			throw new Exception("Parameters in wrong format");
		}
		if(strlen($andwhere) > 0){
			$where = " WHERE `".$by."` = '".$byid."' AND ".$andwhere;
		} else {
			$where = " WHERE `".$by."` = '".$byid."'";
		}
		$this->raw = $this->db->query_first('SELECT `id` FROM `'.$calledClassName.'`'.$where);
		return $this->raw['id'];
	}
	public function Delete($id){
		$calledClassName = get_called_class();
		if(!is_numeric($id)){
			throw new Exception("ID is not set");
		}
		$derv_class_vars = get_class_vars($calledClassName);
		$dervs = $derv_class_vars['derivatives'];
		foreach($dervs as $key => $value){
			//$derv = call_user_func(array($key, 'getInstance'));
			if(!is_string($key)){
				throw new Exception("Derivate class ".$key." of class ".$calledClassName." do not exists");
			}
			$derv = $key::getInstance();
			$dervbutch = $derv->GetButch('','`'.$value.'`= "'.$id.'"');
			for($i=0;$i<count($dervbutch);$i++){
				$derv->Delete($dervbutch[$i]['id']);
			}
		}
		return $this->db->query_first("DELETE FROM `".$calledClassName."` WHERE `id`='".$id."'");
	}
}
?>
