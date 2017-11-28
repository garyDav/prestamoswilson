function rojo(fecha) {
	if(fecha) {
		var values = fecha.split("-");
		var ahora = new Date();
		var fechaObj = new Date(ahora.getUTCFullYear(), ahora.getMonth() , Number(values[2]));
		var fechaMas = new Date();
		fechaMas.setDate(fechaMas.getDate() + 3);
		var color = '';

		if( fechaObj >= ahora && fechaObj <= fechaMas )
			color = 'rojo';
		else {
			console.log('FechaObj: '+fechaObj.getUTCFullYear()+'/'+(fechaObj.getMonth()+1)+'/'+fechaObj.getDate());
			console.log('Ahora: '+ahora);
			console.log('fechaMas: '+fechaMas.getUTCFullYear()+'/'+(fechaMas.getMonth()+1)+'/'+fechaMas.getDate());
			console.log('FechaObj: '+fechaObj);
			console.log('fechaMas: '+fechaMas);
		}

		return color;
	}else {
		return '';
	}
}

var fecha = '2017-05-03';

var values = fecha.split("-");
var ahora = new Date();
var fechaObj = new Date(ahora.getUTCFullYear(), ahora.getMonth() , Number(values[2]));
var fechaMas = new Date();
fechaMas = fechaMas.setDate(fechaMas.getDate() + 3);