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

class Package extends Base {
	public $name, $presetid, $price, $paytype, $stock, $desc;
	public static $derivatives = array();
	public static function properties(){
		return array(
			'required' => array('name', 'presetid', 'price', 'paytype'),
			'values' => array('stock' => '-1')
			
		);
	}
	public function __construct(){
		parent::__construct();
	}
	public function GetName($id){
		if(!is_numeric($id)) throw new Exception("Package ID in wrong format");
		$data = $this->FetchData($id);
		return $data['name'];
	}
	public function GetOrderable(){
		//$this->raw = $this->db->fetch_all_array('SELECT PackageID, name, stock FROM Packages WHERE stock != 0 ORDER BY name DESC');
		return $this->GetButch('', '`stock` != 0', 'name');
	}
}

?>
