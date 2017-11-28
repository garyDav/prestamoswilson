(function(angular) {

	'use strict';
	var app = angular.module('prestamosModule',[
		'ngResource',
		'ngRoute',
		'angular-loading-bar',
		'jcs-autoValidate',
		'clientsModule',
		'giveModule',
		'graphicModule',
		'reportModule',
		'userModule'
	], ["$provide", function($provide) {
		var PLURAL_CATEGORY = {ZERO: "zero", ONE: "one", TWO: "two", FEW: "few", MANY: "many", OTHER: "other"};
		$provide.value("$locale", {
		  "DATETIME_FORMATS": {
		    "AMPMS": [
		      "a.m.",
		      "p.m."
		    ],
		    "DAY": [
		      "domingo",
		      "lunes",
		      "martes",
		      "mi\u00e9rcoles",
		      "jueves",
		      "viernes",
		      "s\u00e1bado"
		    ],
		    "MONTH": [
		      "enero",
		      "febrero",
		      "marzo",
		      "abril",
		      "mayo",
		      "junio",
		      "julio",
		      "agosto",
		      "septiembre",
		      "octubre",
		      "noviembre",
		      "diciembre"
		    ],
		    "SHORTDAY": [
		      "dom",
		      "lun",
		      "mar",
		      "mi\u00e9",
		      "jue",
		      "vie",
		      "s\u00e1b"
		    ],
		    "SHORTMONTH": [
		      "ene",
		      "feb",
		      "mar",
		      "abr",
		      "may",
		      "jun",
		      "jul",
		      "ago",
		      "sep",
		      "oct",
		      "nov",
		      "dic"
		    ],
		    "fullDate": "EEEE, d 'de' MMMM 'de' y",
		    "longDate": "d 'de' MMMM 'de' y",
		    "medium": "dd/MM/yyyy HH:mm:ss",
		    "mediumDate": "dd/MM/yyyy",
		    "mediumTime": "HH:mm:ss",
		    "short": "dd/MM/yy HH:mm",
		    "shortDate": "dd/MM/yy",
		    "shortTime": "HH:mm"
		  },
		  "NUMBER_FORMATS": {
		    "CURRENCY_SYM": "\u20ac",
		    "DECIMAL_SEP": ",",
		    "GROUP_SEP": ".",
		    "PATTERNS": [
		      {
		        "gSize": 3,
		        "lgSize": 3,
		        "macFrac": 0,
		        "maxFrac": 3,
		        "minFrac": 0,
		        "minInt": 1,
		        "negPre": "-",
		        "negSuf": "",
		        "posPre": "",
		        "posSuf": ""
		      },
		      {
		        "gSize": 3,
		        "lgSize": 3,
		        "macFrac": 0,
		        "maxFrac": 2,
		        "minFrac": 2,
		        "minInt": 1,
		        "negPre": "-",
		        "negSuf": "\u00a0\u00a4",
		        "posPre": "",
		        "posSuf": "\u00a0\u00a4"
		      }
		    ]
		  },
		  "id": "es-es",
		  "pluralCat": function (n) {  if (n == 1) {   return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;}
		});
		}]).run([
        'bootstrap3ElementModifier',
        function (bootstrap3ElementModifier) {
              bootstrap3ElementModifier.enableValidationStateIcons(true);
       }]);



	angular.module('jcs-autoValidate')
	.run([
	    'defaultErrorMessageResolver',
	    function (defaultErrorMessageResolver) {
	        // To change the root resource file path
	        defaultErrorMessageResolver.setI18nFileRootPath('app/lib');
	        defaultErrorMessageResolver.setCulture('es-co');

	        defaultErrorMessageResolver.getErrorMessages().then(function (errorMessages) {
	          errorMessages['coincide'] = 'Su contraseña no coincide';
	          errorMessages['parse'] = 'Debe ingresar la nueva contraseña';
	        });
	    }
	]);

	app.directive('coincide', [
            function() {
                return {
                    restrict: 'A',
                    require: 'ngModel',
                    link: function(scope, elm, attrs, ctrl) {

                        var validateFn = function (viewValue) {
                        	if(attrs.coincide != '')
	                            if (ctrl.$isEmpty(viewValue) || viewValue.indexOf(attrs.coincide) === -1) {
	                                ctrl.$setValidity('coincide', false);
	                                return undefined;
	                            } else {
	                                ctrl.$setValidity('coincide', true);
	                                return viewValue;
	                            }
	                        /*else {
	                        	ctrl.$setValidity('parse', false);
	                        }*/
                        };

                        ctrl.$parsers.push(validateFn);
                        ctrl.$formatters.push(validateFn);
                    }
                };
            }]);

	app.config(['$locationProvider',function($locationProvider) {
		$locationProvider.html5Mode(true);
	}]);
	app.config(['cfpLoadingBarProvider',function(cfpLoadingBarProvider) {
		cfpLoadingBarProvider.includeSpinner = true;
	}]);

	app.config(['$routeProvider',function($routeProvider) {
		$routeProvider.
			when('/',{
				templateUrl: 'public/give/views/main.view.html',
				controller: 'principalCtrl'
			}).
			when('/404',{
				templateUrl: 'public/main/views/404.view.html'
			}).
			otherwise({
				redirectTo: '/404'
			});
	}]);

	app.factory('mainService', ['$http','$rootScope','$location','$q', function( $http,$rootScope,$location,$q){
		var self = {
			'cargando'		: false,
			'err'     		: false, 
			'conteo' 		: 0,
			'give_pay' 		: [],
			'pag_actual'    : 1,
			'pag_siguiente' : 1,
			'pag_anterior'  : 1,
			'total_paginas' : 1,
			'paginas'	    : [],

			logout: function() {
				$http.post('php/destroy_session.php');
				//$location.path('/login/#/ingresar');
				window.location="login/";
			},
			config:{},
			cargar: function(){
				var d = $q.defer();
				$http.get('configuracion.json')
					.success(function(data){
						self.config = data;
						d.resolve();
					})
					.error(function(){
						d.reject();
						console.error('No se pudo cargar el archivo de configuración');
					});

				return d.promise;
			},
			cargarPrestamos: function(pag) {
				var d = $q.defer();
				$http.get('rest/v1/main/give/'+pag+'/'+$rootScope.userID).success(function(response) {

					self.err           = response.err;
					self.conteo        = response.conteo;
					self.give_pay      = response.gives;
					self.pag_actual    = response.pag_actual;
					self.pag_siguiente = response.pag_siguiente;
					self.pag_anterior  = response.pag_anterior;
					self.total_paginas = response.total_paginas;
					self.paginas       = response.paginas;
					d.resolve();

				}).error(function(err) {
					d.reject(err);
					console.error(err);
				});
				return d.promise;
			},
			editarUser: function(user) {
				return $http.put('rest/v1/user/'+user.id,user);
			},
			data: function() {
				var d = $q.defer();

				$http.get('php/data.php' )
					.success(function( data ){
						if(data) {
							if( data.error == 'yes' ) {
								$http.post('php/destroy_session.php');
								window.location="login/";
							} else {
								if(data.error == 'not') {
									$rootScope.userID = data.userID;
									$rootScope.userTYPE = data.userTYPE;
								}
							}

						}
						return d.resolve();
					});

				return d.promise;
			},
			mainUser: function(id) {
				var d = $q.defer();
				$http.get('rest/v1/user/view/'+id)
					.success(function( data ) {
						d.resolve(data);
					});
				return d.promise;
			},
			formatDate: function( date ) {
				var d = new Date(date);
				var month = '' + (d.getMonth() + 1);
				var day = '' + d.getDate();
				var year = d.getFullYear();

				if (month.length < 2) month = '0' + month;
				if (day.length < 2) day = '0' + day;

				return [year, month, day].join('-');
			},
			guardarPayment: function(data) {
				$('#myModal'+data.id_give).removeClass('fade');
				$('#myModal'+data.id_give).modal('hide');
				var payment = JSON.parse( JSON.stringify( data ) );
				payment.fec_pago = self.formatDate(payment.fec_pago);
				
				var d = $q.defer();

				$http.post('rest/v1/payment/' , payment )
					.success(function( response ){
						
						self.cargarPrestamos(self.pag_actual);
						d.resolve(response);
						console.log(response);

					}).error(function(err) {
						d.reject();
						console.error(err);
					});

				return d.promise;
			}
		};
		return self;
	}]);


	app.controller('mainCtrl', ['$scope', '$rootScope', 'mainService', 'upload', function($scope,$rootScope,mainService,upload){
		$scope.config = {};
		$scope.titulo    = "";
		$scope.subtitulo = "";

		$scope.mainUser = {};
		$scope.userSelMain = {};
		$scope.editUser = {};

		mainService.cargar().then( function(){
			$scope.config = mainService.config;
			//console.log($scope.config);
		});

		$scope.init = function() {
			mainService.data().then( function(){
				mainService.mainUser($rootScope.userID).then(function( data ) {
					$scope.mainUser = data;
				});
			});
		};

		$scope.mostrarUserModal = function(){
			$scope.init();
			$scope.userSelMain = {};

			$scope.userSelMain = $scope.mainUser;
			$("#modal_userMain").modal();
		};

		$scope.cancelarUserMain = function(frmUser) {
			location.reload();
		}


		// ================================================
		//   Funciones Globales del Scope
		// ================================================
		$scope.activar = function( menu, submenu, titulo, subtitulo ){

			$scope.titulo = "";
			$scope.subtitulo = "";

			$scope.titulo = titulo;
			$scope.subtitulo = subtitulo;
			//console.log($scope.titulo);

			$scope.mPrincipal = "";
			$scope.mUsers = "";
			$scope.mClients = "";
			$scope.mGives = "";
			$scope.mReport = "";
			$scope.mGraphic = "";

			$scope[menu] = 'active';

		};
		$scope.salir = function() {
			mainService.logout();
			//console.log('Mierda');
		};

		$scope.editarUserMain = function(user,frmUser) {
			if( (user.pwdN != '' || user.pwdR != '') && (user.pwdA == null || user.pwdA == '') ) {
				swal("ERROR", "¡Antes debe ingresar su contraseña atigua!", "error");
			}
			else {
			if(typeof user.src == 'object')
				upload.saveImg(user.src).then(function( data ) {
					if ( data.error == 'not' ) {
						user.src = data.src;
						$scope.mainUser.src = data.src;
						mainService.editarUser(user).success(function(response){
							$scope.editUser = response;
							if( $scope.editUser.error == 'not' )
								swal("CORRECTO", "¡"+data.msj+" - "+$scope.editUser.msj+"!", "success");
							else
								if ( $scope.editUser.error == 'yes' )
								swal("ERROR", "¡"+$scope.editUser.msj+"!", "error");
							else 
								swal("ERROR SERVER", "¡"+$scope.editUser+"!", "error");
						})
						.error(function(response){
							console.error(response);
						});
					} else 
					if ( data.error == 'yes' )
						swal("ERROR", "¡"+data.msj+"!", "error");
					else 
						swal("ERROR SERVER", "¡"+data+"!", "error");
				});
			else {
				mainService.editarUser(user).success(function(response){
					$scope.editUser = response;
					if( $scope.editUser.error == 'not' )
						swal("CORRECTO", "¡"+$scope.editUser.msj+"!", "success");
					else
						if ( $scope.editUser.error == 'yes' )
						swal("ERROR", "¡"+$scope.editUser.msj+"!", "error");
					else 
						swal("ERROR SERVER", "¡"+$scope.editUser+"!", "error");
				})
				.error(function(response){
					console.error(response);
				});
			}
			$scope.userSelMain = {};

			frmUser.autoValidateFormOptions.resetForm();
			$("#modal_userMain").modal('hide');
			}
		};

	}]);

	// ================================================
	//   Controlador de principal
	// ================================================
	app.controller('principalCtrl', ['$scope','mainService', function($scope,mainService){
		$scope.activar('mPrincipal','','Principal','información');

		$scope.mgives = [];
		$scope.paymentSelt = {
			id_give: '',
			fec_pago_actual: '',
			fec_pago: '',
			observation: ''
		};
		$scope.viewForm = false;
		$scope.load = false;

		//Prestamo en el presente año
		var fec = new Date();
		$scope.fechaMin = ""+fec.getFullYear()+"-01-01";
		$scope.fechaMax = ""+(Number(fec.getFullYear())+3)+"-12-31";

		$scope.moverA = function( pag ){
			$scope.load = true;

			mainService.cargarPrestamos( pag ).then(function() {
				$scope.mgives = mainService;
				$scope.load = false;
				console.log($scope.mgives);
			});

		};

		$scope.moverA(1);
		

		$scope.mostrarModal = function( id ){

			$scope.viewForm = false;
			$scope.paymentSelt = {};
			$("#myModal"+id).modal();

		};

		$scope.cargarPre = function(fec,id) {
			$scope.viewForm = true;
			var values = fec.split("-");
			var fecha = new Date(Number(values[0]), Number(values[1]-1), Number(values[2]));
			//var fecha = new Date(fec);
			$scope.paymentSelt.id_give = id;
			$scope.paymentSelt.fec_pago_actual = fec;
			$scope.paymentSelt.fec_pago = fecha;
			$scope.paymentSelt.observation = 'Depósito en ';
			//console.log($scope.paymentSelt);
		};

		$scope.guardarPayment = function( data,frm ) {
			if(data.fec_pago) {
				mainService.guardarPayment( data ).then(function(response){

					// codigo cuando se guardo
					$scope.data = {};
					$scope.viewForm = false;
					if ( response.error == 'not' ) {
						swal("CORRECTO", "¡"+"Pago: "+response.pago+" "+response.msj+"!", "success");
					} else
					if ( response.error == 'yes' )
						swal("ERROR", "¡"+response.msj+"!", "error");
					else {
						swal("ERROR SERVER", "¡"+response+"!", "error");
						console.error(response);
					}
					//$(".modal-backdrop").remove();
				});

			}
		};
		$scope.desplegar = function(a,b) {
			if(a==b)
				return 'in';
		}

	}]);

	// ================================================
	//   Filtros
	// ================================================
	app.filter( 'quitarletra', function(){

		return function(palabra){
			if( palabra ){
				if( palabra.length > 1)
					return palabra.substr(1);
				else
					return palabra;
			}
		}
	});

	app.filter( 'palabra', function(){

		return function(palabra){
			if( palabra ){
				var unaPalabra = palabra.split(" ");
				if( unaPalabra[0] )
					return unaPalabra[0];
				else
					return '';
			}
		}
	});

	app.filter( 'reducirTexto', function(){
		return function(palabra){
			if( palabra ){
				if( palabra.length > 32)
					return palabra.substr(0,32)+'...';
				else
					return palabra;
			}
		}
	});

	app.filter( 'textoCorto', function(){
		return function(palabra){
			if( palabra ){
				if( palabra.length > 16)
					return palabra.substr(0,16)+'...';
				else
					return palabra;
			}
		}
	});

	app.filter( 'anio', function(){
		return function(fec){
			if( fec ){
				if( fec.length > 0)
					return fec.substr(0,4);
				else
					return fec;
			}
		}
	});

	app.filter('timeVerbal',function() {
		return function(fecha) {
			if(fecha) {
				var tiempo = new Date();
				var fDia = Number(fecha.substr(8,2));
				var fMes = Number(fecha.substr(5,2));
				var fAnio = Number(fecha.substr(0,4));
				var dias = new Array('domingo','lunes','martes','miercoles','jueves','viernes','sábado');
				var meses = new Array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
				var values = fecha.split("-");
				var fechaObj = new Date(Number(values[0]), Number(values[1]-1), Number(values[2]));
				var verbal = fecha.substr(8,2)+' de '+meses[fMes-1];

				if( fAnio == tiempo.getUTCFullYear() )
					if( fMes == (tiempo.getMonth()+1) ) {
						if( fDia == tiempo.getDate()+1 )
							verbal = 'Mañana';
						if( fDia == tiempo.getDate() )
							verbal = 'Hoy';
						if( fDia == tiempo.getDate()-1 )
							verbal = 'Ayer';
						if( fDia > tiempo.getDate()+1 && fDia <= tiempo.getDate()+7 )
							verbal = dias[fechaObj.getDay()];
					}

				return verbal;
			}else {
				return fecha;
			}
		}
	});

	app.filter('colorRojo',function() {
		return function(fecha) {
			if(fecha) {
				var fDia = Number(fecha.substr(8,2));
				var tiempo = new Date();
				var color = '';

				var dias = fDia-tiempo.getDate();

				if( tiempo.getMonth()+1 == 2 ) {
					if( ( (dias > -1 && dias < 4) || dias < -24 ) )
						color = 'rojo';
				}
				else
					if( ( (dias > -1 && dias < 4) || dias < -26 ) )
						color = 'rojo';

				return color;
			}else {
				return '';
			}
		}
	});

	app.filter('colorAmarillo',function() {
		return function(fecha) {
			if(fecha) {
				var fDia = Number(fecha.substr(8,2));
				var tiempo = new Date();
				var color = '';

				var dias = fDia-tiempo.getDate();

				if( tiempo.getMonth()+1 == 2 ) {
					if( (dias > 3 && dias < 8) || (dias > -25 && dias < -20) )
						color = 'amarillo';
				}
				else
					if( (dias > 3 && dias < 8) || (dias > -27 && dias < -22) )
						color = 'amarillo';

				return color;
			}else {
				return '';
			}
		}
	});

	app.filter('colorVerde',function() {
		return function(fecha) {
			if(fecha) {
				var fDia = Number(fecha.substr(8,2));
				var tiempo = new Date();
				var color = '';

				var dias = fDia-tiempo.getDate();

				if( tiempo.getMonth()+1 == 2 ) {
					if( dias > 7 || (dias > -21 && dias < 0) )
						color = 'verde';
				}
				else
					if( dias > 7 || (dias > -23 && dias < 0) )
						color = 'verde';

				return color;
			}else {
				return '';
			}
		}
	});

	// ================================================
	//   Directiva para archivos
	// ================================================
	app.directive('fileModel',['$parse',function($parse) {
		return {
			restrict: 'A',
			link: function(scope, iElement, iAttrs) {
				iElement.on('change',function(e) {
					$parse(iAttrs.fileModel).assign(scope,iElement[0].files[0]);
				});
			}
		};
	}]);

	// ================================================
	//   Servicio para cargar archivos
	// ================================================
	app.service('upload',['$http','$q',function($http,$q) {
		var self = {
			saveImg : function(img) {
				var d = $q.defer();
				var formData = new FormData();
				formData.append('img',img);
				$http.post('php/server.php',formData,{
					headers: { 'Content-Type': undefined }
				}).success(function( data ) {
					d.resolve( data );
				}).error(function(msj, code) {
					d.reject( msj );
				});
				return d.promise;
			}
		};
		return self;
	}]);

})(window.angular);