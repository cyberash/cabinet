<?php
/*
 *      ispconfig3server.php
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
class ispconfig3ServerModule{
	public static $m_instance = NULL;
	public static function getInstance(){
	if(self::$m_instance == NULL){
		self::$m_instance = new self();
	}
	return self::$m_instance;
	}
function Info(){
	
}
function OperateRequirements(){
	$arr = array(
	0 => array('type'=>'text', 'label'=>'Server IP', 'name' => 'ip'),
	1 => array('type'=>'text', 'label'=>'Server port', 'name' => 'port'),
	2 => array('type'=>'text', 'label'=>'Remote user', 'name' => 'adminname'),
	3 => array('type'=>'password', 'label'=>'User password', 'name' => 'adminpassword')
	);
	return $arr;
}
/*
 * username, password, domain options are only available at the moment
 */
function CreateOptions(){
	$arr = array(
	0 => array('type' => 'username', 'mode' => 'strict', 'name' => 'clientlogin', 'label' => 'Username'),
	1 => array('type' => 'password', 'mode' => 'strict', 'name' => 'clientpassword', 'label' => 'Password'),
	);
	return $arr;
}
/*
 * Client Options stored in presets
 */
function ClientOptions(){
	$arr = array(
	1 => array('type' => 'text', 'name' => 'limit_maildomain', 'label' => 'Max. number of email domains'),
	2 => array('type' => 'text', 'name' => 'limit_mailbox', 'label' => 'Max. number of mailboxes'),
	3 => array('type' => 'text', 'name' => 'limit_mailalias', 'label' => 'Max. number of email aliases'),
	4 => array('type' => 'text', 'name' => 'limit_mailaliasdomain', 'label' => 'Max. number of domain aliases'),
	5 => array('type' => 'text', 'name' => 'limit_mailforward', 'label' => 'Max. number of email forwarders'),
	6 => array('type' => 'text', 'name' => 'limit_mailcatchall', 'label' => 'Max. number of email catchall accounts'),
	7 => array('type' => 'text', 'name' => 'limit_mailrouting', 'label' => 'Max. number of email routes'),
	8 => array('type' => 'text', 'name' => 'limit_mailfilter', 'label' => 'Max. number of email filters'),
	9 => array('type' => 'text', 'name' => 'limit_fetchmail', 'label' => 'Max. number of fetchmail accounts'),
	10 => array('type' => 'text', 'name' => 'limit_mailquota', 'label' => 'Mailbox quota, MB'),
	11 => array('type' => 'text', 'name' => 'limit_spamfilter_wblist', 'label' => 'Max. number of spamfilter white / blacklist filters'),
	12 => array('type' => 'text', 'name' => 'limit_spamfilter_user', 'label' => 'Max. number of spamfilter users'),
	13 => array('type' => 'text', 'name' => 'limit_spamfilter_policy', 'label' => 'Max. number of spamfilter policys'),
	14 => array('type' => 'text', 'name' => 'limit_web_domain', 'label' => 'Max. number of web domains'),
	15 => array('type' => 'text', 'name' => 'limit_web_quota', 'label' => 'Web Quota, MB'),
	16 => array('type' => 'select', 'name' => 'web_php_options', 'label' => 'PHP Options', 'select' => array('no' => 'Disabled', 'fast-cgi' => 'Fast CGI', 'cgi' => 'CGI', 'mod' => 'Apache mod_php', 'suphp' => 'suPHP')),
	17 => array('type' => 'text', 'name' => 'limit_web_aliasdomain', 'label' => 'Max. number of web aliasdomains'),
	18 => array('type' => 'text', 'name' => 'limit_web_subdomain', 'label' => 'Max. number of web subdomains'),
	19 => array('type' => 'text', 'name' => 'limit_ftp_user', 'label' => 'Max. number of FTP users'),
	20 => array('type' => 'text', 'name' => 'limit_shell_user', 'label' => 'Max. number of Shell users'),
	21 => array('type' => 'select', 'name' => 'ssh_chroot', 'label' => 'SSH-Chroot Options', 'select' => array('no' => 'None', 'jailkit' => 'Jailkit')),
	22 => array('type' => 'text', 'name' => 'limit_webdav_user', 'label' => 'Max. number of Webdav users'),
	23 => array('type' => 'text', 'name' => 'limit_dns_zone', 'label' => 'Max. number of DNS zones'),
	24 => array('type' => 'text', 'name' => 'limit_dns_slave_zone', 'label' => 'Max. number of secondary DNS zones'),
	25 => array('type' => 'text', 'name' => 'limit_dns_record', 'label' => 'Max. number DNS records'),
	26 => array('type' => 'text', 'name' => 'limit_client', 'label' => 'Probably limit on customers for resellers'),
	27 => array('type' => 'text', 'name' => 'limit_database', 'label' => 'Max. number of Databases'),
	28 => array('type' => 'text', 'name' => 'limit_cron', 'label' => 'Max. cron jobs'),
	29 => array('type' => 'select', 'name' => 'limit_cron_type', 'label' => 'Max. type of cron jobs (chrooted and full implies url)', 'select' => array('full' => 'Full Cron', 'chrooted' => 'Chrooted Cron', 'url' => 'URL Cron')),
	30 => array('type' => 'text', 'name' => 'limit_cron_frequency', 'label' => 'Min. delay between executions'),
	31 => array('type' => 'text', 'name' => 'limit_traffic_quota', 'label' => 'Traffic Quota'),
	//2 => array('type' => 'checkbox', 'name' => 'phpenable', 'label' => 'PHP Support')
	);
	return $arr;
}
/*
 * $operate_array given to Create() will be similar to
 * array(1=>array('somename'=>'value'))
 * where "somename" is the name you specified in operate requirements
*/
function Create($operate_array, $create_array, $client_options_array){
	$soap_location = "http://".$operate_array['ip'].":".$operate_array['port']."/remote/index.php";
	$soap_uri = "http://".$operate_array['ip'].":".$operate_array['port']."/remote/";
	$client = new SoapClient(null, array('location' => $soap_location,'uri' => $soap_uri));

	if($session_id = $client->login($username,$password)){
		$arr =  $client_options_array;
		$arr['contact_name'] = $create_array['clientlogin'];
		$arr['username'] = $create_array['clientlogin'];
		$arr['password'] = $create_array['clientpassword'];
		if($client->client_add($session_id,false,$arr)){
			if($client->logout($session_id)){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
			/*
			$arr['limit_maildomain'] =
			$arr['limit_mailbox'] =
			$arr['limit_mailalias'] =
			$arr['limit_mailaliasdomain'] =
			$arr['limit_mailforward'] =
			$arr['limit_mailcatchall'] =
			$arr['limit_mailrouting'] =
			$arr['limit_mailfilter'] =
			$arr['limit_fetchmail'] =
			$arr['limit_mailquota'] =
			$arr['limit_spamfilter_wblist'] =
			$arr['limit_spamfilter_user'] =
			$arr['limit_spamfilter_policy'] =
			$arr['limit_web_domain'] =
			$arr['limit_web_quota'] =
			$arr['web_php_options'] =
			$arr['limit_web_aliasdomain'] =
			$arr['limit_web_subdomain'] =
			$arr['limit_ftp_user'] =
			$arr['limit_shell_user'] =
			$arr['ssh_chroot'] =
			$arr['limit_webdav_user'] =
			$arr['limit_dns_zone'] =
			$arr['limit_dns_slave_zone'] =
			$arr['limit_dns_record'] =
			$arr['limit_client'] =
			$arr['limit_database'] =
			$arr['limit_cron'] =
			$arr['limit_cron_type'] =
			$arr['limit_cron_frequency'] =
			$arr['limit_traffic_quota'] =
			*/
		
	} else {
		return false;
	}

}
public function Suspend($operate_array, $create_array){
	throw new Exception("This function is not implemented");
}
public function Unsuspend($operate_array, $create_array){
	throw new Exception("This function is not implemented");
}
public function Delete($operate_array, $create_array){
	$soap_location = "http://".$operate_array['ip'].":".$operate_array['port']."/remote/index.php";
	$soap_uri = "http://".$operate_array['ip'].":".$operate_array['port']."/remote/";
	$username = $operate_array['adminname'];
	$password = $operate_array['adminpassword'];
	$client = new SoapClient(null, array('location' => $soap_location,'uri' => $soap_uri));
	if($session_id = $client->login($username,$password)){
		if($clientid = $client->client_get_by_username($session_id,$create_array['clientlogin'])){
			if($client->client_delete($session_id,$clientid)){
				return true;
			} else {
				throw new Exception("Unable to delete user with ID: ".$clientid);
			}
		} else {
			throw new Exception("Unable to find client ID by username: ".$create_array['clientlogin']);
		}
	} else {
		throw new Exception("Unable to login into ISPConfig 3 with username ".$username." and password *********");
	}
}

public function Update($operate_array, $create_array, $client_options_array){
	$soap_location = "http://".$operate_array['ip'].":".$operate_array['port']."/remote/index.php";
	$soap_uri = "http://".$operate_array['ip'].":".$operate_array['port']."/remote/";
	$username = $operate_array['adminname'];
	$password = $operate_array['adminpassword'];
	$client = new SoapClient(null, array('location' => $soap_location,'uri' => $soap_uri));
	if($session_id = $client->login($username,$password)){
		if($clientid = $client->client_get_by_username($session_id,$create_array['clientlogin'])){
			if($client->client_update($session_id,$clientid,false,$client_options_array)){
				return true;
			} else {
				throw new Exception("Unable to update user with ID: ".$clientid);
			}
		} else {
			throw new Exception("Unable to find client ID by username: ".$create_array['clientlogin']);
		}
	} else {
		throw new Exception("Unable to login into ISPConfig 3 with username ".$username." and password *********");
	}
}
}
?>