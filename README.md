Pingback implementation for PHP
===================================

Created and copyrighted by Tedde Lundgren. Licensed under 
[Attribution-ShareAlike 3.0 Unported][license].

[license]: http://creativecommons.org/licenses/by-sa/3.0/

Introduction
-----------------------------------

Pingbacks, quoting Wikipedia, are "one of three types of linkbacks, 
methods for Web authors to request notification when somebody links to one of their documents". 
More precisely, an explicitly defined procedure based on XML-RPC
that enable web applications (typically blogs) to communicate about links.

This library was built to comply with [the official pingback specification][pingback-specification] and
supports both the sending and the reception of pingback requests.

[pingback-specification]: http://hixie.ch/specs/pingback/pingback-1.0

Requirements
-----------------------------------

Requirements center around libraries commonly available in PHP5.

- PHP >= 5
- cURL (http://se.php.net/manual/en/book.curl.php)
- XML-RPC (http://se.php.net/manual/en/book.xmlrpc.php)
- Document Object Model (http://se.php.net/manual/en/book.dom.php)

Usage and notes on implementation
===================================

Overview
-----------------------------------

The library consists of three classes.

* __Pingback_Server__ manages the server and composite validation of requests
* __Pingback_Utility__ provides a set of static methods to aid the server and enable the client
* __Pingback_Exception__ is the class that exceptions thrown by the library will be of

Pingback_Utility
----------------------------------

Pingback_Utility contains the following static methods.

### Pingback_Utility::isURL()

* `@param    $url`      The URL
* `@return   boolean`   Wheter the URL is valid

Returns a boolean value determining wheter the first argument is a valid URL or not.

### Pingback_Utility::isPingbackEnabled()

* `@param    $url`      The URL
* `@return   boolean`   Wheter the URL is pingback enabled

Returns a boolean value determining wheter the first argument supports the reception
of pingbacks.

### Pingback_Utility::getRawPostData()

* `@return  string`  The raw POST data

Reads the raw, unfiltered POST data and returns it

### Pingback_Utility::getPingbackURL() (bool)

* `@param    @url`    The URL  
* `@return   string`  The server address if found

Extracts the pingback URL from the first argument.

### PingbackUtility::isBacklinking()

* `@param  $from`     The URL of the page
* `@param  $to`       The URL that should exist in atleast one link
* `@return boolean`   Wheter there is a link or not

Determines wheter the first argument is linking to the second.

### PingbackUtility::sendPingback

* `@param  $from`    The originator of the pingback
* `@param  $to`      The target of the pingback
* `@param  $server`  The URL of the server
* `@return string`   The raw response from the server

The client implementation. Takes three arguments. The first one is the source URL
(originator) of the pingback request, the second one is the target url, 
and the third one is the url to the pingback server. Returns the raw server 
response, which is easily parsed with `xmlrpc_decode_request()`.

PingbackServer
-----------------------------------

PingbackServer is an instantiable class representing a pingback server resource.

PingbackServer::__construct($request = null) (PingbackServer)
The constructor method takes one optional argument: a raw 
pingback xml-rpc request. If not set, the constructor will attempt to
infer it.

PingbackServer::execute() (bool)
Executes the server cycle with the bound pingback request. 
Returns a boolean value on wheter or not the request is valid (succeded).
After running this method (and passing it), you should typically 
determine wheter or not the pingback has been registered, or if any 
other considerations that this server object is unable to evaluate. 
See the section on responses.

PingbackServer::setResponse($response)
Sets the server response. Takes one array argument that will be encoded into an XML-RPC response. 
See section on responses.

PingbackServer::setRequest($request)
Sets the server request. Takes one array argument that will be encoded into an XML-RPC request.

PingbackServer::getRequest() (array)
Returns the request, as an array.

PingbackServer::getResponse() (array)
Returns the response, as an array.

PingbackServer::getSourceURL() (string)
Returns the source url.

PingbackServer::getTargetURL() (string)
Returns the target url.

PingbackServer::getFaultCode() (integer)
Returns the XML-RPC fault code as an integer. If one exists, else null.

PingbackServer::getFaultString() (string)
Returns a string description of the fault, if one exists, else null.

PingbackServer::hasFault($fault) (bool)
Determines wheter or not the request has the specified fault. Takes one array argument. See section on responses.

PingbackServer::setFault($fault)
Sets the fault to the single array argument. See section on responses.
Returns the passed fault array.

PingbackServer::setSuccess($success = array())
Sets the request to be successful. Takes one optional array 
argument, that can include things you want to pass along to the 
client. See the section on responses. Returns the passed array,
or the default success response.

PingbackServer::isValid() (bool)
Determines wheter or not the request is valid (does not have a fault). 
Boolean return value.

Responses
-----------------------------------

PingbackServer has a set of public static responses that are used together 
with the methods setResponse(), hasFault(), setFault() and setSuccess().
These static constants correspond to different fault codes defined in the 
pingback specification. These are:

- PingbackServer::$FAULT_GENERIC (Unknown error)
- PingbackServer::$FAULT_SOURCE (Source link invalid)
- PingbackServer::$FAULT_SOURCE_LINK (Source is not backlinking to target)
- PingbackServer::$FAULT_TARGET (Target link invalid)
- PingbackServer::$FAULT_TARGET_INVALID (Target is not pingback enabled)
- PingbackServer::$FAULT_ALREADY_REGISTERED (Pingback already registered)
- PingbackServer::$FAULT_ACCESS_DENIED (Access denied)
- PingbackServer::$SUCCESS (Simple array used to indicate success)

The ones you usually need to worry about are $FAULT_ALREADY_REGISTERED and $FAULT_ACCESS_DENIED
which you have to evaluate yourself, after execution. Just pass these along to the setFault() method.










