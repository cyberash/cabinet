<?php
/*
 *      class.Transaction.php
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
class Transaction extends Base{
	private $db = NULL;
	private $raw = NULL;
	public $transid = NULL;
	public static $derivatives = array();
	public function __construct(){
		$this->db = DB::getInstance();
		parent::__construct();
	}
	public function Create($invoiceid, $gatewayid, $amount, $date=NULL, $comments='', $userid=NULL){
		if(!is_numeric($invoiceid) && !is_float($amount)){
			throw new Exception("Invoice ID or amount in wrong format");
		}
		$inv = Invoice::getInstance();
		$invdata = $inv->FetchData($invoiceid);
		$time = Time::getInstance();
		if($date == NULL){
			$date = $time->UtoM(time());
		}
		$this->raw = $this->db->query_insert('Transaction', array('invoiceid' => $invoiceid, 'customerid' => $invdata['accountid'], 'date' => $date, 'amount' => $amount, 'gatewayid' => $gatewayid, 'comments' => $comments));
		$inv->Update('status','Paid', $invoiceid);
		$inv->Update('transactionid',$this->raw, $invoiceid);
		$inv->Update('datepaid', $date, $invoiceid);
		return $this->raw;
	}
}
?>
