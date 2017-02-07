<?php 

require_once dirname(__DIR__) . '/autoload.php';

$httpRequest = new core\Request();
core\CoreApp::routeRequest($httpRequest);