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
   * determinants provided from URI
   * @var array $resources
   */
  protected $resources;
  /**
   * Instantiate Request object
   * @var Request $request
   */
  protected $request;
  /**
   * User defined array of models which may be associated with this controller.
   * If undefined, BaseModel will attempt to join any and all models specified in the path.
   * @var array 
   */
  protected $associatedModels;
  /**
   * Reflection property called by contructor setting name of child controller.
   * @var string
   */
  protected $controllerName;
  /**
   * Constructor accepts the Request object and an optional array of resources.
   * The resources are values obtained from the URL by the Request object.
   * Nested controller instances (i.e. /controllerA/id/Controller/b) will receive
   * a trunctated version of the Request->resources array. @see init() for details.
   * @param \core\Request $request
   * @param array $resources
   */
  public function __construct(Request $request, $resources = null) {
    $this->request = $request;
    if (is_null($resources)) {
      $this->resources = $request->getResourceArray();
    } else {
      $this->resources = $resources;
    }
    $reflectionClass = new \ReflectionClass($this);
    $this->controllerName = $reflectionClass->getName();
  }
  /**
   * init() parses the resources array, determining the resource type from url 
   * paramaters set in the Request->resources array.
   * This method "delegates" requests to nested controllers. If an additional controller
   * is on the route, we stop processing and instantiate the next controller.
   * 
   */
  public function init() {
    $resourcesIterator = new SimpleIterator($this->resources);
    $renderFlag = true;
    while ($resourcesIterator->hasNext()) {
      $resourceValue = $resourcesIterator->next();
      $resourceType = CoreApp::getResourceType($resourceValue);
      switch ($resourceType) {
        case "controller":
          $this->loadController($resourceValue, $resourcesIterator->truncateFromIndex($resourcesIterator->getIndex()));
          $renderFlag = false;
          return;
        case "int":
          $this->request->setRequestedId($resourceValue);
          break;
        case "string":
          //call method if exists.
          $this->request->setRequestedTag($resourceValue);
          break;
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
    echo "got here with get in $this->controllerName";
  }
  /**
   * Default method, loads all models (if required to) and
   * render the "index" view for this controller
   */
  protected function index() {
    echo "got here with index in $this->controllerName";
  }
  /**
   * Default method. Called from PUT requests.
   */
  protected function update() {
    echo "got here with update in $this->controllerName";
  }
  /**
   * Default method. Called from CREATE requests
   */
  protected function create() {
    echo "got here with create in $this->controllerName";
  }
  /**
   * Default method. Called from DELETE requests
   */
  protected function delete() {
    echo "got here with delete in $this->controllerName";
  }
  /**
   * Convention helper. This class simply returns the model associated with this
   * controller or null. We want to allow controllers to not be tied with models,
   * (i.e. default IndexController) so the callers shouldn't freak out if they get 
   * null.
   * @return string
   */
  protected function getModelClass() {
    $reflectionClass = new \ReflectionClass($this);
    $className = $reflectionClass->getName();
    $classBase = str_replace($className, 'Controller', '');
    $testModel = "app//models//" . Inflector::singularize($classBase) . "Model";
    if (class_exists($testModel)) {
      return $testModel;
    }
    return null;
  }
  
  private function loadController($resourceValue, $resourceStack) {
    $controllerName = CoreApp::getControllerClassPath($resourceValue);
    if ($controllerName == $this->controllerName) {
      return;
    }
    $reflectionClass = new \ReflectionClass($controllerName);
    $controllerInstance = $reflectionClass->newInstance($this->request, $resourceStack);
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
  
  private function prepareGet() {
    if (is_null($this->request->getRequestedId())) {
      $this->index();
    } else {
      $this->get();
    }
  }
  private function prepareDelete() {
    if (is_null($this->request->getRequestedId())) {
      CoreApp::issue("404");
    } else {
      $this->delete();
    }
  }
  
  private function prepareUpdate() {
    if (is_null($this->request->getRequestedId())) {
      CoreApp::issue("404");
    } else {
      $this->delete();
    }
  }
  
  private function loadModel() {
    $modelClass = $this->getModelClass();
    if (is_null($modelClass)) {
      return null;
    }
    $reflectionClass = new \ReflectionClass($modelClass);
    return $reflectionClass->newInstance($this->request);
  }

}