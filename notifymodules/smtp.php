<?php
/*
 *      smtp.php
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
class smtpnotifymodule {
	public $server;
    public $port;
    public $crypto;
    public $user;
    public $pass;
	public static $module_instance = NULL;
    private $timeout = '45';
    private $localhost = 'localhost';
    private $nl = "\r\n";
    private $conn;
    static function getInstance(){
		if(self::$module_instance == NULL){
			self::$module_instance = new self();
		}
		return self::$module_instance;
		
	}
	function OperateRequirements(){
		$arr = array(
			0 => array('type'=>'text', 'label'=>'SMTP Server', 'name' => 'server'),
			1 => array('type'=>'text', 'label'=>'SMTP Server Port', 'name' => 'port'),
			2 => array('type'=>'text', 'label'=>'Username', 'name' => 'user'),
			3 => array('type'=>'password', 'label'=>'Password', 'name' => 'pass'),
			4 => array('type'=>'text', 'label'=>'Encryption Type', 'name' => 'crypto')
		);
		return $arr;
	}
	function Info(){
		return array('name' => 'SMTP Mail Module', 'desc' => 'Sending mails throught SMTP relays', 'shortname' => 'E-mail');
	}
	function Send($reqs, $to, $subject, $message){
		$this->server = $reqs['server'];
		$this->port = $reqs['port'];
		$this->crypto = $reqs['crypto'];
		$this->user = $reqs['user'];
		$this->pass = $reqs['pass'];
		//var_dump($reqs);
		$from = $this->user;
		$this->connect();
        $this->auth();
		fputs($this->conn, 'MAIL FROM: <'. $from .'>'. $this->nl);
        fgets($this->conn);
        fputs($this->conn, 'RCPT TO: <'. $to .'>'. $this->nl);
        fgets($this->conn);
        fputs($this->conn, 'DATA'. $this->nl);
        fgets($this->conn);
        fputs($this->conn,
            'From: '. $from .$this->nl.
            'To: '. $to .$this->nl.
            'Subject: '. $subject .$this->nl.
	    'MIME-Version: 1.0'.$this->nl.
	    'Content-Type: text/html; charset=UTF-8'. $this->nl.
            $this->nl.
            $message . $this->nl.
            '.' .$this->nl
        );
        fgets($this->conn);
        return true;
	}
	function connect() {
        $this->crypto = strtolower(trim($this->crypto));
        $this->server = strtolower(trim($this->server));
		//var_dump($this->port);
        if($this->crypto == 'ssl')
            $this->server = 'ssl://' . $this->server;
        $this->conn = fsockopen(
            $this->server.":".$this->port, $this->port, $errno, $errstr, $this->timeout
        );
        fgets($this->conn);
		//var_dump($errstr);
        return;
    }
    function auth() {
        fputs($this->conn, 'HELO ' . $this->localhost . $this->nl);
        fgets($this->conn);
        if($this->crypto == 'tls') {
            fputs($this->conn, 'STARTTLS' . $this->nl);
            fgets($this->conn);
            stream_socket_enable_crypto(
                $this->conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT
            );
            fputs($this->conn, 'HELO ' . $this->localhost . $this->nl);
            fgets($this->conn);
        }
        if($this->server != 'localhost') {
            fputs($this->conn, 'AUTH LOGIN' . $this->nl);
            fgets($this->conn);
            fputs($this->conn, base64_encode($this->user) . $this->nl);
            fgets($this->conn);
            fputs($this->conn, base64_encode($this->pass) . $this->nl);
            fgets($this->conn);
        }
        return;
    }
    function __destruct() {
        fputs($this->conn, 'QUIT' . $this->nl);
        fgets($this->conn);
        fclose($this->conn);
    }
}
?>
