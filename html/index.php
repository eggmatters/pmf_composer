<?php 

require_once dirname(__DIR__) . '/autoload.php';

$httpRequest = new core\Request();
core\CoreApp::routeRequest($httpRequest);

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>PMF Test Page</title>
  </head>
  <body>
    <pre>
    <?php print_r($httpRequest); ?>
    </pre>
  </body>
</html>