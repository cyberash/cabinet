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
 * Resource Record object definition
 *
 * Builds or parses resource record sections of the DNS  packet including
 * the answer, authority, and additional  sections of the packet.
 *
 * @package Net_DNS
 */
class Net_DNS_RR
{
    /* class variable definitions {{{ */
  public $name;
  public $type;
  public $class;
  public $ttl;
  public $rdlength;
  public $rdata;


    function __construct($rrdata)
    {
        if ($rrdata != 'getRR') { //BC check/warning remove later
            trigger_error("Please use Net_DNS_RR::factory() instead");
        }
    }

    /*
     * Returns an RR object, use this instead of constructor
     *
     * @param mixed $rr_rdata Options as string, array or data
     * @return object Net_DNS_RR or Net_DNS_RR_<type>
     * @access public
     * @see Net_DNS_RR::new_from_array Net_DNS_RR::new_from_data Net_DNS_RR::new_from_string
     */
static public function &factory($rrdata, $update_type = '')
    {
        if (is_string($rrdata)) {
            $rr = &Net_DNS_RR::new_from_string($rrdata, $update_type);
        } elseif (count($rrdata) == 7) {
            list($name, $rrtype, $rrclass, $ttl, $rdlength, $data, $offset) = $rrdata;
            $rr = &Net_DNS_RR::new_from_data($name, $rrtype, $rrclass, $ttl, $rdlength, $data, $offset);
        } else {
            $rr = &Net_DNS_RR::new_from_array($rrdata);
        }
        return $rr;
    }

    /* }}} */
    /* Net_DNS_RR::new_from_data($name, $ttl, $rrtype, $rrclass, $rdlength, $data, $offset) {{{ */
static public function &new_from_data($name, $rrtype, $rrclass, $ttl, $rdlength, $data, $offset)
    {
        $rr = new Net_DNS_RR('getRR');
        $rr->name = $name;
        $rr->type = $rrtype;
        $rr->class = $rrclass;
        $rr->ttl = $ttl;
        $rr->rdlength = $rdlength;
        $rr->rdata = substr($data, $offset, $rdlength);
        if (class_exists('Net_DNS_RR_' . $rrtype)) {
            $scn = 'Net_DNS_RR_' . $rrtype;
            $rr = new $scn($rr, $data, $offset);
        }
        return $rr;
    }

    /* }}} */
    /* Net_DNS_RR::new_from_string($rrstring, $update_type = '') {{{ */
static public function &new_from_string($rrstring, $update_type = '')
    {
        $rr = new Net_DNS_RR('getRR');
        $ttl = 0;
        $parts = preg_split('/[\s]+/', $rrstring);
        while (count($parts) > 0) {
			$s = array_shift($parts);
            if (!isset($name)) {
                $name = ereg_replace('\.+$', '', $s);
            } else if (preg_match('/^\d+$/', $s)) {
                $ttl = $s;
            } else if (!isset($rrclass) && ! is_null(Net_DNS::classesbyname(strtoupper($s)))) {
                $rrclass = strtoupper($s);
                $rdata = join(' ', $parts);
            } else if (! is_null(Net_DNS::typesbyname(strtoupper($s)))) {
                $rrtype = strtoupper($s);
                $rdata = join(' ', $parts);
                break;
            } else {
                break;
            }
        }

        /*
         *  Do we need to do this?
         */
        $rdata = trim(chop($rdata));

        if (! strlen($rrtype) && strlen($rrclass) && $rrclass == 'ANY') {
            $rrtype = $rrclass;
            $rrclass = 'IN';
        } else if (! isset($rrclass)) {
            $rrclass = 'IN';
        }

        if (! strlen($rrtype)) {
            $rrtype = 'ANY';
        }

        if (strlen($update_type)) {
            $update_type = strtolower($update_type);
            if ($update_type == 'yxrrset') {
                $ttl = 0;
                if (! strlen($rdata)) {
                    $rrclass = 'ANY';
                }
            } else if ($update_type == 'nxrrset') {
                $ttl = 0;
                $rrclass = 'NONE';
                $rdata = '';
            } else if ($update_type == 'yxdomain') {
                $ttl = 0;
                $rrclass = 'ANY';
                $rrtype = 'ANY';
                $rdata = '';
            } else if ($update_type == 'nxdomain') {
                $ttl = 0;
                $rrclass = 'NONE';
                $rrtype = 'ANY';
                $rdata = '';
            } else if (preg_match('/^(rr_)?add$/', $update_type)) {
                $update_type = 'add';
                if (! $ttl) {
                    $ttl = 86400;
                }
            } else if (preg_match('/^(rr_)?del(ete)?$/', $update_type)) {
                $update_type = 'del';
                $ttl = 0;
                $rrclass = $rdata ? 'NONE' : 'ANY';
            }
        }

        if (strlen($rrtype)) {
            $rr->name = $name;
            $rr->type = $rrtype;
            $rr->class = $rrclass;
            $rr->ttl = $ttl;
            $rr->rdlength = 0;
            $rr->rdata = '';

            if (class_exists('Net_DNS_RR_' . $rrtype)) {
                $scn = 'Net_DNS_RR_' . $rrtype;
                return new $scn($rr, $rdata);
            } else {
                return $rr;
            }
        } else {
            return null;
        }
    }

    /* }}} */
    /* Net_DNS_RR::new_from_array($rrarray) {{{ */
static public function &new_from_array($rrarray)
    {
        $rr = new Net_DNS_RR('getRR');
        foreach ($rrarray as $k => $v) {
            $rr->{strtolower($k)} = $v;
        }

        if (! strlen($rr->name)) {
            return null;
        }
        if (! strlen($rr->type)){
            return null;
        }
        if (! $rr->ttl) {
            $rr->ttl = 0;
        }
        if (! strlen($rr->class)) {
            $rr->class = 'IN';
        }
        if (strlen($rr->rdata)) {
            $rr->rdlength = strlen($rr->rdata);
        }
        if (class_exists('Net_DNS_RR_' . $rr->rrtype)) {
            $scn = 'Net_DNS_RR_' . $rr->rrtype;
            return new $scn($rr, $rr->rdata);
        } else {
            return $rr;
        }
    }

    function display()
    {
        echo $this->string() . "\n";
    }

public function string()
    {
        return $this->name . ".\t" . (strlen($this->name) < 16 ? "\t" : '') .
                $this->ttl  . "\t"  .
                $this->class. "\t"  .
                $this->type . "\t"  .
                $this->rdatastr();

    }

public function rdatastr()
    {
        if ($this->rdlength) {
            return '; rdlength = ' . $this->rdlength;
        }
        return '; no data';
    }

public function rdata($packetORrdata, $offset = '')
    {
        if ($offset) {
            return $this->rr_rdata($packetORrdata, $offset);
        } else if (strlen($this->rdata)) {
            return $this->rdata;
        } else {
            return null;
        }
    }


public function rr_rdata($packet, $offset)
    {
        return (strlen($this->rdata) ? $this->rdata : '');
    }

public function data($packet, $offset)
    {
        $data = $packet->dn_comp($this->name, $offset);
        $data .= pack('n', Net_DNS::typesbyname(strtoupper($this->type)));
        $data .= pack('n', Net_DNS::classesbyname(strtoupper($this->class)));
        $data .= pack('N', $this->ttl);

        $offset += strlen($data) + 2;  // The 2 extra bytes are for rdlength

        $rdata = $this->rdata($packet, $offset);
        $data .= pack('n', strlen($rdata));
        $data .= $rdata;

        return $data;
    }
}

/**
 * A representation of a resource record of type <b>AAAA</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_AAAA extends Net_DNS_RR
{
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $address;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            $this->address = Net_DNS_RR_AAAA::ipv6_decompress(substr($this->rdata, 0, $this->rdlength));
        } else {
            if (strlen($data)) {
                if (count($adata = explode(':', $data, 8)) >= 3) {
                    foreach($adata as $addr)
                        if (!preg_match('/^[0-9A-F]{0,4}$/i', $addr)) return;
                    $this->address = trim($data);
                }
            }
        } 
    }

public function rdatastr()
    {
        if (strlen($this->address)) {
            return $this->address;
        }
        return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        return Net_DNS_RR_AAAA::ipv6_compress($this->address);
    }

public function ipv6_compress($addr)
    {
        $numparts = count(explode(':', $addr));
        if ($numparts < 3 || $numparts > 8 ||
            !preg_match('/^([0-9A-F]{0,4}:){0,7}(:[0-9A-F]{0,4}){0,7}$/i', $addr)) {
            /* Non-sensical IPv6 address */
            return pack('n8', 0, 0, 0, 0, 0, 0, 0, 0);
        }
        if (strpos($addr, '::') !== false) {
            /* First we have to normalize the address, turn :: into :0:0:0:0: */
            $filler = str_repeat(':0', 9 - $numparts) . ':';
            if (substr($addr, 0, 2) == '::') {
                $filler = "0$filler";
            }
            if (substr($addr, -2, 2) == '::') {
                $filler .= '0';
            }
            $addr = str_replace('::', $filler, $addr);
        }
        $aparts = explode(':', $addr);
        return pack('n8', hexdec($aparts[0]), hexdec($aparts[1]), hexdec($aparts[2]), hexdec($aparts[3]),
                          hexdec($aparts[4]), hexdec($aparts[5]), hexdec($aparts[6]), hexdec($aparts[7]));
    }

public function ipv6_decompress($pack)
    {
        if (strlen($pack) != 16) {
            /* Must be 8 shorts long */
            return '::';
        }
        $a = unpack('n8', $pack);
        $addr = vsprintf("%x:%x:%x:%x:%x:%x:%x:%x", $a);
        /* Shorthand the first :0:0: set into a :: */
        /* TODO: Make this is a single replacement pattern */
        if (substr($addr, -4) == ':0:0') {
            return preg_replace('/((:0){2,})$/', '::', $addr);
        } elseif (substr($addr, 0, 4) == '0:0:') {
            return '0:0:'. substr($addr, 4);
        } else {
            return preg_replace('/(:(0:){2,})/', '::', $addr);
        }
    }

}

/**
 * A representation of a resource record of type <b>A</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_A extends Net_DNS_RR
{
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $address;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                /*
                 *  We don't have inet_ntoa in PHP?
                 */
                $aparts = unpack('C4b', $this->rdata);
                $addr = $aparts['b1'] . '.' .
                    $aparts['b2'] . '.' .
                    $aparts['b3'] . '.' .
                    $aparts['b4'];
                $this->address = $addr;
            }
        } else {
            if (strlen($data) && ereg("([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)[ \t]*$", $data, $regs)) {
                if (($regs[1] >= 0 && $regs[1] <= 255) &&
                        ($regs[2] >= 0 && $regs[2] <= 255) &&
                        ($regs[3] >= 0 && $regs[3] <= 255) &&
                        ($regs[4] >= 0 && $regs[4] <= 255)) {
                    $this->address = $regs[1] . '.' . $regs[2] . '.' . $regs[3] . '.' .$regs[4];
                }
            }
        } 
    }

public function rdatastr()
    {
        if (strlen($this->address)) {
            return $this->address;
        }
        return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        $aparts = split('\.', $this->address);
        if (count($aparts) == 4) {
            return pack('c4', $aparts[0], $aparts[1], $aparts[2], $aparts[3]);
        }
        return null;
    }

}

/**
 * A representation of a resource record of type <b>CNAME</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_CNAME extends Net_DNS_RR
{
    /* class variable definitions {{{ */
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $cname;

public function Net_DNS_RR_CNAME(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                list($cname, $offset) = Net_DNS_Packet::dn_expand($data, $offset);
                $this->cname = $cname;
            }
        } else {
            $this->cname = ereg_replace("[ \t]+(.+)[\. \t]*$", '\\1', $data);
        }
    }

public function rdatastr()
    {
        if (strlen($this->cname)) {
            return $this->cname . '.';
        }
        return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        if (strlen($this->cname)) {
            return $packet->dn_comp($this->cname, $offset);
        }
        return null;
    }

}
/**
 * A representation of a resource record of type <b>HINFO</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_HINFO extends Net_DNS_RR
{
    /* class variable definitions {{{ */
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
	var $cpu;
    var $os;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                list($cpu, $offset) = Net_DNS_Packet::label_extract($data, $offset);
                list($os,  $offset) = Net_DNS_Packet::label_extract($data, $offset);

                $this->cpu = $cpu;
                $this->os  = $os;
            }
        } else {
            $data = str_replace('\\\\', chr(1) . chr(1), $data); /* disguise escaped backslash */
            $data = str_replace('\\"', chr(2) . chr(2), $data); /* disguise \" */

            ereg('("[^"]*"|[^ \t]*)[ \t]+("[^"]*"|[^ \t]*)[ \t]*$', $data, $regs);
            foreach($regs as $idx => $value) {
                $value = str_replace(chr(2) . chr(2), '\\"', $value);
                $value = str_replace(chr(1) . chr(1), '\\\\', $value);
                $regs[$idx] = stripslashes($value);
            }

            $this->cpu = $regs[1];
			$this->os = $regs[2];
        }
    }

public function rdatastr()
    {
        if ($this->text) {
            return '"' . addslashes($this->cpu) . '" "' . addslashes($this->os) . '"';
        } else return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        if ($this->text) {
            $rdata  = pack('C', strlen($this->cpu)) . $this->cpu;
            $rdata .= pack('C', strlen($this->os))  . $this->os;
            return $rdata;
        }
        return null;
    }

}
/**
 * A representation of a resource record of type <b>MX</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_MX extends Net_DNS_RR
{
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $preference;
    var $exchange;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                $a = unpack("@$offset/npreference", $data);
                $offset += 2;
                list($exchange, $offset) = Net_DNS_Packet::dn_expand($data, $offset);
                $this->preference = $a['preference'];
                $this->exchange = $exchange;
            }
        } else {
            ereg("([0-9]+)[ \t]+(.+)[ \t]*$", $data, $regs);
            $this->preference = $regs[1];
            $this->exchange = ereg_replace('(.*)\.$', '\\1', $regs[2]);
        }
    }

public function rdatastr()
    {
        if (preg_match('/^[0-9]+$/', $this->preference)) {
            return $this->preference . ' ' . $this->exchange . '.';
        }
        return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        if (preg_match('/^[0-9]+$/', $this->preference)) {
            $rdata = pack('n', $this->preference);
            $rdata .= $packet->dn_comp($this->exchange, $offset + strlen($rdata));
            return $rdata;
        }
        return null;
    }

}
/**
 * A representation of a resource record of type <b>NAPTR</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_NAPTR extends Net_DNS_RR
{
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
	var $order;
	var $preference;
	var $flags;
	var $services;
	var $regex;
	var $replacement;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                $a = unpack("@$offset/norder/npreference", $data);
                $offset += 4;
                list($flags, $offset) = Net_DNS_Packet::label_extract($data, $offset);
                list($services, $offset) = Net_DNS_Packet::label_extract($data, $offset);
                list($regex, $offset) = Net_DNS_Packet::label_extract($data, $offset);
                list($replacement, $offset) = Net_DNS_Packet::dn_expand($data, $offset);

                $this->order = $a['order'];
                $this->preference = $a['preference'];
                $this->flags = $flags;
                $this->services = $services;
                $this->regex = $regex;
                $this->replacement = $replacement;
            }
        } else {
            $data = str_replace('\\\\', chr(1) . chr(1), $data); /* disguise escaped backslash */
            $data = str_replace('\\"', chr(2) . chr(2), $data); /* disguise \" */
            ereg('([0-9]+)[ \t]+([0-9]+)[ \t]+("[^"]*"|[^ \t]*)[ \t]+("[^"]*"|[^ \t]*)[ \t]+("[^"]*"|[^ \t]*)[ \t]+(.*?)[ \t]*$', $data, $regs);
            $this->preference = $regs[1];
            $this->weight = $regs[2];
            foreach($regs as $idx => $value) {
                $value = str_replace(chr(2) . chr(2), '\\"', $value);
                $value = str_replace(chr(1) . chr(1), '\\\\', $value);
                $regs[$idx] = stripslashes($value);
            }
            $this->flags = $regs[3];
            $this->services = $regs[4];
            $this->regex = $regs[5];
            $this->replacement = $regs[6];
        }
    }

public function rdatastr()
    {
        if ($this->rdata) {
            return intval($this->order) . ' ' . intval($this->preference) . ' "' . addslashes($this->flags) . '" "' . 
                   addslashes($this->services) . '" "' . addslashes($this->regex) . '" "' . addslashes($this->replacement) . '"';
        } else return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        if ($this->preference) {
            $rdata  = pack('nn', $this->order, $this->preference);
            $rdata .= pack('C', strlen($this->flags))    . $this->flags;
            $rdata .= pack('C', strlen($this->services)) . $this->services;
            $rdata .= pack('C', strlen($this->regex))    . $this->regex;
            $rdata .= $packet->dn_comp($this->replacement, $offset + strlen($rdata));
            return $rdata;
        }
        return null;
    }

}
/**
 * A representation of a resource record of type <b>NS</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_NS extends Net_DNS_RR
{
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $nsdname;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;


        if ($offset) {
            if ($this->rdlength > 0) {
                list($nsdname, $offset) = Net_DNS_Packet::dn_expand($data, $offset);
                $this->nsdname = $nsdname;
            }
        } else {
            $this->nsdname = ereg_replace("[ \t]+(.+)[ \t]*$", '\\1', $data);
        }
    }

public function rdatastr()
    {
        if (strlen($this->nsdname)) {
            return $this->nsdname . '.';
        }
        return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        if (strlen($this->nsdname)) {
            return $packet->dn_comp($this->nsdname, $offset);
        }
        return null;
    }

}
/**
 * A representation of a resource record of type <b>PTR</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_PTR extends Net_DNS_RR
{
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $ptrdname;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;


        if ($offset) {
            if ($this->rdlength > 0) {
                list($ptrdname, $offset) = Net_DNS_Packet::dn_expand($data, $offset);
                $this->ptrdname = $ptrdname;
            }
        } else {
            $this->ptrdname = ereg_replace("[ \t]+(.+)[ \t]*$", '\\1', $data);
        }
    }

public function rdatastr()
    {
        if (strlen($this->ptrdname)) {
            return $this->ptrdname . '.';
        }
        return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        if (strlen($this->ptrdname)) {
            return $packet->dn_comp($this->ptrdname, $offset);
        }
        return null;
    }

}
/**
 * A representation of a resource record of type <b>SOA</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_SOA extends Net_DNS_RR
{
    /* class variable definitions {{{ */
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $mname;
    var $rname;
    var $serial;
    var $refresh;
    var $retry;
    var $expire;
    var $minimum;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                list($mname, $offset) = Net_DNS_Packet::dn_expand($data, $offset);
                list($rname, $offset) = Net_DNS_Packet::dn_expand($data, $offset);

                $a = unpack("@$offset/N5soavals", $data);
                $this->mname = $mname;
                $this->rname = $rname;
                $this->serial = $a['soavals1'];
                $this->refresh = $a['soavals2'];
                $this->retry = $a['soavals3'];
                $this->expire = $a['soavals4'];
                $this->minimum = $a['soavals5'];
            }
        } else {
            if (ereg("([^ \t]+)[ \t]+([^ \t]+)[ \t]+([0-9]+)[^ \t]+([0-9]+)[^ \t]+([0-9]+)[^ \t]+([0-9]+)[^ \t]*$", $string, $regs))
            {
                $this->mname = ereg_replace('(.*)\.$', '\\1', $regs[1]);
                $this->rname = ereg_replace('(.*)\.$', '\\1', $regs[2]);
                $this->serial = $regs[3];
                $this->refresh = $regs[4];
                $this->retry = $regs[5];
                $this->expire = $regs[6];
                $this->minimum = $regs[7];
            }
        }
    }

public function rdatastr($pretty = 0)
    {
        if (strlen($this->mname)) {
            if ($pretty) {
                $rdatastr  = $this->mname . '. ' . $this->rname . ". (\n";
                $rdatastr .= "\t\t\t\t\t" . $this->serial . "\t; Serial\n";
                $rdatastr .= "\t\t\t\t\t" . $this->refresh . "\t; Refresh\n";
                $rdatastr .= "\t\t\t\t\t" . $this->retry . "\t; Retry\n";
                $rdatastr .= "\t\t\t\t\t" . $this->expire . "\t; Expire\n";
                $rdatastr .= "\t\t\t\t\t" . $this->minimum . " )\t; Minimum TTL";
            } else {
                $rdatastr  = $this->mname . '. ' . $this->rname . '. ' .
                    $this->serial . ' ' .  $this->refresh . ' ' .  $this->retry . ' ' .
                    $this->expire . ' ' .  $this->minimum;
            }
            return $rdatastr;
        }
        return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        if (strlen($this->mname)) {
            $rdata = $packet->dn_comp($this->mname, $offset);
            $rdata .= $packet->dn_comp($this->rname, $offset + strlen($rdata));
            $rdata .= pack('N5', $this->serial,
                    $this->refresh,
                    $this->retry,
                    $this->expire,
                    $this->minimum);
            return $rdata;
        }
        return null;
    }

}
/**
 * A representation of a resource record of type <b>SRV</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_SRV extends Net_DNS_RR
{
    /* class variable definitions {{{ */
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $preference;
	var $weight;
	var $port;
    var $target;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                $a = unpack("@$offset/npreference/nweight/nport", $data);
                $offset += 6;
                list($target, $offset) = Net_DNS_Packet::dn_expand($data, $offset);
                $this->preference = $a['preference'];
                $this->weight = $a['weight'];
                $this->port = $a['port'];
                $this->target = $target;
            }
        } else {
            ereg("([0-9]+)[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]+(.+)[ \t]*$", $data, $regs);
            $this->preference = $regs[1];
            $this->weight = $regs[2];
            $this->port = $regs[3];
            $this->target = ereg_replace('(.*)\.$', '\\1', $regs[4]);
        }
    }

public function rdatastr()
    {
        if ($this->port) {
            return intval($this->preference) . ' ' . intval($this->weight) . ' ' . intval($this->port) . ' ' . $this->target . '.';
        }
        return '; no data';
    }


public function rr_rdata($packet, $offset)
    {
        if (isset($this->preference)) {
            $rdata = pack('nnn', $this->preference, $this->weight, $this->port);
            $rdata .= $packet->dn_comp($this->target, $offset + strlen($rdata));
            return $rdata;
        }
        return null;
    }

}

define('NET_DNS_DEFAULT_ALGORITHM', 'hmac-md5.sig-alg.reg.int');
define('NET_DNS_DEFAULT_FUDGE', 300);

/**
 * A representation of a resource record of type <b>TSIG</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_TSIG extends Net_DNS_RR
{
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $time_signed;
    var $fudge;
    var $mac_size;
    var $mac;
    var $original_id;
    var $error;
    var $other_len;
    var $other_data;
    var $key;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                list($alg, $offset) = Net_DNS_Packet::dn_expand($data, $offset);
                $this->algorithm = $alg;

                $d = unpack("\@$offset/nth/Ntl/nfudge/nmac_size", $data);
                $time_high = $d['th'];
                $time_low = $d['tl'];
                $this->time_signed = $time_low;
                $this->fudge = $d['fudge'];
                $this->mac_size = $d['mac_size'];
                $offset += 10;

                $this->mac = substr($data, $offset, $this->mac_size);
                $offset += $this->mac_size;

                $d = unpack("@$offset/noid/nerror/nolen", $data);
                $this->original_id = $d['oid'];
                $this->error = $d['error'];
                $this->other_len = $d['olen'];
                $offset += 6;

                $odata = substr($data, $offset, $this->other_len);
                $d = unpack('nodata_high/Nodata_low', $odata);
                $this->other_data = $d['odata_low'];
            }
        } else {
            if (strlen($data) && preg_match('/^(.*)$/', $data, $regs)) {
                $this->key = $regs[1];
            }

            $this->algorithm   = NET_DNS_DEFAULT_ALGORITHM;
            $this->time_signed = time();

            $this->fudge       = NET_DNS_DEFAULT_FUDGE;
            $this->mac_size    = 0;
            $this->mac         = '';
            $this->original_id = 0;
            $this->error       = 0;
            $this->other_len   = 0;
            $this->other_data  = '';

            // RFC 2845 Section 2.3
            $this->class = 'ANY';
        }
    }

public function rdatastr()
    {
        $error = $this->error;
        if (! $error) {
            $error = 'UNDEFINED';
        }

        if (strlen($this->algorithm)) {
            $rdatastr = $this->algorithm . '. ' . $this->time_signed . ' ' .
                $this->fudge . ' ';
            if ($this->mac_size && strlen($this->mac)) {
                $rdatastr .= ' ' . $this->mac_size . ' ' . base64_encode($this->mac);
            } else {
                $rdatastr .= ' 0 ';
            }
            $rdatastr .= ' ' . $this->original_id . ' ' . $error;
            if ($this->other_len && strlen($this->other_data)) {
                $rdatastr .= ' ' . $this->other_data;
            } else {
                $rdatastr .= ' 0 ';
            }
        } else {
            $rdatastr = '; no data';
        }

        return $rdatastr;
    }

public function rr_rdata($packet, $offset)
    {
        $rdata = '';
        $sigdata = '';

        if (strlen($this->key)) {
            $key = $this->key;
            $key = ereg_replace(' ', '', $key);
            $key = base64_decode($key);

            $newpacket = $packet;
            $newoffset = $offset;
            array_pop($newpacket->additional);
            $newpacket->header->arcount--;
            $newpacket->compnames = array();

            /*
             * Add the request MAC if present (used to validate responses).
             */
            if (isset($this->request_mac)) {
                $sigdata .= pack('H*', $this->request_mac);
            }
            $sigdata .= $newpacket->data();

            /*
             * Don't compress the record (key) name.
             */
            $tmppacket = new Net_DNS_Packet;
            $sigdata .= $tmppacket->dn_comp(strtolower($this->name), 0);

            $sigdata .= pack('n', Net_DNS::classesbyname(strtoupper($this->class)));
            $sigdata .= pack('N', $this->ttl);

            /*
             * Don't compress the algorithm name.
             */
            $tmppacket->compnames = array();
            $sigdata .= $tmppacket->dn_comp(strtolower($this->algorithm), 0);

            $sigdata .= pack('nN', 0, $this->time_signed);
            $sigdata .= pack('n', $this->fudge);
            $sigdata .= pack('nn', $this->error, $this->other_len);

            if (strlen($this->other_data)) {
                $sigdata .= pack('nN', 0, $this->other_data);
            }

            $this->mac = mhash(MHASH_MD5, $sigdata, $key);
            $this->mac_size = strlen($this->mac);

            /*
             * Don't compress the algorithm name.
             */
            unset($tmppacket);
            $tmppacket = new Net_DNS_Packet;
            $rdata .= $tmppacket->dn_comp(strtolower($this->algorithm), 0);

            $rdata .= pack('nN', 0, $this->time_signed);
            $rdata .= pack('nn', $this->fudge, $this->mac_size);
            $rdata .= $this->mac;

            $rdata .= pack('nnn',$packet->header->id,
                    $this->error,
                    $this->other_len);

            if ($this->other_data) {
                $rdata .= pack('nN', 0, $this->other_data);
            }
        }
        return $rdata;
    }

public function error()
    {
        if ($this->error != 0) {
            $rcode = Net_DNS::rcodesbyval($error);
        }
        return $rcode;
    }

}
/**
 * A representation of a resource record of type <b>TXT</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_TXT extends Net_DNS_RR
{
    /* class variable definitions {{{ */
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
	var $text;

    function __construct(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                $maxoffset = $this->rdlength + $offset;
                while ($maxoffset > $offset) {
                    list($text, $offset) = Net_DNS_Packet::label_extract($data, $offset);
                    $this->text[] = $text;
                }
            }
        } else {
            $data = str_replace('\\\\', chr(1) . chr(1), $data); /* disguise escaped backslash */
            $data = str_replace('\\"', chr(2) . chr(2), $data); /* disguise \" */

            ereg('("[^"]*"|[^ \t]*)[ \t]*$', $data, $regs);
            $regs[1] = str_replace(chr(2) . chr(2), '\\"', $regs[1]);
            $regs[1] = str_replace(chr(1) . chr(1), '\\\\', $regs[1]);
            $regs[1] = stripslashes($regs[1]);

            $this->text = $regs[1];
        }
    }

public function rdatastr()
    {
        if ($this->text) {
             if (is_array($this->text)) {
                 $tmp = array();
                 foreach ($this->text as $t) {
                     $tmp[] = '"'.addslashes($t).'"';
                 }
                 return implode(' ',$tmp);
             } else {
                 return '"' . addslashes($this->text) . '"';
             }
        } else return '; no data';
    }

public function rr_rdata($packet, $offset)
    {
        if ($this->text) {
            $rdata  = pack('C', strlen($this->text)) . $this->text;
            return $rdata;
        }
        return null;
    }

}

 
