
.config(['$routeProvider', function($routeProvider) {

<?php
foreach ($routes as $routeIdentifier => $route) :
?>

	$routeProvider.when('<?=$route['url']?>', {templateUrl: '<?=$route['templateName']?>', controller: '<?=$routeIdentifier?>'});
<?php endforeach; ?>;

}]);
