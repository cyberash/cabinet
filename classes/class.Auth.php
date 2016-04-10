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

# For genarate new hash for username 'admin' and pass '12345', type:
# echo -n "admin12345" | openssl dgst -rmd160
# or
# php -r "echo hash('ripemd160','admin'.'12345');"

# !!! begin user authenticate !!! only check and set variables, don't redirect or die !!!

 // is alredy once try login form
//$login_renty = false;

//$auth = new Auth();
//$userdata = $auth->make_auth();
//unset($auth);

//if($_SERVER['REMOTE_ADDR']=='127.0.0.1') $userdata['rights'] .= '.D';

//define('iUSER_RIGHTS', $userdata['rights']);
//define('iUSER_ID', $userdata['user_id']);
//define('iUSER_NAME', $userdata['user_name']);
//load_theme($userdata['theme']);
//unset($userdata);

class Auth {
	private $raw;
	public $username;
	public $password;
	private $db, $user;
	public function __construct($username, $password){
		$this->username = $username;
		$this->password = $password;
		$this->user = User::getInstance();
	}

function check_auth() {
	if(strlen(trim($this->username)) < 2){
		return false;
	}
	if(!is_numeric($userid = $this->user->GetID($this->username, 'username'))){
		throw new Exception("User not found with username ".$this->username);
	}
	$this->raw = $this->user->FetchData($userid);
	if($this->raw['password'] != md5($this->password)){
		return false;
	} else {
		return true;
	}
}
function get_rights(){
	$this->raw = $this->user->FetchData($this->user->GetID($this->username, 'username'));
	//echo 'status '.$this->raw['status'];
	if(!$this->raw['status'] && $this->raw['status'] != 'Admin' && $this->raw['status'] != 'Active' && $this->raw['status'] != 'Suspended'){
		return false;
	} else {
		return $this->raw['status'];
	}
}
}
?>
