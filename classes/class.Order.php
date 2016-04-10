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
/**
 * На один заказ приходится только один продукт или дополнение. Более 1 типа продуктов в заказе быть не может.
 * В заказе должны быть указаны тип оплаты заказа (помесячно, ежегодно, разово и т.п), дата заказа, дата последнего инвоиса(?)
 * Также в заказе должна быть ссылка на конкретную услугу
 **/
class Order extends Base {
	private $raw = NULL;
	public $pkgid = NULL;
	public $orderid, $serverid;
	private $data = NULL;
	public static $derivatives = array('Invoice' => 'orderid');
	public static function properties(){
		return array(
			'required' => array(),
			'values' => array()
		);
	}

function __construct(){
	$this->db = DB::getInstance();
	parent::__construct();
}
/*
 * $lastinv may be set in:
 * "new" - new invoice will be generated
 * "no" - no invoice will be created for first time
 * numeric invoice id - existing invoice will be associated with the order
 */
function Create($accountid, $pkgid, $lastinv='new', $firstamount='', $recuramount='', $orderdate = '', $status = 'Pending', $cycle=1){
	$time = Time::getInstance();
	$pkg = Package::getInstance();
	$inv = Invoice::getInstance();
	if($orderdate == ''){
		$orderdate = $time->UtoM(time());
	} elseif(!($time->validateTime($orderdate))){
		throw new Exception("Order date in wrong format");
	}
	if(is_numeric($cycle) && $cycle > 0){
		$nextdue = $time->add_date($orderdate, 0, $cycle);
	} elseif($cycle = '-1'){
		$nextdue = '0000-00-00 00:00:00';
	} else {
		throw new Exception("Payments cycle in wrong format");
	}
	if($firstamount == '' && $cycle == 1){
		$this->raw = $pkg->FetchData($pkgid);
		$firstamount = floatval($this->raw['price']);
	} elseif($firstamount == '' && $cycle > 1){
		$packages = $pkg->FetchData($pkgid);
		$firstamount = floatval($packages['price']*$cycle);
	} elseif(!is_numeric($firstamount)){
		throw new Exception("First amount in wrong format");
	}
	if($recuramount == '' && $cycle == 1){
		$recuramount = $pkg->FetchData($pkgid);
		$recuramount = floatval($recuramount['price']);
	} elseif($recuramount == '' && $cycle > 1){
		$packages = $pkg->FetchData($pkgid);
		$recuramount = floatval($packages['price']*$cycle);
	} elseif(!is_numeric($recuramount)){
		throw new Exception("Recurring amount in wrong format");
	}
	switch($lastinv){
		case 'new':
			$packages = $pkg->FetchData($pkgid);
			$comment = 'Order for package '.$packages['name'].' ('.$orderdate.' - '.$nextdue.')';
			$lastinv = $inv->Create($accountid, '-1', $firstamount, $nextdue,'Unpaid',$comment);
		break;
		case 'no':
			$lastinv = -1;
		break;
		default:
			if(!is_numeric($lastinv)){
				throw new Exception("Last invoice id in wrong format");
			}
	}
	if(!is_numeric($accountid) || !is_numeric($pkgid) || !is_numeric($lastinv) || !is_numeric($cycle) || !is_float($firstamount) || !is_float($recuramount) || !is_string($nextdue) || !is_string($status)){
		//var_dump($lastinv);
		throw new Exception("Problems with data");
	}
	$this->data = array('accountid' => $accountid, 'productid' => $pkgid, 'status' => $status, 'cycle' => $cycle, 'orderdate' => $orderdate, 'lastinv' => $lastinv, 'nextdue' => $nextdue, 'firstamount' => $firstamount, 'recuramount' => $recuramount);
	$this->raw = $this->db->query_insert('Order', $this->data);
	if(!$this->raw){
		throw new Exception("Problem inserting data into the database");
	} else {
		if(is_numeric($lastinv) && $lastinv > 0){
			//$inv->Update($lastinv, 'orderid', $this->raw);
			$inv->Update('orderid', $this->raw, $lastinv);
		}
		return $this->raw;
	}
}
public function FindModuleID($by = 'orderid'){
	switch($by){
		case 'orderid':
			if(!is_numeric($this->orderid)){
				throw new Exception("Order ID is not set or set not correctly");
			} else {
				$order = $this->FetchData($this->orderid);
				//var_dump($order);
				if(isset($order['productid'])){
					$pkg = Package::getInstance()->FetchData($order['productid']);
					if(!isset($pkg['presetid'])) return false;
					$preset = Preset::getInstance()->FetchData($pkg['presetid']);
					if(is_array($pkg) && is_array($preset)){
						$sg = ServerGroups::getInstance();
						$sgdata = $sg->FetchData($preset['groupid']);
						return $sgdata['moduleid'];
					} else {
						return false;
					}
				} else {
					return false;
				}
			}
		break;
		case 'pkgid':
			if(!is_numeric($this->pkgid)){
				throw new Exception("Package ID is not set or set incorrectly");
			} else {
				$presetid = Package::getInstance()->FetchData($this->pkgid)->presetid;
				$sg = ServerGroups::getInstance();
				$sg->id = Preset::getInstance()->FetchData($presetid)->groupid;
				return $sg->FetchData()->moduleid;
			}
		break;
		default:
			return false;
	}
}
public function SuspendOverdueOrders(){
	$setting = Settings::getInstance();
	$service = Service::getInstance();
	$time = Time::getInstance();
	$retarray = array();
	$days = $time->UtoM(time());
	$this->raw = $this->db->fetch_all_array('SELECT * FROM `Order` WHERE `status` = "Active" AND `nextdue` < "'.$days.'" AND `cycle` > 0');
	for($i=0;$i<count($this->raw);$i++){
		if($time->diff_dates($days,$this->raw[$i]['nextdue'],'D') > 4){
			if($service->Suspend($this->raw[$i]['id'])){
				$retarray[$this->raw[$i]['id']] = "1";
			} else {
				$retarray[$this->raw[$i]['id']] = "0";
			}
		}
	}
	return $retarray;
}
public function TerminateOverdueOrders(){
	$setting = Settings::getInstance();
	$service = Service::getInstance();
	$time = Time::getInstance();
	$retarray = array();
	$days = $time->UtoM(time());
	$this->raw = $this->db->fetch_all_array('SELECT * FROM `Order` WHERE `status` = "Suspended" AND `nextdue` < "'.$days.'" AND `cycle` > 0');
	for($i=0;$i<count($this->raw);$i++){
		if($time->diff_dates($days,$this->raw[$i]['nextdue'],'D') > 14){
			if($service->Terminate($this->raw[$i]['id'])){
				$retarray[$this->raw[$i]['id']] = "1";
			} else {
				$retarray[$this->raw[$i]['id']] = "0";
			}
		}
	}
	return $retarray;
}
public function generateLastInv($orderid=''){
	$time = Time::getInstance();
	$inv = Invoice::getInstance();
	$pkg = Package::getInstance();
	$setting = Settings::getInstance();
	if(!is_numeric($orderid) && !is_numeric($this->orderid)){
		$result = array();
		$days = $time->add_date($time->UtoM(time()),$setting->Get('system.cron.daystonewinv'));
		$this->raw = $this->db->fetch_all_array('SELECT * FROM `Order` WHERE `status` = "Active" AND `nextdue` < "'.$days.'" AND `cycle` > 0');
		for($i=0;$i<count($this->raw);$i++){
			
			$invdata = $inv->FetchData($this->raw[$i]['lastinv']);
			if($time->MtoU($invdata['datedue']) < $time->MtoU($this->raw[$i]['nextdue'])){
				//new invoice should be generated
				$pkgdata = $pkg->FetchData($this->raw[$i]['productid']);
				$nextdue = $time->add_date($this->raw[$i]['nextdue'],'',$this->raw[$i]['cycle']);
				$comment = "Order for package ".$pkgdata['name']." (".$this->raw[$i]['nextdue']." - ".$nextdue.")";
				$lastinv = $inv->Create($this->raw[$i]['accountid'], $this->raw[$i]['id'], $this->raw[$i]['recuramount'], $this->raw[$i]['nextdue'],'Unpaid',$comment);
				$result[] = array('invoice' => $lastinv, 'order' => $this->raw[$i]['id']);
				$this->Update('lastinv', $lastinv,$this->raw[$i]['id']);
			}
		}
		return $result;
	} else {
		if(!is_numeric($orderid)){
			$orderid = $this->orderid;
		}
		$orderdata = $this->FetchData($orderid);
		$lastinvdata = $inv->FetchData($orderdata['lastinv']); 
		if($orderdata['status'] == 'Active' && $orderdata['cycle'] > 0 && $time->MtoU($orderdata['nextdue']) > $time->MtoU($lastinvdata['datedue'])){
			$pkgdata = $pkg->FetchData($orderdata['productid']);
			$nextdue = $time->add_date($orderdata['nextdue'],'',$orderdata['cycle']);
			$comment = "Order for package ".$pkgdata['name']." (".$orderdata['nextdue']." - ".$nextdue.")";
			$lastinv = $inv->Create($orderdata['accountid'], $orderdata['id'], $orderdata['recuramount'], $orderdata['nextdue'],'',$comment);
			$this->Update($orderdata['id'], 'lastinv', $lastinv);
		}
	}
}
public function generateCreateArray($array){
	$sm = ServerModule::getInstance();
	if(!is_numeric($this->orderid) && is_numeric($this->pkgid)){
		$sm->id = $this->FindModuleID('pkgid');
	} elseif(is_numeric($this->orderid) && !is_numeric($this->pkgid)){	
		$sm->id = $this->FindModuleID();
	} else {
		return false;
	}
	$ret = array();
	foreach($sm->getArray('Create') as $k => $v){
		if(isset($array[$v['name']])){
			$ret[$v['name']] = $array[$v['name']];
		} else {
			return false;
		}
	}
	return $ret;
}
}
?>
