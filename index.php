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

require_once('core.php');
$userinstance = User::getInstance();
//Logout: clear cookies and all request data
if(@$_REQUEST['logout'] == 'true'){
	setcookie('loginusername', '');
	setcookie('loginpassword', '');
	unset($_REQUEST);
}
//Block 1: Authorization
Page::init('login');
if(!isset($_REQUEST['loginusername']) || !isset($_REQUEST['loginpassword']) || strlen($_REQUEST['loginusername']) < 2 || strlen($_REQUEST['loginpassword']) < 2){
	Page::Login();
	exit;
} else {
	$authstatus = false;
	$auth = new Auth(trim($_REQUEST['loginusername']), trim($_REQUEST['loginpassword']));
	try {
		$authstatus = $auth->check_auth();
	} catch (Exception $e){
		if(iDEBUG){
			$msg = nl2br($e);
		} else {
			$msg = $e->getMessage();
		}
		Page::$messages[] = array('type' => 'attention', 'message' => $msg);
		
	}	
	if(!$authstatus){
		setcookie('loginusername', '');
		setcookie('loginpassword', '');
		unset($_REQUEST);
		if(count(Page::$messages) < 1){
			Page::$messages[] = array('type' => 'attention', 'message' => 'Login or password is incorrect');
		}
		Page::message();
		Page::Login();
		exit;
		//показать авторизацию и сообщение об ошибке
	} else {			//выставить кукисы
		//	
	//exit;
		setcookie('loginusername', $auth->username);
		setcookie('loginpassword', $auth->password);
	}
	$userinstance->username = trim(@$_REQUEST['username']);
}
$auth = new Auth(trim($_REQUEST['loginusername']), trim($_REQUEST['loginpassword']));
//var_dump($userinstance->GetID('username',$auth->username));
Page::$userid = $userinstance->GetID($auth->username, 'username');
Page::$query_string = $_SERVER["QUERY_STRING"];
Page::$request_uri = $_SERVER["REQUEST_URI"];
Page::$page = @$_GET['page'];

//Block 2: Dashboard
if($action == '' && $object == '' && $auth->check_auth() && $auth->get_rights() == 'Admin'){
	Page::init('admin');
        Page::setup(Lang::$langarray['dashboard'],Lang::$langarray['pleasedtomeetyou']);
	Page::Dashboard();
	exit;
} elseif($action == '' && $object == '' && $auth->check_auth() && $auth->get_rights() == 'Active'){
	//show user's dashboard
	Page::init('user');
	if(is_numeric(@$_COOKIE['temporderid'])){
		//creating new order from temprary one
		$temporder = TempOrder::getInstance();
		$temporderarray = $temporder->FetchData($_COOKIE['temporderid']);
		if(count($temporderarray) > 1){
			setcookie('temporderid', '');
			$temporderdata = unserialize($temporderarray['data']);
			$order = Order::getInstance();
			$orderid = $order->Create(Page::$userid, $temporderdata['pkgid'], 'new', '','','','Pending',$temporderdata['cycle']);
			Page::UserOrderPlaced($orderid);
		}
	
	} else {
		Page::UserDashboard();
	}
	exit;
}

//Block 3: Show Object
if($action == '' && $object != '' && $auth->check_auth() && $auth->get_rights() == 'Admin'){
	Page::init('admin');
	switch($object){
		case 'addcustomer':
                        Page::setup(Lang::$langarray['createnewcustomer']);
			Page::AddCustomer();
		break;
		case 'managecustomers':
                        Page::setup(Lang::$langarray['listcostumers']);
			Page::ManageCustomers();
		break;
		case 'managecustomer':
                        Page::setup(Lang::$langarray['edituser']);
			if(!is_string($_REQUEST['username']) && !is_numeric($_REQUEST['userid'])){
				Page::$messages[] = 'Username and userid is not set';
			} else {
				Page::ManageCustomer($_REQUEST['username'], $_REQUEST['userid']);
			}
		break;
		case 'manageservermodules':
                        Page::setup(Lang::$langarray['servermodules']);
			Page::ManageServerModules();
			exit;
		break;
		case 'manageservergroups':
			Page::setup(Lang::$langarray['servergroups']);
                        Page::ManageServerGroups();
		break;
		case 'manageservers':
                        Page::setup(Lang::$langarray['listservers']);
			Page::Servers();
		break;
		case 'addservergroup':
                        Page::setup(Lang::$langarray['addnewgroupofservers']);
			Page::AddServerGroup();
			exit;
		break;
		case 'addserverstep1':
                        Page::setup(Lang::$langarray['addnewserverstepone']);
			Page::AddServerStep1();
		break;
		case 'managepres':
                        Page::setup(Lang::$langarray['addnewserverstepone']);
			Page::Presets();
		break;
		case 'addprestep1':
                        Page::setup(Lang::$langarray['addnewpresetstep1']);
			Page::AddPreStep1();
		break;
		case 'managepkgs':
                        Page::setup(Lang::$langarray['listproducts']);
			Page::Package();
		break;
		case 'addpkg':
                        Page::setup(Lang::$langarray['createnewproduct']);
			Page::AddPkg();
		break;
		case 'addorder':
                        Page::setup(Lang::$langarray['createneworder']);
			Page::AddOrder();
		break;
		case 'manageorders':
                        Page::setup(Lang::$langarray['listorders']);
			Page::ManageOrders();
		break;
		case 'addinv':
                        Page::setup(Lang::$langarray['sendinvoice']);
			Page::AddInvoice();
		break;
		case 'manageinvs':
                        Page::setup(Lang::$langarray['listinvoices']);
			Page::ManageInvoices();
		break;
		case 'managegatewaymodules':
                        Page::setup(Lang::$langarray['paymentgateways']);
			Page::ManageGatewayModules();
		break;
		case 'generalsettings':
                        Page::setup(Lang::$langarray['generalsettings']);
			Page::GeneralSettings();
		break;
		case 'editgateway':
                        Page::setup(Lang::$langarray['setuppaygateway']);
			Page::EditGateway($_POST['modulename']);
		break;
		case 'editorder':
                        Page::setup(Lang::$langarray['editorder']);
			Page::EditOrder($_REQUEST['orderid']);
		break;
		case 'editinv':
			Page::setup(Lang::$langarray['editinvoice']);
			Page::EditInvoice($_REQUEST['invid']);
		break;
		case 'managetrans':
			Page::setup(Lang::$langarray['listtransactions']);
			Page::ManageTrans();
		break;
		case 'editserver':
			Page::setup(Lang::$langarray['editserver']);
			Page::EditServer($_REQUEST['serverid']);
		break;
		case 'geninvoices':
			Page::setup(Lang::$langarray['processingorders']);
			Page::GenerateInvoices();
		break;
		case 'editcron':
			Page::setup(Lang::$langarray['cronsettings']);
			Page::EditCron();
		break;
		case 'editpkg':
			Page::setup(Lang::$langarray['editpackage']);
			Page::EditPkg($_REQUEST['pkgid']);
		break;
		case 'managedepartments':
			Page::setup(Lang::$langarray['supportdepartments']);
			Page::ManageDepartments();
		break;
		case 'adddep':
			Page::setup(Lang::$langarray['cretenewdep']);
			Page::CreateDepartment();
		break;
		case 'editdep':
			Page::setup(Lang::$langarray['editdepartemnt']);
			Page::EditDepartment($_REQUEST['id']);
		break;
		case 'managetickets':
			Page::setup(Lang::$langarray['tickets']);
			if(preg_match('/^(Support|Customer|Hold|Closed|Progress)$/i', $_REQUEST['where'])){
				Page::$select_where = '`status` = "'.$_REQUEST['where'].'"';
			}
			Page::ManageTickets();
		break;
		case 'managecurrencies':
			Page::setup(Lang::$langarray['currencies']);
			Page::ManageCurrencies();
		break;
		case 'managenotifies':
			Page::setup(Lang::$langarray['notifications']);
			Page::ManageNotifies();
		break;
		case 'managenotifytemplates':
			Page::setup(Lang::$langarray['templates']);
			Page::ManageNotifyTemplates();
		break;
		case 'viewticket':
			Page::setup(Lang::$langarray['overviewticket']);
			Page::ViewTicket($_REQUEST['ticketid']);
		break;
		case 'editcurr':
			Page::setup(Lang::$langarray['editcurrency']);
			Page::EditCurrency($_REQUEST['curid']);
		break;
		case 'addcurr':
			Page::setup(Lang::$langarray['addnewcurrency']);
			Page::AddCurrency();
		break;
		case 'addnotifytemplate':
			Page::setup(Lang::$langarray['createnewnt']);
			Page::AddNotifyTemplate();
		break;
		case 'editnotifytemplate':
			Page::setup(Lang::$langarray['editnt']);
			Page::EditNotifyTemplate($_REQUEST['ntid']);
		break;
		case 'managenotifymodules':
			Page::setup(Lang::$langarray['notifymodules']);
			Page::ManageNotifyModules();
		break;
		case 'editntmodule':
			Page::setup(Lang::$langarray['editntmodule']);
			Page::EditNotifyModule($_REQUEST['moduleid']);
		break;
		case 'personalsettings':
			Page::setup(Lang::$langarray['settings']);
			Page::PersonalSettings();
		break;
		case 'sendmessage':
			Page::setup(Lang::$langarray['sendmessage']);
			Page::SendMessage();
		break;
		case 'editcustomer':
			Page::setup(Lang::$langarray['editcustomer']);
			Page::EditCustomer($_REQUEST['customerid']);
		break;
		case 'editpreset':
			Page::setup(Lang::$langarray['editpreset']);
			Page::EditPreset($_REQUEST['presetid']);
		break;
		case 'editservergroup':
			Page::setup(Lang::$langarray['editserversgroup']);
			Page::EditServerGroup($_REQUEST['id']);
		break;
	}
} elseif($action == '' && $object != '' && $auth->check_auth() && $auth->get_rights() == 'Active'){

	Page::init('user');
//Show user's object
	switch($object){
		case 'editprofile':
			Page::UserProfile();
		break;
		case 'orderpkg':
			Page::UserOrderPkg(@$_REQUEST['pkgid']);
		break;
		case 'manageorders':
			Page::UserManageOrders();
		break;
		case 'managetickets':
			Page::UserManageTickets();
		break;
		case 'addticket':
			Page::UserAddTicket();
		break;
		case 'viewticket':
			Page::UserViewTicket($_REQUEST['ticketid']);
		break;
		case 'vieworder':
			Page::UserViewOrder($_REQUEST['orderid']);
		break;
		case 'viewinvoice':
			Page::UserViewInvoice($_REQUEST['invid']);
		break;
		case 'manageinvs':
			Page::UserManageInvoices();
		break;
		case 'perssystemsettings':
			Page::UserPersonalSettings();
		break;
	}
}
//Block 4: Take action and show object
if($action != '' && $auth->check_auth() && $auth->get_rights() == 'Admin'){
	//Page::$userid = $userinstance->GetID();
	Page::init('admin');
	switch($action){
		case 'addcustomer':
			$user = User::getInstance();
			$_POST['password'] = md5($_POST['password']);
			try {
				if($user->Create($_POST)){
					Page::$messages[] = array('type' => 'success', 'message' => 'Customer successfully created');
				}
			} catch (Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched while creating new customer: '.$e);
			}
			Page::message();
			Page::ManageCustomers();
		break;
		case 'activateservermodule':
			$sm = ServerModule::getInstance();
			//$sm->name = $_POST['modulename'];
			$sm->Create($_POST);
			Page::ManageServerModules();
			exit;
		break;
		case 'disactivateservermodule':
			$sm = ServerModule::getInstance();
			try {
				$sm->Delete($_REQUEST['moduleid']);
			} catch (Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched while creating new customer: '.$e);
				Page::message();
			}
			
			Page::ManageServerModules();
			exit;
		break;
		case 'addservergroup':
			$sg = new ServerGroups();
			try {
				$sg->Create($_POST);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched during group creation. Error dump: '.nl2br($e));
				Page::message();
			}
			Page::ManageServerGroups();
		break;
		case 'addserverstep2':
			Page::AddServerStep2($_POST['servergroup']);
		break;
		case 'addserverstep3':
			$server = Server::getInstance();
			$server->moduleid = $_POST['moduleid'];
			$server->Create($_POST['servername'], $_POST['groupid'], $_POST['maxclients'], 1 , $server->generateOperateArray($_POST));
			Page::Servers();
		break;
		case 'addprestep2':
			Page::AddPreStep2($_POST['servergroup']);
		break;
		case 'addprestep3':
			$preset = Preset::getInstance();
			$preset->groupid = $_POST['groupid'];
			try {
				$preset->Create(array('groupid' => $_POST['groupid'], 'name' => $_POST['name'], 'paramsdata' => serialize($preset->generateOperateArray($_POST))));
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched during preset creation. Error dump: '.nl2br($e));
				Page::message();
			}			
			Page::Presets();
		break;
		case 'updatepreset':
			$preset = Preset::getInstance();
			$preset->groupid = $_POST['groupid'];
			try {
				$preset->BatchUpdate(array('name' => $_POST['name'], 'paramsdata' => serialize($preset->generateOperateArray($_POST))), $_POST['id']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched during preset update. Error dump: '.nl2br($e));
				Page::message();
			}
			
			if(is_numeric($_POST['id'])){
				Page::EditPreset($_POST['id']);
			} else {
				Page::Presets();
			}
			
		break;
		case 'addpkg':
			$package = Package::getInstance();
			//$package->Create($_POST['pkgname'], $_POST['presetid'], $_POST['price'], $_POST['paytype'], $_POST['stock']);
			try {
				$package->Create($_POST);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched during package creation. Error dump: '.nl2br($e));
				Page::message();
			}
			
			Page::Package();
		break;
		case 'addorder':
			if($_POST['createinvoice'] == 1){
				$inv = 'new';
			} else {
				$inv = 'no';
			}
			$order = Order::getInstance();
			$order->Create($_POST['customerid'], $_POST['pkgid'], $inv,'','','','Pending', $_POST['cycle']);
			Page::ManageOrders();
		break;
		case 'addinv':
			$userid = User::getInstance()->GetID($_POST['custname']);
			$invoice = Invoice::getInstance();
			$invoice->Create($userid, $_POST['amount'], $_POST['datedue'], 'Unpaid', '', $_POST['datecreated']);
			if(is_numeric($_POST['orderid'])){
				$order = Order::getInstance();
				$orderbutch = $order->FetchData($_POST['orderid']);
				if($orderbutch['accountid'] == $userid){
					$order->Update($_POST['orderid'], 'lastinv', $invoice->invid);
				}
			}
			Page::ManageInvoices();
		break;
		case 'activategatewaymodule':
			$gm = GatewayModule::getInstance();
			$gm->Create($_POST['modulename']);
			Page::ManageGatewayModules();
		break;
		case 'disactivategatewaymodule':
			$gm = GatewayModule::getInstance();
			$gm->Delete($_POST['moduleid']);
			Page::ManageGatewayModules();
		break;
		case 'currencysetupdate':
			$setting = Settings::getInstance();
			if(!is_string($_POST['defcurrency']) || !is_string($_POST['defcurrencysource']) || !is_string($_POST['currencysymbol']) || !isset($_POST['defpaymodule'])){
				throw new Exception("Wrong data specified");
			}
			$setting->Update('system.currency', $_POST['defcurrency']);
			$setting->Update('system.currency.autoupdate', $_POST['defcurrencysource']);
			$setting->Update('system.currency.symbol', $_POST['currencysymbol']);
			$setting->Update('system.paygateway.default', $_POST['defpaymodule']);
			Page::GeneralSettings();
		break;
		case 'editgateway':
			$gw = GatewayModule::getInstance();
			$gw->name = $_POST['gwname'];
			$gw->Update('data', serialize($gw->generateOperateArray($_POST)), $gw->GetID());
			$gw->Update('currency', $_POST['defcurr'], $gw->GetID());
			Page::EditGateway($_POST['gwname']);
		break;
		case 'editorder':
			$order = Order::getInstance();
			$order->orderid = $_POST['orderid'];
			$order->Update('accessdata', serialize($order->generateCreateArray($_POST)), $_POST['orderid']);
			$order->Update('productid', $_POST['defpkg'],$_POST['orderid'] );
			$order->Update('status', $_POST['orderstatus'],$_POST['orderid'] );
			$order->Update('cycle', $_POST['ordercycle'], $_POST['orderid']);
			$order->Update('orderdate', $_POST['orderdate'], $_POST['orderid']);
			$order->Update('nextdue', $_POST['nextdue'],$_POST['orderid'] );
			$order->Update('firstamount', $_POST['firstamount'], $_POST['orderid']);
			$order->Update('recuramount', $_POST['recuramount'], $_POST['orderid']);
			Page::EditOrder($_POST['orderid']);
		break;
		case 'addtrans':
			$trans = Transaction::getInstance();
			$trans->Create($_POST['invid'], $_POST['paygw'], $_POST['amount'], $_POST['date']);
			Page::EditInvoice($_POST['invid']);
		break;
		case 'moduleaction':
			$service = Service::getInstance();
			$service->$_REQUEST['do']($_REQUEST['orderid']);
			Page::EditOrder($_REQUEST['orderid']);
		break;
		case 'editserver':
			$server = Server::getInstance();
			$server->id = $_POST['serverid'];
			$server->Update('servername', $_POST['servername']);
			$server->Update('status', $_POST['serverstatus']);
			$server->Update('maxclients', $_POST['maxclients']);
			$server->Update('accessdata', serialize($server->generateOperateArray($_POST)));
			Page::EditServer($_POST['serverid']);
		break;
		case 'editcron':
			$setting = Settings::getInstance();
			if(isset($_POST['autosusp'])){
				$setting->Update('system.cron.autosuspend', 1);
			} else {
				$setting->Update('system.cron.autosuspend', 0);
			}
			if(isset($_POST['autoterm'])){
				$setting->Update('system.cron.autoterminate', 1);
			} else {
				$setting->Update('system.cron.autoterminate', 0);
			}
			$setting->Update('system.cron.daystonewinv', $_POST['daystonewinv']);
			$setting->Update('system.cron.daystosuspend', $_POST['daystosuspend']);
			$setting->Update('system.cron.daystoterminate',$_POST['daystoterminate']);
			Page::EditCron();
		break;
		case 'editpkg':
			$pkg = Package::getInstance();
			try {
				$pkg->BatchUpdate($_POST, $_POST['pkgid']);
			} catch (Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched during update. Error dump: '.nl2br($e));
				Page::message();
			}
			Page::EditPkg($_POST['pkgid']);
		break;
		case 'delpkg':
			$pkg = Package::getInstance();
			$order = Order::getInstance();
			if(!is_numeric($_REQUEST['pkgid'])){
				throw new Exception("No package ID specified to delete");
			} elseif($order->Calculate('',$_REQUEST['pkgid']) > 0) {
				Page::$messages[] = array('type' => 'attention', 'message' => 'You cannot remove this package because there are still existing orders for that package');
			} else {
				$pkg->Delete($_REQUEST['pkgid']);
				Page::$messages[] = array('type' => 'success', 'message' => 'Package successfully removed');
			}
			Page::Package();
		break;
		case 'delcustomer':
			if(!is_numeric($_REQUEST['userid']) && !isset($_REQUEST['username'])){
				Page::$messages[] = array('type' => 'attention', 'message' => 'User ID is not set');
			} else {
				$user = User::getInstance();
				if(!is_numeric($_REQUEST['userid'])){
					$userid = $user->GetID(trim($_REQUEST['username']), 'username');
				} else {
					$userid = $_REQUEST['userid'];
				}
				$user->Delete($userid);
				Page::ManageCustomers();
			}
		break;
		case 'delinv':
			if(!is_numeric($_REQUEST['invid'])){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Invoice ID is not set');
			} else {
				$inv = Invoice::getInstance();
				$inv->Delete($_REQUEST['invid']);
				Page::ManageInvoices();
			}
		break;
		case 'editinv':
			$inv = Invoice::getInstance();
			try {
				$inv->BatchUpdate($_POST, $_POST['invoiceid']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched during update. Error dump: '.nl2br($e));
				Page::message();
			}
			Page::EditInvoice($_POST['invoiceid']);
		break;
		case 'adddep':
			$dep = Department::getInstance();
			$dep->Create($_POST);
			Page::ManageDepartments();
		break;
		case 'deldep':
			$dep = Department::getInstance();
			$dep->Delete($_REQUEST['id']);
			Page::ManageDepartments();
		break;
		case 'delcurr':
			$curr = Currency::getInstance();
			$curr->Delete($_REQUEST['id']);
			Page::ManageCurrencies();
		break;
		case 'delorder':
			if(!is_numeric($_REQUEST['orderid'])){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Order ID is not set');
			} else {
				$order = Order::getInstance();
				$order->Delete($_REQUEST['orderid']);
				Page::ManageOrders();
			}
		break;
		case 'updatedep':
			$dep = Department::getInstance();
			$dep->id = $_POST['depid'];
			$dep->Update('name', $_POST['depname']);
			$dep->Update('type', $_POST['type']);
			Page::ManageDepartments();
		break;
		case 'updatelangs':
			$lang = Lang::getInstance();
			$lang->UpdateLangs();
			Page::GeneralSettings();
		break;
		case 'updatelang':
			$setting = Settings::getInstance();
			$setting->Update('system.lang.default',$_POST['langcode']);
			Page::GeneralSettings();
		break;
		case 'updatecurr':
			$curr = Currency::getInstance();
			if(!is_numeric($_POST['curid'])){
				throw Exception("Currency ID must be numeric");
			}
			$curr->Update('name', $_POST['name'], $_POST['curid']);
			$curr->Update('symbol', $_POST['symbol'], $_POST['curid']);
			$curr->Update('desc', $_POST['desc'], $_POST['curid']);
			$curr->Update('rate', $_POST['rate'], $_POST['curid']);
			Page::EditCurrency($_POST['curid']);
		break;
		case 'addcurr':
			$curr = Currency::getInstance();
			$curr->Create($_POST);
			Page::ManageCurrencies();
		break;
		case 'updatecurrs':
			$curr = Currency::getInstance();
			try {
				$curr->UpdateCurrs();
			} catch (Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => 'Fatal error catched during update. Error dump: '.nl2br($e));
				Page::message();
			}
			Page::ManageCurrencies();
		break;
		case 'addnotifytemplate':
			$nt = NotifyTemplate::getInstance();
			$nt->Create($_POST);
			Page::ManageNotifyTemplates();
		break;
		case 'editnotifytemplate':
			$nt = NotifyTemplate::getInstance();
			$nt->BatchUpdate($_POST, $_REQUEST['ntid']);
			Page::EditNotifyTemplate($_REQUEST['ntid']);
		break;
		case 'delnotifytemplate':
			$nt = NotifyTemplate::getInstance();
			$nt->Delete($_REQUEST['ntid']);
			Page::ManageNotifyTemplates();
		break;
		case 'activatentmodule':
			$nm = NotificationModule::getInstance();
			$name = $_POST['modulename'].'notifymodule';
			if(!class_exists($name)){
				throw new Exception("Module doesnt exists");
			}
			$module = $name::getInstance();
			$moduleinfo = $module->Info();
			$nm->Create(array('name' => $_POST['modulename'], 'shortname' => $moduleinfo['shortname']));
			Page::ManageNotifyModules();
		break;
		case 'delntmodule':
			$nm = NotificationModule::getInstance();
			$nm->Delete($_REQUEST['moduleid']);
			Page::ManageNotifyModules();
		break;
		case 'updatentmodule':
			$ntd = NotifyModuleData::getInstance();
			$nm = NotificationModule::getInstance();
			if(!$nm->FetchData($_POST['moduleid'])){
				throw new Exception("Module doesnt exists with id: ".$_POST['moduleid']);
			}
			$name = $nm->FetchData($_POST['moduleid']);
			$name = $name['name'].'notifymodule';
			if(!class_exists($name)){
				throw new Exception("Module ".$name." doesnt exists!");
			}
			$module = $name::getInstance();
			$reqs = $module->OperateRequirements();
			
			for($i=0;$i<count($reqs);$i++){
				$row_id = $ntd->GetID($_POST['moduleid'], 'moduleid', "`name` = '".$reqs[$i]['name']."'");
				if($row_id){
					$ntd->Update('value', $_POST[$reqs[$i]['name']],$row_id);
				} else {
					$ntd->Create(array('moduleid' => $_POST['moduleid'], 'name' => $reqs[$i]['name'], 'value' => $_POST[$reqs[$i]['name']]));
				}
			}
			Page::EditNotifyModule($_POST['moduleid']);
		break;
		case 'updatedefnotifymodule':
			$setting = Settings::getInstance();
			$setting->Update('system.notifymodule.default', $_POST['nmoduleid']);
			Page::GeneralSettings();
		break;
		case 'updatepersonalsettings':
			$us = UserSettings::getInstance();
			$us->Set(Page::$userid, 'notifymodule', $_POST['notifymodule']);
			$us->Set(Page::$userid, 'language', $_POST['language']);
			$us->Set(Page::$userid, 'currency', $_POST['currency']);
			$us->Set(Page::$userid, 'notifyaddress', $_POST['notifyaddress']);
			if($_POST['adminnewuser']){
				$us->Set(Page::$userid, 'adminnewuser', 1);
			} else {
				$us->Set(Page::$userid, 'adminnewuser', 0);
			}
			if($_POST['adminneworder']){
				$us->Set(Page::$userid, 'adminneworder', 1);
			} else {
				$us->Set(Page::$userid, 'adminneworder', 0);
			}
			if($_POST['adminnewticket']){
				$us->Set(Page::$userid, 'adminnewticket', 1);
			} else {
				$us->Set(Page::$userid, 'adminnewticket', 0);
			}
			if($_POST['adminnewticketreply']){
				$us->Set(Page::$userid, 'adminnewticketreply', 1);
			} else {
				$us->Set(Page::$userid, 'adminnewticketreply', 0);
			}
			if($_POST['dailyreport']){
				$us->Set(Page::$userid, 'dailyreport', 1);
			} else {
				$us->Set(Page::$userid, 'dailyreport', 0);
			}
			Page::PersonalSettings();
		break;
		case 'sendmessage':
			$nt = Notification::getInstance();
			$user = User::getInstance();
			if($_POST['sendtousers'] == 'all'){
				$sendto = $user->GetButch();
			} elseif(is_numeric($_POST['sendtousers'])){
				$sendto = $user->GetButch('',"`id` = '".$_POST['sendtousers']."'");
			} else {
				throw new Exception("Target users is not specified or in wrong format");
			}
			try {
				$nt->Send($sendto, false, false, $_POST['subject'], $_POST['message']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', 'message' => nl2br("Fatal error catched: ".$e));
				Page::message();
			}
			Page::SendMessage();
		break;
		case 'updatecustomer':
			$user = User::getInstance();
			if(isset($_POST['password'])){
				$_POST['password'] = md5($_POST['password']);
			}
			try {
				$user->BatchUpdate($_POST, $_POST['userid']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', nl2br("Fatal error catched: ".$e));
				Page::message();
			}
			Page::EditCustomer($_POST['userid']);
		break;
		case 'delpres':
			$pres = Preset::getInstance();
			try {
				$pres->Delete($_REQUEST['presid']);
			} catch (Exception $e){
				Page::$messages[] = array('type' => 'attention', nl2br("Fatal error catched: ".$e));
				Page::message();
			}
			Page::Presets();
		break;
		case 'updateservergroup':
			$sg = ServerGroups::getInstance();
			try {
				$sg->BatchUpdate($_POST,$_POST['groupid']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', nl2br("Fatal error catched: ".$e));
				Page::message();
			}
			Page::ManageServerGroups();
		break;
		case 'delserver':
			$server = Server::getInstance();
			try {
				$server->Delete($_REQUEST['id']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', nl2br("Fatal error catched: ".$e));
				Page::message();
			}
			Page::Servers();
		break;
		case 'delservergroup':
			$sg = ServerGroups::getInstance();
			try {
				$sg->Delete($_REQUEST['id']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', nl2br("Fatal error catched: ".$e));
				Page::message();
			}
			Page::ManageServerGroups();
		break;
		case 'deltrans':
			$tr = Transaction::getInstance();
			try{
				$tr->Delete($_REQUEST['id']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', nl2br("Fatal error catched: ".$e));
				Page::message();
			}
			Page::ManageTrans();
		break;
		case 'updateorderserverdata':
			$order = Order::getInstance();
			try {
				$order->BatchUpdate($_POST,$_POST['id']);
			} catch(Exception $e){
				Page::$messages[] = array('type' => 'attention', nl2br("Fatal error catched: ".$e));
				Page::message();
			}
			Page::EditOrder($_POST['id']);
		break;
		default:
			var_dump($_POST);
	}
	
} elseif($action != '' && $auth->check_auth() && $auth->get_rights() == 'Active'){
//Page::$userid = $userinstance->GetID();
Page::init('user');
//Take action and show user object
	switch($action){
		case 'editprofile':
			$user = User::getInstance();
			$profile = Profile::getInstance();
			$userid = Page::$userid;
			$data = $_POST;
			$data['userid'] = $userid;
			if(!($profid = $profile->GetID($userid, 'userid'))){
				$profile->Create($data);
			} else {
				
				$profile->BatchUpdate($data, $profid);
			}
			/*
			if(count($user->FetchProfile($userid)) < 1){
				$user->CreateProfile($userid,$_POST['name'],$_POST['surname'],$_POST['sex'],$_POST['phone'],$_POST['country'],$_POST['address'],$_POST['city'],$_POST['postcode'],$_POST['company'],$_POST['icq'],$_POST['jabber']);
			} else {
				$user->UpdateProfile('name', $_POST['name'],$userinstance->GetID());
				$user->UpdateProfile('surname', $_POST['surname'],$userinstance->GetID());
				$user->UpdateProfile('company', $_POST['company'],$userinstance->GetID());
				$user->UpdateProfile('sex', $_POST['sex'], $userinstance->GetID());
				$user->UpdateProfile('phone', $_POST['phone'], $userinstance->GetID());
				$user->UpdateProfile('country', $_POST['country'], $userinstance->GetID());
				$user->UpdateProfile('address', $_POST['address'], $userinstance->GetID());
				$user->UpdateProfile('city', $_POST['city'], $userinstance->GetID());
				$user->UpdateProfile('postcode', $_POST['postcode'], $userinstance->GetID());
				$user->UpdateProfile('icq', $_POST['icq'], $userinstance->GetID());
				$user->UpdateProfile('jabber', $_POST['jabber'], $userinstance->GetID());
			}
			*/
			Page::UserProfile();
		break;
		case 'placeorder':
			$order = Order::getInstance();
			$orderid = $order->Create($userinstance->GetID(), $_POST['pkgid'],'new','','','','',$_POST['cycle']);
			Page::UserOrderPlaced($orderid);
		break;
		case 'addticket':
			$tc = TicketChange::getInstance();
			$tid = $tc->NewTicket($_POST['subject'], $_POST['message'], $_POST['depid'],$userinstance->GetID(),$userinstance->GetID());
			Page::UserViewTicket($tid);
		break;
		case 'addticketreply':
			$tc = TicketChange::getInstance();
			$tc->ReplyTicket($_POST['message'],$_POST['ticketid'],$userinstance->GetID());
			Page::UserViewTicket($_POST['ticketid']);
		break;
		case 'updatepersonalsettings':
			$us = UserSettings::getInstance();
			$us->Set(Page::$userid, 'notifymodule', $_POST['notifymodule']);
			$us->Set(Page::$userid, 'language', $_POST['language']);
			$us->Set(Page::$userid, 'currency', $_POST['currency']);
			$us->Set(Page::$userid, 'notifyaddress', $_POST['notifyaddress']);
			if($_POST['usernewinvoice']){
				$us->Set(Page::$userid, 'usernewinvoice', 1);
			} else {
				$us->Set(Page::$userid, 'usernewinvoice', 0);
			}
			if($_POST['userneworder']){
				$us->Set(Page::$userid, 'userneworder', 1);
			} else {
				$us->Set(Page::$userid, 'userneworder', 0);
			}
			if($_POST['usernewticket']){
				$us->Set(Page::$userid, 'usernewticket', 1);
			} else {
				$us->Set(Page::$userid, 'usernewticket', 0);
			}
			if($_POST['usernewticketreply']){
				$us->Set(Page::$userid, 'usernewticketreply', 1);
			} else {
				$us->Set(Page::$userid, 'usernewticketreply', 0);
			}
			Page::UserPersonalSettings();
		break;
	}
}

?>
