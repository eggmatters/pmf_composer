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
   *
   * @var ModelBase
   */
  protected $model;
  
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
   * init() parses the resources array, determining the resource type from url 
   * parameters. 
   * init() will be called from either CoreApp or a parent controller's init
   * method
   * 
   */
  public function init() {
    $resourcesIterator = new SimpleIterator($this->resources);
    $resourcesData = $this->request->getResourceData();
    $renderFlag = true;
    $requestObject = new RequestObject();
    while ($resourcesIterator->hasNext()) {
      $resourceValue = $resourcesIterator->next();
      $controllerSet = $requestObject->setControllerNamespace($resourceValue);
      $dirSet = $requestObject->isResourceDirectory($resourceValue);
      if ($controllerSet) {
        $reflectionClass = new \ReflectionClass($requestObject->getControllerNamespace());
        $resourcesArray = $resourcesIterator->truncateFromIndex($resourcesIterator->getIndex());
        $controllerClass = $reflectionClass->newInstance($requestObject, $resourcesArray, $this);
        $controllerClass->init();
        $renderFlag = false;
        $truncatedResources = $resourcesIterator->truncateFromIndex($resourcesIterator->getIndex());
        $controller = new $resourcesData['CONTROLLERS'][$resourceValue]->className($truncatedResources);
        $controller->init();
        return;
      }
      if ($dirSet) {
        $resourcesIterator->next();
      } else {
        $requestObject->setRequestArgument($resourceValue);
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
    $parent->getParams($parent, $params);
    return $params;
    
  }
  /**
   * Default method, will load model from id set in init
   * and render the "get" view for this controller.
   * Also responsible for rendering forms for "update" and "new" requests.
   */
  protected function get() {
    $params = $this->getParams($this);
    print_r($params);
  }

  protected function index() {
    echo "<pre>";
    echo "got here with index";
    echo "<pre>";
  }
  /**
   * Default method. Called from PUT requests.
   */
  protected function update() {
    
  }
  /**
   * Default method. Called from CREATE requests
   */
  protected function create() {
    
  }
  /**
   * Default method. Called from DELETE requests
   */
  protected function delete() {
    
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
    print_r($params);
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