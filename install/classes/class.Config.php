<?php
/*
 *      class.Config.php
 *
 *
 * 		Configuration file editor
 *
 * 
 *      Copyright 2011 Artem Zhirkov <artemz@artemz-desktop>
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
class Config {
    public $configdata = null;
    public static $c_instance = NULL;
    public static $configfile;
    function __construct($configfile = "config.php"){
	self::$configfile = $configfile;
	$file = fopen(self::$configfile, "r");
	while(!feof($file)){
	    $this->configdata .= fread($file, 1024);
	}
	fclose($file);
    }
    private function update(){
		if(!preg_match("/define\(.{1}[a-zA-Z0-9]+.*\)/",$this->configdata)){
			throw new Exception("Unable to update configuration file with that wrong data");
		}
	$file = fopen(self::$configfile, "w+");
	fwrite($file, $this->configdata);
	fclose($file);
    }
    public function set($parameter, $value){
	if($this->configdata){
	    //if(preg_match("\[".$group."\]\n+.*\n*".$parameter."=.+",$this->configdata)){
	    //define('DATABASE_PREFIX', 'mc_');
		//$this->configdata = preg_replace("/\n".$parameter."=.*/","\n".$parameter."=".$value, $this->configdata);
		$this->configdata = preg_replace("/define\(.{1}".$parameter.".{1},.*\);/","define('".$parameter."', '".$value."');", $this->configdata);
		$this->update();
	    //}
	}
    }
    
}
?>
