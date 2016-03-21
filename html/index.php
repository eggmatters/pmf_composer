<?php 

require_once dirname(__DIR__) . '/autoload.php';
require_once dirname(__DIR__) . '/configurations/ModelMapper.php';

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
$cons = new \core\Constraints();
$cons->term("meh", "=", "foo")->andTerm("blah", "!=", "heh")->andTerm()->groupBegin()->term("k", "!=", "v")->orTerm("l", "!=", "t")->groupEnd();
$qb = new core\QueryBase('app\models\PostModel');
$qb->Select()->Join('posts,tags,users')->Where($cons);

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
       print_r($qb->getSelect());
       echo "<br>";
      ?>
    </pre>
  </body>
</html>