<?php

$appName =$appConfig['name'];

?>
'use strict';

// Declare app level module which depends on filters, and services
var <?=$appName?> = angular.module('<?=$appName?>', [
  'ngRoute',
  '<?=$appName?>.filters',
  '<?=$appName?>.services',
  '<?=$appName?>.directives',
  '<?=$appName?>.controllers'
]);
