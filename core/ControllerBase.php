<?php
/**
 * Controller base is the abstract base class from which application controllers
 * are derived. 
 *
 * @author meggers
 */
namespace core;

abstract class ControllerBase {
  /**
   *
   * @var ControllerBase|null
   */
  protected $parent;
  /**
   * @var Request $request
   */
  protected $request;
  
  /**
   *
   * @var array 
   */
  protected $resources;
  
  /**
   *
   * @var RequestObject
   */
  protected $requestObject;
  
  /**
   * Constructor accepts the Request object and an optional array of resources.
   * The resources are values obtained from the URL by the Request object.
   * Nested controller instances (i.e. /controllerA/id/Controller/b) will receive
   * a trunctated version of the Request->resources array. @see init() for details.
   * @param \core\Request $request
   * @param array $resources
   */
  public function __construct(RequestObject $requestObject, $resources, $parent = null) {
    $this->request = CoreApp::getRequest();
    $this->requestObject = $requestObject;
    $this->resources = $resources;
    $this->parent = $parent;
    $this->requestObject->setModelNamespace($resources[0]);
  }
  /**
   * init() parses the resources array, determining which controller
   * is to be called, compiles controller arguments and methods.
   * 
   */
  public function init() {
    $resourcesIterator = new SimpleIterator($this->resources);
    $this->requestObject = new RequestObject();
    while ($resourcesIterator->hasNext()) {
      $resourceValue = $resourcesIterator->next();
      // Get resource "type":
      // is part of namespace?
      //  -- determine if current namespace is part of directory structure. 
      //     -- add and continue.
      //  -- determine if namespace is a controller.
      //     -- collect contstraint arguments
      //     -- call init().
      // is method?
      //  -- deterimine which method.
      //  -- place method on controller call stack.
      //  -- continue.
      // is param?
      //  -- associate with method.
      //  -- place on controller call stack.
    }
    
    
    
    $renderFlag = true;
    $this->requestObject = new RequestObject();
    while ($resourcesIterator->hasNext()) {
      $resourceValue = $resourcesIterator->next();
      $controllerSet = $this->requestObject->setControllerNamespace($resourceValue);
      $dirSet = $this->requestObject->isResourceDirectory($resourceValue);
      if ($controllerSet) {
        $reflectionClass = new \ReflectionClass($this->requestObject->getControllerNamespace());
        $resourcesArray = $resourcesIterator->truncateFromIndex($resourcesIterator->getIndex());
        $controllerClass = $reflectionClass->newInstance($this->requestObject, $resourcesArray, $this);
        $controllerClass->init();
        $renderFlag = false;
      }
      if ($dirSet) {
        $resourcesIterator->next();
      } else {
        $this->requestObject->setRequestArgument($resourceValue);
      }
    }
    if ($renderFlag) {
      $this->callMethod();
    }
  }
  
  public function getRequestObject() {
    return $this->requestObject;
  }
  
  public function getParent() {
    return $this->parent;
  }
  
  public function getParams(ControllerBase $instance, $params = []) {
    $rf = new \ReflectionClass($instance);
    $params[$rf->name] = $instance->requestObject->getRequestArguments();
    $parent = $instance->getParent();
    if (is_null($instance->getParent())) {
      return $params;
    }
    return $parent->getParams($parent, $params);
  }
  /**
   * Default method, will load model from id set in init
   * and render the "get" view for this controller.
   * Also responsible for rendering forms for "update" and "new" requests.
   */
  protected function get() {
    CoreApp::issue("404");
  }

  protected function index() {
    CoreApp::issue("404");
  }
  /**
   * Default method. Called from PUT requests.
   */
  protected function update() {
    CoreApp::issue("404");
  }
  /**
   * Default method. Called from CREATE requests
   */
  protected function create() {
    CoreApp::issue("404");
  }
  /**
   * Default method. Called from DELETE requests
   */
  protected function delete() {
    CoreApp::issue("404");
  }
  
  protected function render() {
    
  }

  
  private function callMethod() {
    switch ($this->request->getHttpMethod()) {
      case "GET":
        $this->prepareGet();
        break;
      case "PUT":
        $this->prepareUpdate();
        break;
      case "POST":
        $this->create();
        break;
      case "DELETE":
        $this->prepareDelete();
        break;
    }
  }
  
  private function prepareIndex() {
    //make sure we can filter out non-model related request strings.
    $this->index();
  }
  
  private function prepareGet() {
    $params = $this->getParams($this);
    if (empty($params)) {
      $this->prepareIndex();
    } else {
      $this->get();
    }
  }
  
  private function prepareCreate() {
    
  }
  private function prepareDelete() {
    
  }
  
  private function prepareUpdate() {

  }
}