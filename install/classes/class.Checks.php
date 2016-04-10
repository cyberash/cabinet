<?php
/*
 *      class.Checks.php
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
class Checks {
	public static $c_instance;
	public static function getInstance(){
		if(self::$c_instance == NULL){
			self::$c_instance = new self();
		}
		return self::$c_instance;
	}
	public function GetChecks(){
		if(!$this->PHPCheck()){
			$ret['PHP'] = 'attention';
		} else {
			$ret['PHP'] = 'success';
		}
		if(!$this->MySQLCheck()){
			$ret['MYSQL'] = 'attention';
		} else {
			$ret['MYSQL'] = 'success';
		}
		if(!$this->CheckConfig()){
			$ret['CONFIG'] = 'attention';
		} else {
			$ret['CONFIG'] = 'success';
		}
		return $ret;
	}
	public function PHPCheck(){
		if(!defined('MIN_PHP_VERSION')){
			throw new Exception("MIN_PHP_VERSION is not defined");
		}
		if(version_compare(PHP_VERSION, MIN_PHP_VERSION, '<')){
			return false;
		} else {
			return true;
		}
	}
	public function MySQLCheck(){
		if(!function_exists('mysql_get_server_info')){
			return false;
		}
		return true;
	}
	public function CheckConfig(){
		if(!file_exists('../config.php')){
			throw new Exception("Unable to locate configuration file");
		}
		if(!is_writable('../config.php')){
			return false;
		} else {
			return true;
		}
	}
}
?>
