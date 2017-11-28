angular.module('giveModule').factory('giveService', ['$http','$rootScope', '$q', function($http, $rootScope, $q){

	var self = {

		'cargando'		: false,
		'err'     		: false, 
		'conteo' 		: 0,
		'give' 			: [],
		'pag_actual'    : 1,
		'pag_siguiente' : 1,
		'pag_anterior'  : 1,
		'total_paginas' : 1,
		'paginas'	    : [],

		formatDate: function( date ) {
			var d = new Date(date);
			var month = '' + (d.getMonth() + 1);
			var day = '' + d.getDate();
			var year = d.getFullYear();

			if (month.length < 2) month = '0' + month;
			if (day.length < 2) day = '0' + day;

			return [year, month, day].join('-');
		},

		guardar: function( data ){

			var give = JSON.parse( JSON.stringify( data ) );
			give.fec_pre = self.formatDate(give.fec_pre);
			give.id_userin = $rootScope.userID;
			
			var d = $q.defer();

			$http.post('rest/v1/give/' , give )
				.success(function( respuesta ){

					if ( respuesta.error == 'not' ) {
						self.cargarPagina( self.pag_actual  );
						d.resolve();
						swal("CORRECTO", "¡"+"ID: "+respuesta.id+" "+respuesta.msj+"!", "success");
					} else 
					if ( respuesta.error == 'yes' )
						swal("ERROR", "¡"+respuesta.msj+"!", "error");
					else 
						swal("ERROR SERVER", "¡"+respuesta+"!", "error");
				}).error(function(err) {
					d.reject();
					console.error(err);
				});

			return d.promise;

		},

		eliminar: function( id ){

			var d = $q.defer();

			$http.delete('rest/v1/give/' + id )
				.success(function( respuesta ){

					self.cargarPagina( self.pag_actual  );
					d.resolve(respuesta);

				}).error(function(err) {
					console.log(err);
					d.reject(err);
				});

			return d.promise;

		},


		cargarPagina: function( pag ){

			var d = $q.defer();

			$http.get('rest/v1/give/' + pag + '/'+ $rootScope.userID )
				.success(function( data ){
					//console.log( data );
					if(data) {
						data.gives.forEach(function(element,index,array) {
							var values = element.fec_pre.split("-");
							//var values = element.fec_pre.replace('-',',');
							element.fec_pre = new Date(Number(values[0]), Number(values[1]-1), Number(values[2]));
							//element.fec_pre = new Date(element.fec_pre);
							element.amount = Number(element.amount);
							element.month = Number(element.month);
							element.fine = Number(element.fine);
							element.interest = Number(element.interest);
							element.gain = Number(element.gain);
						});

						self.err           = data.err;
						self.conteo        = data.conteo;
						self.give          = data.gives;
						self.pag_actual    = data.pag_actual;
						self.pag_siguiente = data.pag_siguiente;
						self.pag_anterior  = data.pag_anterior;
						self.total_paginas = data.total_paginas;
						self.paginas       = data.paginas;

					}
					return d.resolve();
				});

			return d.promise;
		},

		cargarUsers: function() {
			var d = $q.defer();

			$http.get('rest/v1/give/users')
				.success(function( response ) {
					d.resolve(response);
				})
				.error(function( err ) {
					d.reject(err);
					console.error(err);
				});

			return d.promise;
		},
		cargarClients: function() {
			var d = $q.defer();

			$http.get('rest/v1/give/clients')
				.success(function( response ) {
					d.resolve(response);
				})
				.error(function( err ) {
					d.reject(err);
					console.error(err);
				});

			return d.promise;
		}


	};

	return self;

}]);


// ================================================
//   Filtros
// ================================================
angular.module('userModule').filter( 'edad', function(){

	return function(fecha){
		if( fecha ){
			var values=fecha.split("-");
			var dia = values[2];
			var mes = values[1];
			var ano = values[0];

			// cogemos los valores actuales
			var fecha_hoy = new Date();
			var ahora_ano = fecha_hoy.getYear();
			var ahora_mes = fecha_hoy.getMonth()+1;
			var ahora_dia = fecha_hoy.getDate();

			// realizamos el calculo
			var edad = (ahora_ano + 1900) - ano;
			if ( ahora_mes < mes )
			{
			    edad--;
			}
			if ((mes == ahora_mes) && (ahora_dia < dia))
			{
			    edad--;
			}
			if (edad > 1900)
			{
			    edad -= 1900;
			}
			return edad;
		}
	}
});