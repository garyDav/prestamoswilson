<?php
	session_start();
	if ( isset($_SESSION['userID']) && isset($_SESSION['userTYPE']) ){
		echo json_encode( array( 'userID'=>$_SESSION['userID'],'userTYPE'=>$_SESSION['userTYPE'],'error'=>'not' ) );
	} else {
		echo json_encode( array( 'error'=>'yes' ) );
	}

?>
