(function(angular) {
	'use strict';

	angular.module('clientsModule')
	.config(['$routeProvider',function($routeProvider) {
		$routeProvider.
			when('/clients/:pag',{
				templateUrl: 'public/clients/views/list.view.html',
				controller: 'clientsCtrl'
			});
	}]);


})(window.angular);