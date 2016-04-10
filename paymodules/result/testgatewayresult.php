<?php
/*
 *      testresult.php
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
require_once('../../core.php');

//change to your gateway's name
define('GWNAME', 'othertestgateway');
define('INVOICE', (int)$_REQUEST['inv']);
define('AMOUNT', (int)$_REQUEST['received']);
define('KEY', $_REQUEST['secretkey']);
define('COMMENT', '');

$gm = GatewayModule::getInstance();
$curr = Currency::getInstance();
$service = Service::getInstance();
$invoice = Invoice::getInstance();
$tr = Transaction::getInstance();

//finding ID for this gateway
$gwid = $gm->GetID(GWNAME);
if(!$gwid){
	die("This gateway is not activated and configured to accept payments");
}

$gmdata = $gm->FetchData($gwid);
$invdata = $invoice->FetchData(INVOICE);
$currency = $curr->FetchData($curr->GetID($gmdata['currency'],'name'));
//$currency = $curr->GetCurrency('',$gmdata['currency']);
$gateway_data = unserialize($gmdata['data']);

if($invdata['amount'] > AMOUNT*$currency['rate']){
	echo "Received amount is not enough for this invoice";
	//send some notifications
	exit;
} elseif($gateway_data['key'] != KEY){
	echo "Secret keys doesnt match";
} else {
	$tr->Create(INVOICE, $gwid, AMOUNT);
	$service->newPayment(INVOICE);
	echo 'OK';
}

?>
