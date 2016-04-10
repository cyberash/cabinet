<?php
/*
 *      class.User.php
 *      
 *      Copyright 2010 Artem Zhirkov <artemz@artemz-laptop>
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
class User extends Base {
	public $username, $password, $email, $info, $status, $opentime, $lastlogin;
	public static $derivatives = array('Invoice' => 'accountid', 'Order' => 'accountid', 'Ticket' => 'userid', 'Profile' => 'userid');
	private $raw = NULL;
	private $db = NULL;
	private $data;
	public function __construct(){
		parent::__construct();
		$this->db = DB::getInstance();
	}
	public static function properties(){
		$time = Time::getInstance();
		return array(
			'required' => array('username', 'password', 'email'),
			'values' => array('opentime' => $time->UtoM(time()), 'status' => 'Active')
			
		);
	}
	public function FetchNamesLike($n){
		$this->raw = $this->db->fetch_all_array("SELECT `username` FROM `User` WHERE `username` LIKE '$n%'");
		if(!$this->raw){
			return false;
		} else {
			return $this->raw;
		}
	}
	public function CreateProfile($userid,$name,$surname,$sex='',$phone='',$country='',$address='',$city='',$postcode='',$company='',$icq='',$jabber=''){
		if(!is_numeric($userid) || !is_string($name) || !is_string($surname)){
			throw new Exception("Data is not set or set in wrong format");
		}
		if($sex !='' && !ereg("^(M|F)$",$sex)){
			throw new Exception("Sex in wrong format");
		}
		if($phone != '' && strlen($phone) > 15){
			throw new Exception("Phone number is a way too long");
		}
		if($country != '' && strlen($country) > 59){
			throw new Exception("Country name is a way too long");
		}
		if($postcode !='' && !is_numeric($postcode)){
			throw new Exception("Post code must be numeric");
		}
		if($jabber != '' && !$this->isValidEmail($jabber)){
			throw new Exception("Jabber address is invalid");
		}
		$arr = array('AccountID' => $userid, 'name' => $name, 'surname' => $surname, 'sex' => $sex, 'phone' => $phone, 'country' => $country, 'address' => $address, 'city' => $city, 'postcode' => $postcode, 'company' => $company, 'icq' => $icq, 'jabber' => $jabber);
		$this->raw = $this->db->query_insert('Profiles', $arr);
		return $this->raw;
	}
	function GetUsername($id=''){
		if(!is_numeric($id) && !is_numeric($this->id)){
			throw new Exception("User ID is not set");
		} elseif(!is_numeric($id)){
			$id = $this->id;
		}
		$this->raw = $this->db->query_first("SELECT `username` FROM `User` WHERE `id`='".$id."'");
		if(!$this->raw){
			return false;
		} else {
			return $this->raw['username'];
		}
	}
	function ResetPW(){
	}
	function CheckData($data){
		if(!$this->data){
			$this->data = $data;
		}
		if(!$this->data || $this->data == NULL || !is_array($this->data)){
			return false;
		} elseif(strlen($this->data['username']) < 2 || strlen($this->data['password']) < 2 || !$this->isValidEmail($this->data['email'])){
				return false; 
		} else {
			return true;
		}
	}
	function isValidEmail($email){
	$isValid = true;
	$atIndex = strrpos($email, "@");
	if(is_bool($atIndex) && !$atIndex){
		$isValid = false;
	} else {
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if($localLen < 1 || $localLen > 64){
			$isValid = false;
		} elseif($domainLen < 1 || $domainLen > 255){
			$isValid = false;
		} elseif($local[0] == '.' || $local[$localLen-1] == '.'){
			$isValid = false;
		} elseif(preg_match('/\\.\\./', $local)){
			$isValid = false;
		} elseif(!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
			$isValid = false;
		} elseif(preg_match('/\\.\\./', $domain)){
			$isValid = false;
		} elseif(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))){
			if(!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))){
				$isValid = false;
			}
		}
		if($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))){
			$isValid = false;
		}
		return $isValid;
	}
	}
}

?>
