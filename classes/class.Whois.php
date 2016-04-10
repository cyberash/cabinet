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

/**
* This class provides informations about internet domains and
* their registrars. It can give you the WHOIS informations or
* tells if a domain is available for registration or not.
*/

/** Functions
new Whois(domain_name) :: constructor
info() :: whois information
html_info() :: whois information - html formated
is_available() :: the availability of a domain
get_whois_server() :: the whois server of the domain
get_tld() :: the tld of the domain
get_domain :: the domain name without tld
get_fulldomain() :: the domain name
is_valid() :: validation of a domain name
get_tlds() :: all supported top level domains
*/

class Whois {

public $domain;
public $whois_string;
public $errstr;

/**
* Constructor of class domain
* @param string	$str_domainame    the full name of the domain
* @desc Constructor of class domain
*/
function __construct($str_domainname) {
	$this->domain = $str_domainname;
}

/**
* Returns the whois data of the domain
* @return string $whoisdata Whois data as string
* @desc Returns the whois data of the domain
*/

protected function fget() {
}

function info(){
	if($this->is_valid()){

	$tldname = $this->get_tld();
	$domainname = $this->get_domain();
	$whois_server = $this->get_whois_server();

	// If tldname have been found
	if(!empty($whois_server)){
		// Getting whois information
		$fp = @stream_socket_client($whois_server.':43', $errno, $errstr, 10);
		if($errno == 0 && !$fp) {
			$this->errstr='Socket Error: Could not initialize socket';
			return false;
		}elseif(!$fp) {
			$this->errstr='Socket Error #'.$errno.': '.$errstr;
			return false;
		}

		$dom = $domainname.'.'.$tldname;

		// New IDN
		if($tldname=="de") fputs($fp, "-C ISO-8859-1 -T dn $dom\r\n");
		else fputs($fp, "$dom\r\n");

		// Getting string
		$string='';

		// Checking whois server for .com .net .edu for featch real whois_server
		if($tldname=='com' || $tldname=='net' || $tldname=='edu'){
			while(!feof($fp)){
				$line=trim(fgets($fp));
				$string.=$line;
				$lineArr=split(":",$line);
				if(strtolower($lineArr[0])=="whois server") $whois_server=trim($lineArr[1]);
			}

				// Getting whois information
				$fp = @fsockopen($whois_server,43, $errno, $errstr, 10);
				if(!$fp) return "Can't connect to $whois_server";
				fputs($fp, "$dom\r\n");
				$string='';
				while(!feof($fp)) $string.=fgets($fp);
			}else{
				while(!feof($fp)) $string.=fgets($fp);
			}
			fclose($fp);
			return $string;
		}else{
			return "No whois server for this tld in list!";
		}
	}else{
		return "Domainname isn't valid!";
	}
}

/**
* Returns the whois data of the domain in HTML format
* @return string $whoisdata Whois data as string in HTML
* @desc Returns the whois data of the domain  in HTML format
*/
public function html_info(){
	return nl2br($this->info());
}

/**
* Returns name of the whois server of the tld
* @return string $server the whois servers hostname
* @desc Returns name of the whois server of the tld
*/
public function get_whois_server(){
	$tldname=$this->get_tld();
	$server = isset(self::$servers[$tldname]) ? self::$servers[$tldname][0] : 'whois.ripe.net';
	return $server;
}

/**
* Returns the tld of the domain without domain name
* @return string $tldname the tlds name without domain name
* @desc Returns the tld of the domain without domain name
*/
public function get_tld(){
	$tldname = substr($this->domain, strpos($this->domain, '.')+1);
	return $tldname;
}


/**
* Returns all tlds which are supported by the class
* @return array $tlds all tlds as array
* @desc Returns all tlds which are supported by the class
*/
public static function get_tlds(){
	$tlds = array_keys(Whois::$servers);
	return $tlds;
}

/**
* Returns the name of the domain without tld
* @return string $domain the domains name without tld name
* @desc Returns the name of the domain without tld
*/
public function get_domain(){
	$domain = explode('.',$this->domain);
	return $domain[0];
}

/**
* Returns the full domain
* @return string $fulldomain
* @desc Returns the full domain
*/
public function get_fulldomain() {
	return $this->domain;
}

/**
* Returns the string which will be returned by the whois server of the tld if a domain is avalable
* @return string $notfound  the string which will be returned by the whois server of the tld if a domain is avalable
* @desc Returns the string which will be returned by the whois server of the tld if a domain is avalable
*/
public function get_notfound_string() {
	$tldname = $this->get_tld();
	$notfound = isset(self::$servers[$tldname]) ? self::$servers[$tldname][1] : 'No entries found';
	return $notfound;
}

/**
* Returns the array of whois info
ns			- array of nameservers
email		- array of contact emails
expirate	- Expiration date
status		- State of delegate
registrar	- Registration Service Provided
*/
public function parsed_info() {
	$info = array();
	$info['nameserver'] = array();
	$info['email'] = array();
	$info['status'] = '';
	$info['expirate'] = '';
	if(empty($this->whois_string)) $this->whois_string = $this->info();
	$info['domain'] = $this->get_domain();
	$info['tld'] = $this->get_tld();
	//$info['raw'] = $this->whois_string;
	$strs = explode("\n",$this->whois_string);
foreach($strs as $str){

	$str = trim(str_ireplace('domain','',$str));

	if(stristr($str, 'e-mail:')) $info['email'][] = trim(str_ireplace('e-mail:', '', $str));
	elseif(stristr($str, 'Contact E-mail:')) $info['email'][] = trim(str_ireplace('Contact E-mail:', '', $str));
	elseif(stristr($str, 'Tech Email:')) $info['email'][] = trim(str_ireplace('Tech Email:', '', $str));

	elseif(stristr($str, 'paid-till')) $info['expirate'] = trim(str_ireplace(array('paid-till:','.'), array('','-'), $str));
	elseif(stristr($str, 'Expiration date:')) $info['expirate'] = trim(str_ireplace(array('Expiration date:','.'), array('','-'), $str));

	elseif(stristr($str, 'nserver')) $info['nameserver'][] = strtolower(trim(str_ireplace('nserver:', '', $str)));
	elseif(stristr($str, 'Name Server:')) $info['nameserver'][] = strtolower(trim(str_ireplace('Name Server:', '', $str)));

	elseif(stristr($str, 'state:')) $info['status'] = strtolower(trim(str_ireplace('state:', '', $str)));
	elseif(stristr($str, 'Status:')) $info['status'] = strtolower(trim(str_ireplace('Status:', '', $str)));

	elseif(stristr($str, 'registrar')) $info['registrar'] = trim(str_ireplace('registrar:', '', $str));
	elseif(stristr($str, 'Registration Service Provided By:')) $info['registrar'] = trim(str_ireplace('Registration Service Provided By:', '', $str));
}

$info['expirate'] = trim(str_ireplace(array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'), '', $info['expirate']));
$info['expirate'] = trim(str_ireplace(array('gmt', 'utc'), '', $info['expirate']));

	return $info;
}

/**
* Returns if the domain is available for registering
* @return boolean $is_available Returns 1 if domain is available and 0 if domain isn't available
* @desc Returns if the domain is available for registering
*/
public function is_available() {
	if(iDEMO) return false;
	if(empty($this->whois_string)) $this->whois_string = $this->info();
	// if No get answer from whois server
	if(empty($this->whois_string)) return true;
	$not_found_string = $this->get_notfound_string();
	// if No whois server for this tld
	if(empty($not_found_string)) return true;
	$domain = $this->domain;
	$whois_string = str_replace($domain, '', $this->whois_string);
	$whois_string = preg_replace('/\s+/', ' ', $whois_string); //Replace whitespace with single space
	$array = explode(':',$not_found_string);
	if($array[0]=='MAXCHARS'){
		if(strlen($whois_string)<=$array[1]) return true; else return false;
	}else{
		if(preg_match('/'.$not_found_string.'/i',$whois_string)) return true; else return false;
	}
}

function get_cn_server($whois_text){

}


/**
* Returns if the domain name is valid
* @return boolean $is_valid Returns 1 if domain is valid and 0 if domain isn't valid
* @desc Returns if the domain name is valid
*/
public function is_valid(){
	$domainArr=explode('.',$this->domain);

	// If it's a tld with two Strings (like co.uk)
	if(count($domainArr)==3){
		$tld=$domainArr[1].'.'.$domainArr[2];

		//if(!isset(self::$servers[$tld])) return false;

	}elseif(count($domainArr)>3) return false;

	// Creating regular expression for
	if($this->get_tld()=="de"){
		$idn = '';
		for($i=0;$i<count($this->idn);$i++){
			$idn.=chr($this->idn[$i]);
			// $idn.="\x".$this->idn[$i]."";
		}
		$pattern="^[a-z".$idn."0-9\-]{3,}$";
	}else $pattern="^[a-z0-9\-]{3,}$";

	if(ereg($pattern,strtolower($this->get_domain())) && !ereg("^-|-$",strtolower($this->get_domain())) && !preg_match("/--/",strtolower($this->get_domain()))) return true; else return false;

}

// check domain name for root tld
public static function is_root($domain) {
	$tlds = Whois::get_tlds();
	$domainArr = explode('.',$domain);
	$count = count($domainArr);
	if($count<2 || $count>3) return false;
	elseif($count == 2) return true;
	else{ // 2-th zone domain
		if(strlen($domainArr[1])>4) return false;
		//elseif(strlen($domainArr[1])<3) return true;
		elseif(!in_array($domainArr[1].'.'.$domainArr[2],$tlds) && !in_array($domainArr[1],array('us','com','ru','info','biz','org','net'))) { echo $domainArr[1].'.'.$domainArr[2]."<br />"; return false; }
		else return true;
	}
}

/**
* Initializing server variables
* array(top level domain,whois_Server,not_found_string or MAX number of CHARS: MAXCHARS:n)
**/
public static $servers = array(
// nic.ru
	'ru' => array('whois.ripn.ru','No entries found'),
	'su' => array('whois.ripn.ru','No entries found'),

	'com' => array('whois.crsnic.net','No match'),
	'net' => array('whois.crsnic.net','No match'),

//
	'org' => array('whois.pir.org','NOT FOUND'),
//	'org' => array('whois.nic.ru','No entries found'),
	'biz' => array('whois.biz','Not found'),
	'info' => array('whois.afilias.info','Not found'),
	'name' => array('whois.nic.name','No match'),

// ripn.ru
	'com.ru' => array('whois.ripn.ru','No entries found'),
	'net.ru' => array('whois.ripn.ru','No entries found'),
	'org.ru' => array('whois.ripn.ru','No entries found'),
	'pp.ru' => array('whois.ripn.ru','No entries found'),

// relcom.ru
	'msk.ru' => array('whois.relcom.ru','No entries found'),
	'msk.su' => array('whois.relcom.ru','No entries found'),
	'spb.ru' => array('whois.relcom.ru','No entries found'),
	'spb.su' => array('whois.relcom.ru','No entries found'),
	'nov.ru' => array('whois.relcom.ru','No entries found'),
	'nov.su' => array('whois.relcom.ru','No entries found'),
	'ru.net' => array('whois.relcom.ru','No entries found'),

	'ac' => array('whois.nic.ac','No match'),
	'ac.cn' => array('whois.cnnic.net.cn','no matching record'),
	'ac.jp' => array('whois.nic.ad.jp','No match'),
	'ac.uk' => array('whois.ja.net','No such domain'),
	'ad.jp' => array('whois.nic.ad.jp','No match'),
	'adm.br' => array('whois.nic.br','No match'),
	'adv.br' => array('whois.nic.br','No match'),
	'aero' => array('whois.information.aero','is available'),
	'ag' => array('whois.nic.ag','Not found'),
	'agr.br' => array('whois.nic.br','No match'),
	'ah.cn' => array('whois.cnnic.net.cn','No entries found'),
	'al' => array('whois.ripe.net','No entries found'),
	'am' => array('whois.amnic.net','No match'),
	'am.br' => array('whois.nic.br','No match'),
	'arq.br' => array('whois.nic.br','No match'),
	'at' => array('whois.nic.at','nothing found'),
	'au' => array('whois.aunic.net','No Data Found'),
	'art.br' => array('whois.nic.br','No match'),
	'as' => array('whois.nic.as','Domain Not Found'),
	'asn.au' => array('whois.aunic.net','No Data Found'),
	'ato.br' => array('whois.nic.br','No match'),
	'av.tr' => array('whois.nic.tr','Not found in database'),
	'az' => array('whois.ripe.net','no entries found'),
	'ba' => array('whois.ripe.net','No match for'),
	'be' => array('whois.geektools.com','No such domain'),
	'bg' => array('whois.digsys.bg','does not exist'),
	'bio.br' => array('whois.nic.br','No match'),
	'biz.tr' => array('whois.nic.tr','Not found in database'),
	'bj.cn' => array('whois.cnnic.net.cn','No entries found'),
	'bel.tr' => array('whois.nic.tr','Not found in database'),
	'bmd.br' => array('whois.nic.br','No match'),
	'br' => array('whois.registro.br','No match'),
	'by' => array('whois.ripe.net','no entries found'),
	'ca' => array('whois.cira.ca','Status: AVAIL'),
	'cc' => array('whois.nic.cc','No match'),
	'cd' => array('whois.cd','No match'),
	'ch' => array('whois.nic.ch','We do not have an entry'),
	'cim.br' => array('whois.nic.br','No match'),
	'ck' => array('whois.ck-nic.org.ck','No entries found'),
	'cl' => array('whois.nic.cl','no existe'),
	'cn' => array('whois.cnnic.net.cn','No entries found'),
	'cng.br' => array('whois.nic.br','No match'),
	'cnt.br' => array('whois.nic.br','No match'),
	'com.au' => array('whois.aunic.net','No Data Found'),
	'com.br' => array('whois.nic.br','No match'),
	'com.cn' => array('whois.cnnic.net.cn','No entries found'),
	'com.eg' => array('whois.ripe.net','No entries found'),
	'com.hk' => array('whois.hknic.net.hk','No Match for'),
	'com.mx' => array('whois.nic.mx','Nombre del Dominio'),
	'com.tr' => array('whois.nic.tr','Not found in database'),
	'com.tw' => array('whois.twnic.net','NO MATCH TIP'),
	'conf.au' => array('whois.aunic.net','No entries found'),
	'co.at' => array('whois.nic.at','nothing found'),
	'co.jp' => array('whois.nic.ad.jp','No match'),
	'co.uk' => array('whois.nic.uk','No match for'),
	'cq.cn' => array('whois.cnnic.net.cn','No entries found'),
	'csiro.au' => array('whois.aunic.net','No Data Found'),
	'cx' => array('whois.nic.cx','No match'),
	'cy' => array('whois.ripe.net','no entries found'),
	'cz' => array('whois.nic.cz','No data found'),
	'de' => array('whois.denic.de','not found'),
	'dr.tr' => array('whois.nic.tr','Not found in database'),
	'dk' => array('whois.dk-hostmaster.dk','No entries found'),
	'dz' => array('whois.ripe.net','no entries found'),
	'ecn.br' => array('whois.nic.br','No match'),
	'ee' => array('whois.eenet.ee','NOT FOUND'),
	'edu' => array('whois.verisign-grs.net','No match'),
	'edu' => array('whois.crsnic.net','No match'),
	'edu.au' => array('whois.aunic.net','No Data Found'),
	'edu.br' => array('whois.nic.br','No match'),
	'edu.tr' => array('whois.nic.tr','Not found in database'),
	'eg' => array('whois.ripe.net','No entries found'),
	'es' => array('whois.ripe.net','No entries found'),
	'esp.br' => array('whois.nic.br','No match'),
	'etc.br' => array('whois.nic.br','No match'),
	'eti.br' => array('whois.nic.br','No match'),
	'eun.eg' => array('whois.ripe.net','No entries found'),
	'emu.id.au' => array('whois.aunic.net','No Data Found'),
	'eng.br' => array('whois.nic.br','No match'),
	'eu' => array('whois.eu','Status:      FREE'),
	'far.br' => array('whois.nic.br','No match'),
	'fi' => array('whois.ripe.net','No entries found'),
	'fj' => array('whois.usp.ac.fj',''),
	'fj.cn' => array('whois.cnnic.net.cn','No entries found'),
	'fm.br' => array('whois.nic.br','No match'),
	'fnd.br' => array('whois.nic.br','No match'),
	'fo' => array('whois.ripe.net','no entries found'),
	'fot.br' => array('whois.nic.br','No match'),
	'fst.br' => array('whois.nic.br','No match'),
	'fr' => array('whois.nic.fr','No entries found'),
	'gb' => array('whois.ripe.net','No match for'),
	'gb.com' => array('whois.nomination.net','No match for'),
	'gb.net' => array('whois.nomination.net','No match for'),
	'g12.br' => array('whois.nic.br','No match'),
	'gd.cn' => array('whois.cnnic.net.cn','No entries found'),
	'ge' => array('whois.ripe.net','no entries found'),
	'gen.tr' => array('whois.nic.tr','Not found in database'),
	'ggf.br' => array('whois.nic.br','No match'),
	'gl' => array('whois.ripe.net','no entries found'),
	'gr' => array('whois.ripe.net','no entries found'),
	'gr.jp' => array('whois.nic.ad.jp','No match'),
	'gs' => array('whois.adamsnames.tc','is not registered'),
	'gs.cn' => array('whois.cnnic.net.cn','No entries found'),
	'gov.au' => array('whois.aunic.net','No Data Found'),
	'gov.br' => array('whois.nic.br','No match'),
	'gov.cn' => array('whois.cnnic.net.cn','No entries found'),
	'gov.hk' => array('whois.hknic.net.hk','No Match for'),
	'gov.tr' => array('whois.nic.tr','Not found in database'),
	'gob.mx' => array('whois.nic.mx','Nombre del Dominio'),
	'gs' => array('whois.adamsnames.tc','is not registered'),
	'gz.cn' => array('whois.cnnic.net.cn','No entries found'),
	'gx.cn' => array('whois.cnnic.net.cn','No entries found'),
	'he.cn' => array('whois.cnnic.net.cn','No entries found'),
	'ha.cn' => array('whois.cnnic.net.cn','No entries found'),
	'hb.cn' => array('whois.cnnic.net.cn','No entries found'),
	'hi.cn' => array('whois.cnnic.net.cn','No entries found'),
	'hl.cn' => array('whois.cnnic.net.cn','No entries found'),
	'hn.cn' => array('whois.cnnic.net.cn','No entries found'),
	'hm' => array('whois.registry.hm','(null)'),
	'hk' => array('whois.hknic.net.hk','No Match for'),
	'hk.cn' => array('whois.cnnic.net.cn','No entries found'),
	'hu' => array('whois.ripe.net','MAXCHARS:500'),
	'id.au' => array('whois.aunic.net','No Data Found'),
	'ie' => array('whois.domainregistry.ie','no match'),
	'ind.br' => array('whois.nic.br','No match'),
	'imb.br' => array('whois.nic.br','No match'),
	'inf.br' => array('whois.nic.br','No match'),
	'info.au' => array('whois.aunic.net','No Data Found'),
	'info.tr' => array('whois.nic.tr','Not found in database'),
	'it' => array('whois.nic.it','No entries found'),
	'idv.tw' => array('whois.twnic.net','NO MATCH TIP'),
	'int' => array('whois.iana.org','not found'),
	'is' => array('whois.isnic.is','No entries found'),
	'il' => array('whois.isoc.org.il','No data was found'),
	'jl.cn' => array('whois.cnnic.net.cn','No entries found'),
	'jor.br' => array('whois.nic.br','No match'),
	'jp' => array('whois.nic.ad.jp','No match'),
	'js.cn' => array('whois.cnnic.net.cn','No entries found'),
	'jx.cn' => array('whois.cnnic.net.cn','No entries found'),
	'k12.tr' => array('whois.nic.tr','Not found in database'),
	'ke' => array('whois.rg.net','No match for'),
	'kr' => array('whois.krnic.net','is not registered'),
	'kz' => array('whois.nic.kz', 'Nothing found for this query'),
	'la' => array('whois.nic.la','NO MATCH'),
	'lel.br' => array('whois.nic.br','No match'),
	'li' => array('whois.nic.ch','We do not have an entry'),
	'lk' => array('whois.nic.lk','No domain registered'),
	'ln.cn' => array('whois.cnnic.net.cn','No entries found'),
	'lt' => array('ns.litnet.lt','No matches found'),
	'lu' => array('whois.dns.lu','No entries found'),
	'lv' => array('whois.ripe.net','no entries found'),
	'ltd.uk' => array('whois.nic.uk','No match for'),
	'ma' => array('whois.ripe.net','No entries found'),
	'mat.br' => array('whois.nic.br','No match'),
	'mc' => array('whois.ripe.net','No entries found'),
	'md' => array('whois.ripe.net','No match for'),
	'me.uk' => array('whois.nic.uk','No match for'),
	'med.br' => array('whois.nic.br','No match'),
	'mil' => array('whois.nic.mil','No match'),
	'mil.br' => array('whois.nic.br','No match'),
	'mil.tr' => array('whois.nic.tr','Not found in database'),
	'mk' => array('whois.ripe.net','No match for'),
	'mn' => array('whois.nic.mn','Domain not found'),
	'mo.cn' => array('whois.cnnic.net.cn','No entries found'),
	'ms' => array('whois.adamsnames.tc','is not registered'),
	'mt' => array('whois.ripe.net','No Entries found'),
	'mus.br' => array('whois.nic.br','No match'),
	'mx' => array('whois.nic.mx','Nombre del Dominio'),
	'name.tr' => array('whois.nic.tr','Not found in database'),
	'ne.jp' => array('whois.nic.ad.jp','No match'),
	'net.au' => array('whois.aunic.net','No Data Found'),
	'net.br' => array('whois.nic.br','No match'),
	'net.cn' => array('whois.cnnic.net.cn','No entries found'),
	'net.eg' => array('whois.ripe.net','No entries found'),
	'net.hk' => array('whois.hknic.net.hk','No Match for'),
	'net.lu' => array('whois.dns.lu','No entries found'),
	'net.mx' => array('whois.nic.mx','Nombre del Dominio'),
	'net.uk' => array('whois.nic.uk','No match for '),
	'net.tr' => array('whois.nic.tr','Not found in database'),
	'net.tw' => array('whois.twnic.net','NO MATCH TIP'),
	'nl' => array('whois.domain-registry.nl','is not a registered domain'),
	'nm.cn' => array('whois.cnnic.net.cn','No entries found'),
	'no' => array('whois.norid.no','no matches'),
	'no.com' => array('whois.nomination.net','No match for'),
	'nom.br' => array('whois.nic.br','No match'),
	'not.br' => array('whois.nic.br','No match'),
	'ntr.br' => array('whois.nic.br','No match'),
	'nu' => array('whois.nic.nu','NO MATCH for'),
	'nx.cn' => array('whois.cnnic.net.cn','No entries found'),
	'nz' => array('whois.domainz.net.nz','Not Listed'),
	'plc.uk' => array('whois.nic.uk','No match for'),
	'odo.br' => array('whois.nic.br','No match'),
	'oop.br' => array('whois.nic.br','No match'),
	'or.jp' => array('whois.nic.ad.jp','No match'),
	'or.at' => array('whois.nic.at','nothing found'),
	'org.au' => array('whois.aunic.net','No Data Found'),
	'org.br' => array('whois.nic.br','No match'),
	'org.cn' => array('whois.cnnic.net.cn','No entries found'),
	'org.hk' => array('whois.hknic.net.hk','No Match for'),
	'org.lu' => array('whois.dns.lu','No entries found'),
	'org.tr' => array('whois.nic.tr','Not found in database'),
	'org.tw' => array('whois.twnic.net','NO MATCH TIP'),
	'org.uk' => array('whois.nic.uk','No match for'),
	'pk' => array('whois.pknic.net','is not registered'),
	'pl' => array('whois.ripe.net','No information about'),
	'pol.tr' => array('whois.nic.tr','Not found in database'),
	'pp.ru' => array('whois.ripn.ru','No entries found'),
	'ppg.br' => array('whois.nic.br','No match'),
	'pro.br' => array('whois.nic.br','No match'),
	'psi.br' => array('whois.nic.br','No match'),
	'psc.br' => array('whois.nic.br','No match'),
	'pt' => array('whois.ripe.net','No match for'),
	'qh.cn' => array('whois.cnnic.net.cn','No entries found'),
	'qsl.br' => array('whois.nic.br','No match'),
	'rec.br' => array('whois.nic.br','No match'),
	'ro' => array('whois.ripe.net','No entries found'),
	'sc.cn' => array('whois.cnnic.net.cn','No entries found'),
	'sd.cn' => array('whois.cnnic.net.cn','No entries found'),
	'se' => array('whois.nic-se.se','No data found'),
	'se.com' => array('whois.nomination.net','No match for'),
	'se.net' => array('whois.nomination.net','No match for'),
	'sg' => array('whois.nic.net.sg','NO entry found'),
	'sh' => array('whois.nic.sh','No match for'),
	'sh.cn' => array('whois.cnnic.net.cn','No entries found'),
	'si' => array('whois.arnes.si','No entries found'),
	'sk' => array('whois.ripe.net','no entries found'),
	'slg.br' => array('whois.nic.br','No match'),
	'sm' => array('whois.ripe.net','no entries found'),
	'sn.cn' => array('whois.cnnic.net.cn','No entries found'),
	'srv.br' => array('whois.nic.br','No match'),
	'st' => array('whois.nic.st','No entries found'),
	'sx.cn' => array('whois.cnnic.net.cn','No entries found'),
	'tc' => array('whois.adamsnames.tc','is not registered'),
	'tel.tr' => array('whois.nic.tr','Not found in database'),
	'th' => array('whois.nic.uk','No entries found'),
	'tj.cn' => array('whois.cnnic.net.cn','No entries found'),
	'tm' => array('whois.nic.tm','No match for'),
	'tn' => array('whois.ripe.net','No entries found'),
	'tmp.br' => array('whois.nic.br','No match'),
	'to' => array('whois.tonic.to','No match'),
	// 'tr' => array('whois.ripe.net','Not found in database'),
	'trd.br' => array('whois.nic.br','No match'),
	'tur.br' => array('whois.nic.br','No match'),
	'tv' => array('whois.nic.tv','MAXCHARS:75'),
	'tv.br' => array('whois.nic.br','No match'),
	'tw' => array('whois.twnic.net','NO MATCH TIP'),
	'tw.cn' => array('whois.cnnic.net.cn','No entries found'),
	'ua' => array('whois.net.ua','No entries found'),
		'com.ua' => array('whois.net.ua','No entries found'),
		'net.ua' => array('whois.net.ua','No entries found'),
		'edu.ua' => array('whois.net.ua','No entries found'),
		'biz.ua' => array('whois.net.ua','No entries found'),
		'in.ua' => array('whois.net.ua','No entries found'),
		'gov.ua' => array('whois.net.ua','No entries found'),
		'org.ua' => array('whois.net.ua','No entries found'),
	'uk' => array('whois.thnic.net','No match for'),
	'uk.com' => array('whois.nomination.net','No match for'),
	'uk.net' => array('whois.nomination.net','No match for'),
	'us' => array('whois.nic.us','Not found'),
	'va' => array('whois.ripe.net','No entries found'),
	'vet.br' => array('whois.nic.br','No match'),
	'vg' => array('whois.adamsnames.tc','is not registered'),
	'wattle.id.au' => array('whois.aunic.net','No Data Found'),
	'web.tr' => array('whois.nic.tr','Not found in database'),
	'ws' => array('whois.worldsite.ws','No match for'),
	'xj.cn' => array('whois.cnnic.net.cn','No entries found'),
	'xz.cn' => array('whois.cnnic.net.cn','No entries found'),
	'yn.cn' => array('whois.cnnic.net.cn','No entries found'),
	'yu' => array('whois.ripe.net','No entries found'),
	'za' => array('whois.frd.ac.za','No match for'),
	'zlg.br' => array('whois.nic.br','No match'),
	'zj.cn' => array('whois.cnnic.net.cn','No entries found')
);



public $idn=array(224,225,226,227,228,229,230,231,232,233,234,235,240,236,237,238,239,241,242,243,244,245,246,248,254,249,250,251,252,253,255);
	//	var $idn=array("00E0","00E1","00E2","00E3","00E4","00E5","0101","0103","0105","00E6","00E7","0107","0109","010B","010D","010F","0111","00E8","00E9","00EA","00EB","0113","0115","0117","0119","011B","014B","00F0","011D","011F","0121","0123","0125","0127","00EC","00ED","00EE","00EF","0129","012B","012D","012F","0131","0135","0137","0138","013A","013C","013E","0142","00F1","0144","0146","0148","00F2","00F3","00F4","00F5","00F6","00F8","014D","014F","0151","0153","0155","0157","0159","015B","015D","015F","0161","0163","0165","0167","00FE","00F9","00FA","00FB","00FC","0169","016B","016D","016F","0171","0173","0175","00FD","00FF","0177","017A","017C","017E");


}
