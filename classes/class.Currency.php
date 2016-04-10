<?php
/*
 *      class.Currency.php
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
class Currency extends Base {
	public static $derivatives = array();
	public $name, $desc, $rate, $symbol;
	private $db = NULL;
	private $raw;
	public function __construct(){
		parent::__construct();
		$this->db = DB::getInstance();
	}
	public static function properties(){
		return array(
			'required' => array('name', 'rate')
		);
	}
	function GetDefaultCurrency($userid = '-1'){
		$settings = Settings::getInstance();
		$defcursymb = $settings->Get('system.currency', $userid);
		return $this->FetchData($this->GetID($defcursymb, 'name'));
	}
	//amount in default currency
	//currid - id of converting currency
	function FormatCurrency($amount, $currid = NULL, $userid = '-1', $noformat=false){
		$settings = Settings::getInstance();
		if(!is_numeric($amount) || !is_numeric($userid)){
			throw new Exception("user id or amount is not numeric");
		}
		if(is_numeric($currid)){
			$currdata = $this->FetchData($currid);
		} else {
			$currdata = $this->FetchData($this->GetID($settings->Get('system.currency', $userid), 'name'));
		}
		if(strlen($currdata['symbol']) > 0){
			$str = '%i '.$currdata['symbol'];
		} else {
			$str = '%i '.strtoupper($currdata['name']);
		}
		if($noformat){
			return $amount*$currdata['rate'];
		} else {
			return money_format($str, $amount*$currdata['rate']);
		}
		
	}
	public function UpdateCurrs(){
		$setting = Settings::getInstance();
		$systemcurr = $setting->Get('system.currency');
		$systemcurrrate = NULL;
		$provider = $setting->Get('system.currency.autoupdate').'curprovider';
		$ecb = $provider::getInstance();
		$provinfo = $ecb->Info();
		$currs = $ecb->GetButch();
		//find system curr exchange rate
		for($i=0;$i<count($currs);$i++){
			if(array_key_exists($systemcurr,$currs[$i])){
				$systemcurrrate = $currs[$i][$systemcurr];
			}
		}
		if($systemcurr == $provinfo['default']){
			$systemcurrrate = 1;
		} elseif($systemcurrrate == NULL) {
			throw new Exception("system default currency doesnt exists in provided data");
		}
		for($i=0;$i<count($currs);$i++){
			$tempkey = array_keys($currs[$i]);
			$id = $this->GetID($tempkey[0],'name');
			$rate = $currs[$i][$tempkey[0]]/$systemcurrrate;
			if(!$id){
				$this->Create(array('name' => $tempkey[0], 'rate' => $rate));
			} else {
				$this->Update('rate', $rate, $id);
			}

		}
		return true;
	}
	function RetriveAllModules(){
		$dir = SYSTEM_PATH.'/curmodules';
		if($handle = opendir($dir)){
			while(false != ($file = readdir($handle))){
				if(strstr($file, 'php') && sizeof(explode('.', $file)) < 3){
					$name = explode('.', $file);
					
					$prov = call_user_func(array("ecbcurprovider",'getInstance'));
					$this->data[] = array('name' => $name[0], 'info' => $prov->Info());
				}
			}
		return $this->data;
		} else {
			throw new Exception("Unable to open directory with currency modules: ".$dir);
		}
	}
}
?>
