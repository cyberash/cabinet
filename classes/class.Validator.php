<?php
/*
 *      class.Validator.php
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
class Validator {
	private $data = NULL;

public function __construct($data){
	$this->data = $data;
}
public function email(){
	$isValid = true;
	$atIndex = strrpos($this->data, "@");
	if(is_bool($atIndex) && !$atIndex){
		$isValid = false;
	} else {
		$domain = substr($this->data, $atIndex+1);
		$local = substr($this->data, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if($localLen < 1 || $localLen > 64){
			$isValid = false;
		} elseif($domainLen < 1 || $domainLen > 255){
			$isValid = false;
		} elseif($local[0] == '.' || $local[$localLen-1] == '.'){
			$isValid = false;
		} elseif(preg_match('/\\.\\./', $local)){
			$isValid = false;
		} elseif(!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
			$isValid = false;
		} elseif(preg_match('/\\.\\./', $domain)){
			$isValid = false;
		} elseif(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))){
			if(!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))){
				$isValid = false;
			}
		}
		if($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))){
			$isValid = false;
		}
		return $isValid;
}
}
}
?>
