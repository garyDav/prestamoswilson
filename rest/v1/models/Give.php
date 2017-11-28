<?php if(!defined('SPECIALCONSTANT')) die(json_encode([array('id'=>'0','error'=>'Acceso Denegado')]));

$app->get('/give/:pagina/:id',function($pagina,$id) use($app) {
	try {
		//sleep(1);
		if( isset( $pagina ) ){
			$pag = $pagina;
		}else{
			$pag = 1;
		}
		$res = get_paginado_give( $pag,$id );

		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode( $res ));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
});

$app->get('/give/users',function() use($app) {
	try {
		$conex = getConex();

		$sql = "SELECT id id_pres,ci ci_pres,name name_pres,last_name last_name_pres  FROM user ORDER BY id_pres DESC;";
		$result = $conex->prepare( $sql );

		$result->execute();
		$conex = null;

		$res = $result->fetchAll(PDO::FETCH_OBJ);

		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode($res));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));

$app->get('/give/clients',function() use($app) {
	try {
		$conex = getConex();

		$sql = "SELECT id id_cli,ci ci_cli,name name_cli,last_name last_name_cli  FROM clients ORDER BY id_cli DESC;";
		$result = $conex->prepare( $sql );

		$result->execute();
		$conex = null;

		$res = $result->fetchAll(PDO::FETCH_OBJ);

		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode($res));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));

$app->get('/main/give/:pagina/:id',function($pagina,$id) use($app) {
	try {

		if( isset( $pagina ) ){
			$pag = $pagina;
		}else{
			$pag = 1;
		}
		$res = get_paginado_main_give( $pag,$id );

	
		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode($res));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));

$app->post("/give/",function() use($app) {
	try {
		$postdata = file_get_contents("php://input");

		$request = json_decode($postdata);
		$request = (array) $request;
		$conex = getConex();
		$res = array( 'error'=>'yes', 'msj'=>'Puta no se pudo hacer nada, revisa mierda' );

		if( isset( $request['id'] )  ){  // ACTUALIZAR

			$sql = "UPDATE give 
						SET
							id_user = '". $request['id_user'] ."',
							id_clients = '". $request['id_clients'] ."',
							amount = '". $request['amount'] ."',
							fec_pre = '". $request['fec_pre'] ."',
							month = '". $request['month'] ."',
							fine = '". $request['fine'] ."',
							interest = '". $request['interest'] ."',
							type = '". $request['type'] ."',
							detail = '". $request['detail'] ."',
							gain = '". $request['gain'] ."'
					WHERE id=" . $request['id'].";";

			$hecho = $conex->prepare( $sql );
			$hecho->execute();
			$conex = null;

			
			$res = array( 'id'=>$request['id'], 'error'=>'not', 'msj'=>'Registro actualizado' );



		}else{  // INSERT

			$sql = "CALL pInsertGive(
						'". $request['id_user'] . "',
						'". $request['id_clients'] . "',
						'". $request['id_userin'] . "',
						'". $request['amount'] . "',
						'". $request['fec_pre'] . "',
						'". $request['month'] . "',
						'". $request['fine'] . "',
						'". $request['interest'] . "',
						'". $request['type'] . "',
						'". $request['detail'] . "',
						'". $request['gain'] . "' );";

			$hecho = $conex->prepare( $sql );
			$hecho->execute();
			$conex = null;

			$res = $hecho->fetchObject();

		}

		$app->response->headers->set('Content-type','application/json');
		$app->response->status(200);
		$app->response->body(json_encode($res));

	}catch(PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
});

$app->delete('/give/:id',function($id) use($app) {
	try {
		$conex = getConex();
		$result = $conex->prepare("DELETE FROM give WHERE id='$id'");

		$result->execute();
		$conex = null;

		$app->response->headers->set('Content-type','application/json');
		$app->response->status(200);
		$app->response->body(json_encode(array('id'=>$id,'error'=>'not','msj'=>'Prestamo eliminado correctamente')));

	} catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));


$app->put("/hiddenGive/:id",function($id) use($app) {
	try {
		$conex = getConex();
		$result = $conex->prepare("UPDATE give SET visible=0 WHERE id='$id';");

		$result->execute();
		$conex = null;
		$res = array('id'=>$id,'error'=>'not','msj'=>'Prestamo ocultado correctamente');

		$app->response->headers->set('Content-type','application/json');
		$app->response->status(200);
		$app->response->body(json_encode($res));

	}catch(PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));


?>