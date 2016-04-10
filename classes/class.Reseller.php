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

class Reseller {

public $id;   // integer Account ID
public $domain;  // string Domain
public $email;  // string Contact's Email address
public $amount;
public $rate;
public $status;
public $label;
public $cname;
public $company;
public $info;  // string extend Info
public $theme;
public $contacts;
public $contact;

static $LANG = array(
'Rate' => 'Размер ежемесячного вознаграждения, %',
'ShortPrefix' => 'Короткий ID'
);

function __construct( $id = '' ) {
	global $DB, $RESELLERS;
	if(!empty($id)) {
		$this->id = $id;
		$result = $DB->make_select('Resellers', '*', "`ResellerID`='$this->id'");
		$row = $DB->row($result);
		if(!$row) {
			log_error("Reseller ID $this->id not found", __FILE__, __LINE__);
			$this->id = '';
			pdata("Reseller ID $this->id not found\n");
		}
		$this->load($row);
		$RESELLERS[$id] = $this;
	}else{
		$this->company = new Company();
	}
}

public function load( $data ) {
	global $Companys;
	$this->id = $data['ResellerID'];
	$this->domain = $data['domain'];
	$this->amount = $data['amount'];
	$this->rate = $data['rate'];
	$this->status = $data['status'];
	$this->label = $data['label'];
	$this->cname = $data['cname'];
	$this->info = $data['info'];
	$this->theme = $data['theme'];
	$this->contacts = $data['contacts'];
	if(isset($Companys[$data['CompanyID']])) $this->company = $Companys[$data['CompanyID']]; else $this->company = new Company($data['CompanyID']);
}

public function Show() {
	global $LANG, $DB;
	$show = beginTable("$LANG[Reseller] $LANG[Details] $this->label");
	$show .= StaticField($LANG['Domain'], $this->domain);
	$show .= StaticField($LANG['Title'], $this->label);
	$show .= StaticField($LANG['Amount'], $this->amount .' руб.');
	$show .= StaticField(Reseller::$LANG['Rate'], $this->rate .'%');
	$show .= StaticField($LANG['Company'], $this->company->title);
	$show .= StaticField(Reseller::$LANG['ShortPrefix'], $this->cname);
	$show .= StaticField('Theme', $this->theme);
	$show .= StaticField('Контакты', '<pre>'.$this->contacts.'</pre>');
	$accs = $DB->count_objs('Accounts', "Status!='Deleted' AND ResellerID='$this->id'");
	$show .= StaticField('Всего абонентов', '<a href="?object=account&action=list&ResellerID='.$this->id.'">'.$accs.' шт.</a>');
	$show .= endTable();
	return $show;
}

public function edit_form() {
	global $LANG;
	$show = openForm(iSELF.'?object=reseller&amp;action=edit2');
	$show .= HiddenField('ResellerID', $this->id);
	$show .= beginTable("$LANG[Details] $LANG[Reseller]: $this->id , $this->label");
	$show .= TextField($LANG['Title'], 'label', $this->label);
	$show .= TextField(Reseller::$LANG['ShortPrefix'], 'cname', $this->cname);
	$show .= TextField(Reseller::$LANG['Rate'], 'rate', $this->rate);
	$packs = Company::load_companys();
	$pack_array = array();
	foreach($packs as $pack) { $pack_array[] = $pack->id; $pack_array[] = $pack->title; }
	$show .= ArrayDropBox('CompanyID', 'CompanyID', $this->company->id, $pack_array);
	$show .= TextField('Theme', 'theme', $this->theme);
	$show .= LargeTextField('Контакты', 'contacts', $this->contacts);
	$show .= Submitter('edit_reseller', $LANG['Edit']);
	$show .= endTable();
	return $show;
}

public function ShowActions() {
	global $LANG;
	$show = beginTable($LANG['Actions']);
	$show .= "<tr><td><a href='".iSELF."?object=reseller&amp;action=edit&amp;ResellerID=$this->id'><img src=\"images/edit.png\">$LANG[Edit] </a></td></tr>";
	$show .= "<tr><td><a href='".iSELF."?object=reseller&amp;action=payment&amp;ResellerID=$this->id'><img src=\"images/edit.png\">Списать со счёта</a></td></tr>";
	$show .= "<tr><td><a href='".iSELF."?object=tarif&amp;action=list&amp;ResellerID=$this->id'><img src=\"images/edit.png\">Тарифы</a></td></tr>";
	$show .= "<tr><td><a href='".iSELF."?object=reseller&amp;action=delete&amp;ResellerID=$this->id'><img src=\"images/delete.png\">$LANG[Delete] </a></td></tr>";
	$show .= endTable();
	return $show;
}

public function ShowAmounts() {
	global $LANG, $AMOUNT, $DB;
	$result = $DB->make_select('Amount', '*', "`ResellerID`='$this->id'", 'opentime', 'DESK');
	//$count = $DB->count($result);
	$show = beginTable('движение средств');
	$show .= makeTH('Дата', 'Тип операции', $LANG['Amount'], 'Остаток счёта', 'Комментарий');
	while( $data = $DB->row($result) ) $show .= makeTD(
		$data['opentime'],
		$AMOUNT[$data['type']],
		$data['amount'].' руб.',
		$data['rest'],
		$data['comment']
		);
	$show .= endTable();
	return $show;
}

public function save() {
	global $DB;
	return $DB->make_update('Resellers', "`ResellerID`='$this->id'" , array(
		'domain' => $this->domain,
		'amount' => $this->amount,
		'rate' => $this->rate,
		'status' => $this->status,
		'label' => $this->label,
		'cname' => $this->cname,
		'CompanyID' => $this->company->id,
		'info' => $this->info,
		'theme' => $this->theme,
		'contacts' => $this->contacts
		) );
}

static function add_reseller($obj) {
	global $DB;
	return $DB->make_insert( 'Resellers', array(
		'ResellerID' => $obj->id,
		'domain' => $obj->domain,
		'amount' => $obj->amount,
		'rate' => $obj->rate,
		'status' => $obj->status,
		'label' => $obj->label,
		'cname' => $obj->cname,
		'CompanyID' => $obj->company->id,
		'info' => $obj->info,
		'theme' => $obj->theme,
		'contacts' => $obj->contacts
		) );
}

static function load_Resellers( $columns = null, $filter = null, $sortby = null, $sortdir = null, $limit = null, $start = null ) {
	global $DB;
	$result = $DB->make_select('Resellers', $columns, $filter, $sortby, $sortdir, $limit, $start );
	$dbo_array = array();
	while( $data = $DB->row($result) ) {
		$dbo = new Reseller();
		$dbo->load($data);
		$dbo_array[] = $dbo;
	}
	return $dbo_array;
}

}
