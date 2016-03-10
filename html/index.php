<?php 

require_once dirname(__DIR__) . '/autoload.php';

$modelAttributes = (object) array(
  'shskhyu' => array(
      'one' => 1,
      'test' => (object)array(
          'one' => 1,
          'two' => 2,
          'three' => 3
    )
  )
);

\core\CoreApp::routeRequest();

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title></title>
  </head>
  <body>
    <pre>
      <?php
       
      ?>
    </pre>
  </body>
</html>
