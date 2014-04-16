<?=$appConfig['name']?>.config(function($stateProvider, $urlRouterProvider) {

//  $urlRouterProvider.otherwise("/state1");

	$stateProvider<?php

foreach ($routes as $routeIdentifier => $route) :

?>


	.state('<?=$routeIdentifier?>', {
		"url": "<?=$route['url']?>",
		"templateUrl": "<?=$route['templateName']?>",
		"apiUrl": "<?=$route['apiUrl']?>"
	})<?php endforeach; ?>;

});