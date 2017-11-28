(function(angular) {

	'use strict';	
	
	angular.module('reportModule')
	.config(['$routeProvider',function($routeProvider) {
		$routeProvider.
			when('/reports',{
				templateUrl: 'public/reports/views/report.view.html',
				controller: 'reportCtrl'
			});
	}]);



})(window.angular);