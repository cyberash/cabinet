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


class Net_DNS_Client extends Net_DNS {}
Net_DNS_Resolver::$Net_DNS_packet_id = mt_rand(0, 65535);

/**
 * Initializes a resolver object
 *
 * Net_DNS allows you to query a nameserver for DNS  lookups.  It bypasses the
 * system resolver library  entirely, which allows you to query any nameserver,
 * set your own values for retries, timeouts, recursion,  etc.
 *
 */
class Net_DNS {
    /**
     * A default resolver object created on instantiation
     *
     * @var Net_DNS_Resolver object
     */
public $resolver;
public $VERSION = '1.00b2'; // This should probably be a define :(
public $PACKETSZ = 512;
public $HFIXEDSZ = 12;
public $QFIXEDSZ = 4;
public $RRFIXEDSZ = 10;
public $INT32SZ = 4;
public $INT16SZ = 2;


	/**
     * Initializes a resolver object
     *
     * @see Net_DNS_Resolver
	 * @param array $defaults
	 * @return Net_DNS
	 */
function __construct($defaults = array()) {
	$this->resolver = new Net_DNS_Resolver($defaults);
}

    /**
     * Translates opcode names to integers
     *
     * Translates the name of a DNS OPCODE into it's assigned  number
     * listed in RFC1035, RFC1996, or RFC2136. Valid  OPCODES are:
     * <ul>
     *   <li>QUERY
     *   <li>IQUERY
     *   <li>STATUS
     *   <li>NS_NOTIFY_OP
     *   <li>UPDATE
     * <ul>
     *
     * @param   string  $opcode A DNS Packet OPCODE name
     * @return  integer The integer value of an OPCODE
     * @see     Net_DNS::opcodesbyval()
     */
static function opcodesbyname($opcode)
    {
        $op = array(
                'QUERY'        => 0,   // RFC 1035
                'IQUERY'       => 1,   // RFC 1035
                'STATUS'       => 2,   // RFC 1035
                'NS_NOTIFY_OP' => 4,   // RFC 1996
                'UPDATE'       => 5,   // RFC 2136
                );
        if (! strlen($op[$opcode])) {
            $op[$opcode] = null;
        }
        return $op[$opcode];
    }

    /**
     * Translates opcode integers into names
     *
     * Translates the integer value of an opcode into it's name
     *
     * @param   integer $opcodeval  A DNS packet opcode integer
     * @return  string  The name of the OPCODE
     * @see     Net_DNS::opcodesbyname()
     */
static function opcodesbyval($opcodeval)
    {
        $opval = array(
                0 => 'QUERY',
                1 => 'IQUERY',
                2 => 'STATUS',
                4 => 'NS_NOTIFY_OP',
                5 => 'UPDATE',
                );
        if (! strlen($opval[$opcodeval])) {
            $opval[$opcodeval] = null;
        }
        return $opval[$opcodeval];
    }

    /**
     * Translates rcode names to integers
     *
     * Translates the name of a DNS RCODE (result code) into it's assigned number.
     * <ul>
     *   <li>NOERROR
     *   <li>FORMERR
     *   <li>SERVFAIL
     *   <li>NXDOMAIN
     *   <li>NOTIMP
     *   <li>REFUSED
     *   <li>YXDOMAIN
     *   <li>YXRRSET
     *   <li>NXRRSET
     *   <li>NOTAUTH
     *   <li>NOTZONE
     * <ul>
     *
     * @param   string  $rcode  A DNS Packet RCODE name
     * @return  integer The integer value of an RCODE
     * @see     Net_DNS::rcodesbyval()
     */
static function rcodesbyname($rcode)
    {
        $rc = array(
                'NOERROR'   => 0,   // RFC 1035
                'FORMERR'   => 1,   // RFC 1035
                'SERVFAIL'  => 2,   // RFC 1035
                'NXDOMAIN'  => 3,   // RFC 1035
                'NOTIMP'    => 4,   // RFC 1035
                'REFUSED'   => 5,   // RFC 1035
                'YXDOMAIN'  => 6,   // RFC 2136
                'YXRRSET'   => 7,   // RFC 2136
                'NXRRSET'   => 8,   // RFC 2136
                'NOTAUTH'   => 9,   // RFC 2136
                'NOTZONE'   => 10,    // RFC 2136
                );
        if (! strlen($rc[$rcode])) {
            $rc[$rcode] = null;
        }
        return $rc[$rcode];
    }

    /**
     * Translates rcode integers into names
     *
     * Translates the integer value of an rcode into it's name
     *
     * @param   integer $rcodeval   A DNS packet rcode integer
     * @return  string  The name of the RCODE
     * @see     Net_DNS::rcodesbyname()
     */
static function rcodesbyval($rcodeval)
    {
        $rc = array(
                0 => 'NOERROR',
                1 => 'FORMERR',
                2 => 'SERVFAIL',
                3 => 'NXDOMAIN',
                4 => 'NOTIMP',
                5 => 'REFUSED',
                6 => 'YXDOMAIN',
                7 => 'YXRRSET',
                8 => 'NXRRSET',
                9 => 'NOTAUTH',
                10 => 'NOTZONE',
                );
        if (! strlen($rc[$rcodeval])) {
            $rc[$rcodeval] = null;
        }
        return $rc[$rcodeval];
    }

    /**
     * Translates RR type names into integers
     *
     * Translates a Resource Record from it's name to it's  integer value.
     * Valid resource record types are:
     *
     * @param   string  $rrtype A DNS packet RR type name
     * @return  integer The integer value of an RR type
     * @see     Net_DNS::typesbyval()
     */
static function typesbyname($rrtype)
    {
        $rc = array(
                'A'             => 1,
                'NS'            => 2,
                'MD'            => 3,
                'MF'            => 4,
                'CNAME'         => 5,
                'SOA'           => 6,
                'MB'            => 7,
                'MG'            => 8,
                'MR'            => 9,
                'NULL'          => 10,
                'WKS'           => 11,
                'PTR'           => 12,
                'HINFO'         => 13,
                'MINFO'         => 14,
                'MX'            => 15,
                'TXT'           => 16,
                'RP'            => 17,
                'AFSDB'         => 18,
                'X25'           => 19,
                'ISDN'          => 20,
                'RT'            => 21,
                'NSAP'          => 22,
                'NSAP_PTR'      => 23,
                'SIG'           => 24,
                'KEY'           => 25,
                'PX'            => 26,
                'GPOS'          => 27,
                'AAAA'          => 28,
                'LOC'           => 29,
                'NXT'           => 30,
                'EID'           => 31,
                'NIMLOC'        => 32,
                'SRV'           => 33,
                'ATMA'          => 34,
                'NAPTR'         => 35,
                'UINFO'         => 100,
                'UID'           => 101,
                'GID'           => 102,
                'UNSPEC'        => 103,
                'TSIG'          => 250,
                'IXFR'          => 251,
                'AXFR'          => 252,
                'MAILB'         => 253,
                'MAILA'         => 254,
                'ANY'           => 255,
                );
        if (empty($rc[$rrtype])) {
            $rc[$rrtype] = null;
        }
        return $rc[$rrtype];
    }

    /**
     * Translates RR type integers into names
     *
     * Translates the integer value of an RR type into it's name
     *
     * @param   integer $rrtypeval  A DNS packet RR type integer
     * @return  string  The name of the RR type
     * @see     Net_DNS::typesbyname()
     */
static function typesbyval($rrtypeval)
    {
        $rc = array(
                1 => 'A',
                2 => 'NS',
                3 => 'MD',
                4 => 'MF',
                5 => 'CNAME',
                6 => 'SOA',
                7 => 'MB',
                8 => 'MG',
                9 => 'MR',
                10 => 'NULL',
                11 => 'WKS',
                12 => 'PTR',
                13 => 'HINFO',
                14 => 'MINFO',
                15 => 'MX',
                16 => 'TXT',
                17 => 'RP',
                18 => 'AFSDB',
                19 => 'X25',
                20 => 'ISDN',
                21 => 'RT',
                22 => 'NSAP',
                23 => 'NSAP_PTR',
                24 => 'SIG',
                25 => 'KEY',
                26 => 'PX',
                27 => 'GPOS',
                28 => 'AAAA',
                29 => 'LOC',
                30 => 'NXT',
                31 => 'EID',
                32 => 'NIMLOC',
                33 => 'SRV',
                34 => 'ATMA',
                35 => 'NAPTR',
                100 => 'UINFO',
                101 => 'UID',
                102 => 'GID',
                103 => 'UNSPEC',
                250 => 'TSIG',
                251 => 'IXFR',
                252 => 'AXFR',
                253 => 'MAILB',
                254 => 'MAILA',
                255 => 'ANY',
                );
        $rrtypeval = preg_replace(array('/\s*/',' /^0*/'), '', $rrtypeval);
        if (empty($rc[$rrtypeval])) {
            $rc[$rrtypeval] = null;
        }
        return $rc[$rrtypeval];
    }

    /**
     * translates a DNS class from it's name to it's  integer value. Valid
     * class names are:
     *
     * @param   string  $class  A DNS packet class type
     * @return  integer The integer value of an class type
     * @see     Net_DNS::classesbyval()
     */
static function classesbyname($class)
    {
        $rc = array(
                'IN'   => 1,   // RFC 1035
                'CH'   => 3,   // RFC 1035
                'HS'   => 4,   // RFC 1035
                'NONE' => 254, // RFC 2136
                'ANY'  => 255  // RFC 1035
                );
        if (!isset($rc[$class])) {
            $rc[$class] = null;
        }
        return $rc[$class];
    }

    /**
     * Translates RR class integers into names
     *
     * Translates the integer value of an RR class into it's name
     *
     * @param   integer $classval   A DNS packet RR class integer
     * @return  string  The name of the RR class
     * @see     Net_DNS::classesbyname()
     */
static function classesbyval($classval)
    {
        $rc = array(
                1 => 'IN',
                3 => 'CH',
                4 => 'HS',
                254 => 'NONE',
                255 => 'ANY'
                );
        $classval = preg_replace(array('/\s*/',' /^0*/'), '', $classval);
        if (empty($rc[$classval])) {
            $rc[$classval] = null;
        }
        return $rc[$classval];
    }

}

/**
 * A object represation of a DNS packet (RFC1035)
 *
 * This object is used to manage a DNS packet.  It contains methods for
 * DNS packet compression as defined in RFC1035, as well as parsing  a DNS
 * packet response from a DNS server, or building a DNS packet from  the
 * instance variables contained in the class.
 *
 * @package Net_DNS
 */
class Net_DNS_Packet
{
    /* class variable definitions {{{ */
    /**
     * debugging flag
     *
     * If set to true (non-zero), debugging code will be displayed as the
     * packet is parsed.
     *
     * @var boolean $debug
     * @access  public
     */
  public $debug;
    /**
     * A packet Header object.
     *
     * An object of type Net_DNS_Header which contains the header
     * information  of the packet.
     *
     * @var object Net_DNS_Header $header
     * @access  public
     */
  public $header;
    /**
     * A hash of compressed labels
     *
     * A list of all labels which have been compressed in the DNS packet
     * and  the location offset of the label within the packet.
     *
     * @var array   $compnames
     */
  public $compnames;
    /**
     * The origin of the packet, if the packet is a server response.
     *
     * This contains a string containing the IP address of the name server
     * from which the answer was given.
     *
     * @var string  $answerfrom
     * @access  public
     */
  public $answerfrom;
    /**
     * The size of the answer packet, if the packet is a server response.
     *
     * This contains a integer containing the size of the DNS packet the
     * server responded with if this packet was received by a DNS server
     * using the query() method.
     *
     * @var string  $answersize
     * @access  public
     */
  public $answersize;
    /**
     * An array of Net_DNS_Question objects
     *
     * Contains all of the questions within the packet.  Each question is
     * stored as an object of type Net_DNS_Question.
     *
     * @var array   $question
     * @access  public
     */
  public $question;
    /**
     * An array of Net_DNS_RR ANSWER objects
     *
     * Contains all of the answer RRs within the packet.  Each answer is
     * stored as an object of type Net_DNS_RR.
     *
     * @var array   $answer
     * @access  public
     */
  public $answer;
    /**
     * An array of Net_DNS_RR AUTHORITY objects
     *
     * Contains all of the authority RRs within the packet.  Each authority is
     * stored as an object of type Net_DNS_RR.
     *
     * @var array   $authority
     * @access  public
     */
  public $authority;
    /**
     * An array of Net_DNS_RR ADDITIONAL objects
     *
     * Contains all of the additional RRs within the packet.  Each additional is
     * stored as an object of type Net_DNS_RR.
     *
     * @var array   $additional
     * @access  public
     */
  public $additional;

    /* }}} */
    /* class constructor - Net_DNS_Packet($debug = false) {{{ */
    /*
     * unfortunately (or fortunately), we can't follow the same
     * silly method for determining if name is a hostname or a packet
     * stream in PHP, since there is no ref() function.  So we're going
     * to define a new method called parse to deal with this
     * circumstance and another method called buildQuestion to build a question.
     * I like it better that way anyway.
     */
    /**
     * Initalizes a Net_DNS_Packet object
     *
     * @param boolean $debug Turns debugging on or off
     */
    function __construct($debug = false)
    {
        $this->debug = $debug;
        $this->compnames = array();
    }

    /* }}} */
    /* Net_DNS_Packet::buildQuestion($name, $type = "A", $class = "IN") {{{ */
    /**
     * Adds a DNS question to the DNS packet
     *
     * @param   string $name    The name of the record to query
     * @param   string $type    The type of record to query
     * @param   string $class   The class of record to query
     * @see Net_DNS::typesbyname(), Net_DNS::classesbyname()
     */
    function buildQuestion($name, $type = 'A', $class = 'IN')
    {
        $this->header = new Net_DNS_Header();
        $this->header->qdcount = 1;
        $this->question[0] = new Net_DNS_Question($name, $type, $class);
        $this->answer = null;
        $this->authority = null;
        $this->additional = null;
        /* Do not print question packet
        if ($this->debug) {
            $this->display();
        }
        */
    }

    /**
     * Parses a DNS packet returned by a DNS server
     *
     * Parses a complete DNS packet and builds an object hierarchy
     * containing all of the parts of the packet:
     * <ul>
     *   <li>HEADER
     *   <li>QUESTION
     *   <li>ANSWER || PREREQUISITE
     *   <li>ADDITIONAL || UPDATE
     *   <li>AUTHORITY
     * </ul>
     *
     * @param string $data  A binary string containing a DNS packet
     * @return boolean true on success, null on parser error
     */
    function parse($data)
    {
        if ($this->debug) {
            echo ';; HEADER SECTION' . "\n";
        }

        $this->header = new Net_DNS_Header($data);

        if ($this->debug) {
            $this->header->display();
        }

        /*
         *  Print and parse the QUESTION section of the packet
         */
        if ($this->debug) {
            echo "\n";
            $section = ($this->header->opcode  == 'UPDATE') ? 'ZONE' : 'QUESTION';
            echo ";; $section SECTION (" . $this->header->qdcount . ' record' .
                ($this->header->qdcount == 1 ? '' : 's') . ")\n";
        }

        $offset = 12;

        $this->question = array();
        for ($ctr = 0; $ctr < $this->header->qdcount; $ctr++) {
            list($qobj, $offset) = $this->parse_question($data, $offset);
            if (is_null($qobj)) {
                return null;
            }

            $this->question[count($this->question)] = $qobj;
            if ($this->debug) {
                echo ";;\n;";
                $qobj->display();
            }
        }

        /*
         *  Print and parse the PREREQUISITE or ANSWER  section of the packet
         */
        if ($this->debug) {
            echo "\n";
            $section = ($this->header->opcode == 'UPDATE') ? 'PREREQUISITE' :'ANSWER';
            echo ";; $section SECTION (" .
                $this->header->ancount . ' record' .
                (($this->header->ancount == 1) ? '' : 's') .
                ")\n";
        }

        $this->answer = array();
        for ($ctr = 0; $ctr < $this->header->ancount; $ctr++) {
            list($rrobj, $offset) = $this->parse_rr($data, $offset);

            if (is_null($rrobj)) {
                return null;
            }
            array_push($this->answer, $rrobj);
            if ($this->debug) {
                $rrobj->display();
            }
        }

        /*
         *  Print and parse the UPDATE or AUTHORITY section of the packet
         */
        if ($this->debug) {
            echo "\n";
            $section = ($this->header->opcode == 'UPDATE') ? 'UPDATE' : 'AUTHORITY';
            echo ";; $section SECTION (" .
                $this->header->nscount . ' record' .
                (($this->header->nscount == 1) ? '' : 's') .
                ")\n";
        }

        $this->authority = array();
        for ($ctr = 0; $ctr < $this->header->nscount; $ctr++) {
            list($rrobj, $offset) = $this->parse_rr($data, $offset);

            if (is_null($rrobj)) {
                return null;
            }
            array_push($this->authority, $rrobj);
            if ($this->debug) {
                $rrobj->display();
            }
        }

        /*
         *  Print and parse the ADDITIONAL section of the packet
         */
        if ($this->debug) {
            echo "\n";
            echo ';; ADDITIONAL SECTION (' .
                $this->header->arcount . ' record' .
                (($this->header->arcount == 1) ? '' : 's') .
                ")\n";
        }

        $this->additional = array();
        for ($ctr = 0; $ctr < $this->header->arcount; $ctr++) {
            list($rrobj, $offset) = $this->parse_rr($data, $offset);

            if (is_null($rrobj)) {
                return null;
            }
            array_push($this->additional, $rrobj);
            if ($this->debug) {
                $rrobj->display();
            }
        }

        return true;
    }

    /* }}} */
    /* Net_DNS_Packet::data() {{{*/
    /**
     * Build a packet from a Packet object hierarchy
     *
     * Builds a valid DNS packet suitable for sending to a DNS server or
     * resolver client containing all of the data in the packet hierarchy.
     *
     * @return string A binary string containing a DNS Packet
     */
    function data()
    {
        $data = $this->header->data();

        for ($ctr = 0; $ctr < $this->header->qdcount; $ctr++) {
            $data .= $this->question[$ctr]->data($this, strlen($data));
        }

        for ($ctr = 0; $ctr < $this->header->ancount; $ctr++) {
            $data .= $this->answer[$ctr]->data($this, strlen($data));
        }

        for ($ctr = 0; $ctr < $this->header->nscount; $ctr++) {
            $data .= $this->authority[$ctr]->data($this, strlen($data));
        }

        for ($ctr = 0; $ctr < $this->header->arcount; $ctr++) {
            $data .= $this->additional[$ctr]->data($this, strlen($data));
        }

        return $data;
    }

    /*}}}*/
    /* Net_DNS_Packet::dn_comp($name, $offset) {{{*/
    /**
     * DNS packet compression method
     *
     * Returns a domain name compressed for a particular packet object, to
     * be stored beginning at the given offset within the packet data.  The
     * name will be added to a running list of compressed domain names for
     * future use.
     *
     * @param string    $name       The name of the label to compress
     * @param integer   $offset     The location offset in the packet to where
     *                              the label will be stored.
     * @return string   $compname   A binary string containing the compressed
     *                              label.
     * @see Net_DNS_Packet::dn_expand()
     */
    function dn_comp($name, $offset)
    {
        $names = explode('.', $name);
        $compname = '';
        while (count($names)) {
            $dname = join('.', $names);
            if (isset($this->compnames[$dname])) {
                $compname .= pack('n', 0xc000 | $this->compnames[$dname]);
                break;
            }

            $this->compnames[$dname] = $offset;
            $first = array_shift($names);
            $length = strlen($first);
            $compname .= pack('Ca*', $length, $first);
            $offset += $length + 1;
        }
        if (! count($names)) {
            $compname .= pack('C', 0);
        }
        return $compname;
    }

    /*}}}*/
    /* Net_DNS_Packet::dn_expand($packet, $offset) {{{ */
    /**
     * DNS packet decompression method
     *
     * Expands the domain name stored at a particular location in a DNS
     * packet.  The first argument is a variable containing  the packet
     * data.  The second argument is the offset within the  packet where
     * the (possibly) compressed domain name is stored.
     *
     * @param   string  $packet The packet data
     * @param   integer $offset The location offset in the packet of the
     *                          label to decompress.
     * @return  array   Returns a list of type array($name, $offset) where
     *                  $name is the name of the label which was decompressed
     *                  and $offset is the offset of the next field in the
     *                  packet.  Returns array(null, null) on error
     */
static public function dn_expand($packet, $offset)
    {
        $packetlen = strlen($packet);
        $int16sz = 2;
        $name = '';
        while (1) {
            if ($packetlen < ($offset + 1)) {
                return array(null, null);
            }

            $a = unpack("@$offset/Cchar", $packet);
            $len = $a['char'];

            if ($len == 0) {
                $offset++;
                break;
            } else if (($len & 0xc0) == 0xc0) {
                if ($packetlen < ($offset + $int16sz)) {
                    return array(null, null);
                }
                $ptr = unpack("@$offset/ni", $packet);
                $ptr = $ptr['i'];
                $ptr = $ptr & 0x3fff;
                $name2 = Net_DNS_Packet::dn_expand($packet, $ptr);

                if (is_null($name2[0])) {
                    return array(null, null);
                }
                $name .= $name2[0];
                $offset += $int16sz;
                break;
            } else {
                $offset++;

                if ($packetlen < ($offset + $len)) {
                    return array(null, null);
                }

                $elem = substr($packet, $offset, $len);
                $name .= $elem . '.';
                $offset += $len;
            }
        }
        $name = ereg_replace('\.$', '', $name);
        return array($name, $offset);
    }

    /*}}}*/
    /* Net_DNS_Packet::label_extract($packet, $offset) {{{ */
    /**
     * DNS packet decompression method
     *
     * Extracts the label stored at a particular location in a DNS
     * packet.  The first argument is a variable containing  the packet
     * data.  The second argument is the offset within the  packet where
     * the (possibly) compressed domain name is stored.
     *
     * @param   string  $packet The packet data
     * @param   integer $offset The location offset in the packet of the
     *                          label to extract.
     * @return  array   Returns a list of type array($name, $offset) where
     *                  $name is the name of the label which was decompressed
     *                  and $offset is the offset of the next field in the
     *                  packet.  Returns array(null, null) on error
     */
    function label_extract($packet, $offset)
    {
        $packetlen = strlen($packet);
        $name = '';
        if ($packetlen < ($offset + 1)) {
            return array(null, null);
        }

        $a = unpack("@$offset/Cchar", $packet);
        $len = $a['char'];
		$offset++;

        if ($len + $offset > $packetlen) {
            $name = substr($packet, $offset);
            $offset = $packetlen;
        } else {
            $name = substr($packet, $offset, $len);
            $offset += $len;
        }
        return array($name, $offset);
    }

    /*}}}*/
    /* Net_DNS_Packet::parse_question($data, $offset) {{{ */
    /**
     * Parses the question section of a packet
     *
     * Examines a DNS packet at the specified offset and parses the data
     * of the QUESTION section.
     *
     * @param   string  $data   The packet data returned from the server
     * @param   integer $offset The location offset of the start of the
     *                          question section.
     * @return  array   An array of type array($q, $offset) where $q
     *                  is a Net_DNS_Question object and $offset is the
     *                  location of the next section of the packet which
     *                  needs to be parsed.
     */
    function parse_question($data, $offset)
    {
        list($qname, $offset) = $this->dn_expand($data, $offset);
        if (is_null($qname)) {
            return array(null, null);
        }

        if (strlen($data) < ($offset + 2 * 2)) {
            return array(null, null);
        }

        $q = unpack("@$offset/n2int", $data);
        $qtype = $q['int1'];
        $qclass = $q['int2'];
        $offset += 2 * 2;

        $qtype = Net_DNS::typesbyval($qtype);
        $qclass = Net_DNS::classesbyval($qclass);

        $q = new Net_DNS_Question($qname, $qtype, $qclass);
        return array($q, $offset);
    }

    /*}}}*/
    /* Net_DNS_Packet::parse_rr($data, $offset) {{{ */
    /**
     * Parses a resource record section of a packet
     *
     * Examines a DNS packet at the specified offset and parses the data
     * of a section which contains RRs (ANSWER, AUTHORITY, ADDITIONAL).
     *
     * @param string    $data   The packet data returned from the server
     * @param integer   $offset The location offset of the start of the resource
     *                          record section.
     * @return  array   An array of type array($rr, $offset) where $rr
     *                  is a Net_DNS_RR object and $offset is the
     *                  location of the next section of the packet which
     *                  needs to be parsed.
     */
    function parse_rr($data, $offset)
    {
        list($name, $offset) = $this->dn_expand($data, $offset);
        if ($name === null) {
            return array(null, null);
        }

        if (strlen($data) < ($offset + 10)) {
            return array(null, null);
        }

        $a = unpack("@$offset/n2tc/Nttl/nrdlength", $data);
        $type = $a['tc1'];
        $class = $a['tc2'];
        $ttl = $a['ttl'];
        $rdlength = $a['rdlength'];

        $type = Net_DNS::typesbyval($type);
        $class = Net_DNS::classesbyval($class);

        $offset += 10;
        if (strlen($data) < ($offset + $rdlength)) {
            return array(null, null);
        }

        $rrobj = &Net_DNS_RR::factory(array($name,
                    $type,
                    $class,
                    $ttl,
                    $rdlength,
                    $data,
                    $offset));

        if (is_null($rrobj)) {
            return array(null, null);
        }

        $offset += $rdlength;

        return array($rrobj, $offset);
    }

    /* }}} */
    /* Net_DNS_Packet::display() {{{ */
    /**
     * Prints out the packet in a human readable formatted string
     */
    function display()
    {
        echo $this->string();
    }

    /*}}}*/
    /* Net_DNS_Packet::string() {{{ */
    /**
     * Builds a human readable formatted string representing a packet
     */
    function string()
    {
        $retval = '';
        if ($this->answerfrom) {
            $retval .= ';; Answer received from ' . $this->answerfrom . '(' .
                $this->answersize . " bytes)\n;;\n";
        }

        $retval .= ";; HEADER SECTION\n";
        $retval .= $this->header->string();
        $retval .= "\n";

        $section = ($this->header->opcode == 'UPDATE') ? 'ZONE' : 'QUESTION';
        $retval .= ";; $section SECTION (" . $this->header->qdcount     .
            ' record' . ($this->header->qdcount == 1 ? '' : 's') .
            ")\n";

        foreach ($this->question as $qr) {
            $retval .= ';; ' . $qr->string() . "\n";
        }

        $section = ($this->header->opcode == 'UPDATE') ? 'PREREQUISITE' : 'ANSWER';
        $retval .= "\n;; $section SECTION (" . $this->header->ancount     .
            ' record' . ($this->header->ancount == 1 ? '' : 's') .
            ")\n";

        if (is_array($this->answer)) {
            foreach ($this->answer as $ans) {
                $retval .= ';; ' . $ans->string() . "\n";
            }
        }

        $section = ($this->header->opcode == 'UPDATE') ? 'UPDATE' : 'AUTHORITY';
        $retval .= "\n;; $section SECTION (" . $this->header->nscount     .
            ' record' . ($this->header->nscount == 1 ? '' : 's') .
            ")\n";

        if (is_array($this->authority)) {
            foreach ($this->authority as $auth) {
                $retval .= ';; ' . $auth->string() . "\n";
            }
        }

        $retval .= "\n;; ADDITIONAL SECTION (" . $this->header->arcount     .
            ' record' . ($this->header->arcount == 1 ? '' : 's') .
            ")\n";

        if (is_array($this->additional)) {
            foreach ($this->additional as $addl) {
                $retval .= ';; ' . $addl->string() . "\n";
            }
        }

        $retval .= "\n\n";
        return $retval;
    }

}



/**
 * Object representation of the HEADER section of a DNS packet
 *
 * The Net_DNS::Header class contains the values of a DNS  packet.  It parses
 * the header of a DNS packet or can  generate the binary data
 * representation of the packet.  The format of the header is described in
 * RFC1035.
 *
 * @package Net_DNS
 */
class Net_DNS_Header
{
    /* class variable definitions {{{ */
    /**
     * The packet's request id
     *
     * The request id of the packet represented as  a 16 bit integer.
     */
  public $id;
    /**
     * The QR bit in a DNS packet header
     *
     * The QR bit as described in RFC1035.  QR is set to 0 for queries, and
     * 1 for repsones.
     */
  public $qr;
    /**
     * The OPCODE name of this packet.
     *
     * The string value (name) of the opcode for the DNS packet.
     */
  public $opcode;
    /**
     * The AA (authoritative answer) bit in a DNS packet header
     *
     * The AA bit as described in RFC1035.  AA is set to  1 if the answer
     * is authoritative.  It has no meaning if QR is set to 0.
     */
  public $aa;
    /**
     * The TC (truncated) bit in a DNS packet header
     *
     * This flag is set to 1 if the response was truncated.  This flag has
     * no meaning in a query packet.
     */
  public $tc;
    /**
     * The RD (recursion desired) bit in a DNS packet header
     *
     * This bit should be set to 1 in a query if recursion  is desired by
     * the DNS server.
     */
  public $rd;
    /**
     * The RA (recursion available) bit in a DNS packet header
     *
     * This bit is set to 1 by the DNS server if the server is willing to
     * perform recursion.
     */
  public $ra;
    /**
     * The RCODE name for this packet.
     *
     * The string value (name) of the rcode for the DNS packet.
     */
  public $rcode;
    /**
     * Number of questions contained within the packet
     *
     * 16bit integer representing the number of questions in the question
     * section of the DNS packet.
     *
     * @var integer $qdcount
     * @see     Net_DNS_Question class
     */
  public $qdcount;
    /**
     * Number of answer RRs contained within the packet
     *
     * 16bit integer representing the number of answer resource records
     * contained in the answer section of the DNS packet.
     *
     * @var integer $ancount
     * @see     Net_DNS_RR class
     */
  public $ancount;
    /**
     * Number of authority RRs within the packet
     *
     * 16bit integer representing the number of authority (NS) resource
     * records  contained in the authority section of the DNS packet.
     *
     * @var integer $nscount
     * @see     Net_DNS_RR class
     */
  public $nscount;
    /**
     * Number of additional RRs within the packet
     *
     * 16bit integer representing the number of additional resource records
     * contained in the additional section of the DNS packet.
     *
     * @var integer $arcount
     * @see     Net_DNS_RR class
     */
  public $arcount;

    /* }}} */
    /* class constructor - Net_DNS_Header($data = "") {{{ */
    /**
     * Initializes the default values for the Header object.
     *
     * Builds a header object from either default values, or from a DNS
     * packet passed into the constructor as $data
     *
     * @param string $data  A DNS packet of which the header will be parsed.
     * @return  object  Net_DNS_Header
     * @access public
     */
    function __construct($data = '')
    {
        if ($data != '') {
            /*
             * The header MUST be at least 12 bytes.
             * Passing the full datagram to this constructor
             * will examine only the header section of the DNS packet
             */
            if (strlen($data) < 12)
                return false;

            $a = unpack('nid/C2flags/n4counts', $data);
            $this->id      = $a['id'];
            $this->qr      = ($a['flags1'] >> 7) & 0x1;
            $this->opcode  = ($a['flags1'] >> 3) & 0xf;
            $this->aa      = ($a['flags1'] >> 2) & 0x1;
            $this->tc      = ($a['flags1'] >> 1) & 0x1;
            $this->rd      = $a['flags1'] & 0x1;
            $this->ra      = ($a['flags2'] >> 7) & 0x1;
            $this->rcode   = $a['flags2'] & 0xf;
            $this->qdcount = $a['counts1'];
            $this->ancount = $a['counts2'];
            $this->nscount = $a['counts3'];
            $this->arcount = $a['counts4'];
        } else {
            $this->id      = Net_DNS_Resolver::nextid();
            $this->qr      = 0;
            $this->opcode  = 0;
            $this->aa      = 0;
            $this->tc      = 0;
            $this->rd      = 1;
            $this->ra      = 0;
            $this->rcode   = 0;
            $this->qdcount = 1;
            $this->ancount = 0;
            $this->nscount = 0;
            $this->arcount = 0;
        }

        if (Net_DNS::opcodesbyval($this->opcode)) {
            $this->opcode = Net_DNS::opcodesbyval($this->opcode);
        }
        if (Net_DNS::rcodesbyval($this->rcode)) {
            $this->rcode = Net_DNS::rcodesbyval($this->rcode);
        }
    }

    /**
     * Displays the properties of the header.
     *
     * Displays the properties of the header.
     *
     * @access public
     */
    function display()
    {
        echo $this->string();
    }

    /**
     * Returns a formatted string containing the properties of the header.
     *
     * @return string   a formatted string containing the properties of the header.
     * @access public
     */
    function string()
    {
        $retval = ';; id = ' . $this->id . "\n";
        if ($this->opcode == 'UPDATE') {
            $retval .= ';; qr = ' . $this->qr . '    ' .
                'opcode = ' . $this->opcode . '    '   .
                'rcode = ' . $this->rcode . "\n";
            $retval .= ';; zocount = ' . $this->qdcount . '  ' .
                'prcount = ' . $this->ancount . '  '           .
                'upcount = ' . $this->nscount . '  '           .
                'adcount = ' . $this->arcount . "\n";
        } else {
            $retval .= ';; qr = ' . $this->qr . '    ' .
                'opcode = ' . $this->opcode . '    '   .
                'aa = ' . $this->aa . '    '           .
                'tc = ' . $this->tc . '    '           .
                'rd = ' . $this->rd . "\n";

            $retval .= ';; ra = ' . $this->ra . '    ' .
                'rcode  = ' . $this->rcode . "\n";

            $retval .= ';; qdcount = ' . $this->qdcount . '  ' .
                'ancount = ' . $this->ancount . '  '    .
                'nscount = ' . $this->nscount . '  '    .
                'arcount = ' . $this->arcount . "\n";
        }
        return $retval;
    }

    /**
     * Returns the binary data containing the properties of the header
     *
     * Packs the properties of the Header object into a binary string
     * suitable for using as the Header section of a DNS packet.
     *
     * @return string   binary representation of the header object
     * @access public
     */
    function data()
    {
        $opcode = Net_DNS::opcodesbyname($this->opcode);
        $rcode  = Net_DNS::rcodesbyname($this->rcode);

        $byte2 = ($this->qr << 7)
            | ($opcode << 3)
            | ($this->aa << 2)
            | ($this->tc << 1)
            | ($this->rd);

        $byte3 = ($this->ra << 7) | $rcode;

        return pack('nC2n4', $this->id,
                $byte2,
                $byte3,
                $this->qdcount,
                $this->ancount,
                $this->nscount,
                $this->arcount);
    }
}


/**
 * Builds or parses the QUESTION section of a DNS packet
 *
 * Builds or parses the QUESTION section of a DNS packet
 *
 * @package Net_DNS
 */
class Net_DNS_Question
{
    /* class variable definitions {{{ */
  public $qname = null;
  public $qtype = null;
  public $qclass = null;

    /* }}} */
    /* class constructor Net_DNS_Question($qname, $qtype, $qclass) {{{ */
    function __construct($qname, $qtype, $qclass)
    {
        $qtype  = !is_null($qtype)  ? strtoupper($qtype)  : 'ANY';
        $qclass = !is_null($qclass) ? strtoupper($qclass) : 'ANY';

        // Check if the caller has the type and class reversed.
        // We are not that kind for unknown types.... :-)
        if ( ( is_null(Net_DNS::typesbyname($qtype)) ||
               is_null(Net_DNS::classesbyname($qtype)) )
          && !is_null(Net_DNS::classesbyname($qclass))
          && !is_null(Net_DNS::typesbyname($qclass)))
        {
            list($qtype, $qclass) = array($qclass, $qtype);
        }
        $qname = preg_replace(array('/^\.+/', '/\.+$/'), '', $qname);
        $this->qname = $qname;
        $this->qtype = $qtype;
        $this->qclass = $qclass;
    }
    /* }}} */
    /* Net_DNS_Question::display() {{{*/
    function display()
    {
        echo $this->string() . "\n";
    }

    /*}}}*/
    /* Net_DNS_Question::string() {{{*/
    function string()
    {
        return $this->qname . ".\t" . $this->qclass . "\t" . $this->qtype;
    }

    /*}}}*/
    /* Net_DNS_Question::data(&$packet, $offset) {{{*/
    function data($packet, $offset)
    {
        $data = $packet->dn_comp($this->qname, $offset);
        $data .= pack('n', Net_DNS::typesbyname(strtoupper($this->qtype)));
        $data .= pack('n', Net_DNS::classesbyname(strtoupper($this->qclass)));
        return $data;
    }

}

/**
 * A DNS Resolver library
 *
 * Resolver library.  Builds a DNS query packet, sends the packet to the
 * server and parses the reponse.
 *
 * @package Net_DNS
 */
class Net_DNS_Resolver
{
  static $Net_DNS_packet_id;
    /**
     * An array of all nameservers to query
     *
     * @var array $nameservers
     * @access public
     */
  public $nameservers;
    /**
     * The UDP port to use for the query (default = 53)
     *
     * @var integer $port
     * @access public
     */
  public $port;
    /**
     * The domain in which the resolver client host resides.
     *
     * @var string $domain
     * @access public
     */
  public $domain;
    /**
     * The searchlist to apply to unqualified hosts
     *
     * An array of strings containg domains to apply to unqualified hosts
     * passed to the resolver.
     *
     * @var array $searchlist
     * @access public
     */
  public $searchlist;
    /**
     * The number of seconds between retransmission of unaswered queries
     *
     * @var integer $retrans
     * @access public
     */
  public $retrans;
    /**
     * The number of times unanswered requests should be retried
     *
     * @var integer $retry
     * @access public
     */
  public $retry;
    /**
     * Whether or not to use TCP (Virtual Circuits) instead of UDP
     *
     * If set to 0, UDP will be used unless TCP is required.  TCP is
     * required for questions or responses greater than 512 bytes.
     *
     * @var boolean $usevc
     * @access public
     */
  public $usevc;
    /**
     * Unknown
     */
  public $stayopen;
    /**
     * Ignore TC (truncated) bit
     *
     * If the server responds with the TC bit set on a response, and $igntc
     * is set to 0, the resolver will automatically retransmit the request
     * using virtual circuits (TCP).
     *
     * @access public
     * @var boolean $igntc
     */
  public $igntc;
    /**
     * Recursion Desired
     *
     * Sets the value of the RD (recursion desired) bit in the header. If
     * the RD bit is set to 0, the server will not perform recursion on the
     * request.
     *
     * @var boolean $recurse
     * @access public
     */
  public $recurse;
    /**
     * Unknown
     */
  public $defnames;
    /**
     * Unknown
     */
  public $dnsrch;
    /**
     * Contains the value of the last error returned by the resolver.
     *
     * @var string $errorstring
     * @access public
     */
  public $errorstring;
    /**
     * The origin of the packet.
     *
     * This contains a string containing the IP address of the name server
     * from which the answer was given.
     *
     * @var string $answerfrom
     * @access public
     */
  public $answerfrom;
    /**
     * The size of the answer packet.
     *
     * This contains a integer containing the size of the DNS packet the
     * server responded with.
     *
     * @var string $answersize
     * @access public
     */
  public $answersize;
    /**
     * The number of seconds after which a TCP connection should timeout
     *
     * @var integer $tcp_timeout
     * @access public
     */
  public $tcp_timeout;
    /**
     * The location of the system resolv.conf file.
     *
     * @var string $resolv_conf
     */
  public $resolv_conf = '/etc/resolv.conf';
    /**
     * The name of the user defined resolv.conf
     *
     * The resolver will attempt to look in both the current directory as
     * well as the user's home directory for a user defined resolver
     * configuration file
     *
     * @var string $dotfile
     * @see Net_DNS_Resolver::$confpath
     */
  public $dotfile = '.resolv.conf';
    /**
     * A array of directories to search for the user's resolver config
     *
     * @var string $confpath
     * @see Net_DNS_Resolver::$dotfile
     */
  public $confpath;
    /**
     * debugging flag
     *
     * If set to true (non-zero), debugging code will be displayed as the
     * resolver makes the request.
     *
     * @var boolean $debug;
     * @access public
     */
  public $debug;
    /**
     * An array of sockets connected to a name servers
     *
     * @var array $sockets
     * @access private
     */
  public $sockets;
    /**
     * axfr tcp socket
     *
     * Used to store a PHP socket resource for a connection to a server
     *
     * @var resource $_axfr_sock;
     * @access private
     */
  public $_axfr_sock;
    /**
     * axfr resource record list
     *
     * Used to store a resource record list from a zone transfer
     *
     * @var resource $_axfr_rr;
     * @access private
     */
  public $_axfr_rr;
    /**
     * axfr soa count
     *
     * Used to store the number of soa records received from a zone transfer
     *
     * @var resource $_axfr_soa_count;
     * @access private
     */
  public $_axfr_soa_count;


    /**
     * Initializes the Resolver Object
     *
     * @return Net_DNS_Resolver
     */
    function __construct($defaults = array())
    {
        $mydefaults = array(
                'nameservers' => array(),
                'port'        => '53',
                'domain'      => '',
                'searchlist'  => array(),
                'retrans'     => 5,
                'retry'       => 3,
                'usevc'       => 0,
                'stayopen'    => 0,
                'igntc'       => 0,
                'recurse'     => 1,
                'defnames'    => 1,
                'dnsrch'      => 1,
                'debug'       => 0,
                'errorstring' => '',
                'tcp_timeout' => 5
                );
        foreach ($mydefaults as $k => $v) $this->{$k} = isset($defaults[$k]) ? $defaults[$k] : $v;
        $this->confpath[0] = getenv('HOME');
        $this->confpath[1] = '.';
        if(empty($this->nameservers)) $this->res_init();
    }

    /**
     * Initalizes the resolver library
     *
     * res_init() searches for resolver library configuration files and
     * initializes the various properties of the resolver object.
     *
     * @see Net_DNS_Resolver::$resolv_conf, Net_DNS_Resolver::$dotfile,
     *      Net_DNS_Resolver::$confpath, Net_DNS_Resolver::$searchlist,
     *      Net_DNS_Resolver::$domain, Net_DNS_Resolver::$nameservers
     * @access public
     */
protected function res_init() {

	$this->read_env();

	if (file_exists($this->resolv_conf) && is_readable($this->resolv_conf)) {
		$this->read_config($this->resolv_conf);
	}

	foreach ($this->confpath as $dir) {
		$file = $dir.DIRECTORY_SEPARATOR.$this->dotfile;
		if (file_exists($file) && is_readable($file)) {
			$this->read_config($file);
		}
	}

	if (!strlen($this->domain) && sizeof($this->searchlist)) {
		$this->domain = $this->searchlist[0];
	} else if (! sizeof($this->searchlist) && strlen($this->domain)) {
		$this->searchlist = array($this->domain);
	}
}

    /* }}} */
    /* Net_DNS_Resolver::read_config {{{ */
    /**
     * Reads and parses a resolver configuration file
     *
     * @param string $file The name of the file to open and parse
     */
    function read_config($file)
    {
    	if (is_readable($file)) {
	        if (! ($f = fopen($file, 'r'))) {
	            $this->error = "can't open $file";
	        }
    	}

    	if (!is_resource($f)) {
    		$this->error = "can't open $file";
    	} else {
	        while (! feof($f)) {
	            $line = chop(fgets($f, 10240));
	            $line = ereg_replace('(.*)[;#].*', '\\1', $line);
	            if (ereg("^[ \t]*$", $line, $regs)) {
	                continue;
	            }
	            ereg("^[ \t]*([^ \t]+)[ \t]+([^ \t]+)", $line, $regs);
	            $option = $regs[1];
	            $value = $regs[2];

	            switch ($option) {
	                case 'domain':
	                    $this->domain = $regs[2];
	                    break;
	                case 'search':
	                    $this->searchlist[] = $regs[2];
	                    break;
	                case 'nameserver':
	                    foreach (split(' ', $regs[2]) as $ns) {
	                        $this->nameservers[] = $ns;
	                    }
	                    break;
	            }
	        }
	        fclose($f);
    	}
    }

    /* }}} */
    /* Net_DNS_Resolver::read_env() {{{ */
    /**
     * Examines the environment for resolver config information
     */
    function read_env()
    {
        if (getenv('RES_NAMESERVERS')) {
            $this->nameservers = explode(' ', getenv('RES_NAMESERVERS'));
        }

        if (getenv('RES_SEARCHLIST')) {
            $this->searchlist = explode(' ', getenv('RES_SEARCHLIST'));
        }

        if (getenv('LOCALDOMAIN')) {
            $this->domain = getenv('LOCALDOMAIN');
        }

        if (getenv('RES_OPTIONS')) {
            $env = split(' ', getenv('RES_OPTIONS'));
            foreach ($env as $opt) {
                list($name, $val) = split(':', $opt);
                if ($val == '') $val = 1;
                $this->{$name} = $val;
            }
        }
    }

    /* }}} */
    /* Net_DNS_Resolver::string() {{{ */
    /**
     * Builds a string containing the current state of the resolver
     *
     * Builds formatted string containing the state of the resolver library suited
     * for display.
     *
     * @access public
     */
    function string()
    {
        $state  = ";; Net_DNS_Resolver state:\n";
        $state .= ';;  domain       = ' . $this->domain . "\n";
        $state .= ';;  searchlist   = ' . implode(' ', $this->searchlist) . "\n";
        $state .= ';;  nameservers  = ' . implode(' ', $this->nameservers) . "\n";
        $state .= ';;  port         = ' . $this->port . "\n";
        $state .= ';;  tcp_timeout  = ';
        $state .= ($this->tcp_timeout ? $this->tcp_timeout : 'indefinite') . "\n";
        $state .= ';;  retrans  = ' . $this->retrans . '  ';
        $state .= 'retry    = ' . $this->retry . "\n";
        $state .= ';;  usevc    = ' . $this->usevc . '  ';
        $state .= 'stayopen = ' . $this->stayopen . '    ';
        $state .= 'igntc = ' . $this->igntc . "\n";
        $state .= ';;  defnames = ' . $this->defnames . '  ';
        $state .= 'dnsrch   = ' . $this->dnsrch . "\n";
        $state .= ';;  recurse  = ' . $this->recurse . '  ';
        $state .= 'debug    = ' . $this->debug . "\n";
        return $state;
    }

    /**
     * Returns the next request Id to be used for the DNS packet header
     */
static function nextid()
    {
        if (Net_DNS_Resolver::$Net_DNS_packet_id++ > 65535) {
        	Net_DNS_Resolver::$Net_DNS_packet_id = 1;
        }
        return Net_DNS_Resolver::$Net_DNS_packet_id;
    }

    /**
     * Gets or sets the nameservers to be queried.
     *
     * Returns the current nameservers if an array of new nameservers is not
     * given as the argument OR sets the nameservers to the given nameservers.
     *
     * Nameservers not specified by ip address must be able to be resolved by
     * the default settings of a new Net_DNS_Resolver.
     *
     * @access public
     */
    function nameservers($nsa = array())
    {
        $defres = new Net_DNS_Resolver();

        if (is_array($nsa)) {
            $a = array();
            foreach ($nsa as $ns) {
                if (preg_match('/^(\d+(:?\.\d+){0,3})$/', $ns)) {
                    $a[] = ($ns == 0) ? '0.0.0.0' : $ns;
                } else {
                    $names = array();
                    if (!preg_match('/\./', $ns)) {
                        if (!empty($defres->searchlist)) {
                            foreach ($defres->searchlist as $suffix) {
                                $names[] = $ns .'.' . $suffix;
                            }
                        } elseif (!empty($defres->domain)) {
                            $names[] = $ns .'.'. $defres->domain;
                        }
                    } else {
                        $names[] = $ns;
                    }
                    $packet = $defres->search($ns);
                    if (is_object($packet)) {
                        $addresses = $this->cname_addr($names, $packet);
                        foreach ($addresses as $b) {
                            $a[] = $b;
                        }
                        $a = array_unique($a);
                    }
                }
            }
            if (count($a)) {
                $this->nameservers = $a;
            }
        }
        return $this->nameservers;
    }

    /* not tested -- Net_DNS_Resolver::cname_addr() {{{ */
    function cname_addr($names, $packet)
    {
        $addr = array();
        //my $oct2 = '(?:2[0-4]\d|25[0-5]|[0-1]?\d\d|\d)';
        foreach ($packet->answer as $rr) {
            if (in_array($rr->name, $names)) {
                if ($rr->type == 'CNAME') {
                    $names[] = $rr->cname;
                } elseif ($rr->type == 'A') {
                    // Run a basic taint check.
                    //next RR unless $rr->address =~ m/^($oct2\.$oct2\.$oct2\.$oct2)$/o;

                    $addr[] = $rr->address;
                }
            }
		}
		return $addr;
	}

    /* Net_DNS_Resolver::search() {{{ */
    /**
     * Searches nameservers for an answer
     *
     * Goes through the search list and attempts to resolve name based on
     * the information in the search list.
     *
     * @param string $name The name (LHS) of a resource record to query.
     * @param string $type The type of record to query.
     * @param string $class The class of record to query.
     * @return mixed    an object of type Net_DNS_Packet on success,
     *                  or false on failure.
     * @see Net_DNS::typesbyname(), Net_DNS::classesbyname()
     * @access public
     */
    function search($name, $type = 'A', $class = 'IN')
    {
        /*
         * If the name looks like an IP address then do an appropriate
         * PTR query.
         */
        if (preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $name, $regs)) {
            $name = $regs[4].'.'.$regs[3].'.'.$regs[2].'.'.$regs[1].'.in-addr.arpa.';
            $type = 'PTR';
        }

        /*
         * If the name contains at least one dot then try it as is first.
         */
        if (strstr($name, '.')) {
            if ($this->debug) {
                echo ";; search($name, $type, $class)\n";
            }
            $ans = $this->query($name, $type, $class);
            if (is_object($ans) && ($ans->header->ancount > 0)) {
                return $ans;
            }
        }

        /*
         * If the name does not end in a dot then apply the search list.
         */
        $domain = '';
        if ((! preg_match('/\.$/', $name)) && $this->dnsrch) {
            foreach ($this->searchlist as $domain) {
                $newname = "$name.$domain";
                if ($this->debug) {
                    echo ";; search($newname, $type, $class)\n";
                }
                $ans = $this->query($newname, $type, $class);
                if (is_object($ans) && ($ans->header->ancount > 0)) {
                    return $ans;
                }
            }
        }

        /*
         * Finally, if the name has no dots then try it as is.
         */
        if (strpos($name, '.') === false) {
            if ($this->debug) {
                echo ";; search($name, $type, $class)\n";
            }
            $ans = $this->query($name.'.', $type, $class);
            if (is_object($ans) && ($ans->header->ancount > 0)) {
                return $ans;
            }
        }

        /*
         * No answer was found.
         */
        return false;
    }

    /* Net_DNS_Resolver::rawQuery() {{{ */
    /**
     * Queries nameservers for an answer
     *
     * Queries the nameservers listed in the resolver configuration for an
     * answer to a question packet.
     *
     * @param string $name The name (LHS) of a resource record to query.
     * @param string $type The type of record to query.
     * @param string $class The class of record to query.
     * @return mixed an object of type Net_DNS_Packet, regardless of whether the packet
     *               has an answer or not
     * @see Net_DNS::typesbyname(), Net_DNS::classesbyname()
     * @access public
     */
    function rawQuery($name, $type = 'A', $class = 'IN')
    {
        /*
         * If the name does not contain any dots then append the default domain.
         */
        if ((strchr($name, '.') < 0) && $this->defnames) {
            $name .= '.' . $this->domain;
        }

        /*
         * If the name looks like an IP address then do an appropriate
         * PTR query.
         */
        if (preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $name, $regs)) {
            $name = $regs[4].'.'.$regs[3].'.'.$regs[2].'.'.$regs[1].'.in-addr.arpa.';
            $type = 'PTR';
        }

        if ($this->debug) {
            echo ";; query($name, $type, $class)\n";
        }
        $packet = new Net_DNS_Packet($this->debug);
        $packet->buildQuestion($name, $type, $class);
        $packet->header->rd = $this->recurse;
        $ans = $this->send($packet);
        return $ans;
    }

    /* Net_DNS_Resolver::query() {{{ */
    /**
     * Queries nameservers for an answer
     *
     * Queries the nameservers listed in the resolver configuration for an
     * answer to a question packet.
     *
     * @param string $name The name (LHS) of a resource record to query.
     * @param string $type The type of record to query.
     * @param string $class The class of record to query.
     * @return mixed    an object of type Net_DNS_Packet on success,
     *                  or false on failure.
     * @see Net_DNS::typesbyname(), Net_DNS::classesbyname()
     * @access public
     */
    function query($name, $type = 'A', $class = 'IN')
    {
        $ans = $this->rawQuery($name, $type, $class);
        if (is_object($ans) && $ans->header->ancount > 0) {
            return $ans;
        }
        return false;
    }

    /* Net_DNS_Resolver::send($packetORname, $qtype = '', $qclass = '') {{{ */
    /**
     * Sends a packet to a nameserver
     *
     * Determines the appropriate communication method (UDP or TCP) and
     * sends a DNS packet to a nameserver.  Use of the this function
     * directly  is discouraged. $packetORname should always be a properly
     * formatted binary DNS packet.  However, it is possible to send a
     * query here and bypass Net_DNS_Resolver::query()
     *
     * @param string $packetORname      A binary DNS packet stream or a
     *                                  hostname to query
     * @param string $qtype     This should not be used
     * @param string $qclass    This should not be used
     * @return object Net_DNS_Packet    An answer packet object
     */
    function send($packetORname, $qtype = '', $qclass = '')
    {
        $packet = $this->make_query_packet($packetORname, $qtype, $qclass);
        $packet_data = $packet->data();

        if ($this->usevc != 0 || strlen($packet_data > 512)) {
            $ans = $this->send_tcp($packet, $packet_data);
        } else {
            $ans = $this->send_udp($packet, $packet_data);

            if ($ans && $ans->header->tc && $this->igntc != 0) {
                if ($this->debug) {
                    echo ";;\n;; packet truncated: retrying using TCP\n";
                }
                $ans = $this->send_tcp($packet, $packet_data);
            }
        }
        return $ans;
    }

    /* }}} */
    /* Net_DNS_Resolver::printhex($packet_data) {{{ */
    /**
     * Prints packet data as hex code.
     */
    function printhex($data)
    {
        $data = '  ' . $data;
        $start = 0;
        while ($start < strlen($data)) {
            printf(';; %03d: ', $start);
            for ($ctr = $start; $ctr < $start+16; $ctr++) {
                if ($ctr < strlen($data)) {
                    printf('%02x ', ord($data[$ctr]));
                } else {
                    echo '   ';
                }
            }
            echo '   ';
            for ($ctr = $start; $ctr < $start+16; $ctr++) {
                if (ord($data[$ctr]) < 32 || ord($data[$ctr]) > 127) {
                    echo '.';
                } else {
                    echo $data[$ctr];
                }
            }
            echo "\n";
            $start += 16;
        }
    }
    /* }}} */
    /* Net_DNS_Resolver::send_tcp($packet, $packet_data) {{{ */
    /**
     * Sends a packet via TCP to the list of name servers.
     *
     * @param string $packet    A packet object to send to the NS list
     * @param string $packet_data   The data in the packet as returned by
     *                              the Net_DNS_Packet::data() method
     * @return object Net_DNS_Packet Returns an answer packet object
     */
private function send_tcp($packet, $packet_data) {
	$timeout = $this->tcp_timeout;

	foreach ($this->nameservers as $ns) {
            $sock_key = 'tcp://'.$ns.':'.$this->port;
            if ($this->debug) {
                echo ";; send_tcp($sock_key)\n";
            }
            if (isset($this->sockets[$sock_key]) && is_resource($this->sockets[$sock_key])) {
                $sock = &$this->sockets[$sock_key];
            } else {
                if (!($sock = stream_socket_client($sock_key, $errno, $errstr, $timeout))) {
                    $this->errorstring = 'connection failed';
                    if ($this->debug) {
                        echo ";; ERROR: send_tcp: connection failed: $errstr\n";
                    }
                    continue;
                }
                $this->sockets[$sock_key] = $sock;
                unset($sock);
                $sock = &$this->sockets[$sock_key];
            }
            $lenmsg = pack('n', strlen($packet_data));
            if ($this->debug) {
                echo ';; sending ' . strlen($packet_data) . " bytes\n";
            }

            if (($sent = fwrite($sock, $lenmsg)) == -1) {
                $this->errorstring = 'length send failed';
                if ($this->debug) {
                    echo ";; ERROR: send_tcp: length send failed\n";
                }
                continue;
            }

            if (($sent = fwrite($sock, $packet_data)) == -1) {
                $this->errorstring = 'packet send failed';
                if ($this->debug) {
                    echo ";; ERROR: send_tcp: packet data send failed\n";
                }
            }

		$r = array($sock); $w=null; $e=null;
		stream_select($r, $w, $e, $timeout);
		$buf = stream_get_contents($sock, 2);
		$len = unpack('nint', $buf);
		$len = (int) @$len['int'];

		/* If $buf is empty, we want to supress errors
		long enough to reach the continue; down the line */
		if (!$len) continue;
		$r = array($sock); $w=null; $e=null;
		$ans = stream_select($r, $w, $e, $timeout);
		if($ans === false) {
			$this->errorstring='stream_select() failed to set timeout';
			continue;
		}elseif($ans == 0) {
			$this->errorstring='Socket Error: timeout';
			continue;
		}
		$buf = stream_get_contents($sock,$len);

            $actual = strlen($buf);
            $this->answerfrom = $ns;
            $this->answersize = $len;
            if ($this->debug) {
                echo ";; received $actual bytes\n";
            }
            if ($actual != $len) {
                $this->errorstring = "expected $len bytes, recive $actual bytes";
                if ($this->debug) {
                    echo ';; send_tcp: ' . $this->errorstring;
                }
                continue;
            }

            $ans = new Net_DNS_Packet($this->debug);
            if ($ans->parse($buf) == null) {
                continue;
            }
            $this->errorstring = $ans->header->rcode;
            $ans->answerfrom = $this->answerfrom;
            $ans->answersize = $this->answersize;
            return $ans;
        }
}

    /**
     * Sends a packet via UDP to the list of name servers.
     *
     * This function sends a packet to a nameserver.
     *
     * @param object Net_DNS_Packet $packet A packet object to send to the NS list
     * @param string $packet_data   The data in the packet as returned by
     *                              the Net_DNS_Packet::data() method
     * @return object Net_DNS_Packet Returns an answer packet object
     */
private function send_udp($packet, $packet_data) {
	$retrans = $this->retrans;
	$timeout = $retrans;
	$ctr = 0;
	// Create a socket handle for each nameserver
	foreach ($this->nameservers as $nameserver) {
		$sock_key = 'udp://'.$nameserver.':'.$this->port;
		$sock = false;
		if (isset($this->sockets[$sock_key]) && is_resource($this->sockets[$sock_key])) {
			$socks[$ctr] = &$this->sockets[$sock_key];
			$peerhost[$ctr] = $nameserver;
			$peerport[$ctr] = $this->port;
			$ctr++;
		} else {
			if ($this->debug) {
				echo ";; NOTICE: send_udp: open connection to $sock_key\n";
			}
			if(!($sock = stream_socket_client($sock_key, $errno, $errstr, $timeout))) {
				$this->errorstring = 'connection failed';
				if ($this->debug) {
					echo ";; ERROR: send_udp: connection failed: $errstr\n";
				}
			}else{
				socket_set_blocking($sock, false);
				$this->sockets[$sock_key] = $sock;
				$socks[$ctr] = &$this->sockets[$sock_key];
				$peerhost[$ctr] = $nameserver;
				$peerport[$ctr] = $this->port;
				$ctr++;
			}
		}
	}
	if ($ctr == 0) {
		$this->errorstring = 'no nameservers connect';
		return null;
	}

	for($i = 0; $i < $this->retry; $i++, $retrans += $this->retrans, $timeout = (int) ($retrans / $ctr)) {
		if ($timeout < 1) $timeout = 1;
            foreach ($socks as $k => $s) {
                if ($this->debug) {
                    echo ';; send_udp(' . $peerhost[$k] . ':' . $peerport[$k] . '): sending ' . strlen($packet_data) . " bytes\n";
                }

                if (! fwrite($s, $packet_data)) {
                    if ($this->debug) {
                        echo ";; send error\n";
                    }
                }

                /*
                 *  Here's where it get's really nasty.  We don't have a select()
                 *  function here, so we have to poll for a response... UGH!
                 */

		//usleep(1000);
                //$timetoTO  = microtime(true) + $timeout;

                $buf = '';
                //while (!feof($s) && ! strlen($buf) && $timetoTO > (                            microtime(true))) {
		while (!feof($s) && !strlen($buf)) {
			$r = array($s); $w=null; $e=null;
			$ans = stream_select($r, $w, $e, $timeout);
			if($ans === false) {
				$this->errorstring='stream_select() failed to set timeout';
				break;
			}elseif($ans == 0) {
				$this->errorstring='Socket Error: timeout';
				break;
			}
                    if ($buf = stream_get_contents($s)) {
                        $this->answerfrom = $peerhost[$k];
                        $this->answersize = strlen($buf);
                        if ($this->debug) {
                            echo ';; answer from ' . $peerhost[$k] . ':' .
                                $peerport[$k] .  ': ' . strlen($buf) . " bytes\n";
                        }
                        $ans = new Net_DNS_Packet($this->debug);
                        if ($ans->parse($buf)) {
                            if ($ans->header->qr != '1') break;
                            if ($ans->header->id != $packet->header->id) break;
                            $this->errorstring = $ans->header->rcode;
                            $ans->answerfrom = $this->answerfrom;
                            $ans->answersize = $this->answersize;
                            return $ans;
                        }
                    }
                }

            }
	}
	//$this->errorstring = 'Socket Error: timeout';
	return null;
}

    /**
     * Unknown
     */
    function make_query_packet($packetORname, $type = '', $class = '')
    {
        if (is_object($packetORname) && strcasecmp(get_class($packetORname), 'net_dns_packet') == 0) {
            $packet = $packetORname;
        } else {
            $name = $packetORname;
            if ($type == '') {
                $type = 'A';
            }
            if ($class == '') {
                $class = 'IN';
            }

            /*
             * If the name looks like an IP address then do an appropriate
             * PTR query.
             */
            if (preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $name, $regs)) {
                $name = $regs[4].'.'.$regs[3].'.'.$regs[2].'.'.$regs[1].'.in-addr.arpa.';
                $type = 'PTR';
            }

            if ($this->debug) {
                echo ";; query($name, $type, $class)\n";
            }
            $packet = new Net_DNS_Packet($this->debug);
            $packet->buildQuestion($name, $type, $class);
        }

        $packet->header->rd = $this->recurse;
        return $packet;
    }

    /* }}} */
    /* Net_DNS_Resolver::axfr_old($dname, $class = 'IN') {{{ */
    /**
     * Performs an AXFR query (zone transfer) (OLD BUGGY STYLE)
     *
     * This is deprecated and should not be used!
     *
     * @param string $dname The domain (zone) to transfer
     * @param string $class The class in which to look for the zone.
     * @return object Net_DNS_Packet
     * @access public
     */
    function axfr_old($dname, $class = 'IN')
    {
        return $this->axfr($dname, $class, true);
    }
    /* }}} */
    /* Net_DNS_Resolver::axfr($dname, $class = 'IN', $old = false) {{{ */
    /**
     * Performs an AXFR query (zone transfer)
     *
     * Requests a zone transfer from the nameservers. Note that zone
     * transfers will ALWAYS use TCP regardless of the setting of the
     * Net_DNS_Resolver::$usevc flag.  If $old is set to true, Net_DNS requires
     * a nameserver that supports the many-answers style transfer format.  Large
     * zone transfers will not function properly.  Setting $old to true is _NOT_
     * recommended and should only be used for backwards compatibility.
     *
     * @param string $dname The domain (zone) to transfer
     * @param string $class The class in which to look for the zone.
     * @param boolean $old Requires 'old' style many-answer format to function.
                           Used for backwards compatibility only.
     * @return object Net_DNS_Packet
     * @access public
     */
    function axfr($dname, $class = 'IN', $old = false)
    {
        if ($old) {
            if ($this->debug) {
                echo ";; axfr_start($dname, $class)\n";
            }
            if (! count($this->nameservers)) {
                $this->errorstring = 'no nameservers';
                if ($this->debug) {
                    echo ";; ERROR: no nameservers\n";
                }
                return null;
            }
            $packet = $this->make_query_packet($dname, 'AXFR', $class);
            $packet_data = $packet->data();
            $ans = $this->send_tcp($packet, $packet_data);
            return $ans;
        } else {
            if ($this->axfr_start($dname, $class) === null) {
                return null;
            }
            $ret = array();
            while (($ans = $this->axfr_next()) !== null) {
                if ($ans === null) {
                    return null;
                }
                array_push($ret, $ans);
            }
            return $ret;
        }
    }

    /* }}} */
    /* Net_DNS_Resolver::axfr_start($dname, $class = 'IN') {{{ */
    /**
     * Sends a packet via TCP to the list of name servers.
     *
     * @param string $packet    A packet object to send to the NS list
     * @param string $packet_data   The data in the packet as returned by
     *                              the Net_DNS_Packet::data() method
     * @return object Net_DNS_Packet Returns an answer packet object
     * @see Net_DNS_Resolver::send_tcp()
     */
    function axfr_start($dname, $class = 'IN')
    {
        if ($this->debug) {
            echo ";; axfr_start($dname, $class)\n";
        }

        if (! count($this->nameservers)) {
            $this->errorstring = "no nameservers";
            if ($this->debug) {
                echo ";; ERROR: axfr_start: no nameservers\n";
            }
            return null;
        }
        $packet = $this->make_query_packet($dname, "AXFR", $class);
        $packet_data = $packet->data();

        $timeout = $this->tcp_timeout;

        foreach ($this->nameservers as $ns) {
            $dstport = $this->port;
            if ($this->debug) {
                echo ";; axfr_start($ns:$dstport)\n";
            }
            $sock_key = "tcp://$ns:$dstport";
            if (is_resource($this->sockets[$sock_key])) {
                $sock = &$this->sockets[$sock_key];
            } else {
                if (! ($sock = stream_socket_client($sock_key, $errno, $errstr, $timeout))) {
                    $this->errorstring = "connection failed";
                    if ($this->debug) {
                        echo ";; ERROR: axfr_start: connection failed: $errstr\n";
                    }
                    continue;
                }
                $this->sockets[$sock_key] = $sock;
                unset($sock);
                $sock = &$this->sockets[$sock_key];
            }
            $lenmsg = pack("n", strlen($packet_data));
            if ($this->debug) {
                echo ";; sending " . strlen($packet_data) . " bytes\n";
            }

            if (($sent = fwrite($sock, $lenmsg)) == -1) {
                $this->errorstring = "length send failed";
                if ($this->debug) {
                    echo ";; ERROR: axfr_start: length send failed\n";
                }
                continue;
            }

            if (($sent = fwrite($sock, $packet_data)) == -1) {
                $this->errorstring = "packet send failed";
                if ($this->debug) {
                    echo ";; ERROR: axfr_start: packet data send failed\n";
                }
            }

            socket_set_timeout($sock, $timeout);

            $this->_axfr_sock = $sock;
            $this->_axfr_rr = array();
            $this->_axfr_soa_count = 0;
            return $sock;
        }
    }

    /* }}} */
    /* Net_DNS_Resolver::axfr_next() {{{ */
    /**
     * Requests the next RR from a existing transfer started with axfr_start
     *
     * @return object Net_DNS_RR Returns a Net_DNS_RR object of the next RR
     *                           from a zone transfer.
     * @see Net_DNS_Resolver::send_tcp()
     */
    function axfr_next()
    {
        if (! count($this->_axfr_rr)) {
            if (! isset($this->_axfr_sock) || ! is_resource($this->_axfr_sock)) {
                $this->errorstring = 'no zone transfer in progress';
                return null;
            }
            $timeout = $this->tcp_timeout;
            $buf = $this->read_tcp($this->_axfr_sock, 2, $this->debug);
            if (! strlen($buf)) {
                $this->errorstring = 'truncated zone transfer';
                return null;
            }
            $len = unpack('n1len', $buf);
            $len = $len['len'];
            if (! $len) {
                $this->errorstring = 'truncated zone transfer';
                return null;
            }
            $buf = $this->read_tcp($this->_axfr_sock, $len, $this->debug);
            if ($this->debug) {
                echo ';; received ' . strlen($buf) . "bytes\n";
            }
            if (strlen($buf) != $len) {
                $this->errorstring = 'expected ' . $len . ' bytes, received ' . strlen($buf);
                if ($this->debug) {
                    echo ';; ' . $err . "\n";
                }
                return null;
            }
            $ans = new Net_DNS_Packet($this->debug);
            if (! $ans->parse($buf)) {
                if (! $this->errorstring) {
                    $this->errorstring = 'unknown error during packet parsing';
                }
                return null;
            }
            if ($ans->header->ancount < 1) {
                $this->errorstring = 'truncated zone transfer';
                return null;
            }
            if ($ans->header->rcode != 'NOERROR') {
                $this->errorstring = 'errorcode ' . $ans->header->rcode . ' returned';
                return null;
            }
            foreach ($ans->answer as $rr) {
                if ($rr->type == 'SOA') {
                    if (++$this->_axfr_soa_count < 2) {
                        array_push($this->_axfr_rr, $rr);
                    }
                } else {
                    array_push($this->_axfr_rr, $rr);
                }
            }
            if ($this->_axfr_soa_count >= 2) {
                unset($this->_axfr_sock);
            }
        }
        $rr = array_shift($this->_axfr_rr);
        return $rr;
    }

    /**
     * Unknown - not ported yet
     */
    function read_tcp($sock, $nbytes, $debug = 0)
    {
        $buf = '';
        while (strlen($buf) < $nbytes) {
            $nread = $nbytes - strlen($buf);
            $read_buf = '';
            if ($debug) {
                echo ";; read_tcp: expecting $nread bytes\n";
            }
            $read_buf = fread($sock, $nread);
            if (! strlen($read_buf)) {
                if ($debug) {
                    echo ";; ERROR: read_tcp: fread failed\n";
                }
                break;
            }
            if ($debug) {
                echo ';; read_tcp: received ' . strlen($read_buf) . " bytes\n";
            }
            if (!strlen($read_buf)) {
                break;
            }

            $buf .= $read_buf;
        }
        return $buf;
    }
}
