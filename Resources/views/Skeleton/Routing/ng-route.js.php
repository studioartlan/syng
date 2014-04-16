<?=$appConfig['name']?>.config(['$routeProvider', function($routeProvider) {

<?php
foreach ($routes as $routeIdentifier => $route) :
?>
	$routeProvider.when('<?=$route['url']?>', {
		apiUrl: '<?=$route['apiUrl']?>',
		templateUrl: '<?=$route['templateName']?>',
		controller: '<?=$routeIdentifier?>'
	});

<?php endforeach; ?>
}]);
