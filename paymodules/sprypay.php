<?php
/*
 *      sprypay.php
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
class sprypayPaymentGateway {
	public static $g_instance = NULL;
	public static function getInstance(){
		if(self::$g_instance == NULL){
			self::$g_instance = new self();
		}
		return self::$g_instance;
	}
	public function Info(){
		return array('name' => 'SpryPay SPPI');
	}
	public function Currency(){
		return array('usd', 'rur', 'eur');
	}
	public function OperateRequirements(){
		$arr = array(
		0 => array('type' => 'text', 'label' => 'Shop ID', 'name'=> 'shopid'),
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
		$lang = Lang::getInstance();
		if(!preg_match('/(en|ru)/i', $userlang = $lang->GetLang4User($data_array['invoice']['accountid']))){
			$userlang = 'ru';
		}
		return '         
		<form action="https://sprypay.ru/sppi/" method="post">
		<input type="hidden" name="spShopId" value="'.$operate_array['shopid'].'">
		<input type="hidden" name="spShopPaymentId" value="'.$data_array['invoice']['id'].'">
		<input type="hidden" name="spAmount" value="'.$data_array['invoice']['amount'].'">
		<input type="hidden" name="spCurrency" value="'.$data_array['currency'].'">
		<input type="hidden" name="spPurpose" value="'.$data_array['invoice']['comment'].'">
		<input type="hidden" name="spUserEmail" value="'.$data_array['userdata']['email'].'">
		<input type="hidden" name="lang" value="'.$userlang.'">
		';
	}
}
?>