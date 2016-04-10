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

class Password {

# Use crypt() salt N, a positive number <= 4096.  If random
# seeds are desired, specify a zero value (the default).
public $CryptSalt = 0;

public $MinChars;
public $MaxChars;
private static $p_instance = NULL;

protected $StringUsed = 'ABCDEFGHIJKLMNPQRSTUVWXYabcdefghijmnopqrstuvwxyz0123456789';

# Crypt set on the basis of options interpreted:
#    false   Password generation will be done (w/o encryption).
#    true   Password generation and encryption will be done.
public $Crypt = 0;

# If crypt mode is set to `password generation will be done, not yet known if
# encryption will be done,' fill in the blanks.  If crypt mode is not yet set,
# it's going to be `password generation will be done, encryption will not.'
public static function getInstance(){
	if(self::$p_instance == NULL){
		$p_instance = new self();
	}
	return $p_instance;
}
function __construct($MinChars=6, $MaxChars=8, $Crypt=false) {
	//if($this->CryptMode == 1) $this->CryptMode += ($this->Crypt + 1);
	//elseif($this->CryptMode == 0) $this->CryptMode = 2;

	# If a cryptographic seed value was specified, process it.
	if(!empty($this->CryptSalt)) {
		# Make sure the seed value is between 0 and 4095, inclusive.
		if($this->CryptSalt < 0 || $this->CryptSalt > 4096)
			die("Crypt() seed value must be zero thru 4096\n");
	};
	
	$this->ValString = strlen($this->StringUsed) - 1;
	$this->MinChars = $MinChars;
	$this->MaxChars = $MaxChars;
	$this->Crypt = $Crypt;

}

# sub CryptPassword(A, B):  Encrypt the password provided; keep a running
# list of codes used as long as B is true.
function CryptPassword($password, $B){
	# Characters for salt construction.
	$SaltList='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./';

	$ThisSeed = $this->CryptSalt;
	if($ThisSeed) $ThisSeed--;
	else {
		if($B) $UsedSeed = array();
		$ThisSeed = mt_rand(0, 4095);
		do{
			$ThisSeed = mt_rand(0, 4095);
		}while(!isset($UsedSeed[$ThisSeed]));
		$UsedSeed[$ThisSeed] = $ThisSeed;
	}
	// Generate a crypt salt string from a number from 0 through 4095.
	$i = $ThisSeed >> 6;
	$j = $ThisSeed % 64;
	$SaltOut = substr($SaltList, $i, 1).substr($SaltList, $j, 1);

	$crypt = crypt($password, $SaltOut);
	return $crypt;
}

# Process the password provided.
function ProcessPassword() {
	$CharFormat = $this->MaxChars + 3;

	$len = mt_rand($this->MinChars, $this->MaxChars);
	$password = '';
	for ($i=0; $i < $len; $i++) $password .= substr($this->StringUsed, mt_rand(0, $this->ValString), 1);

	if ($this->Crypt) {
		$password = sprintf('%-'.$CharFormat.'s', $password).' '.$this->CryptPassword($password, $i);
	}
	
	return $password;
}


}
