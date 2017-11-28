<?php if(!defined('SPECIALCONSTANT')) die(json_encode([array('id'=>'0','error'=>'Acceso Denegado')]));

$app->get('/give/reports/:pagina/:id',function($pagina,$id) use($app) {
	try {
		//sleep(1);
		if( isset( $pagina ) ){
			$pag = $pagina;
		}else{
			$pag = 1;
		}
		$res = get_paginado_reporte( $pag,$id );

		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode( $res ));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
});

$app->get('/give/reportsmonth/:mes/:id',function($mes,$id) use($app) {
	try {
		//sleep(1);
		$res = get_paginado_reporte_mes( $mes, $id );

		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode( $res ));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
});


$app->post("/reportes/",function() use($app) {
	$objDatos = json_decode(file_get_contents("php://input"));

	$fecha = $objDatos->fecha;

	try {
		$conex = getConex();

		$result = $conex->prepare("CALL pReporte('$fecha');");

		$result->execute();
		$res = $result->fetchAll(PDO::FETCH_OBJ);
		//$res = array('response'=>'La puta Marta');

		$conex = null;

		$app->response->headers->set("Content-type","application/json");
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode($res));
	}catch(PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
});