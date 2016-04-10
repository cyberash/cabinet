<?php
/*
 *      class.TicketChange.php
 *      
 *      Copyright 2010 Artem Zhirkov <artemz@artemz-desktop>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

?>
<?
class TicketChange extends Base{
	public $db, $raw, $id;
	public static $derivatives = array();
	public function __construct(){
		parent::__construct();
		$this->db = DB::getInstance();
	}
	public function Create($ticketid,$userid,$message,$type="reply",$date=''){
		$time = Time::getInstance();
		if(!is_numeric($ticketid) || !is_numeric($userid)){
			throw new Exception("Ticket or User ID is not numeric");
		}
		if(!preg_match('/^(status|reply)$/i', $type)){
			throw new Exception("change type in wrong format");
		}
		if($date == ''){
			$date = $time->UtoM(time());
		} elseif(!$time->validateTime($date)){
			throw new Exception("Date in wrong format");
		}
		$array = array("userid" => $userid, "message" => $message, "type" => $type, "ticketid" => $ticketid, "date" => $date);
		$this->raw = $this->db->query_insert("TicketChange", $array);
		return $this->raw;
	}
	public function NewTicket($subject, $message, $depid, $creatorid, $userid){
		if(strlen($subject) < 3 || strlen($subject) > 255){
			throw new Exception("ticket's subject too long or too short");
		}
		if(!is_numeric($depid) || !is_numeric($creatorid) || !is_numeric($userid)){
			throw new Exception("department, creator or user id in wrong format");
		}
		$user = User::getInstance();
		$dep = Department::getInstance();
		$depdata = $dep->FetchData($depid);
		$crdata = $user->FetchData($creatorid);
		$userdata = $user->FetchData($userid);
		if($userdata['status'] == 'Admin'){
			throw new Exception("Ticket cannot be created for admin");
		} elseif($depdata['type'] == 'Private'){
			throw new Exception("Users cannot submit tickets to private deprtments");
		}
		$ticket = Ticket::getInstance();
		$tid = $ticket->Create(array('depid' => $depid,'subject' => $subject, 'userid' => $userid));
		$this->Create($tid,$creatorid,$message);
		return $tid;
	}
	public function ReplyTicket($message,$ticketid,$userid){
		if(!is_numeric($ticketid) || !is_numeric($userid)){
			throw new Exception("User or ticket id is not numeric");
		}
		$ticket = Ticket::getInstance();
		$user = User::getInstance();
		$userdata = $user->FetchData($userid);
		$tcdata = $ticket->FetchData($ticketid);
		if($userdata['status'] != 'Admin' && $userid != $tcdata['userid']){
			throw new Exception("Users cannot reply to ");
		}
		$this->raw = $this->Create($ticketid,$userid,$message);
		return $this->raw;
	}
}
?>
