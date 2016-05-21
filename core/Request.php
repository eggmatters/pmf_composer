<?php

namespace core;

/**
 * Container class storing data from incoming request.
 * We parse the request_uri into an array for use by 
 * controllers and models. 
 * We also go ahead and sanitize GET and POST values and store them here.
 * 
 * Instances of this class are the closest we will get to globally defined objects.
 *
 * @author meggers
 */
class Request {
  /**
   * SERVER['REQUEST_URI'] value
   * @var string 
   */
  private $requestUri;
  /**
   * parsed uri
   * @var string 
   */
  private $requestPath;
  /**
   * An array of individual elements from the requestPath
   * @var array 
   */
  private $resourceArray;
  /**
   * An array of metadata about a resource.
   * @var array
   */
  private $resourceData;
  
  /**
   * SERVER['HTTP_METHOD']
   * @var string 
   */
  private $httpMethod;
  /**
   * http/https
   * @var string 
   */
  private $protocol;
  /**
   * filtered, sanitized version of GET parameters
   * @var array 
   */
  private $getParams;
  /**
   * filtered, sanitized version of POST parameters
   * @var array 
   */
  private $postParams;
  /**
   * set if path contains an id (by controller)
   * @var int 
   */
  private $requestObjects;

  
  /**
   * parses the path from the incoming request uri. If no argument, filters it 
   * from the SERVER superglobal.
   * All other superglobals are filtered and sanitized where applicable.
   * @param string $requestUri
   */
  public function __construct($requestUri = null) {
    if (is_null($requestUri)){
      $this->requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } else {
      $this->requestUri = $requestUri;
    }
    $this->requestPath = preg_replace("/^\/(.*)((\?.*)|\/)?$/U", "$1",$this->requestUri);
    $this->resourceArray = (empty($this->requestPath)) ? [] : explode('/', $this->requestPath);
    $this->httpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    $this->protocol = (stripos(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL'),'https') === true ) ? 'https' : 'http';
    $this->getParams = [];
    if (!empty($_GET)) {
      $this->getParams = $this->filterRequestArray('GET', $_GET);
    }
    $this->postParams = [];
    if (!empty($_POST)) {
      $this->postParams = $this->filterRequestArray('POST', $_POST);
    }  
    $this->requestObjects = RequestObject::setFromResources($this->resourceArray);
  }
  
  public function getRequestUri() {
    return $this->requestUri;
  }
  
  public function getRequestPath() {
    return $this->requestPath;
  }
  
  public function getResourceArray() {
    return $this->resourceArray;
  }
  
  public function getResourceData() {
    return $this->resourceData;
  }
  
  public function getHttpMethod() {
    return $this->httpMethod;
  }
  
  public function getProtocol() {
    return $this->protocol;
  }
  
  public function getGetParams() {
    return $this->getParams;
  }
  
  public function getPostParams() {
    return $this->postParams;
  }
  
  private function filterRequestArray($filter, $requestArray) {
    $filterConstant = $this->getFilterConstant($filter);
    $returnArray = [];
    foreach($requestArray as $key => $value) {
      if (is_array($value)) {
        $returnArray[$key] = $this->parseInputArray($value, $filterConstant);
      } else {
        $returnArray[$key] = filter_input($filterConstant, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      }
    }
    return $returnArray;
  }
  
  private function parseInputArray($inputArray, $filterConstant, $returnArray = null) {
    if (is_null($returnArray)) {
      $returnArray = [];
    }
    foreach($inputArray as $key => $value) {
      if (is_array($value)) {
        $returnArray[$key] = $this->parseInputArray($value, $filterConstant, $returnArray);
      } else {
        $returnArray[$key] = filter_input($filterConstant, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      }
    }
    return $returnArray;
  }
  private function getFilterConstant($filter) {
    switch($filter) {
      case "GET": 
        return INPUT_GET;
      case "POST":
        return INPUT_POST;
      case "COOKIE":
        return INPUT_COOKIE;
      case "SESSION":
        return INPUT_SESSION;
    }
    return -1;
  }
}
