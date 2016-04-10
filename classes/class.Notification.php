<?php
/*
 *      class.Notification.php
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
class Notification extends Base {
	public $text, $subject, $userid, $moduleid, $date, $address, $db, $status;
	public static $derivatives = array();
	public static function properties(){
		$time = Time::getInstance();
		return array(
			'required' => array('text', 'subject', 'moduleid', 'address', 'status'),
			'values' => array('date' => $time->UtoM(time()))
		);
	}
	public function __construct(){
		$this->db = DB::getInstance();
		parent::__construct();
	}
	public function Send($users, $data=false, $templatetype=false, $subject = false, $message = false){
		$nt = NotifyTemplate::getInstance();
		//$ntdata = $nt->FetchData($templateid);
		
		$us = UserSettings::getInstance();
		$setting = Settings::getInstance();
		$nm = NotificationModule::getInstance();
		$user = User::getInstance();
		$lang = Lang::getInstance();
		
		for($i=0;$i<count($users);$i++){
			$usersettings = $us->Get($users[$i]['id']);
			if(@$usersettings[$templatetype] != "0"){
				
				if(is_numeric($usersettings['language'])){
					$langdata = $lang->FetchData($usersettings['language']);
					$langcode = $langdata['code'];
				} else {
					$langcode = $setting->Get('system.lang.default');
				}
				if(strlen($langcode) != 2) {
					throw new Exception("Lang code not found or in wrong format");
				}
				
				if(is_array($data) && $templatetype != '' && $templatetype != false){
					if(!is_numeric($templateid = $nt->GetID($templatetype, 'type', "`langcode` = '".$langcode."'")) && !is_numeric($templateid = $nt->GetID($templatetype, 'type', "`langcode` = 'en'"))){
						
						throw new Exception("Notify Template not found with type ".$templatetype." and language ".$langcode);
					}
					
					$ntdata = $nt->FetchData($templateid);
					if(!is_array($ntdata)) continue;
					$message = $this->prepare_template($ntdata['text'],$data);
				} elseif(is_string($subject) && is_string($message)) {
					$ntdata['subject'] = $subject;
				} else {
					throw new Exception("Wrong parameters specified");
				}
				
				if(is_numeric($usersettings['notifymodule']) && is_string($usersettings['notifyaddress'])){
					$moduleid = $usersettings['notifymodule'];
					$address = $usersettings['notifyaddress'];
				} else {
					$userdata = $user->FetchData($users[$i]['id']);
					$moduleid = $setting->Get('system.notifymodule.default');
					$address = $userdata['email'];
				}
				if(strlen($address) < 2 || !is_numeric($moduleid)) continue;
				
				if($nm->Send($moduleid, $address, $ntdata['subject'], $message)){
					$status = 'Done';
				} else {
					$status = 'Fail';
				}
				
				$this->Create(array('userid' => $users[$i]['id'], 'moduleid' => $moduleid, 'subject' => $ntdata['subject'], 'text' => $message, 'address' => $address, 'status' => $status));
			} else {
				return true;
			}
			
		}
		return true;
	}
	function prepare_template($data, $array){
	$pattern = "/{[^}]*}/";
	preg_match_all($pattern, $data, $matches);
	$matches = $matches[0];
	for($i=0;$i<count($matches);$i++){
		$pure = str_replace('{', '', $matches[$i]);
		$pure = str_replace('}', '', $pure);
		$pure_arr = explode('.', $pure);
		if(count($pure_arr) == 2){
			if(array_key_exists($pure_arr[0], $array) && array_key_exists($pure_arr[1], @$array[$pure_arr[0]])){
				$data = str_replace($matches[$i], $array[$pure_arr[0]][$pure_arr[1]], $data);
			} else {
				$data = str_replace($matches[$i], '', $data);
			}
		} elseif(count($pure_arr) == 1){
			if(array_key_exists($pure_arr[0], $array)){
				$data = str_replace($matches[$i], $array[$pure_arr[0]], $data);
			} else {
				$data = str_replace($matches[$i], '', $data);
			}
		} else {
			return false;
		}
	}
	return $data;
	}
}
?>
