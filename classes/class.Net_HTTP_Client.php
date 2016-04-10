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

/*

   HTTP_Client class

@DESCRIPTION

   HTTP Client component
	suppots methods HEAD, GET, POST
	1.0 and 1.1 compliant
	WebDAV methods tested against Apache/mod_dav

@SYNOPSIS

 $http = new Net_HTTP_Client( "dir.yahoo.com", 80 );
 $http->setProtocolVersion( "1.1" );
 $http->addHeader( "Host", "google.com" );
 $http->addHeader( "Connection", "keep-alive" );

 if( $http->Get( "/Reference/Libraries/" ) == 200 )
 $page1 = $http->getBody();

 if( $http->Get( "/News_and_Media/" ) == 200 )
 $page2 = $http->getBody();
 $http->disconnect();

*/


/// debug levels , use it as Client::setDebug( DBGSOCK & DBGTRACE )
define( 'DBGTRACE', 1 ); // to debug methods calls
define( 'DBGINDATA', 2 ); // to debug data received
define( 'DBGOUTDATA', 4 ); // to debug data sent
define( 'DBGLOW', 8 ); // to debug low-level (usually internal) methods
define( 'DBGSOCK', 16 ); // to debug socket-level code

define( 'CRLF', "\r\n" );


class Net_HTTP_Client {

private $url; // array containg server URL, similar to array returned by parseurl()
public $errstr, $errno; // server response code eg. "200 OK"
public $protocolVersion = '1.1'; // HTTP protocol version used
private $requestHeaders, $responseHeaders, $requestBody; // internal buffers
public $request; 
private $socket = false; // TCP socket identifier
private $useSSL = false;
private $useProxy = false; // proxy informations
private $proxyHost, $proxyPort;
private $timeout = 300;
private $debug = 0; // debugging flag

/**
* Net_HTTP_Client
* constructor
* Note : when host and port are defined, the connection is immediate
* @seeAlso connect
**/
function __construct( $host= NULL, $port= NULL ) {
		$this->dbg('DBGTRACE', "Net_HTTP_Client( $host, $port )", '');

		if( $host != NULL ) {
			$this->url['scheme'] = 'http';
			$this->url['host'] = $host;
			if($port != NULL ) $this->url['port'] = $port;
		}
	$this->addHeader('Accept-Encoding', 'deflate, gzip, *;q=0');
}

/**
 * turn on debug messages
 * @param level a combinaison of debug flags
 * @see debug flags ( DBG..) defined at top of file
 **/
public function setDebug( $level ) {
		$this->dbg('DBGTRACE', "setDebug( $level )", '');
		$this->debug = $level;
}

public function setBody($post) {
	$this->requestBody = $post;
}

/**
 * Connect
 * open the connection to the server
 * @return boolean false is connection failed, true otherwise
 **/
public function connect() {
	$this->dbg('DBGTRACE', "Connect( {$this->url['scheme']}, {$this->url['host']}, {$this->url['port']} )", '');
	// connect if necessary
	if( !$this->socket || feof( $this->socket) ) {

		if( $this->useProxy ) {
			$host = $this->proxyHost;
			$port = $this->proxyPort;
		} else {
			$host = $this->url['host'];
			$port = $this->url['port'];
		}
		$port = empty($port) ? 80 : (int) $port;
		$this->dbg('DBGSOCK', "open network socket to $host:$port", '');
		if($this->useSSL) {
			$host = 'ssl://'.$host;
		}else{
			$host = 'tcp://'.$host;
		}
		$this->socket = @stream_socket_client($host.':'.$port, $this->errno, $this->errstr, $this->timeout,STREAM_CLIENT_CONNECT);
		if($this->errno == 0 && ! $this->socket) {
			$this->dbg('DBGSOCK', "Could not initialize socket", '');
			return false;
		}elseif( ! $this->socket ) {
			$this->dbg('DBGSOCK', "Failed with error : ($this->errno) $this->errstr", '');
			return false;
		}
	}
	return true;
}

/**
 * Disconnect
 * close the connection to the  server
 **/
public function Disconnect() {
	$this->dbg('DBGTRACE', "DBG.TRACE Disconnect()", '');
	if( $this->socket ) {
		$this->dbg('DBGSOCK', 'DBG.SOCK close network socket', '');
		fclose( $this->socket );
	}
}

/**
 * turn on proxy support
 * @param proxyHost proxy host address eg "proxy.mycorp.com"
 * @param proxyPort proxy port usually 80 or 8080
 **/
function setProxy( $proxyHost, $proxyPort ) {
		$this->dbg('DBGTRACE', "setProxy( $proxyHost, $proxyPort )", '');
		$this->useProxy = true;
		$this->proxyHost = $proxyHost;
		$this->proxyPort = $proxyPort;
}


/**
 * setProtocolVersion
 * define the HTTP protocol version to use
 *	@param version string the version number with one decimal: "0.9", "1.0", "1.1"
 * when using 1.1, you MUST set the mandatory headers "Host"
 * @return boolean false if the version number is bad, true if ok
 **/
function setProtocolVersion( $version ) {
	$this->dbg('DBGTRACE', "setProtocolVersion( $version )", '');
	if( $version > 0 and $version <= 1.1 ) $this->protocolVersion = $version;
	else return false;
	return true;
}

/**
 * set a username and password to access a protected resource
 * Only "Basic" authentication scheme is supported yet
 *	@param username string - identifier
 *	@param password string - clear password
 **/
public function setCredentials( $username, $password ) {
	$hdrvalue = base64_encode( "$username:$password" );
	$this->addHeader( 'Authorization', 'Basic '.$hdrvalue );
}

/**
 * addHeader
 * set a unique request header
 *	@param headerName the header name
 *	@param headerValue the header value, ( unencoded)
 **/
function addHeader( $headerName, $headerValue ) {
	$this->dbg('DBGTRACE', "addHeader( $headerName, $headerValue )", '');
	$this->requestHeaders[$headerName] = $headerValue;
}

/**
 * removeHeader
 * unset a request header
 *	@param headerName the header name
 **/
function removeHeader( $headerName ) {
	$this->dbg('DBGTRACE', "removeHeader( $headerName)", '');
	unset( $this->requestHeaders[$headerName] );
}

/**
 * addCookie
 * set a session cookie, that will be used in the next requests.
 * this is a hack as cookie are usually set by the server, but you may need it
 * it is your responsabilty to unset the cookie if you request another host
 * to keep a session on the server
 *	@param string the name of the cookie
 *	@param string the value for the cookie
 **/
function addCookie( $cookiename, $cookievalue ) {
	$this->dbg('DBGTRACE', "addCookie( $cookiename, $cookievalue )", '');
	$cookie = $cookiename . '=' . $cookievalue;
	$this->requestHeaders['Cookie'] = $cookie;
}

/**
 * removeCookie
 * unset cookies currently in use
 **/
function removeCookie() {
	$this->dbg('DBGTRACE', "removeCookies()", '');
	unset( $this->requestHeaders['Cookie'] );
}

/**
 * HEAD
 * issue a HEAD request
 * @param uri string URI of the document
 * @return string response status code (200 if ok)
 **/
function Head( $uri ) {
	$this->dbg('DBGTRACE', "Head( $uri )", '');
	$uri = $this->makeUri( $uri );
	if( $this->sendRequest( "HEAD $uri HTTP/$this->protocolVersion" ) ) $this->processReply();
	return $this->errno;
}


/**
 * GET
 * issue a GET http request
 * @param uri URI (path on server) or full URL of the document
 * @return string response status code (200 if ok)
 **/
public function Get( $url ) {
	$this->dbg('DBGTRACE', "Get( $url )", '');
	$uri = $this->makeUri( $url );
	if($this->sendRequest("GET $uri HTTP/".$this->protocolVersion)) $this->processReply();
	return $this->errno;
}

/**
 * POST
 * issue a POST http request
 * @param uri string URI of the document
 * @param query_params array parameters to send in the form "parameter name" => value
 * @return string response status code (200 if ok)
 * @example
 *   $params = array( "login" => "tiger", "password" => "secret" );
 *   $http->post( "/login.php", $params );
 **/
function Post( $uri, $query_params='' ) {
	$this->dbg('DBGTRACE', "Post( $uri, $query_params )", '');
	$uri = $this->makeUri( $uri );
	if(is_array($query_params) ) {
		$postArray = array();
		foreach( $query_params as $k=>$v ) $postArray[] = urlencode($k) .'='. urlencode($v);
		$this->requestBody = implode('&', $postArray);
	}
	// set the content type for post parameters
	$this->addHeader('Content-Type', 'application/x-www-form-urlencoded' );
	if($this->sendRequest("POST $uri HTTP/".$this->protocolVersion)) $this->processReply();
	$this->removeHeader('Content-Type');
	$this->removeHeader('Content-Length');
	return $this->errno;
}

	/**
	 * Options
	 * issue a OPTIONS http request
	 * @param uri URI (path on server) or full URL of the document
	 * @return array list of options supported by the server or NULL in case of error
	 **/
	function Options( $url )
	{
		$this->dbg('DBGTRACE', "Options( $url )", '');
		$uri = $this->makeUri( $url );

		if( $this->sendRequest( "OPTIONS $uri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		if( @$this->responseHeaders["Allow"] == NULL ) return NULL;
		else return explode( ",", $this->responseHeaders["Allow"] );
	}

	/**
	 * Put
	 * Send a PUT request
	 * PUT is the method to sending a file on the server. it is *not* widely supported
	 * @param uri the location of the file on the server. dont forget the heading "/"
	 * @param filecontent the content of the file. binary content accepted
	 * @return string response status code 201 (Created) if ok
	 * @see RFC2518 "HTTP Extensions for Distributed Authoring WEBDAV"
	 **/
	function Put( $uri, $filecontent )
	{
		$this->dbg('DBGTRACE', "Put( $uri, (filecontent not displayed )", '');
		$uri = $this->makeUri( $uri );
		$this->requestBody = $filecontent;
		if( $this->sendRequest( "PUT $uri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		return $this->errno;
	}

	/**
	 * Send a MOVE HTTP-DAV request
	 * Move (rename) a file on the server
	 * @param srcUri the current file location on the server. dont forget the heading "/"
	 * @param destUri the destination location on the server. this is *not* a full URL
	 * @param overwrite boolean - true to overwrite an existing destinationn default if yes
	 * @return string response status code 204 (Unchanged) if ok
	 * @see RFC2518 "HTTP Extensions for Distributed Authoring WEBDAV"
	 **/
	function Move( $srcUri, $destUri, $overwrite=true )
	{
		$this->dbg('DBGTRACE', "Move( $srcUri, $destUri, $overwrite )", '');
		if( $overwrite )
			$this->requestHeaders['Overwrite'] = "T";
		else
			$this->requestHeaders['Overwrite'] = "F";

		$destUrl = $this->url['scheme'] . "://" . $this->url['host'];
		if( $this->url['port'] != "" )
			$destUrl .= ":" . $this->url['port'];
		$destUrl .= $destUri;
		$this->requestHeaders['Destination'] =  $destUrl;

		if( $this->sendRequest( "MOVE $srcUri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		return $this->errno;
	}

	/**
	 * Send a COPY HTTP-DAV request
	 * Copy a file -allready on the server- into a new location
	 * @param srcUri the current file location on the server. dont forget the heading "/"
	 * @param destUri the destination location on the server. this is *not* a full URL
	 * @param overwrite boolean - true to overwrite an existing destination - overwrite by default
	 * @return string response status code 204 (Unchanged) if ok
	 * @see RFC2518 "HTTP Extensions for Distributed Authoring WEBDAV"
	 **/
	function Copy( $srcUri, $destUri, $overwrite=true )
	{
		$this->dbg('DBGTRACE', "Copy( $srcUri, $destUri, $overwrite )", '');
		if( $overwrite )
			$this->requestHeaders['Overwrite'] = "T";
		else
			$this->requestHeaders['Overwrite'] = "F";

		$destUrl = $this->url['scheme'] . "://" . $this->url['host'];
		if( $this->url['port'] != "" )
			$destUrl .= ":" . $this->url['port'];
		$destUrl .= $destUri;
		$this->requestHeaders['Destination'] =  $destUrl;

		if( $this->sendRequest( "COPY $srcUri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		return $this->errno;
	}


	/**
	 * Send a MKCOL HTTP-DAV request
	 * Create a collection (directory) on the server
	 * @param uri the directory location on the server. dont forget the heading "/"
	 * @return string response status code 201 (Created) if ok
	 * @see RFC2518 "HTTP Extensions for Distributed Authoring WEBDAV"
	 **/
	function MkCol( $uri )
	{
		$this->dbg('DBGTRACE', "Mkcol( $uri )", '');
		// $this->requestHeaders['Overwrite'] = "F";
		if( $this->sendRequest( "MKCOL $uri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		return $this->errno;
	}

	/**
	 * Delete a file on the server using the "DELETE" HTTP-DAV request
	 * This HTTP method is *not* widely supported
	 * Only partially supports "collection" deletion, as the XML response is not parsed
	 * @param uri the location of the file on the server. dont forget the heading "/"
	 * @return string response status code 204 (Unchanged) if ok
	 * @see RFC2518 "HTTP Extensions for Distributed Authoring WEBDAV"
	 **/
	function Delete( $uri )
	{
		$this->dbg('DBGTRACE', "Delete( $uri )", '');
		if( $this->sendRequest( "DELETE $uri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		return $this->errno;
	}

	/**
	 * PropFind
	 * implements the PROPFIND method
	 * PROPFIND retrieves meta informations about a resource on the server
	 * XML reply is not parsed, you'll need to do it
	 * @param uri the location of the file on the server. dont forget the heading "/"
	 * @param scope set the scope of the request.
	 *         O : infos about the node only
	 *         1 : infos for the node and its direct children ( one level)
	 *         Infinity : infos for the node and all its children nodes (recursive)
	 * @return string response status code - 207 (Multi-Status) if OK
	 * @see RFC2518 "HTTP Extensions for Distributed Authoring WEBDAV"
	 **/
	function PropFind( $uri, $scope=0 )
	{
		$this->dbg('DBGTRACE', "Propfind( $uri, $scope )", '');
		$this->requestHeaders['Depth'] = $scope;
		if( $this->sendRequest( "PROPFIND $uri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		return $this->errno;
	}


	/**
	 * Lock - WARNING: EXPERIMENTAL
	 * Lock a ressource on the server. XML reply is not parsed, you'll need to do it
	 * @param $uri URL (relative) of the resource to lock
	 * @param $lockScope -  use "exclusive" for an eclusive lock, "inclusive" for a shared lock
	 * @param $lockType - acces type of the lock : "write"
	 * @param $lockScope -  use "exclusive" for an eclusive lock, "inclusive" for a shared lock
	 * @param $lockOwner - an url representing the owner for this lock
	 * @return server reply code, 200 if ok
	 **/
	function Lock( $uri, $lockScope, $lockType, $lockOwner )
	{
		$body = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<D:lockinfo xmlns:D='DAV:'>
<D:lockscope><D:$lockScope/></D:lockscope>\n<D:locktype><D:$lockType/></D:locktype>
	<D:owner><D:href>$lockOwner</D:href></D:owner>
</D:lockinfo>\n";

		$this->requestBody = utf8_encode( $body );
		if( $this->sendRequest( "LOCK $uri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		return $this->errno;
	}


	/**
	 * Unlock - WARNING: EXPERIMENTAL
	 * unlock a ressource on the server
	 * @param $uri URL (relative) of the resource to unlock
	 * @param $lockToken  the lock token given at lock time, eg: opaquelocktoken:e71d4fae-5dec-22d6-fea5-00a0c91e6be4
	 * @return server reply code, 204 if ok
	 **/
	function Unlock( $uri, $lockToken )
	{
		$this->addHeader( "Lock-Token", "<$lockToken>" );
		if( $this->sendRequest( "UNLOCK $uri HTTP/$this->protocolVersion" ) )
			$this->processReply();
		return $this->errno;
	}

/**
 * getHeaders
 * return the response headers
 * to be called after a Get() or Head() call
 * @return array headers received from server in the form headername => value
 **/
function getHeaders() {
	$this->dbg('DBGTRACE', "getHeaders()", '');
	return $this->responseHeaders;
}

/**
 * getHeader
 * return the response header "headername"
 * @param headername the name of the header
 * @return header value or NULL if no such header is defined
 **/
function getHeader( $header ) {
	$header = strtolower($header);
	if (! is_string($header) || ! isset($this->responseHeaders[$header])) return null;
	return $this->responseHeaders[$header];
}

	/**
	 * getBody
	 * return the response body
	 * invoke it after a Get() call for instance, to retrieve the response
	 * @return string body content
	 * @seeAlso get, head
	 **/
	function getBody()
	{
		$this->dbg('DBGTRACE', "getBody()", '');
		return $this->responseBody;
	}

	/**
	  * getStatus return the server response's status code
	  * @return string a status code
	  * code are divided in classes (where x is a digit)
	  *  - 20x : request processed OK
	  *  - 30x : document moved
	  *  - 40x : client error ( bad url, document not found, etc...)
	  *  - 50x : server error
	  * @see RFC2616 "Hypertext Transfer Protocol -- HTTP/1.1"
	  **/
	function getStatus()
	{
		return $this->errno;
	}


	/**
	  * getStatusMessage return the full response status, of the form "CODE Message"
	  * eg. "404 Document not found"
	  * @return string the message
	  **/
	function getStatusMessage()
	{
		return $this->errstr;
	}




/*********************************************
* Only protected or private methods below
**/

/**
  * send a request
  * data sent are in order
  * a) the command
  * b) the request headers if they are defined
  * c) the request body if defined
  * @return string the server repsonse status code
  **/
function sendRequest( $command ) {
	$this->dbg('DBGTRACE', "sendRequest( $command )", '');
	$this->responseHeaders = array();
	$this->responseBody = false;

	if(!$this->connect()) return false;

	if(!empty($this->requestBody)) $this->addHeader('Content-Length', strlen($this->requestBody));

	$this->request = $command . CRLF;
	if(is_array($this->requestHeaders)) foreach( $this->requestHeaders as $k => $v ) $this->request .= "$k: $v" . CRLF;
	if(!empty($this->requestBody)) $this->request .= CRLF . $this->requestBody;

	// unset body (in case of successive requests)
	$this->requestBody = '';
	$this->dbg('DBGOUTDATA', 'Sending', $this->request);
	return fwrite( $this->socket, $this->request . CRLF );
	return true;
}

/**
  * read a reply
  * @return error number
  **/
public function processReply() {
	$this->dbg('DBGLOW', 'processReply()', '');
	$this->responseHeaders = array();
	$this->responseBody = false;

	$rawResult = '';
	stream_set_blocking( $this->socket, false );
	$r = array($this->socket); $w=null; $e=null;
	while (!feof($this->socket)) {
		$ans = stream_select($r, $w, $e, $this->timeout);
		if($ans === false) {
			$this->errstr='stream_select() failed to set timeout';
			break;
		}elseif($ans == 0) {
			$this->errstr='Socket Error: timeout';
			break;
		}
		$data = stream_get_contents($this->socket);
		$rawResult .= $data;
		$this->dbg('DBGSOCK', '', $data);
	}
	stream_set_blocking( $this->socket, true );

	$rawResult = trim($rawResult);

	$hi = strpos($rawResult, "\r\n\r\n");
	if($hi === false) {
		$headers_str = $rawResult;
	}else{
		$headers_str = substr($rawResult,0,$hi);
		$this->responseBody = substr($rawResult,$hi+4);
	}
	$this->dbg('DBGINDATA', 'Headers', $headers_str);
	
	$headers_strs = explode("\r\n",$headers_str);
	if(preg_match( '|^HTTP/([\d\.x]+) (\d+) ([^\r\n]+)|', $headers_strs[0], $a )) {
		unset($headers_strs[0]);
		$this->errstr = $a[3];
		$this->errno = (int) $a[2];
	}else{
		$this->errstr = $headers_strs[0];
		$this->errno = 0;
		return $this->errno;
	}
	$this->dbg('DBGINDATA', 'ReplyLine', $this->errstr);

	// Parse headers
	$headers = array();
        foreach($headers_strs as $line) {
            $line = trim($line, "\r\n");
            if ($line == '') break;

            if (preg_match('|^([\w-]+):\s+(.+)|', $line, $m)) {
                unset($last_header);
                $h_name = strtolower($m[1]);
                $h_value = $m[2];

                if (isset($headers[$h_name])) {
                    if (! is_array($headers[$h_name])) $headers[$h_name] = array($headers[$h_name]);
                    $headers[$h_name][] = $h_value;
                } else $headers[$h_name] = $h_value;
                $last_header = $h_name;
            } elseif (preg_match("|^\s+(.+)$|", $line, $m) && $last_header !== null) {
                if (is_array($headers[$last_header])) {
                    end($headers[$last_header]);
                    $last_header_key = key($headers[$last_header]);
                    $headers[$last_header][$last_header_key] .= $m[1];
                } else $headers[$last_header] .= $m[1];
            }
        }
		$this->responseHeaders = $headers;

        // Decode any content-encoding (gzip or deflate) if needed
        switch (strtolower($this->getHeader('Content-Encoding'))) {
            // Handle gzip encoding
            case 'gzip':
                $this->responseBody = self::decodeGzip($this->responseBody);
                break;
            // Handle deflate encoding
            case 'deflate':
                $this->responseBody = self::decodeDeflate($this->responseBody);
                break;
            default:
                break;
        }

//		if( $this->responseHeaders['set-cookie'] )
//			$this->addHeader( "cookie", $this->responseHeaders['set-cookie'] );
	return $this->errno;
}

/**
 * Calculate and return the URI to be sent ( proxy purpose )
 * @param the local URI
 * @return URI to be used in the HTTP request
 * @scope private
 **/
function makeUri( $uri ) {
	$a = parse_url( $uri );

	if( isset($a['scheme']) && isset($a['host']) ) {
		$this->url = $a;
	}else{
		unset( $this->url['query']);
		unset( $this->url['fragment']);
		$this->url = array_merge( $this->url, $a );
	}
	if( $this->useProxy ) {
		$requesturi= 'http://' . $this->url['host'] . ( empty($this->url['port']) ? '' : ':' . $this->url['port'] ) . $this->url['path'] . ( empty($this->url['query']) ? '' : '?' . $this->url['query'] );
	} else {
		$requesturi = $this->url['path'] . (empty( $this->url['query'] ) ? '' : "?" . $this->url['query']);
	}
	return $requesturi;
}

public static function decodeGzip($body) {
        if (! function_exists('gzinflate')) {
		$this->errstr = 'Unable to decode gzipped response body: perhaps the zlib extension is not loaded?'; 
		return false;
        }
        return gzinflate(substr($body, 10));
}

public static function decodeDeflate($body) {
	if (! function_exists('gzuncompress')) {
		$this->errstr = 'Unable to decode deflated response body: perhaps the zlib extension is not loaded?'; 
		return false;
	}
    	return gzuncompress($body);
}

protected function dbg($level, $title, $msg) {
	if ($this->debug & constant($level)) echo '<span style="color: orange; font-weight: bold;>">'.$level.' '.$title.'</span><br /><span style="color: blue; white-space: pre;">'.$msg.'</span><br />';
}

}