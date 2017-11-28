// ================================================
//   Controlador de usuarios
// ================================================
angular.module('userModule').controller('userCtrl', ['$scope','$routeParams', 'userService', function($scope, $routeParams, userService){

	var pag = $routeParams.pag;

	$scope.activar('mUsers','','Usuarios','lista de usuarios');
	$scope.users   = {};
	$scope.userSel = {};
	$scope.load = true;

	//Edad Minima
	var em = new Date();
	em.setFullYear(em.getFullYear()-70);
	$scope.edadMinima = ""+em.getFullYear()+"-01-01";

	//Edad Maxima
	var me = new Date();
	me.setFullYear(me.getFullYear()-18);
	$scope.edadMaxima = ""+me.getFullYear()+"-01-01";


	$scope.moverA = function( pag ){
		$scope.load = true;

		userService.cargarPagina( pag ).then( function(){
			$scope.users = userService;
			$scope.load = false;
		});

	};

	$scope.moverA(pag);

	// ================================================
	//   Mostrar modal de edicion
	// ================================================
	$scope.mostrarModal = function( user ){

		// console.log( user );
		angular.copy( user, $scope.userSel );
		$("#modal_user").modal();

	}

	// ================================================
	//   Funcion para guardar
	// ================================================
	$scope.guardar = function( user, frmUser){

		userService.guardar( user ).then(function(){

			// codigo cuando se actualizo
			$("#modal_user").modal('hide');
			$scope.userSel = {};

			frmUser.autoValidateFormOptions.resetForm();

		});


	}
	// ================================================
	//   Funcion para eliminar
	// ================================================
	$scope.eliminar = function( id ){

		swal({
			title: "¿Esta seguro de eliminar?",
			text: "¡Si confirma esta acción eliminará el registro!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Si, Eliminar!",
			closeOnConfirm: false
		},
		function(){
			userService.eliminar( id ).then(function(){
				swal("Eliminado!", "Registro eliminado correctamente.", "success");
			});
		});

	}

}]);
