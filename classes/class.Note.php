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

class Note {

public $id;
public $category;
public $title;
public $content;
public $opentime;
public $autor;

function __construct( $id = '' ) {
	global $DB, $Notes;
	if(!empty($id)) {
		$this->id = $id;
		$result = $DB->make_select('Notes', '*', '`NoteID`='.$this->id);
		$row = $DB->row($result);
		if(!$row) {
			log_error("Note ID $this->id not found", __FILE__, __LINE__);
			pdata("Note ID $this->id not found\n");
		}
		$this->load($row);
		$Notes[$id] = $this;
	}
}


public function load( $data ) {
	$this->id = isset($data['NoteID']) ? (int) $data['NoteID'] : 0;
	$this->category = $data['category'];
	$this->title = $data['title'];
	$this->content = $data['content'];
	$this->opentime = $data['opentime'];
	$this->autor = $data['autor'];
}

public function show() {
	global $LANG;
	$show = beginTable("$LANG[Details] $LANG[Note]: $this->id, $this->title");
	$show .= StaticField($LANG['Title'], 	$this->title);
	$show .= StaticField('Категория', $this->category);
	$show .= StaticField($LANG['Content'], html_entity_decode($this->content,ENT_QUOTES,'UTF-8'));
	$show .= StaticField($LANG['CreateDate'], 	$this->opentime);
	$show .= StaticField('Автор', 	$this->autor);
	$show .= endTable();
	return $show;
}

public function edit() {
	global $LANG;
	$show = openForm(iSELF);
	$show .= HiddenField('object', 'note');
	$show .= HiddenField('action', 'edit2');
	$show .= HiddenField('NoteID', $this->id);
	$show .= beginTable("$LANG[Details] $LANG[Note]: $this->id , $this->title");
	$show .= TextField($LANG['Title'], 'title',	 $this->title);
	$show .= TextField($LANG['CreateDate'], 'opentime',	 $this->opentime);
	$show .= TextField('Автор', 'autor',	 $this->autor);
	$show .= ArrayDropBox('Категория', 'category', $this->category, array('user','для пользователей','reseller','для диллеров','admin','для персонала'));
	$show .= LargeTextField($LANG['Content'], 'content', html_entity_decode($this->content,ENT_QUOTES,'UTF-8'));
	$show .= Submitter("edit_note", $LANG['Edit']);
	$show .= endTable();
	return $show;
}

public function add() {
	global $LANG;
	$show = openForm(iSELF);
	$show .= HiddenField('object', 'note');
	$show .= HiddenField('action', 'add2');
	$show .= HiddenField('opentime', '');
	$show .= beginTable("$LANG[Add] $LANG[Note]");
	$show .= TextField($LANG['Title'], 'title', '');
	$show .= TextField('Автор', 'autor', '');
	$show .= ArrayDropBox('Категория', 'category', 'admin', array('user','для пользователей','reseller','для диллеров','admin','для персонала'));
	$show .= LargeTextField($LANG['Content'], 'content', '');
	$show .= Submitter("add_note", $LANG['Add']);
	$show .= endTable();
	return $show;
}

public function save() {
	global $DB;
	return $DB->make_update('Notes', '`NoteID`='.$this->id ,
				array(	'category' => $this->category,
					'title' => $this->title,
					'content' => $this->content,
					'opentime' => $this->opentime,
					'autor' => $this->autor
					) );
}

public function ShowActions() {
	global $LANG;
	$show = beginTable($LANG['Actions']);
	$show .= "<tr><td><a href=\"?object=note&amp;action=edit&amp;NoteID=$this->id\"><img src=\"images/edit.png\">$LANG[Edit] </a></td></tr>";
	$show .= "<tr><td><a href=\"?object=note&amp;action=delete&amp;NoteID=$this->id\"><img src=\"images/delete.png\">$LANG[Delete] </a></td></tr>";
	$show .= endTable();
	return $show;
}

public static function load_notes( $columns = null, $filter = null, $sortby = null, $sortdir = null, $limit = null, $start = null )
{
  global $DB;
  $result = $DB->make_select('Notes', $columns, $filter, $sortby, $sortdir, $limit, $start );
  $dbo_array = array();
  while( $data = $DB->row($result) )
    {
      $dbo = new note();
      $dbo->load($data);
      $dbo_array[] = $dbo;
    }

  return $dbo_array;
}

public static function add_note($obj) {
	global $DB;
	if( !$DB->make_insert('Notes',
				array(	'title' => $obj->title,
						'content' => $obj->content,
						'autor' => $obj->autor
					) )) return false;
	$id = $DB->insert_id();
	if($id == false) pdata( 'Could not retrieve ID from previous INSERT!' );
	elseif($id == 0) pdata( 'Previous INSERT did not generate an ID' );
	$obj->id = $id;
	return true;
}

}
