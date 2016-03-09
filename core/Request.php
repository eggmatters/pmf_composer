<?php

namespace core;

/**
 * Description of Request
 *
 * @author meggers
 */
class Request {
  private $requestUri;
  private $requestPath;
  private $resourceArray;
  private $httpMethod;
  private $protocol;
  private $getParams;
  private $postParams;
  
  
  public function __construct($requestUri = null) {
    if (is_null($requestUri)){
      $this->requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } else {
      $this->requestUri = $requestUri;
    }
    $this->requestPath = preg_replace("/^\/(.*)((\?.*)|\/)?$/U", "$1",$requestUri);
    $this->resourceArray = explode('/', $this->requestPath);
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
    $this->id = null;
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
  
  public function setRequestedId($id) {
    $this->id = $id;
  }
  
  public function getRequestedId() {
    return $this->id;
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
