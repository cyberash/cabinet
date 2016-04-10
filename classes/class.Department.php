<?php
/*
 *      class.Department.php
 *      
 *      Copyright 2010 Artem Zhirkov <artemz@artemz-desktop>
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
class Department extends Base {
	public $name, $type;
	public static $derivatives = array();
	public static function properties(){
		return array(
			'required' => array('name', 'type'),
			'values' => array()
			
		);
	}
	public function __construct(){
		parent::__construct();
	}
	public function GetName($id){
		if(!is_numeric($id)){
			throw new Exception("Department ID is not set");
		} else {
			$temp = $this->FetchData($id);
			return $temp['name'];
		}
	}
}
?>
