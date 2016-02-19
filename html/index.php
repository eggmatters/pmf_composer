<?php 

require_once dirname(__DIR__) . '/autoload.php';

$app = new app\controllers\IndexController();
$model = new app\models\IndexModel();
$model->get();
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
        print_r($_GET);
        print_r($_SERVER);
      ?>
    </pre>
  </body>
</html>
