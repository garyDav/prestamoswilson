<?php if(!defined("SPECIALCONSTANT")) die(json_encode([array("id"=>"0","nombre"=>"Acceso Denegado")]));

function getConex() {
	try {
		$us = "root";
		//$pwd = "";
		$pwd = "garydavid";
		$conex = new PDO("mysql:host=localhost;dbname=prestamos;charset=utf8",$us,$pwd,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$conex->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e) {
		return "Error: ".$e->getMessage();
	}
	return $conex;
}

?>
