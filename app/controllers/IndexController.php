<?php
/**
 * Description of IndexController
 *
 * @author meggers
 */
namespace app\controllers;
use core\ControllerBase;
use core\Response;

class IndexController extends ControllerBase {

  public function get() {
    
  }
  
  public function index() {
    $this->render('index', ['layout' => 'main']);
  }
  
  public function getAbout(string $about) {
    
  }
}
