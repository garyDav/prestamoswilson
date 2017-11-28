// ================================================
//   Controlador de clientes
// ================================================
angular.module('clientsModule').controller('clientsCtrl', ['$scope','$routeParams', 'clientsService', 'upload', function($scope, $routeParams, clientsService, upload){
//angular.module('clientsModule').controller('clientsCtrl', ['$scope','$routeParams', function($scope, $routeParams){

	var pag = $routeParams.pag;
	//console.log(pag);


	$scope.activar('mClients','','Clientes','listado');
	$scope.clientes   = {};
	$scope.clienteSel = {};
	$scope.load = true;
	//console.log($scope.load);

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

		clientsService.cargarPagina( pag ).then( function(){
			$scope.clientes = clientsService;
			$scope.load = false;
		});

	};

	$scope.moverA(pag);

	// ================================================
	//   Mostrar modal de edicion
	// ================================================
	$scope.mostrarModal = function( cliente ){

		//console.log( cliente );
		angular.copy( cliente, $scope.clienteSel );
		$("#modal_cliente").modal();

	}

	// ================================================
	//   Funcion para guardar
	// ================================================
	$scope.guardar = function( cliente, frmCliente){

		if( !cliente.src )
			cliente.src = '';

		if( typeof $scope.clienteSel.src == 'object' )
			upload.saveImg($scope.clienteSel.src).then(function( data ) {
				if ( data.error == 'not' ) {
					$scope.clienteSel.src = data.src;
					clientsService.guardar( cliente ).then(function( dataCli ){

						// codigo cuando se actualizo
						$("#modal_cliente").modal('hide');
						$scope.clienteSel = {};

						frmCliente.autoValidateFormOptions.resetForm();
						if( dataCli.error == 'not' )
							swal("¡Correcto!",data.msj+" ID: "+dataCli.id+" "+dataCli.msj, "success");
						else
							swal("¡Error!", dataCli , "error");

					});
				} else 
				if ( data.error == 'yes' )
					swal("ERROR", "¡"+data.msj+"!", "error");
				else {
					swal("ERROR SERVER", "¡"+data+"!", "error");
					console.error(data);
				}
			});
		else {
			clientsService.guardar( cliente ).then(function( dataCli ){

				// codigo cuando se actualizo
				$("#modal_cliente").modal('hide');
				$scope.clienteSel = {};

				frmCliente.autoValidateFormOptions.resetForm();
				if( dataCli.error == 'not' )
					swal("¡Correcto!", "ID: "+dataCli.id+" "+dataCli.msj, "success");
				else
					swal("¡Error!", dataCli , "error");

			});
		}

		/*} else {
			swal("ERROR", "¡Inserte una imagen!", "error");
		}*/

		


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
			clientsService.eliminar( id ).then(function( data ){
				if( data.error == 'not' )
					swal("Eliminado!", "ID: "+data.id+" "+data.msj, "success");
				else
					swal("Error!", data , "error");
			});
		});

	}

}]);
