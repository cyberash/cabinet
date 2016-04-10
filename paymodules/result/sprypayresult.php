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
define('GWNAME', 'sprypay');
define('INVOICE', (int)@$_REQUEST['spShopPaymentId']);
define('AMOUNT', (int)@$_REQUEST['spAmount']);
define('GATEWAY_PAYMENTID', @$_REQUEST['spPaymentId']);
define('HASH', @$_REQUEST['spHashString']);
define('DATE', @$_REQUEST['spEnrollDateTime']);
define('GATEWAY_SHOPID', @$_REQUEST['spShopId']);
define('CURRENCY', @$_REQUEST['spCurrency)']);
define('GATEWAY_USER_EMAIL', @$_REQUEST['spCustomerEmail']);
define('PAYMENT_SYSTEM_ID', @$_REQUEST['spPaymentSystemId']); 
define('PAYMENT_SYSTEM_AMOUNT', @$_REQUEST['spPaymentSystemAmount']);
define('PAYMENT_SYSTEM_PAYMENT_ID', @$_REQUEST['spPaymentSystemPaymentId']);
define('GATEWAY_BALANCE', @$_REQUEST['spBalanceAmount']);
define('PURPOSE', @$_REQUEST['spPurpose']);
//wmzgatewayresult.php?inv=&received=1.00&secretkey=
$gm = GatewayModule::getInstance();
$curr = Currency::getInstance();
$service = Service::getInstance();
$invoice = Invoice::getInstance();
$tr = Transaction::getInstance();
$gmdata = $gm->FetchData($gm->GetID(GWNAME));
$invdata = $invoice->FetchData(INVOICE);
$currency = $curr->FetchData($curr->GetID($gmdata['currency'],'name'));
$gateway_data = unserialize($gmdata['data']);
$hash = md5(GATEWAY_PAYMENTID.GATEWAY_SHOPID.INVOICE.GATEWAY_BALANCE.AMOUNT.CURRENCY.GATEWAY_USER_EMAIL.PURPOSE.PAYMENT_SYSTEM_ID.PAYMENT_SYSTEM_AMOUNT.PAYMENT_SYSTEM_PAYMENT_ID.DATE.$gateway_data['key']);
if($hash != HASH){
	die("Hash verification failed");
}
if($invdata['amount'] > AMOUNT*$currency['rate']){
	echo "Received amount is not enough to settle this invoice";
	//send some notifications
	exit;
} else {
	$tr->Create(INVOICE, $gm->GetID(GWNAME), AMOUNT);
	$service->newPayment(INVOICE);
	echo 'OK';
}
?>