<?php if(!defined('SPECIALCONSTANT')) die(json_encode([array('id'=>'0','error'=>'Acceso Denegado')]));

$app->get('/client/:id',function($id) use($app) {
	try {
		//sleep(1);
		if( isset( $id ) ){
			$pag = $id;
		}else{
			$pag = 1;
		}
		$res = get_todo_paginado( 'clients', $pag );

		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode($res));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
});

$app->post("/client/",function() use($app) {
	try {
		$postdata = file_get_contents("php://input");

		$request = json_decode($postdata);
		$request = (array) $request;
		$conex = getConex();
		$res = array( 'error'=>'yes', 'msj'=>'Puta no se pudo hacer nada, revisa mierda' );

		if( isset( $request['id'] )  ){  // ACTUALIZAR

			$sql = "UPDATE clients 
						SET
							ci    		 = '". $request['ci'] ."',
							ex           = '". $request['ex'] ."',
							name 		 = '". $request['name'] ."',
							last_name 	 = '". $request['last_name'] ."',
							civil_status = '". $request['civil_status'] ."',
							profession	 = '". $request['profession'] ."',
							address      = '". $request['address'] ."',
							cellphone1   = '". $request['cellphone1'] ."',
							cellphone2   = '". $request['cellphone2'] ."',
							src 		 = '". $request['src'] . "',
							fec_nac      = '". $request['fec_nac'] ."',
							sex			 = '". $request['sex'] ."' 
					WHERE id=" . $request['id'].";";

			$hecho = $conex->prepare( $sql );
			$hecho->execute();
			$conex = null;

			
			$res = array( 'id'=>$request['id'], 'error'=>'not', 'msj'=>'Registro actualizado' );



		}else{  // INSERT avatar.png

			if( $request['src'] == '' )
				$request['src'] = 'avatar.png';
			$sql = "CALL pInsertClients(
						'". $request['ci'] . "',
						'". $request['ex'] . "',
						'". $request['name'] . "',
						'". $request['last_name'] . "',
						'". $request['civil_status'] . "',
						'". $request['profession'] . "',
						'". $request['address'] . "',
						'". $request['cellphone1'] . "',
						'". $request['cellphone2'] . "',
						'". $request['src'] . "',
						'". $request['fec_nac'] . "',
						'". $request['sex'] . "');";

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

$app->delete('/client/:id',function($id) use($app) {
	try {
		$conex = getConex();
		$result = $conex->prepare("DELETE FROM clients WHERE id='$id'");

		$result->execute();
		$conex = null;

		$app->response->headers->set('Content-type','application/json');
		$app->response->status(200);
		$app->response->body(json_encode(array('id'=>$id,'error'=>'not','msj'=>'Cliente eliminado correctamente.')));

	} catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));


