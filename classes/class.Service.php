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

//class Service implements ServiceTemplate {
class Service {
	public static $se_instance = NULL;
	public $orderid;
	public $invid;
	private $db;
	public static function getInstance(){
		if(self::$se_instance == NULL){
			self::$se_instance = new self();
		}
		return self::$se_instance;
	}
	public function __construct(){
		$this->db = DB::getInstance();
	}
	public function newPayment($invid){
		if(!is_numeric($this->invid) && !is_numeric($invid)){
			throw new Exception("Invoice ID is not set or set incorrectly");
		} elseif(is_numeric($this->invid) && !is_numeric($invid)){
			$invid = $this->invid;
		}
		$error = 0;
		$sm = ServerModule::getInstance();
		$user = User::getInstance();
		$order = Order::getInstance();
		$invoice = Invoice::getInstance();
		$time = Time::getInstance();
		$nt = Notification::getInstance();
		
		$invdata = $invoice->FetchData($invid);
		$users = $user->GetButch(1,'`id` = "'.$invdata['accountid'].'"');
		$orderdata = $order->FetchData($invdata['orderid']);
		
		$ntarray = array(
			'USER' => $users[0],
			'INV' => $invdata,
			'ORDER' => $orderdata
		);
		switch($orderdata['status']){
			case 'Active':
				$nt->Send($users,$ntarray,'usernewpayment');
				$order->Update('nextdue', $time->add_date($orderdata['nextdue'],0,$orderdata['cycle']), $orderdata['id']);
			break;
			case 'Pending':
			//Making all this shit for creating new account; sendmail email with access data
				try {
					$this->Create($orderdata['id']);
				} catch (Exception $e){
					$admins = $user->GetButch('','`status` = "Admin"');
					$nterarray = array(
						'USER' => $users[0],
						'INV' => $invdata,
						'MESSAGE' => $e->getMessage(),
						'DEBUG' => $e
					);
					$nt->Send($admins,$nterarray,'adminservicesetuperror');
					$error = 1;
				}
				if($error > 0){
					//we need to refetch order data as we updated accessdata when created new service
					$orderdata = $order->FetchData($invdata['orderid']);
					$accessdata = unserialize($orderdata['accessdata']);
					
					$order->orderid = $invdata['orderid'];
					$sm->id = $order->FindModuleID();
					$modulecreatearray = $sm->getArray('Create');
					$acdata_to_mail = '';
					for($i=0;$i<count($modulecreatearray);$i++){
						if(array_key_exists($modulecreatearray[$i]['name'], $accessdata)){
							$acdata_to_mail .= $modulecreatearray[$i]['label'].": ".$accessdata[$modulecreatearray[$i]['name']]."\n";
						}
					}
					$ntarray['ACCESSDATA'] = $acdata_to_mail;
					//there email should me sent
					$nt->Send($admins,$ntarray,'usernewservicedetails');
					
					$order->Update('status', 'Active', $invdata['orderid']);
				} else {
					die("Error");
				}
				
			break;
			case 'Suspended':
				$this->Unsuspend($order->orderid);
				$order->Update('','status', 'Active');
			break;
			case 'Terminated':
			//there are should be some notification for admin and user about payment for terminated service or money should be returned back to the balance
				//$this->Create($order->orderid);
				//$order->Update('','status', 'Active');
			break;
			default:
				throw new Exception("Unknown order status!");
		}
		
		
		
	}
	public function Create($orderid){
		$order = Order::getInstance();
		$sm = ServerModule::getInstance();
		$preset = Preset::getInstance();
		$order->orderid = $orderid;
		$orderdata = $order->FetchData($orderid);
		$pkg = Package::getInstance()->FetchData($orderdata['productid']);
		$preset->presetid = $pkg['presetid'];
		$sm->id = $order->FindModuleID();
		$presetdata = $preset->FetchData($preset->presetid);
		if(!is_array($orderdata['accessdata'])){
			$orderdata['accessdata'] = serialize($this->FillCreateArray($sm->getArray('Create'),$orderdata['accountid']));
			$order->Update('accessdata',$orderdata['accessdata'],$orderid);
		}
		if(!is_numeric($orderdata['serverid']) || $orderdata['serverid'] == '-1'){
			$serverdata = $this->selectServerFromGroup($presetdata['groupid']);
			$order->Update('serverid', $serverdata['ServerID'],$orderid);
		} else {
			$server = Server::getInstance();
			$serverdata = $server->FetchData($orderdata['serverid']);
		}
		if(strlen($serverdata['accessdata']) < 5){
			throw new Exception("Server #".$orderdata['serverid']." is not configured");
		}
		return $sm->CreateService(unserialize($serverdata['accessdata']), unserialize($orderdata['accessdata']), $preset->getOptions());
	}
	public function Terminate($orderid){
		$order = Order::getInstance();
		$order->orderid = $orderid;
		$sm = ServerModule::getInstance();
		$orderdata = $order->FetchData($orderid);
		$server = Server::getInstance();
		$sm->id = $order->FindModuleID();
		$serverdata = $server->FetchData($orderdata['serverid']);
		return $sm->DeleteService(unserialize($serverdata['accessdata']), unserialize($orderdata['accessdata']));
	}
	public function Suspend($orderid){
		$order = Order::getInstance();
		$sm = ServerModule::getInstance();
		$order->orderid = $orderid;
		$order->Update('','status','Suspended');
		$orderdata = $order->FetchData();
		$server = Server::getInstance();
		$sm->id = $order->FindModuleID();
		$serverdata = $server->FetchData($orderdata['serverid']);
		return $sm->SuspendService(unserialize($serverdata['accessdata']), unserialize($orderdata['accessdata']));
	}
	public function Unsuspend($orderid){
		$order = Order::getInstance();
		$sm = ServerModule::getInstance();
		$order->orderid = $orderid;
		$order->Update('','status','Terminated');
		$orderdata = $order->FetchData();
		$server = Server::getInstance();
		$sm->id = $order->FindModuleID();
		$serverdata = $server->FetchData($orderdata['serverid']);
		return $sm->UnsuspendService(unserialize($serverdata['accessdata']), unserialize($orderdata['accessdata']));
	}
	public function selectServerFromGroup($groupid){
		//selecting random server by default for now...
		$server = Server::getInstance();
		$servers = $server->GetActiveByGroup($groupid);
		if(count($servers) < 1){
			throw new Exception("No active servers in the group");
		}
		return $servers[rand(0,count($servers)-1)];
	}
	public function FillCreateArray($crarray, $userid){
		if(!is_array($crarray) || !is_numeric($userid)){
			throw new Exception("Problems with create array or with user id");
		}
		$retarray = array();
		$user = User::getInstance();
		$user->id = $userid;
		for($i=0;$i<count($crarray);$i++){
			switch($crarray[$i]['type']){
				case 'username':
					$retarray['clientlogin'] = $user->GetUsername();
				break;
				case 'password':
					$retarray['clientpassword'] = Password::getInstance()->ProcessPassword();
				break;
			}
		}
		return $retarray;
	}
}
