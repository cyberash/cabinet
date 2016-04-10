<?php
/*
 *      class.Lang.php
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
class Lang extends Base {
	public static $derivatives = array();
	public static $langarray = array();
	public $code, $desc;
	private $data;
	public static function properties(){
		return array(
			'required' => array('code')
		);
	}
	public function __construct(){
		parent::__construct();
		
	}
	public function RetriveAllLangs(){
		if($handle = opendir(SYSTEM_PATH.'/lang')){
			while(false != ($file = readdir($handle))){
				if(strstr($file, 'php') && sizeof(explode('.', $file)) == 3){
					$name = explode('.', $file);
					$this->data[] = $name[1];
				}
			}
		return $this->data;
		} else {
			throw new Exception("Unable to open directory with langauges!");
		}
	}
	public function UpdateLangs(){
		$all_langs = $this->RetriveAllLangs();
		$active_langs = $this->GetButch();
		for($i=0;$i<count($all_langs);$i++){
			if(!$this->GetID($all_langs[$i], 'code')){
				$this->Create(array('code' => $all_langs[$i]));
			}
		}
		return true;
	}
	public function GetLang4User($userid){
		if(!is_numeric($userid)){
			throw new Exception("User ID is not numeric");
		}
		
		$usersettings = UserSettings::getInstance();
		$userlang = $usersettings->Get($userid, 'language');
		$userlang = $userlang['language'];
		if(!preg_match('/[a-z]{2}/i', $userlang)){
			$settings = Settings::getInstance();
			$userlang = $settings->Get('system.lang.default');
		}
		return $userlang;
	}
	public function GetLangPath($code){
		return SYSTEM_PATH.'/lang/lang.'.$code.'.php';
	}
}
?>
