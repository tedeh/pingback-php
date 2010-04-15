<?php

class PingbackServer
{
  public static $FAULT_GENERIC = array('faultCode' => 0, 'faultString' => 'Unknown error.');
  public static $FAULT_SOURCE = array('faultCode' => 0x0010, 'faultString' => 'The source URI does not exist.');
  public static $FAULT_SOURCE_LINK = array('faultCode' => 0x0011, 'faultString' => 'The source URI does not contain a link to the target URI, and so cannot be used as a source.');
  public static $FAULT_TARGET = array('faultCode' => 0x0020, 'faultString' => 'The specified target URI does not exist.');
  public static $FAULT_TARGET_INVALID = array('faultCode' => 0x0021, 'faultString' => 'The specified target URI cannot be used as a target.');
  public static $FAULT_ALREADY_REGISTERED = array('faultCode' => 0x0030, 'faultString' => 'The pingback has already been registered.');
  public static $FAULT_ACCESS_DENIED = array('faultCode' => 0x0031, 'faultString' => 'Access denied.');
  public static $SUCCESS = array('Success');
  
  private $server;
  
  private $request;
  private $response;
  
  private $sourceURL;
  private $targetURL;
  
  private $sourceBody;
  private $targetBody;
  
  private $hasFault = false;
  private $fault;
  
  public function __construct($request)
  {
    $this->server = xmlrpc_server_create();
    $this->setRequest($request);
    xmlrpc_server_register_method($this->server, 'pingback.ping', array($this, 'ping'));
  }
  
  private function ping($method, $parameters)
  {
    list($this->sourceURL, $this->targetURL) = $parameters;
    
    if(!PingbackUtility::isURL($this->sourceURL)) return $this->setFault(self::$FAULT_SOURCE);
    if(!PingbackUtility::isURL($this->targetURL)) return $this->setFault(self::$FAULT_TARGET);
    if(!PingbackUtility::isPingbackEnabled($this->targetURL)) return $this->setFault(self::$FAULT_TARGET_INVALID);
    if(!PingbackUtility::isBacklinking($this->sourceURL, $this->targetURL)) return $this->setFault(self::$FAULT_SOURCE_LINK);
    
    // if no error occured, all went well.
    return $this->setSuccess();
  }
  
  public function execute()
  {
    $this->response = xmlrpc_server_call_method($this->server, $this->request, null, array('encoding' => 'utf-8'));
    return $this->isValid();
  }
  
  public function setResponse($response)
  {
    $this->response = $response;
  }
  
  public function setRequest($request)
  {
    $this->request = $request;
  }
  
  public function getRequest()
  {
    return $this->request;
  }
  
  public function getResponse()
  {
    return $this->response;
  }
  
  public function getSourceURL()
  {
    return $this->sourceURL;
  }
  
  public function getTargetURL()
  {
    return $this->targetURL;
  }
  
  public function getFaultCode()
  {
    return $this->hasFault ? $this->fault['faultCode'] : null;
  }
  
  public function getFaultString()
  {
    return $this->hasFault ? $this->fault['faultString'] : null;
  }
  
  public function hasFault($fault)
  {
    return $fault === $this->fault;
  }
  
  public function setFault($fault)
  {
    $this->hasFault = true;
    $this->fault = $fault;
    $this->response = xmlrpc_encode($fault);
    return $fault;
  }
  
  public function setSuccess($success = array())
  {
    $this->hasFault = false;
    $this->fault = null;
		$success = !empty($success) ? $success : self::$SUCCESS;
    $this->response = xmlrpc_encode($success);
    return $success;
  }
  
  public function isValid()
  {
    return !$this->hasFault;
  }
}










