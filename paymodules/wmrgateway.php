<?php
/*
 *      othertestgateway.php
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
class wmrgatewayPaymentGateway {
	public static $g_instance = NULL;
	public static function getInstance(){
		if(self::$g_instance == NULL){
			self::$g_instance = new self();
		}
		return self::$g_instance;
	}
	public function Info(){
		return array('name' => 'WebMoney WMR');
	}
	public function Currency(){
		return array('rub');
	}
	public function OperateRequirements(){
		$arr = array(
		0 => array('type' => 'text', 'label' => 'WMR Purse', 'name'=> 'wmrpurse'),
		1 => array('type' => 'text', 'label' => 'Secrect Key', 'name' => 'key')
		);
		return $arr;
	}
	/*
	 * $data_array:
	 * 'userdata' => array();
	 * 'invoice' => array();
	 * 'currency' => string
	 */
	public function Form($operate_array, $data_array){
		return '         
		<form action="https://merchant.webmoney.ru/lmi/payment.asp" method="post">
		<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$data_array['invoice']['amount'].'">
		<input type="hidden" name="LMI_PAYMENT_DESC" value="'.$data_array['invoice']['comment'].'">
		<input type="hidden" name="LMI_PAYMENT_NO" value="'.$data_array['invoice']['id'].'">
		<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$operate_array['wmrpurse'].'">
		<input type="hidden" name="LMI_SIM_MODE" value="0">
		';
	}
}
?>