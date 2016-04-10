<?php
/*
 *      ispmanager.php
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
class ispmanagerServerModule{
	public static $m_instance = NULL;
	public static function getInstance(){
	if(self::$m_instance == NULL){
		self::$m_instance = new self();
	}
	return self::$m_instance;
	}
function Info(){
	
}
function OperateRequirements(){
	$arr = array(
	0 => array('type'=>'text', 'label'=>'Server IP', 'name' => 'ip'),
	1 => array('type'=>'text', 'label'=>'Administrator username', 'name' => 'adminname'),
	2 => array('type'=>'password', 'label'=>'Administrator password', 'name' => 'adminpassword')
	);
	return $arr;
}
/*
 * username, password, domain options are only available at the moment
 */
function CreateOptions(){
	$arr = array(
	0 => array('type' => 'username', 'mode' => 'strict', 'name' => 'clientlogin', 'label' => 'Username'),
	1 => array('type' => 'password', 'mode' => 'strict', 'name' => 'clientpassword', 'label' => 'Password'),
	);
	return $arr;
}
/*
 * Client Options stored in presets
 */
function ClientOptions(){
	$arr = array(
	0 => array('type' => 'text', 'name' => 'preset', 'label' => 'Preset name defined in ISPManager'),
	);
	return $arr;
}
/*
 * $operate_array given to Create() will be similar to
 * array(1=>array('somename'=>'value'))
 * where "somename" is the name you specified in operate requirements
*/
function Create($operate_array, $create_array, $client_options_array){
	if(!preg_match('/^[a-zA-Z0-9]{1,32}$/',$create_array['clientlogin'])){
		throw new Exception("Customers username in wrong format");
	}
	if(!preg_match('/^[a-zA-Z0-9]{1,32}$/',$client_options_array['preset'])){
		throw new Exception("Preset name in wrong format");
	}
	$str = '&func=user.edit&sok=yes&name='.$create_array['clientlogin'].'&passwd='.$create_array['clientpassword'].'&confirm='.$create_array['clientpassword'].'&ip='.$operate_array['ip'].'&preset='.$client_options_array['preset'];
	return $this->Exec($operate_array['ip'], $operate_array['adminname'], $operate_array['adminpassword'], $str);
	/*
	$fp = fopen("https://".$operate_array['ip']."?authinfo=".$operate_array['adminname'].":".$operate_array['adminpassword'].'&func=user.edit&sok=yes&name='.$create_array['clientlogin'].'&passwd='.$create_array['clientpassword'].'&confirm='.$create_array['clientpassword'].'&preset='.$client_options_array['preset'], 'r');
	$fr = '';
	while(!feof($fp)){
		$fr .= fread($fp, 1024);
	}
	if(strstr($fr, 'error')){
		return false;
	} else {
		return $fr;
	}
	*/
	//return 'OK';
}
public function Suspend($operate_array, $create_array){
	$fp = fopen("https://".$operate_array['ip']."?authinfo=".$operate_array['adminname'].":".$operate_array['adminpassword'].'&func=user.edit&sok=yes&name='.$create_array['clientlogin'].'&passwd='.$create_array['clientpassword'].'&confirm='.$create_array['clientpassword'].'&preset='.$client_options_array['preset'], 'r');
	$fr = '';
	while(!feof($fp) || !$fp){
		$fr .= fread($fp, 1024);
	}
	if(strstr($fr, 'error')){
		return false;
	} else {
		return $fr;
	}
}
public function Unsuspend($operate_array, $create_array){
	$fp = fopen("https://".$operate_array['ip']."?authinfo=".$operate_array['adminname'].":".$operate_array['adminpassword'].'&func=user.edit&sok=yes&name='.$create_array['clientlogin'].'&passwd='.$create_array['clientpassword'].'&confirm='.$create_array['clientpassword'].'&preset='.$client_options_array['preset'], 'r');
	$fr = '';
	while(!feof($fp)){
		$fr .= fread($fp, 1024);
	}
	if(strstr($fr, 'error')){
		return false;
	} else {
		return $fr;
	}
}
public function Delete($operate_array, $create_array){
	if(!preg_match('/^[a-zA-Z0-9]{1,32}$/',$create_array['clientlogin'])){
		throw new Exception("Customers username in wrong format");
	}
	$str = '&func=user.delete&sok=yes&elid='.strtolower($create_array['clientlogin']);
	return $this->Exec($operate_array['ip'], $operate_array['adminname'], $operate_array['adminpassword'], $str);
}
public function Update($operate_array, $create_array, $client_options_array){
	$fp = fopen("https://".$operate_array['ip']."?authinfo=".$operate_array['adminname'].":".$operate_array['adminpassword'].'&func=user.edit&sok=yes&name='.$create_array['clientlogin'].'&passwd='.$create_array['clientpassword'].'&confirm='.$create_array['clientpassword'].'&preset='.$client_options_array['preset'], 'r');
	$fr = '';
	while(!feof($fp)){
		$fr .= fread($fp, 1024);
	}
	if(strstr($fr, 'error')){
		return false;
	} else {
		return $fr;
	}
}
private function Exec($ip, $username, $password, $addstr){
	if(!preg_match('/^[a-zA-Z0-9]{1,32}$/',$username)){
		throw new Exception("Administrator's username is in wrong format");
	}
	if($password == '' || $password == NULL){
		throw new Exception("Administrator's password must not be empty");
	}
	if(ini_get('allow_url_fopen') == 0){
		throw new Exception("Accessing remote files is disabled by allow_url_fopen directive; consider enabling allow_url_fopen directve in your php.ini file");
	}
	if(strlen($ip) < 3){
		throw new Exception("Server IP is too short");
	}
	$fr = '';
	$fp = fopen("https://".$ip."/manager/ispmgr?out=xml&authinfo=".$username.":".$password.$addstr, 'r');
	if(!$fp){
		throw new Exception("Unable to open ispmanager");
	} else {
		while(!feof($fp)){
			$fr .= fread($fp, 1024);
		}
		if(strstr($fr, 'error')){
			throw new Exception("Error occuried while executing remote command: ".$fr);
		} else {
			return $fr;
		}
	}
	
}
}
?>