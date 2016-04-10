<?php
/**
    MultiCabinet - billing system for WHM panels.
    Copyright (c) 2008, Vladimir M. Andreev. All rights reserved.

    This file is part of MultiCabinet billing system.

    MultiCabinet is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    MultiCabinet is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
**/
$step = isset($_REQUEST['step']) ? $_REQUEST['step'] : '';
$do = isset($_REQUEST['do']) ? $_REQUEST['do'] : '';
require_once('tinycore.php');
Page::init();

$config = new Config("../config.php");
if($step == '' && $do == ''){
	Page::Checks();
} elseif($step != ''){
	switch($step){
		case 2:
			Page::Paths();
		break;
		case 3:
			$error = 0;
			//$ini = ini_manager::getInstance();

			if(!preg_match('/^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/',$_POST['domain'])){
				$error++;
				Page::message('attention', 'Domain name in wrong format');
			}
			
			if(!preg_match('/(^(\/{1}[^\/]+)+$)|(^[A-Za-z]:{1}(\\\{1}[^\\\]+)+$)/', $_POST['wwwpath'])){
				$error++;
				Page::message('attention', 'Path to WWW directory in wrong format');
			}
			if($error > 0){
				Page::Paths();
			} else {
				//$ini->set_entry('system','path',$_POST['wwwpath']);
				$config->set('SYSTEM_PATH',$_POST['wwwpath']);
				$config->set('SYSTEM_DOMAIN',$_POST['domain']);
				Page::Database();
			}
		break;
		case 4:
			$ini = ini_manager::getInstance();
			if(!preg_match('/^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/', $_POST['host']) || !preg_match('/^[0-9a-zA-Z_]+$/', $_POST['dbname']) || !preg_match('/^[0-9a-zA-Z_]+$/', $_POST['dbuser']) || strlen($_POST['dbpassword']) < 3){
				Page::message('attention', 'Wrong data entered. Try again');
				Page::Database();
			} else {
				$config->set('DATABASE_HOST',$_POST['host']);
				$config->set('DATABASE_USER',$_POST['dbuser']);
				$config->set('DATABASE_PASSWORD',$_POST['dbpassword']);
				$config->set('DATABASE_NAME',$_POST['dbname']);
				Page::PerformDBinstall();
			}
		break;
		case 5:
			Page::AddAdmin();
		break;
		case 6:
			$db = DB::getInstance();
			$time = Time::getInstance();
			if(!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) || !preg_match('/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $_POST['email'])){
				Page::message('attention', 'Admin username or email in wrong format. Go back and try again');
				Page::AddAdmin();
			} elseif($_POST['password'] != $_POST['password2']){
				Page::message('attention', 'Passwords do not match');
				Page::AddAdmin();
			} else {
				if($db->query_insert('User', array('username' => $_POST['username'], 'password' => md5($_POST['password']), 'email' => $_POST['email'], 'opentime' => $time->UtoM(time()), 'status' => 'Admin'))){
					Page::message('success', 'Success! Installation completed. We recommend you to remove /install/ folder from the server');
				} else {
					Page::message('attention', 'Error! Installation not completed. Please, try again');
				}
				$config->set('IS_INSTALLED',1);
				Page::Done();
			}
	}
}
?>
