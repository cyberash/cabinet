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

class Company
{

public $id;
public $title; // наименование
public $inn; // ИНН
public $kpp; // КПП
public $c_rasschet; // Счёт в банке для юр.лиц
public $c_bankname; // Банк для юр.лиц
public $c_bik; // Бик банка для юр.лиц
public $c_k_rasschet; // Корр. счёт банка для юр.лиц
public $p_rasschet; // Счёт в банке для физ.лиц
public $p_bankname; // Банк для физ.лиц
public $p_bik; // Бик банка для физ.лиц
public $p_k_rasschet; // Корр. счёт банка для физ.лиц
public $general_manager; // Главный директор
public $chief_accountant; // Главный бухгалтер
public $nds; // Ставка НДС
public $stamp; // Имя файда для изображения штампа

function __construct( $id = '' ){
	global $DB, $Companys;
	if($id != null) {
	$this->id = $id;
	$result = $DB->make_select('Companys', '*', '`CompanyID`='.$this->id);
	$row = $DB->row($result);
		if(!$row) {
			log_error("Company ID $this->id not found", __FILE__, __LINE__);
			pdata("Company ID $this->id not found\n");
		}
	$this->load($row);
	$Companys[$id] = $this;
	}else{
	}
}

function load( $data ){
	$this->id = isset($data['CompanyID']) ? (int) $data['CompanyID'] : 0;
	$this->title = $data['title'];
	$this->inn = $data['inn'];
	$this->kpp = $data['kpp'];
	$this->c_rasschet = $data['c_rasschet'];
	$this->c_bankname = $data['c_bankname'];
	$this->c_bik = $data['c_bik'];
	$this->c_k_rasschet = $data['c_k_rasschet'];
	$this->p_rasschet = $data['p_rasschet'];
	$this->p_bankname = $data['p_bankname'];
	$this->p_bik = $data['p_bik'];
	$this->p_k_rasschet = $data['p_k_rasschet'];
	$this->general_manager = $data['general_manager'];
	$this->chief_accountant = $data['chief_accountant'];
	$this->nds = $data['nds'];
	$this->stamp = $data['stamp'];
}

function show() {
	global $LANG;
	$show = beginTable("$LANG[Details] $LANG[Company]: $this->id , $this->title");
	$show .= StaticField($LANG['Title'], 	 $this->title);
	$show .= StaticField("ИНН", 	 $this->inn);
	$show .= StaticField("КПП", 	 $this->kpp);
	$show .= StaticField("Счёт в банке для юр.лиц", 	 $this->c_rasschet);
	$show .= StaticField("Банк для юр.лиц", 	 $this->c_bankname);
	$show .= StaticField("Бик банка для юр.лиц", 	 $this->c_bik);
	$show .= StaticField("Корр. счёт банка для юр.лиц", 	 $this->c_k_rasschet);
	$show .= StaticField("Счёт в банке для физ.лиц", 	 $this->p_rasschet);
	$show .= StaticField("Банк для физ.лиц", 	 $this->p_bankname);
	$show .= StaticField("Бик банка для физ.лиц", 	 $this->p_bik);
	$show .= StaticField("Корр. счёт банка для физ.лиц", 	 $this->p_k_rasschet);
	$show .= StaticField("Главный директор", 	 $this->general_manager);
	$show .= StaticField("Главный бухгалтер", 	 $this->chief_accountant);
	$show .= StaticField("Ставка НДС", 	 $this->nds);
	$show .= StaticField("Изображение Печати", $this->stamp);
	$show .= endTable();
	return $show;
}

function edit() {
	global $LANG;
	$show = openForm(iSELF.'?object=company&amp;action=edit2');
	$show .= beginTable("$LANG[Details] $LANG[Company]: $this->id , $this->title");
	$show .= HiddenField('CompanyID', $this->id);
	$show .= TextField($LANG['Title'], 'title',	 $this->title);
	$show .= TextField('ИНН', 'inn',	 $this->inn);
	$show .= TextField('КПП', 'kpp',	 $this->kpp);
	$show .= TextField('Счёт в банке для юр.лиц', 'c_rasschet',	 $this->c_rasschet);
	$show .= TextField('Банк для юр.лиц', 'c_bankname',	 $this->c_bankname);
	$show .= TextField('Бик банка для юр.лиц', 'c_bik',	 $this->c_bik);
	$show .= TextField('Корр. счёт банка для юр.лиц', 'c_k_rasschet',	 $this->c_k_rasschet);
	$show .= TextField('Счёт в банке для физ.лиц', 'p_rasschet',	 $this->p_rasschet);
	$show .= TextField('Банк для физ.лиц', 'p_bankname',	 $this->p_bankname);
	$show .= TextField('Бик банка для физ.лиц', 'p_bik',	 $this->p_bik);
	$show .= TextField('Корр. счёт банка для физ.лиц', 'p_k_rasschet',	 $this->p_k_rasschet);
	$show .= TextField('Главный директор', 'general_manager',	 $this->general_manager);
	$show .= TextField('Главный бухгалтер', 'chief_accountant',	 $this->chief_accountant);
	$show .= TextField('Ставка НДС', 'nds',	 $this->nds);
	$show .= TextField("Изображение Печати", 'stamp', $this->stamp);
	$show .= Submitter('edit_company', $LANG['Edit']);
	$show .= endTable();
	return $show;
}

function add() {
	global $LANG;
	$show = openForm(iSELF.'?object=company&amp;action=add2');
	$show .= beginTable("$LANG[Add] $LANG[Company]");
	$show .= TextField($LANG['Title'], 'title', '');
	$show .= TextField('ИНН', 'inn', '');
	$show .= TextField('КПП', 'kpp', '');
	$show .= TextField('Счёт в банке для юр.лиц', 'c_rasschet', '');
	$show .= TextField('Банк для юр.лиц', 'c_bankname', '');
	$show .= TextField('Бик банка для юр.лиц', 'c_bik', '');
	$show .= TextField('Корр. счёт банка для юр.лиц', 'c_k_rasschet', '');
	$show .= TextField('Счёт в банке для физ.лиц', 'p_rasschet', '');
	$show .= TextField('Банк для физ.лиц', 'p_bankname', '');
	$show .= TextField('Бик банка для физ.лиц', 'p_bik', '');
	$show .= TextField('Корр. счёт банка для физ.лиц', 'p_k_rasschet', '');
	$show .= TextField('Главный директор', 'general_manager', '');
	$show .= TextField('Главный бухгалтер', 'chief_accountant', '');
	$show .= TextField('Ставка НДС', 'nds', '');
	$show .= TextField("Изображение Печати", 'stamp', '');
	$show .= Submitter('add_company', $LANG['Add']);
	$show .= endTable();
	return $show;
}

function save() {
	global $DB;
	return $DB->make_update('Companys', 'CompanyID='.$this->id,
			array('title' => $this->title,
				'inn' => $this->inn,
				'kpp' => $this->kpp,
				'c_rasschet' => $this->c_rasschet,
				'c_bankname' => $this->c_bankname,
				'c_bik' => $this->c_bik,
				'c_k_rasschet' => $this->c_k_rasschet,
				'p_rasschet' => $this->p_rasschet,
				'p_bankname' => $this->p_bankname,
				'p_bik' => $this->p_bik,
				'p_k_rasschet' => $this->p_k_rasschet,
				'general_manager' => $this->general_manager,
				'chief_accountant' => $this->chief_accountant,
				'nds' => $this->nds,
				'stamp' => $this->stamp
			) );
}

function ShowActions() {
	global $LANG;
	$show = beginTable($LANG['Actions']);
	$show .= "<tr><td><a href=\"?object=company&amp;action=edit&CompanyID=$this->id\"><img src=\"images/edit.png\">$LANG[Edit]</a></td></tr>";
	$show .= "<tr><td><a href=\"?object=company&amp;action=delete&CompanyID=$this->id\"><img src=\"images/delete.png\">$LANG[Delete]</a></td></tr>";
	$show .= endTable();
	return $show;
}

public static function load_Companys( $columns = null, $filter = null, $sortby = null, $sortdir = null, $limit = null, $start = null )
{
  global $DB;
  $result = $DB->make_select('Companys', $columns, $filter, $sortby, $sortdir, $limit, $start );
  $dbo_array = array();
  while( $data = $DB->row($result) )
    {
      $dbo = new Company();
      $dbo->load($data);
      $dbo_array[] = $dbo;
    }

  return $dbo_array;
}

static function add_company($obj) {
	global $DB;
	if( !$DB->make_insert('Companys',
				array( 'title' => $obj->title,
				       'inn' => $obj->inn,
				       'kpp' => $obj->kpp,
				       'c_rasschet' => $obj->c_rasschet,
				       'c_bankname' => $obj->c_bankname,
				       'c_bik' => $obj->c_bik,
				       'c_k_rasschet' => $obj->c_k_rasschet,
				       'p_rasschet' => $obj->p_rasschet,
				       'p_bankname' => $obj->p_bankname,
				       'p_bik' => $obj->p_bik,
				       'p_k_rasschet' => $obj->p_k_rasschet,
				       'general_manager' => $obj->general_manager,
				       'chief_accountant' => $obj->chief_accountant,
				       'nds' => $obj->nds,
					'stamp' => $obj->stamp
					) )) return false;
	$id = $DB->insert_id();
	if($id == false) pdata( 'Could not retrieve ID from previous INSERT!' );
	elseif($id == 0) pdata( 'Previous INSERT did not generate an ID' );
	$obj->id = $id;
	return true;
}

}
