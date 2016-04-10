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

class Letter {

public $id;
public $title;
public $name;
public $ResellerID;
public $from;
public $subject;
public $body;

// Возращает тело письма ища по `LetterID` или `name`
function __construct($id = 0, $ResellerID = '') {
	global $DB, $Letters;
	if(!empty($id)) {
		if(is_int($id)) {
			$result = $DB->make_select('Letters', '*', '`LetterID`='.$id);
		}else{
			$result = $DB->make_select('Letters', '*', '`name`="'.$id.'" AND (`ResellerID` IS NULL OR `ResellerID`="'.$ResellerID.'")', 'ResellerID', 'DESC', 1);
		}
		$row = $DB->row($result);
		if(!$row) {
			log_error("Letter ID $id not found", __FILE__, __LINE__);
			//pdata("Letter ID $id not found\n");
		}
		$this->load($row);
		$Letters[$id] = $this;
	} else $this->id = 0;
}


public function load( $data ) {
	$this->id = empty($data['LetterID']) ? 0 : (int) $data['LetterID'];
	$this->title = $data['title'];
	$this->name = $data['name'];
	$this->ResellerID = $data['ResellerID'];
	$this->from = $data['from'];
	$this->subject = $data['subject'];
	$this->body = $data['body'];
}

public function show() {
	global $LANG;
	$show = beginTable("$LANG[Details] $LANG[Letter]: $this->title");
	$show .= '<tr><td colspan="2"><table><tr><td><a href="?object=letter&amp;action=edit&amp;LetterID='.$this->id.'"><img src="images/edit.png">'.$LANG['Edit'].' </a></td>';
	$show .= '<td><a href="?object=letter&amp;action=delete&amp;LetterID='.$this->id.'"><img src="images/delete.png">'.$LANG['Delete'].' </a></td>';
	$show .= '<td><a href="?object=letter&amp;action=list"><img src="images/list.png">'.$LANG['List'].' '.$LANG['Letters'].'</a></td></tr></tr></table></td></tr>';
	$show .= StaticField($LANG['Title'], 	$this->title);
	$show .= StaticField($LANG['ID'], $this->name);
	$show .= StaticField($LANG['ResellerID'], $this->ResellerID);
	$show .= StaticField('From', 	$this->from);
	$show .= StaticField('Subject', $this->subject);
	$show .= StaticField('Body', '<pre>'.$this->body.'</pre>');
	$show .= endTable();
	return $show;
}

public function edit() {
	global $LANG;
	$show = openForm(iSELF."?object=letter&amp;action=edit2");
	$show .= HiddenField('LetterID', $this->id);
	$show .= beginTable("$LANG[Details] $LANG[Letter]: $this->id , $this->title");
	$show .= TextField($LANG['ID'], 'name', $this->name);
	$show .= TextField($LANG['ResellerID'], 'ResellerID', $this->ResellerID);
	$show .= TextField($LANG['Title'], 'title',	 $this->title);
	$show .= TextField('From', 'from',	 $this->from);
	$show .= TextField('Subject', 'subject',	 $this->subject);
        $show .= '<tr><td valign="top"><b>Body:</b></td><td><textarea name="body" cols="120" rows="40" wrap="physical">'.html_entity_decode($this->body,ENT_QUOTES,'UTF-8').'</textarea></td></tr>';
	$show .= Submitter('edit_letter', $LANG['Edit']);
	$show .= endTable();
	$show .= closeForm();
	return $show;
}

public function add() {
	global $LANG;
	$show = openForm(iSELF."?object=letter&amp;action=add2");
	$show .= beginTable("$LANG[Add] $LANG[Letter]");
	$show .= HiddenField('name', '');
	$show .= HiddenField('ResellerID', '');
	$show .= TextField($LANG['Title'], 'title', '');
	$show .= TextField('From', 'from', '');
	$show .= TextField('Subject', 'subject', '');
        $show .= '<tr><td valign="top"><b>Body:</b></td><td><textarea name="body" cols="120" rows="40" wrap="physical"></textarea></td></tr>';
	$show .= Submitter("add_letter", $LANG['Add']);
	$show .= endTable();
	return $show;
}

public function save() {
	global $DB;
	return $DB->make_update('Letters', "LetterID = $this->id" ,
				array( "title" => $this->title,
				       "from" => $this->from,
				       "subject" => $this->subject,
				       "body" => $this->body
					) );
}

public function ShowActions() {
	global $LANG;
	//$show .= '<tr><td>'.$LANG['Actions'].'</td>';
	$show = '<tr><td><a href="?object=letter&amp;action=edit&amp;LetterID='.$this->id.'"><img src="images/edit.png">'.$LANG['Edit'].' </a></td>';
	$show .= '<td><a href="?object=letter&amp;action=delete&amp;LetterID='.$this->id.'"><img src="images/delete.png">'.$LANG['Delete'].' </a></td></tr>';
	return $show;
}

static public function load_letters( $columns = null, $filter = null, $sortby = null, $sortdir = null, $limit = null, $start = null ) {
	global $DB;
	$result = $DB->make_select('Letters', $columns, $filter, $sortby, $sortdir, $limit, $start );
	$letter_array = array();
	while( $data = $DB->row($result) ) {
		$letter = new Letter();
		$letter->load($data);
		$letter_array[] = $letter;
	}
	return $letter_array;
}

static public function add_letter($obj) {
	global $DB;
	if( !$DB->make_insert('Letters',
				array( 'title' => $obj->title,
				       'from' => $obj->from,
				       'subject' => $obj->subject,
				       'body' => $obj->body
					) )) return false;
	$id = $DB->insert_id();
	if($id == false) pdata( 'Could not retrieve ID from previous INSERT!' );
	elseif($id == 0) pdata( 'Previous INSERT did not generate an ID' );
	$obj->id = $id;
	return true;
}

}
