<?php
/*
 *      testserver.php
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
class testserverServerModule{
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
	2 => array('type' => 'domain', 'mode' => 'free', 'name'=>'cleintdomain', 'label' => 'Domain')
	);
	return $arr;
}
/*
 * Client Options stored in presets
 */
function ClientOptions(){
	$arr = array(
	0 => array('type' => 'text', 'name' => 'bandwidth', 'label' => 'Monthly bandwith'),
	1 => array('type' => 'text', 'name' => 'diskspace', 'label' => 'Disk usage'),
	2 => array('type' => 'checkbox', 'name' => 'phpenable', 'label' => 'PHP Support')
	);
	return $arr;
}
/*
 * $operate_array given to Create() will be similar to
 * array(1=>array('somename'=>'value'))
 * where "somename" is the name you specified in operate requirements
*/
function Create($operate_array, $create_array, $client_options_array){
	//$fp = fopen("https://".$operate_array['ip']."?authinfo=".$operate_array['adminname'].":".$operate_array['adminpassword'].'&'.$create_array.'&'.$client_options_array, 'r');
	//$fr = '';
	//while(!feof($fp)){
	//	$fr .= fread($fp, 1024);
	//}
	//if(strstr($fr, 'error')){
	//	return false;
	//} else {
	//	return $fr;
	//}
	return 'OK';
}
public function Suspend($operate_array, $create_array){
	return true;
}
public function Unsuspend($operate_array, $create_array){
	return true;
}
public function Delete($operate_array, $create_array){
	return true;
}
public function Update($operate_array, $create_array, $client_options_array){
	return true;
}
}
?>
