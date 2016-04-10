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

class Processor {

public $error_ar;
public $title;
public $servers;
public $ips;
static $ver = '1.0';

function __construct( ) {
	global $DB;
	$result = $DB->make_select('Servers');
	while( $data = $DB->row($result) ) {
		$server = new Server();
		$server->load($data);
		$servers[] = $server;
		foreach($server->ips as $ip) $this->ips[$ip] = $server->id;
	}

}

private function ip2server($ip){
	if(isset($this->ips[$ip])) $ServerID = $this->ips[$ip]; else $ServerID = 'none';
	return $ServerID;
}

public function get_from_whm() {
	global $DB, $error_ar;
echo "Fetching accounts from servers...<br>\n";
$start_time_st = microtime(1);
$DB->truncate_table('whmaccts');
$DB->truncate_table('Domains');
$servers = Server::load_servers('*', "`hash`!=''", 'ServerID');
foreach($servers as $server){
	$start_time = microtime(1);
	echo "Fetching list from server $server->id ... ";
	$acs = 0;
	$whm = new WhmAPI($server->id,$server->hash);
	$accts = $whm->listaccts();
	if($accts !== false) {
		foreach($accts as $acct){
			$acs++;
			$DB->make_insert('whmaccts', array(
			'domain'=>$acct['domain'],
			'server'=>$server->id,
			'username'=>$acct['user'],
			'email'=>$acct['email'],
			'quota'=>$acct['disklimit'],
			'package'=>$acct['plan'],
			'status' => ($acct['suspended'] ? 'Suspend' : 'Active'),
			'theme'=>$acct['theme'],
			'ResellerID'=>$acct['owner']
			));
		}
	}else{
		$error_ar[] = 'Can\'t get list of accounts : '.$whm->errstr;
	}
	echo " get $acs Accounts for ".intval((microtime(1) - $start_time)*1000)."ms.; ... ";

	$start_time = microtime(1);
	//echo "Fetching list from server $server->id ... ";
	$dms = 0;
	$whm = new WhmAPI($server->id,$server->hash);
	$page = $whm->request('/cgi/domainlist.pl');
	$strs = explode("\n",$page);
	foreach($strs as $str) {
		$domains = array_unique(explode('|',$str));
		if(count($domains)>=2) {
		$username = array_shift($domains);
			foreach($domains as $domain) {
				$domainArr = explode('.',$domain);
				if(count($domainArr)<=3 && strlen($domainArr[1])<=4) {
					$dms++;
					$DB->make_insert('Domains', array('name'=>$domain,'username'=>$username,'ServerID'=>$server->id));
				}
			}
		}
	}
		echo " get $dms Domains for ".intval((microtime(1) - $start_time)*1000)."ms.\n";

}
echo "\nFinished Step. Time ".intval((microtime(1) - $start_time_st)*1000)."ms.<br><br>\n\n";
}


public function get_from_dns() {
	global $DB, $error_ar, $DNSSERVER;
echo "Fetching 'A' records for accounts from our DNS server ...<br>\n";
$start_time = microtime(1);
$account_result = $DB->make_select('Accounts', '*', "`Status`!='Deleted'");
$dns_defaults = array(
	'nameservers' => array($DNSSERVER),
	'port'        => '53',
	'retrans'     => 5,
	'retry'       => 3,
	'usevc'       => 0,
	'stayopen'    => 1,
	'igntc'       => 0,
	'recurse'     => false,
	'debug'       => false,
	'tcp_timeout' => 5
);
$dns = new Net_DNS_Client($dns_defaults);
while($account = $DB->row($account_result)) {
	$domain = $account['domain'];
	$dns_packet = new Net_DNS_Packet();
	$dns_packet->buildQuestion($domain.'.', 'A');
	$dns_packet->header->rd = false;
	$dns_ans = $dns->resolver->send($dns_packet);
	if(empty($dns_ans->answer[0]->address)) {
		$ip =  '';
		$error_ar[] = "dns record for account $domain not found";
		continue;
//raw($domain);
	}else{
		$ip = $dns_ans->answer[0]->address;
//raw("$ip - $domain");
	}
	//if(empty($ip)) { $error_ar[] = "dns record for account $domain not found"; $ip = '0.0.0.0'; }
	$server = $this->ip2server($ip);
	if($server!=$account['ServerID'] && $server!='none') {
		$error_ar[] = "Server wrong for domain $domain , account on server '$server', but was at $account[ServerID]";
		//$DB->make_update("`Accounts` SET `ServerID`='$server' WHERE `AccountID`=$account[AccountID]");
	}elseif($server == 'none') $error_ar[] = "IP wrong for domain $domain , account on server $account[ServerID], but in DNS at $ip";
	//echo "$domain \t $ip \t $server \n";
/*
	$account_servers = array();
	$whmaccount_result = $DB->make_select("SELECT * FROM `whmaccts` WHERE `domain`='$domain'  AND `username`='$account[domain]'");
	while($whmaccount = $DB->row($whmaccount_result)) $account_servers[] = $whmaccount['server'];
	if(count($account_servers)==0)  $error_ar[] = "account $domain , no found on servers";
	elseif(count($account_servers)>1) {
		foreach($account_servers as $account_server) $error_ar[] = "clone of account $domain on server $account_server";
		if(!in_array($server,$account_servers)) {
			$error_ar[] = "ip wrong for domain $domain , account on server $whmaccount[server], but in DNS $server";
			$server = $whmaccount['server'];
		}
	}
*/
	//$DB->make_update("Accounts SET ServerID='$server' WHERE AccountID=$account[AccountID]");
}
echo "Finished Step. Time ".intval((microtime(1) - $start_time)*1000)."ms.\n\n";
}

function calc_accts() {
	global $DB, $error_ar;
echo "Billing accounts ...\n";
$start_time = microtime(1);
//$n = 0;
$account_result = $DB->make_select('Accounts');
//$account_result = $DB->make_select('Accounts', '', "`status`='Open'");
for($n = 0; $account_row = $DB->row($account_result); $n++) {
	$status = $account_row['status'];
	$amount = $account_row['amount'];
	$bonustime = $account_row['bonustime'];
	//$statusinfo = $account_row['statusinfo'];
	$NOW = date('Y-m-d H:i:s');
	$now = strtotime($NOW);
	//$now = time();
	$addsql = array();
if($status=='Active') {
	$account = new Account();
	$account->load($account_row);
	list($amount,$bonustime,$closetime) = $account->getBalance();
	$ostperiod = $closetime-$now;
	$ostdays = round($ostperiod/iDAY,0);

	$addsql['closetime'] = date('Y-m-d H:i:s',$closetime);
	if($ostperiod + iDAY*$account->billing_opts['credit_days'] <= 0) {
		echo "Suspend account $account->domain (id=$account->id)\n";
		if(ACTION) {
			if($account->suspend(false)) {
			}
			$account->mail_send(3);
		}
	}elseif( ($ostdays == 7) || ($ostdays == 3) || ($ostdays <= 1) ) {
		echo "Send mail to account $account->domain ({$account->contact->Email}) with '$status' status about close date in $ostdays days\n";
		if(ACTION) $account->mail_send(2);
	}
	unset($account);
	$addsql['amount'] = $amount;
	$addsql['bonustime'] = $bonustime;
}elseif($status=='Open') {
	$account = new Account();
	$account->load($account_row);
	//$amount = 0; $bonustime = 0;
	$period_live = $now-strtotime($account_row['opentime']);
	$days_live = round($period_live/iDAY,0);
	if($days_live == 25) {
		if(ACTION) $account->mail_send(2);
		echo "Send mail to account $account->domain ({$account->contact->Email}) with '$status' status about close date in $ostdays days\n";
	}elseif($days_live >= 30 ) {
		echo "Close account $account->domain (id=$account->id)\n";
		if(ACTION) {
			if($account->suspend(false)){
			}
			$account->mail_send(3);
		}
	}
	unset($account);
	//$addsql['amount'] = $amount;
	//$addsql['bonustime'] = $bonustime;
}elseif($status=='Suspend') {
	$account = new Account();
	$account->load($account_row);
	$period = $now - strtotime($account_row['closetime']);
	if($period >= 60*iDAY) {
		echo "Delete account $account_row[domain] (id=$account_row[AccountID])\n";
		if(ACTION) {
			log_event('delete account', 'notice', '', $account_row['AccountID'], $account_row['ResellerID']);

			$whm = new WhmAPI($account->ServerID);
			$result = $whm->killacct($account);
			if($result) {
				//echo nl2br($whm->xml->rawout);
			}else{
				echo $whm->geterrmsg();
			}
		}
		$addsql['status'] = 'Deleted';
		//$addsql['statusinfo'] = "$statusinfo\nautomatic deleted $NOW";
	}
}elseif($status=='Deleted') {
	$addsql['lastproc'] = 0;
	// pack and archive account if closetime > 6 * iMON
}elseif($status=='Staff') {
	// our accounts
}else {
	$error_ar[] = "Unknown Status for account $account_row[domain] (id=$account_row[AccountID])\n";
}

if(ACTION) {
	$addsql['lastproc'] = $NOW;
	$result = $DB->make_update('Accounts', '`AccountID`='.$account_row['AccountID'], $addsql);
}
//$n++;
}

echo "Finished Step. $n account calculated. Time ".intval((microtime(1) - $start_time)*1000)."ms.\n\n";
}

public function db_optimize() {
	global $DB, $error_ar;

echo "Archive non billed Orders ...\n";
$start_time = microtime(1);
$n = 0;
if($file = fopen(iHOMEDIR.'log/orders.xml','a')) {
	/* fwrite($file, '<?xml version="1.0" encoding="utf-8"?><Orders>'); */
	$res = $DB->make_select('Orders', '', '`AccountID` IS NULL AND UNIX_TIMESTAMP(opentime)<='.(iNOW_UNIX-2*iMON));
	while($data = $DB->row($res)){
		$n++;
		$data['info'] = unserialize($data['info']);
		$xml_obj = array2xml($data, 'Order');
		fwrite($file, $xml_obj);
	}
	fclose($file);
	$DB->make_delete('Orders', '`AccountID` IS NULL AND UNIX_TIMESTAMP(opentime)<='.(iNOW_UNIX-3*iMON));
}
echo "Finished Step. Archive $n orders. Time ".intval((microtime(1) - $start_time)*1000)."ms.\n\n";

echo "Archive deleted packages...\n";
$start_time = microtime(1);
$n = 0;
if($file = fopen(iHOMEDIR.'log/packages.xml','a')) {
	$res = $DB->make_select('Packages', '', "`status`='Deleted'");
	while($data = $DB->row($res)){
		if($DB->count_objs('Accounts',"`PackageID`='$data[PackageID]' AND `Status`!='Deleted'")==0) {
			$n++;
			$xml_obj = array2xml($data, 'Package');
			fwrite($file, $xml_obj);
			$DB->make_delete('Packages', "`PackageID`='$data[PackageID]'");
			$DB->make_delete('Tarifs', "`PackageID`='$data[PackageID]'");
		}
	}
	fclose($file);
}
echo "Finished Step. Archive $n packages. Time ".intval((microtime(1) - $start_time)*1000)."ms.\n\n";

echo "Change status to Deleted for packages what not in Reseller Tarif table...\n";
$start_time = microtime(1);
$n = 0;
$res = $DB->query_adv('SELECT *,`Packages`.`PackageID` as PackageID FROM `Packages` LEFT OUTER JOIN `Tarifs` ON `Packages`.`PackageID` = `Tarifs`.`PackageID` WHERE `Tarifs`.`PackageID` IS NULL');
while($data = $DB->row($res)) {
	$DB->make_update('Packages',"`PackageID`='$data[PackageID]'",array('status'=>'Deleted'));
	$n++;
}
echo "Finished Step. Change $n packages. Time ".intval((microtime(1) - $start_time)*1000)."ms.\n\n";

echo "Optimize Databse ...\n";
$start_time = microtime(1);
$DB->query_adv('OPTIMIZE TABLE `Accounts`, `Amount`, `Companys`, `Resellers`, `Domains`, `Pool`, `History`, `Letters`, `Notes`, `Packages`, `Payments`, `Servers`, `Services`, `Statistic`, `Orders`, `Users`, `whmaccts`, `wm_payment`, `WrongOrders`');
echo "Finished Step. Time ".intval((microtime(1) - $start_time)*1000)."ms.\n\n";
}

public function calc_resellers() {
	global $DB, $error_ar;
	if(date('j') != 5) return true;
	$objs = 0;
	$now_alarm = strtotime(date('Y-m-05 00:00:00'));
	$last_alarm = strtotime('-1 month',$now_alarm);
	$reseller_result = $DB->make_select('Resellers');
while($reseller_row = $DB->row($reseller_result)) {
	$NOW = date('Y-m-d H:i:s');
	$now = strtotime($NOW);
if($reseller_row['status']=='Active') {
	$time_a = '';
	$payment_sum = $DB->row($DB->make_select('Payments','SUM(amount)',"`ResellerID`='$reseller_row[ResellerID]' and UNIX_TIMESTAMP(opentime)>=$last_alarm and UNIX_TIMESTAMP(opentime)<$now_alarm "));
	$payment_sum = $payment_sum['SUM(amount)'];
	if(empty($payment_sum)) $payment_sum = 0;
	$DB->make_insert('Amount',array('ResellerID'=>$reseller_row['ResellerID'], 'amount'=>$payment_sum,'type'=>'push'));
	$reseller_row['amount'] += $payment_sum*$reseller_row['rate']/100;
	$DB->make_update('Resellers',"`ResellerID`='$reseller_row[ResellerID]'",array('amount'=>$reseller_row['amount']));
	echo "Reseller $reseller_row[ResellerID] amount+= $payment_sum \n";
}
}

}

public function audit() {
	global $DB, $error_ar;

// Проверять сужествование связей обьектов.
// Массив связей. (проверяемый ключ => таблица которая отвечет за него)
$LINKS['Orders'] = array(
'AccountID' => 'Accounts',
'ResellerID' => 'Resellers'
);

$LINKS['Accounts'] = array(
'ResellerID' => 'Resellers',
'PackageID' => 'Packages',
'ServerID' => 'Servers'
);

$LINKS['Pool'] = array(
'AccountID' => 'Accounts'
);

$LINKS['History'] = array(
'AccountID' => 'Accounts'
);

//$LINKS['Tarifs'] = array(
//'ResellerID' => 'Resellers'
//);

foreach($LINKS as $test_table => $links) {
	//$primary_key = substr($test_table,0,-1).'ID';
	$primary_key = '';
	$res = $DB->query_adv("SELECT k.column_name FROM information_schema.key_column_usage k WHERE k.constraint_name = 'PRIMARY' AND k.table_schema = 'billing_db' AND k.table_name = '$test_table'");
	while($data = $DB->row($res)) $primary_key .= "$data[column_name]";
//raw($primary_key);
	echo "Cheking $test_table table consistency...";
	foreach($links as $link_key => $link_table) {
		echo "$link_key...";
		$res = $DB->query_adv("SELECT `$test_table`.`$primary_key` as $primary_key, `$test_table`.`$link_key` as $link_key FROM `$test_table` LEFT OUTER JOIN `$link_table` ON `$test_table`.`$link_key` = `$link_table`.`$link_key` WHERE (`$test_table`.`$link_key`) AND `$link_table`.`$link_key` IS NULL");
		while($data = $DB->row($res)) {
			echo "\n Missing object `$link_table`.`$link_key`=$data[$link_key] for lost object `$test_table`.`$primary_key`=$data[$primary_key]\n";
		}
	}
	echo "OK\n";
}


// Проверка на потеренные тарифы
/*
$res = $DB->query_adv('SELECT * FROM `Tarifs` LEFT OUTER JOIN `Resellers` ON `Tarifs`.`ResellerID` = `Resellers`.`ResellerID` WHERE `Resellers`.`ResellerID` IS NULL');
while($data = $DB->row($res)) {
	echo "Lost Tarif $data[ResellerID].$data[PackageID]\n";
}
*/

/*
Нормализация сервисов:
1)проверки на opentime<closetime
2)проверка на непересекаемость периодов сервисов.
*/




/*
$acct_result = $DB->make_select("SELECT * FROM whmaccts a ,whmaccts b WHERE a.Domain=b.Domain AND a.server!=b.server");
$acct_row = $DB->row($acct_result);
$acct_count = $DB->count($acct_result);
echo beginTable("$acct_count Accounts");
if ($acct_row) {
	do {
echo "<tr>
<td>$acct_row[domain]</td>
<td>$acct_row[server]</td>
<td>$acct_row[username]</td>
<td>$acct_row[email]</td>
<td>$acct_row[quota]</td>
<td>$acct_row[package]</td>
<td>$acct_row[theme]</td>
<td>$acct_row[ResellerID]</td>
</tr>\n";
	} while ($acct_row = $DB->row($acct_result));
}
echo endTable();
echo "Finished Step , found $acct_count doublicated accounts. Time ".intval((microtime(1) - $start_time)*1000)."ms.<br><br>\n\n";
*/

/*
$start_time = microtime(1);
echo "Chech for identical count of acoounts in biling and on servers...<br>\n";
$acct_result = $DB->make_select("SELECT * FROM (SELECT domain, server, username, ResellerID, 'no in Billing' as System FROM `whmaccts`) UNION  (SELECT domain, ServerID, username, ResellerID, 'no on Server' as System FROM `Accounts`  WHERE Accounts.status!='Deleted') GROUP by domain HAVING COUNT(domain)!=2");
$acct_row = $DB->row($acct_result);
$acct_count = $DB->count($acct_result);
echo beginTable("$acct_count Accounts");
if ($acct_row) {
	do {
echo "<tr>
<td>$acct_row[domain]</td>
<td>$acct_row[server]</td>
<td>$acct_row[username]</td>
<td>$acct_row[ResellerID]</td>
<td>$acct_row[System]</td>
</tr>\n";
	} while ($acct_row = $DB->row($acct_result));
}

echo endTable();
echo "Finished Step, found $acct_count lost accounts. Time ".intval((microtime(1) - $start_time)*1000)."ms.<br><br>\n\n";
*/

$start_time = microtime(1);
echo "Chech acoounts info servers on servers...<br>\n";
echo " !Фукция не рабочая и результаты не следует считать верными ...<br>\n";
#
$acct_result = $DB->make_select('whmaccts`, `Accounts', "whmaccts.domain, whmaccts.username, Accounts.username as username2, whmaccts.ResellerID, Accounts.ResellerID as ResellerID2, whmaccts.email, 'email' as email2, whmaccts.package, Accounts.PackageID as package2, whmaccts.theme, Accounts.ServerID", 'whmaccts.domain=Accounts.domain', 'domain');
$acct_row = $DB->row($acct_result);
echo beginTable();
echo '<tr><th>Warning</th><th>wrong value</th><th>true value</th><th>Domain</th><tr>';
if ($acct_row) {
	do {
$equal = true;
if($acct_row['username']!=$acct_row['username2']) echo "<tr><td>Incorect Username in Billing</td><td>$acct_row[username2]</td><td>$acct_row[username]</td><td>$acct_row[domain]</td></tr>";
//if($acct_row['ResellerID']!=$acct_row['ResellerID2']) {echo "ResellerID: Server - $acct_row[ResellerID], Billing - $acct_row[ResellerID2]<br>";$equal=false;
	//echo whmreq("/scripts/dochangeowner?user=$acct_row[Username]&owner=$acct_row[ResellerID2]",$acct_row['Server'],$SERVER[$acct_row['Server']]['hash']);
//}

if($acct_row['theme']!='x3') echo "<tr><td>Incorect Theme on Server</td><td>$acct_row[theme]</td><td>x3</td><td>$acct_row[domain]</td></tr>";

//if(!preg_match("/^[-0-9A-Z_\.]{1,50}@([-0-9A-Z_\.]+\.){1,50}([0-9A-Z]){2,4}$/i", $acct_row['email'])){
//	echo "<tr><td>Incorrect Email on Server</td><td>$acct_row[email]</td><td>$acct_row[email2]</td><td>$acct_row[domain]</td></tr>";
	//whmreq("/scripts2/dochangeemail?user=$acct_row[Username]&email=$acct_row[Email2]",$acct_row['Server'],$SERVER[$acct_row['Server']]['hash']);
//}

//if($acct_row['package']!=$acct_row['package2']) {
	//echo "<tr><td>Incorrect Tarif on Server</td><td>$acct_row[package2]</td><td>$acct_row[package]</td><td>$acct_row[domain]</td></tr>";
	//$Tarif = $acct_row['package2'];
	//echo whmreq("/scripts2/upacct?user=$acct_row[Username]&pkg=$Tarif",$acct_row['Server'],$SERVER[$acct_row['Server']]['hash']);
	//echo whmreq("/scripts/saveedituser?BWLIMIT=10485760000&FEATURELIST=default&OWNER=$acct_row[ResellerID2]&user=$acct_row[Username]&DNS=$acct_row[Domain]&PLAN=$Tarif&newuser=$acct_row[Username]&RS=x2&MAXSQL={$TARIF[$Tarif]['SQL']}&MAXADDON={$TARIF[$Tarif]['AddonDomains']}&LANG=russian&seeshell=0&MAXPOP=1000&MAXFTP=1000&MAXLST=1000&MAXSUB=1000&MAXPARK=1000&HASCGI=1&shell=0",$acct_row['Server'],$SERVER[$acct_row['Server']]['hash']);
	//echo whmreq("/scripts/editquota?user=$acct_row[Username]&quota={$TARIF[$Tarif]['Quota']}",$acct_row['Server'],$SERVER[$acct_row['Server']]['hash']);
	//whmreq("/scripts2/domanageshells?user=$acct_row[Username]&shell=Disable",$acct_row['Server'],$SERVER[$acct_row['Server']]['hash']);
//}
	} while ($acct_row = $DB->row($acct_result));
}
echo "</td></tr>";
echo endTable();
echo "Finished Step. Time ".intval((microtime(1) - $start_time)*1000)."ms.<br><br>\n\n";
}

}

function ip2server($ip){
	global $DB;
	$result = $DB->make_select('Servers', '*', "`ips` LIKE '%$ip%'");
	if($server = $DB->row($result)) $ServerID = $server['ServerID']; else $ServerID = 'none';
	//$servers = load_servers('*', "`hash`!=''", '`ServerID`');
	//foreach($servers as $server) if(in_array($ip, $server->ips)) $ServerID = $server->id;
	return $ServerID;
}
