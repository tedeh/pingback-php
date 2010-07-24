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

* `@param string $url`      The URL
* `@return boolean`         Wheter the URL is valid

Returns a boolean value determining wheter the first argument is a valid URL or not.

### Pingback_Utility::isPingbackEnabled()

* `@param string $url`      The URL
* `@return boolean`         Wheter the URL is pingback enabled

Returns a boolean value determining wheter the first argument supports the reception
of pingbacks.

### Pingback_Utility::getRawPostData()

* `@return string`  The raw POST data

Reads the raw, unfiltered POST data and returns it

### Pingback_Utility::getPingbackURL()

* `@param string @url`    The URL  
* `@return string`        The server address if found

Extracts the pingback URL from the first argument.

### PingbackUtility::isBacklinking()

* `@param string $from`     The URL of the page
* `@param string $to`       The URL that should exist in atleast one link
* `@return boolean`         Wheter there is a link or not

Determines wheter the first argument is linking to the second.

### PingbackUtility::sendPingback()

* `@param string $from`    The originator of the pingback
* `@param string $to`      The target of the pingback
* `@param string $server`  The URL of the server
* `@return string`         The raw response from the server

The client implementation. Takes three arguments. The first one is the source URL
(originator) of the pingback request, the second one is the target url, 
and the third one is the url to the pingback server. Returns the raw server 
response, which is easily parsed with `xmlrpc_decode_request()`.

Pingback_Server
-----------------------------------

PingbackServer is an instantiable class representing a pingback server resource. The majority
of requirements put upon a valid pingback request are automatically evaluated. However, two
specific requirements have to be evaluated by additional, user-provided logic. These corresponding
faults of these two requirements are:

* __Pingback_Server::RESPONSE_FAULT_ALREADY_REGISTERED__ the pingback has already been registered
* __Pingback_Server::RESPONSE_FAULT_ACCESS_DENIED__ access to the server resource has been denied

After execution of the bound request, the implementor may leverage `Pingback_Server->setFault()` to
ensure a proper response if all requirements of the request have not been met.

### Pingback_Server->__construct()

* `@param array $options` Array of options

Takes an array of options that will be automatically set. Creates the server instance and
enabled the pingback method on it.

### Pingback_Server->getOption()

* `@param string $option`   Option to return
* `@return mixed`           The value or null if not exist

Returns an option.

### Pingback_Server->setOption()

* `@param string $option`   Name of option
* `@param mixed $value`     Value to set

Takes an array of options.

### Pingback_Server->setOptions()

* `@param array $options` Array of options

Takes an array of options.

### Pingback_Server->execute()

* `@param string $request`  Evaluate this request

Evaluates the given request. If no argument is given, evaluates
the set request.

### Pingback_Server->setResponse()

* `@param string $response` The response

Sets the response as a string.

### Pingback_Server->setRequest()

* `@param string $request` The request

Sets the request as a string.

### Pingback_Server->getRequest()

* `@return string` The request

Returns the request.

### Pingback_Server->getResponse()

* `@return string` The response

Returns the response.

### Pingback_Server->getSourceURL()

* `@return string` The source URL

Returns the source URL from the request.

### Pingback_Server->getTargetURL()

* `@return string` The target URL

Returns the target URL from the request.

### Pingback_Server->getTargetURL()

* `@return boolean` The target URL

Returns the target URL from the request.

### Pingback_Server->getFaultAsArray()

* `@param integer $faultCode`   The fault code
* `@return array`               The fault (code and string) formatted as an array

Returns the passed fault code as an array fit for the response.

### Pingback_Server->setFault()

* `@param integer $faultCode` The fault code to set

Sets the fault code of the request.

### Pingback_Server->getSuccessAsArray()

* `@return array` The success array

Returns the success response as an array fit for the response.

### Pingback_Server->setSuccess()

Marks the current request as successful.

### Pingback_Server->isValid()

* `@return boolean` The request validity

Returns wheter or not the current request is considered valid.  

#### Available options

The methods for setting options supports the following option names and values:

* `string 'encoding'` encoding used by the server

Responses
-----------------------------------

Pingback_Server has a set of class constants that may be used with the
appropriate methods on a server instance. The actual messages returned by the server
can be manipulated by changing the strings in the `Pingback_Server->responses` array (where
the array keys correspond to the class constants).

- __Pingback_Server::RESPONSE_FAULT_GENERIC__ Unknown error
- __Pingback_Server::RESPONSE_FAULT_SOURCE__ Source link invalid
- __Pingback_Server::RESPONSE_FAULT_SOURCE_LINK__ Source is not backlinking to target
- __Pingback_Server::RESPONSE_FAULT_TARGET__ Target link invalid
- __Pingback_Server::RESPONSE_FAULT_TARGET_INVALID__ Target is not pingback enabled
- __Pingback_Server::RESPONSE_FAULT_ALREADY_REGISTERED__ Pingback already registered
- __Pingback_Server::RESPONSE_FAULT_ACCESS_DENIED__ Access denied
- __Pingback_Server::RESPONSE_SUCCESS__ Indicates success










