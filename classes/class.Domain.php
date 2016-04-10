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

class Domain {

public $id;
public $name;
public $username;
public $ServerID;
public $AccountID;
public $status;
public $expirate;
public $contact;

static $ver = '1.0';

function __construct( $id = '' ) {
	global $DB, $Domains;
	if(!empty($id)) {
		$this->id = $id;
		$result = $DB->make_select('Domains', '*', '`DomainID`='.$this->id);
		if(!$row = $DB->row($result)) {
			log_error("DomainID $this->id not found", __FILE__, __LINE__);
			pdata("DomainID $this->id not found\n");
		}
		$this->load($row);
		$Domains[$id] = $this;
	}
}


public function load( $data ) {
	$this->id = isset($data['DomainID']) ? (int) $data['DomainID'] : 0;
	$this->name = $data['name'];
	$this->username = $data['username'];
	$this->ServerID = $data['ServerID'];
	$this->status = $data['status'];
	$this->expirate = $data['expirate'];
}

public function show() {
	global $LANG;
	$show = beginTable("$LANG[Details] $LANG[Domain]: $this->id, $this->name");
	$show .= StaticField($LANG['ID'], 	$this->id);
	$show .= StaticField($LANG['Title'], 	$this->name);
	$show .= StaticField($LANG['Description'], 	$this->description);
	$show .= endTable();
	return $show;
}

public function edit() {
	global $LANG;
	$show = openForm(iSELF.'?object=domain&amp;action=edit2');
	$show .= beginTable("$LANG[Details] $LANG[Domain]: $this->id , $this->name");
	$show .= HiddenField('DomainID', $this->id);
	$show .= TextField($LANG['Title'], 'name',	 $this->name);
	$show .= ArrayDropBox($lClient_type, 'Owner', $acct_row['Owner'], array('person','person', 'company','company', 'businessman','businessman'));
	$show .= LargeTextField($LANG['Content'], 'content',	 $this->content);
	$show .= Submitter('edit_domain', $LANG['Edit']);
	$show .= endTable();
	return $show;
}

public function add() {
	global $LANG;
	$show = openForm(iSELF.'?object=domain&amp;action=add2');
	$show .= beginTable("$LANG[Add] $LANG[Domain]");
	$show .= TextField($LANG['Title'], 'name', '');
	$show .= TextField($LANG['CreateDate'], 'createdate', '');
	$show .= TextField($LANG['User'], 'autor', '');
	$show .= TextField($LANG['Content'], 'content', '');
	$show .= Submitter('add_domain', $LANG['Add']);
	$show .= endTable();
	return $show;
}

public function save() {
	global $DB;
	return $DB->make_update('Domains', '`DomainID`='.$this->id ,
				array( 'name' => $this->name,
				       'content' => $this->content,
				       'createdate' => $this->createdate,
				       'autor' => $this->autor
					) );
}

public function ShowActions() {
	global $LANG;
	$show = beginTable($LANG['Actions']);
	$show .= "<tr><td><a href='?object=domain&amp;action=edit&amp;DomainID=$this->id'><img src='images/edit.png' alt='edit' />$LANG[Edit] </a></td></tr>";
	$show .= "<tr><td><a href='?object=domain&amp;action=delete&amp;DomainID=$this->id'><img src='images/delete.png' alt='delete' />$LANG[Delete] </a></td></tr>";
	$show .= endTable();
	return $show;
}


public static function load_domains( $columns = null, $filter = null, $sortby = null, $sortdir = null, $limit = null, $start = null ) {
	global $DB;
	$result = $DB->make_select('Domains', $columns, $filter, $sortby, $sortdir, $limit, $start );
	$domain_array = array();
	while($data = $DB->row($result)) {
		$domain = new Domain();
		$domain->load($data);
		$domain_array[] = $domain;
	}
	return $domain_array;
}

static function add_domain($obj) {
	global $DB;
	if( !$DB->make_insert( 'Domains',
				array( 'name' => $obj->name,
				       'content' => $obj->content,
				       'createdate' => $obj->createdate,
				       'autor' => $obj->autor
					) )) return false;

	$id = $DB->insert_id();
	if($id == false) pdata( 'Could not retrieve ID from previous INSERT!' );
	elseif($id == 0) pdata( 'Previous INSERT did not generate an ID' );
	$obj->id = $id;
	return true;
}

static function list_domain($pagenum=1){
global $LANG, $DB;
$page_posts = 20;
$spos = ($pagenum-1)*$page_posts;
$objs = Domain::load_domains('*', '', 'DomainID', 'DESC', $page_posts, $spos);
$count = $DB->count_objs('Domains');
$result = beginTable("$count $LANG[Domains]",'100%');
$result .= makeTH($LANG['ID'], $LANG['Title']);
foreach($objs as $obj) $result .= makeTD(
	"<a href='?object=domain&amp;action=show&amp;DomainID=$obj->id'>$obj->id</a>",
	$obj->name
	);
$result .= '<tr><td colspan="10" class="nob">';
$result .= makePageNav($pagenum,$page_posts,$count,iSELF.'?object=domain&amp;action=list&amp;');
$result .= '</td></tr>';
$result .= endTable();
return $result;
}

}
