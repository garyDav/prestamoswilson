<?php if(!defined("SPECIALCONSTANT")) die(json_encode([array("id"=>"0","nombre"=>"Acceso Denegado")]));


// ================================================
//   Funcion que pagina cualquier TABLA
// ================================================
function get_todo_paginado( $tabla, $pagina = 1, $por_pagina = 20 ){

	$conex = getConex();

	$sql = "SELECT count(*) as cuantos from $tabla";

	$result = $conex->prepare($sql);
	$result->execute();
	$res = $result->fetchObject();
	$cuantos = $res->cuantos;


	$total_paginas = ceil( $cuantos / $por_pagina );

	if( $pagina > $total_paginas ){
		$pagina = $total_paginas;
	}


	$pagina -= 1;  // 0
	$desde   = $pagina * $por_pagina; // 0 * 20 = 0
	if( $desde < 0 )
		$desde = 0;

	if( $pagina >= $total_paginas-1 ){
		$pag_siguiente = 1;
	}else{
		$pag_siguiente = $pagina + 2;
	}

	if( $pagina < 1 ){
		$pag_anterior = $total_paginas;
	}else{
		$pag_anterior = $pagina;
	}


	$sql = "SELECT * FROM $tabla ORDER BY id DESC limit $desde, $por_pagina";
	$result = $conex->prepare($sql);
	$result->execute();
	$datos = $result->fetchAll(PDO::FETCH_OBJ);

	$arrPaginas = array();
	for ($i=0; $i < $total_paginas; $i++) { 
		array_push($arrPaginas, $i+1);
	}


	$respuesta = array(
			'err'     		=> false, 
			'conteo' 		=> $cuantos,
			$tabla 			=> $datos,
			'pag_actual'    => ($pagina+1),
			'pag_siguiente' => $pag_siguiente,
			'pag_anterior'  => $pag_anterior,
			'total_paginas' => $total_paginas,
			'paginas'	    => $arrPaginas
			);


	return  $respuesta;

}


// ================================================
//   Funcion que pagina la tabla give
// ================================================
function get_paginado_give( $pagina = 1, $id = 1, $por_pagina = 10 ){

	$conex = getConex();

	if( $id == '1' )
		$sql = "SELECT count(*) as cuantos FROM give";
	else
		$sql = "SELECT count(*) as cuantos FROM give WHERE (id_userin='$id' OR id_user='$id');";

	$result = $conex->prepare($sql);
	$result->execute();
	$res = $result->fetchObject();
	$cuantos = $res->cuantos;


	$total_paginas = ceil( $cuantos / $por_pagina );

	if( $pagina > $total_paginas ){
		$pagina = $total_paginas;
	}


	$pagina -= 1;  // 0
	$desde   = $pagina * $por_pagina; // 0 * 20 = 0
	if( $desde < 0 )
		$desde = 0;

	if( $pagina >= $total_paginas-1 ){
		$pag_siguiente = 1;
	}else{
		$pag_siguiente = $pagina + 2;
	}

	if( $pagina < 1 ){
		$pag_anterior = $total_paginas;
	}else{
		$pag_anterior = $pagina;
	}

	if( $id == '1' )
		$sql = "SELECT g.id,u.id id_user,c.id id_clients,u.ci us_ci,u.name us_name,u.last_name us_last_name,c.ci cli_ci,c.name cli_name,c.last_name cli_last_name,g.amount,g.fec_pre,g.month,g.fine,g.interest,g.type,g.detail,g.gain,g.total_capital,g.total_interest,g.total_lender,g.total_assistant FROM give g,user u,clients c WHERE g.id_user=u.id AND g.id_clients=c.id ORDER BY g.id DESC limit $desde, $por_pagina";
	else
		$sql = "SELECT g.id,u.id id_user,c.id id_clients,u.ci us_ci,u.name us_name,u.last_name us_last_name,c.ci cli_ci,c.name cli_name,c.last_name cli_last_name,g.amount,g.fec_pre,g.month,g.fine,g.interest,g.type,g.detail,g.gain,g.total_capital,g.total_interest,g.total_lender,g.total_assistant FROM give g,user u,clients c WHERE g.id_user=u.id AND g.id_clients=c.id AND (g.id_userin='$id' OR g.id_user='$id') ORDER BY g.id DESC limit $desde, $por_pagina;";
	$result = $conex->prepare($sql);
	$result->execute();
	$datos = $result->fetchAll(PDO::FETCH_OBJ);

	$arrPaginas = array();
	for ($i=0; $i < $total_paginas; $i++) { 
		array_push($arrPaginas, $i+1);
	}


	$respuesta = array(
			'err'     		=> false, 
			'conteo' 		=> $cuantos,
			'gives' 		=> $datos,
			'pag_actual'    => ($pagina+1),
			'pag_siguiente' => $pag_siguiente,
			'pag_anterior'  => $pag_anterior,
			'total_paginas' => $total_paginas,
			'paginas'	    => $arrPaginas
			);


	return  $respuesta;

}

// ================================================
//   Funcion paginar tabla give y su payment
// ================================================
function get_paginado_main_give( $pagina = 1, $id = 1, $por_pagina = 6 ){

	$conex = getConex();

	if( $id == '1' )
		$sql = "SELECT count(*) as cuantos FROM give WHERE visible<>0";
	else
		$sql = "SELECT count(*) as cuantos FROM give WHERE (id_userin='$id' OR id_user='$id') AND visible<>0;";

	$result = $conex->prepare($sql);
	$result->execute();
	$res = $result->fetchObject();
	$cuantos = $res->cuantos;


	$total_paginas = ceil( $cuantos / $por_pagina );

	if( $pagina > $total_paginas ){
		$pagina = $total_paginas;
	}


	$pagina -= 1;  // 0
	$desde   = $pagina * $por_pagina; // 0 * 20 = 0
	if( $desde < 0 )
		$desde = 0;

	if( $pagina >= $total_paginas-1 ){
		$pag_siguiente = 1;
	}else{
		$pag_siguiente = $pagina + 2;
	}

	if( $pagina < 1 ){
		$pag_anterior = $total_paginas;
	}else{
		$pag_anterior = $pagina;
	}

	if( $id == '1' )
		$sql = "SELECT g.id,c.name,c.last_name,c.ci,c.ex,c.src,g.amount,g.fec_pre,g.month,'' payment,'' nopayment FROM clients c,give g,user u WHERE c.id=g.id_clients AND g.id_user=u.id AND g.visible<>0 ORDER BY g.id DESC limit $desde, $por_pagina";
	else
		$sql = "SELECT g.id,c.name,c.last_name,c.ci,c.ex,c.src,g.amount,g.fec_pre,g.month,'' payment,'' nopayment FROM clients c,give g,user u WHERE c.id=g.id_clients AND g.id_user=u.id AND (g.id_userin='$id' OR g.id_user='$id') AND g.visible<>0 ORDER BY g.id DESC limit $desde, $por_pagina;";
	$result = $conex->prepare($sql);
	$result->execute();
	$datos = $result->fetchAll(PDO::FETCH_OBJ);

	$arrPaginas = array();
	for ($i=0; $i < $total_paginas; $i++) { 
		array_push($arrPaginas, $i+1);
	}

	foreach ($datos as $valor) {
		//Resultados para payment
		$id = $valor->id;
		$sql = "SELECT id,fec_pago,observation FROM payment WHERE id_give='$id';";
		$result = $conex->prepare( $sql );
		$result->execute();
		$valor->payment = $result->fetchAll(PDO::FETCH_OBJ);

		//Obteniendo candidad de pagos ya realizados
		$sql = "SELECT count(id) cantidad FROM payment WHERE id_give='$id';";
		$result = $conex->prepare( $sql );
		$result->execute();
		$cantidad = $result->fetchObject()->cantidad;


		
		//Resultados para nopayment
		$cont=0;
		$resultado = [];
		$fecha_pre = $valor->fec_pre;
		$fecha_actual = date('Y-m-d');

		$final_pre = strtotime ( '+'.$valor->month.' month' , strtotime ( $fecha_pre ) ) ;
		$final_pre = date ( 'Y-m-d' , $final_pre );

		while($fecha_pre < $final_pre) { //$fecha_pre != $final_pre
			$fecha_pre = strtotime ( '+1 month' , strtotime ( $fecha_pre ) ) ;
			$fecha_pre = date ( 'Y-m-d' , $fecha_pre );
			
			if($cont > $cantidad-1)
				$resultado[] = (object) array( 'fec_pago'=>$fecha_pre, 'observation'=>'Falta pagar' );
			$cont++;
		}

		$valor->nopayment = $resultado;
	}


	$respuesta = array(
			'err'     		=> false, 
			'conteo' 		=> $cuantos,
			'gives' 		=> $datos,
			'pag_actual'    => ($pagina+1),
			'pag_siguiente' => $pag_siguiente,
			'pag_anterior'  => $pag_anterior,
			'total_paginas' => $total_paginas,
			'paginas'	    => $arrPaginas
			);


	return  $respuesta;

}

// ================================================
//   Funcion paginar tabla give y su payment en reporte
// ================================================
function get_paginado_reporte( $pagina = 1, $id = 1, $por_pagina = 6 ){

	$conex = getConex();

	if( $id == '1' )
		$sql = "SELECT count(*) as cuantos FROM give WHERE visible<>0";
	else
		$sql = "SELECT count(*) as cuantos FROM give WHERE (id_userin='$id' OR id_user='$id') AND visible<>0;";

	$result = $conex->prepare($sql);
	$result->execute();
	$res = $result->fetchObject();
	$cuantos = $res->cuantos;


	$total_paginas = ceil( $cuantos / $por_pagina );

	if( $pagina > $total_paginas ){
		$pagina = $total_paginas;
	}


	$pagina -= 1;  // 0
	$desde   = $pagina * $por_pagina; // 0 * 20 = 0
	if( $desde < 0 )
		$desde = 0;

	if( $pagina >= $total_paginas-1 ){
		$pag_siguiente = 1;
	}else{
		$pag_siguiente = $pagina + 2;
	}

	if( $pagina < 1 ){
		$pag_anterior = $total_paginas;
	}else{
		$pag_anterior = $pagina;
	}

	if( $id == '1' )
		$sql = "SELECT g.id,u.name us_name,u.last_name us_last_name,c.name cli_name,c.last_name cli_last_name,c.src cli_src,u.src us_src,g.amount,g.fec_pre,g.month,g.total_capital,g.total_interest,g.total_lender,g.total_assistant,g.visible,us.name ac_name,us.last_name ac_last_name,'' payment,'' nopayment FROM clients c,give g,user u,user us WHERE c.id=g.id_clients AND g.id_userin=u.id AND g.id_user=us.id AND g.visible<>0 ORDER BY g.id DESC limit $desde, $por_pagina";
	else
		$sql = "SELECT g.id,u.name us_name,u.last_name us_last_name,c.name cli_name,c.last_name cli_last_name,c.src cli_src,u.src us_src,g.amount,g.fec_pre,g.month,g.total_capital,g.total_interest,g.total_lender,g.total_assistant,g.visible,us.name ac_name,us.last_name ac_last_name,'' payment,'' nopayment FROM clients c,give g,user u,user us WHERE c.id=g.id_clients AND g.id_userin=u.id AND g.id_user=us.id AND (g.id_userin='$id' OR g.id_user='$id') AND g.visible<>0 ORDER BY g.id DESC limit $desde, $por_pagina;";
	$result = $conex->prepare($sql);
	$result->execute();
	$datos = $result->fetchAll(PDO::FETCH_OBJ);

	$arrPaginas = array();
	for ($i=0; $i < $total_paginas; $i++) { 
		array_push($arrPaginas, $i+1);
	}

	foreach ($datos as $valor) {
		//Resultados para payment
		$id = $valor->id;
		$sql = "SELECT id,fec_pago,interests,capital_shares,lender,assistant,observation FROM payment WHERE id_give='$id';";
		$result = $conex->prepare( $sql );
		$result->execute();
		$valor->payment = $result->fetchAll(PDO::FETCH_OBJ);

		//Obteniendo candidad de pagos ya realizados
		$sql = "SELECT count(id) cantidad FROM payment WHERE id_give='$id';";
		$result = $conex->prepare( $sql );
		$result->execute();
		$cantidad = $result->fetchObject()->cantidad;


		
		//Resultados para nopayment
		$cont=0;
		$resultado = [];
		$fecha_pre = $valor->fec_pre;
		$fecha_actual = date('Y-m-d');

		$final_pre = strtotime ( '+'.$valor->month.' month' , strtotime ( $fecha_pre ) ) ;
		$final_pre = date ( 'Y-m-d' , $final_pre );

		while($fecha_pre < $final_pre) { //$fecha_pre != $final_pre
			$fecha_pre = strtotime ( '+1 month' , strtotime ( $fecha_pre ) ) ;
			$fecha_pre = date ( 'Y-m-d' , $fecha_pre );
			
			if($cont > $cantidad-1)
				$resultado[] = (object) array( 'fec_pago'=>$fecha_pre,'interests'=>'0','capital_shares'=>'0','lender'=>'0','assistant'=>'0','observation'=>'Falta pagar...' );
			$cont++;
		}

		$valor->nopayment = $resultado;
	}


	$respuesta = array(
			'err'     		=> false, 
			'conteo' 		=> $cuantos,
			'gives' 		=> $datos,
			'pag_actual'    => ($pagina+1),
			'pag_siguiente' => $pag_siguiente,
			'pag_anterior'  => $pag_anterior,
			'total_paginas' => $total_paginas,
			'paginas'	    => $arrPaginas
			);


	return  $respuesta;

}


// ================================================
//   Funcion paginar tabla give y su payment en reporte
// ================================================
function get_paginado_reporte_mes( $mes, $id ) {

	$conex = getConex();
	if( $id == '1' )
		$sql = "SELECT DISTINCT u.id,u.name,u.last_name,'0' total FROM clients c,give g,user u,user us WHERE g.id_clients=c.id AND (g.id_user=u.id OR g.id_userin=u.id) ORDER BY u.id DESC";
	else
		$sql = "SELECT DISTINCT u.id,u.name,u.last_name,'0' total FROM clients c,give g,user u WHERE g.id_clients=c.id AND (g.id_user=u.id OR g.id_userin=u.id) AND (g.id_user='$id' OR g.id_userin='$id') ORDER BY u.id DESC";
	$result = $conex->prepare($sql);
	$result->execute();
	$dataUsers = $result->fetchAll(PDO::FETCH_OBJ);


	if( $id == '1' )
		$sql = "SELECT g.id,g.amount,g.fec_pre,g.month,g.interest,g.type,g.gain,g.visible,u.id u_id,u.name u_name,u.last_name u_last_name,us.id us_id,us.name us_name,us.last_name us_last_name,c.name cli_name,c.last_name cli_last_name,c.src FROM clients c,give g,user u,user us WHERE g.id_user=u.id AND g.id_clients=c.id AND g.id_userin=us.id AND g.visible<>0 ORDER BY g.id DESC";
	else
		$sql = "SELECT g.id,g.amount,g.fec_pre,g.month,g.interest,g.type,g.gain,g.visible,u.id u_id,u.name u_name,u.last_name u_last_name,us.id us_id,us.name us_name,us.last_name us_last_name,c.name cli_name,c.last_name cli_last_name,c.src FROM clients c,give g,user u,user us WHERE g.id_user=u.id AND g.id_clients=c.id AND g.id_userin=us.id AND (g.id_user='$id' OR g.id_userin='$id') AND g.visible<>0 ORDER BY g.id DESC";
	$result = $conex->prepare($sql);
	$result->execute();
	$datos = $result->fetchAll(PDO::FETCH_OBJ);

	$resultado = [];
	$fecha_in = date('Y').'-'.$mes.'-'.'01';
	//$fecha_in = '2017-03-19';
	$fecha_out = strtotime ( '+1 month' , strtotime ( $fecha_in ) ) ;
	//$fecha_in = date_create_from_format ( 'Y-m-d' , $fecha_in );
	//$fecha_in = date ( 'Y-m-d' , $fecha_in->date );
	$fecha_out = date ( 'Y-m-d' , $fecha_out );

	foreach ($datos as $valor) {
		$fecha_pre = $valor->fec_pre;

		$mes_pre = date("m", strtotime($fecha_pre));
		$final_pre = strtotime ( '+'.$valor->month.' month' , strtotime ( $fecha_pre ) ) ;
		$fecha_pre = strtotime ( '+1 month' , strtotime ( $fecha_pre ) ) ;
		$fecha_pre = date ( 'Y-m-d' , $fecha_pre );
		$final_pre = date ( 'Y-m-d' , $final_pre );
		/*$resultado[] = (object) array( 
			'fecha_pre'=>$fecha_pre,
			'fecha_in'=>$fecha_in,
			'fecha_out'=>$fecha_out
			);*/
		if( !($fecha_pre < $fecha_out && $final_pre >= $fecha_in) )
			continue;
		$capital = 0;
		$interest = 0;
		$lender = 0;
		$assistant = 0;
		$pcapital = 0;
		$mes_dif = 0;
		//$mes_dif = $mes-$mes_pre;



		$inicio = $valor->fec_pre.' 00:00:00';
		$fin = $fecha_out.' 00:00:00';
		 
		$datetime1=new DateTime($inicio);
		$datetime2=new DateTime($fin);
		 
		# obtenemos la diferencia entre las dos fechas
		$interval=$datetime2->diff($datetime1);
		 
		# obtenemos la diferencia en meses
		$intervalMeses=$interval->format("%m");
		# obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
		$intervalAnos = $interval->format("%y")*12;
		 
		$mes_dif = $intervalMeses+$intervalAnos;






		if( $valor->type == 'men' ) {
			$interest = $valor->amount*($valor->interest/100);
			$lender = $interest*((100-$valor->gain)/100);
			$assistant = $interest*($valor->gain/100);
			if( $final_pre >= $fecha_in && $final_pre < $fecha_out )
				$capital = $valor->amount;
		} else if( $valor->type == 'menIn' ) {
			$pcapital = (int)($valor->amount/$valor->month);
			$capital = $pcapital;
			$interest = ($valor->amount-($pcapital*$mes_dif))*($valor->interest/100);
			$lender = $interest*((100-$valor->gain)/100);
			$assistant = $interest*($valor->gain/100);
			if( $final_pre >= $fecha_in && $final_pre < $fecha_out )
				$capital = $pcapital+($valor->amount-($pcapital*$mes_dif));
		}



		$resultado[] = (object) array( 
			'id'=>$valor->id,
			'fec_pre'=>$valor->fec_pre,
			'capital'=>$capital,
			'interest'=>$interest,
			'lender'=>$lender,
			'assistant'=>$assistant,
			'u_id' => $valor->u_id,
			'u_name' => $valor->u_name,
			'u_last_name' => $valor->u_last_name,
			'us_id' => $valor->us_id,
			'us_name' => $valor->us_name,
			'us_last_name' => $valor->us_last_name,
			'cli_name' => $valor->cli_name,
			'cli_last_name' => $valor->cli_last_name,
			'src' => $valor->src,
			'mes_dif' => $mes_dif,
			'month' => $valor->month
			);

	}


	return array(
		'dataUsers' => $dataUsers, 
		'resultado' => $resultado
		);

}

?>
