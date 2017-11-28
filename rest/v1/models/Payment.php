<?php if(!defined('SPECIALCONSTANT')) die(json_encode([array('id'=>'0','error'=>'Acceso Denegado')]));

$app->post("/payment/",function() use($app) {
	try {
		$postdata = file_get_contents("php://input");

		$request = json_decode($postdata);
		$request = (array) $request;
		$conex = getConex();

		$sql = "CALL tInsertPayment(
					'". $request['id_give'] . "',
					'". $request['fec_pago_actual'] . "',
					'". $request['fec_pago'] . "',
					'". $request['observation'] . "' );";

		$hecho = $conex->prepare( $sql );
		$hecho->execute();
		$conex = null;

		$res = $hecho->fetchObject();

		$app->response->headers->set('Content-type','application/json');
		$app->response->status(200);
		$app->response->body(json_encode($res));

	}catch(PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
});



?>