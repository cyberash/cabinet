<?php
/*
 *      class.Page.php
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
class Page {
	//Test::$a = 'ZERO';
	public static $messages = array();
	public static $userid = NULL;
	public static $xtpl = NULL;
	public static $select_where = NULL;
	public static $page = NULL;
	public static $per_page = 15;
	public static $query_string = NULL;
	public static $request_uri = NULL;
	public static function init($usertype){
		self::$xtpl = NULL;
		if(!preg_match('/^(user|admin|login|order|reg)$/i', $usertype)){
			throw new Exception("Unknown user type");
		}
		
			$order = Order::getInstance();
			self::$xtpl = XTemplate::getInstance();
			self::$xtpl->restart("themes/simpla/".$usertype."_skel.tpl");
			self::$xtpl->assign('LANG', Lang::$langarray);
			if(self::$userid == NULL && !preg_match('/^(login|order|reg)$/i', $usertype)){
				self::$xtpl->assign('ATTENTIONMSG', 'User ID is not set');
				self::$xtpl->parse('main.attention');
			} elseif(!preg_match('/^(login|order|reg)$/i', $usertype)){
				$order = Order::getInstance();
				$inv = Invoice::getInstance();
				$orderscnt = $order->Calculate('`accountid` = "'.self::$userid.'"');
				if($orderscnt == '' || !$orderscnt){
					$orderscnt = 0;
				}
				self::$xtpl->assign('ORDERSCNT', $orderscnt);
				self::$xtpl->assign('INVCNT', $inv->Calculate('`accountid` = "'.self::$userid.'"'));
				self::$xtpl->assign('UINVCNT', $inv->Calculate('`accountid` = "'.self::$userid.'" AND `status` = "Unpaid"'));
				self::$xtpl->parse('main.stats');
				
			}
		//}
		if(count(self::$messages) > 0){
				for($i=0;$i<count(self::$messages);$i++){
					self::$xtpl->assign(strtoupper(self::$messages[$i]['type']).'MSG', self::$messages[$i]['message']);
					self::$xtpl->parse('main.'.self::$messages[$i]['type']);
				}
		}
	}
	public static function setup($title, $desc=''){
		if(!is_object(self::$xtpl)){
			throw new Exception("Page was not initiated!");
		}
		if(strlen($title) < 1){
			$title = Lang::$langarray['notitle'];
		}
		self::$xtpl->assign('TITLE', $title);
		self::$xtpl->assign('DESCR', $desc);
	}
	public static function count_pages($total_items){
		if(!is_numeric(self::$per_page) || !is_numeric($total_items)){
			throw new Exception("items per page or total items number is not set");
		}
		return ceil($total_items/self::$per_page);
	}
	public static function message(){
		if(count(self::$messages) > 0){
				for($i=0;$i<count(self::$messages);$i++){
					self::$xtpl->assign(strtoupper(self::$messages[$i]['type']).'MSG', self::$messages[$i]['message']);
					self::$xtpl->parse('main.'.self::$messages[$i]['type']);
				}
		}
	}
	public static function add_message(&$xtpl, $type, $message){
		$xtpl->assign(strtoupper($type).'MSG', $message);
		$xtpl->parse('main.'.$type);
	}
	public static function Login(){
		$xtpl = self::$xtpl;
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function Dashboard(){
		$xtpl = self::$xtpl;
		$xtpl->assign('DASHCURR', 'current');
		$inv = Invoice::getInstance();
		$dashuser = User::getInstance();
		$order = Order::getInstance();
		$curr = Currency::getInstance();
		$orderbutch = $order->GetButch(5);
		$users = $dashuser->GetButch(5,'`status` = "Active"');
		$invs = $inv->GetButch(5, '`status` = "Unpaid"');
		if(count($orderbutch) < 1){
			$xtpl->parse('main.dashboard.ordersinfo');
		} else {
			$pkg = Package::getInstance();
			for($i = 0; $i < count($orderbutch); $i++){
				$xtpl->assign('ORDER', $orderbutch[$i]);
				try {
					$xtpl->assign('PKGNAME', $pkg->GetName($orderbutch[$i]['productid']));
					$xtpl->assign('USERNAME', $dashuser->GetUsername($orderbutch[$i]['accountid']));
				} catch(Exception $e){
					$xtpl->assign('ATTENTIONMSG', 'Fatal error catched during fetching additional order information. This means some order is not assigned to any user or package. Error dump: '.nl2br($e));
					$xtpl->parse('main.attention');
				}
				$xtpl->parse('main.dashboard.orderstable.orderrow');
			}
		$xtpl->parse('main.dashboard.orderstable');
		}
		if(count($users) < 1){
			$xtpl->parse('main.dashboard.usersinfo');
		} else {
			for($i=0;$i<count($users);$i++){
				$xtpl->assign('USER', $users[$i]);
				$xtpl->parse('main.dashboard.userstable.userrow');
			}
			$xtpl->parse('main.dashboard.userstable');
		}
		if(count($invs) < 1){
			$xtpl->parse('main.dashboard.invsinfo');
		} else {
			for($i=0;$i<count($invs);$i++){
				$xtpl->assign('USERNAME', $dashuser->GetUsername($invs[$i]['accountid']));
				$xtpl->assign('INV', $invs[$i]);
				$xtpl->assign('AMOUNT', $curr->FormatCurrency($invs[$i]['amount']));
				$xtpl->parse('main.dashboard.invstable.invrow');
			}
			$xtpl->parse('main.dashboard.invstable');
		}
		$xtpl->parse('main.dashboard');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddCustomer(){
		$xtpl = self::$xtpl;
		$xtpl->assign('CUSTCREATECURR', 'current');
		$xtpl->assign('CUSTCURR', 'current');
		$xtpl->parse('main.addcustomer');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageCustomers(){
		$xtpl = self::$xtpl;
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('CUSTMANAGECURR', 'current');
		$xtpl->assign('CUSTCURR', 'current');
		$user = User::getInstance();
		$userbutch = $user->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		if(count($userbutch) < 1){
			$xtpl->parse('main.managecustomers.usersinfo');
		} else {
			for($i = 0; $i < count($userbutch); $i++){
				$xtpl->assign('USER', $userbutch[$i]);
				$xtpl->parse('main.managecustomers.userstable.userrow');
			}
			for($i=1;$i<=self::count_pages($user->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.managecustomers.userstable.page');
			}
			$xtpl->parse('main.managecustomers.userstable');
		}
		$xtpl->parse('main.managecustomers');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageCurrencies(){
		$xtpl = self::$xtpl;
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('MANAGECURRS', 'current');
		$curr = Currency::getInstance();
		$currs = $curr->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		if(count($currs) < 1){
			$xtpl->parse('main.managecurrs.cursinfo');
		} else {
			for($i=0;$i<count($currs);$i++){
				$xtpl->assign('CURR', $currs[$i]);
				$xtpl->parse('main.managecurrs.curstable.currow');
			}
			for($i=1;$i<=self::count_pages($curr->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.managecurrs.curstable.page');
			}
			$xtpl->parse('main.managecurrs.curstable');
		}
		$xtpl->parse('main.managecurrs');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public function EditCurrency($currid){
		$xtpl = self::$xtpl;
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('MANAGECURRS', 'current');
		$curr = Currency::getInstance();
		if(!is_numeric($currid) || !$currid){
			$xtpl->parse('main.editcurr.currinfo');
		} else {
			//var_dump($curr->FetchData($currid));
			$xtpl->assign('CURR', $curr->FetchData($currid));
			$xtpl->parse('main.editcurr.currbox');
		}
		$xtpl->parse('main.editcurr');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public function AddCurrency(){
		$xtpl = self::$xtpl;
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('MANAGECURRS', 'current');
		$xtpl->parse('main.addcurr');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageCustomer($username='', $userid=''){
		$xtpl = self::$xtpl;
		$xtpl->assign('CUSTMANAGECURR', 'current');
		$xtpl->assign('CUSTCURR', 'current');
		$user = User::getInstance();
		if(!is_numeric($userid)){
			$userid = $user->GetID($username, 'username');
		}
		$userdata = $user->FetchData($userid);
		if(!$userdata){
			$xtpl->parse('main.managecustomer.customererror');
		} else {
			$xtpl->assign('CUSTOMER', $userdata);
			$order = Order::getInstance();
			$inv = Invoice::getInstance();
			$profile = Profile::getInstance();
			$ticket = Ticket::getInstance();
			$nt = Notification::getInstance();
			
			$xtpl->assign('TOTALORDERS', $order->Calculate('`accountid` = '.$userdata['id']));
			$xtpl->assign('TOTALINV', $inv->Calculate('`accountid` = '.$userdata['id']));
			$xtpl->assign('TOTALTICKETS', $ticket->Calculate('`userid` = '.$userdata['id']));
			$xtpl->assign('TOTALNOTIFICATIONS', $nt->Calculate('`userid` = '.$userdata['id']));
			
			$ordersbutch = $order->GetButch(25, '`accountid` = '.$userid);
			$invs = $inv->GetButch(25, '`accountid` = '.$userid);
			$tickets = $ticket->GetButch(25, '`userid` = '.$userid);
			$nts = $nt->GetButch(25, '`userid` = '.$userid);
			
			$profdata = $profile->FetchData($userid);
			$xtpl->assign('PROFILE', $profdata);
			if(count($ordersbutch) < 1){
				$xtpl->parse('main.managecustomer.customerorders.ordersinfo');
			} else {
				for($i = 0; $i < count($ordersbutch); $i++){
					$xtpl->assign('ORDER', $ordersbutch[$i]);
					$xtpl->parse('main.managecustomer.customerorders.orderstable.orderrow');
				}
				$xtpl->parse('main.managecustomer.customerorders.orderstable');
			}
			$xtpl->parse('main.managecustomer.customerorders');
			if(count($invs) < 1){
				$xtpl->parse('main.managecustomer.customerinvs.invsinfo');
			} else {
				for($i = 0; $i < count($invs); $i++){
					$xtpl->assign('INV', $invs[$i]);
					$xtpl->parse('main.managecustomer.customerinvs.invstable.invrow');
				}
				$xtpl->parse('main.managecustomer.customerinvs.invstable');
			}
			$xtpl->parse('main.managecustomer.customerinvs');

			if(count($tickets) < 1){
				$xtpl->parse('main.managecustomer.customertickets.ticketsinfo');
			} else {
				$dep = Department::getInstance();
				for($i = 0; $i < count($tickets); $i++){
					$xtpl->assign('TICKET', $tickets[$i]);
					$xtpl->assign('DEPNAME', $dep->GetName($tickets[$i]['id']));
					$xtpl->parse('main.managecustomer.customertickets.ticketstable.ticketrow');
				}
				$xtpl->parse('main.managecustomer.customertickets.ticketstable');
			}
			$xtpl->parse('main.managecustomer.customertickets');

			if(count($nts) < 1){
				$xtpl->parse('main.managecustomer.notifications.ntinfo');
			} else {
				for($i=0;$i<count($nts);$i++){
					$xtpl->assign('NT', $nts[$i]);
					$xtpl->parse('main.managecustomer.notifications.nttable.ntrow');
				}
				$xtpl->parse('main.managecustomer.notifications.nttable');
			}
			$xtpl->parse('main.managecustomer.notifications');


			
			$xtpl->parse('main.managecustomer.customerdata');
		}
		$xtpl->parse('main.managecustomer');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageServerModules(){
		$xtpl = self::$xtpl;
		$xtpl->assign('SMODMANAGECURR', 'current');
		$xtpl->assign('SERVERSCURR', 'current');
		$servermodule = ServerModule::getInstance();
		try {
			$availablemodules = $servermodule->RetriveAllModules();
		} catch(Exception $e){
			if(iDEBUG) {
				$msg = $e;
			} else {
				$msg = $e->getMessage();
			}
			self::add_message($xtpl, 'attention', nl2br($msg));
		}
		
		$activemodules = $servermodule->GetButch('5');
		if(count($activemodules) < 1){
			$xtpl->parse('main.manageservermodules.activemodulesinfo');
		} else {
			for($i=0;$i<count($activemodules);$i++){
				$xtpl->assign('ACTIVEMODULE', $activemodules[$i]);
				$xtpl->parse('main.manageservermodules.activemodulesstable.activemodulerow');
			}
			$xtpl->parse('main.manageservermodules.activemodulesstable');
		}
		if(count($availablemodules) < 1){
			$xtpl->parse('main.manageservermodules.availablemodulesinfo');
		} else {
			for($i=0;$i<count($availablemodules); $i++){
				//$servermodule->name = $availablemodules[$i];
				$xtpl->assign('AVAILABLEMODULENAME', $availablemodules[$i]);
				if($servermodule->GetID($availablemodules[$i],'modulename')){
					$xtpl->assign('AVAILABLEMODULESTATUS', 'Active');
				} else {
					$xtpl->assign('AVAILABLEMODULESTATUS', 'Inactive');
				}
				$xtpl->parse('main.manageservermodules.availablemodulesstable.availablemodulerow');
			}
			$xtpl->parse('main.manageservermodules.availablemodulesstable');
		}
		$xtpl->parse('main.manageservermodules');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageServerGroups(){
		$xtpl = self::$xtpl;
		$xtpl->assign('SERVERSCURR', 'current');
		$xtpl->assign('SGROUPMANAGECURR', 'current');
		$servergroups = ServerGroups::getInstance();
		$module = ServerModule::getInstance();
		$groupsbutch = $servergroups->GetButch('5');
		if(count($groupsbutch) < 1){
			$xtpl->parse("main.manageservergroups.servergroupsinfo");
		} else {
			for($i=0;$i<count($groupsbutch);$i++){
				$xtpl->assign('GROUP', $groupsbutch[$i]);
				try {
					if(!($modulename = $module->GetName($groupsbutch[$i]['moduleid']))){
						throw new Exception("Unable to find server module #".$groupsbutch[$i]['moduleid']." for server group #".$groupsbutch[$i]['id']);
					}
					$xtpl->assign('MODULENAME', $modulename);
				} catch (Exception $e){
					if(iDEBUG) {
						$msg = $e;
					} else {
						$msg = $e->getMessage();
					}
					self::add_message($xtpl, 'attention', nl2br($msg));
				}
				$xtpl->parse('main.manageservergroups.servergroupstable.servergroupsrow');
			}
			$xtpl->parse('main.manageservergroups.servergroupstable');
		}
		$xtpl->parse('main.manageservergroups');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditServerGroup($groupid){
		$xtpl = self::$xtpl;
		$xtpl->assign('SERVERSCURR', 'current');
		$xtpl->assign('SGROUPMANAGECURR', 'current');
		$sg = ServerGroups::getInstance();
		$module = ServerModule::getInstance();
		if(!is_numeric($groupid)){
			$xtpl->parse('main.editservergroup.info');
		} else {
			$sgdata = $sg->FetchData($groupid);
			$modules = $module->GetButch('',"`status` = '1'");
			if(!$sgdata){
				self::add_message($xtpl, 'attention', 'Server group doesnt exists with id '.$groupid);
			} else {
				$xtpl->assign('GROUP', $sgdata);
				$xtpl->assign('DEF'.$sgdata['status'], 'selected="selected"');
			}
			if(count($modules) < 1){
				$xtpl->parse('main.editservergroup.table.moduleserror');
			} else {
				for($i=0;$i<count($modules);$i++){
					if(@$sgdata['moduleid '] == @$modules[$i]['id']){
						$xtpl->assign('DEFAULT', 'selected="selected"');
					} else {
						$xtpl->assign('DEFAULT', '');
					}
					$xtpl->assign('MODULE', $modules[$i]);
					$xtpl->parse('main.editservergroup.table.moduleslist');
				}
			}
			$xtpl->parse('main.editservergroup.table');
		}
		$xtpl->parse('main.editservergroup');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddServerGroup(){
		$xtpl = self::$xtpl;
		$xtpl->assign('SERVERSCURR', 'current');
		$xtpl->assign('SGROUPMANAGECURR', 'current');
		$servermodule = ServerModule::getInstance();
		$servermodules = $servermodule->GetButch();
		if(count($servermodules) < 1){
			$xtpl->assign('DISABLED', 'disabled');
			$xtpl->parse('main.addservergroup.moduleserror');
		} else {
			for($i=0;$i<count($servermodules);$i++){
				$xtpl->assign('MODULE', $servermodules[$i]);
				$xtpl->parse('main.addservergroup.moduleslist');
			}
		}
		$xtpl->parse('main.addservergroup');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddServerStep1(){
		$xtpl = self::$xtpl;
		$xtpl->assign('SERVERSCURR', 'current');
		$xtpl->assign('SSERVERMANAGECURR', 'current');
		$servergroups = ServerGroups::getInstance();
		$groups = $servergroups->GetButch();
		if(count($groups) < 1){
			$xtpl->assign('DISABLED', 'disabled');
			$xtpl->parse('main.addserverstep1.groupslisterror');
		} else {
			for($i=0;$i<count($groups);$i++){
				$xtpl->assign("GROUP", $groups[$i]);
				$xtpl->parse('main.addserverstep1.groupslist');
			}
		}
		$xtpl->parse('main.addserverstep1');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddServerStep2($groupid){
		$xtpl = self::$xtpl;
		$group = ServerGroups::getInstance();
		$module = ServerModule::getInstance();
		$groupinfo = $group->FetchData($groupid);
		
		$module->id = $groupinfo['moduleid'];
		$moduleclass = $module->GetName().'ServerModule';
		//I FUCKING HATE PHP < 5.3
		$moduleclass = call_user_func(array($moduleclass,'getInstance'));
		$xtpl->assign('SERVERSCURR', 'current');
		$xtpl->assign('SSERVERMANAGECURR', 'current');
		$xtpl->assign('MODULEID', $groupinfo['moduleid']);
		$xtpl->assign('MODULENAME', $module->GetName());
		$xtpl->assign('GROUPID', $groupid);
		foreach($moduleclass->OperateRequirements() as $k => $v){
			$xtpl->assign('INPUT', $v);
			if($v['type'] == 'text' || $v['type'] == 'password'){
				$xtpl->parse('main.addserverstep2.inputtext');
			} else {
				throw new Exception("Unsupportted field type");
			}
		}
		$xtpl->parse('main.addserverstep2');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function Servers(){
		$xtpl = self::$xtpl;
		$server = Server::getInstance();
		$group = ServerGroups::getInstance();
		$module = ServerModule::getInstance();
		$xtpl->assign('SERVERSCURR', 'current');
		$xtpl->assign('SSERVERMANAGECURR', 'current');
		$butch = $server->GetButch('5');
		if(count($butch) < 1){
			$xtpl->parse('main.manageservers.serversinfo');
		} else {
			for($i=0;$i<count($butch);$i++){
				try {
					$groupinfo = $group->FetchData($butch[$i]['servergroupid']);
					if(!is_array($groupinfo)){
						throw new Exception("Unable to find group of servers #".$butch[$i]['servergroupid']." for server ".$butch[$i]['id']);
					} else {
						$xtpl->assign('GROUPNAME', $groupinfo['name']);
					}
				} catch(Exception $e){
					if(iDEBUG) {
						$msg = $e;
					} else {
						$msg = $e->getMessage();
					}
					self::add_message($xtpl, 'attention', nl2br($msg));
				}
				try {
					$moduleinfo = $module->FetchData($groupinfo['moduleid']);
					if(!is_array($moduleinfo)){
						throw new Exception("Unable to find module #".$groupinfo['moduleid']." for server ".$butch[$i]['id']);
					} else {
						$xtpl->assign('MODULENAME', $moduleinfo['modulename']);
					}
				} catch (Exception $e){
					if(iDEBUG) {
						$msg = $e;
					} else {
						$msg = $e->getMessage();
					}
					self::add_message($xtpl, 'attention', nl2br($msg));
				}
				
				$xtpl->assign('SERVER', $butch[$i]);
				$xtpl->parse('main.manageservers.serverstable.serversrow');
			}
			$xtpl->parse('main.manageservers.serverstable');
		}
		$xtpl->parse('main.manageservers');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function Presets(){
		$xtpl = self::$xtpl;
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$pkg = Preset::getInstance();
		$pkgbutch = $pkg->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		$xtpl->assign('PRODUCTSCURR', 'current');
		$xtpl->assign('PRESETMANAGECURR', 'current');
		if(count($pkgbutch) < 1){
			$xtpl->parse('main.managepresgroups.presetsinfo');
		} else {
			for($i=0;$i<count($pkgbutch);$i++){
				$xtpl->assign('PKG', $pkgbutch[$i]);
				$xtpl->parse('main.managepresgroups.pkgstable.pkgrow');
			}
			for($i=1;$i<=self::count_pages($pkg->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.managepresgroups.pkgstable.page');
			}
			$xtpl->parse('main.managepresgroups.pkgstable');
		}
		$xtpl->parse('main.managepresgroups');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditPreset($presetid){
		$fieldvalue = '';
		$xtpl = self::$xtpl;
		$xtpl->assign('PRODUCTSCURR', 'current');
		$xtpl->assign('PRESETMANAGECURR', 'current');
		$preset = Preset::getInstance();
		$servergroup = ServerGroups::getInstance();
		$module = ServerModule::getInstance();
		if(!is_numeric($presetid)){
			throw new Exception("Preset ID is not numeric");
		}
		if(!($presetdata = $preset->FetchData($presetid))){
			throw new Exception("Preset doesnt exists with id ".$presetid);
		}
		$xtpl->assign('PRES', $presetdata);
		$sgdata = $servergroup->FetchData($presetdata['groupid']);
		$module->id = $sgdata['moduleid'];
		$modulename = $module->GetName().'ServerModule';
		if($modulename == 'ServerModule'){
			throw new Exception("Unable to get module name with id ".$sgdata['moduleid']);
		}
		if(!class_exists($modulename)){
			throw new Exception("Server module doesnt exists with name ".$modulename);
		}
		$moduleclass = $modulename::getInstance();
		//var_dump(unserialize($presetdata['paramsdata']));
		$data = unserialize($presetdata['paramsdata']);
		//var_dump($data);
		foreach($moduleclass->ClientOptions() as $k => $v){
			
			//for($i=0;$i<count($data);$i++){
				if(array_key_exists($v['name'], $data)){
					$fieldvalue = $data[$v['name']];
					$xtpl->assign('FIELDVALUE', $data[$v['name']]);
				}
			//}
			
			$xtpl->assign('VALUE', $presetdata['paramsdata'][$v['name']]);
			$xtpl->assign('INPUT', $v);
			switch($v['type']){
				case 'text':
					$xtpl->parse('main.editpreset.inputtext');
				break;
				case 'password':
					$xtpl->parse('main.editpreset.inputtext');
				break;
				case 'checkbox':
					$xtpl->parse('main.editpreset.checkbox');
				break;
				case 'select':
					foreach($v['select'] as $option => $opt_label){
						if($fieldvalue == $option){
							$xtpl->assign('SELECTED', 'selected="selected"');
						} else {
							$xtpl->assign('SELECTED', '');
						}
						$xtpl->assign('OPTIONNAME', $option);
						$xtpl->assign('OPTIONLABEL', $opt_label);
						$xtpl->parse('main.editpreset.select.option');
					}
					$xtpl->parse('main.editpreset.select');
				break;
				default:
					throw new Exception("Unknown field type: ".$v['type']);
			}
		}
		$xtpl->parse('main.editpreset');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddPreStep1(){
		$xtpl = self::$xtpl;
		$servergroups = ServerGroups::getInstance()->GetButch();
		$xtpl->assign('PRODUCTSCURR', 'current');
		$xtpl->assign('PRESETMANAGECURR', 'current');
		if(count($servergroups) < 1){
			$xtpl->assign('DISABLED', 'disabled');
		} else {
			for($i=0;$i<count($servergroups);$i++){
				$xtpl->assign('GROUP', $servergroups[$i]);
				$xtpl->parse('main.addprestep1.groupslist');
			}
		}
		$xtpl->parse('main.addprestep1');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddPreStep2($groupid){
		$xtpl = self::$xtpl;
		$servergroup = ServerGroups::getInstance()->FetchData($groupid);
		$module = ServerModule::getInstance();
		$module->id = $servergroup['moduleid'];
		$modulename = $module->GetName().'ServerModule';
		$moduleclass = call_user_func(array($modulename,'getInstance'));
		$xtpl->assign('PRODUCTSCURR', 'current');
		$xtpl->assign('PRESETMANAGECURR', 'current');
		$xtpl->assign('GROUPID', $groupid);
		foreach($moduleclass->ClientOptions() as $k => $v){
			$xtpl->assign('INPUT', $v);
			switch($v['type']){
				case 'text':
					$xtpl->parse('main.addprestep2.inputtext');
				break;
				case 'password':
					$xtpl->parse('main.addprestep2.inputtext');
				break;
				case 'checkbox':
					$xtpl->parse('main.addprestep2.checkbox');
				break;
				case 'select':
					foreach($v['select'] as $option => $opt_label){
						$xtpl->assign('OPTIONNAME', $option);
						$xtpl->assign('OPTIONLABEL', $opt_label);
						$xtpl->parse('main.addprestep2.select.option');
					}
					$xtpl->parse('main.addprestep2.select');
				break;
				default:
					throw new Exception("Unknown field type");
			}
		}
		$xtpl->parse('main.addprestep2');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function Package(){
		$xtpl = self::$xtpl;
		$pkg = Package::getInstance();
		$preset = Preset::getInstance();
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('PRODUCTSCURR', 'current');
		$xtpl->assign('PKGMANAGECURR', 'current');
		$butch = $pkg->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		if(count($butch) < 1){
			$xtpl->parse('main.managepkgs.pkgsinfo');
		} else {
			for($i=0;$i<count($butch);$i++){
				$presetname = '';
				if(!is_array($presetdata = $preset->FetchData($butch[$i]['presetid']))){
					self::add_message($xtpl, 'attention', 'Preset #'.$butch[$i]['presetid'].' not found for package #'.$butch[$i]['id']);
				} else {
					$xtpl->assign('PRESETNAME', $presetdata['name']);
				}
				$xtpl->assign('PKG', $butch[$i]);
				$xtpl->parse('main.managepkgs.pkgstable.pkgrow');
			}
			for($i=1;$i<=self::count_pages($pkg->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.managepkgs.pkgstable.page');
			}
			$xtpl->parse('main.managepkgs.pkgstable');
		}
		$xtpl->parse('main.managepkgs');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddPkg(){
		$xtpl = self::$xtpl;
		$xtpl->assign('PRODUCTSCURR', 'current');
		$xtpl->assign('ADDPKGCURR', 'current');
		$presets = Preset::getInstance()->GetButch();
		if(count($presets) < 1){
			$xtpl->assign('DISABLED', 'disabled');
			$xtpl->parse('main.addpkg.preseterror');
		} else {
			for($i=0;$i<count($presets);$i++){
				$xtpl->assign('PRESET',$presets[$i]);
				$xtpl->parse('main.addpkg.preslist');
			}
		}
		$xtpl->parse('main.addpkg');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddInvoice(){
		$xtpl = self::$xtpl;
		$xtpl->assign('INVCURR', 'current');
		$xtpl->assign('ADDINVCURR', 'current');
		$users = User::getInstance()->GetButch();
		$time = Time::getInstance();
		$xtpl->assign('TODAY', $time->UtoM(time()));
		if(count($users) < 1){
			$xtpl->assign('DISABLED', 'disabled');
			$xtpl->parse('main.addinv.custerror');
		}
		$xtpl->parse('main.addinv');
		$xtpl->parse('main');
		$xtpl->out('main');
		//to be continious
	}
	public static function AddOrder(){
		$xtpl = self::$xtpl;
		$xtpl->assign('ORDERSCURR', 'current');
		$xtpl->assign('ADDORDERSCURR', 'current');
		$users = User::getInstance()->GetButch();
		$pkg = Package::getInstance()->GetButch();
		if(count($users) < 1){
			$xtpl->parse('main.addorder.usererror');
			$xtpl->assign('DISABLED', 'disables');
		} else {
			for($i=0;$i<count($users);$i++){
				$xtpl->assign('USER', $users[$i]);
				$xtpl->parse('main.addorder.userlist');
			}
		}
		if(count($pkg) < 1){
			$xtpl->parse('main.addorder.pkgerror');
			$xtpl->assign('DISABLED', 'disables');
		} else {
			for($i=0;$i<count($pkg);$i++){
				$xtpl->assign('PKG', $pkg[$i]);
				$xtpl->parse('main.addorder.pkglist');
			}
		}
		$xtpl->parse('main.addorder');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageOrders(){
		$xtpl = self::$xtpl;
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('ORDERSCURR', 'current');
		$xtpl->assign('MANAGEORDERSCURR', 'current');
		$order = Order::getInstance();
		$orders = $order->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		$user = User::getInstance();
		$pkg = Package::getInstance();
		if(count($orders) < 1){
			$xtpl->parse('main.manageorders.ordersinfo');
		} else {
			for($i=0;$i<count($orders);$i++){
				$username = '';
				$pkgname = '';
				if(!is_numeric($orders[$i]['accountid']) || !is_string($username = $user->GetUsername($orders[$i]['accountid']))){
					self::add_message($xtpl, 'attention', 'User #'.$orders[$i]['accountid'].' not found for order #'.$orders[$i]['id']);
				}
				if(!is_numeric($orders[$i]['productid']) || !is_string($pkgname = $pkg->GetName($orders[$i]['productid']))){
					self::add_message($xtpl, 'attention', 'Package #'.$orders[$i]['productid'].' not found for order #'.$orders[$i]['id']);
				}
				$xtpl->assign('PKGNAME', $pkgname);
				$xtpl->assign('USERNAME', $username);
				$xtpl->assign('ORDER', $orders[$i]);
				$xtpl->parse('main.manageorders.orderstable.ordersrow');
			}
			for($i=1;$i<=self::count_pages($order->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.manageorders.orderstable.page');
			}
			$xtpl->parse('main.manageorders.orderstable');
		}
		$xtpl->parse('main.manageorders');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageInvoices(){
		$xtpl = self::$xtpl;
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('MANAGEINVCURR', 'current');
		$inv = Invoice::getInstance();
		$user = User::getInstance();
		$invoice = $inv->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		$curr = Currency::getInstance();
		if(count($invoice) < 1){
			$xtpl->parse('main.manageinvs.invsinfo');
		} else {
			for($i=0;$i<count($invoice);$i++){
				$username = '';
				if(!is_numeric($invoice[$i]['accountid']) || !is_string($username = $user->GetUsername($invoice[$i]['accountid']))){
					self::add_message($xtpl, 'attention', 'User #'.$invoice[$i]['accountid'].' not found for invoice #'.$invoice[$i]['id']);
				} else {
					$xtpl->assign('USERNAME', $username);
				}
				$xtpl->assign('INV', $invoice[$i]);
				$xtpl->assign('AMOUNT', $curr->FormatCurrency($invoice[$i]['amount']));
				$xtpl->parse('main.manageinvs.invstable.invrow');
			}
			for($i=1;$i<=self::count_pages($inv->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.manageinvs.invstable.page');
			}
			$xtpl->parse('main.manageinvs.invstable');
		}
		$xtpl->parse('main.manageinvs');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageTickets(){
		$xtpl = self::$xtpl;
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('SUPCURR', 'current');
		$ticket = Ticket::getInstance();
		if(self::$select_where != NULL){
			if(strstr(self::$select_where, 'status') && strstr(self::$select_where, 'Support')){
				$xtpl->assign('OPENTCURR', 'current');
			} elseif(strstr(self::$select_where, 'status') && strstr(self::$select_where, 'Customer')){
				$xtpl->assign('ANSWTCURR', 'current');
			} elseif(strstr(self::$select_where, 'status') && strstr(self::$select_where, 'Hold')){
				$xtpl->assign('HOLDTCURR', 'current');
			} elseif(strstr(self::$select_where, 'status') && strstr(self::$select_where, 'Closed')){
				$xtpl->assign('CLOSEDTCURR', 'current');
			} elseif(strstr(self::$select_where, 'status') && strstr(self::$select_where, 'Progress')){
				$xtpl->assign('PRORESSTCURR', 'current');
			}
			$where = self::$select_where;
		} else {
			$where = 1;
		}
		$tcs = $ticket->GetButch(self::$per_page, $where, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		//$tcs = $ticket->GetButch(20,$where);
		if(count($tcs) < 1){
			$xtpl->parse('main.managetickets.ticketsinfo');
		} else {
			$dep = Department::getInstance();
			$user = User::getInstance();
			for($i=0;$i<count($tcs);$i++){
				$depname = '';
				$tusername = '';
				$xtpl->assign('TICKET', $tcs[$i]);
				try {
					if(!is_numeric($tcs[$i]['depid'])){
						throw new Exception("Department ID for ticket ".$tcs[$i]['id']." in wrong format");
					} else {
						$depname = $dep->GetName($tcs[$i]['depid']);
					}
				} catch (Exception $e){
					$xtpl->assign('ATTENTIONMSG', 'Fatal error catched during fetching additional ticket information. This means ticket #'.$tcs[$i]['id'].' is not assigned to any user or department. Error dump: '.nl2br($e));
					$xtpl->parse('main.attention');
				}
				try {
					$tusername = $user->GetUsername($tcs[$i]['userid']);
				} catch (Exception $e){
					$xtpl->assign('ATTENTIONMSG', 'Fatal error catched during fetching additional ticket information. This means ticket #'.$tcs[$i]['id'].' is not assigned to any user or department. Error dump: '.nl2br($e));
					$xtpl->parse('main.attention');
				}
				if($tcs[$i]['id'] == 3){
					//var_dump($depname);
					}
				$xtpl->assign('DEPNAME', $depname);
				$xtpl->assign('USERNAME', $tusername);
				$xtpl->parse('main.managetickets.ticketstable.ticketsrow');
			}
			//tickets pagination
			for($i=1;$i<=self::count_pages($ticket->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.managetickets.ticketstable.page');
			}
			$xtpl->parse('main.managetickets.ticketstable');
		}
		$xtpl->parse('main.managetickets');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ViewTicket($tid){
		$xtpl = self::$xtpl;
		$xtpl->assign('SUPCURR', 'current');
		$tc = TicketChange::getInstance();
		$user = User::getInstance();
		$ticket = Ticket::getInstance();
		$ticdata = $ticket->FetchData($tid);
		if(!is_numeric($tid) || !$ticdata){
			$xtpl->parse('main.viewticket.ticketrinfo');
		} else {
			$xtpl->assign('TID',$tid);
			$xtpl->assign('DEF'.$ticdata['status'], 'selected="selected"');
			$tcs = $tc->GetButch('','`ticketid` = "'.$tid.'"');
			for($i=0;$i<count($tcs);$i++){
				$xtpl->assign('USERNAME', $user->GetUsername($tcs[$i]['userid']));
				$xtpl->assign('TC', $tcs[$i]);
				$xtpl->parse('main.viewticket.ticket.ticket'.$tcs[$i]['type']);
			}
			$xtpl->parse('main.viewticket.ticket');
		}
		$xtpl->parse('main.viewticket');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function Invoice($invdata, $defgw = NULL){
		$currency = NULL;
		$oparray = array();
		$xtpl = XTemplate::getInstance();
		$gm = GatewayModule::getInstance();
		$setting = Settings::getInstance();
		$curr = Currency::getInstance();
		$user = User::getInstance();
		$xtpl->restart("themes/simpla/invoice.tpl");
		if(count($invdata) < 2){
			$xtpl->parse('inverror');
		} else {
			$gmodules = $gm->GetButch();
			if($defgw == NULL){
				$defgw = $setting->Get('system.paygateway.default');
			}
			$prov = call_user_func(array($gm->GetName($defgw)."PaymentGateway", 'getInstance'));
			$gwdata = $gm->FetchData($defgw);
			if(strlen($gwdata['currency']) != 3){
				$currs = $curr->GetButch();
				for($i=0;$i<count($currs);$i++){
					if(in_array($currs[$i]['name'], $prov->Currency())){
						if($setting->Get('system.currency') == $currs[$i]['name'] || $currency == NULL){
							$currency = $currs[$i];
						}
					}
				}
			} else {
				$currency = $curr->FetchData($curr->GetID($gwdata['currency'],'name'));
				//$currency = $curr->GetCurrency('', $gwdata['currency']);
			}
			$xtpl->assign('CURR', $currency);
			$xtpl->assign('AMOUNT', $curr->FormatCurrency($invdata['amount'], $currency['id']));
			$data_array['userdata'] = $user->FetchData($invdata['accountid']);
			$data_array['invoice'] = $invdata;
			$data_array['currency'] = $currency['name'];
			$data_array['invoice']['amount'] = $curr->FormatCurrency($invdata['amount'], $currency['id'], -1, true);
			$oparray = unserialize($gwdata['data']);
			$xtpl->assign('FORM', $prov->Form($oparray, $data_array));
			for($i=0;$i<count($gmodules);$i++){
				$prov = call_user_func(array($gmodules[$i]['modulename']."PaymentGateway",'getInstance'));
				if($defgw == $gmodules[$i]['id']){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->assign('GM', $gmodules[$i]);
				$ginfo = $prov->Info();
				$xtpl->assign('GNAME', $ginfo['name']);
				$xtpl->parse('main.gwlist');
			}
		}
		$xtpl->assign('INV', $invdata);
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageGatewayModules(){
		$xtpl = self::$xtpl;
		$gm = GatewayModule::getInstance();	
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('MANAGEGWMCURR', 'current');
		$activemodules = $gm->GetButch();
		try {
			$allmodules = $gm->RetriveAllModules();
		} catch(Exception $e){
			if(iDEBUG) {
				$msg = $e;
			} else {
				$msg = $e->getMessage();
			}
			self::add_message($xtpl, 'attention', nl2br($msg));
		}
		if(count($activemodules) < 1){
			$xtpl->parse('main.managegatewaymodules.activemodulesinfo');
		} else {
			for($i=0;$i<count($activemodules);$i++){
				$xtpl->assign('ACTIVEMODULE', $activemodules[$i]);
				$xtpl->parse('main.managegatewaymodules.activemodulesstable.activemodulerow');
			}
			$xtpl->parse('main.managegatewaymodules.activemodulesstable');
		}
		if(count($allmodules) < 1){
			$xtpl->parse('main.managegatewaymodules.availablemodulesinfo');
		} else {
			for($i=0;$i<count($allmodules);$i++){
				$xtpl->assign('AVAILABLEMODULENAME', $allmodules[$i]);
				if($gm->GetID($allmodules[$i])){
					$xtpl->assign('AVAILABLEMODULESTATUS', 'Active');
				} else {
					$xtpl->assign('AVAILABLEMODULESTATUS', 'Inactive');
				}
				$xtpl->parse('main.managegatewaymodules.availablemodulesstable.availablemodulerow');
			}
			$xtpl->parse('main.managegatewaymodules.availablemodulesstable');
		}
		$xtpl->parse('main.managegatewaymodules');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageNotifies(){
		$xtpl = self::$xtpl;
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('NOTIFYCURR', 'current');
		$xtpl->assign('NOTIFYHISTCURR', 'current');
		$notify = Notification::getInstance();
		$nots = $notify->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		if(count($nots) < 1){
			$xtpl->parse('main.managenotifies.notfound');
		} else {
			for($i=0;$i<count($nots);$i++){
				$xtpl->assign('NOT', $nots[$i]);
				$xtpl->parse('main.managenotifies.table.row');
			}
			for($i=1;$i<=self::count_pages($notify->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.managenotifies.table.page');
			}
			$xtpl->parse('main.managenotifies.table');
		}
		$xtpl->parse('main.managenotifies');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageNotifyTemplates(){
		$xtpl = self::$xtpl;
		$xtpl->assign('NOTIFYCURR', 'current');
		$xtpl->assign('NTEMPLATESCURR', 'current');
		$notify = NotifyTemplate::getInstance();
		$nots = $notify->GetButch();
		if(count($nots) < 1){
			$xtpl->parse('main.managenotifytemplates.notfound');
		} else {
			for($i=0;$i<count($nots);$i++){
				$xtpl->assign('NT', $nots[$i]);
				$xtpl->parse('main.managenotifytemplates.table.row');
			}
			$xtpl->parse('main.managenotifytemplates.table');
		}
		$xtpl->parse('main.managenotifytemplates');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function AddNotifyTemplate(){
		$xtpl = self::$xtpl;
		$xtpl->assign('NOTIFYCURR', 'current');
		$xtpl->assign('NTEMPLATESCURR', 'current');
		$xtpl->parse('main.addnotifytemplate');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditNotifyTemplate($ntid){
		$xtpl = self::$xtpl;
		$xtpl->assign('NOTIFYCURR', 'current');
		$xtpl->assign('NTEMPLATESCURR', 'current');
		if(!is_numeric($ntid)){
			$xtpl->parse('main.editnotifytemplate.wrongid');
		} else {
			$nt = NotifyTemplate::getInstance();
			$ntdata = $nt->FetchData($ntid);
			$xtpl->assign('NT', $ntdata);
			$xtpl->parse('main.editnotifytemplate.content');
		}
		$xtpl->parse('main.editnotifytemplate');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public function ManageNotifyModules(){
		$xtpl = self::$xtpl;
		$xtpl->assign('NOTIFYCURR', 'current');
		$xtpl->assign('NTMODULESCURR', 'current');
		$nm = NotificationModule::getInstance();
		try {
			$nm_all = $nm->RetriveAllModules();
		} catch(Exception $e){
			if(iDEBUG) {
				$msg = $e;
			} else {
				$msg = $e->getMessage();
			}
			self::add_message($xtpl, 'attention', nl2br($msg));
		}
		$nm_active = $nm->GetButch();
		if(count($nm_active) < 1){
			$xtpl->parse('main.managenotifymodules.activemodulesinfo');
		} else {
			for($i=0;$i<count($nm_active);$i++){
				$xtpl->assign('ACTIVEMODULE', $nm_active[$i]);
				$xtpl->parse('main.managenotifymodules.activemodulesstable.activemodulerow');
			}
			$xtpl->parse('main.managenotifymodules.activemodulesstable');
		}
		if(count($nm_all) < 1){
			$xtpl->parse('main.managenotifymodules.availablemodulesinfo');
		} else {
			for($i=0;$i<count($nm_all);$i++){
				if($nm->GetID($nm_all[$i], 'name')){
					$xtpl->assign('AVAILABLEMODULESTATUS', 'Active');
				} else {
					$xtpl->assign('AVAILABLEMODULESTATUS', 'Not active');
				}
				$xtpl->assign('AVAILABLEMODULENAME', $nm_all[$i]);
				$xtpl->parse('main.managenotifymodules.availablemodulesstable.availablemodulerow');
			}
			$xtpl->parse('main.managenotifymodules.availablemodulesstable');
		}
		$xtpl->parse('main.managenotifymodules');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditNotifyModule($id){
		$xtpl = self::$xtpl;
		$xtpl->assign('NOTIFYCURR', 'current');
		$xtpl->assign('NTMODULESCURR', 'current');
		if(!is_numeric($id)){
			$xtpl->parse('main.editntmodule.notfound');
		} else {
			$nm = NotificationModule::getInstance();
			$nmd = NotifyModuleData::getInstance();
			$nm_values = $nmd->GetButch('',"`moduleid` = '".$id."'");
			$name = $nm->FetchData($id);
			$name = $name['name'].'notifymodule';
			$module = $name::getInstance();
			$reqs = $module->OperateRequirements();
			$xtpl->assign('INFO', $module->Info());
			$xtpl->assign('MODULEID', $id);
			for($i=0;$i<count($reqs);$i++){
				if(count($nm_values) > 0){
					//var_dump($reqs);
					for($m=0;$m<count($nm_values);$m++){
						if($reqs[$i]['name'] == $nm_values[$m]['name']){
							$xtpl->assign('VALUE',$nm_values[$m]['value']);
						}
					}
				}
				$xtpl->assign('INPUT', $reqs[$i]);
				$xtpl->parse('main.editntmodule.table.inputtext');
			}
			$xtpl->parse('main.editntmodule.table');
		}
		
		$xtpl->parse('main.editntmodule');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function GeneralSettings(){
		$xtpl = self::$xtpl;
		$xtpl->assign('SETCURR', 'current');
		$xtpl->assign('GENERALSETTINGS', 'current');
		$setting = Settings::getInstance();
		$currs = Currency::getInstance();
		$pms = GatewayModule::getInstance();
		$lang = Lang::getInstance();
		$nm = NotificationModule::getInstance();
		$currlist = $currs->GetButch();
		$langs = $lang->GetButch();
		$gmlist = $pms->GetButch();
		try {
			$providers = $currs->RetriveAllModules();
		} catch(Exception $e){
			if(iDEBUG) {
				$msg = $e;
			} else {
				$msg = $e->getMessage();
			}
			self::add_message($xtpl, 'attention', nl2br($msg));
		}
		
		$ntm = $nm->GetButch();
		if(count($currlist) < 1){
			$xtpl->assign('DISABLED', 'disabled');
			$xtpl->parse('main.generalsettings.currerror');
		} else {
			$xtpl->assign('SYMBOL', $setting->Get('system.currency.symbol'));
			for($i=0;$i < count($currlist);$i++){
				if($currlist[$i]['name'] == $setting->Get('system.currency')){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->assign('CURR', $currlist[$i]);
				$xtpl->parse('main.generalsettings.curlist');
			}
			for($i=0;$i < count($providers);$i++){
				if($providers[$i]['name'] == $setting->Get('system.currency.autoupdate')){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->assign('PROVIDER', $providers[$i]);
				$xtpl->assign('PROVINFO', $providers[$i]['info']);
				$xtpl->parse('main.generalsettings.sourcelist');
			}
			for($i=0;$i<count($gmlist);$i++){
				if($gmlist[$i]['modulename'] == $setting->Get('system.paygateway.default')){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->assign('PM', $gmlist[$i]);
				$xtpl->parse('main.generalsettings.defaultpm');
			}
		}
		if(count($langs) > 0){
			for($i=0;$i<count($langs);$i++){
				$xtpl->assign('LANGCODE',$langs[$i]['code']);
				$xtpl->parse('main.generalsettings.langlist');
			}
		}
		if(count($ntm) > 0){
			$defnmodule = $setting->Get('system.notifymodule.default');
			for($i=0;$i<count($ntm);$i++){
				if($defmodule == $ntm[$i]['id']){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->assign('NMODULE',$ntm[$i]);
				$xtpl->parse('main.generalsettings.modulelist');
			}
		}
		$xtpl->parse('main.generalsettings');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function SendMessage(){
		
		$xtpl = self::$xtpl;
		$user = User::getInstance();
		$users = $user->GetButch();
		
		$nm = NotificationModule::getInstance();
		
		$xtpl->assign('NOTIFYCURR', 'current');
		$xtpl->assign('SENDNOTIFCURR', 'current');
		
		if(($nm->Calculate()) < 1){
			$xtpl->parse('main.sendmessage.mesinfo');
		} else {
			for($i=0;$i<count($users);$i++){
				$xtpl->assign('CUST', $users[$i]);
				$xtpl->parse('main.sendmessage.message.customers');
			}
			$xtpl->parse('main.sendmessage.message');
		}
		$xtpl->parse('main.sendmessage');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function PersonalSettings(){
		$xtpl = self::$xtpl;
		$nm = NotificationModule::getInstance();
		$lang = Lang::getInstance();
		$usrstg = UserSettings::getInstance();
		$setting = Settings::getInstance();
		$curr = Currency::getInstance();
		$xtpl->assign('PERSSETCURR', 'current');
		$xtpl->assign('PERSSETTINGSCURR', 'current');
		$usersettings = $usrstg->Get(self::$userid);
		$xtpl->assign('NOTIFYADDRESS', $usersettings['notifyaddress']);
		$nms = $nm->GetButch();
		$langs = $lang->GetButch();
		$currs = $curr->GetButch();
		if($usersettings['adminnewuser'] == "1" || !is_string($usersettings['adminnewuser'])){
			$xtpl->assign('NEWUSERSEL', 'checked="checked"');
		}
		if($usersettings['adminneworder'] == "1" || !is_string($usersettings['adminneworder'])){
			$xtpl->assign('NEWORDERSEL', 'checked="checked"');
		}
		if($usersettings['adminnewticket'] == "1" || !is_string($usersettings['adminnewticket'])){
			$xtpl->assign('NEWTCSEL', 'checked="checked"');
		}
		if($usersettings['adminnewticketreply'] == "1" || !is_string($usersettings['adminnewticketreply'])){
			$xtpl->assign('NEWTRSEL', 'checked="checked"');
		}
		if($usersettings['dailyreport'] == "1" || !is_string($usersettings['dailyreport'])){
			$xtpl->assign('DAILYREPORT', 'checked="checked"');
		}
		for($i=0;$i<count($nms);$i++){
			if($nms[$i]['id'] == $usersettings['notifymodule'] || $nms[$i]['id'] == $setting->Get('system.notifymodule.default')){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			$xtpl->assign('NM', $nms[$i]);
			$xtpl->parse('main.personalsettings.ntlist');
		}
		for($i=0;$i<count($langs);$i++){
			if($langs[$i]['code'] == $usersettings['language'] || $langs[$i]['code'] == $setting->Get('system.lang.default')){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			//var_dump($langs[$i]);
			$xtpl->assign('LNG', $langs[$i]);
			$xtpl->parse('main.personalsettings.langlist');
		}
		for($i=0;$i<count($currs);$i++){
			if($currs[$i]['name'] == $usersettings['currency'] || $currs[$i]['name'] == $setting->Get('system.currency')){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			$xtpl->assign('CURR',$currs[$i]);
			$xtpl->parse('main.personalsettings.currlist');
		}
		$xtpl->parse('main.personalsettings');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditGateway($modulename){
		$xtpl = self::$xtpl;
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('MANAGEGWMCURR', 'current');
		if(is_string($modulename)){
			$gm = GatewayModule::getInstance();
			$curr = Currency::getInstance();
			$currs = $curr->GetButch();
			$gmd = $gm->FetchData($gm->GetID($modulename));
			$gms = unserialize($gmd['data']);
			$module = call_user_func(array($modulename."PaymentGateway",'getInstance'));
			$modules = $module->OperateRequirements();
			$modulecurr = $module->Currency();
			$xtpl->assign('INFO', $module->Info());
			$xtpl->assign('GWNAME', $modulename);
			for($i=0;$i<count($currs);$i++){
				if(in_array($currs[$i]['name'], $modulecurr)){
					if($gmd['currency'] == $currs[$i]['name']){
						$xtpl->assign('DEFAULT', 'selected="selected"');
					} else {
						$xtpl->assign('DEFAULT', '');
					}
					$xtpl->assign('CURR', $currs[$i]);
					$xtpl->parse('main.editgateway.gateway.defcurr');
				}
			}
			
			if(is_array($modules) && count($modules) > 0){
				
				for($i=0;$i<count($modules);$i++){
					if(array_key_exists($modules[$i]['name'], $gms)){
						$modules[$i]['value'] = $gms[$modules[$i]['name']];
					}
					$xtpl->assign('INPUT', $modules[$i]);
					$xtpl->parse('main.editgateway.gateway.inputtext');
				}
				
			}
			$xtpl->parse('main.editgateway.gateway');
		} else {
			$xtpl->parse('main.editgateway.gwerror');
		}
		$xtpl->parse('main.editgateway');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditOrder($orderid){
		$xtpl = self::$xtpl;
		$xtpl->assign('ORDERSCURR', 'current');
		$xtpl->assign('MANAGEORDERSCURR', 'current');
		$order = Order::getInstance();
		$invoice = Invoice::getInstance();
		$user = User::getInstance();
		$sm = ServerModule::getInstance();
		$server = Server::getInstance();
		$pkg = Package::getInstance();
		$orderdata = $order->FetchData($orderid);
		$userdata = $user->FetchData($orderdata['accountid']);
		$lastinv = $invoice->FetchData($orderdata['lastinv']);
		$pkgs = $pkg->GetButch();
		$servers = $server->GetButch();
		$gms = @unserialize($orderdata['accessdata']);
		$xtpl->assign('USER', $userdata);
		if($userdata['status'] == 'Active'){
			$xtpl->assign('USERSTATUS', 'Active');
		} else {
			$xtpl->assign('USERSTATUS', 'Not active');
		}
		$xtpl->assign('ORDERID', $orderid);
		$xtpl->assign('LASTINV', $lastinv);
		$xtpl->assign('Status'.$orderdata['status'], 'selected="selected"');
		$xtpl->assign('DEF'.$orderdata['cycle'], 'selected="selected"');
		$xtpl->assign('FIRSTAMOUNT', $orderdata['firstamount']);
		$xtpl->assign('RECURAMOUNT', $orderdata['recuramount']);
		$xtpl->assign('ORDERDATE', $orderdata['orderdate']);
		$xtpl->assign('NEXTDUE', $orderdata['nextdue']);
		
		$order->orderid = $orderid;
		try {
			$sm->id = $order->FindModuleID();
			$servername = $sm->GetName()."ServerModule";
			if(!class_exists($servername)){
				throw new Exception("Server module not found with name: ".$servername);
			}
			$module = $servername::getInstance();
			if(!is_object($module)){
				throw new Exception("Unable to load server module with name: ".$servername);
			}
			$modules = $module->CreateOptions();
		} catch(Exception $e){
			$xtpl->assign('ATTENTIONMSG', 'Fatal error catched while fetching server module data. This means package preset, server group or server module doesnt exists. Error dump: '.nl2br($e));
			$xtpl->parse('main.attention');
		}
		for($i=0;$i<count($pkgs);$i++){
			$xtpl->assign('PKG', $pkgs[$i]);
			if($orderdata['productid'] == $pkgs[$i]['id']){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			$xtpl->parse('main.editorder.order.inputpkg');
		}
		if(is_array($modules) && count($modules) > 0){
			for($i=0;$i<count($modules);$i++){
				//for($n=0;$n<count($gms);$n++){
					
				//}
				if(@array_key_exists($modules[$i]['name'], @$gms)){
						$modules[$i]['value'] = $gms[$modules[$i]['name']];
					}
				$xtpl->assign('INPUT', $modules[$i]);
				$xtpl->parse('main.editorder.order.inputtext');
			}
		}
		for($i=0;$i<count($servers);$i++){
			$xtpl->assign('SRV', $servers[$i]);
			if($orderdata['serverid'] == $servers[$i]['ServerID']){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			$xtpl->parse('main.editorder.server');
		}
		
		$xtpl->parse('main.editorder.order');
		$xtpl->parse('main.editorder');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditInvoice($invid){
		$xtpl = self::$xtpl;
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('MANAGEINVCURR', 'current');
		$order = Order::getInstance();
		$invoice = Invoice::getInstance();
		$user = User::getInstance();
		$time = Time::getInstance();
		$gm = GatewayModule::getInstance();
		$gmodules = $gm->GetButch();
		$setting = Settings::getInstance();
		$defgw = $setting->Get('system.paygateway.default');
		$invdata = $invoice->FetchData($invid);
		for($i=0;$i<count($gmodules);$i++){
			$prov = call_user_func(array($gmodules[$i]['modulename']."PaymentGateway",'getInstance'));
			if($defgw == $gmodules[$i]['id']){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			$xtpl->assign('GM', $gmodules[$i]);
			$ginfo = $prov->Info();
			$xtpl->assign('GNAME', $ginfo['name']);
			$xtpl->parse('main.editinv.paygw');
		}
		if($invdata['status'] == 'Paid' && $invdata['transactionid'] != '-1'){
			$trans = Transaction::getInstance();
			$xtpl->assign('TRANS', $trans->FetchData($invdata['transactionid']));
			$xtpl->parse('main.editinv.invoice.transaction');
		}
		$xtpl->assign('CURRDATE', $time->UtoM(time()));
		$xtpl->assign('Status'.$invdata['status'], 'selected="selected"');
		$xtpl->assign('USER', $user->FetchData($invdata['accountid']));
		$xtpl->assign('ORDER', $order->FetchData($invdata['orderid']));
		$xtpl->assign('INV', $invdata);
		$xtpl->parse('main.editinv.invoice');
		$xtpl->parse('main.editinv');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageTrans(){
		$xtpl = self::$xtpl;
		$user = User::getInstance();
		$gm = GatewayModule::getInstance();
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('MANAGETRANS', 'current');
		$trans = Transaction::getInstance();
		$transbutch = $trans->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		if(count($transbutch) > 0){
			for($i=0;$i<count($transbutch);$i++){
				$username = '';
				$gatewayname = '';
				if(!is_numeric($transbutch[$i]['customerid']) || !is_string($username = $user->GetUsername($transbutch[$i]['customerid']))){
					self::add_message($xtpl, 'attention', 'User #'.$transbutch[$i]['customerid'].' not found for transaction #'.$transbutch[$i]['id']);
				} else {
					$xtpl->assign('USERNAME', $username);
				}
				if(!is_numeric($transbutch[$i]['gatewayid']) || !is_string($gatewayname = $gm->GetName($transbutch[$i]['gatewayid']))){
					self::add_message($xtpl, 'attention', 'Payment gateway #'.$transbutch[$i]['gatewayid'].' not found for transaction #'.$transbutch[$i]['id']);
				} else {
					$xtpl->assign('GATEWAYNAME', $gatewayname);
				}
				$xtpl->assign('TRANS', $transbutch[$i]);
				$xtpl->parse('main.managetrans.transtable.transrow');
			}
			for($i=1;$i<=self::count_pages($trans->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.managetrans.transtable.page');
			}
			$xtpl->parse('main.managetrans.transtable');
		} else {
			$xtpl->parse('main.managetrans.transinfo');
		}
		$xtpl->parse('main.managetrans');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditServer($serverid){
		$xtpl = self::$xtpl;
		$xtpl->assign('SERVERSCURR', 'current');
		$xtpl->assign('SSERVERMANAGECURR', 'current');
		$server = Server::getInstance();
		$sg = ServerGroups::getInstance();
		$sm = ServerModule::getInstance();
		if(!$serverid || !is_numeric($serverid)){
			$xtpl->parse('main.editserver.gwerror');
		} else {
			$serverdata = $server->FetchData($serverid);
			
			$xtpl->assign('Status'.$serverdata['status'], 'selected="selected"');
			$sgdata = $sg->FetchData($serverdata['servergroupid']);
			$sm->id = $sgdata['moduleid'];
			$sa = unserialize($serverdata['accessdata']);
			$op_array = $sm->getOperateArray();
			
			if(is_array($op_array) && count($op_array) > 0){
				for($i=0;$i<count($op_array);$i++){
					//for($n=0;$n<count($sa);$n++){
						if(@array_key_exists($op_array[$i]['name'], @$sa)){
							$op_array[$i]['value'] = $sa[$op_array[$i]['name']];
						}
					//}
					$xtpl->assign('INPUT', $op_array[$i]);
					$xtpl->parse('main.editserver.server.inputtext');
				}
			}
			$xtpl->assign('SRV', $serverdata);
			$xtpl->parse('main.editserver.server');
		}
		$xtpl->parse('main.editserver');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditCron(){
		$xtpl = self::$xtpl;
		$xtpl->assign('SETCURR', 'current');
		$xtpl->assign('CRONCURR', 'current');
		$setting = Settings::getInstance();
		$ini = ini_manager::getInstance();
		$xtpl->assign('INTCRON', 'php -q '.$ini->get_entry('system', 'path').'/cron.php');
		$xtpl->assign('EXTCRON', 'wget -O /dev/null http://'.$_SERVER["HTTP_HOST"].'/cron.php');
		$xtpl->assign('DAYSTONEWINV', $setting->Get('system.cron.daystonewinv'));
		$xtpl->assign('TERMDAYS', $setting->Get('system.cron.daystoterminate'));
		$xtpl->assign('SUSPDAYS', $setting->Get('system.cron.daystosuspend'));
		$xtpl->assign('AUTOSUSP'.$setting->Get('system.cron.autosuspend'), 'checked');
		$xtpl->assign('AUTOTERM'.$setting->Get('system.cron.autoterminate'), 'checked');
		$xtpl->parse('main.editcron.cron');
		$xtpl->parse('main.editcron');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditPkg($pkgid){
		$xtpl = self::$xtpl;
		$pkg = Package::getInstance();
		$preset = Preset::getInstance();
		$xtpl->assign('PRODUCTSCURR', 'current');
		$xtpl->assign('PKGMANAGECURR', 'current');
		if(!is_numeric($pkgid)){
			$xtpl->assign('ATTENTIONMSG', 'Package ID is not set');
			$xtpl->parse('main.attention');
		} else {
			$pkgdata = $pkg->FetchData($pkgid);
			$presets = $preset->GetButch('',"`status` = '1'");
			for($i=0;$i<count($presets);$i++){
				if($pkgdata['presetid'] == $presets[$i]['id']){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->assign('PRESET', $presets[$i]);
				$xtpl->parse('main.editpkg.preset');
			}
			$xtpl->assign('PKG', $pkgdata);
			$xtpl->assign('DEF'.$pkgdata['paytype'], 'selected="selected"');
			$xtpl->parse('main.editpkg');
		}
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function GenerateInvoices(){
		$order = Order::getInstance();
		$xtpl = self::$xtpl;
		$result = $order->generateLastInv();
		if(count($result) < 1){
			$xtpl->parse('main.geninvoices.gwerror');
		} else {
			$xtpl->assign('TOTAL', count($result));
			for($i=0;$i<count($result);$i++){
				$xtpl->assign('RESULT', $result[$i]);
				$xtpl->parse('main.geninvoices.results.list');
			}
			$xtpl->parse('main.geninvoices.results');
		}
		$xtpl->parse('main.geninvoices');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ManageDepartments(){
		$xtpl = self::$xtpl;
		if(!is_numeric(self::$page)){
			$page = 1;
		} else {
			$page = self::$page;
		}
		$xtpl->assign('SUPCURR', 'current');
		$xtpl->assign('DEPSCURR', 'current');
		$dep = Department::getInstance();
		$deps = $dep->GetButch(self::$per_page, 1, 'id', 'DESC', self::$per_page*$page-self::$per_page);
		if(count($deps) < 1){
			$xtpl->parse('main.managedepartments.depsinfo');
		} else {
			for($i=0;$i<count($deps);$i++){
				$xtpl->assign('DEP',$deps[$i]);
				$xtpl->parse('main.managedepartments.depstable.deprow');
			}
			for($i=1;$i<=self::count_pages($dep->Calculate());$i++){
				if($page == $i){
					$xtpl->assign('CURRENT', 'current');
				} else {
					$xtpl->assign('CURRENT', '');
				}
				$xtpl->assign('NUM', $i);
				if(preg_match('/page=[0-9]+/', self::$request_uri)){
					$link = preg_replace('/page=[0-9]+/', 'page='.$i, self::$request_uri);
				} else {
					$link = self::$request_uri.'&page='.$i;
				}
				
				$xtpl->assign('LINK', $link);
				$xtpl->parse('main.managedepartments.depstable.page');
			}
			$xtpl->parse('main.managedepartments.depstable');
		}
		$xtpl->parse('main.managedepartments');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function CreateDepartment(){
		$xtpl = self::$xtpl;
		$xtpl->assign('SUPCURR', 'current');
		$xtpl->assign('DEPSCURR', 'current');
		$xtpl->parse('main.adddep');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditDepartment($id){
		$xtpl = self::$xtpl;
		$xtpl->assign('SUPCURR', 'current');
		$xtpl->assign('DEPSCURR', 'current');
		$dep = Department::getInstance();
		if(!is_numeric($id)){
			$xtpl->parse('main.editdep.depinfo');
		} else {
			$depdata = $dep->FetchData($id);
			$xtpl->assign('DEP', $depdata);
			$xtpl->assign('DEF'.$depdata['type'], 'selected="selected"');
			$xtpl->parse('main.editdep.depbox');
		}
		$xtpl->parse('main.editdep');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserDashboard(){
		$xtpl = self::$xtpl;
		$user = User::getInstance();
		$profile = Profile::getInstance();
		$order = Order::getInstance();
		$inv = Invoice::getInstance();
		$pkg = Package::getInstance();
		$ticket = Ticket::getInstance();
		$curr = Currency::getInstance();
		$xtpl->assign('DASHCURR', 'current');
		
		if(self::$userid != NULL){
			$profid = $profile->GetID(self::$userid,'userid');
			if(!$profid){
				$xtpl->assign('ATTENTIONMSG', 'Warning! Your account has no profile! Please, set one');
				$xtpl->parse('main.attention');
			} else {
				$profdata = $profile->FetchData($profid);
				$xtpl->assign('PROFILE', $profdata);
			}
			$orderscnt = $order->Calculate('`accountid` = "'.self::$userid.'"');
			if($orderscnt == '' || !$orderscnt){
				$orderscnt = 0;
			}
			$xtpl->assign('ORDERSCNT', $orderscnt);
			$xtpl->assign('INVCNT', $inv->Calculate('`accountid` = "'.self::$userid.'"'));
			$xtpl->assign('UINVCNT', $inv->Calculate('`accountid` = "'.self::$userid.'" AND `status` = "Unpaid"'));
			if($orderscnt < 1){
				$xtpl->parse('main.dashboard.ordersinfo');
			} else {
				$orders = $order->GetButch(5,'`accountid` = "'.self::$userid.'"');
				for($i=0;$i<count($orders);$i++){
					$xtpl->assign('ORDER',$orders[$i]);
					$xtpl->assign('PKGNAME', $pkg->GetName($orders[$i]['productid']));
					$xtpl->parse('main.dashboard.orderstable.orderrow');
				}
				$xtpl->parse('main.dashboard.orderstable');
			}
			$invs = $inv->GetButch(5,'`accountid` = "'.self::$userid.'" AND `status` = "Unpaid"');
			$tickets = $ticket->GetButch(5,'`userid` = "'.self::$userid.'" AND `status` != "Closed"');
			if(count($tickets) < 1){
				$xtpl->parse('main.dashboard.ticketsinfo');
			} else {
				for($i=0;$i<count($tickets);$i++){
					$xtpl->assign('TICKET', $tickets[$i]);
					$xtpl->parse('main.dashboard.ticketstable.ticketsrow');
				}
				$xtpl->parse('main.dashboard.ticketstable');
			}
			if(count($invs) < 1){
				$xtpl->parse('main.dashboard.invsinfo');
			} else {
				for($i=0;$i<count($invs);$i++){
					$xtpl->assign('AMOUNT', $curr->FormatCurrency($invs[$i]['amount']));
					$xtpl->assign('INV', $invs[$i]);
					$xtpl->parse('main.dashboard.invstable.invsrow');
				}
				$xtpl->parse('main.dashboard.invstable');
			}
			$xtpl->parse('main.dashboard');
		}
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserProfile(){
		$xtpl = self::$xtpl;
		$cy = Country::getInstance();
		$user = User::getInstance();
		$profile = Profile::getInstance();
		$xtpl->assign('SETCURR', 'current');
		$xtpl->assign('CURRPROFILE', 'current');
		if(self::$userid == NULL){
			$xtpl->assign('ATTENTIONMSG', 'User ID is not set');
			$xtpl->parse('main.attention');
		} else {
			if($profid = $profile->GetID(self::$userid, 'userid')){
				$pe = $profile->FetchData($profid);
				$xtpl->assign('PROFILE', $pe);
				$xtpl->assign('DEFAULTS'.@$pe['sex'], 'selected="selected"');
			}
			
			
			foreach($cy->Listing() as $key => $value){
				if(@$pe['country'] == $key){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->assign('ID', $key);
				$xtpl->assign('COUNTRYNAME', $value);
				$xtpl->parse('main.editprofile.countrylist');
			}
			$xtpl->parse('main.editprofile');
		}
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserOrderPkg($pkgid=''){
		$xtpl = self::$xtpl;
		$pkg = Package::getInstance();
		$pkgs = $pkg->GetOrderable();
		if(count($pkgs) < 1){
			$xtpl->assign('ATTENTIONMSG', 'There is no orderable packages');
			$xtpl->parse('main.attention');
		} else {
			for($i=0;$i<count($pkgs);$i++){
				$xtpl->assign('PKGR', $pkgs[$i]);
				if($pkgid == $pkgs[$i]['id']){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->parse('main.orderpkg.pkgrow');
			}
			if(is_numeric($pkgid)){
				$pkgdata = $pkg->FetchData($pkgid);
				$xtpl->assign('PKG',$pkgdata);
				switch($pkgdata['paytype']){
					case 'Free':
						$xtpl->assign('PRICE', 'Free');
						$xtpl->assign('CYCLEDISABLED', "disabled");
					break;
					case 'Onetime':
						$xtpl->assign('PRICE', $pkgdata['price']);
						$xtpl->assign('CYCLEDISABLED', "disabled");
					case 'Recurring':
						$xtpl->assign('PRICE', $pkgdata['price']);
					break;
				}
				$xtpl->parse('main.orderpkg.pkgorder');
			}
			$xtpl->parse('main.orderpkg');
		}
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserOrderPlaced($orderid){
		$xtpl = self::$xtpl;
		$xtpl->assign('ORDERSCURR', 'current');
		$order = Order::getInstance();
		$pkg = Package::getInstance();
		$inv = Invoice::getInstance();
		$orderdata = $order->FetchData($orderid);
		$invdata = $inv->FetchData($orderdata['lastinv']);
		$pkgdata = $pkg->FetchData($orderdata['productid']);
		$xtpl->assign('PKG', $pkgdata);
		$xtpl->assign('INV', $invdata);
		$xtpl->assign('ORDER',$orderdata);
		$xtpl->parse('main.orderplaced');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserManageOrders(){
		$xtpl = self::$xtpl;
		$order = Order::getInstance();
		$pkg = Package::getInstance();
		$xtpl->assign('ORDERSCURR', 'current');
		$xtpl->assign('MANAGEORDERSCURR', 'current');
		if(self::$userid == NULL){
			$xtpl->assign('ATTENTIONMSG', 'User ID is not set');
			$xtpl->parse('main.attention');
		} else {
			$orders = $order->GetButch('',self::$userid);
			if(count($orders) < 1){
				$xtpl->parse('main.manageorders.ordersinfo');
			} else {
				for($i=0;$i<count($orders);$i++){
					$xtpl->assign('PKGNAME',$pkg->GetName($orders[$i]['productid']));
					$xtpl->assign('ORDER',$orders[$i]);
					$xtpl->parse('main.manageorders.orderstable.ordersrow');
				}
				$xtpl->parse('main.manageorders.orderstable');
			}
			$xtpl->parse('main.manageorders');
		}
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserManageInvoices(){
		$xtpl = self::$xtpl;
		$inv = Invoice::getInstance();
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('INVSCURR', 'current');
		$invs = $inv->GetButch('',self::$userid,'Unpaid');
		if(count($invs) < 1){
			$xtpl->parse('main.manageinvs.invsinfo');
		} else {
			for($i=0;$i<count($invs);$i++){
				$xtpl->assign('INV', $invs[$i]);
				$xtpl->parse('main.manageinvs.invstable.invrow');
			}
			$xtpl->parse('main.manageinvs.invstable');
		}
		$xtpl->parse('main.manageinvs');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserManageTickets(){
		$xtpl = self::$xtpl;
		$xtpl->assign('SUPPCURR', 'current');
		$xtpl->assign('TICKETSSCURR', 'current');
		$ticket = Ticket::getInstance();
		$dep = Department::getInstance();
		$tickets = $ticket->GetButch(15,'`userid` = "'.self::$userid.'" AND `status` != "Closed"', 'id', 'DESC');
		if(count($tickets) < 1){
			$xtpl->parse('main.managetickets.ticketsinfo');
		} else {
			for($i=0;$i<count($tickets);$i++){
				$xtpl->assign('DEPNAME', $dep->GetName($tickets[$i]['depid']));
				$xtpl->assign('TICKET', $tickets[$i]);
				$xtpl->parse('main.managetickets.ticketstable.ticketsrow');
			}
			$xtpl->parse('main.managetickets.ticketstable');
		}
		
		$xtpl->parse('main.managetickets');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserAddTicket(){
		$xtpl = self::$xtpl;
		$dep = Department::getInstance();
		$xtpl->assign('SUPPCURR', 'current');
		$xtpl->assign('NEWTCURR', 'current');
		$deps = $dep->GetButch("",'`type` = "Public"');
		if(count($deps) < 1){
			$xtpl->parse('main.addticket.depinfo');
		} else {
			for($i=0;$i<count($deps);$i++){
				$xtpl->assign('DEP', $deps[$i]);
				$xtpl->parse('main.addticket.ticket.deprow');
			}
			$xtpl->parse('main.addticket.ticket');
		}
		$xtpl->parse('main.addticket');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserViewTicket($tid){
		$xtpl = self::$xtpl;
		$xtpl->assign('SUPPCURR', 'current');
		$xtpl->assign('TICKETSSCURR', 'current');
		$tc = TicketChange::getInstance();
		$user = User::getInstance();
		$ticket = Ticket::getInstance();
		$ticdata = $ticket->FetchData($tid);
		if(!is_numeric($tid) || !$ticdata){
			$xtpl->parse('main.viewticket.ticketrinfo');
		} elseif($ticdata['userid'] != self::$userid){
			$xtpl->parse('main.viewticket.ticketrights');
		} else {
			$xtpl->assign('TID',$tid);
			$xtpl->assign('DEF'.$ticdata['status'], 'selected="selected"');
			$tcs = $tc->GetButch('','`ticketid` = "'.$tid.'"');
			for($i=0;$i<count($tcs);$i++){
				$user->id = $tcs[$i]['userid'];
				$xtpl->assign('USERNAME', $user->GetUsername());
				$xtpl->assign('TC', $tcs[$i]);
				$xtpl->parse('main.viewticket.ticket.ticket'.$tcs[$i]['type']);
			}
			$xtpl->parse('main.viewticket.ticket');
		}
		$xtpl->parse('main.viewticket');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserViewOrder($orderid){
		$xtpl = self::$xtpl;
		$order = Order::getInstance();
		$xtpl->assign('ORDERSCURR', 'current');
		$xtpl->assign('MANAGEORDERSCURR', 'current');
		if(!is_numeric($orderid)){
			$xtpl->parse('main.vieworder.ordererror');
		} else {
			$orderdata = $order->FetchData($orderid);
			if($orderdata['accountid'] != self::$userid){
				$xtpl->parse('main.vieworder.orderrights');
			} else {
				$inv = Invoice::getInstance();
				$pkg = Package::getInstance();
				$xtpl->assign('LASTINV',$inv->FetchData($orderdata['lastinv']));
				$xtpl->assign('PKGNAME',$pkg->GetName($orderdata['productid']));
				$xtpl->assign('ORDER',$orderdata);
				$xtpl->parse('main.vieworder.order');
			}
		}
		$xtpl->parse('main.vieworder');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserViewInvoice($invid){
		$xtpl = self::$xtpl;
		$invoice = Invoice::getInstance();
		$xtpl->assign('FINCURR', 'current');
		$xtpl->assign('INVSCURR', 'current');
		if(!is_numeric($invid)){
			$xtpl->parse('main.viewinvoice.inverror');
		} else {
			$invdata = $invoice->FetchData($invid);
			if($invdata['accountid'] != self::$userid){
				$xtpl->parse('main.viewinvoice.invrights');
			} else {
				$order = Order::getInstance();
				$pkg = Package::getInstance();
				$orderdata = $order->FetchData($invdata['orderid']);
				$pkgdata = $pkg->FetchData($orderdata['productid']);
				$xtpl->assign('INV',$invdata);
				$xtpl->assign('PKG',$pkgdata);
				$xtpl->assign('ORDER',$orderdata);
				if($invdata['status'] == 'Paid'){
					$xtpl->parse('main.viewinvoice.invoice.paid');
					
				}
				$xtpl->parse('main.viewinvoice.invoice');
			}
		}
		$xtpl->parse('main.viewinvoice');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function UserPersonalSettings(){
		$xtpl = self::$xtpl;
		$nm = NotificationModule::getInstance();
		$lang = Lang::getInstance();
		$setting = Settings::getInstance();
		$usrstg = UserSettings::getInstance();
		$curr = Currency::getInstance();
		$xtpl->assign('SETCURR', 'current');
		$xtpl->assign('SYSTEMCURRSETT', 'current');
		
		$nms = $nm->GetButch();
		$langs = $lang->GetButch();

		$currs = $curr->GetButch();
		if($usersettings = $usrstg->Get(self::$userid)){
			$xtpl->assign('NOTIFYADDRESS', $usersettings['notifyaddress']);
			if($usersettings['usernewinvoice'] == "1"){
				$xtpl->assign('NEWINVSEL', 'checked="checked"');
			}
			if($usersettings['userneworder'] == "1"){
				$xtpl->assign('NEWORDSEL', 'checked="checked"');
			}
			if($usersettings['usernewticket'] == "1"){
				$xtpl->assign('NEWTCSEL', 'checked="checked"');
			}
			if($usersettings['usernewticketreply'] == "1"){
				$xtpl->assign('NEWTRSEL', 'checked="checked"');
			}
		}
		
		for($i=0;$i<count($nms);$i++){
			if($nms[$i]['id'] == @$usersettings['notifymodule'] || $nms[$i]['id'] == $setting->Get('system.notifymodule.default')){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			$xtpl->assign('NM', $nms[$i]);
			$xtpl->parse('main.personalsettings.ntlist');
		}
		for($i=0;$i<count($langs);$i++){
			if($langs[$i]['code'] == @$usersettings['language'] || $langs[$i]['code'] == $setting->Get('system.lang.default')){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			//var_dump($langs[$i]);
			$xtpl->assign('LNG', $langs[$i]);
			$xtpl->parse('main.personalsettings.langlist');
		}
		for($i=0;$i<count($currs);$i++){
			if($currs[$i]['name'] == @$usersettings['currency'] || $currs[$i]['name'] == $setting->Get('system.currency')){
				$xtpl->assign('DEFAULT', 'selected="selected"');
			} else {
				$xtpl->assign('DEFAULT', '');
			}
			$xtpl->assign('CURR',$currs[$i]);
			$xtpl->parse('main.personalsettings.currlist');
		}
		$xtpl->parse('main.personalsettings');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function EditCustomer($userid){
		$xtpl = self::$xtpl;
		$user = User::getInstance();
		$userdata = $user->FetchData($userid);
		$xtpl->assign('CUSTCURR', 'current');
		$xtpl->assign('CUSTMANAGECURR', 'current');
		$xtpl->assign('DEF'.$userdata['status'], 'selected="selected"');
		$xtpl->assign('CUST',$userdata);
		
		$xtpl->parse('main.editcustomer');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function ListProducts(){
		$xtpl = self::$xtpl;
		$product = Package::getInstance();
		$orders = $product->GetOrderable();
		if(count($orders) < 1){
			$xtpl->parse('main.productlist.productinfo');
		} else {
			for($i=0;$i<count($orders);$i++){
				$xtpl->assign('PKG',$orders[$i]);
				$xtpl->parse('main.productlist.product');
			}			
		}
		$xtpl->parse('main.productlist');
		$xtpl->parse('main');
		$xtpl->out('main');
		
		
	}
	public static function OrderPkg($pkgid){
		$xtpl = self::$xtpl;
		$product = Package::getInstance();
		$products = $product->GetOrderable();
		if(count($products) < 1){
			
		} else {
			for($i=0;$i<count($products);$i++){
				if($products[$i]['id'] == $pkgid){
					$xtpl->assign('DEFAULT', 'selected="selected"');
				} else {
					$xtpl->assign('DEFAULT', '');
				}
				$xtpl->assign('PKGR',$products[$i]);
				$xtpl->parse('main.orderpkg.pkgrow');
			}
		}
		if($pkgdata = $product->FetchData($pkgid)){
			$xtpl->assign('PKG',$pkgdata);
			switch($pkgdata['paytype']){
				case 'Free':
					$xtpl->assign('PRICE', 'Free');
					$xtpl->assign('CYCLEDISABLED', "disabled");
				break;
				case 'Onetime':
					$xtpl->assign('PRICE', $pkgdata['price']);
					$xtpl->assign('CYCLEDISABLED', "disabled");
				case 'Recurring':
					$xtpl->assign('PRICE', $pkgdata['price']);
				break;
			}
			$xtpl->parse('main.orderpkg.pkgorder');
		} else {
			$xtpl->assign('ID', $pkgid);
			$xtpl->parse('main.orderpkg.pkginfo');
		}
		$xtpl->parse('main.orderpkg');
		$xtpl->parse('main');
		$xtpl->out('main');
		
	}
	public static function RegForm(){
		$xtpl = self::$xtpl;
		$xtpl->parse('main.regform');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
	public static function RegSuccess(){
		$xtpl = self::$xtpl;
		$xtpl->parse('main.regsuccess');
		$xtpl->parse('main');
		$xtpl->out('main');
	}
}
?>
