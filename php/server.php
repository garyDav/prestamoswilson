<?php

if(!is_dir('../app/photos/'))
	mkdir('../app/photos/',777);
if($_FILES['img']) {
	if(is_uploaded_file($_FILES['img']['tmp_name'])) {
		//Definir nombres
		$e = 'yes';
		$msj = '';
		$name = '';

		$nombre = $_FILES['img']['name'];
		$nombre = strtolower($nombre);
		$tipo = $_FILES['img']['type'];
		$tipo = strtolower($tipo);
		$size = $_FILES['img']['size'];
		$error = $_FILES['img']['error'];
		$extension = substr($tipo,strpos($tipo,'/')+1);
		$name = substr($nombre,0,-4).'-'.substr(time(),-3).'.'.$extension;
		$lugar = '../app/photos/';
		//Fin de definir nombres

		if(!empty($nombre) && isset($nombre)) {
			if($error == 0) {
				if(strpos($tipo,'jpg') || strpos($tipo,'jpeg') || strpos($tipo,'bmp') || strpos($tipo,'png')) {
					if($size < 1887500) {
						if(move_uploaded_file($_FILES['img']['tmp_name'], $lugar.$name)) {
							$msj = 'Imagen subida';
							$e = 'not';
						}
					} else
						$msj = 'Imagen demasiado grande.';
				} else
					$msj = 'Formato incorrecto de la imagen.';
			} else
				$msj = 'Error al subir la imagen.';
		} else
			$msj = 'La imagen no se subio bien.';
	} else
		$msj = 'Por favor elija una imagen.';
	print_r(json_encode(array('error'=>$e,'msj'=>$msj,'src'=>$name)));
}
?>
