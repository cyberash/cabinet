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
define('GWNAME', 'wmegateway');
define('INVOICE', (int)@$_REQUEST['LMI_PAYMENT_NO']);
define('AMOUNT', (int)@$_REQUEST['LMI_PAYMENT_AMOUNT']);
define('KEY', @$_REQUEST['LMI_SECRET_KEY']);
define('HASH', @$_REQUEST['LMI_HASH']);
define('DATE', @$_REQUEST['LMI_SYS_TRANS_DATE']);
define('MODE', @$_REQUEST['LMI_MODE']);
define('WMINVNUM', @$_REQUEST['LMI_SYS_INVS_NO)']);
define('WMTRANSNUM', @$_REQUEST['LMI_SYS_TRANS_NO']);
define('PURSE', @$_REQUEST['LMI_PAYEE_PURSE']); //merchan's purse
define('PAYER_PURSE', @$_REQUEST['LMI_PAYER_PURSE']);
define('PAYER_WMID', @$_REQUEST['LMI_PAYER_WM']);
define('COMMENT', '');
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

//var_dump($gateway_data);
$hash = md5(PURSE.AMOUNT.INVOICE.MODE.WMINVNUM.WMTRANSNUM.DATE.$gateway_data['key'].PAYER_PURSE.PAYER_WMID);

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