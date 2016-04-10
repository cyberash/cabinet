<?php
/*
 *      order.php
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
Page::init('order');

if(!is_numeric(@$_REQUEST['id'])){
	Page::setup('Products list');
	Page::ListProducts();
	//show list of products
} else {
	Page::setup("Order product #".$_REQUEST['id']);
	Page::OrderPkg($_REQUEST['id']);
}
/*	
} elseif(!isset($_REQUEST['username']) || !isset($_REQUEST['password']) || strlen($_REQUEST['username']) < 2 || strlen($_REQUEST['password']) < 2){
	setcookie('pkgid', $_REQUEST['id']);
	$xtpl->parse('main');
	$xtpl->out('main');
} else {
	$auth = new Auth(trim($_REQUEST['username']), trim($_REQUEST['password']));
	$xtpl->restart("themes/simpla/login.tpl");
	if(!$auth->check_auth()){
		$xtpl->parse('main.info');
		$xtpl->parse('main');
		$xtpl->out('main');
		exit;
	} else {
		Page::UserOrderPkg($_REQUEST['id']);
	}
}
*/
?>
