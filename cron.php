<?php
/*
 *      cron.php
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
$setting = Settings::getInstance();
$order = Order::getInstance();
$nt = Notification::getInstance();
$user = User::getInstance();
if($setting->Get('system.cron.autosuspend') == '1'){
	echo "Starting autosuspention of overdue orders...\n";
	$suspended = $order->SuspendOverdueOrders();
	echo "Done.\n";
	}
if($setting->Get('system.cron.autoterminate') == '1'){
	echo "Starting autotermination of overdue orders...\n";
	$terminated = $order->TerminateOverdueOrders();
	echo "Done.\n";
	}
echo "Generating invoices...";
 $order->generateLastInv();

if(is_array($suspended) && count($suspended) > 1){ 
	foreach($suspended as $k => $v){
		if($v = "1"){
			$data['SUSPENDEDORDERS'] = $data['SUSPENDEDORDERS'].'#'.$k.' ';
		} else {
			$data['ORDERSTOSUSPENDED'] = $data['ORDERSTOSUSPENDED'].'#'.$k.' ';
		}
	}
} else {
	$data['SUSPENDEDORDERS'] = "No";
	$data['ORDERSTOSUSPENDED'] = "No";
}

if(is_array($terminated) && count($terminated) > 1){
	foreach($terminated as $k => $v){
		if($v = "1"){
			$data['TERMINATEDORDERS'] = $data['TERMINATEDORDERS'].'#'.$k.' ';
		} else {
			$data['ORDERSTOTERMINATE'] = $data['ORDERSTOTERMINATE'].'#'.$k.' ';
		}
	}
} else {
	$data['TERMINATEDORDERS'] = "No";
	$data['ORDERSTOTERMINATE'] = "No";
}



$users = $user->GetButch('',"`status` = 'admin'");
$nt->Send($users,$data,'dailyreport');
exit;
?>
