===================================
Pingbacks for PHP
===================================

Created by Tedde Lundgren. This README is based on a partially
modified blog post that appread on http://tedeh.net.

-----------------------------------
Introduction
-----------------------------------

Pingbacks, quoting Wikipedia, are "one of three types of linkbacks, 
methods for Web authors to request notification when somebody links to one of their documents". 
More precisely, an explicitly defined set of procedures based on XML-RPCxmlrpcwiki
that enable web applications (typically blogs) to communicate.

-----------------------------------
My end
-----------------------------------

Pingbacks have seen many popular implementations lately. 
XML-RPC is extensively defined and available in a wide array of environments. 
In early 2009, I was working on a blog that required a full, on the spec 
implementation of Pingbacks. Naturally, my first instinct wasn't to build one 
custom only for myself, but to seek out a library that fitted my needs. 
I found a few built with PHP (scripting language of choice) but was thourougly 
disappointed with each and every one of them. They were all bloated with crude 
code and ad hoc solutions. Neither leveraged the full capabilities of PHP whatsoever.

Instead of compromising, I actually did decide to build one for myself. 
This post, and the associated library, is what came out of these attempts.

-----------------------------------
Solution
-----------------------------------

Pingback functionality are a two-sided coin: the client and the server. 
The client is simply responsible for sending a pingback to an URL that fronts 
an XML-RPC server. The server then processes this request and checks it 
against a certain set of procedures so that it complies with what's defined 
in the official specification, and the server itself.

Implementing the client was very simple, and resulted in about 9 lines of PHP code 
(basically a cURL-request) wrapped inside a static method. The server required more
consideration. No generalized library like the one I wrote can for example know wheter 
or not a pingback has already been registered or not. But it can easily find out if a 
page is backlinking and if the source and target are valid URLs and so forth.

The solution resulted one regular and one static class: PingbackServer and PingbackUtility. 
PingbackUtility contains a few static methods to aid the user and the server 
(See the usage section). PingbackUtility includes the client. `PingbackServer` 
is an instantiable class with a set of methods and constants for 
implementing a specification compliant pingback server.

-----------------------------------
Requirements
-----------------------------------

Requirements center around libraries usually available in PHP5. In most cases, 
this should not be an issue.

- PHP 5.x
- cURL (http://se.php.net/manual/en/book.curl.php)
- XML-RPC (http://se.php.net/manual/en/book.xmlrpc.php)
- Document Object Model (http://se.php.net/manual/en/book.dom.php)

===================================
Usage
===================================

----------------------------------
PingbackUtility
----------------------------------

PingbackUtility is a class that contains the following static methods:

PingbackUtility::isURL($url) (bool)
Takes one argument, an url, and returns a boolean value determining wheter 
or not the passed URL is valid or not.

PingbackUtility::isPingbackEnabled($url) (bool)
Takes one url as argument and returns a boolean value determining wheter 
or not the passed url is pingback enabled or not.

PingbackUtility::getPingbackURL($url) (bool)
Takes one url as argument and returns a string with the pingback server URL 
(if found) or null.

PingbackUtility::isBacklinking($url) (bool)
Takes two arguments (urls) and decides wheter the first url is linking 
(regular html anchor link) to the second one. Returns boolean true or false.

PingbackUtility::sendPingback($source, $target, $server) (string)
The pingback client. Takes three arguments. The first one is the source URL
(originator) of the pingback request, the second one is the target url, 
and the third one is the url to the pingback server. Returns the raw server 
response, which is easily parsed with xmlrpc_decode_request().

-----------------------------------
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

-----------------------------------
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

-----------------------------------
Further reading
-----------------------------------

Official Pingback specification, which this library was built aroud.
http://hixie.ch/specs/pingback/pingback-1.0

Wikipedia on the definition of Pingback
http://en.wikipedia.org/wiki/Pingback

Wikipedia on XML-RPC
http://en.wikipedia.org/wiki/Xmlrpc









