<?php 

require_once dirname(__DIR__) . '/autoload.php';

//$modelAttributes = (object) array(
//  'shskhyu' => array(
//      'one' => 1,
//      'test' => (object)array(
//          'one' => 1,
//          'two' => 2,
//          'three' => 3
//    )
//  )
//);

//\core\CoreApp::routeRequest();

$qb = new core\QueryBase('app\models\TestModel');
$db1 = $qb->Select("test_value,test_data,user_name,user_role");

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
       echo "index.php";
      ?>
    </pre>
  </body>
</html>