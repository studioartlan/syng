
.config(['$routeProvider', function($routeProvider) {


	$routeProvider.when('/sngdemo/', {templateUrl: 'views/sngdemo.html', controller: 'studioartlan_sng_demo_index'});

	$routeProvider.when('/sngdemo/parameter/{parameter}', {templateUrl: 'views/sngdemo/parameter.html', controller: 'studioartlan_sng_demo_parameter'});
;

}]);
