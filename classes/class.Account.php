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

class Account {

public $id;   // integer Account ID
public $reseller; // object of Reseller
public $username;  // string Account Username
public $password;  // string Account Password
public $opentime;  // date Account create date
public $closetime;  // date Account suspend date
public $package;  // object of Package
public $ServerID; //ServerID
public $amount = 0;  // integer Amount
public $bonustime;  // integer Bonus Time
public $domain;  // string Domain
public $contype = 'person';  // string Account Type (Business Account/Individual Account) = person/company/businessman
public $contact;  // string extend Info of Account
public $status;  // string Account Status (Active/Open/Suspend/Deleted)
public $lastproc;  // date Account last time amount recalculate
public $lastlogin; // date User last login
public $OrderID; // [protected] id of order from which account created
public $itsResellerID = NULL; // It is Reseller. False if simple account or true if account is reseller
//public $version = '1.0';

/**  :TODO:
 Посмотреть по производительности. Может лучше suspend_type,suspend_reason вынести в поле БД billing_opts, контакт тоже отдельное поле, оставить в info только данные загружаемые при просмотре акка.
*/
public $info = array(
'comment'=>'',
'ruanketa'=>'',
'suspend_type'=>'',
'suspend_reason'=>'',
'suspend_text'=>''
);
public $billing_opts = array('credit_days'=>0,'negativ_days'=>3);

static $LANG = array(
'comment'=> 'Доп. комментарий'
);

function __construct($id = '') {
	global $DB, $Accounts;
	$id = intval($id);
	if($id != null) {
		$result = $DB->make_select('Accounts','*','`AccountID`='.$id);
		$row = $DB->row($result);
		if(!$row) {
			log_error("Account ID $id not found", __FILE__, __LINE__);
			//pdata("Account $id not found<br \>");
			$this->id = 0;
			$this->package = new Package();
			$this->reseller = new Reseller();
			$this->contact = new Contact();
		}else{
			$this->load($row);
			$Accounts[$this->id] = $this;
		}
	}else{
		$this->id = 0;
		$this->package = new Package();
		$this->reseller = new Reseller();
		$this->contact = new Contact();
	}
}

public function load($data) {
	global $RESELLERS, $Packages;
	$this->id = empty($data['AccountID']) ? 0 : intval($data['AccountID']);
	$this->username = (string) $data['username'];
	$this->password = (string) $data['password'];
	$this->opentime = (string) $data['opentime'];
	$this->closetime = (string) $data['closetime'];
	$this->amount = (float) $data['amount'];
	$this->bonustime = (int) $data['bonustime'];
	$this->domain = (string) $data['domain'];
	$this->contype = (string) $data['contype'];
	$this->OrderID = $data['OrderID'];
	$this->status = (string) $data['status'];
	$this->lastproc =  (string) $data['lastproc'];
	$this->lastlogin = (string) $data['lastlogin'];
	$this->ServerID = (string) $data['ServerID'];
	$this->itsResellerID = isset($data['Reseller']) ? (string) $data['Reseller'] : NULL;

	$info = unserialize($data['info']);
	$this->contact = Contact::GetContact($this->contype, $info['contact']);
	if(!empty($info['billing_opts'])) {
		$this->billing_opts['credit_days'] = (int) $info['billing_opts']['credit_days'];
		$this->billing_opts['negativ_days'] = (int) $info['billing_opts']['negativ_days'];
	}
	foreach($this->info as $key=>$val) {
		$this->info[$key] = isset($info[$key]) ? (string) $info[$key] : '';
	}

	if(isset($RESELLERS[$data['ResellerID']])) $this->reseller = $RESELLERS[$data['ResellerID']]; else $this->reseller = new Reseller($data['ResellerID']);

	if(isset($Packages[$data['PackageID']])) $this->package = $Packages[$data['PackageID']]; else $this->package = new Package($data['PackageID']);

	// Удалённые данные
	if(empty($this->package->id)) {
		$this->package->id = $data['PackageID'];
		$this->package->title = $data['PackageID'].'(deleted)';
	}




}

public function getBalance_old($image = false) { // return array(amount,bonustime,closetime)
// А надоли проверять даты сервисов на коректность (типа дата старта не в будущем) ?
	global $DB, $AmountGraph;
	if($image) $AmountGraph = array();
	if($this->status == 'Open') return array(0,0,strtotime($this->opentime) + iMON);
	elseif($this->status == 'Staff') {
		if($image) {
			$points[]=array(strtotime($this->opentime),5,0,$this->package->price/iMON);
			if($image) $points[]=array(iNOW_UNIX,5,0,$this->package->price/iMON);
			$AmountGraph = $points;
		}
		return array(0,0,0);
	}else{
		$events = array(); // все точки биллинга
		$negativ_days = $this->billing_opts['negativ_days'];
		$amount = 0; // текущий баланс
		$bonus = 0; // текущий баланс бонусов
		$pay_count = 0; // порядковый номер платежа
		if(($this->status == 'Suspend' && strtotime($this->closetime)<iNOW_UNIX) || ($this->status == 'Deleted')) $events[strtotime($this->closetime)] = array();
		else $events[iNOW_UNIX] = array(); // а что если iNOW_UNIX неправильна? тогда с этим событием херня
		$payments = Payment::load_payments('*,UNIX_TIMESTAMP(opentime) as opentime', "`AccountID`=$this->id", 'opentime');
		foreach($payments as $payment) $events[$payment->opentime][] = array($payment,2);
		$services = Service::load_service_old('*', '`AccountID`='.$this->id, 'opentime');
		foreach($services as $serv) {
			$serv->opentime = strtotime($serv->opentime);
			$serv->closetime = strtotime($serv->closetime);
			if($serv->period) {
				$events[$serv->opentime][] = array($serv,1); // открытие сервиса
				if($serv->closetime != 0) $events[$serv->closetime][] = array($serv,0); // закрытие сервиса
			}else $events[$serv->opentime][] = array($serv,3); // оплата
		}

		ksort($events,SORT_NUMERIC);
		$now_services = array(); $period = 0; $now_tarif = 0; $time = 0; $last_time = 0;
		$points = array(); // массив [точка времени]{тариф,баланс,бонусов}
		foreach($events as $time => $event_ar) {  // Цикл по всем точкам событий
			$period = $time - $last_time;
			// высчитываем баланс за период от $last_time до $time
			if($period>0 && $now_tarif>0) {
//if(!$image) echo ' t1='.date('Y-m-d H:i:s',$last_time).' add_amount='.$add_amount.' add_bonus='.$add_bonus/iDAY.' amount='.round($amount,4).' bonus='.round($bonus/iDAY,0).'<br> t2='.date('Y-m-d H:i:s',$time).' period='.round($period/iMON,1).' mon'.' now_tarif='.$now_tarif*iMON.'<br><br>';
				$z = $now_tarif*$period;	// full price of period
				$x = $amount;			// money in account
				$y = $now_tarif*$bonus;		// price of bonus time

//if(!$image) raw(date('Y-m-d H:i:s',$last_time)." y=$y x=$x z=$z period=".$period/iDAY);

if($y < 0) $y = 0; // Бонусы не могут быть отрицательны
/*
Физически возможные значения
$x = { <0 ; 0 ; >0 }
$y = { 0 ; >0 }
$z = { 0 ; >0 }
$now_tarif = { 0 ; >0 }
*/

				// Костыль. Если сняли сумму больше чем на счету, но остались бонусы.
				if($x<0 && $y>0) {
					if($x+$y>0) { // Живёт на бонусах
						$points[]=array($last_time,0,$y+$x,$now_tarif);
						$y+=$x; $x=0;
					}else{
						$points[]=array($last_time,$x+$y,0,$now_tarif);
						$x+=$y; $y=0;
					}
				}

				if($x>0) {	// нормальный
					if($x>=$z) { $x-=$z; } // норма
					elseif(($x<$z) && ($x+$y>=$z)) { // ушёл на бонусы
						$points[]=array($last_time+$x/$now_tarif,0,$y,$now_tarif);
						$y=$x+$y-$z; $x=0;
					}else{ // ушёл в минус
						$points[]=array($last_time+$x/$now_tarif,0,$y,$now_tarif);
						$points[]=array($last_time+($x+$y)/$now_tarif,0,0,$now_tarif);
						$x=$x+$y-$z; $y=0; }
				}elseif($x==0) {	// живёт на бонусах
					if($y>=$z) {	 // живёт и дальше на бонусах
						$y-=$z;
					}else{		// ушёл в минус
						$points[]=array($last_time+$y/$now_tarif,0,0,$now_tarif);
						$x=$y-$z; $y=0;
					}
				}elseif($x<0) $x-=$z;


				// Костыль. На случай если аккаунт должен был остановится, а этого не произошло
				// при достижении определённого минуса, выставить баланс в ноль
				// $negativ_days = 3 дня минуса было разрешено у старого биллинг.
				// + анализ на списание всех денег за обслуживание до первой оплаты

				if( ($x + $now_tarif*$negativ_days*iDAY < 0) && ($pay_count > 0) ) { // Сбросить в ноль уже
						$last_negative = $x;
						$points[]=array($last_time+$negativ_days*iDAY+($amount+$bonus*$now_tarif)/$now_tarif,-$now_tarif*$negativ_days*iDAY,0,$now_tarif);
						$points[]=array($last_time+$negativ_days*iDAY+($amount+$bonus*$now_tarif)/$now_tarif,0,0,$now_tarif);
						$x = 0;
				}else unset($last_negative);


				$amount = $x; $bonus = $y/$now_tarif;
			}
			// пересчитываем платежи и сервисы для следущего периода
			$add_amount = 0; $add_bonus =0;
			$points[] = array($time, $amount, $bonus*$now_tarif, $now_tarif);
			foreach($event_ar as $event) {
				if($event[1] == 3) $amount -= $event[0]->getSumm(); // разовая услуга
				elseif($event[1] == 1) $now_services[$event[0]->id] = $event[0]; // добавить сервис
				elseif($event[1] == 0) unset($now_services[$event[0]->id]); // отключить сервис
				elseif($event[1] == 2) { $pay_count++; $amount += $event[0]->amount; $bonus += $event[0]->bonustime; $add_amount += $event[0]->amount; $add_bonus += $event[0]->bonustime; } // новый платёж
			}

			// высчитываем тариф для следущего периода. Всегда >=0 т.к. в БД хранятся только UNSIGNED.
			$now_tarif = 0;
			foreach($now_services as $serv) $now_tarif += $serv->price/$serv->period;
			$points[] = array($time, $amount, $bonus*$now_tarif, $now_tarif);

			// Сохраняем время начало периода
			$last_time = $time;
		}

		$AmountGraph = $points;

		if($now_tarif>0) { // остались включены сервисы, значит аккаунт не закрыт
			if(isset($last_negative)) { // Скинутый в ноль
				$closetime = intval(($last_negative / $now_tarif) + iNOW_UNIX + $bonus);
			}else $closetime = intval(($amount / $now_tarif) + iNOW_UNIX + $bonus);
		}else{ // аккаунт остановлен
			//$closetime = intval(($amount / $last_tarif) + iNOW_UNIX + $bonus);
			$closetime = $time;
			if($this->status == 'Active') {
				$this->info['comment'] .= "\n<h1>Ошибка. Нет открытой услуги 'Хостинг'! <h1>";
				log_error('Account (id='.$this->id.') status and Services mismatch');
			}
/*
Решить на автомате:
1) Есть услуга хостинг -> открыть её
2) Нет услуги хостинг -> создать ёё с даты создания акка с текущим тарифом
*/
		}
		//if($this->status == 'Deleted') $closetime = strtotime($this->closetime); // почемуто фукция возращает неправильную дату
		return array($amount,$bonus,$closetime);
	}
}

public function getBalance($image = false) { // return array(amount,bonustime,closetime)
// А надоли проверять даты сервисов на коректность (типа дата старта не в будущем) ?
	global $DB, $AmountGraph;
	if($image) $AmountGraph = array();
	if($this->status == 'Open') return array(0,0,strtotime($this->opentime) + iMON);
	elseif($this->status == 'Staff') {
		if($image) {
			$points[]=array(strtotime($this->opentime),5,0,$this->package->price/iMON);
			if($image) $points[]=array(iNOW_UNIX,5,0,$this->package->price/iMON);
			$AmountGraph = $points;
		}
		return array(0,0,0);
	}else{
		$events = array(); // все точки биллинга
		$negativ_days = $this->billing_opts['negativ_days'];
		$amount = 0; // текущий баланс
		$bonus = 0; // текущий баланс бонусов
		$pay_count = 0; // порядковый номер платежа
		if(($this->status == 'Suspend' && strtotime($this->closetime)<iNOW_UNIX) || ($this->status == 'Deleted')) $events[strtotime($this->closetime)] = array();
		else $events[iNOW_UNIX] = array(); // а что если iNOW_UNIX неправильна? тогда с этим событием херня
		$payments = Payment::load_payments('*,UNIX_TIMESTAMP(opentime) as opentime', "`AccountID`=$this->id", 'opentime');
		foreach($payments as $payment) $events[$payment->opentime][] = array($payment,2);
		$services = Service::load_service('*', '`AccountID`='.$this->id, 'opentime');
		foreach($services as $serv) {
			$serv->opentime = strtotime($serv->opentime);
			$serv->closetime = strtotime($serv->closetime);
			if($serv->period) {
				$events[$serv->opentime][] = array($serv,1); // открытие сервиса
				if($serv->closetime != 0) $events[$serv->closetime][] = array($serv,0); // закрытие сервиса
			}else $events[$serv->opentime][] = array($serv,3); // оплата
		}

		ksort($events,SORT_NUMERIC);
		$now_services = array(); $period = 0; $now_tarif = 0; $time = 0; $last_time = 0;
		$points = array(); // массив [точка времени]{тариф,баланс,бонусов}
		foreach($events as $time => $event_ar) {  // Цикл по всем точкам событий
			$period = $time - $last_time;
			// высчитываем баланс за период от $last_time до $time
			if($period>0 && $now_tarif>0) {
//if(!$image) echo ' t1='.date('Y-m-d H:i:s',$last_time).' add_amount='.$add_amount.' add_bonus='.$add_bonus/iDAY.' amount='.round($amount,4).' bonus='.round($bonus/iDAY,0).'<br> t2='.date('Y-m-d H:i:s',$time).' period='.round($period/iMON,1).' mon'.' now_tarif='.$now_tarif*iMON.'<br><br>';
				$z = $now_tarif*$period;	// full price of period
				$x = $amount;			// money in account
				$y = $now_tarif*$bonus;		// price of bonus time

//if(!$image) raw(date('Y-m-d H:i:s',$last_time)." y=$y x=$x z=$z period=".$period/iDAY);

if($y < 0) $y = 0; // Бонусы не могут быть отрицательны
/*
Физически возможные значения
$x = { <0 ; 0 ; >0 }
$y = { 0 ; >0 }
$z = { 0 ; >0 }
$now_tarif = { 0 ; >0 }
*/

				// Костыль. Если сняли сумму больше чем на счету, но остались бонусы.
				if($x<0 && $y>0) {
					if($x+$y>0) { // Живёт на бонусах
						$points[]=array($last_time,0,$y+$x,$now_tarif);
						$y+=$x; $x=0;
					}else{
						$points[]=array($last_time,$x+$y,0,$now_tarif);
						$x+=$y; $y=0;
					}
				}

				if($x>0) {	// нормальный
					if($x>=$z) { $x-=$z; } // норма
					elseif(($x<$z) && ($x+$y>=$z)) { // ушёл на бонусы
						$points[]=array($last_time+$x/$now_tarif,0,$y,$now_tarif);
						$y=$x+$y-$z; $x=0;
					}else{ // ушёл в минус
						$points[]=array($last_time+$x/$now_tarif,0,$y,$now_tarif);
						$points[]=array($last_time+($x+$y)/$now_tarif,0,0,$now_tarif);
						$x=$x+$y-$z; $y=0; }
				}elseif($x==0) {	// живёт на бонусах
					if($y>=$z) {	 // живёт и дальше на бонусах
						$y-=$z;
					}else{		// ушёл в минус
						$points[]=array($last_time+$y/$now_tarif,0,0,$now_tarif);
						$x=$y-$z; $y=0;
					}
				}elseif($x<0) $x-=$z;


				// Костыль. На случай если аккаунт должен был остановится, а этого не произошло
				// при достижении определённого минуса, выставить баланс в ноль
				// $negativ_days = 3 дня минуса было разрешено у старого биллинг.
				// + анализ на списание всех денег за обслуживание до первой оплаты

				if( ($x + $now_tarif*$negativ_days*iDAY < 0) && ($pay_count > 0) ) { // Сбросить в ноль уже
						$last_negative = $x;
						$points[]=array($last_time+$negativ_days*iDAY+($amount+$bonus*$now_tarif)/$now_tarif,-$now_tarif*$negativ_days*iDAY,0,$now_tarif);
						$points[]=array($last_time+$negativ_days*iDAY+($amount+$bonus*$now_tarif)/$now_tarif,0,0,$now_tarif);
						$x = 0;
				}else unset($last_negative);


				$amount = $x; $bonus = $y/$now_tarif;
			}
			// пересчитываем платежи и сервисы для следущего периода
			$add_amount = 0; $add_bonus =0;
			$points[] = array($time, $amount, $bonus*$now_tarif, $now_tarif);
			foreach($event_ar as $event) {
				if($event[1] == 3) $amount -= $event[0]->getSumm(); // разовая услуга
				elseif($event[1] == 1) $now_services[$event[0]->id] = $event[0]; // добавить сервис
				elseif($event[1] == 0) unset($now_services[$event[0]->id]); // отключить сервис
				elseif($event[1] == 2) { $pay_count++; $amount += $event[0]->amount; $bonus += $event[0]->bonustime; $add_amount += $event[0]->amount; $add_bonus += $event[0]->bonustime; } // новый платёж
			}

			// высчитываем тариф для следущего периода. Всегда >=0 т.к. в БД хранятся только UNSIGNED.
			$now_tarif = 0;
			foreach($now_services as $serv) $now_tarif += $serv->price/$serv->period;
			$points[] = array($time, $amount, $bonus*$now_tarif, $now_tarif);

			// Сохраняем время начало периода
			$last_time = $time;
		}

		$AmountGraph = $points;

		if($now_tarif>0) { // остались включены сервисы, значит аккаунт не закрыт
			if(isset($last_negative)) { // Скинутый в ноль
				$closetime = intval(($last_negative / $now_tarif) + iNOW_UNIX + $bonus);
			}else $closetime = intval(($amount / $now_tarif) + iNOW_UNIX + $bonus);
		}else{ // аккаунт остановлен
			//$closetime = intval(($amount / $last_tarif) + iNOW_UNIX + $bonus);
			$closetime = $time;
			if($this->status == 'Active') {
				$this->info['comment'] .= "\n<h1>Ошибка. Нет открытой услуги 'Хостинг'! <h1>";
				log_error('Account (id='.$this->id.') status and Services mismatch');
			}
/*
Решить на автомате:
1) Есть услуга хостинг -> открыть её
2) Нет услуги хостинг -> создать ёё с даты создания акка с текущим тарифом
*/
		}
		//if($this->status == 'Deleted') $closetime = strtotime($this->closetime); // почемуто фукция возращает неправильную дату
		return array($amount,$bonus,$closetime);
	}
}

public function show_alert() {
	$show = '';
	if($this->status == 'Suspend') {
		$show .= '<table><tr><td><span style="color: red; font-size: 12pt; font-weight: bold; background-color: #e0e0e0;">Аккаунт приостановлен';
		if($this->info['suspend_type']=='manual') {
			$show .= ' <a href="?object=account&amp;action=suspend_info&amp;AccountID='.$this->id.'">вручную</a>:</span><pre>'.$this->info['suspend_reason'].'</pre>';
		}else $show .= ' автоматически</span>';
		$show .= '</td></tr></table>';
	}elseif($this->status == 'Deleted'){
		$show .= '<table><tr><td><span style="color: red; font-size: 12pt;">Аккаунт удалён</span></td></tr></table>';
	}
	return $show;
}

public function show() {
	//if(!$this->id) return '';
	global $LANG, $lInfo, $DB;
	list($amount,$bonustime,$closetime) = $this->getBalance();
	//list($amount2,$bonustime2,$closetime2) = $this->getBalance_old();
	$whois = new Whois($this->domain);
	if(!empty($this->domain)) {
	$result = $DB->make_select('Domains', '*', "`username`='$this->username' AND `ServerID`='$this->ServerID'");
	if(!$row = $DB->row($result)) {
		$DB->make_insert('Domains', array('name'=>$this->domain,'username'=>$this->username,'ServerID'=>$this->ServerID));
		$result = $DB->make_select('Domains', '*', "`username`='$this->username' AND `ServerID`='$this->ServerID'");
		$row = $DB->row($result);
	}
	$domain = new Domain();
	$domain->load($row);
	if(!empty($domain->expirate)) $domain_str = "<a href='http://$this->domain' target='_blank'>$this->domain</a>".'<br />Истекает: '.$domain->expirate; else {
	//if($_SERVER['HTTP_HOST'] != 'localhost') {
		if($whois->is_available()) $domain_str = "<a href='http://$this->ServerID/~$this->username/' target='_blank'>$this->domain [Not Delegeted]</a>";
		else{
			$whois_parsed = $whois->parsed_info();
			$domain_str = "<a href='http://$this->domain' target='_blank'>$this->domain</a>".'<br />Истекает: '.$whois_parsed['expirate'];
			$domain->expirate = $whois_parsed['expirate'];
			$DB->make_update('Domains', '`DomainID`='.$domain->id, array( 'expirate' => $domain->expirate));
		}
	//}else $domain_str = "<a href='http://$this->domain' target='_blank'>$this->domain</a>";
	}
	}else $domain_str = "<a href='http://$this->domain' target='_blank'>$this->domain</a>";
	$show = beginTable("$LANG[AccountDetails] c ID $this->id , {$this->reseller->label}", '100%');
	$show .= StaticField($LANG['Domain'], $domain_str);
	$show .= StaticField($LANG['Server'], $this->ServerID);
	if(!empty($this->ServerID)) $show .= StaticField('Проверить', '<a href="http://'.$this->ServerID.'/~'.$this->username.'/">'.$this->ServerID.'/~'.$this->username.'</a>');
	$show .= StaticField($LANG['Username'], $this->username);
	$show .= StaticField($LANG['Password'], $this->password);
	$show .= StaticField('Дата Создания', 	date('d.m.Y H:i:s', strtotime($this->opentime)));

	$show .= '<tr><td colspan="2">Биллинг:</td></tr>';

	$show .= StaticField($LANG['Status'], Account::show_status($this->status));

	if($this->status=='Suspend' || $this->status=='Deleted') $show .= StaticField('Остановлен', '<b>'.date('d.m.Y H:i', strtotime($this->closetime)).'</b>');
	else{
		$show .= StaticField('Оплачен по', date('d.m.Y H:i', $closetime));
		if($this->billing_opts['credit_days']>0) $show .= StaticField('Проработает до', date('d.m.Y H:i', $closetime+iDAY*$this->billing_opts['credit_days']));
	}

	$show .= StaticField($LANG['Amount'], '<b>'.round($amount,2).' руб.</b>');
	$show .= StaticField($LANG['Bonus'], '<b>'.round($bonustime/iDAY,1).'</b> дн.');
	//$show .= StaticField('OLD | Оплачен по', date('d.m.Y H:i', $closetime2));
	//$show .= StaticField('OLD | '.$LANG['Amount'], '<b>'.round($amount2,2).' руб.</b>');
	//$show .= StaticField('OLD | '.$LANG['Bonus'], '<b>'.round($bonustime2/iDAY,1).'</b> дн.');
	$show .= StaticField('Макс. глубина минуса', $this->billing_opts['negativ_days'].' дн.');
	$show .= StaticField('Возможный кредит', $this->billing_opts['credit_days'].' дн.');
	$show .= StaticField($LANG['Package'], 	'<a href="'.iSELF.'?object=package&amp;action=show&amp;PackageID='.$this->package->id.'">'.$this->package->title.' ('.round($this->package->price,2).' руб.)</a>');

	$show .= '<tr><td colspan="2">Доп. информация:</td></tr>';

	$show .= StaticField($LANG['Client_type'], 	$lInfo[$this->contype]);
	$show .= StaticField('RUCenter Anketa', $this->info['ruanketa']);
	$show .= StaticField('<a href="?object=account&amp;action=edit_comment&amp;AccountID='.$this->id.'">'.Account::$LANG['comment'].'</a>', '<pre>'.$this->info['comment'].'</pre>');
	$show .= StaticField('Последний вход', $this->lastlogin == '0000-00-00 00:00:00' ? 'никогда' : date('d.m.Y H:i', strtotime($this->lastlogin)));
	if($this->itsResellerID) $show .= StaticField('Система управления бизнесом', '<font color="blue"><b>'.$this->itsResellerID.'</b></font>');
	$show .= endTable();
	$show .= '<br />'.$this->contact->show();
	return $show;
}

static public function generate_unique_username($username_domain) {
	global $DB;
	// MAX username in WHM is 8 letters
	$maxlen = 8;
	if(strlen($username_domain)>0 && is_numeric($username_domain[0])) $username_domain = chr(rand(97, 122)).substr($username_domain,1);
	while( strlen($username_domain)<$maxlen ) { $username_domain .= chr(rand(97, 122)); }
	$username = substr($username_domain,0,$maxlen);
	$i=0;
	while($DB->count_objs('Accounts',"`username`='$username'")>0) {
		$i++;
		$hvalue = base_convert($i, 10, 36);
		$username = substr($username_domain,0,$maxlen-strlen($hvalue)) . $hvalue;
	}
	return $username;
}

static function toarray($obj) {
	$info=array(
		'contact' => get_object_vars($obj->contact),
		'billing_opts' => $obj->billing_opts,
		'comment' => $obj->info['comment'],
		'ruanketa' => $obj->info['ruanketa'],
		'suspend_type' => $obj->info['suspend_type'],
		'suspend_reason' => $obj->info['suspend_reason'],
		'suspend_text' => $obj->info['suspend_text']
		);
	return array('ResellerID' => $obj->reseller->id,
		'username' => $obj->username,
		'password' => $obj->password,
		'opentime' => $obj->opentime,
		'closetime' => $obj->closetime,
		'PackageID' => $obj->package->id,
		'amount' => $obj->amount,
		'bonustime' => $obj->bonustime,
		'domain' => $obj->domain,
		'contype' => $obj->contype,
		'ServerID' => $obj->ServerID,
		'info' => serialize($info),
		'status' => $obj->status,
		'lastproc' => $obj->lastproc,
		'lastlogin' => $obj->lastlogin,
		'OrderID' => $obj->OrderID,
		'Reseller' => $obj->itsResellerID
	);
}

public function save() {
		global $DB;
		if(empty($this->id)) return false;
		return $DB->make_update('Accounts', '`AccountID` = '.$this->id , Account::toarray($this));
}

public function ShowActions() {
	global $LANG;
	$show = beginTable($LANG['Actions']);

	$show .= "<tr><td><a href='?object=account&amp;action=graph&amp;AccountID=$this->id' target='_blank'><img src='images/gnumeric.png' alt='graph' />график</a></td></tr>";

	if(checkrights('R')) {
		$show .= "<tr><td><a href='?object=reseller&amp;action=add&amp;AccountID=$this->id'><img src='images/add.png' alt='add pay' />Сделать диллером</a></td></tr>";
	}

	if(checkrights('S')) {
		$show .= "<tr><td><a href='?object=payment&amp;action=add&amp;AccountID=$this->id'><img src='images/add_pay.png' alt='add pay' />$LANG[Add] Платёж</a></td></tr>";
		$show .= "<tr><td>
<form method=\"post\" action=\"account_print.php?action=contract&amp;AccountID=$this->id\" target='_blank'>
<input type=\"text\" name=\"opentime\" value=\"\" />
<input type=\"submit\" name=\"login\" value=\"печать договора\" class='button' />
</form></td></tr>";
		$show .= "<tr><td><a href=\"?object=service&amp;action=add&amp;AccountID=$this->id\"><img src=\"images/add_pay.png\" alt='add service'/>$LANG[Add] Услугу</a></td></tr>";
		$show .= "<tr><td><a href=\"?object=account&amp;action=package&amp;AccountID=$this->id\"><img src=\"images/edit.png\" alt='edit package' />Изменить Тариф</a></td></tr>";
	}

	$show .= "<tr><td><a href=\"?object=account&amp;action=edit&amp;AccountID=$this->id\"><img src=\"images/edit.png\" alt='edit' />$LANG[Edit]</a></td></tr>";
	$show .= "<tr><td><a href=\"?object=account&amp;action=password&amp;AccountID=$this->id\"><img src=\"images/edit.png\" alt='edit' />изменить Пароль</a></td></tr>";
	$show .= "<tr><td><a href=\"?object=account&amp;action=history&amp;AccountID=$this->id\"><img src=\"images/list.png\" alt='history' />показать историю</a></td></tr>";
	$show .= "<tr><td><form method=\"post\" action=\"http://billing.".($_SERVER['REMOTE_ADDR']=='127.0.0.1' ? 'localhost' : $this->reseller->domain)."/?object=login\" target='_blank'>
<input type=\"hidden\" name=\"user_name\" value=\"$this->username\" />
<input type=\"hidden\" name=\"user_pass\" value=\"$this->password\" />
<input type=\"hidden\" name=\"from_billing\" value=\"1\" />
<input type=\"submit\" name=\"login\" value=\"Вход в Кабинет\" class='button' />
</form>
</td></tr>";
	$show .= "<tr><td><a href=\"ftp://$this->username:".urlencode($this->password)."@$this->ServerID\" target='_blank'>Вход в ftp</a></td></tr>";
	$show .= "<tr><td><a href=\"webdav://$this->username:".urlencode($this->password)."@$this->ServerID:2077\" target='_blank'>Вход в webdav</a></td></tr>";
	$show .= "<tr><td><form method=\"post\" action=\"http://$this->ServerID:2082/login/\" target='_blank'>
<input type=\"hidden\" name=\"user\" value=\"$this->username\" />
<input type=\"hidden\" name=\"pass\" value=\"$this->password\" />
<input type=\"submit\" name=\"login\" value=\"Вход в Cpanel\" class='button' />
</form>
</td></tr>";

// Delete
	if($this->status!='Deleted') $show .= "<tr><td><a href='?object=account&amp;action=delete&amp;AccountID=$this->id'><img src='images/delete.png' alt='delete' />$LANG[Delete]</a></td></tr>";

// Suspend
	if($this->status!='Suspend' && $this->status!='Deleted') {
		$show .= "<tr><td><a href='?object=account&amp;action=suspend&amp;AccountID=$this->id'><img src='images/delete.png' alt='suspend' />Приостановить</a></td></tr>";
	}

// Recreate
	$show .= "<tr><td><a href='?object=account&amp;action=recreate&amp;AccountID=$this->id'><img src='images/delete.png' alt='recreate' />Пересоздать</a></td></tr>";


// Unsuspend
	if($this->status=='Suspend') $show .= "<tr><td><a href='?object=account&amp;action=unsuspend&amp;AccountID=$this->id'><img src='images/add.png' alt='unsuspend' />Возобновить</a></td></tr>";

	$show .= endTable();
	return $show;
}

public function ShowDomains(){
	global $LANG, $DB;
$objs = Domain::load_domains('*', "`username`='$this->username' AND `ServerID`='$this->ServerID'");
$count = count($objs);
$show = openForm(iSELF,'get');
$show .= HiddenField('object', 'testdomain');
$show .= beginTable("$count $LANG[Domain]", '100%');
$show .= makeTH($LANG['Domain'], $LANG['DateClosed']);
foreach($objs as $obj) $show .= makeTD(
	"<input type='radio' name='url' value='$obj->name' />$obj->name",
	''
	);
$show .= ArrayDropBox('проверить', 'action', 'whois', array('http','HTTP','whois','WHOIS'));
$show .= Submitter('go', 'check');
$show .= endTable();
$show .= closeForm();
return $show;
}

public function ShowPayments(){
	global $LANG, $PAYMETHOD, $PAYTARGET;
$objs = Payment::load_payments("*,DATE_FORMAT(opentime,'%d.%m.%Y %H:%i') as opentime", "`AccountID`=$this->id", 'PaymentID', 'DESC');
$count = count($objs);
$show = beginTable("$count $LANG[Payments]", '100%');
$show .= makeTH($LANG['PaymentID'], $LANG['DatePaid'], $LANG['Type'], $LANG['Amount'], $LANG['Bonus'], 'Примечание', 'Печать');
foreach($objs as $obj) $show .= makeTD(
	"<a href='?object=payment&amp;action=show&amp;PaymentID=$obj->id'>$obj->id</a>",
	$obj->opentime,
	$PAYMETHOD[$obj->method],
	(($obj->service == 'hosting')||($obj->service == 'hosting_cont')) ? round($obj->amount,2).' ( '.@round($obj->amount/$this->package->price,1).' мес. )' : round($obj->amount,2),
	intval($obj->bonustime/iDAY),
	$PAYTARGET[$obj->service]['sname'],
	"<a href='?object=payment_print&amp;PaymentID=$obj->id' target='_blank'>АКТ</a>"
	);
$show .= endTable();
return $show;
}

public function ShowOrders(){
	global $LANG, $PAYMETHOD, $PAYTARGET;
	$objs = Order::load_orders("*,DATE_FORMAT(opentime,'%d.%m.%Y %H:%i') as opentime", " `AccountID`=".$this->id, 'OrderID', 'DESC');
	$show = beginTable(count($objs).' '.$LANG['Orders'], '100%');
	$show .= makeTH($LANG['OrderID'],$LANG['Amount'].' руб.',$LANG['Service'],$LANG['Paymethod'],$LANG['DateOpened']);
	foreach($objs as $obj) {
		if($obj->status=='Open') $color= 'black';
		elseif($obj->status=='Billed') $color= 'green';
		elseif($obj->status=='Closed') $color= 'red';
		$show .= makeTD(
		"<a href='?object=order&amp;action=show&amp;OrderID=$obj->id'>$obj->id</a>",
		$obj->amount,
		$this->OrderID == $obj->id ? '<b>'.$PAYTARGET[$obj->service]['sname'].'</b>' : $PAYTARGET[$obj->service]['sname'],
		$PAYMETHOD[$obj->paymethod],
		"<font color='$color'>".$obj->opentime."</font>"
		);
	}
	$show .= endTable();
	return $show;
}

public function ShowServices(){
	global $LANG;
	$services = Service::load_service('*', '`AccountID`='.$this->id);
	$show = beginTable(count($services)." $LANG[Services]", '100%');
	$show .= makeTH($LANG['Service'], $LANG['Price'], 'Инфо', $LANG['CreateDate'], $LANG['CloseDate'], 'Списано услугой, руб.');
	foreach($services as $serv) {
	/*
		if($serv->ServiceID == 'hosting') {
			foreach($serv->mod as $mod) {
				$show .= makeTD(
			"<a href='?object=service&amp;action=show&amp;ServiceID=$serv->id'>$serv->name</a>",
			round($mod['price'],2),
			$mod['package']->id,
			date('d.m.Y H:i',$mod['opentime']),
			$mod['time'] == 0 ? '---------' : date('d.m.Y H:i',$mod['opentime']+$mod['time']),
			round($mod['summ'],4)
			);
			}
		}else
	*/
		$show .= makeTD(
			"<a href='?object=service&amp;action=show&amp;ServiceID=$serv->id'>$serv->name</a>",
			round($serv->price,2),
			$serv->getInfo(),
			date('d.m.Y H:i',strtotime($serv->opentime)),
			$serv->closetime == '0000-00-00 00:00:00' ? '---------' : date('d.m.Y H:i',strtotime($serv->closetime)),
			round($serv->getSumm(),4)
			);
	}
	$show .= endTable();
	return $show;
}

public function __toString() {
	$a = array();
	//foreach($this as $key => $val) if(!is_object($val)) $a[$key]=$val; else $a[$key] = $val->id;
	foreach($this as $key => $val) if(!is_object($val)) $a[$key]=$val;
	$a['ResellerID'] = $this->reseller->id;
	$a['PackageID'] = $this->package->id;
	unset($a['reseller']); unset($a['package']);
	foreach($this->contact as $key => $val) $a['contact'][$key]=$val;
	return serialize($a);
}

public function __clone() {
	$this->contact = clone $this->contact;
}

public function mail_send($LetterID, $showonly = false) {
	$account = clone $this;
	$letter = new Letter($LetterID, $account->reseller->id);
	list($account->amount,$account->bonustime,$account->closetime) = $account->GetBalance();
	$account->closetime = date('d-m-Y',$account->closetime);
	$account->amount = round($this->amount,2);
	$server = new Server($account->ServerID);
	$account->ServerIP = $server->ips[0];
	$letter->from = ReplaceTokens::replace(html_entity_decode($letter->from,ENT_QUOTES,'UTF-8'), $account);
	$letter->subject = ReplaceTokens::replace(html_entity_decode($letter->subject,ENT_QUOTES,'UTF-8'), $account);
	$letter->body = ReplaceTokens::replace(html_entity_decode($letter->body,ENT_QUOTES,'UTF-8'), $account);
	log_event('mail account', 'notice', 'subject='.$letter->subject, $this->id);
	if($showonly) return array($letter->from, $letter->subject, $letter->body);
	else return mail_send($this->contact->Email,$letter->from, $letter->subject, $letter->body);
}

public function get_token_vars($matches) {
	//global $obj;
	//echo $matches[1];
	$matches = explode('->',$matches[1]);
//raw($obj);
	$v = $this;
	foreach($matches as $match) {
		$v = $v->$match;
		//if(isset($v->$match)) $v = $v->$match; else{ $v = ''; break; }
	}
	return $v;
}

public function suspend($reason_ar,&$show='') {
	global $DB;
	log_event('suspend account', 'notice', '', $this->id, $this->reseller->id);
	$NOW = date('Y-m-d H:i:s');
	$whm = new WhmAPI($this->ServerID);
	if($reason_ar === false){
		$reason = 'automatic by billing';
		$this->info['suspend_type'] = 'auto';
		$this->info['suspend_reason'] = '';
		$this->info['suspend_text'] = '';
	}else{
		$reason = 'manual from billing by '.iUSER_NAME;
		$this->info['suspend_type'] = $reason_ar['suspend_type'];
		$this->info['suspend_reason'] = $reason_ar['suspend_reason'];
		$this->info['suspend_text'] = $reason_ar['suspend_text'];
	}
	$result = $whm->suspend($this, $reason);
	$this->status = 'Suspend';
	$this->closetime = $NOW;
	$DB->make_update('Accounts', '`AccountID`='.$this->id, array('status'=>$this->status,  'closetime'=>$this->closetime));
	//$this->save();
	Service::closeService($this->id, 'hosting', $NOW);
	Service::closeService($this->id, 'anyperiod', $NOW);
	if($result) {
		$show = '<br />Аккаунт приостановлен <br />';
		$show .= '<pre>'.$result.'</pre><br />';
		return true;
	}else{
		$show = $whm->geterrmsg();
		return false;
	}
}

public function unsuspend() {
	global $DB;
	log_event('unsuspend account', 'notice', '', $this->id, $this->reseller->id);
	$whm = new WhmAPI($this->ServerID);
	$result = $whm->unsuspend($this);
	if($result) {
		$show = '<br />Аккаунт возобновлён <br />';
		$show .= '<pre>'.$result.'</pre><br />';
		$this->lastproc = iNOW_TEXT;
		$this->status = 'Active';
		$DB->make_update('Accounts', '`AccountID`='.$this->id, array('status'=>$this->status, 'lastproc'=>$this->lastproc));
		if($data = $DB->row($DB->make_select('Services', '*', "`AccountID`=$this->id AND `name`='hosting'", 'opentime', 'DESC', 1))) {
		$serv = new Service_hosting;
		$serv->load($data);
		$last = count($serv->mod)-1;
		if($serv->mod[$last]['time'] == 0) {
			// проблема, сервис открыт, а аккаунт в суспенде
			// так пусть так и будет
		}elseif(iNOW_UNIX - $serv->mod[$last]['opentime'] - $serv->mod[$last]['time'] > 3*iDAY) { // Открываем новый сервис, если простой более 3х дней. TODO использовать billing_opts
			$serv->mod[] = $serv->mod[$last];
			$serv->mod[$last+1]['opentime'] = iNOW_UNIX;
			$serv->mod[$last+1]['time'] = 0;
		}else{	// если срок маленький, то пускай списывается прошлым сервисом
			$serv->mod[$last]['time'] = 0;

		}
		$serv->save();
		}
/*
	$result = $DB->make_select('Services', '*,UNIX_TIMESTAMP(opentime) as opentime,UNIX_TIMESTAMP(closetime) as closetime', "`AccountID`=$this->id AND `name`='hosting'", 'opentime', 'DESC', 1);
	if($data = $DB->row($result)) {
		if($data['closetime'] == 0) {
			// проблема, сервис открыт, а аккаунт в суспенде
			// так пусть так и будет
		}elseif(iNOW_UNIX - $data['closetime'] > 3*iDAY) { // Открываем новый сервис, если простой более 3х дней. TODO использовать billing_opts
			$serv = new Service_hosting(array('AccountID'=>$this->id, 'opentime'=>iNOW_TEXT, 'closetime'=>0, 'mod'=>$this->package->id));
			$serv->add_service();
		}else{	// если срок маленький, то пускай списывается прошлым сервисом
			Service::closeService($this->id, 'hosting', 0);
		}
	}
*/
	}else{
		$show = $whm->geterrmsg();
	}
	return $show;
}

static function show_status($status, $color=true) {
	global $LANG;
	switch ($status) {
		case 'Active': $style = 'color:green;'; break;
		case 'Open': $style = 'color:yellow;'; break;
		case 'Suspend': $style = 'color:#BD3026;'; break;
		case 'Deleted': $style = 'color:#BD3026;'; break;
		case 'Staff': $style = 'color:blue;'; break;
		default: $style = ''; break;
	}
	if(!$color) $style = '';
	return '<span style="'.$style.' font-weight:bold;">'.$LANG['AccountStatus'.$status].'</span>';;
}

static function add_account($obj) {
	global $DB;
	$data = Account::toarray($obj);
	if(!iDEMO) unset($data['opentime']);
	if( !$DB->make_insert('Accounts', $data)) return false;
	$id = $DB->insert_id();
	if($id == false) pdata( 'Could not retrieve ID from previous INSERT!' );
	elseif($id == 0) pdata( 'Previous INSERT did not generate an ID' );
	$obj->id = $id;
	return true;
}

// Load multiple Accounts from database
static function load_accounts( $columns = null, $filter = null, $sortby = null, $sortdir = null, $limit = null, $start = null ) {
	global $DB, $FOUND_ROWS;
	$result = $DB->make_select('Accounts', $columns, $filter, $sortby, $sortdir, $limit, $start );
	$FOUND_ROWS = $DB->row($DB->query_adv('SELECT FOUND_ROWS()'));
	$FOUND_ROWS = $FOUND_ROWS['FOUND_ROWS()'];
	$dbo_array = array();
	//$count = $DB->count($result);
	//raw($count);
	while($data = $DB->row($result)){
		$dbo = new Account();
		$dbo->load($data);
		$dbo_array[] = $dbo;
	}
	//$result->free();
	return $dbo_array;
}


}
// End of class Account