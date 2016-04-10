<?php
/*
 *      reg.php
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
require_once('core.php');
//checking if loginned and existing user
$auth = new Auth(trim(@$_REQUEST['loginusername']), trim(@$_REQUEST['loginpassword']));
$user = User::getInstance();
Page::init('reg');
try {
	$authstatus = $auth->check_auth();
} catch (Exception $e){
	$authstatus = false;
	if(iDEBUG){
		$msg = nl2br($e);
	} else {
		$msg = $e->getMessage();
	}
	Page::$messages[] = array('type' => 'attention', 'message' => $msg);
	Page::message();
}
if(!$authstatus && !isset($_REQUEST['regDo'])){
	//registering order
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'placeorder'){
		//temprary saving order data
		$temp = TempOrder::getInstance();
		setcookie('temporderid', $temp->Create(array('data' => serialize($_POST))));
	}
	Page::setup('Registration form');
	Page::RegForm();
	
} elseif(!isset($_REQUEST['regDo'])) {
	if(@$_REQUEST['action'] == 'placeorder'){
		//placing order
		$temp = TempOrder::getInstance();
		setcookie('temporderid', $temp->Create(array('data' => serialize($_POST))));
		header("Location: /index.php");
		//Page::UserOrderPlaced();
	} else {
		Page::$messages[] = array('type' => 'attention', 'message' => 'You are already registered!');
		Page::message();
	}
	Page::RegForm();
} else {
	//saving new user
	//checking if user with specified username and or email already exists
	if($user->GetID( trim(@$_POST['username']), 'username') || $user->GetID(trim(@$_POST['email']), 'email')){
		Page::$messages[] = array('type' => 'attention', 'message' => 'User already exists with specified username or email');
		Page::message();
		Page::RegForm();
	} elseif($_REQUEST['password'] != $_REQUEST['password2']) {
		Page::$messages[] = array('type' => 'attention', 'message' => 'Passwords you entered are not equal');
		Page::message();
		Page::RegForm();
	} elseif(!$user->CheckData($_POST)){
		Page::$messages[] = array('type' => 'attention', 'message' => 'Data you entered is incorrect');
		Page::message();
		Page::RegForm();
	} else {
		try {
			$notcodedpw = $_POST['password'];
			$_POST['password'] = md5($_POST['password']);
			$userstatus = $user->Create($_POST);
		} catch(Exception $e){
			$userstatus = false;
			if(iDEBUG){
				$msg = nl2br($e);
			} else {
				$msg = $e->getMessage();
			}
			Page::$messages[] = array('type' => 'attention', 'message' => $msg);
			Page::message();
		}
		if(!$userstatus){
			Page::RegForm();
		} else {
			setcookie('loginusername', $_POST['username']);
			setcookie('loginpassword', $notcodedpw);
			Page::RegSuccess();
		}
		
	}
}
?>
