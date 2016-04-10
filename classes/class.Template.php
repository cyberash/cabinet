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

class ReplaceTokens {
	
static $obj;

function __construct($data) {
	foreach($data as $key=>$val) $this->$key = $val;
}

static protected function run_token_handler($matches) {
	return ReplaceTokens::get_token_vars($matches);
}

static protected function get_token_vars($matches) {
	//raw($matches); return '';
	$matches = explode('->',$matches[1]);
	//unset($matches[0]);
	$v = ReplaceTokens::$obj;
	foreach($matches as $match) {
		$v = $v->$match;
		//if(property_exists($v,$match)) $v = $v->$match; else{ $v = ''; break; }
	}
	return $v;
}

static public function replace($template,$obj) {
	//$template = 'eghurrrr {contact->Email->zone} wrnvwirw';
	ReplaceTokens::$obj = $obj;
	//return preg_replace('!{([\w|\->]+)}!e', '\$data_array[\'$1\']',  $Template);
	//preg_match('/{(?:[.]+\->[.]*)+}/',$template,$matches); raw($matches); return '';
	//return preg_replace_callback('!{(?:([0-9A-Za-z_]+)|\->([0-9A-Za-z_]+))+}!', array('ReplaceTokens','get_token_vars'), $template);
	return preg_replace_callback('!{([0-9A-Za-z_\->]+)}!', array('ReplaceTokens','run_token_handler'), $template);
}

}
