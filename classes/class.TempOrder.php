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

class TempOrder extends Base {
	public $data, $time;
	public static $derivatives = array();
	public static function properties(){
		return array(
			'required' => array('data'),
			'values' => array('time' => Time::getInstance()->UtoM(time()))
			
		);
	}
	public function __construct(){
		parent::__construct();
	}
	public function GetOverdue(){
		$time = Time::getInstance();
		$oneday = $time->rem_date($time->UtoM(time()), 1);
		return $this->GetButch('', '`time` < "'.$oneday.'"', 'name');
	}
}

?>
