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

//$qb = new core\QueryBase('app\models\PostModel');
//$db1 = $qb->Select()->Join('posts,tags');
$cons = new \core\Constraints();

$cons->term("meh", "=", "foo")->andTerm("blah", "!=", "heh")->andTerm()->groupBegin()->term("k", "!=", "v")->orTerm("l", "!=", "t")->groupEnd();
//"meh = foo AND blah != heh AND ( k != v OR l != t )" 

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
       print_r($cons->getConstraints());
       
      ?>
    </pre>
  </body>
</html>