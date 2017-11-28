(function(angular) {

	'use strict';	
	
	angular.module('graphicModule')
	.config(['$routeProvider',function($routeProvider) {
		$routeProvider.
			when('/graphic',{
				templateUrl: 'public/graphics/views/graphic.view.html',
				controller: 'graphicCtrl'
			});
	}]);



})(window.angular);