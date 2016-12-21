<?php 

require_once dirname(__DIR__) . '/autoload.php';
require_once dirname(__DIR__) . '/configurations/ModelMapper.php';

$httpRequest = new core\Request();
core\CoreApp::routeRequest($httpRequest, $resourcesIterator);

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>GOT HERE</title>
  </head>
  <body>
    <pre>
    <?php print_r($httpRequest); ?>
    </pre>
  </body>
</html>