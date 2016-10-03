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
    $this->getModelClass();
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
  /**
   * Default method, will load model from id set in init
   * and render the "get" view for this controller.
   * Also responsible for rendering forms for "update" and "new" requests.
   */
  protected function get() {
    echo "<pre>";
    print_r($this->model);
    echo "<pre>";
  }

  protected function index() {
    echo "<pre>";
    print_r($this->models);
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
  /**
   * Convention helper. This class simply returns the model associated with this
   * controller or null. We want to allow controllers to not be tied with models,
   * (i.e. default IndexController) so the callers shouldn't freak out if they get 
   * null.
   * @return string
   */
  protected function getModelClass() {
    if (!$this->requestObject->getModelNamespace()) {
      return null;
    }
    $reflectionClass = new \ReflectionClass($this->requestObject->getModelNamespace());
    $this->model = $reflectionClass->newInstance();
  }
  
  private function loadController($resourceValue, $resourceStack) {
    $controllerName = CoreApp::getControllerClassPath($resourceValue);
    if ($controllerName == $this->controllerName) {
      return;
    }
    $reflectionClass = new \ReflectionClass($controllerName);
    $controllerInstance = $reflectionClass->newInstance($resourceStack);
    $controllerInstance->init();
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
    $this->loadModels();
    $this->index();
  }
  
  private function prepareGet() {
    if (empty($this->requestObject->getRequestArguments())) {
      $this->prepareIndex();
    } else {
      $this->loadModel();
      $this->get();
    }
  }
  private function prepareDelete() {
    if (is_null($this->request->getRequestedIds($this->controllerName))) {
      CoreApp::issue("404");
    } else {
      $this->loadModel();
    }
  }
  
  private function prepareUpdate() {
    if (is_null($this->request->getRequestedIds($this->controllerName))) {
      CoreApp::issue("404");
    } else {
      $this->loadModel();
      $this->update();
    }
  }
  
  private function loadModel() {
    $modelBase = $this->getModelClass();
    $id = $this->request->getRequestedIds($this->controllerName)->id;
    $this->model = $modelBase::get($id, $this->request->getRequestUri());
  }
  
  private function loadModels() {
    $modelBase = $this->getModelClass();
    $this->models = $modelBase::getAll($this->request->getRequestUri());
  }

}