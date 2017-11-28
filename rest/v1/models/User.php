<?php if(!defined('SPECIALCONSTANT')) die(json_encode([array('id'=>'0','error'=>'Acceso Denegado')]));

$app->get('/user/:id',function($id) use($app) {
	try {
		//sleep(1);
		if( isset( $id ) ){
			$pag = $id;
		}else{
			$pag = 1;
		}
		$res = get_todo_paginado( 'user', $pag );

		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode($res));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
});

$app->get('/user/view/:id',function($id) use($app) {
	try {
		$conex = getConex();

		$sql = "SELECT id,ci,ex,name,last_name,sex,email,src,registered,'' pwdA,'' pwdN,'' pwdR FROM user WHERE id='$id';";
		$result = $conex->prepare( $sql );

		$result->execute();
		$conex = null;

		$res = $result->fetchObject();

		$app->response->headers->set('Content-type','application/json');
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode($res));
	}catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));

$app->post("/user/",function() use($app) {
	try {
		$postdata = file_get_contents("php://input");

		$request = json_decode($postdata);
		$request = (array) $request;
		$conex = getConex();
		$res = array( 'error'=>'yes', 'msj'=>'Puta no se pudo hacer nada, revisa mierda.' );

		if( isset( $request['id'] )  ){  // ACTUALIZAR

			$sql = "UPDATE user 
						SET
							ci            = '". $request['ci'] ."',
							ex            = '". $request['ex'] ."',
							name          = '". $request['name'] ."',
							last_name     = '". $request['last_name'] ."',
							email  		  = '". $request['email'] ."',
							sex  		  = '". $request['sex'] ."',
							pwd 	   	  = '". $request['pwd'] ."',
							type          = '". $request['type'] ."'
					WHERE id=" . $request['id'].";";

			$hecho = $conex->prepare( $sql );
			$hecho->execute();
			$conex = null;
			
			$res = array( 'id'=>$request['id'], 'error'=>'not', 'msj'=>'Usuario actualizado.' );

		}else{  // INSERT

			$salt = '#/$02.06$/#_#/$25.10$/#';
			$pwd = md5($salt.$request['pwd']);
			$pwd = sha1($salt.$pwd);

			$sql = "CALL pInsertUser(
						'". $request['ci'] . "',
						'". $request['ex'] . "',
						'". $request['name'] . "',
						'". $request['last_name'] . "',
						'". $request['email'] . "',
						'". $request['sex'] . "',
						'". $pwd . "',
						'". $request['type'] . "' );";

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

$app->delete('/user/:id',function($id) use($app) {
	try {
		$conex = getConex();
		$result = $conex->prepare("DELETE FROM user WHERE id='$id'");

		$result->execute();
		$conex = null;

		$app->response->headers->set('Content-type','application/json');
		$app->response->status(200);
		$app->response->body(json_encode(array('id'=>$id,'error'=>'not','msj'=>'Registro eliminado correctamente.')));

	} catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));


$app->put("/user/:id",function($id) use($app) {
	$jsonmessage = \Slim\Slim::getInstance()->request();
  	$objDatos = json_decode($jsonmessage->getBody());

	$email = $objDatos->email;
	$pwdA = $objDatos->pwdA;
	$pwdN = $objDatos->pwdN;
	$pwdR = $objDatos->pwdR;
	$src = $objDatos->src;

	if( $pwdA == null )
		$pwdA = '';
	if( $src == null )
		$src = '';

	if( $pwdA && $pwdN && $pwdR ) {
		$salt = '#/$02.06$/#_#/$25.10$/#';
		$pwdA = md5($salt.$pwdA);
		$pwdA = sha1($salt.$pwdA);

		$pwdN = md5($salt.$pwdN);
		$pwdN = sha1($salt.$pwdN);

		$pwdR = md5($salt.$pwdR);
		$pwdR = sha1($salt.$pwdR);
	}

	try {
		$conex = getConex();
		$result = $conex->prepare("CALL pUpdateUser('$id','$email','$pwdA','$pwdN','$pwdR','$src');");

		$result->execute();
		$conex = null;
		$res = $result->fetchObject();

		$app->response->headers->set('Content-type','application/json');
		$app->response->status(200);
		$app->response->body(json_encode($res));

	}catch(PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
})->conditions(array('id'=>'[0-9]{1,11}'));

$app->post("/login/",function() use($app) {
	$objDatos = json_decode(file_get_contents("php://input"));

	$correo = $objDatos->email;
	$contra = $objDatos->pwd;
	//sleep(3);

	try {
		$conex = getConex();

		$salt = '#/$02.06$/#_#/$25.10$/#';
		$contra = md5($salt.$contra);
		$contra = sha1($salt.$contra);

		$result = $conex->prepare("CALL pSession('$correo','$contra');");

		$result->execute();
		$res = $result->fetchObject();
		if($res->error == 'not'){
			$_SESSION['uid'] = uniqid('ang_');
			$_SESSION['userID'] = $res->id;
			$_SESSION['userTYPE'] = $res->type;
		}

		$conex = null;

		$app->response->headers->set("Content-type","application/json");
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->status(200);
		$app->response->body(json_encode($res));

	}catch(PDOException $e) {
		echo "Error: ".$e->getMessage();
	}
});
