<?php
/*
 *      invoice.php
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
$auth = new Auth(trim(@$_REQUEST['loginusername']), trim(@$_REQUEST['loginpassword']));
$user = User::getInstance();
$invoice = Invoice::getInstance();
$invdata = $invoice->FetchData($_GET['id']);
if(!$invdata){
	die("No invoice ID specified or this invoice doesnt exists");
}

if($auth->get_rights() == 'Admin' && $auth->check_auth() || $auth->get_rights() == 'Active' && $auth->check_auth() && $invdata['accountid'] == $user->GetID($_REQUEST['loginusername'], 'username')){
	Page::Invoice($invdata, @$_REQUEST['defgw']);
} else {
	die("You dont have rights to view this invoice");
}
?>
