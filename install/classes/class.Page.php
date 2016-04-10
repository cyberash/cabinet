<?php
/*
 *      class.Page.php
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
class Page {
	public static $xtpl;
	public static function init(){
		self::$xtpl = XTemplate::getInstance();
		self::$xtpl->restart('template/install.tpl');
		self::$xtpl->assign('VERSION', iVersion);
	}
	public static function message($type, $message){
		if(!is_object(self::$xtpl)){
			throw new Exception("xtpl is not an object");
		}
		if(!preg_match('/^(success|attention)$/i',$type)){
			throw new Exception("unknown message type");
		}
		self::$xtpl->assign(strtoupper($type).'MSG', $message);
		self::$xtpl->parse('main.'.$type);
	}
	public static function Checks() {
		$xtpl = self::$xtpl;
		$checks = Checks::getInstance();
		try {
			foreach($checks->GetChecks() as $k => $v){
			switch($k){
				case 'PHP':
					$xtpl->assign(strtoupper($v).'MSG', 'PHP minimum version '.MIN_PHP_VERSION);
				break;
				case 'MYSQL':
					$xtpl->assign(strtoupper($v).'MSG', 'MySQL supported by your PHP installation');
				break;
				case 'CONFIG':
					$xtpl->assign(strtoupper($v).'MSG', 'Configuration file on its place and writeable');
				break;
			}
			$xtpl->parse('main.checks.'.$v);
			}
		} catch (Exception $e){
			die("Fatal error: ".$e->getMessage());
		}
		
		$xtpl->parse('main.checks');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function Paths(){
		$xtpl = self::$xtpl;
		//currently, I just assume this file is located in install/classes. This probably should be changed to code walking 1-2 levels up to find base dir path.
		//$xtpl->assign('WWWPATH', str_replace('/install/classes', '', __DIR__));
		$xtpl->assign('WWWPATH', preg_replace('/.install.classes/', '', __DIR__));
		$xtpl->assign('DOMAIN', $_SERVER['HTTP_HOST']);
		$xtpl->parse('main.pathes');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function Database(){
		$xtpl = self::$xtpl;
		$xtpl->parse('main.database');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function PerformDBinstall(){
		$xtpl = self::$xtpl;
		$db = DB::getInstance();
		if($db->errno !=0){
			self::message('attention', 'Error when trying to connect to the database occuried. Errno: '.$db->errno.'. Get back and try again!');
		} else {
			//var_dump(file_exists(DBFILE));
			if(!defined('DBFILE') || !file_exists(DBFILE)){
				self::message('attention', 'SQL dump file not found or its patch is not defined');
			} else {
				$data = file_get_contents(DBFILE);
				$query_array = splitQueries($data);
				if(count($query_array) < 1){
					self::message('attention', 'No SQL queries found!');
				} else {
					for($i=0;$i<count($query_array);$i++){
						if(strlen($query_array[$i]) > 0){
							$xtpl->assign('QUERY', 'QUERY');
							if($db->query($query_array[$i])){
								$xtpl->assign('RESULT', 'OK');
							} else {
								$xtpl->assign('RESULT', 'Query '.$query_array[$i].' <br> ERROR '.$db->errno.':'.$db->error);
							}
							$xtpl->parse('main.dbinstall.query');
						}
					}
					$xtpl->parse('main.dbinstall');
				}
			}
		}
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddAdmin(){
		$xtpl = self::$xtpl;
		$xtpl->parse('main.addadmin');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function Done(){
		$xtpl = self::$xtpl;
		$xtpl->parse('main');
		$xtpl->out('main');
	}
}
?>
