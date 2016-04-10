<?php
/*
 *      class.Time.php
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
class Time{
	public static $t_instance = NULL;
	public static function getInstance(){
		if(self::$t_instance == NULL){
			self::$t_instance = new self();
		}
		return self::$t_instance;
	}
	/*
	 * Converting mysql TIMESTAMP to UNIX date
	 */
	public function MtoU($mysqltime){
		return strtotime($mysqltime);
	}
	/*
	 * Converting UNIX time to mysql TIMESTAMP
	 */
	public function UtoM($unixtime){
		return date('Y-m-d H:i:s', $unixtime);
	}
	public function add_date($od,$day=0,$mth=0,$yr=0){
		$cd = strtotime($od);
		return date('Y-m-d H:i:s', mktime( date('H',$cd), date('i',$cd), date('s',$cd), date('m',$cd)+$mth, date('d',$cd)+$day, date('Y',$cd)+$yr));
	}
	public function rem_date($od,$day=0,$mth=0,$yr=0){
		$cd = strtotime($od);
		return date('Y-m-d H:i:s', mktime( date('H',$cd), date('i',$cd), date('s',$cd), date('m',$cd)-$mth, date('d',$cd)-$day, date('Y',$cd)-$yr));
	}
/* We shouldn't use strtotime function to compare dates because its susceptible to the 2038 bug
 */
	public function diff_dates($first_d, $second_d, $out_format){
		$first_d = strtotime($first_d);
		$second_d = strtotime($second_d);
		if($second_d > $first_d) {
			return false;
		} else {
			$diff = abs($first_d - $second_d);
			switch($out_format){
				case 'M':
					return round($diff/iMON);
				break;
				case 'D':
					return round($diff/iDAY);
				break;
				case 'Y':
					return round($diff/iYAR);
				break;
				default:
					throw new Exception("Unknown time format");
			}
		}
	}
	public function validateTime($timestamp){
		if(preg_match('/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|11)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468][048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(02)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02)(-)(29)))(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))?$/i', $timestamp)){
			return true;
		} else {
			return false;
		}
	}
}

?>
