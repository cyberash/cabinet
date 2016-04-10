<?php
/*
 *      class.Invoice.php
 *      
 *      Copyright 2010 Artem Zhirkov <zhirkow@yahoo.com>
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
class Invoice extends Base {
	public $invid, $accountid, $orderid, $amount, $status, $datecreated, $datedue, $datepaid, $comment, $transactionid;
	public static $derivatives = array('Transaction' => 'invoiceid');
	public static function properties(){
		return array(
			'required' => array(),
			'values' => array()
		);
	}
	private $db = NULL;
	private $raw = NULL;
	public function __construct(){
		$this->db = DB::getInstance();
		parent::__construct();
	}
	public function Create($accountid, $orderid = -1, $amount=0, $duedate, $status='Unpaid', $comment ='', $datecreated='', $datepaid = '', $transactionid='-1'){
		$time = Time::getInstance();
		if(!is_numeric($accountid) || !is_numeric($transactionid) || !is_numeric($orderid)){
			throw new Exception("problems with invoice data");
		}
		$amount = floatval($amount);
		if($datecreated == ''){
			$datecreated = $time->UtoM(time());
		} elseif(!($time->validateTime($datecreated))){
			throw new Exception("Date of creation in wrong format!");
		}
		if(!($time->validateTime($duedate)) && $duedate != '0000-00-00 00:00:00'){
			throw new Exception("Due date in wrong format");
		}
		if(!preg_match('/^(Paid|Unpaid|Cancelled)$/i', $status)){
			throw new Exception("Invoice status in wrong format");
		}
		if($datepaid != '' && !($time->validateTime($datepaid))){
			throw new Exception("Date of payment in wrong format");
		}
		$this->data = array('accountid' => $accountid, 'orderid' => $orderid, 'amount' => $amount, 'status' => $status, 'datecreated' => $datecreated, 'datedue' => $duedate, 'datepaid' => $datepaid, 'comment' => $comment, 'transactionid' => $transactionid);
		$this->raw = $this->db->query_insert('Invoice', $this->data);
		if(is_numeric($this->raw)){
			$this->invid = $this->raw;
			return $this->raw;
		} else {
			return false;
		}
	}
}
?>
