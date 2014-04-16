<?php

$appName =$appConfig['name'];

?>
'use strict';

/* Controllers */

angular.module('<?=$appName?>.controllers', [])

<?php
foreach ($routes as $routeIdentifier => $route) :
?>

  .controller('<?=$routeIdentifier?>', [function() {

  }])

<?php endforeach; ?>
;


