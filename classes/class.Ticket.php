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

?>
<?
class Ticket extends Base{
	public $db, $raw, $id, $userid, $depid,$subject,$status,$date;
	public static $derivatives = array('TicketChange' => 'ticketid');
	public function __construct(){
		parent::__construct();
		$this->db = DB::getInstance();
	}
	public static function properties(){
		$time = Time::getInstance();
		return array(
			'required' => array('depid', 'userid', 'subject'),
			'values' => array('date' => $time->UtoM(time()), 'status' => 'Support')
			
		);
	}
}
?>
