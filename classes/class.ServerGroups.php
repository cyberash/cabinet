<?php
/*
 *      class.ServerGroups.php
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
class ServerGroups extends Base{
	public $id;
	public $name, $moduleid, $status;
	public static $sg_instance = NULL;
	private $db;
	private $raw = NULL;
	private $data = NULL;
	public function __construct(){
		parent::__construct();
		$this->db = DB::getInstance();
	}
	public static $derivatives = array();
	public static function properties(){
		return array(
			'required' => array('name', 'moduleid'),
			'values' => array('status' => '1')
			
		);
	}
	public function GetName(){
		if(!is_null($this->id) && $this->id != '' && is_numeric($this->id)){
			$this->raw = $this->db->query_first("SELECT name FROM ServerGroups WHERE `id`='".$this->id."'");
			return $this->raw;
		} else {
			throw new Exception("Problems with module id");
			return false;
		}
	}
}

?>
