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

if (!defined('iSELF')) { header('Location: index.php'); exit; }

class Payment {

public $id;
public $AccountID;
public $ResellerID;
public $OrderID;
public $opentime;
public $method;
public $service;
public $amount;
public $bonustime;

function __construct( $id = 0 ) {
	global $DB, $Payments;
	if(!empty($id)) {
		$this->id = $id;
		$result = $DB->make_select('Payments', '*', '`PaymentID`='.$this->id);
		$row = $DB->row($result);
		if(!$row) {
			log_error("Payment ID $this->id not found", __FILE__, __LINE__);
			pdata("Payment ID $this->id not found\n");
		}
		$this->load($row);
		$Payments[$id] = $this;
	} else $this->id =0;
}


public function load( $data ) {
	$this->id = empty($data['PaymentID']) ? 0 : (int) $data['PaymentID'];
	$this->AccountID = (int) $data['AccountID'];
	$this->ResellerID = (string) $data['ResellerID'];
	$this->OrderID = (int) $data['OrderID'];
	$this->opentime = empty($data['opentime']) ? iNOW_TEXT : $data['opentime'];
	$this->method = (string) $data['method'];
	$this->service = (string) $data['item'];
	$this->amount = (float) $data['amount'];
	$this->bonustime = isset($data['bonustime']) ? (int) $data['bonustime'] : 0;
}

public function show() {
	global $LANG, $PAYTARGET, $PAYMETHOD;
	$show = beginTable("$LANG[Details] $LANG[Payment]: $this->id");
	$show .= StaticField($LANG['ID'], 	$this->id);
	$show .= StaticField($LANG['AccountID'], 	"<a href=\"?object=account&amp;action=show&amp;AccountID=$this->AccountID\">$this->AccountID</a>");
	$show .= StaticField($LANG['ResellerID'], 	$this->ResellerID);
	$show .= StaticField($LANG['OrderID'], 	"<a href='?object=order&amp;action=show&amp;OrderID=$this->OrderID'>$this->OrderID</a>");
	$show .= StaticField($LANG['CreateDate'], 	$this->opentime);
	$show .= StaticField($LANG['Paymethod'], 	$PAYMETHOD[$this->method]);
	$show .= StaticField($LANG['Service'], 	$PAYTARGET[$this->service]['name']);
	$show .= StaticField($LANG['Amount'], 	round($this->amount,2).' руб.');
	$show .= StaticField($LANG['Bonus'], 	round($this->bonustime/iDAY,1).' days');
	$show .= endTable();
	return $show;
}

public function edit() {
	global $LANG, $PAYTARGET, $PAYMETHOD;
	$show = openForm(iSELF);
	$show .= HiddenField('object', 	'payment');
	$show .= HiddenField('action', 	'edit2');
	$show .= HiddenField('PaymentID', 	$this->id);
	$show .= beginTable("$LANG[Details] $LANG[Payment]: $this->id");

	$show .= StaticField($LANG['AccountID'], 	$this->AccountID);
	$show .= StaticField($LANG['ResellerID'], 	$this->ResellerID);
	$show .= TextField($LANG['OrderID'], 'OrderID', $this->OrderID);
	$show .= TextField($LANG['CreateDate'], 'opentime', $this->opentime);

	$pack_array = array();
	foreach($PAYTARGET as $index => $val) { $pack_array[] = $index; $pack_array[] = $val['name'];	}
	$show .= ArrayDropBox($LANG['Service'], 'item', $this->service, $pack_array);

	$pack_array = array();
	foreach($PAYMETHOD as $index => $val) {
		array_push($pack_array, $index);
		array_push($pack_array, $val);
	}
	$show .= ArrayDropBox($LANG['Paymethod'], 'method', $this->method, $pack_array);

	$show .= TextField($LANG['Amount'], 'amount',	$this->amount);
	$show .= TextField($LANG['Bonus'], 'bonustime',	number_format($this->bonustime/iDAY,2));
	$show .= Submitter("edit_payment", $LANG['Edit']);
	$show .= endTable();
	return $show;
}

public function add_form($AccountID, $OrderID = null) {
	global $LANG, $lInfo, $PAYMETHOD, $PAYTARGET, $DomainPrice;
	$show = openForm(iSELF);
	$show .= HiddenField('object', 	'payment');
	$show .= HiddenField('action', 'add2');
	$show .= HiddenField('AccountID', $AccountID);
	$show .= HiddenField('OrderID', $OrderID);
	$show .= beginTable($LANG['Payment']);
	if(!empty($OrderID)){
		$order = new Order($OrderID);
		$show .= StaticField($LANG['OrderID'], $order->id);
		if($order->service=='hosting'){

		// Calculate hosting price
		//$serv = new Service_hosting(array('opentime'=>date('Y-m-d H:i:s', iNOW_UNIX - $order->count*iMON), 'closetime'=>iNOW_TEXT, 'mod'=>$order->package->id));
		$hosting_price = $order->count*$order->package->price;

		$show .= '<tr><td class="nob">'.HiddenField('item', 'hosting').'</td></tr>';

	  	$pack_array = array();
	  	foreach($PAYMETHOD as $index => $val) {	$pack_array[] = $index; $pack_array[] = $val; }
	  	$show .= ArrayDropBox($LANG['Paymethod'], 'method', $order->paymethod, $pack_array);
  		$show .= TextField($LANG['Amount'].', руб.', 'amount', round($hosting_price,2));

		if($order->domaininfo['action'] == 'reg') {
			$domain_price = $DomainPrice[zone($order->domain)][0];
			if($DomainPrice[zone($order->domain)][3]) foreach($order->package->bonusdomain as $time => $bonus) if($order->count >= $time) $domain_price = 0.0;
			$show .= CheckBox($lInfo['reg'].' '.$order->domain, 'domain', true);
			$show .= TextField($LANG['Domain'].' '.$LANG['Amount'].', руб.', 'domain_amount', round($domain_price,2));
		}
		}elseif($order->service=='domain' || $order->service=='domain_cont'){
			$amount = $order->amount;
			$show .= StaticField('Назаначение', "<input type='radio' name='item' value='$order->service' checked='checked' />".$PAYTARGET[$order->service]['name']. '<br />(регистрация/продление домена и добавление услуги)<br />'."<input type='radio' name='item' value='hosting' />".$PAYTARGET['hosting']['name']. '<br />(продление хостинга)');
	  		$pack_array = array();
			foreach($PAYMETHOD as $index => $val) {	$pack_array[] = $index; $pack_array[] = $val; }
	  		$show .= ArrayDropBox($LANG['Paymethod'], 'method', $order->paymethod, $pack_array);
  			$show .= TextField($LANG['Amount'], 'amount', round($amount,2));
		}else{
			$amount = $order->amount;
			$pack_array = array();
			foreach($PAYTARGET as $index => $val) { $pack_array[] = $index; $pack_array[] = $val['name']; }
			$show .= ArrayDropBox($LANG['Service'], 'item', $order->service, $pack_array);
	  		$pack_array = array();
			foreach($PAYMETHOD as $index => $val) {	$pack_array[] = $index; $pack_array[] = $val; }
	  		$show .= ArrayDropBox($LANG['Paymethod'], 'method', $order->paymethod, $pack_array);
  			$show .= TextField($LANG['Amount'], 'amount', round($amount,2));
		}
	}else{
		$amount = 0.00; $Service='hosting_cont'; $PayMethod='';
		$show .= TextField($LANG['OrderID'], 'OrderID', '');
		$pack_array = array();
		foreach($PAYTARGET as $index => $val) { $pack_array[] = $index; $pack_array[] = $val['name']; }
		$show .= ArrayDropBox($LANG['Service'], 'item', $Service, $pack_array);
	  	$pack_array = array();
	  	foreach($PAYMETHOD as $index => $val) {	$pack_array[] = $index; $pack_array[] = $val; }
	  	$show .= ArrayDropBox($LANG['Paymethod'], 'method', $PayMethod, $pack_array);
  		$show .= TextField($LANG['Amount'], 'amount', round($amount,2));
	}
	if(!empty($OrderID) && ($order->service=='hosting' || $order->service=='hosting_cont')) {
		$show .= CheckBox('Отправить уведомление','Note',true);
	}else $show .= CheckBox('Отправить уведомление','Note',false);
	$show .= Submitter('new_pay', $LANG['Add']);
	$show .= endTable();
	$show .= closeForm();
	return $show;
}

public function save(){
	global $DB;
	return $DB->make_update('Payments', '`PaymentID`='.$this->id ,
				array( "AccountID" => $this->AccountID,
				       "ResellerID" => $this->ResellerID,
				       "OrderID" => $this->OrderID,
				       "opentime" => $this->opentime,
				       "method" => $this->method,
						'item' => $this->service,
				       "amount" => $this->amount,
				       "bonustime" => $this->bonustime
					) );
}

public function ShowActions() {
	global $LANG;
	$show = beginTable($LANG['Actions']);
	$show .= "<tr><td><a href='?object=payment_print&amp;PaymentID=$this->id'><img src='images/edit.png' alt='print' />печать акта</a></td></tr>";
	$show .= "<tr><td><a href='?object=payment&amp;action=edit&amp;PaymentID=$this->id'><img src='images/edit.png' alt='edit' />$LANG[Edit] </a></td></tr>";
	$show .= "<tr><td><a href='?object=payment&amp;action=delete&amp;PaymentID=$this->id'><img src='images/delete.png' alt='delete' />$LANG[Delete] </a></td></tr>";
	$show .= endTable();
	return $show;
}

static public function load_payments($columns = null, $filter = null, $sortby = null, $sortdir = null, $limit = null, $start = null ) {
	global $DB, $FOUND_ROWS;
	$result = $DB->make_select('Payments', $columns, $filter, $sortby, $sortdir, $limit, $start );
	$res_count = $DB->query_adv('SELECT FOUND_ROWS()');
	$FOUND_ROWS = $DB->row($res_count);
	$FOUND_ROWS = $FOUND_ROWS['FOUND_ROWS()'];
	$payment_array = array();
	while( $data = $DB->row($result) ) {
		$payment = new Payment();
		$payment->load($data);
	$payment_array[] = $payment;
	}
	return $payment_array;
}

static public function add_payment($obj) {
	global $DB;
	if( !$DB->make_insert( 'Payments',
				array( "AccountID" => $obj->AccountID,
				       "ResellerID" => $obj->ResellerID,
				       "OrderID" => $obj->OrderID,
				       "opentime" => $obj->opentime,
				       "method" => $obj->method,
					'item' => $obj->service,
				       "amount" => $obj->amount,
				       "bonustime" => $obj->bonustime
					) )) return false;

	$id = $DB->insert_id();
	if($id == false) pdata( 'Could not retrieve ID from previous INSERT!' );
	elseif($id == 0) pdata( 'Previous INSERT did not generate an ID' );
	$obj->id = $id;
	return true;
}
}
