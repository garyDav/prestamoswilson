(function(angular) {

	'use strict';	
	
	angular.module('giveModule')
	.config(['$routeProvider',function($routeProvider) {
		$routeProvider.
			when('/gives',{
				templateUrl: 'public/give/views/list.view.html',
				controller: 'giveCtrl'
			});
	}]);



})(window.angular);