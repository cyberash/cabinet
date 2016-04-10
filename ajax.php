<?php
/*
 *      ajax.php
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
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once('../lib/core.php');
$methods = array(0 => 'username', 1 => 'email', 2 => 'queryUsernames', 3 => 'querySearch');
for($i = 0; $i < count($methods); $i++){
	if(isset($_GET[$methods[$i]])){
		$method = $methods[$i];
		$tocheck = $_GET[$methods[$i]];
	}
}
switch ($method) {
	case 'username':
		$user = new User($tocheck);
		if($user->FetchData()){
			echo "0";
		} else {
			echo "1";
		}
	break;
	case 'email':
	$valid = new Validator($tocheck);
		if($valid->email()){
			echo 1;
		} else {
			echo 0;
		}
	break;
	case 'queryUsernames':
		//there are should be check for admin rights
		//echo User::getInstance()->FetchNamesLike($tocheck);
		$names = User::getInstance()->FetchNamesLike($tocheck);
		$ret['query'] = $method;
		for($i=0;$i<count($names);$i++){
			$ret['suggestions'] = $names[$i]['username'];
		}
		echo json_encode($ret);
	break;
	case 'querySearch':
		$inv = Invoice::getInstance();
		$user = User::getInstance();
		$order = Order::getInstance();
		if(is_numeric($tocheck)){
			$invs = $inv->GetButch(5, "`id` = '".$tocheck."'");
			$users = $user->GetButch(5, "`id` = '".$tocheck."'");
			$orders = $order->GetButch(5, "`id` = '".$tocheck."'");
		} else {
			$invs = 0;
			$users = $user->GetButch(5, "`username` LIKE \"%".$tocheck."%\" OR `email` LIKE \"%".$tocheck."%\"");
			$orders = 0;
		}
		echo '<div id="searchresults">';
		if(count($invs) > 0 && is_array($invs)){
			echo '<span class="category">Invoices</span>';
			for($i=0;$i<count($invs);$i++){
				echo '
				<a href="?editinv&invid=">
				<span class="searchheading">Invoice #</span>
				<span>Customer: Order: Total: Status:</span>
				</a>';
			}
		}
		if(count($users) > 0 && is_array($users)){
			echo '<span class="category">Cutomers</span>';
			for($i=0;$i<count($users);$i++){
				echo '
				<a href="?object=managecustomer&userid='.$users[$i]['id'].'">
				<span class="searchheading">User #'.$users[$i]['id'].'</span>
				<span>'.$users[$i]['username'].' - '.$users[$i]['email'].' - '.$users[$i]['status'].'</span>
				</a>';
			}
		}
		if(count($orders) > 0 && is_array($orders)){
			echo '<span class="category">Orders</span>';
			for($i=0;$i<count($orders);$i++){
				echo '
				<a href="?object=editorder&orderid=">
				<span class="searchheading">Order #'.$orders[$i]['id'].'</span>
				<span>Order for package by customer</span>
				</a>';
			}
		}
		echo '</div>';
	break;
	default:
	echo 'PLEASE GTFO';
	exit;
}
?>
