<?php
/*
 *      ecbprovider.php
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
class ecbcurprovider{
	public static $i = NULL;
	public $ecb_url = 'http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml';
	private $raw = NULL;
	public static function getInstance(){
		if(self::$i == NULL){
			self::$i = new self();
		}
		return self::$i;
	}
	public function Info(){
		return array(
			'default' => 'eur',
			'name' => 'European Central Bank'
		);
	}
	public function GetButch(){
		$data = get_web_page($this->ecb_url);
		$retarray = array();
		$xml = new SimpleXMLElement($data['content']);
		$arr = get_object_vars($xml->Cube->Cube);
		for($i=0;$i<count($arr["Cube"]);$i++){
			$this->raw = get_object_vars($arr["Cube"][$i]);
			$retarray[][strtolower($this->raw["@attributes"]["currency"])] = floatval($this->raw["@attributes"]["rate"]);
		}
		//var_dump($retarray);
		return $retarray;
	}
}

?>
